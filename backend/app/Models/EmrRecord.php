<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmrRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'emr_records';

    protected $fillable = [
        'no_emr',
        'profile_anak_id',
        'registrasi_anak_id',
        'terapis_id',
        'dokter_id',
        'tanggal_pemeriksaan',
        'jenis_layanan',
        'status',
        'locked_at',
        'signed_by_id',
        'signature_path',
        'sha256_hash',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'locked_at' => 'datetime',
    ];

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }

    public function registrasiAnak()
    {
        return $this->belongsTo(RegistrasiAnak::class, 'registrasi_anak_id');
    }

    public function terapis()
    {
        return $this->belongsTo(UserProfile::class, 'terapis_id');
    }

    public function dokter()
    {
        return $this->belongsTo(UserProfile::class, 'dokter_id');
    }

    public function soap()
    {
        return $this->hasOne(EmrSoap::class, 'emr_record_id');
    }

    public function assessments()
    {
        return $this->hasMany(EmrAssessment::class, 'emr_record_id');
    }

    public function addendums()
    {
        return $this->hasMany(EmrAddendum::class, 'emr_record_id')->orderBy('created_at', 'desc');
    }

    public function attachments()
    {
        return $this->hasMany(EmrAttachment::class, 'emr_record_id');
    }
}
