<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranRegistrasi extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_registrasi';

    protected $fillable = [
        'registrasi_anak_id',
        'promo_id',
        'nomor_pembayaran',
        'tanggal_bayar',
        'total_tagihan',
        'jumlah_bayar',
        'metode_pembayaran',
        'status',
        'keterangan',
        'bukti_pembayaran',
        'status_verifikasi',
    ];

    public function registrasi()
    {
        return $this->belongsTo(
            RegistrasiAnak::class,
            'registrasi_anak_id'
        );
    }

    public function registrasiAnak()
    {
        return $this->belongsTo(
            RegistrasiAnak::class,
            'registrasi_anak_id'
        );
    }

    public function promo()
    {
        return $this->belongsTo(
            Promosi::class,
            'promo_id'
        );
    }
}