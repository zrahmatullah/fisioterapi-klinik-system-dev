<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EvaluasiTerapi;
use Barryvdh\DomPDF\Facade\Pdf;
use Tymon\JWTAuth\Facades\JWTAuth;

class HasilEvaluasiAnakController extends Controller
{
    /**
     * ============================
     * HELPER AMBIL USER DARI TOKEN
     * ============================
     */
    private function userFromToken(Request $request)
    {
        $token = $request->bearerToken() ?? $request->query('token');

        if (!$token) {
            return null;
        }

        try {
            return JWTAuth::setToken($token)->authenticate();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * ============================
     * LIST HASIL EVALUASI (JSON)
     * ============================
     */
    public function evaluasiAnakSaya(Request $request)
    {
        $user = $request->user(); // axios (auth:api)

        if (!$user || $user->role_id != 15) {
            return response()->json([]);
        }

        $profileId = $user->user_profile_id;

        $data = EvaluasiTerapi::with('registrasi.profileAnak')
            ->whereHas('registrasi.profileAnak', function ($q) use ($profileId) {
                $q->where('id_orang_tua', $profileId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(
            $data->map(fn($e) => [
                'id' => $e->id,
                'tanggal_evaluasi' => $e->created_at->toDateString(),
                'nama_anak' => $e->registrasi->profileAnak->nama_anak ?? '-',
                'total_sesi' => $e->total_sesi,
                'komponen_perilaku' => $e->komponen_perilaku,
                'kondisi_awal' => $e->kondisi_awal,
                'kondisi_saat_ini' => $e->kondisi_saat_ini,
                'kemampuan_sebelumnya' => $e->kemampuan_sebelumnya,
                'peningkatan_kemampuan_saat_ini' => $e->peningkatan_kemampuan_saat_ini,
                'program_lanjutan' => $e->program_lanjutan,
                'kesimpulan_hasil_followup' => $e->kesimpulan_hasil_followup,
                'saran_terapi' => $e->saran_terapi,
            ])
        );
    }

    /**
     * ============================
     * CETAK SEMUA EVALUASI (PDF)
     * ============================
     */
    public function cetakEvaluasiAnakSaya(Request $request)
    {
        $user = $this->userFromToken($request);

        if (!$user || $user->role_id != 15) {
            abort(403);
        }

        $evaluasi = EvaluasiTerapi::with('registrasi.profileAnak')
            ->whereHas('registrasi.profileAnak', function ($q) use ($user) {
                $q->where('id_orang_tua', $user->user_profile_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if ($evaluasi->isEmpty()) {
            abort(404, 'Data evaluasi tidak ditemukan');
        }

        $pdf = Pdf::loadView('cetak.evaluasi-anak', [
            'evaluasi' => $evaluasi
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('evaluasi-terapi-semua.pdf');
    }

    /**
     * ============================
     * CETAK PER ITEM (PDF)
     * ============================
     */
    public function cetakEvaluasiAnakPerItem(Request $request, $evaluasiId)
    {
        $user = $this->userFromToken($request);

        if (!$user || $user->role_id != 15) {
            abort(403);
        }

        $evaluasi = EvaluasiTerapi::with('registrasi.profileAnak')
            ->where('id', $evaluasiId)
            ->whereHas('registrasi.profileAnak', function ($q) use ($user) {
                $q->where('id_orang_tua', $user->user_profile_id);
            })
            ->firstOrFail();

        $pdf = Pdf::loadView('cetak.evaluasi-anak-per-item', [
            'evaluasi' => $evaluasi
        ])->setPaper('A4', 'portrait');

        return $pdf->stream(
            'evaluasi-terapi-' . $evaluasi->id . '.pdf'
        );
    }
}
