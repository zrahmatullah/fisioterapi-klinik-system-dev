<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAppointment extends Model
{
    use HasFactory;

    protected $table = 'patient_appointments';

    protected $fillable = [
        'profile_anak_id',
        'paket_terapi_pasien_id',
        'layanan_id',
        'dokter_id',
        'terapis_id',
        'ruangan_id',
        'sesi_ke',
        'total_sesi',
        'tanggal_appointment',
        'jam_mulai',
        'jam_selesai',
        'status',
        'registrasi_anak_id',
        'catatan',
        'checked_in_at',
        'checked_in_by',
    ];

    protected $casts = [
        'tanggal_appointment' => 'date',
        'checked_in_at' => 'datetime',
        'sesi_ke' => 'integer',
        'total_sesi' => 'integer',
    ];

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }

    public function paketTerapiPasien()
    {
        return $this->belongsTo(PaketTerapiPasien::class, 'paket_terapi_pasien_id');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function dokter()
    {
        return $this->belongsTo(UserProfile::class, 'dokter_id');
    }

    public function terapis()
    {
        return $this->belongsTo(UserProfile::class, 'terapis_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function registrasiAnak()
    {
        return $this->belongsTo(RegistrasiAnak::class, 'registrasi_anak_id');
    }

    public function checkedInBy()
    {
        return $this->belongsTo(UserLogin::class, 'checked_in_by');
    }
}
