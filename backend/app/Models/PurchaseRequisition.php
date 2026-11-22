<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequisition extends Model
{
    use HasFactory;

    protected $table = 'purchase_requisitions';

    protected $fillable = [
        'nomor_pr',
        'unit_pemohon',
        'tanggal_pr',
        'alasan_kebutuhan',
        'status',
        'approved_by',
        'approved_at',
        'catatan_approval',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pr' => 'date',
        'approved_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(PrItem::class, 'purchase_requisition_id');
    }

    public function approver()
    {
        return $this->belongsTo(UserProfile::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(UserProfile::class, 'created_by');
    }
}
