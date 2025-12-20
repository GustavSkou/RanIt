<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\kudosList;

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
        'elevation',
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

    public function points()
    {
        return $this->hasMany(Point::class, 'activity_id', 'id');
    }

    public function kudosByAuth()
    {
        return kudosList::where('activity_id', $this->id)->where('user_id', Auth::user()->id)->count();
    }

    public function kudos_count()
    {
        return kudosList::where('activity_id', $this->id)->count();
    }

    public function movingTime()
    {
        $firstPoint = $this->points->first();
        $lastPoint = $this->points->last();
        $duration = strtotime($lastPoint['timestamp']) - strtotime($firstPoint['timestamp']);
        return $this->formatDuration($duration);
    }

    public function getFormattedDuration()
    {
        return $this->formatDuration($this->duration);
    }

    public function getFormattedDistance()
    {
        $distance = round($this->distance, 2);
        $formatted = sprintf('%s', $distance);
        return $formatted;
    }
    public function getDistanceType()
    {
        switch ($this->type) {
            default:
                return "km";
        }
    }

    public function getFormattedAverageSpeed()
    {
        if (!$this->average_speed || $this->average_speed <= 0) {
            return null;
        }

        switch ($this->type) {
            case 'running':
                $minutesPerKm = 60 / $this->average_speed;
                $minutes = floor($minutesPerKm);
                $seconds = round(($minutesPerKm - $minutes) * 60);
                return sprintf('%d:%02d', $minutes, $seconds);

            case 'cycling':
                return round($this->average_speed, 2);

            default:
                break;
        }
    }
    public function getSpeedType()
    {
        if (!$this->average_speed || $this->average_speed <= 0) {
            return null;
        }

        switch ($this->type) {
            case 'running':
                return " /km";

            case 'cycling':
                return " km/h";

            default:
                break;
        }
    }

    public function getFormattedAverageHeartRate()
    {
        return round($this->average_heart_rate, 0);
    }

    public function getFormattedElevation()
    {
        return round($this->elevation, 2) . "m";
    }

    private function formatDuration($duration)
    {
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        $seconds = $duration % 60;

        if ($hours > 0) {
            $formatted = sprintf('%2d:%02d:%02d', $hours, $minutes, $seconds);
            return $formatted;
        } elseif ($minutes > 0) {
            $formatted = sprintf('%2d:%02d', $minutes, $seconds);
            return $formatted;
        } else {
            $formatted = sprintf('%2d', $seconds);
            return $formatted;
        }
    }
}
