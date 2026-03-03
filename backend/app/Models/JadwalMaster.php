<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalMaster extends Model
{
    use HasFactory;

    protected $table = 'jadwal_master';

    protected $fillable = [
        'hari',
        'jam_mulai',
        'jam_selesai',
        'status_aktif'
    ];

    protected $casts = [
        'status_aktif' => 'boolean'
    ];

    public function jadwalUser()
    {
        return $this->hasMany(JadwalUser::class, 'jadwal_master_id');
    }
}
