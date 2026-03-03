<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // <<< tambahkan ini

use App\Models\JenisUser;
use Illuminate\Http\Request;

class JenisUserController extends Controller
{
    public function index()
    {
        return response()->json(JenisUser::all(), 200);
    }


    public function store(Request $request)
    {
        return JenisUser::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $data = JenisUser::findOrFail($id);
        $data->update($request->all());
        return $data;
    }

    public function destroy($id)
    {
        JenisUser::findOrFail($id)->delete();
        return response()->json(['message' => 'Berhasil dihapus']);
    }
}
