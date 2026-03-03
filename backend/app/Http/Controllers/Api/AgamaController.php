<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agama;
use Illuminate\Http\Request;

class AgamaController extends Controller
{
    public function index()
    {
        return response()->json(Agama::orderBy('nama')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:agama_m,nama'
        ]);

        return response()->json(
            Agama::create($validated),
            201
        );
    }

    public function show($id)
    {
        return response()->json(
            Agama::findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $agama = Agama::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:agama_m,nama,' . $agama->id
        ]);

        $agama->update($validated);

        return response()->json($agama);
    }

    public function destroy($id)
    {
        Agama::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Agama berhasil dihapus'
        ]);
    }
}
