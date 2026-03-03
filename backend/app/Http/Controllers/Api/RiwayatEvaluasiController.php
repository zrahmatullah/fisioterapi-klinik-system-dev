<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatEvaluasiController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::table('evaluasi_terapi as e')
            ->join('registrasi_anak as r', 'r.id', '=', 'e.registrasi_anak_id')
            ->join('profile_anak as pa', 'pa.id', '=', 'r.profile_anak_id')
            ->leftJoin('pelayanan_terapi_anak as pta', 'pta.registrasi_anak_id', '=', 'r.id')
            ->leftJoin('layanan as l', 'l.id', '=', 'pta.layanan_id')
            ->leftJoin('kategori_layanan as kl', 'kl.id', '=', 'l.kategori_layanan_id')
            ->select(
                'e.id',
                'r.id as registrasi_anak_id',
                'pa.nama_anak',

                // 🔥 TANGGAL PEMBUATAN
                DB::raw("TO_CHAR(e.created_at, 'DD/MM/YYYY') as tanggal_pembuatan"),

                // 🔥 KATEGORI LAYANAN
                DB::raw("COALESCE(kl.kategori_layanan, '-') as kategori_layanan"),

                'e.total_sesi',

                // 🔥 FIELD EVALUASI (WAJIB)
                'e.komponen_perilaku',
                'e.kondisi_awal',
                'e.kondisi_saat_ini',
                'e.program_lanjutan',
                'e.kesimpulan_hasil_followup',
                'e.kemampuan_sebelumnya',
                'e.peningkatan_kemampuan_saat_ini',
                'e.saran_terapi'
            )
            ->groupBy(
                'e.id',
                'r.id',
                'pa.nama_anak',
                'e.created_at',
                'kl.kategori_layanan',
                'e.total_sesi',
                'e.komponen_perilaku',
                'e.kondisi_awal',
                'e.kondisi_saat_ini',
                'e.program_lanjutan',
                'e.kesimpulan_hasil_followup',
                'e.kemampuan_sebelumnya',
                'e.peningkatan_kemampuan_saat_ini',
                'e.saran_terapi'
            )
            ->orderByDesc('e.created_at')
            ->get();

        return response()->json($data);
    }
}
