<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrItem extends Model
{
    use HasFactory;

    protected $table = 'pr_items';

    protected $fillable = [
        'purchase_requisition_id',
        'master_produk_id',
        'qty_diminta',
        'qty_disetujui',
        'keterangan',
    ];

    protected $casts = [
        'qty_diminta' => 'integer',
        'qty_disetujui' => 'integer',
    ];

    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id');
    }

    public function masterProduk()
    {
        return $this->belongsTo(MasterProduk::class, 'master_produk_id');
    }
}
