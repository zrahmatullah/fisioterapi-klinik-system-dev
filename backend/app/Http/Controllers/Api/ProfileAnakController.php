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
        $user = auth('api')->user();
        $query = ProfileAnak::with(['orangTua', 'agama', 'jenisKelamin']);

        if ($user) {
            // Super Admin, Kasir, Pendaftaran, Farmasi, Manajemen: see all
            if ($user->role_id === 13 || strtolower($user->username) === 'admin' || in_array($user->role_id, [16, 18, 19, 20, 21])) {
                return $query->get();
            }

            // Orang Tua (Role 15): STRICTLY scoped to their own children
            if ($user->role_id === 15) {
                return $query->where('id_orang_tua', $user->user_profile_id)->get();
            }

            // Terapis (Role 14): only see patients registered / assigned to this therapist
            if ($user->role_id === 14) {
                $profileId = $user->user_profile_id;
                $userId = $user->id;
                return $query->whereHas('registrasis', function ($q) use ($profileId, $userId) {
                    $q->where('terapis_id', $profileId)->orWhere('terapis_id', $userId);
                })->get();
            }

            // Dokter (Role 17): only see patients registered / assigned to this doctor
            if ($user->role_id === 17) {
                $profileId = $user->user_profile_id;
                $userId = $user->id;
                return $query->whereHas('registrasis', function ($q) use ($profileId, $userId) {
                    $q->where('terapis_id', $profileId)->orWhere('terapis_id', $userId);
                })->get();
            }
        }

        return $query->get();
    }

    // POST /api/profile-anak
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_anak' => 'required|string|max:255',
            'id_orang_tua' => 'nullable|exists:user_profile,id',
            'agama_id' => 'required|exists:agama_m,id',
            'jenis_kelamin_id' => 'required|exists:jenis_kelamin,id',
            'NIK' => 'required|string|max:20|unique:profile_anak,NIK',
            'alamat' => 'nullable|string',
            'umur' => 'nullable|integer|min:0',
            'tanggal_lahir' => 'nullable|date',
            'tempat_lahir' => 'nullable|string|max:255',
            'no_hp_orang_tua' => 'nullable|string|max:20',
            'status_aktif' => 'boolean',
            'catatan_alergi' => 'nullable|string',
            'diagnosis_awal' => 'nullable|string',
            'rujukan_dari' => 'nullable|string',
            'sekolah' => 'nullable|string',
            // Data Orang Tua & Akun Portal (Step 2)
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'nama_orang_tua' => 'nullable|string|max:255',
            'no_wa_orang_tua' => 'nullable|string|max:20',
            'email_orang_tua' => 'nullable|email|max:255',
            'username_ortu' => 'nullable|string|max:50',
            'password_ortu' => 'nullable|string|min:4',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $validated) {
            // 1. Auto generate No RM jika belum ada
            $ym = date('Ym');
            $countRm = ProfileAnak::where('no_rm', 'like', "RM-{$ym}-%")->count() + 1;
            $noRm = 'RM-' . $ym . '-' . str_pad($countRm, 4, '0', STR_PAD_LEFT);
            $validated['no_rm'] = $noRm;

            // 2. Data Orang Tua & Akun Portal (Opsional - jika diinput)
            $parentAccount = null;
            $parentProfileId = $request->id_orang_tua;
            $parentUserId = null;

            $hasParentInput = $request->filled('nama_orang_tua') || $request->filled('nama_ibu') || $request->filled('nama_ayah') || $request->filled('no_wa_orang_tua');

            if ($parentProfileId) {
                $login = \App\Models\UserLogin::where('user_profile_id', $parentProfileId)->first();
                $parentUserId = $login?->id;
            } elseif ($hasParentInput) {
                // Auto create Akun Portal Orang Tua (role_id = 15) jika ada input orang tua
                $parentName = $request->nama_orang_tua 
                    ?? ($request->nama_ibu ?? ($request->nama_ayah ?? 'Orang Tua ' . $request->nama_anak));
                $phone = $request->no_wa_orang_tua ?? ($request->no_hp_orang_tua ?? '-');
                $email = $request->email_orang_tua ?? (strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $parentName)) . rand(100, 999) . '@klinikku.id');
                
                // Username & Password
                $cleanChild = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->nama_anak));
                $username = $request->username_ortu ?: ('ortu_' . substr($cleanChild, 0, 8) . rand(10, 99));
                $rawPassword = $request->password_ortu ?: '123456';

                // Ensure username unique
                while (\App\Models\UserLogin::where('username', $username)->exists()) {
                    $username = 'ortu_' . substr($cleanChild, 0, 8) . rand(100, 999);
                }

                $parentProfile = \App\Models\UserProfile::create([
                    'nama' => $parentName,
                    'no_telepon' => $phone,
                    'email' => $email,
                    'alamat' => $request->alamat,
                    'jenis_user_id' => 3,
                    'status_aktif' => true,
                ]);

                $parentLogin = \App\Models\UserLogin::create([
                    'username' => $username,
                    'password' => \Illuminate\Support\Facades\Hash::make($rawPassword),
                    'role_id' => 15, // Role Orang Tua
                    'user_profile_id' => $parentProfile->id,
                    'status_aktif' => true,
                ]);

                $parentProfileId = $parentProfile->id;
                $parentUserId = $parentLogin->id;

                $parentAccount = [
                    'username' => $username,
                    'password' => $rawPassword,
                    'nama_wali' => $parentName,
                    'no_wa' => $phone,
                ];
            }

            $validated['id_orang_tua'] = $parentProfileId;
            $validated['parent_user_id'] = $parentUserId;

            $anak = ProfileAnak::create($validated);

            // Create PasienWali jika ada data orang tua
            if ($parentProfileId || $hasParentInput) {
                \App\Models\PasienWali::create([
                    'profile_anak_id' => $anak->id,
                    'user_profile_id' => $parentProfileId,
                    'nama_wali' => $request->nama_orang_tua ?? ($request->nama_ibu ?? ($request->nama_ayah ?? 'Orang Tua')),
                    'hubungan' => $request->filled('nama_ibu') ? 'Ibu Kandung' : ($request->filled('nama_ayah') ? 'Ayah Kandung' : 'Wali'),
                    'no_hp' => $request->no_wa_orang_tua ?? $request->no_hp_orang_tua,
                    'email' => $request->email_orang_tua,
                    'alamat' => $request->alamat,
                    'is_wali_utama' => true,
                    'is_kontak_darurat' => true,
                ]);
            }

            return response()->json([
                'message' => 'Data master pasien anak berhasil disimpan dengan No. RM: ' . $noRm,
                'data' => $anak->load(['orangTua', 'agama', 'jenisKelamin', 'parentUser']),
                'parent_account' => $parentAccount,
            ], 201);
        });
    }

    // GET /api/profile-anak/{id}
    public function show($id)
    {
        $user = auth('api')->user();
        $anak = ProfileAnak::with(['orangTua', 'agama', 'jenisKelamin'])->findOrFail($id);

        if ($user && $user->role_id === 15 && strtolower($user->username) !== 'admin') {
            if ($anak->id_orang_tua !== $user->user_profile_id) {
                return response()->json([
                    'message' => 'Akses ditolak: Anda tidak memiliki wewenang untuk melihat data pasien anak ini.'
                ], 403);
            }
        }

        return $anak;
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
