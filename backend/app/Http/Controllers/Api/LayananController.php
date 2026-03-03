<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function index()
    {
        return response()->json(Layanan::with('kategori')->get());
    }

    public function show($id)
    {
        $layanan = Layanan::with('kategori')->find($id);
        if (!$layanan) return response()->json(['message' => 'Layanan tidak ditemukan'], 404);
        return response()->json($layanan);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori_layanan_id' => 'required|exists:kategori_layanan,id',
            'layanan' => 'required|string',
            'harga_weekday' => 'required|numeric',
            'harga_weekend' => 'required|numeric',
            'qty' => 'sometimes|integer',
            'status_aktif' => 'sometimes|boolean',
        ]);

        $layanan = Layanan::create($data);
        return response()->json($layanan, 201);
    }

    public function update(Request $request, $id)
    {
        $layanan = Layanan::find($id);
        if (!$layanan) return response()->json(['message' => 'Layanan tidak ditemukan'], 404);

        $data = $request->validate([
            'kategori_layanan_id' => 'sometimes|exists:kategori_layanan,id',
            'layanan' => 'sometimes|string',
            'harga_weekday' => 'sometimes|numeric',
            'harga_weekend' => 'sometimes|numeric',
            'qty' => 'sometimes|integer',
            'status_aktif' => 'sometimes|boolean',
        ]);

        $layanan->update($data);
        return response()->json($layanan);
    }

    public function destroy($id)
    {
        $layanan = Layanan::find($id);
        if (!$layanan) return response()->json(['message' => 'Layanan tidak ditemukan'], 404);

        $layanan->delete();
        return response()->json(['message' => 'Layanan dihapus']);
    }
}
