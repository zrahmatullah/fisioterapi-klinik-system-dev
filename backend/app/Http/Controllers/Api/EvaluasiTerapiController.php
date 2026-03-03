<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\EvaluasiTerapi;
use App\Models\RegistrasiAnak;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf; // ⬅️ TAMBAHKAN INI


class EvaluasiTerapiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'registrasi_anak_id' => 'required|exists:registrasi_anak,id',
        ]);

        $registrasi = RegistrasiAnak::with([
            'pelayanans',
            'pelayanans.catatanAktivitas'
        ])->findOrFail($request->registrasi_anak_id);

        // 🔥 VALIDASI UTAMA
        $totalSesi = $registrasi->pelayanans->count();
        $totalCatatan = $registrasi->pelayanans
            ->filter(fn($p) => $p->catatanAktivitas)
            ->count();

        if ($totalSesi !== $totalCatatan) {
            return response()->json([
                'message' => 'Semua sesi terapi harus memiliki catatan aktivitas'
            ], 422);
        }

        // Cegah double evaluasi
        if ($registrasi->evaluasiTerapi) {
            return response()->json([
                'message' => 'Evaluasi terapi sudah pernah dibuat'
            ], 422);
        }

        $data = $request->all();
        $data['total_sesi'] = $totalSesi;

        $evaluasi = EvaluasiTerapi::create($data);

        return response()->json([
            'success' => true,
            'data' => $evaluasi
        ], 201);
    }

    public function showByRegistrasi($registrasiId)
    {
        return EvaluasiTerapi::where('registrasi_anak_id', $registrasiId)->first();
    }

    public function cetakPdf($id)
    {
        $evaluasi = EvaluasiTerapi::with([
            'registrasiAnak',
            'registrasiAnak.profileAnak',
            'registrasiAnak.terapis',
            'registrasiAnak.ruangan'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('cetak.evaluasi-terapi', [
            'data' => $evaluasi
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('evaluasi-terapi.pdf');
    }

}
