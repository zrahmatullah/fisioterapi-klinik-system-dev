<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PelayananTerapiAnak;
use App\Models\PembayaranRegistrasi;

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

    public function pelayanans()
    {
        return $this->hasMany(
            PelayananTerapiAnak::class,
            'registrasi_anak_id'
        );
    }

    public function pembayarans()
    {
        return $this->hasMany(
            PembayaranRegistrasi::class,
            'registrasi_anak_id'
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