<?php 

namespace App\Http\Parsers;

use App\Models\Activity;
use App\Models\Point;

interface IFileParser {
    public function parseFileType($file);

    public function createActivity($xml): Activity;

    public function createPoints($xml, $activityId);

}