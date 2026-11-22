<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientVitalRecord extends Model
{
    use HasFactory;

    protected $table = 'patient_vital_records';

    protected $fillable = [
        'profile_anak_id',
        'registrasi_anak_id',
        'emr_record_id',
        'recorded_at',
        'recorded_by_id',
        'td_sistole',
        'td_diastole',
        'hr',
        'temp',
        'rr',
        'spo2',
        'bb',
        'tb',
        'lk',
        'vital_data',
        'status',
        'catatan',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'td_sistole' => 'integer',
        'td_diastole' => 'integer',
        'hr' => 'float',
        'temp' => 'float',
        'rr' => 'float',
        'spo2' => 'float',
        'bb' => 'float',
        'tb' => 'float',
        'lk' => 'float',
        'vital_data' => 'array',
    ];

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }

    public function registrasiAnak()
    {
        return $this->belongsTo(RegistrasiAnak::class, 'registrasi_anak_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(UserProfile::class, 'recorded_by_id');
    }
}
