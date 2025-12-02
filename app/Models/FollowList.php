<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowList extends Model
{
    /** @use HasFactory<\Database\Factories\FollowList> */
    use HasFactory;

    protected $fillable = ['user_id', 'follows_user_id'];

    public function user()
    {
        return $this->belongsTo(user::class, 'user_id', 'id');
    }

    public function follows_user()
    {
        return $this->belongsTo(user::class, 'follows_user_id', 'id');
    }
}
