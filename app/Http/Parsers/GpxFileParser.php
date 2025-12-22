<?php

namespace App\Http\Parsers;

use App\Models\Activity;
use App\Models\Point;
use App\Console\Commands\Distance;
use Illuminate\Support\Facades\Auth;

Class GpxFileParser extends IFileParser {
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
        $totalDistance = 0;
        $accumulatedSpeed = 0;
        $accumulatedHeartRate = 0;
        $durationInSeconds = 0;
        $totalElevation = 0;

        $speedPoints = 0;
        $hrPoints = 0;
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

                    $heartRate = null;
                    if (isset($point->extensions)) {


                        $gpxtpx = $point->extensions->children('gpxtpx', true);
                        if (isset($gpxtpx->TrackPointExtension->hr)) {
                            $heartRate = (int) $gpxtpx->TrackPointExtension->hr;
                            $accumulatedHeartRate += $heartRate;
                            $hrPoints++;
                        }


                    }

                    function getHeartRate(){}

                    $speed = 0;
                    if ($latitude2 != null && $longitude2 != null) {

                        $distance = Distance::Distance($latitude, $longitude, $latitude2, $longitude2);
                        $totalDistance += $distance;

                        $validated = $this->getValidatedTime($time, $time2);

                        if ($validated) {
                            $speed = $distance / (($validated['timeStamp1'] - $validated['timeStamp2']) / (60 * 60));
                            $accumulatedSpeed += $speed;
                            $speedPoints++;
                            $durationInSeconds += $validated['timeStamp1'] - $validated['timeStamp2'];
                        }
                    }

                    if ($elevation != null && $elevation2 != null) {
                        // If we are going up, add the elevation diff
                        if ($elevation > $elevation2) {
                            $totalElevation += ($elevation - $elevation2);
                        }
                    }

                    $point = [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'elevation' => $elevation,
                        'timestamp' => $timeString,
                        'heart_rate' => $heartRate,     // is nullable
                        'cadence' => null,
                        'power' => null,
                        'activity_id' => $activityId
                    ];


                    /*
                    
                        Rewrite this to insert from the allPoints based on the chunksize and how big the array is proportional to the chunksize
                        when these is no remainder we should insert 
                        
                        if (count(array) % chunksize == 0)
                            insert array
                    
                    */
                    array_push($allPoints, $point);
                    array_push($chunkPoints, $point);

                    if (count($chunkPoints) >= $chunkSize) {
                        Point::insert($chunkPoints);
                        $chunkPoints = [];
                    }

                    $latitude2 = $latitude;
                    $longitude2 = $longitude;
                    $time2 = $time;
                    $elevation2 = $elevation;
                }
            }
        }

        // insert the rest of the points
        if (count($chunkPoints) > 0) {
            Point::insert($chunkPoints);
        }

        return [
            'distance' => $totalDistance,
            'duration' => $durationInSeconds,
            'elevation' => $totalElevation,
            'start_time' => $firstTime,
            'average_speed' => $speedPoints > 0 ? $accumulatedSpeed / $speedPoints : null,
            'average_heart_rate' => $hrPoints > 0 ? $accumulatedHeartRate / $hrPoints : null,
            'points' => $allPoints
        ];
    }
    }
