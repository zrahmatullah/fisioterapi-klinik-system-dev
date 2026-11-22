<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformedConsent extends Model
{
    use HasFactory;

    protected $table = 'informed_consents';

    protected $fillable = [
        'profile_anak_id',
        'registrasi_anak_id',
        'judul_persetujuan',
        'tipe_persetujuan',
        'pernyataan',
        'nama_penandatangan',
        'hubungan_wali',
        'tanda_tangan_base64',
        'ip_address',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }

    public function registrasiAnak()
    {
        return $this->belongsTo(RegistrasiAnak::class, 'registrasi_anak_id');
    }
}
