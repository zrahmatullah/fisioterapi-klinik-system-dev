<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceClaim extends Model
{
    use HasFactory;

    protected $table = 'insurance_claims';

    protected $fillable = [
        'nomor_pengajuan',
        'guarantor_id',
        'invoice_id',
        'profile_anak_id',
        'nomor_gl',
        'nominal_diajukan',
        'nominal_disetujui',
        'status',
        'tgl_pengajuan',
        'tgl_pembayaran',
        'catatan',
    ];

    protected $casts = [
        'nominal_diajukan' => 'decimal:2',
        'nominal_disetujui' => 'decimal:2',
        'tgl_pengajuan' => 'date',
        'tgl_pembayaran' => 'date',
    ];

    public function guarantor()
    {
        return $this->belongsTo(Guarantor::class, 'guarantor_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }
}
