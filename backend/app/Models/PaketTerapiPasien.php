<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTerapiPasien extends Model
{
    use HasFactory;

    protected $table = 'paket_terapi_pasien';

    protected $fillable = [
        'profile_anak_id',
        'layanan_id',
        'invoice_id',
        'nomor_paket',
        'nama_paket',
        'total_sesi',
        'sesi_terpakai',
        'sisa_sesi',
        'harga_paket',
        'is_lunas_awal',
        'status_bayar',
        'metode_pembayaran_awal',
        'tanggal_mulai',
        'tanggal_berakhir',
        'status',
    ];

    protected $casts = [
        'total_sesi' => 'integer',
        'sesi_terpakai' => 'integer',
        'sisa_sesi' => 'integer',
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function appointments()
    {
        return $this->hasMany(PatientAppointment::class, 'paket_terapi_pasien_id');
    }
}
