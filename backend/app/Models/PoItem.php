<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoItem extends Model
{
    use HasFactory;

    protected $table = 'po_items';

    protected $fillable = [
        'purchase_order_id',
        'master_produk_id',
        'qty_pesan',
        'qty_diterima',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'qty_pesan' => 'integer',
        'qty_diterima' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function masterProduk()
    {
        return $this->belongsTo(MasterProduk::class, 'master_produk_id');
    }
}
