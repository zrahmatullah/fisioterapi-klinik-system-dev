<?php

namespace App\Http\Controllers\Api;

use App\Models\UserLogin;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class MasterUserProfileController extends Controller
{
    public function index()
    {
        $data = UserProfile::with([
            'jenisUser:id,jenis_user',
            'jenisKelamin:id,nama',
            'userLogin.role'
        ])->orderBy('nama')->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'              => 'required|string|max:255',
            'alamat'            => 'nullable|string',
            'nip'               => 'nullable|string',
            'jenis_user_id'     => 'required|exists:jenis_user,id',
            'email'             => 'required|email|unique:user_profile,email',
            'no_telepon'        => 'nullable|string|max:20',
            'jenis_kelamin_id'  => 'nullable|exists:jenis_kelamin,id',
            'spesialisasi'      => 'nullable|string|max:255',
            'username'          => 'required|unique:user_login,username',
            'password'          => 'required|min:6',
            'role_id'           => 'required|exists:role,id',
        ]);

        $profile = UserProfile::create([
            'nama'              => $validated['nama'],
            'alamat'            => $validated['alamat'] ?? null,
            'nip'               => $validated['nip'] ?? null,
            'jenis_user_id'     => $validated['jenis_user_id'],
            'email'             => $validated['email'],
            'no_telepon'        => $validated['no_telepon'] ?? null,
            'jenis_kelamin_id'  => $validated['jenis_kelamin_id'] ?? null,
            'spesialisasi'      => $validated['spesialisasi'] ?? null,
            'status_aktif'      => true,
        ]);

        UserLogin::create([
            'user_profile_id' => $profile->id,
            'username'        => $validated['username'],
            'password'        => Hash::make($validated['password']),
            'role_id'         => $validated['role_id'],
            'status_aktif'    => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditambahkan',
            'data'    => $profile->load([
                'jenisUser:id,jenis_user',
                'jenisKelamin:id,nama',
                'userLogin.role'
            ]),
        ], 201);
    }

    public function show($id)
    {
        $data = UserProfile::with([
            'jenisUser:id,jenis_user',
            'jenisKelamin:id,nama',
            'userLogin.role'
        ])->findOrFail($id);

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $profile = UserProfile::with('userLogin')->findOrFail($id);

        $validated = $request->validate([
            'nama'              => 'required|string|max:255',
            'alamat'            => 'nullable|string',
            'nip'               => 'nullable|string',
            'jenis_user_id'     => 'required|exists:jenis_user,id',
            'email'             => 'required|email|unique:user_profile,email,' . $profile->id,
            'no_telepon'        => 'nullable|string|max:20',
            'jenis_kelamin_id'  => 'nullable|exists:jenis_kelamin,id',
            'spesialisasi'      => 'nullable|string|max:255',
            'username'          => 'required|unique:user_login,username,' . $profile->userLogin->id,
            'password'          => 'nullable|min:6',
            'role_id'           => 'required|exists:role,id',
        ]);

        $profile->update([
            'nama'              => $validated['nama'],
            'alamat'            => $validated['alamat'] ?? null,
            'nip'               => $validated['nip'] ?? null,
            'jenis_user_id'     => $validated['jenis_user_id'],
            'email'             => $validated['email'],
            'no_telepon'        => $validated['no_telepon'] ?? null,
            'jenis_kelamin_id'  => $validated['jenis_kelamin_id'] ?? null,
            'spesialisasi'      => $validated['spesialisasi'] ?? null,
        ]);

        if ($profile->wasChanged('email')) {
            $profile->update(['email_verified_at' => null]);
        }

        $profile->userLogin->update([
            'username' => $validated['username'],
            'role_id'  => $validated['role_id'],
            'password' => $validated['password']
                ? Hash::make($validated['password'])
                : $profile->userLogin->password,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate',
            'data'    => $profile->load([
                'jenisUser:id,jenis_user',
                'jenisKelamin:id,nama',
                'userLogin.role'
            ]),
        ]);
    }

    public function destroy($id)
    {
        $profile = UserProfile::with('userLogin')->findOrFail($id);

        if ($profile->userLogin) {
            $profile->userLogin->delete();
        }

        $profile->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);
    }

    public function storePublic(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string',
            'email'       => 'required|email|unique:user_profile,email',
            'no_telepon'  => 'required',
            'username'    => 'required|unique:user_login,username',
            'password'    => 'required|min:6',
        ]);

        DB::transaction(function () use ($request) {

            $userLogin = UserLogin::create([
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'role_id'  => 15,
            ]);

            $userProfile = UserProfile::create([
                'user_login_id' => $userLogin->id,
                'jenis_user_id' => 8,
                'nama'          => $request->nama,
                'email'         => $request->email,
                'no_telepon'    => $request->no_telepon,
            ]);

            $userLogin->update([
                'user_profile_id' => $userProfile->id
            ]);
        });

        return response()->json([
            'message' => 'Registrasi berhasil'
        ]);
    }
}
