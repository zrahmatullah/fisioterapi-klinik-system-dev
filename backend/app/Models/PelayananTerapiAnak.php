<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelayananTerapiAnak extends Model
{
    use HasFactory;

    protected $table = 'pelayanan_terapi_anak';

    protected $fillable = [
        'registrasi_anak_id',
        'layanan_id',
        'terapis_id',
        'qty',
        'harga',
        'tanggal_penjadwalan',
        'jam_mulai',
        'jam_selesai',
        'status',
        'tanggal_reschedule_request',
    ];

    public function registrasi()
    {
        return $this->belongsTo(RegistrasiAnak::class, 'registrasi_anak_id');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function terapis()
    {
        return $this->belongsTo(UserProfile::class, 'terapis_id');
    }
    public function catatanAktivitas()
    {
        return $this->hasOne(
            CatatanAktivitasAnak::class,
            'pelayanan_terapi_anak_id'
        );
    }
}