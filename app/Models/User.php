<?php

namespace App\Models;

use App\Models\Roles\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'mobile_number',
        'address',
        'role_id',
        'status',
        'profile_photo',
        'email_verified_at',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Relationship: Each user belongs to a specific role.
     */
    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    /**
     * Accessor to get full profile photo URL
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return url('storage/' . $this->profile_photo);
        }
        return url('images/default-avatar.png');
    }

    /**
     * Helper scope to filter active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
