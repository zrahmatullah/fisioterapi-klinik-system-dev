<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TherapyPackage extends Model
{
    use HasFactory;

    protected $table = 'therapy_packages';

    protected $fillable = [
        'nama_paket',
        'layanan_id',
        'jumlah_sesi',
        'harga_paket',
        'masa_berlaku_hari',
        'deskripsi',
        'status_aktif',
    ];

    protected $casts = [
        'jumlah_sesi' => 'integer',
        'harga_paket' => 'decimal:2',
        'masa_berlaku_hari' => 'integer',
        'status_aktif' => 'boolean',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }
}
