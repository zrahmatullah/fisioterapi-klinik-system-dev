<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promosi;
use Illuminate\Http\Request;

class PromosiController extends Controller
{
    /**
     * GET /api/promosi
     * List semua promo
     */
    public function index()
    {
        $data = Promosi::orderByDesc('created_at')->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * GET /api/promosi/aktif
     * Promo aktif (dropdown pembayaran)
     */
    public function aktif()
    {
        $data = Promosi::aktif()
            ->orderBy('nama_promo')
            ->get();

        return response()->json($data);
    }

    /**
     * POST /api/promosi
     * Simpan promo baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_promo'      => 'required|string|unique:promosi,kode_promo',
            'nama_promo'      => 'required|string',
            'deskripsi'       => 'nullable|string',
            'tipe_diskon'     => 'required|in:persen,nominal',
            'nilai_diskon'    => 'required|numeric|min:0',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status_aktif'    => 'required|boolean',
        ]);

        $promosi = Promosi::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Promosi berhasil dibuat',
            'data' => $promosi
        ], 201);
    }

    /**
     * GET /api/promosi/{id}
     * Detail promo
     */
    public function show($id)
    {
        $promosi = Promosi::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $promosi
        ]);
    }

    /**
     * PUT /api/promosi/{id}
     * Update promo
     */
    public function update(Request $request, $id)
    {
        $promosi = Promosi::findOrFail($id);

        $validated = $request->validate([
            'kode_promo'      => 'required|string|unique:promosi,kode_promo,' . $id,
            'nama_promo'      => 'required|string',
            'deskripsi'       => 'nullable|string',
            'tipe_diskon'     => 'required|in:persen,nominal',
            'nilai_diskon'    => 'required|numeric|min:0',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status_aktif'    => 'required|boolean',
        ]);

        $promosi->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Promosi berhasil diperbarui',
            'data' => $promosi
        ]);
    }

    /**
     * DELETE /api/promosi/{id}
     * Hapus promo
     */
    public function destroy($id)
    {
        $promosi = Promosi::findOrFail($id);
        $promosi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Promosi berhasil dihapus'
        ]);
    }
}
