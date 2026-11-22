<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'surat_keluar';

    protected $fillable = [
        'nomor_surat',
        'surat_template_id',
        'profile_anak_id',
        'registrasi_anak_id',
        'penandatangan_id',
        'judul_surat',
        'parameter_data',
        'isi_final_html',
        'qr_verification_code',
        'file_pdf_path',
        'status',
        'tanggal_surat',
        'created_by',
    ];

    protected $casts = [
        'parameter_data' => 'array',
        'tanggal_surat' => 'date',
    ];

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }

    public function template()
    {
        return $this->belongsTo(SuratTemplate::class, 'surat_template_id');
    }

    public function penandatangan()
    {
        return $this->belongsTo(UserProfile::class, 'penandatangan_id');
    }
}
