<?php

namespace App\Http\Controllers\Api;

use App\Models\ProfileAnak;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\PembayaranRegistrasi;

class ProfileAnakController extends Controller
{
    // GET /api/profile-anak
    public function index()
    {
        return ProfileAnak::with(['orangTua', 'agama', 'jenisKelamin'])->get();
    }

    // POST /api/profile-anak
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_anak' => 'required|string|max:255',
            'id_orang_tua' => 'required|exists:user_profile,id',
            'agama_id' => 'required|exists:agama_m,id',
            'jenis_kelamin_id' => 'required|exists:jenis_kelamin,id',
            'NIK' => 'required|string|max:20|unique:profile_anak,NIK',
            'alamat' => 'nullable|string',
            'umur' => 'nullable|integer|min:0',
            'tanggal_lahir' => 'nullable|date',
            'tempat_lahir' => 'nullable|string|max:255',
            'no_hp_orang_tua' => 'nullable|string|max:20',
            'status_aktif' => 'boolean',
        ]);

        $anak = ProfileAnak::create($validated);

        return response()->json(
            $anak->load(['orangTua', 'agama', 'jenisKelamin']),
            201
        );
    }

    // GET /api/profile-anak/{id}
    public function show($id)
    {
        return ProfileAnak::with(['orangTua', 'agama', 'jenisKelamin'])->findOrFail($id);
    }

    // PUT /api/profile-anak/{id}
    public function update(Request $request, $id)
    {
        $anak = ProfileAnak::findOrFail($id);

        $validated = $request->validate([
            'nama_anak' => 'sometimes|required|string|max:255',
            'id_orang_tua' => 'sometimes|required|exists:user_profile,id',
            'agama_id' => 'sometimes|required|exists:agama_m,id',
            'jenis_kelamin_id' => 'sometimes|required|exists:jenis_kelamin,id',
            'NIK' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('profile_anak', 'NIK')->ignore($anak->id),
            ],
            'alamat' => 'nullable|string',
            'umur' => 'nullable|integer|min:0',
            'tanggal_lahir' => 'nullable|date',
            'tempat_lahir' => 'nullable|string|max:255',
            'no_hp_orang_tua' => 'nullable|string|max:20',
            'status_aktif' => 'boolean',
        ]);

        $anak->update($validated);

        return response()->json(
            $anak->load(['orangTua', 'agama', 'jenisKelamin'])
        );
    }

    // DELETE /api/profile-anak/{id}    
    public function destroy($id)
    {
        ProfileAnak::findOrFail($id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    // GET /api/profile-anak-saya
    public function anakSaya(Request $request)
    {
        $user = $request->user();
        $profile_id = $user->user_profile_id ?? null;

        if (!$profile_id || $user->role_id != 15) {
            return response()->json([]);
        }

        return ProfileAnak::with(['orangTua', 'agama', 'jenisKelamin'])
            ->where('id_orang_tua', $profile_id)
            ->get();
    }

    public function riwayatPembayaranAnak(Request $request)
    {
        $user = $request->user();
        $profile_id = $user->user_profile_id ?? null;

        // validasi role orang tua
        if (!$profile_id || $user->role_id != 15) {
            return response()->json([]);
        }

        return PembayaranRegistrasi::with([
            'registrasiAnak.profileAnak',
            'registrasiAnak.ruangan',
            'registrasiAnak.terapis'
        ])
            ->whereHas('registrasiAnak.profileAnak', function ($q) use ($profile_id) {
                $q->where('id_orang_tua', $profile_id);
            })
            ->orderBy('tanggal_bayar', 'desc')
            ->get();
    }
}
