<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProduk extends Model
{
    use HasFactory;

    protected $table = 'master_produk';

    protected $fillable = [
        'produk_kategori_id',
        'kode_produk',
        'nama_produk',
        'nama_generik',
        'jenis',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok_minimum',
        'total_stok',
        'is_obat',
        'lokasi_rak',
        'status_aktif',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'stok_minimum' => 'integer',
        'total_stok' => 'integer',
        'is_obat' => 'boolean',
        'status_aktif' => 'boolean',
    ];

    public function batches()
    {
        return $this->hasMany(ProdukBatch::class, 'master_produk_id')->orderBy('tanggal_kedaluwarsa', 'asc');
    }

    public function kartuStoks()
    {
        return $this->hasMany(KartuStok::class, 'master_produk_id')->orderBy('tanggal', 'desc');
    }
}
