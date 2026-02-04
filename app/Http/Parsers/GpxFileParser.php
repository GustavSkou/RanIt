<?php

namespace App\Http\Parsers;

use App\Models\Activity;
use App\Models\Point;
use Illuminate\Support\Facades\Auth;

use function App\Http\Helpers\distance;

Class GpxFileParser implements IFileParser {

    private $totalDistance = 0;
    private $accumulatedSpeed = 0;
    private $accumulatedHeartRate = 0;
    private $durationInSeconds = 0;
    private $totalElevation = 0;
    private $speedPoints = 0;
    private $hrPoints = 0;

    public function parseFileType($file) 
    {
        $contentString = $file->get();
        $xml = simplexml_load_string($contentString);
        return $xml;
    }

    public function createActivity($xml): Activity
    {
        return Activity::create([
            'name' => isset($xml->trk->name) ? (string)$xml->trk->name : 'Unnamed Activity',
            'user_id' => Auth::user()->id,
            'type' => isset($xml->trk->type) ? (string)$xml->trk->type : null
        ]);
    }

    public function createPoints($xml, $activityId)
    {        
        $latitude2 = null;
        $longitude2 = null;
        $time2 = null;
        $elevation2 = null;
        $firstTime = null;

        $allPoints = [];

        // chunk the dataset if it is larger than this value
        $chunkSize = 1000;
        $chunkPoints = [];

        foreach ($xml->trk as $track) {
            foreach ($track->trkseg as $segment) {
                foreach ($segment->trkpt as $point) {
                    $latitude = (float) $point['lat'];
                    $longitude = (float) $point['lon'];
                    $elevation = (float) $point->ele;
                    $timeString = (string) $point->time;
                    $time = new \DateTime($timeString);

                    if ($firstTime == null) {
                        $firstTime = $time;
                    }

                    if (isset($point->extensions)) {
                        $gpxtpx = $point->extensions->children('gpxtpx', true);
                        $heartRate = $this->setHeartRate($gpxtpx);  // are nullable
                        //cadence and power can be added here later
                    }

                    $distance = $this->setDistance($latitude, $longitude, $latitude2, $longitude2);
                    $validated = $this->getValidatedTime($time, $time2);

                    if ($validated) {
                        $speed = $distance / (($validated['timeStamp1'] - $validated['timeStamp2']) / (60 * 60));
                        $this->accumulatedSpeed += $speed;
                        $this->speedPoints++;
                        $this->durationInSeconds += $validated['timeStamp1'] - $validated['timeStamp2'];
                    }

                    $totalElevation = $this->setElevation($elevation, $elevation2, $totalElevation);

                    $point =[
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'elevation' => $elevation,
                        'timestamp' => $timeString,
                        'heart_rate' => $heartRate,     // is nullable
                        'cadence' => null,
                        'power' => null,
                        'activity_id' => $activityId
                    ];

                    array_push($allPoints, $point);

                    $this->insertChunk($allPoints, $chunkSize);

                    $latitude2 = $latitude;
                    $longitude2 = $longitude;
                    $time2 = $time;
                    $elevation2 = $elevation;
                }
            }
        }

        $this->insertLastChunk($allPoints, $chunkSize);

        return [
            'distance' => $this->totalDistance,
            'duration' => $this->durationInSeconds,
            'elevation' => $this->totalElevation,
            'start_time' => $firstTime,
            'average_speed' => $this->speedPoints > 0 ? $this->accumulatedSpeed / $this->speedPoints : null,
            'average_heart_rate' => $this->hrPoints > 0 ? $this->accumulatedHeartRate / $this->hrPoints : null,
            'points' => $allPoints
        ];
    }

    private function insertChunk($allPoints, $chunkSize) {
        if (count($allPoints) % $chunkSize == 0) {
            Point::insert(array_slice($allPoints, count($allPoints) - $chunkSize));
        }
    }

    /**
     * insert the rest of the points
     * by subtracting the remainder from the total count, we then get the starting index for the last slice
     * 6500 % 1000 = 500
     * 6500 - 500 = 6000
     * so we insert from index 6000 to the end 6500
     */
    private function insertLastChunk($allPoints, $chunkSize) {
        if (count($allPoints) % $chunkSize != 0) {
            Point::insert(array_slice($allPoints, count($allPoints) - (count($allPoints) % $chunkSize)));
        }
    }

    private function setDistance($latitude1, $longitude1, $latitude2, $longitude2) {
        if ($latitude2 == null || $longitude2 == null) {
            return 0;
        }

        $distance = distance($latitude1, $longitude1, $latitude2, $longitude2);
        $this->totalDistance += $distance;
        return $distance;
    }

    private function getValidatedTime($time1, $time2)
    {
        if ($time1 == null || $time2 == null) {
            return false;
        }

        $timeStamp1 = $time1->getTimestamp();
        $timeStamp2 = $time2->getTimestamp();

        if ($timeStamp1 == 0 || $timeStamp2 == 0 || $timeStamp1 == null || $timeStamp2 == null || $timeStamp1 <= $timeStamp2) {
            return false;
        }

        if ($timeStamp1 - $timeStamp2 > 1) {
            return false;
        }

        return [
            'timeStamp1' => $timeStamp1,
            'timeStamp2' => $timeStamp2
        ];
    }

    private function setElevation($elevation, $elevation2, &$totalElevation) {
        if ($elevation == null || $elevation2 == null) {
            return $totalElevation;
        } 
        
        // If we are going up, add the elevation diff
        if ($elevation > $elevation2) {
            $totalElevation += ($elevation - $elevation2);
        }
        
        return $totalElevation;
    }

    private function setHeartRate($gpxtpx) {
        if (isset($gpxtpx->TrackPointExtension->hr)) {
            return (int) $gpxtpx->TrackPointExtension->hr;
        }
        return null;
    }
}