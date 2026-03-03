<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan';

    protected $fillable = [
        'kategori_layanan_id',
        'layanan',
        'harga_weekday',
        'harga_weekend',
        'qty',
        'status_aktif',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriLayanan::class, 'kategori_layanan_id');
    }

    // Accessor untuk harga otomatis sesuai hari
    public function getHargaAttribute()
    {
        $today = Carbon::now()->dayOfWeek; // 0 = Sunday, 6 = Saturday

        if ($today == 0 || $today == 6) {
            return $this->harga_weekend;
        }
        return $this->harga_weekday;
    }
}
