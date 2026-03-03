<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanKeluhanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $query = DB::table('keluhan_anak as k')
            ->leftJoin('profile_anak as pa', 'pa.id', '=', 'k.profile_anak_id');

        if ($bulan && $tahun) {
            $query->whereMonth('k.tanggal_keluhan', (int) $bulan)
                ->whereYear('k.tanggal_keluhan', (int) $tahun);
        }

        $rows = $query
            ->orderBy('k.tanggal_keluhan', 'asc')
            ->select(
                'k.no_keluhan',
                'pa.nama_anak',
                'k.kategori_keluhan',
                'k.tanggal_keluhan',
                'k.isi',
                'k.status_tanggapan',
                'k.tanggapan_keluhan'
            )
            ->get();

        $data = $rows->map(function ($r, $i) {
            return [
                'no'               => $i + 1,
                'no_keluhan'       => $r->no_keluhan,
                'nama_pasien'      => $r->nama_anak ?? '-',
                'kategori_keluhan' => $r->kategori_keluhan ?? '-',
                'tanggal_keluhan' => Carbon::parse($r->tanggal_keluhan)->format('d/m/Y'),
                'isi_keluhan'      => $r->isi,
                'status_tanggapan' => $r->status_tanggapan,
                'tanggapan'        => $r->tanggapan_keluhan ?? '-',
            ];
        });

        return response()->json([
            'periode' => ($bulan && $tahun)
                ? sprintf('%02d/%d', $bulan, $tahun)
                : 'Semua Periode',
            'data' => $data
        ]);
    }

    public function cetakPdf(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        $query = DB::table('keluhan_anak as k')
            ->leftJoin('profile_anak as pa', 'pa.id', '=', 'k.profile_anak_id');

        if ($start && $end) {
            $query->whereBetween('k.tanggal_keluhan', [$start, $end]);

            $periode = Carbon::parse($start)->format('d/m/Y')
                . ' s/d ' .
                Carbon::parse($end)->format('d/m/Y');
        } else {
            $periode = 'Semua Periode';
        }

        $rows = $query
            ->orderBy('k.tanggal_keluhan', 'asc')
            ->select(
                'k.no_keluhan',
                'pa.nama_anak',
                'k.kategori_keluhan',
                'k.tanggal_keluhan',
                'k.isi',
                'k.status_tanggapan',
                'k.tanggapan_keluhan',
                'k.updated_at',
            )
            ->get();

        $pdf = Pdf::loadView('cetak.laporan-keluhan', [
            'rows'         => $rows,
            'periode'      => $periode,
            'tanggalCetak' => Carbon::now()->format('d/m/Y'),
        ])->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-keluhan.pdf');
    }
}