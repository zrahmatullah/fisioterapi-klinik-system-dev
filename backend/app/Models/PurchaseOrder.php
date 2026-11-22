<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'nomor_po',
        'purchase_requisition_id',
        'supplier_id',
        'tanggal_po',
        'total_estimasi',
        'status',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_po' => 'date',
        'total_estimasi' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(PoItem::class, 'purchase_order_id');
    }

    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id');
    }
}
