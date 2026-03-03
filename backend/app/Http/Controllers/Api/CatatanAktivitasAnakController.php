<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CatatanAktivitasAnak;
use Barryvdh\DomPDF\Facade\Pdf;

class CatatanAktivitasAnakController extends Controller
{
    public function byRegistrasi($registrasiId)
    {
        return CatatanAktivitasAnak::with([
            'pelayananTerapiAnak.layanan',
            'pelayananTerapiAnak.terapis'
        ])
            ->where('registrasi_anak_id', $registrasiId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'registrasi_anak_id' => 'required|exists:registrasi_anak,id',
            'pelayanan_terapi_anak_id' => 'required|exists:pelayanan_terapi_anak,id',
            'aktivitas_terapi' => 'nullable|string',
            'keterangan_terapi' => 'nullable|string',
            'tugas_rumah' => 'nullable|string',
            'checkin_sesi' => 'required|boolean',
        ]);

        $data = CatatanAktivitasAnak::updateOrCreate(
            [
                'pelayanan_terapi_anak_id' => $validated['pelayanan_terapi_anak_id']
            ],
            $validated
        );

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // public function cetak($id)
    // {
    //     dd($id);
    //     $data = CatatanAktivitasAnak::with([
    //         'pelayananTerapiAnak.layanan',
    //         'pelayananTerapiAnak.terapis',
    //         'registrasiAnak.profileAnak'
    //     ])->findOrFail($id);

    //     $pdf = Pdf::loadView('cetak.catatan-aktivitas-anak', [
    //         'data' => $data
    //     ])->setPaper('A4', 'portrait');

    //     return $pdf->stream('catatan-aktivitas-anak-' . $id . '.pdf');
    // }

    public function cetak($pelayananId)
    {
        $data = CatatanAktivitasAnak::where('pelayanan_terapi_anak_id', $pelayananId)
            ->with([
                'pelayananTerapiAnak.layanan',
                'pelayananTerapiAnak.terapis',
                'registrasiAnak.profileAnak'
            ])
            ->firstOrFail();

        $pdf = Pdf::loadView('cetak.catatan-aktivitas-anak', [
            'data' => $data
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('catatan-aktivitas-anak-' . $pelayananId . '.pdf');
}
}
