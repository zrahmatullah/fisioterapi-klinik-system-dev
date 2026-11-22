<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ProfileAnak extends Model
{
    use SoftDeletes;

    protected $table = 'profile_anak';

    protected $fillable = [
        'no_rm',
        'status_aktif',
        'nama_anak',
        'id_orang_tua',
        'parent_user_id',
        'agama_id',
        'jenis_kelamin_id',
        'NIK',
        'alamat',
        'umur',
        'tanggal_lahir',
        'tempat_lahir',
        'no_hp_orang_tua',
        'foto_anak',
        'sekolah',
        'catatan_alergi',
        'diagnosis_awal',
        'rujukan_dari',
    ];

    protected $appends = ['usia_lengkap', 'usia_pediatrik'];

    public function getUsiaLengkapAttribute()
    {
        if (!$this->tanggal_lahir) {
            return $this->umur ? "{$this->umur} tahun" : '-';
        }
        $birth = Carbon::parse($this->tanggal_lahir);
        $diff = $birth->diff(Carbon::now());
        if ($diff->y > 0) {
            return "{$diff->y} thn {$diff->m} bln";
        }
        return "{$diff->m} bln {$diff->d} hr";
    }

    public function getUsiaPediatrikAttribute()
    {
        if (!$this->tanggal_lahir) {
            return $this->umur ? "{$this->umur} Tahun" : '-';
        }
        $birth = Carbon::parse($this->tanggal_lahir);
        $diff = $birth->diff(Carbon::now());
        return "{$diff->y} Tahun, {$diff->m} Bulan, {$diff->d} Hari";
    }

    public function orangTua()
    {
        return $this->belongsTo(UserProfile::class, 'id_orang_tua');
    }

    public function parentUser()
    {
        return $this->belongsTo(UserLogin::class, 'parent_user_id');
    }

    public function appointments()
    {
        return $this->hasMany(PatientAppointment::class, 'profile_anak_id');
    }

    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'id_orang_tua');
    }

    public function jenisKelamin()
    {
        return $this->belongsTo(JenisKelamin::class, 'jenis_kelamin_id');
    }

    public function agama()
    {
        return $this->belongsTo(Agama::class, 'agama_id');
    }

    public function registrasis()
    {
        return $this->hasMany(RegistrasiAnak::class, 'profile_anak_id');
    }

    public function walis()
    {
        return $this->hasMany(PasienWali::class, 'profile_anak_id');
    }

    public function informedConsents()
    {
        return $this->hasMany(InformedConsent::class, 'profile_anak_id');
    }

    public function emrRecords()
    {
        return $this->hasMany(EmrRecord::class, 'profile_anak_id');
    }

    public function goalTrackings()
    {
        return $this->hasMany(EmrGoalTracking::class, 'profile_anak_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'profile_anak_id');
    }

    public function paketTerapi()
    {
        return $this->hasMany(PaketTerapiPasien::class, 'profile_anak_id');
    }

    public function patientInsurances()
    {
        return $this->hasMany(PatientInsurance::class, 'profile_anak_id');
    }

    public function therapyOrders()
    {
        return $this->hasMany(TherapyOrder::class, 'profile_anak_id');
    }

    public function suratKeluars()
    {
        return $this->hasMany(SuratKeluar::class, 'profile_anak_id');
    }
}
