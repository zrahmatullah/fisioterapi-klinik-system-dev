<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profile';

    protected $fillable = [
        'status_aktif',
        'jenis_user_id',
        'nama',
        'alamat',
        'nip',
        'email',
        'no_telepon',
        'jenis_kelamin_id',
        'spesialisasi',
    ];

    public function jenisUser()
    {
        return $this->belongsTo(JenisUser::class, 'jenis_user_id');
    }

    public function jenisKelamin()
    {
        return $this->belongsTo(JenisKelamin::class, 'jenis_kelamin_id');
    }

    public function userLogin()
    {
        return $this->hasOne(UserLogin::class, 'user_profile_id');
    }

    public function sesiTerapi()
    {
        return $this->hasMany(
            PelayananTerapiAnak::class,
            'terapis_id'
        );
    }

    public function jadwalUser()
    {
        return $this->hasMany(JadwalUser::class, 'user_profile_id');
    }

    public function ruangans()
    {
        return $this->belongsToMany(
            Ruangan::class,
            'terapis_to_ruangan',
            'terapis_id',
            'ruangan_id'
        );
    }

}
