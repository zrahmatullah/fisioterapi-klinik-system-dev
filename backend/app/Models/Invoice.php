<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoices';

    protected $fillable = [
        'nomor_invoice',
        'profile_anak_id',
        'registrasi_anak_id',
        'subtotal',
        'diskon',
        'pajak',
        'total_tagihan',
        'total_terbayar',
        'sisa_tagihan',
        'status',
        'jenis_penjamin',
        'nomor_penjamin',
        'payment_track',
        'guarantor_id',
        'patient_insurance_id',
        'covered_amount',
        'excess_amount',
        'nominal_klaim_asuransi',
        'selisih_excess_cash',
        'excess_cash_paid',
        'is_paket_lunas',
        'ar_status',
        'nomor_klaim',
        'tgl_klaim_diajukan',
        'tgl_klaim_lunas',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'pajak' => 'decimal:2',
        'total_tagihan' => 'decimal:2',
        'total_terbayar' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
        'covered_amount' => 'decimal:2',
        'excess_amount' => 'decimal:2',
        'tgl_klaim_diajukan' => 'date',
        'tgl_klaim_lunas' => 'date',
    ];

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }

    public function registrasiAnak()
    {
        return $this->belongsTo(RegistrasiAnak::class, 'registrasi_anak_id');
    }

    public function guarantor()
    {
        return $this->belongsTo(Guarantor::class, 'guarantor_id');
    }

    public function patientInsurance()
    {
        return $this->belongsTo(PatientInsurance::class, 'patient_insurance_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(PembayaranTransaksi::class, 'invoice_id');
    }

    public function claims()
    {
        return $this->hasMany(InsuranceClaim::class, 'invoice_id');
    }
}
