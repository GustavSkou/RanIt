<?php

namespace App\Http\Parsers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

use App\Models\Activity;
use App\Models\Point;

Class ActivityParser {

    private IFileParser $fileParser;

    public function parse ($file) 
    {
        switch (strtolower($file->getClientOriginalExtension())) 
        {
            case 'gpx':
                $this->fileParser = new GpxFileParser();
                break;
            default:
                throw new \Exception('Unsupported file type: ' . $file->getClientOriginalExtension());
        }

        $fileContentString = $this->fileParser->parseFileType($file);

        $activity = $this->fileParser->createActivity($fileContentString);

        Log::info('Activity created', ['activity_id' => $activity->id]);

        $pointsSummary = $this->fileParser->createPoints($fileContentString, $activity->id);

        $activity->distance = $pointsSummary['distance'];
        $activity->average_speed = $pointsSummary['average_speed'];
        $activity->average_heart_rate = $pointsSummary['average_heart_rate'];
        $activity->start_time = $pointsSummary['start_time'];
        $activity->duration = $pointsSummary['duration'];
        $activity->elevation = $pointsSummary['elevation'];
        $activity->save();

        return [
            'activity' => $activity,
            'points' => $pointsSummary['points']
        ];
    }
}
