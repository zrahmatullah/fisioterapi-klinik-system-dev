<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftKasir extends Model
{
    use HasFactory;

    protected $table = 'shift_kasir';

    protected $fillable = [
        'kasir_id',
        'waktu_buka',
        'waktu_tutup',
        'saldo_awal',
        'total_tunai_sistem',
        'total_non_tunai_sistem',
        'saldo_akhir_fisik',
        'selisih',
        'status',
        'catatan_shift',
    ];

    protected $casts = [
        'waktu_buka' => 'datetime',
        'waktu_tutup' => 'datetime',
        'saldo_awal' => 'decimal:2',
        'total_tunai_sistem' => 'decimal:2',
        'total_non_tunai_sistem' => 'decimal:2',
        'saldo_akhir_fisik' => 'decimal:2',
        'selisih' => 'decimal:2',
    ];

    public function kasir()
    {
        return $this->belongsTo(UserProfile::class, 'kasir_id');
    }
}
