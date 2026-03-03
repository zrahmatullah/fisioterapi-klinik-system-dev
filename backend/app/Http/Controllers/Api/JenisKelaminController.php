<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisKelamin;
use Illuminate\Http\Request;

class JenisKelaminController extends Controller
{
    public function index()
    {
        return response()->json(JenisKelamin::orderBy('nama')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:20|unique:jenis_kelamin,nama'
        ]);

        return response()->json(
            JenisKelamin::create($validated),
            201
        );
    }

    public function show($id)
    {
        return response()->json(
            JenisKelamin::findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $jk = JenisKelamin::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:20|unique:jenis_kelamin,nama,' . $jk->id
        ]);

        $jk->update($validated);

        return response()->json($jk);
    }

    public function destroy($id)
    {
        JenisKelamin::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Jenis kelamin berhasil dihapus'
        ]);
    }
}
