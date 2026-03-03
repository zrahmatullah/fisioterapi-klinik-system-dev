<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::with('jenisPegawai', 'userLogin')->get();
        return response()->json($pegawai);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pegawai' => 'required|string|max:255',
            'jenis_pegawai_id' => 'required|exists:jenis_pegawai,id',
            'alamat' => 'nullable|string',
            'nip' => 'nullable|string|max:50',
            'status_aktif' => 'required|boolean',
        ]);

        $pegawai = Pegawai::create($request->all());
        return response()->json($pegawai, 201);
    }

    public function show($id)
    {
        $pegawai = Pegawai::with('jenisPegawai', 'userLogin')->findOrFail($id);
        return response()->json($pegawai);
    }

    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update($request->all());
        return response()->json($pegawai);
    }

    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
