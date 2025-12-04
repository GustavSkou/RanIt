<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected $attributes = [
        'type' => 'route'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function date()
    {
        $date = date('M d, Y', strtotime($this->start_time));
        return $date;
    }

    public function icon()
    {
        return $this->belongsTo(Icon::class, 'type', 'name');
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
        if (!$this->average_speed || $this->average_speed <= 0) {
            return null;
        }

        switch ($this->type) {
            case 'running':
                $minutesPerKm = 60 / $this->average_speed;
                $minutes = floor($minutesPerKm);
                $seconds = round(($minutesPerKm - $minutes) * 60);
                return sprintf('%d:%02d /km', $minutes, $seconds);

            case 'cycling':
                return round($this->average_speed, 2);

            default:
                break;
        }
    }

    public function GetFormattedAverageHeartRate()
    {
        return round($this->average_heart_rate, 0);
    }
}
