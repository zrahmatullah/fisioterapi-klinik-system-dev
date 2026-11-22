<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrnItem extends Model
{
    use HasFactory;

    protected $table = 'grn_items';

    protected $fillable = [
        'goods_receipt_id',
        'master_produk_id',
        'po_item_id',
        'nomor_batch',
        'tanggal_kedaluwarsa',
        'qty_diterima',
        'harga_beli_satuan',
        'subtotal',
    ];

    protected $casts = [
        'tanggal_kedaluwarsa' => 'date',
        'qty_diterima' => 'integer',
        'harga_beli_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class, 'goods_receipt_id');
    }

    public function masterProduk()
    {
        return $this->belongsTo(MasterProduk::class, 'master_produk_id');
    }
}
