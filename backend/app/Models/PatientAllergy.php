<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAllergy extends Model
{
    use HasFactory;

    protected $table = 'patient_allergies';

    protected $fillable = [
        'profile_anak_id',
        'allergy_id',
        'custom_reaction',
        'severity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }

    public function allergy()
    {
        return $this->belongsTo(MasterAllergy::class, 'allergy_id');
    }
}
