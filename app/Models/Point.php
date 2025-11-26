<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class point extends Model
{
    /** @use HasFactory<\Database\Factories\PointFactory> */
    use HasFactory;

    protected $fillable = ['latitude', 'longitude', 'elevation', 'timestamp', 'activity_id'];
}
