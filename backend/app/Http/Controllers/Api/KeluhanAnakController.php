<?php

namespace App\Http\Controllers\Api;

use App\Models\KeluhanAnak;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;

class KeluhanAnakController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ORANG TUA - LIST KELUHAN ANAK SAYA
    |--------------------------------------------------------------------------
    */
    public function indexOrangTua(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->user_profile_id) {
            return response()->json([], 403);
        }

        return KeluhanAnak::with('profileAnak')
            ->whereHas('profileAnak', function ($q) use ($user) {
                $q->where('id_orang_tua', $user->user_profile_id);
            })
            ->where('status_aktif', true)
            ->orderBy('tanggal_keluhan', 'desc')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | ORANG TUA - SIMPAN KELUHAN
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'profile_anak_id'  => 'required|exists:profile_anak,id',
            'kategori_keluhan' => 'required|string|max:100',
            'isi'              => 'required|string',
        ]);

        $keluhan = KeluhanAnak::create([
            'profile_anak_id'   => $request->profile_anak_id,
            'no_keluhan'        => 'KLH-' . now()->format('YmdHis'),
            'kategori_keluhan'  => $request->kategori_keluhan,
            'tanggal_keluhan'   => now(),
            'isi'               => $request->isi,
            'status_tanggapan'  => 'belum_ditanggapi',
            'status_aktif'      => true,
        ]);

        return response()->json([
            'message' => 'Keluhan berhasil dikirim',
            'data'    => $keluhan
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN - LIST SEMUA KELUHAN
    |--------------------------------------------------------------------------
    */
    public function indexAdmin(Request $request)
    {
        $user = $request->user();

        // ⛔ HANYA ADMIN
        if ((int) $user->role_id !== 13) {
            return response()->json([], 403);
        }

        return KeluhanAnak::with('anak')
            ->where('status_aktif', true)
            ->orderByRaw("
            CASE 
                WHEN status_tanggapan = 'belum_ditanggapi' THEN 0
                ELSE 1
            END
        ")
            ->orderBy('tanggal_keluhan', 'desc')
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - DETAIL KELUHAN
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        return KeluhanAnak::with('profileAnak')->findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN - TANGGAPI KELUHAN
    |--------------------------------------------------------------------------
    */
    public function tanggapi(Request $request, $id)
    {
        $request->validate([
            'tanggapan_keluhan' => 'required|string',
        ]);

        $keluhan = KeluhanAnak::findOrFail($id);

        $keluhan->update([
            'tanggapan_keluhan' => $request->tanggapan_keluhan,
            'status_tanggapan'  => 'sudah_ditanggapi',
        ]);

        return response()->json([
            'message' => 'Keluhan berhasil ditanggapi'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN - NONAKTIFKAN KELUHAN (OPSIONAL)
    |--------------------------------------------------------------------------
    */
    public function nonaktifkan($id)
    {
        $keluhan = KeluhanAnak::findOrFail($id);

        $keluhan->update([
            'status_aktif' => false
        ]);

        return response()->json([
            'message' => 'Keluhan berhasil dinonaktifkan'
        ]);
    }

    public function cetak($id)
    {
        $keluhan = KeluhanAnak::with([
            'anak',
            'anak.orangTua'
        ])->findOrFail($id);

        $pdf = Pdf::loadView(
            'cetak.keluhan-anak',
            compact('keluhan')
        )->setPaper('A4');

        return $pdf->stream(
            'Keluhan-' . $keluhan->no_keluhan . '.pdf'
        );
    }
}