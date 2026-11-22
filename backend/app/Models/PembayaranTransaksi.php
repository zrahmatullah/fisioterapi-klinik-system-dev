<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranTransaksi extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_transaksi';

    protected $fillable = [
        'invoice_id',
        'nomor_transaksi',
        'kasir_id',
        'shift_kasir_id',
        'metode_bayar',
        'jumlah_bayar',
        'kembalian',
        'nomor_referensi',
        'bukti_bayar',
        'status',
        'catatan',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function kasir()
    {
        return $this->belongsTo(UserProfile::class, 'kasir_id');
    }
}
