<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Exports\LaporanPembayaranExport;

class LaporanPembayaranController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 Ambil filter
        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $bulan     = $request->bulan;
        $tahun     = $request->tahun;

        // 🔹 Query dasar
        $query = DB::table('pembayaran_registrasi as p')
            ->leftJoin('registrasi_anak as r', 'r.id', '=', 'p.registrasi_anak_id')
            ->leftJoin('profile_anak as pa', 'pa.id', '=', 'r.profile_anak_id')
            ->leftJoin('pelayanan_terapi_anak as pta', 'pta.registrasi_anak_id', '=', 'r.id')
            ->leftJoin('layanan as l', 'l.id', '=', 'pta.layanan_id')
            ->leftJoin('kategori_layanan as kl', 'kl.id', '=', 'l.kategori_layanan_id')
            ->where('p.status', 'lunas');

        // 🔹 PRIORITAS 1: RANGE TANGGAL
        if ($startDate && $endDate) {
            $query->whereBetween(
                'p.tanggal_bayar',
                [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]
            );
        }
        // 🔹 PRIORITAS 2: BULAN & TAHUN (fallback)
        elseif ($bulan && $tahun) {
            $query->whereMonth('p.tanggal_bayar', $bulan)
                ->whereYear('p.tanggal_bayar', $tahun);
        }

        // 🔹 Ambil data
        $rows = $query
            ->groupBy(
                'p.id',
                'p.nomor_pembayaran',
                'p.tanggal_bayar',
                'p.total_tagihan',
                'p.jumlah_bayar',
                'p.metode_pembayaran',
                'p.status',
                'pa.nama_anak',
                'kl.kategori_layanan',
                'l.layanan'
            )
            ->orderBy('p.tanggal_bayar', 'asc')
            ->select(
                'p.id',
                'p.nomor_pembayaran as no_invoice',
                'p.tanggal_bayar',
                'p.total_tagihan as sub_total',
                'p.jumlah_bayar as total',
                'p.metode_pembayaran',
                'pa.nama_anak as nama_pasien',
                'kl.kategori_layanan as kategori_layanan',
                'l.layanan as jenis_layanan',
                DB::raw('COUNT(pta.id) as jumlah_sesi')
            )
            ->get();

        // 🔹 Mapping ke UI
        $data = $rows->map(function ($item, $index) {
            return [
                'no'               => $index + 1,
                'no_invoice'       => $item->no_invoice,
                'nama_pasien'      => $item->nama_pasien ?? '-',
                'tanggal_bayar'    => Carbon::parse($item->tanggal_bayar)->format('d/m/Y'),
                'kategori_layanan' => $item->kategori_layanan ?? '-',
                'jenis_layanan'    => $item->jenis_layanan ?? '-',
                'jumlah_sesi'      => (int) $item->jumlah_sesi,
                'subtotal'         => (float) $item->sub_total,
                'total'            => (float) $item->total,
            ];
        });

        // 🔹 Label periode
        if ($startDate && $endDate) {
            $periode = Carbon::parse($startDate)->format('d/m/Y')
                . ' s/d ' .
                Carbon::parse($endDate)->format('d/m/Y');
        } elseif ($bulan && $tahun) {
            $periode = sprintf('%02d/%d', $bulan, $tahun);
        } else {
            $periode = '-';
        }

        return response()->json([
            'periode'     => $periode,
            'data'        => $data,
            'grand_total' => $data->sum('total'),
        ]);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new LaporanPembayaranExport(
                $request->start_date,
                $request->end_date
            ),
            'laporan_pembayaran.xlsx'
        );
    }

    public function cetakPdf(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        $query = DB::table('pembayaran_registrasi as p')
            ->leftJoin('registrasi_anak as r', 'r.id', '=', 'p.registrasi_anak_id')
            ->leftJoin('profile_anak as pa', 'pa.id', '=', 'r.profile_anak_id')
            ->leftJoin('pelayanan_terapi_anak as pta', 'pta.registrasi_anak_id', '=', 'r.id')
            ->leftJoin('layanan as l', 'l.id', '=', 'pta.layanan_id')
            ->leftJoin('kategori_layanan as kl', 'kl.id', '=', 'l.kategori_layanan_id')
            ->where('p.status', 'lunas');

        if ($start && $end) {
            $query->whereBetween('p.tanggal_bayar', [$start, $end]);
            $periode = \Carbon\Carbon::parse($start)->format('d/m/Y')
                . ' s/d ' .
                \Carbon\Carbon::parse($end)->format('d/m/Y');
        } else {
            $periode = 'Semua Periode';
        }

        $rows = $query
            ->groupBy(
                'p.id',
                'p.nomor_pembayaran',
                'p.tanggal_bayar',
                'pa.nama_anak',
                'kl.kategori_layanan',
                'l.layanan',
                'p.total_tagihan',
                'p.jumlah_bayar'
            )
            ->orderBy('p.tanggal_bayar', 'asc')
            ->select(
                'p.nomor_pembayaran',
                'pa.nama_anak',
                'p.tanggal_bayar',
                'kl.kategori_layanan',
                'l.layanan',
                DB::raw('COUNT(pta.id) as jumlah_sesi'),
                'p.total_tagihan',
                'p.jumlah_bayar'
            )
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'cetak.laporan-pembayaran',
            [
                'rows' => $rows,
                'periode' => $periode,
                'tanggalCetak' => now()->format('d/m/Y')
            ]
        )->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-pembayaran.pdf');
    }
}