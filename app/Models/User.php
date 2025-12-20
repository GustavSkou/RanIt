<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture_path'
    ];

    protected $attributes = [
        'profile_picture_path' => '/icons/social/default-profile-pic.png' // set to default non user generate image
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function followLists()
    {
        return $this->hasMany(FollowList::class, 'user_id', 'id');
    }

    public function following()
    {
        return $this->hasManyThrough(
            User::class,
            FollowList::class,
            'user_id',
            'id',
            'id',
            'follows_user_id'
        );
    }

    public function followers()
    {
        return $this->hasManyThrough(
            User::class,
            FollowList::class,
            'follows_user_id', // Foreign key on FollowList table
            'id',              // Foreign key on User table  
            'id',              // Local key on User table
            'user_id'          // Local key on FollowList table
        );
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /*public function hasGivenKudos($activityId)
    {
        kudosList::where('activity_id', $activityId)->where('user_id')
    }*/
}
