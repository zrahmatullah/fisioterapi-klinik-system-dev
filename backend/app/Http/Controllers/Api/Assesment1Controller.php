<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assesment1;
use Illuminate\Http\Request;

class Assesment1Controller extends Controller
{
    public function index()
    {
        $data = Assesment1::where('status_aktif', true)->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'registrasi_anak_id' => 'required|exists:registrasi_anak,id',
        ]);

        $assesment = Assesment1::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data assesment berhasil disimpan',
            'data' => $assesment
        ], 201);
    }

    public function show($id)
    {
        $assesment = Assesment1::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $assesment
        ]);
    }

    public function update(Request $request, $id)
    {
        $assesment = Assesment1::findOrFail($id);
        $assesment->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data assesment berhasil diperbarui',
            'data' => $assesment
        ]);
    }

    public function destroy($id)
    {
        $assesment = Assesment1::findOrFail($id);
        $assesment->update(['status_aktif' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Data assesment berhasil dinonaktifkan'
        ]);
    }
}
