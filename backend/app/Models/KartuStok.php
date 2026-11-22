<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuStok extends Model
{
    use HasFactory;

    protected $table = 'kartu_stok';

    protected $fillable = [
        'master_produk_id',
        'produk_batch_id',
        'tanggal',
        'tipe_mutasi',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'referensi',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'jumlah' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
    ];

    public function masterProduk()
    {
        return $this->belongsTo(MasterProduk::class, 'master_produk_id');
    }

    public function batch()
    {
        return $this->belongsTo(ProdukBatch::class, 'produk_batch_id');
    }

    public function user()
    {
        return $this->belongsTo(UserLogin::class, 'user_id');
    }
}
