<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAllergy extends Model
{
    use HasFactory;

    protected $table = 'master_allergies';

    protected $fillable = [
        'code',
        'category',
        'allergen_name',
        'severity_default',
        'deskripsi',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function patientAllergies()
    {
        return $this->hasMany(PatientAllergy::class, 'allergy_id');
    }
}
