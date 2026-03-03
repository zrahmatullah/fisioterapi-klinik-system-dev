<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAdminController extends Controller
{
    public function index()
    {
        /* =========================
           KPI
        ========================= */

        $totalPasien = DB::table('profile_anak')->count();

        $terapiAktif = DB::table('registrasi_anak as r')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('pelayanan_terapi_anak as pta')
                    ->whereColumn('pta.registrasi_anak_id', 'r.id');
            })
            ->count();

        $totalTerapis = DB::table('user_profile as u')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('pelayanan_terapi_anak as pta')
                    ->whereColumn('pta.terapis_id', 'u.id');
            })
            ->count();

        $pendapatanBulanIni = DB::table('pembayaran_registrasi')
            ->whereNotNull('tanggal_bayar')
            ->whereMonth('tanggal_bayar', Carbon::now()->month) // disini ambil tgl transaksi perbulan
            ->whereYear('tanggal_bayar', Carbon::now()->year) // sama pertahun
            ->sum('jumlah_bayar');

        /* =========================
           LINE – Pendapatan 6 bulan
        ========================= */
        $pendapatan = DB::table('pembayaran_registrasi')
            ->whereNotNull('tanggal_bayar')
            ->where('status', 'lunas') // ⬅️ ini yang benar
            ->whereBetween('tanggal_bayar', [
                Carbon::now()->subMonths(5)->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->select(
                DB::raw("TO_CHAR(tanggal_bayar, 'Mon') as bulan"),
                DB::raw('SUM(jumlah_bayar) as total'),
                DB::raw('MIN(tanggal_bayar) as sort_date')
            )
            ->groupBy(DB::raw("TO_CHAR(tanggal_bayar, 'Mon')"))
            ->orderBy('sort_date')
            ->get();


        /* =========================
           BAR – Tindakan Terapi
        ========================= */
        $tindakan = DB::table('pelayanan_terapi_anak as pta')
            ->join('layanan as l', 'l.id', '=', 'pta.layanan_id')
            ->select(
                'l.layanan',
                DB::raw('COUNT(pta.id) as total')
            )
            ->groupBy('l.layanan')
            ->orderByDesc('total')
            ->get();

        /* =========================
           DOUGHNUT – Status Pasien
        ========================= */
        $pasienAktif = DB::table('registrasi_anak as r')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('pelayanan_terapi_anak as pta')
                    ->whereColumn('pta.registrasi_anak_id', 'r.id');
            })
            ->count();

        $pasienPending = DB::table('registrasi_anak as r')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('pelayanan_terapi_anak as pta')
                    ->whereColumn('pta.registrasi_anak_id', 'r.id');
            })
            ->count();

        $pasienSelesai = max($totalPasien - ($pasienAktif + $pasienPending), 0);

        /* =========================
           TAMBAHAN DASHBOARD
        ========================= */
        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;

        // PIE – Layanan paling sering diambil
        $layananDiambil = DB::table('pelayanan_terapi_anak as pta')
            ->join('layanan as l', 'l.id', '=', 'pta.layanan_id')
            ->whereMonth('pta.created_at', $bulan)
            ->whereYear('pta.created_at', $tahun)
            ->select(
                'l.layanan',
                DB::raw('COUNT(pta.id) as total')
            )
            ->groupBy('l.layanan')
            ->orderByDesc('total')
            ->get();

        $layananDikeluhkan = DB::table('keluhan_anak')
            ->whereMonth('tanggal_keluhan', $bulan)
            ->whereYear('tanggal_keluhan', $tahun)
            ->whereNotNull('kategori_keluhan')
            ->select(
                'kategori_keluhan',
                DB::raw('COUNT(id) as total')
            )
            ->groupBy('kategori_keluhan')
            ->orderByDesc('total')
            ->get();

        // LINE – Perkembangan pendaftaran
        // $pendaftaran = DB::table('registrasi_anak')
        //     ->whereYear('created_at', $tahun)
        //     ->select(
        //         DB::raw("DATE_TRUNC('month', created_at) as bulan_date"),
        //         DB::raw("TO_CHAR(DATE_TRUNC('month', created_at), 'Mon YYYY') as bulan"),
        //         DB::raw('COUNT(id) as total')
        //     )
        //     ->groupBy(DB::raw("DATE_TRUNC('month', created_at)"))
        //     ->orderBy(DB::raw("DATE_TRUNC('month', created_at)"))
        //     ->get();

        $pendaftaran = DB::table('registrasi_anak')
        ->whereBetween('created_at', [
            '2025-12-01',
            '2026-01-31'
        ])
        ->select(
            DB::raw("DATE_TRUNC('month', created_at) as bulan_date"),
            DB::raw("TO_CHAR(DATE_TRUNC('month', created_at), 'Mon YYYY') as bulan"),
            DB::raw('COUNT(id) as total')
        )
        ->groupBy(DB::raw("DATE_TRUNC('month', created_at)"))
        ->orderBy(DB::raw("DATE_TRUNC('month', created_at)"))
        ->get();

        /* =========================
           RESPONSE
        ========================= */
        return response()->json([
            'kpi' => [
                'total_pasien' => $totalPasien,
                'terapi_aktif' => $terapiAktif,
                'pendapatan_bulan_ini' => (int) $pendapatanBulanIni,
                'terapis' => $totalTerapis,
            ],
            'pendapatan' => [
                'labels' => $pendapatan->pluck('bulan'),
                'data'   => $pendapatan->pluck('total'),
            ],
            'tindakan' => [
                'labels' => $tindakan->pluck('layanan'),
                'data'   => $tindakan->pluck('total'),
            ],
            'status_pasien' => [
                'aktif'   => $pasienAktif,
                'pending' => $pasienPending,
                'selesai' => $pasienSelesai,
            ],
            'tambahan_dashboard' => [
                'layanan_diambil' => [
                    'labels' => $layananDiambil->pluck('layanan'),
                    'data'   => $layananDiambil->pluck('total'),
                ],
                'layanan_dikeluhkan' => [
                    'labels' => $layananDikeluhkan->pluck('kategori_keluhan'), // ✅ FIX
                    'data'   => $layananDikeluhkan->pluck('total'),
                ],
                'pendaftaran' => [
                    'labels' => $pendaftaran->pluck('bulan'),
                    'data'   => $pendaftaran->pluck('total'),
                ],
            ]
        ]);
    }
}
