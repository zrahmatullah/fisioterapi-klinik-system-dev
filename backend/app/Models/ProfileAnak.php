<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileAnak extends Model
{
    protected $table = 'profile_anak';

    protected $fillable = [
        'status_aktif',
        'nama_anak',
        'id_orang_tua',
        'agama_id',
        'jenis_kelamin_id',
        'NIK',
        'alamat',
        'umur',
        'tanggal_lahir',
        'tempat_lahir',
        'no_hp_orang_tua',
    ];

    public function orangTua()
    {
        return $this->belongsTo(UserProfile::class, 'id_orang_tua');
    }

    public function jenisKelamin()
    {
        return $this->belongsTo(JenisKelamin::class, 'jenis_kelamin_id');
    }


    public function agama()
    {
        return $this->belongsTo(Agama::class, 'agama_id');
    }
    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'id_orang_tua');
    }
}
