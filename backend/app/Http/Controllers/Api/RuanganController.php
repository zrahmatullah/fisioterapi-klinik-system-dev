<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index()
    {
        return response()->json(Ruangan::all());
    }

    public function show($id)
    {
        $ruangan = Ruangan::find($id);
        if (!$ruangan) return response()->json(['message' => 'Ruangan tidak ditemukan'], 404);
        return response()->json($ruangan);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ruangan' => 'required|string',
            'status_aktif' => 'boolean',
        ]);
        $ruangan = Ruangan::create($data);
        return response()->json($ruangan, 201);
    }

    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::find($id);
        if (!$ruangan) return response()->json(['message' => 'Ruangan tidak ditemukan'], 404);

        $data = $request->validate([
            'ruangan' => 'sometimes|string',
            'status_aktif' => 'sometimes|boolean',
        ]);

        $ruangan->update($data);
        return response()->json($ruangan);
    }

    public function destroy($id)
    {
        $ruangan = Ruangan::find($id);
        if (!$ruangan) return response()->json(['message' => 'Ruangan tidak ditemukan'], 404);

        $ruangan->delete();
        return response()->json(['message' => 'Ruangan dihapus']);
    }
}
