<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukBatch extends Model
{
    use HasFactory;

    protected $table = 'produk_batch';

    protected $fillable = [
        'master_produk_id',
        'nomor_batch',
        'tanggal_kedaluwarsa',
        'stok',
        'harga_beli_satuan',
    ];

    protected $casts = [
        'tanggal_kedaluwarsa' => 'date',
        'stok' => 'integer',
        'harga_beli_satuan' => 'decimal:2',
    ];

    public function masterProduk()
    {
        return $this->belongsTo(MasterProduk::class, 'master_produk_id');
    }
}
