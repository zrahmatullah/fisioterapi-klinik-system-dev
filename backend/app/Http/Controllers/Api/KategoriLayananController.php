<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriLayanan;
use Illuminate\Http\Request;

class KategoriLayananController extends Controller
{
    public function index()
    {
        return response()->json(KategoriLayanan::with('layanan')->get());
    }

    public function show($id)
    {
        $kategori = KategoriLayanan::with('layanan')->find($id);
        if (!$kategori) return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        return response()->json($kategori);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori_layanan' => 'required|string',
            'status_aktif' => 'boolean',
        ]);
        $kategori = KategoriLayanan::create($data);
        return response()->json($kategori, 201);
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriLayanan::find($id);
        if (!$kategori) return response()->json(['message' => 'Kategori tidak ditemukan'], 404);

        $data = $request->validate([
            'kategori_layanan' => 'sometimes|string',
            'status_aktif' => 'sometimes|boolean',
        ]);

        $kategori->update($data);
        return response()->json($kategori);
    }

    public function destroy($id)
    {
        $kategori = KategoriLayanan::find($id);
        if (!$kategori) return response()->json(['message' => 'Kategori tidak ditemukan'], 404);

        $kategori->delete();
        return response()->json(['message' => 'Kategori dihapus']);
    }
}
