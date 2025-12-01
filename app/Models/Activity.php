<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'device',
        'distance',
        'start_time',
        'duration',
        'average_speed',
        'average_heart_rate',
        'map_image_path',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function GetFormattedDuration()
    {
        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            $formatted = sprintf('%02dh %02dm %02ds', $hours, $minutes, $seconds);
            return $formatted;
        } elseif ($minutes > 0) {
            $formatted = sprintf('%02dm %02ds', $minutes, $seconds);
            return $formatted;
        } else {
            $formatted = sprintf('%02ds', $seconds);
            return $formatted;
        }
    }

    public function GetFormattedDistance()
    {
        $formatted = round($this->distance, 2);
        return $formatted;
    }

    public function GetFormattedAverageSpeed()
    {
        if ($this->type == 'ride') {
        }
        if (!$this->average_speed || $this->average_speed <= 0) {
            return null;
        }

        $minutesPerKm = 60 / $this->average_speed;

        // Split into minutes and seconds
        $minutes = floor($minutesPerKm);
        $seconds = round(($minutesPerKm - $minutes) * 60);

        return sprintf('%d:%02d /km', $minutes, $seconds);
    }
}
