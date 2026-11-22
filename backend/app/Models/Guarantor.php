<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guarantor extends Model
{
    use HasFactory;

    protected $table = 'guarantors';

    protected $fillable = [
        'kode_penjamin',
        'nama_penjamin',
        'tipe',
        'kontak_klaim',
        'email_klaim',
        'alamat',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function patientInsurances()
    {
        return $this->hasMany(PatientInsurance::class, 'guarantor_id');
    }

    public function claims()
    {
        return $this->hasMany(InsuranceClaim::class, 'guarantor_id');
    }
}
