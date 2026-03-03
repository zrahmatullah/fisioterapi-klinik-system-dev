<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assesment1 extends Model
{
    use HasFactory;

    protected $table = 'assesment_1';

    protected $fillable = [
        'registrasi_anak_id',
        'status_aktif',
        'umur',
        'informasi_subjektif',
        'informasi_objektif',
        'gangguan_kehamilan',
        'proses_kelahiran',
        'usia_kehamilan_lahir',
        'gangguan_melahirkan',
        'riwayat_kejang',
        'riwayat_kejang_ket',
        'konsumsi_obat_epilepsi',
        'konsumsi_obat_epilepsi_ket',
        'perkembangan_sesuai_usia',
        'perkembangan_sesuai_usia_ket',
        'disusui_ibu',
        'disusui_ibu_ket',
        'tv_gadget_addict',
        'tv_gadget_addict_ket',
        'sering_memutar_benda',
        'sering_memutar_benda_ket',
        'main_mobil_berulang',
        'main_mobil_berulang_ket',
        'flapping',
        'flapping_ket',
        'tantrum',
        'tantrum_ket',
        'kontak_mata',
        'kontak_mata_ket',
        'gangguan_makan_menelan',
        'gangguan_makan_menelan_ket',
        'komunikasi_dua_arah',
        'komunikasi_dua_arah_ket',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'riwayat_kejang' => 'boolean',
        'konsumsi_obat_epilepsi' => 'boolean',
        'perkembangan_sesuai_usia' => 'boolean',
        'disusui_ibu' => 'boolean',
        'tv_gadget_addict' => 'boolean',
        'sering_memutar_benda' => 'boolean',
        'main_mobil_berulang' => 'boolean',
        'flapping' => 'boolean',
        'tantrum' => 'boolean',
        'kontak_mata' => 'boolean',
        'gangguan_makan_menelan' => 'boolean',
        'komunikasi_dua_arah' => 'boolean',
    ];

    public function registrasiAnak()
    {
        return $this->belongsTo(RegistrasiAnak::class, 'registrasi_anak_id');
    }
}
