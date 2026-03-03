<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';
    protected $fillable = [
        'status_aktif',
        'jenis_pegawai_id',
        'nama_pegawai',
        'alamat',
        'nip'
    ];

    public function jenisPegawai()
    {
        return $this->belongsTo(JenisPegawai::class);
    }

    public function userLogin()
    {
        return $this->hasOne(UserLogin::class, 'pegawai_id');
    }
}
