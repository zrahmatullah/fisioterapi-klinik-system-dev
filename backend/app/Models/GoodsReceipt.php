<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    use HasFactory;

    protected $table = 'goods_receipts';

    protected $fillable = [
        'nomor_grn',
        'purchase_order_id',
        'supplier_id',
        'nomor_surat_jalan',
        'nomor_faktur_supplier',
        'tanggal_terima',
        'penerima_id',
        'status',
        'total_nilai_barang',
        'catatan',
    ];

    protected $casts = [
        'tanggal_terima' => 'date',
        'total_nilai_barang' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function penerima()
    {
        return $this->belongsTo(UserProfile::class, 'penerima_id');
    }

    public function items()
    {
        return $this->hasMany(GrnItem::class, 'goods_receipt_id');
    }
}
