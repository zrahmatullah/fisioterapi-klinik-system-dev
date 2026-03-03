<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanHistoryPromosiController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        $query = DB::table('promosi as p')
            ->leftJoin('pembayaran_registrasi as pr', 'pr.promo_id', '=', 'p.id');

        if ($start && $end) {
            $query->whereBetween('p.tanggal_mulai', [$start, $end]);
            $periode = Carbon::parse($start)->format('m/Y') . ' s/d ' . Carbon::parse($end)->format('m/Y');
        } else {
            $periode = 'Semua Periode';
        }

        $rows = $query
            ->select(
                'p.id',
                'p.kode_promo',
                'p.nama_promo',
                'p.tipe_diskon',
                'p.nilai_diskon',
                'p.deskripsi',
                'p.tanggal_mulai',
                'p.tanggal_selesai',
                'p.created_at',
                DB::raw('COUNT(pr.promo_id) as total_dipakai')
            )
            ->groupBy(
                'p.id',
                'p.kode_promo',
                'p.nama_promo',
                'p.tipe_diskon',
                'p.nilai_diskon',
                'p.deskripsi',
                'p.tanggal_mulai',
                'p.tanggal_selesai',
                'p.created_at'
            )
            ->orderBy('p.tanggal_mulai', 'asc')
            ->get();

        $data = $rows->map(function ($r, $i) {
            return [
                'no'             => $i + 1,
                'kode_promo'     => $r->kode_promo,
                'nama_promo'     => $r->nama_promo,
                'nominal_promo'  => $r->tipe_diskon === 'persen'
                    ? $r->nilai_diskon . ' %'
                    : number_format($r->nilai_diskon, 0, ',', '.'),
                'isi_promo'      => $r->deskripsi,
                'tanggal_promo'  => Carbon::parse($r->tanggal_mulai)->format('d/m/Y')
                    . ' - ' .
                    Carbon::parse($r->tanggal_selesai)->format('d/m/Y'),
                'waktu_promo'    => Carbon::parse($r->created_at)->format('H:i'),
                'total_dipakai'  => (int) $r->total_dipakai
            ];
        });

        return response()->json([
            'periode' => $periode,
            'data' => $data
        ]);
    }


    public function cetakPdf(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        $query = DB::table('promosi as p')
            ->leftJoin('pembayaran_registrasi as pr', 'pr.promo_id', '=', 'p.id');

        if ($start && $end) {
            $query->whereBetween('p.tanggal_mulai', [$start, $end]);
            $periode = Carbon::parse($start)->format('m/Y') . ' s/d ' . Carbon::parse($end)->format('m/Y');
        } else {
            $periode = 'Semua Periode';
        }

        $rows = $query
            ->select(
                'p.kode_promo',
                'p.nama_promo',
                'p.tipe_diskon',
                'p.nilai_diskon',
                'p.deskripsi',
                'p.tanggal_mulai',
                'p.tanggal_selesai',
                'p.created_at',
                DB::raw('COUNT(pr.promo_id) as total_dipakai')
            )
            ->groupBy(
                'p.kode_promo',
                'p.nama_promo',
                'p.tipe_diskon',
                'p.nilai_diskon',
                'p.deskripsi',
                'p.tanggal_mulai',
                'p.tanggal_selesai',
                'p.created_at'
            )
            ->orderBy('p.tanggal_mulai', 'asc')
            ->get();

        $pdf = Pdf::loadView('cetak.laporan-promosi', [
            'rows' => $rows,
            'periode' => $periode,
            'tanggalCetak' => Carbon::now()->format('d-m-Y')
        ])->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-promosi.pdf');
    }

}