<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PelayananTerapiAnak;

class RegistrasiAnak extends Model
{
    use HasFactory;

    protected $table = 'registrasi_anak';

    protected $fillable = [
        'profile_anak_id',
        'id_pasien',
        'no_regis',
        'tgl_regis',
        'terapis_id',
        'ruangan_id',
        'no_antrian',
        'status_kedatangan',
        'waktu_kedatangan',
        'status_pelayanan',
        'nama_ayah',
        'nama_ibu',
        'notelp',
        'usia_saat_menikah',
        'alamat',
        'keluhan_saat_ini',
        'kemampuan_saat_ini',
        'payment_track',
        'guarantor_id',
        'patient_insurance_id',
        'patient_therapy_package_id',
        'therapy_order_id',
        'antrian_nomor',
        'status_sesi',
        'sesi_ke',
        'total_sesi',
        'mood_anak',
        'durasi_menit',
        'home_program_catatan',
        'jadwal_hari',
        'jadwal_jam',
        'parent_confirmed_at',
    ];

    protected $casts = [
        'sesi_ke' => 'integer',
        'total_sesi' => 'integer',
        'durasi_menit' => 'integer',
        'parent_confirmed_at' => 'datetime',
    ];

    // ================= RELATIONS =================

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }

    public function terapis()
    {
        return $this->belongsTo(UserProfile::class, 'terapis_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function guarantor()
    {
        return $this->belongsTo(Guarantor::class, 'guarantor_id');
    }

    public function patientInsurance()
    {
        return $this->belongsTo(PatientInsurance::class, 'patient_insurance_id');
    }

    public function patientTherapyPackage()
    {
        return $this->belongsTo(PaketTerapiPasien::class, 'patient_therapy_package_id');
    }

    public function therapyOrder()
    {
        return $this->belongsTo(TherapyOrder::class, 'therapy_order_id');
    }

    public function pelayanans()
    {
        return $this->hasMany(
            PelayananTerapiAnak::class,
            'registrasi_anak_id'
        );
    }

    public function eResep()
    {
        return $this->hasOne(EResep::class, 'registrasi_anak_id');
    }

    public function orderPenunjangs()
    {
        return $this->hasMany(OrderPenunjang::class, 'registrasi_anak_id');
    }

    public function appointments()
    {
        return $this->hasMany(PatientAppointment::class, 'registrasi_anak_id');
    }

    public function invoices()
    {
        return $this->hasMany(
            Invoice::class,
            'registrasi_anak_id'
        );
    }

    public function pembayarans()
    {
        return $this->hasManyThrough(
            PembayaranTransaksi::class,
            Invoice::class,
            'registrasi_anak_id',
            'invoice_id'
        );
    }

    public function orangTua()
    {
        return $this->belongsTo(UserProfile::class, 'id_orang_tua');
    }

    public function catatanAktivitas()
    {
        return $this->hasMany(
            CatatanAktivitasAnak::class,
            'registrasi_anak_id'
        );
    }

    public function evaluasiTerapi()
    {
        return $this->hasOne(EvaluasiTerapi::class, 'registrasi_anak_id');
    }
}
