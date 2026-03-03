<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiTerapi extends Model
{
    protected $table = 'evaluasi_terapi';

    protected $fillable = [
        'registrasi_anak_id',
        'total_sesi',
        'komponen_perilaku',
        'kondisi_awal',
        'kondisi_saat_ini',
        'program_lanjutan',
        'kesimpulan_hasil_followup',
        'kemampuan_sebelumnya',
        'peningkatan_kemampuan_saat_ini',
        'saran_terapi'
    ];

    public function registrasiAnak()
    {
        return $this->belongsTo(RegistrasiAnak::class, 'registrasi_anak_id');
    }

    public function registrasi()
    {
        return $this->belongsTo(RegistrasiAnak::class, 'registrasi_anak_id');
    }

}
