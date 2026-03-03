<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\UserProfile;
use App\Models\Role;

class UserLogin extends Authenticatable implements JWTSubject
{
    protected $table = 'user_login';

    protected $fillable = [
        'status_aktif',
        'username',
        'password',
        'user_profile_id',
        'role_id'
    ];

    protected $hidden = ['password'];

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // 🔗 RELASI KE user_profile
    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'user_profile_id');
    }

    // 🔗 RELASI KE role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
