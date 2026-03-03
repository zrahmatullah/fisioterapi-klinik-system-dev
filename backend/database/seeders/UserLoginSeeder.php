<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserLoginSeeder extends Seeder
{
    public function run()
    {
        // 1️⃣ Role
        $roles = ['Admin', 'Dokter', 'Pasien', 'Terapis'];
        foreach ($roles as $role) {
            DB::table('role')->insert([
                'role' => $role,
                'status_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $roleIds = DB::table('role')->pluck('id', 'role');

        // 2️⃣ Jenis Pegawai (hanya untuk Dokter & Terapis, Admin & Pasien bisa pakai default)
        $jenisPegawai = ['Dokter', 'Terapis', 'Admin', 'Pasien'];
        foreach ($jenisPegawai as $jp) {
            DB::table('jenis_pegawai')->insert([
                'jenis_pegawai' => $jp,
                'status_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $jenisPegawaiIds = DB::table('jenis_pegawai')->pluck('id', 'jenis_pegawai');

        // 3️⃣ Pegawai untuk semua user
        $pegawaiData = [
            ['nama_pegawai' => 'Admin Sistem', 'jenis' => 'Admin'],
            ['nama_pegawai' => 'Dr. Andi', 'jenis' => 'Dokter'],
            ['nama_pegawai' => 'Terapis Budi', 'jenis' => 'Terapis'],
            ['nama_pegawai' => 'Pasien Dummy', 'jenis' => 'Pasien'],
        ];

        foreach ($pegawaiData as $p) {
            DB::table('pegawai')->insert([
                'nama_pegawai' => $p['nama_pegawai'],
                'jenis_pegawai_id' => $jenisPegawaiIds[$p['jenis']],
                'status_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $pegawaiIds = DB::table('pegawai')->pluck('id', 'nama_pegawai');

        // 4️⃣ User Login
        $users = [
            ['username' => 'admin', 'password' => 'admin123', 'pegawai' => 'Admin Sistem', 'role' => 'Admin'],
            ['username' => 'dokter', 'password' => 'dokter123', 'pegawai' => 'Dr. Andi', 'role' => 'Dokter'],
            ['username' => 'terapis', 'password' => 'terapis123', 'pegawai' => 'Terapis Budi', 'role' => 'Terapis'],
            ['username' => 'pasien', 'password' => 'pasien123', 'pegawai' => 'Pasien Dummy', 'role' => 'Pasien'],
        ];

        foreach ($users as $u) {
            DB::table('user_login')->insert([
                'username' => $u['username'],
                'password' => Hash::make($u['password']),
                'pegawai_id' => $pegawaiIds[$u['pegawai']],
                'role_id' => $roleIds[$u['role']],
                'status_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
