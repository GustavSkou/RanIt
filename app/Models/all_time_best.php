<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class all_time_best extends Model
{
    /** @use HasFactory<\Database\Factories\AllTimeBestFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'activity_id', 'sport_id', 'distance', 'duration'];
}
