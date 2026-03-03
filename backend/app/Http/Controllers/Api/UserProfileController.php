<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    /**
     * GET /api/user-profile
     * ?jenis_user_id=9
     */
    public function index(Request $request)
    {
        $query = UserProfile::with([
            'jenisUser:id,jenis_user',
            'jenisKelamin:id,nama',
            'userLogin'
        ]);

        if ($request->filled('jenis_user_id')) {
            $query->where('jenis_user_id', $request->jenis_user_id);
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('nama')->get()
        ]);
    }

    /**
     * POST /api/user-profile
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'              => 'required|string|max:255',
            'jenis_user_id'     => 'required|exists:jenis_user,id',
            'email'             => 'nullable|email|unique:user_profile,email',
            'alamat'            => 'nullable|string',
            'no_telepon'        => 'nullable|string|max:20',
            'jenis_kelamin_id'  => 'nullable|exists:jenis_kelamin,id',
            'spesialisasi'      => 'nullable|string|max:255',
            'status_aktif'      => 'nullable|boolean',
        ]);

        $userProfile = UserProfile::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User profile berhasil dibuat',
            'data' => $userProfile->load([
                'jenisUser:id,jenis_user',
                'jenisKelamin:id,nama'
            ])
        ], 201);
    }


    /**
     * GET /api/user-profile/{id}
     */
    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => UserProfile::with([
                'jenisUser:id,jenis_user',
                'jenisKelamin:id,nama',
                'userLogin'
            ])->findOrFail($id)
        ]);
    }

    /**
     * PUT /api/user-profile/{id}
     */
    public function update(Request $request, $id)
    {
        $userProfile = UserProfile::findOrFail($id);

        $validated = $request->validate([
            'nama'              => 'sometimes|required|string|max:255',
            'email'             => 'nullable|email|unique:user_profile,email,' . $userProfile->id,
            'alamat'            => 'nullable|string',
            'no_telepon'        => 'nullable|string|max:20',
            'jenis_kelamin_id'  => 'nullable|exists:jenis_kelamin,id',
            'spesialisasi'      => 'nullable|string|max:255',
            'status_aktif'      => 'nullable|boolean',
        ]);

        $userProfile->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User profile berhasil diperbarui',
            'data' => $userProfile->load([
                'jenisUser:id,jenis_user',
                'jenisKelamin:id,nama'
            ])
        ]);
    }

    /**
     * DELETE /api/user-profile/{id}
     */
    public function destroy($id)
    {
        UserProfile::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'User profile berhasil dihapus'
        ]);
    }

    public function terapis()
    {
        $data = UserProfile::with([
            'jenisUser:id,jenis_user',
            'jenisKelamin:id,nama',
            'userLogin:id,user_profile_id,username',
            'jadwalUser.jadwalMaster'
        ])
            ->where('jenis_user_id', 9)
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
