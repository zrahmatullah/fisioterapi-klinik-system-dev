<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promosi extends Model
{
    use HasFactory;

    protected $table = 'promosi';

    protected $fillable = [
        'kode_promo',
        'nama_promo',
        'deskripsi',
        'tipe_diskon',
        'nilai_diskon',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_aktif',
    ];
    protected $casts = [
        'status_aktif'     => 'boolean',
        'nilai_diskon'     => 'decimal:2',
        'tanggal_mulai'    => 'date',
        'tanggal_selesai'  => 'date',
    ];
    public function layanan()
    {
        return $this->belongsToMany(
            Layanan::class,
            'layanan_promosi'
        );
    }
    public function scopeAktif($query)
    {
        return $query
            ->where('status_aktif', true)
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now());
    }
}
