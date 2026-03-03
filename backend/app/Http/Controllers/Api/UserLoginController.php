<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserLoginController extends Controller
{
    public function index()
    {
        $users = UserLogin::with('pegawai', 'role')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:user_login,username',
            'password' => 'required|string|min:6',
            'pegawai_id' => 'required|exists:pegawai,id',
            'role_id' => 'required|exists:role,id',
            'status_aktif' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = UserLogin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'pegawai_id' => $request->pegawai_id,
            'role_id' => $request->role_id,
            'status_aktif' => $request->status_aktif,
        ]);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = UserLogin::with('pegawai', 'role')->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = UserLogin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|unique:user_login,username,' . $id,
            'password' => 'nullable|string|min:6',
            'pegawai_id' => 'sometimes|exists:pegawai,id',
            'role_id' => 'sometimes|exists:role,id',
            'status_aktif' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->update($request->except('password'));

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = UserLogin::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
