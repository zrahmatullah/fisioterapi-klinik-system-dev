<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EResepItem extends Model
{
    use HasFactory;

    protected $table = 'e_resep_items';

    protected $fillable = [
        'e_resep_id',
        'master_produk_id',
        'nama_obat',
        'jumlah',
        'satuan',
        'signa',
        'waktu_minum',
        'catatan_racikan',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function eResep()
    {
        return $this->belongsTo(EResep::class, 'e_resep_id');
    }

    public function masterProduk()
    {
        return $this->belongsTo(MasterProduk::class, 'master_produk_id');
    }
}
