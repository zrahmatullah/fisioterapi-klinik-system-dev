<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JenisPegawai;

class JenisPegawaiController extends Controller
{
    public function index()
    {
        $data = JenisPegawai::all();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_pegawai' => 'required|string|max:255',
            'status_aktif' => 'required|boolean',
        ]);

        $jenis = JenisPegawai::create($request->all());
        return response()->json($jenis, 201);
    }

    public function show($id)
    {
        $jenis = JenisPegawai::findOrFail($id);
        return response()->json($jenis);
    }

    public function update(Request $request, $id)
    {
        $jenis = JenisPegawai::findOrFail($id);
        $jenis->update($request->all());
        return response()->json($jenis);
    }

    public function destroy($id)
    {
        $jenis = JenisPegawai::findOrFail($id);
        $jenis->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
