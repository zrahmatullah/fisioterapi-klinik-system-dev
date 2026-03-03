<?php

namespace App\Http\Controllers\Api;

use App\Models\Promosi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PembayaranRegistrasi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PembayaranRegistrasiController extends Controller
{
    public function index()
    {
        return PembayaranRegistrasi::with('registrasi')->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'registrasi_anak_id' => 'required|exists:registrasi_anak,id',
            'tanggal_bayar'      => 'required|date',
            'total_tagihan'      => 'required|numeric',
            'promo_id'           => 'nullable|exists:promosi,id',
            'metode_pembayaran'  => 'nullable|string',
            'keterangan'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $totalTagihan = $request->total_tagihan;
        $diskon = 0;

        if ($request->filled('promo_id')) {
            $promo = Promosi::aktif()->find($request->promo_id);
            if ($promo) {
                $diskon = $promo->tipe_diskon === 'persen'
                    ? ($promo->nilai_diskon / 100) * $totalTagihan
                    : $promo->nilai_diskon;
            }
        }

        $jumlahBayar = max(0, $totalTagihan - $diskon);

        $pembayaran = PembayaranRegistrasi::create([
            'registrasi_anak_id' => $request->registrasi_anak_id,
            'promo_id'           => $request->promo_id,
            'nomor_pembayaran'   => 'INV-' . date('YmdHis'),
            'tanggal_bayar'      => $request->tanggal_bayar,
            'total_tagihan'      => $totalTagihan,
            'jumlah_bayar'       => $jumlahBayar,
            'metode_pembayaran'  => $request->metode_pembayaran,
            'status'             => 'pending',
            'status_verifikasi'  => null,
            'keterangan'         => $request->keterangan
                ?? ($diskon > 0 ? 'Promo digunakan' : 'Menunggu pembayaran'),
        ]);

        return response()->json([
            'pembayaran'  => $pembayaran,
            'diskon'      => $diskon,
            'total_bayar' => $jumlahBayar
        ], 201);
    }


    public function show($id)
    {
        return PembayaranRegistrasi::with('registrasi')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $pembayaran = PembayaranRegistrasi::findOrFail($id);

        $pembayaran->update(
            $request->only([
                'tanggal_bayar',
                'metode_pembayaran',
                'status',
                'keterangan',
                'status_verifikasi'
            ])
        );

        return response()->json($pembayaran);
    }

    public function destroy($id)
    {
        PembayaranRegistrasi::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Pembayaran berhasil dihapus'
        ]);
    }

    public function byRegistrasi($registrasiId)
    {
        return PembayaranRegistrasi::where('registrasi_anak_id', $registrasiId)
            ->orderBy('tanggal_bayar', 'desc')
            ->get();
    }

    /**
     * 🔥 UPLOAD BUKTI PEMBAYARAN (ORANG TUA)
     */
    public function uploadBukti(Request $request)
    {
        $request->validate([
            'pembayaran_id'    => 'required|integer',
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if (!$request->hasFile('bukti_pembayaran')) {
            return response()->json([
                'message' => 'File tidak sampai ke server'
            ], 400);
        }

        $pembayaran = PembayaranRegistrasi::find($request->pembayaran_id);

        if (!$pembayaran) {
            return response()->json([
                'message' => 'Data pembayaran tidak ditemukan'
            ], 404);
        }

        if ($pembayaran->status === 'lunas') {
            return response()->json([
                'message' => 'Pembayaran sudah lunas'
            ], 400);
        }

        $file = $request->file('bukti_pembayaran');

        $path = $file->store('bukti-pembayaran', 'public');

        $pembayaran->update([
            'bukti_pembayaran'  => $path,
            'status_verifikasi' => 'menunggu',
        ]);

        return response()->json([
            'message' => 'Bukti pembayaran berhasil diupload, menunggu verifikasi admin'
        ]);
    }




    /**
     * 🔥 RIWAYAT PEMBAYARAN ORANG TUA
     */
    public function riwayatPembayaranAnak(Request $request)
    {
        $user = $request->user();
        $profile_id = $user->user_profile_id ?? null;

        // role orang tua = 15
        if (!$profile_id || (int) $user->role_id !== 15) {
            return response()->json([]);
        }

        return PembayaranRegistrasi::with([
            'registrasi.profileAnak',
            'registrasi.ruangan',
            'registrasi.terapis',
            'registrasi.pelayanans.layanan',
            'registrasi.pelayanans.terapis',
            'registrasi.pelayanans.catatanAktivitas',
        ])
            ->whereHas('registrasi.profileAnak', function ($q) use ($profile_id) {
                $q->where('id_orang_tua', $profile_id);
            })
            ->orderBy('tanggal_bayar', 'desc')
            ->get();
    }

    public function verifikasiAdmin(Request $request, $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:diterima,ditolak',
        ]);

        $pembayaran = PembayaranRegistrasi::findOrFail($id);

        if (!$pembayaran->bukti_pembayaran) {
            return response()->json([
                'message' => 'Belum ada bukti pembayaran'
            ], 400);
        }

        $data = [
            'status_verifikasi' => $request->status_verifikasi,
        ];

        if ($request->status_verifikasi === 'diterima') {
            $data['status'] = 'lunas';
        }

        $pembayaran->update($data);

        return response()->json([
            'message' => 'Verifikasi pembayaran berhasil',
            'data' => $pembayaran
        ]);
    }
}