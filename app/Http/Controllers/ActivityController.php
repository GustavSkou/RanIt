<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Point;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    public function Index()
    {
        try {
            $userId = Auth::user()->id;
        } catch (Exception $ex) {
            $userId = 1;
        }


        $activities = Activity::where('user_id', $userId)->paginate(25);

        return view('activities')->with('activities', $activities);
    }

    public function ShowUpload()
    {
        return view('file_upload');
    }

    public function ShowEdit(Activity $activity)
    {
        $points = Point::where('activity_id', $activity->id)->get();
        return view('edit_activity')
            ->with('activity', $activity)
            ->with('points', $points);
    }

    public function Edit(Request $request) {}

    public function Upload(Request $request)
    {
        try {
            Log::info('Upload method started: Request data', [
                'hasFile' => $request->hasFile('fileUpload'),
                'allFiles' => $request->allFiles(),
                'all' => $request->all()
            ]);

            $validated = $request->validate([
                'file' => 'required|file'
            ]);
            $file = $validated['file'];

            Log::info('File validated successfully');

            $contentString = $file->get();
            $xml = simplexml_load_string($contentString);
            $activity = $this->CreateActivity($xml);

            Log::info('Activity created', ['activity_id' => $activity->id]);

            $pointsSummary = $this->CreatePoints($xml, $activity->id);

            $activity->distance = $pointsSummary['distance'];
            $activity->average_speed = $pointsSummary['average_speed'];
            $activity->average_heart_rate = $pointsSummary['average_heart_rate'];
            $activity->start_time = $pointsSummary['start_time'];
            $activity->duration = $pointsSummary['duration'];
            $activity->save();

            $points = $pointsSummary['points'];

            $this->generateActivityMapImage($activity, $points);

            Log::info('Upload completed successfully', [
                'activity_id' => $activity->id,
            ]);

            return redirect()
                ->route('show.editActivity', $activity);
            //->with('points', $points);
        } catch (\Exception $e) {
            Log::error('Upload failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    private function CreateActivity($xml): Activity
    {
        return Activity::create([
            'name' => isset($xml->trk->name) ? (string)$xml->trk->name : 'Unnamed Activity',
            'user_id' => Auth::user()->id,
            'type' => isset($xml->trk->type) ? (string)$xml->trk->type : null
        ]);
    }

    private function CreatePoints($xml, $activityId)
    {
        $totalDistance = 0;
        $accumulatedSpeed = 0;
        $accumulatedHeartRate = 0;

        $speedPoints = 0;
        $hrPoints = 0;
        $latitude2 = null;
        $longitude2 = null;
        $time2 = null;
        $firstTime = null;

        $points = [];

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

                    $speed = 0;
                    if ($latitude2 != null && $longitude2 != null) {
                        $distance = $this->Distance($latitude, $longitude, $latitude2, $longitude2);
                        $totalDistance += $distance;

                        if ($time != null && $time2 != null) {
                            $speed = $distance / (($time->getTimestamp() - $time2->getTimestamp()) / (60 * 60));
                            $accumulatedSpeed += $speed;
                            $speedPoints++;
                        }
                    }

                    $point = Point::create([
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'elevation' => $elevation,
                        'timestamp' => $timeString,
                        'speed' => $speed,              // is nullable
                        'heart_rate' => $heartRate,     // is nullable
                        'activity_id' => $activityId
                    ]);
                    array_push($points, [$point->latitude, $point->longitude]);

                    $latitude2 = $latitude;
                    $longitude2 = $longitude;
                    $time2 = $time;
                }
            }
        }

        $durationInSeconds = null;
        if ($firstTime != null) {
            $durationInSeconds = $time2 ? $time2->getTimestamp() - $firstTime->getTimestamp() : null;
        }

        return [
            'distance' => $totalDistance,
            'duration' => $durationInSeconds,
            'start_time' => $firstTime,
            'average_speed' => $speedPoints > 0 ? $accumulatedSpeed / $speedPoints : null,
            'average_heart_rate' => $hrPoints > 0 ? $accumulatedHeartRate / $hrPoints : null,
            'points' => $points
        ];
    }

    private function generateActivityMapImage(Activity $activity, $points)
    {
        if (empty($points)) {
            return;
        }

        // Create output directory if it doesn't exist

        if (Storage::disk('public')->exists('file.jpg')) {
            // ...
        }

        $outputDir = storage_path('app/public/maps');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $outputPath = $outputDir . "/activity-{$activity->id}.png";
        $pointsJson = json_encode($points);

        // Execute the command
        // This will also save the image in storage
        Artisan::call('map:generate', [
            'pointsJson' => $pointsJson,
            'outputPath' => $outputPath,
            '--width' => 800,
            '--height' => 600
        ]);

        $activity->map_image_path = "maps/activity-{$activity->id}.png";
        $activity->save();
    }

    private function Distance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        // Earth radius
        $R = 6371.0088;

        // Convert degrees to radians
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $dPhi = deg2rad($lat2 - $lat1);
        $dLambda = deg2rad($lon2 - $lon1);

        // Haversine formula
        $a = sin($dPhi / 2) ** 2 + cos($phi1) * cos($phi2) * sin($dLambda / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c; // distance in chosen unit
    }
}
