<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPenunjang extends Model
{
    use HasFactory;

    protected $table = 'order_penunjang';

    protected $fillable = [
        'nomor_order',
        'registrasi_anak_id',
        'profile_anak_id',
        'dokter_id',
        'jenis_order',
        'nama_tindakan',
        'tarif',
        'catatan_klinis',
        'hasil_pemeriksaan',
        'alasan_batal',
        'status',
        'verified_at',
        'verified_by',
        'invoice_id',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function registrasiAnak()
    {
        return $this->belongsTo(RegistrasiAnak::class, 'registrasi_anak_id');
    }

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }

    public function dokter()
    {
        return $this->belongsTo(UserProfile::class, 'dokter_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(UserLogin::class, 'verified_by');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
