<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserLogin;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\OtpEmail;
use App\Models\UserProfile;

class AuthController extends Controller
{
    // ================= REGISTER =================
    public function register(Request $request)
    {
        $request->validate([
            'username'         => 'required|unique:user_login,username',
            'password'         => 'required|min:6',
            'user_profile_id'  => 'required|exists:user_profile,id',
            'role_id'          => 'required|exists:role,id',
        ]);

        $user = UserLogin::create([
            'username'        => $request->username,
            'password'        => Hash::make($request->password),
            'user_profile_id' => $request->user_profile_id,
            'role_id'         => $request->role_id,
            'status_aktif'    => true,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Register berhasil',
            'user' => [
                'username' => $user->username,
                'role_id'  => $user->role_id,
            ],
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    // ================= LOGIN =================
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Username atau Password salah'
            ], 401);
        }

        /** @var \App\Models\UserLogin $user */
        $user = auth('api')->user();

        // Load relasi yang diperlukan
        $user->load([
            'userProfile.jenisUser',
            'role'
        ]);

        return response()->json([
            'message' => 'Login berhasil',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'nama' => $user->userProfile?->nama,
                'role' => $user->role->role,
                'role_id' => $user->role->id,
                'jenis_user' => $user->userProfile?->jenisUser?->jenis_user,
                // tambahkan userProfile id agar frontend bisa filter profile anak
                'user_profile_id' => $user->userProfile?->id,
            ],
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }


    // ================= ME =================
    public function me()
    {
        $user = auth('api')->user();
        $user->load('userProfile.jenisUser', 'role');

        return response()->json($user);
    }

    // ================= LOGOUT =================
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    // ================= REFRESH =================
    public function refresh()
    {
        return response()->json([
            'access_token' => auth('api')->refresh(),
            'token_type' => 'bearer',
        ]);
    }

    // ================= FORGOT PASSWORD - CHECK USERNAME =================
    public function checkUsername(Request $request)
    {
        $request->validate([
            'username' => 'required'
        ]);

        $user = UserLogin::with('userProfile')
            ->where('username', $request->username)
            ->first();

        if (!$user || !$user->userProfile || !$user->userProfile->email) {
            return response()->json([
                'error' => 'Username tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Username ditemukan',
            'email'   => $user->userProfile->email
        ]);
    }

    // ================= FORGOT PASSWORD - UPDATE =================
    public function updatePassword(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = UserLogin::where('username', $request->username)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Username tidak ditemukan'
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Password berhasil diperbarui'
        ]);
    }

    // ================= RESET PASSWORD VIA OTP =================
    public function resetPasswordByOtp(Request $request)
    {
        $request->validate([
            'email'        => 'required|email',
            'reset_token' => 'required',
            'password'    => 'required|min:6|confirmed'
        ]);

        $otp = OtpEmail::where('email', $request->email)
            ->whereNotNull('reset_token')
            ->latest()
            ->first();

        if (!$otp || !Hash::check($request->reset_token, $otp->reset_token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token reset tidak valid'
            ], 422);
        }

        $profile = UserProfile::where('email', $request->email)->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        UserLogin::where('user_profile_id', $profile->id)
            ->update([
                'password' => Hash::make($request->password)
            ]);

        // hapus token supaya tidak bisa dipakai ulang
        $otp->update(['reset_token' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah'
        ]);
    }

}
