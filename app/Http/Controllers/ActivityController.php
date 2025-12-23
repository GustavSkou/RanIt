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

    private function WeeksInRow($activities) {
        $activitesByWeek = $this->sortByYearAndWeek($activities);
        $weekNum = ((int)now()->format("W") )- 1;
        $yearNum = (int)now()->format("Y");

        if ($weekNum == 0) {
            $weekNum = 53;
            $yearNum--;
        }

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

            $activityParser = new \App\Http\Parsers\ActivityParser();
            $response = $activityParser->parse($file);
            $activity = $response['activity'];
            $points = $response['points'];

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

    /** 
     * Return a 3d array of activities sorted by year and week number
     */
    private function sortByYearAndWeek($activities)
    {
        $sorted = [];

        foreach ($activities as $activity) {
            $date = $activity->start_time;
            if ($date == null) {
                continue;
            }

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
}