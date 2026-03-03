<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // 1️⃣ Tambah jenis_user admin
        DB::table('jenis_user')->updateOrInsert(
            ['jenis_user' => 'admin'],
            [
                'status_aktif' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $jenisId = DB::table('jenis_user')->where('jenis_user', 'admin')->value('id');

        // 2️⃣ Tambah role admin
        DB::table('role')->updateOrInsert(
            ['role' => 'admin'],
            [
                'status_aktif' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $roleId = DB::table('role')->where('role', 'admin')->value('id');

        // 3️⃣ Tambah user_profile admin
        DB::table('user_profile')->updateOrInsert(
            ['nama' => 'admin'],
            [
                'jenis_user_id' => $jenisId,
                'status_aktif' => 1,
                'alamat' => 'Admin Address',
                'nip' => '0001',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $userProfileId = DB::table('user_profile')->where('nama', 'admin')->value('id');

        // 4️⃣ Tambah user_login admin
        DB::table('user_login')->updateOrInsert(
            ['username' => 'admin'],
            [
                'password' => Hash::make('admin123'),
                'status_aktif' => 1,
                'user_profile_id' => $userProfileId,
                'role_id' => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
