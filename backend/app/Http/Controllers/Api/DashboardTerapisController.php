<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegistrasiAnak;
use Illuminate\Http\Request;

class DashboardTerapisController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $terapis = $user->userProfile;

        if (!$terapis || $terapis->jenis_user_id != 9) {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
        }

        $data = RegistrasiAnak::with([
            'profileAnak.jenisKelamin',
            'profileAnak.userProfile',
            'ruangan',
            'pelayanans' => function ($q) {
                $q->with([
                    'layanan.kategori',   
                    'terapis',            
                    'catatanAktivitas'    
                ]);
            },
            'pembayarans'
        ])
            ->where('terapis_id', $terapis->id)
            ->orderBy('tgl_regis', 'desc')
            ->get();

        return response()->json($data);
    }
}
