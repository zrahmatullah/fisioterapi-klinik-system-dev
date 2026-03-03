<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalUser extends Model
{
    use HasFactory;

    protected $table = 'jadwal_user';

    protected $fillable = [
        'user_profile_id',
        'jadwal_master_id',
        'status_aktif'
    ];

    protected $casts = [
        'status_aktif' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_profile_id');
    }

    public function jadwalMaster()
    {
        return $this->belongsTo(JadwalMaster::class, 'jadwal_master_id');
    }
}
