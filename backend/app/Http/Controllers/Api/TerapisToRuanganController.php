<?php

namespace App\Http\Controllers\Api;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TerapisToRuanganController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'terapis_id' => 'required|exists:user_profile,id',
            'ruangan_id' => 'required|exists:ruangan,id',
        ]);

        DB::table('terapis_to_ruangan')->updateOrInsert(
            [
                'terapis_id' => $request->terapis_id,
                'ruangan_id' => $request->ruangan_id
            ],
            ['status_aktif' => true]
        );

        return response()->json(['message' => 'Mapping berhasil']);
    }

    public function byTerapis($id)
    {
        $ruangan = UserProfile::with('ruangans')->findOrFail($id)->ruangans;
        return response()->json($ruangan);
    }

    public function destroy($terapis_id, $ruangan_id)
    {
        DB::table('terapis_to_ruangan')
            ->where(compact('terapis_id', 'ruangan_id'))
            ->delete();

        return response()->json(['message' => 'Mapping dihapus']);
    }

    public function index()
    {
        return DB::table('terapis_to_ruangan')
            ->join('user_profile as t', 't.id', '=', 'terapis_to_ruangan.terapis_id')
            ->join('ruangan as r', 'r.id', '=', 'terapis_to_ruangan.ruangan_id')
            ->select(
                'terapis_to_ruangan.id',
                't.nama as terapis',
                'r.ruangan as ruangan'
            )
            ->orderBy('t.nama')
            ->get();
    }

}

