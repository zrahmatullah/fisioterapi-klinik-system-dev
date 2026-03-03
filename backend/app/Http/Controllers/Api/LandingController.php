<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    public function index()
    {
        /* ======================
           HERO STAT
        ====================== */
        $jumlahLayanan = DB::table('layanan')->count();
        $jumlahTerapis = DB::table('user_profile')->count();
        $jumlahSesi    = DB::table('pelayanan_terapi_anak')->count();

        /* ======================
           TERAPIS
        ====================== */
        $terapis = DB::table('user_profile')
            ->select('nama', 'spesialisasi')
            ->where('jenis_user_id', 9)
            ->limit(6)
            ->get();

        /* ======================
           LAYANAN
        ====================== */
        $layanan = DB::table('layanan')
            ->select('layanan as title')
            ->get();

        /* ======================
           PAKET / HARGA
        ====================== */
        $paket = DB::table('layanan')
            ->select(
                'layanan as nama',
                'harga_weekday',
                'harga_weekend'
            )
            ->whereNotNull('harga_weekday')
            ->get();

        /* ======================
           PROMOSI (SESUAI TABEL)
        ====================== */
        $promosi = DB::table('promosi')
            ->select(
                'id',
                'kode_promo',
                'nama_promo',
                'deskripsi',
                'tipe_diskon',
                'nilai_diskon',
                'tanggal_mulai',
                'tanggal_selesai'
            )
            ->where('status_aktif', 't') // PostgreSQL boolean
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'stat' => [
                'layanan' => $jumlahLayanan,
                'terapis' => $jumlahTerapis,
                'sesi'    => $jumlahSesi,
            ],
            'terapis' => $terapis,
            'layanan' => $layanan,
            'paket'   => $paket,
            'promosi' => $promosi, // ✅ SUDAH SESUAI
        ]);
    }
}
