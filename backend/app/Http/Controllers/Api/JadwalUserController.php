<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalUser;
use Illuminate\Http\Request;

class JadwalUserController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalUser::with(['jadwalMaster', 'user']);

        if ($request->user_profile_id) {
            $query->where('user_profile_id', $request->user_profile_id);
        }

        $data = $query->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_profile_id' => 'required|exists:user_profile,id',
            'jadwal_master_id' => 'required|exists:jadwal_master,id',
            'status_aktif' => 'boolean'
        ]);

        $data = JadwalUser::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal user berhasil ditambahkan',
            'data' => $data
        ], 201);
    }

    public function show($id)
    {
        $data = JadwalUser::with(['jadwalMaster', 'user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $jadwalUser = JadwalUser::findOrFail($id);

        $validated = $request->validate([
            'status_aktif' => 'boolean'
        ]);

        $jadwalUser->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal user berhasil diperbarui',
            'data' => $jadwalUser
        ]);
    }

    public function destroy($id)
    {
        JadwalUser::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal user berhasil dihapus'
        ]);
    }

    public function sync(Request $request)
    {
        $validated = $request->validate([
            'user_profile_id' => 'required|exists:user_profile,id',
            'jadwal_master_id' => 'required|array',
            'jadwal_master_id.*' => 'exists:jadwal_master,id'
        ]);

        JadwalUser::where('user_profile_id', $validated['user_profile_id'])
            ->delete();

        foreach ($validated['jadwal_master_id'] as $jadwalId) {
            JadwalUser::create([
                'user_profile_id' => $validated['user_profile_id'],
                'jadwal_master_id' => $jadwalId,
                'status_aktif' => true
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal terapis berhasil disimpan'
        ]);
    }
}
