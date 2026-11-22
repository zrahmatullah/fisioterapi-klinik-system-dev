<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientInsurance extends Model
{
    use HasFactory;

    protected $table = 'patient_insurances';

    protected $fillable = [
        'profile_anak_id',
        'guarantor_id',
        'no_polis',
        'no_surat_jaminan',
        'tgl_berlaku_mulai',
        'tgl_berlaku_selesai',
        'plafon_nominal',
        'kuota_sesi',
        'sisa_kuota_sesi',
        'dokumen_gl_path',
        'catatan',
        'status_aktif',
    ];

    protected $casts = [
        'tgl_berlaku_mulai' => 'date',
        'tgl_berlaku_selesai' => 'date',
        'plafon_nominal' => 'decimal:2',
        'kuota_sesi' => 'integer',
        'sisa_kuota_sesi' => 'integer',
        'status_aktif' => 'boolean',
    ];

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }

    public function guarantor()
    {
        return $this->belongsTo(Guarantor::class, 'guarantor_id');
    }

    public function isValid(): bool
    {
        $today = now()->toDateString();
        return $this->status_aktif &&
            $this->sisa_kuota_sesi > 0 &&
            $this->tgl_berlaku_mulai <= $today &&
            $this->tgl_berlaku_selesai >= $today;
    }
}
