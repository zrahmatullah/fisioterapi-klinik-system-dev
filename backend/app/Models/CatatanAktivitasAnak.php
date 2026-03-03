<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanAktivitasAnak extends Model
{
    use HasFactory;

    protected $table = 'catatan_aktivitas_anak';

    protected $fillable = [
        'registrasi_anak_id',
        'pelayanan_terapi_anak_id',
        'aktivitas_terapi',
        'keterangan_terapi',
        'tugas_rumah',
        'checkin_sesi',
    ];

    protected $casts = [
        'checkin_sesi' => 'boolean',
    ];

    public function registrasiAnak()
    {
        return $this->belongsTo(
            RegistrasiAnak::class,
            'registrasi_anak_id'
        );
    }

    public function pelayananTerapiAnak()
    {
        return $this->belongsTo(
            PelayananTerapiAnak::class,
            'pelayanan_terapi_anak_id'
        );
    }
}
