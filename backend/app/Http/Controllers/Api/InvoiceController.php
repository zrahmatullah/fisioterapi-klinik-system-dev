<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegistrasiAnak;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function show($registrasi_id)
    {
        $registrasi = RegistrasiAnak::with([
            'profileAnak',
            'terapis',
            'ruangan',
            'pembayarans',
            'pelayanans.layanan',
        ])->findOrFail($registrasi_id);

        // Pastikan sudah dibayar
        $pembayaran = $registrasi->pembayarans->last();
        if (!$pembayaran) {
            return response()->json(['message' => 'Belum ada pembayaran'], 404);
        }

        return response()->json([
            'registrasi' => $registrasi,
            'pembayaran' => $pembayaran,
        ]);
    }
}
