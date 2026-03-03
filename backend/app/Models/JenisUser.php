<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisUser extends Model
{
    protected $table = 'jenis_user';

    protected $fillable = [
        'status_aktif',
        'jenis_user'
    ];

    public function userProfile()
    {
        return $this->hasMany(UserProfile::class, 'jenis_user_id');
    }
}
