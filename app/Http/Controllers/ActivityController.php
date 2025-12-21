<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Point;

use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ActivityController extends Controller
{
    public function Index()
    {
        $user = Auth::user();
        $users = $this->GetUsers('id');

        $activities = Activity::whereIn('user_id', $users)
            ->orderBy('start_time', 'desc')
            ->paginate(25);

        $latestActivity = $activities->where('user_id', $user->id)->first();
        $weeksInRow = $this->WeeksInRow($activities->where('user_id', $user->id));

        return view('dashboard', [
            'activities' => $activities,
            'latestActivity' => $latestActivity,
            'weeksInRow' => $weeksInRow
        ]);
    }

    public function Show(Activity $activity)
    {
        $activity->load('points');

        return view('activity', [
            'activity' => $activity
        ]);
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

    public function ActivitiesByWeek(User $user)
    {
        $activities = Activity::where('user_id', $user->id)->get();
        $activitiesSortByWeek = $this->sortByYearAndWeek($activities);
        return view();
    }

    public function WeeksInRow($activities) {
        $activitesByWeek = $this->sortByYearAndWeek($activities);
        $weekNum = now()->format("W");
        $yearNum = now()->format("Y");

        $weeksInRow = 0;
        while (isset($activitesByWeek[$yearNum][$weekNum]) && count($activitesByWeek[$yearNum][$weekNum]) > 0) {
            $weeksInRow++;
            $weekNum--; 
            
            if ($weekNum == 0) {
                $weekNum = 53;  // format return 01-53
                $yearNum--;
            }
        }
        return $weeksInRow;
    }

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
            $activity->elevation = $pointsSummary['elevation'];
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

                    $speed = 0;
                    if ($latitude2 != null && $longitude2 != null) {

                        $distance = $this->Distance($latitude, $longitude, $latitude2, $longitude2);
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

    private function generateActivityMapImage(Activity $activity, $points)
    {
        $points = array_map(function ($point) {
            return [$point['latitude'], $point['longitude']];
        }, $points);

        if (empty($points)) {
            Log::info("Map image generation stopped, no points");
            return;
        }

        $mapOutputDir = storage_path('app/public/maps');
        if (!file_exists($mapOutputDir)) {
            mkdir($mapOutputDir, 0755, true);
        }
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $mapFileName = "/activity-{$activity->id}.png";
        $mapOutputPath = $mapOutputDir . $mapFileName;

        // create temp json file for points and put the point in there
        $tempPointsFile = "/points-{$activity->id}.json";
        $pointFileOutputPath = $tempDir . $tempPointsFile;
        $pointsJson = json_encode($points);
        file_put_contents($pointFileOutputPath, $pointsJson);

        try {
            // Execute the command
            // This will also save the image in storage
            $response =  Artisan::call('map:generate', [
                'pointsFilePath' => $pointFileOutputPath,
                'outputPath' => $mapOutputPath,
                '--width' => 550,
                '--height' => 211
            ]);

            if ($response) {
                $activity->map_image_path = "maps/activity-{$activity->id}.png";
                $activity->save();
                //Log::info("image saved", ['map_image_path' => "maps/activity-{$activity->id}.png",]);
            } else {
                //Log::info("Failed image save", ['activity' => $activity->id]);
            }
        } finally {
            if (file_exists($pointFileOutputPath)) {
                unlink($pointFileOutputPath);
            }
        }
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

        return [
            'timeStamp1' => $timeStamp1,
            'timeStamp2' => $timeStamp2
        ];
    }

    /** 
     * Return a 3d array of activities sorted by year and week number
     */
    private function sortByYearAndWeek($activities)
    {
        $sorted = [];

        foreach ($activities as $activity) {
            $date = $activity->start_time;
            $weekNum = $date->format("W");
            $yearNum = $date->format("Y");

            if (!isset($sorted[$yearNum])) {
                $sorted[$yearNum] = [];
            }

            if (!isset($sorted[$yearNum][$weekNum])) {
                $sorted[$yearNum][$weekNum] = [];
            }

            array_push($sorted[$yearNum][$weekNum], $activity);
        }

        return $sorted;
    }

    /**
     * Get the Authed and its followed users.
     * @return array An array of Users
     */
    private function GetUsers(string $pluckKey)
    {
        $user = Auth::user();
        $users = $user->following()->get()->pluck($pluckKey)->toArray();
        array_push($users, $user->id);
        Log::info($users);
        return $users;
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
