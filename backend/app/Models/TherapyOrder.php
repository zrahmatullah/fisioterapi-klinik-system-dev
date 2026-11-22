<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TherapyOrder extends Model
{
    use HasFactory;

    protected $table = 'therapy_orders';

    protected $fillable = [
        'profile_anak_id',
        'registrasi_anak_id',
        'dokter_id',
        'terapis_id',
        'layanan_id',
        'diagnosis_klinis',
        'icd10_kode',
        'frekuensi_mingguan',
        'total_sesi_rekomendasi',
        'target_jangka_pendek',
        'target_jangka_panjang',
        'catatan_khusus',
        'status',
    ];

    protected $casts = [
        'total_sesi_rekomendasi' => 'integer',
    ];

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }

    public function registrasiAnak()
    {
        return $this->belongsTo(RegistrasiAnak::class, 'registrasi_anak_id');
    }

    public function dokter()
    {
        return $this->belongsTo(UserProfile::class, 'dokter_id');
    }

    public function terapis()
    {
        return $this->belongsTo(UserProfile::class, 'terapis_id');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }
}
