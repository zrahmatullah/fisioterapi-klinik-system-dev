<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClinicSetting;
use App\Models\EmrAuditLog;
use App\Models\Role;
use App\Models\UserLogin;
use App\Models\RoleModulePermission;
use Illuminate\Support\Facades\DB;

class ClinicSettingsController extends Controller
{
    // ================= 1. GET ALL SETTINGS =================
    public function getSettings()
    {
        $settings = ClinicSetting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    // ================= 2. UPDATE SETTINGS =================
    public function updateSettings(Request $request)
    {
        $data = $request->except(['_token']);
        foreach ($data as $key => $val) {
            ClinicSetting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($val) ? json_encode($val) : (string)$val]
            );
        }
        return response()->json(['message' => 'Pengaturan klinik berhasil diperbarui']);
    }

    // ================= 3. AUDIT LOGS (UU PDP & PERMENKES 24/2022) =================
    public function auditLogs(Request $request)
    {
        $logs = EmrAuditLog::with(['user.userProfile', 'profileAnak'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->query('per_page', 25));

        return response()->json($logs);
    }

    // ================= 4. RBAC MATRIX & ROLES =================
    public function rbacMatrix()
    {
        $roles = Role::where('status_aktif', true)->get();
        $permissions = DB::table('permissions')->get()->groupBy('group');
        $rolePermissions = DB::table('role_permissions')->get();

        return response()->json([
            'roles' => $roles,
            'permissions_by_group' => $permissions,
            'role_permissions' => $rolePermissions,
        ]);
    }

    // ================= 5. FULL V-C-U-D ROLE MODULE MATRIX =================
    public function getFullRbacMatrix()
    {
        $roles = Role::where('status_aktif', true)->orderBy('id', 'asc')->get();

        $standardModules = [
            ['code' => 'pendaftaran', 'name' => 'Pendaftaran & Antrian Pasien'],
            ['code' => 'emr', 'name' => 'Rekam Medis Elektronik (RME / SOAP)'],
            ['code' => 'asesmen', 'name' => 'Asesmen & Evaluasi Terapi Anak'],
            ['code' => 'jadwal', 'name' => 'Penjadwalan & Mapping Praktik'],
            ['code' => 'kasir', 'name' => 'Kasir POS & Billing Penjamin'],
            ['code' => 'logistik', 'name' => 'Farmasi, Gudang & Logistik (PR/PO/GRN)'],
            ['code' => 'dokumen', 'name' => 'Dokumen Medis & Berkas Pasien'],
            ['code' => 'laporan', 'name' => 'Laporan Klinik & Keuangan'],
            ['code' => 'settings', 'name' => 'Pengaturan Sistem & Master Data'],
        ];

        // Ensure every role has an entry for each module
        foreach ($roles as $r) {
            $roleSlug = strtolower(str_replace([' ', '-'], '_', $r->role));

            foreach ($standardModules as $mod) {
                $exists = RoleModulePermission::where('role_id', $r->id)
                    ->where('module_code', $mod['code'])
                    ->first();

                if (!$exists) {
                    $isSuper = in_array($roleSlug, ['super_admin', 'superadmin', 'admin', 'administrator']);
                    $isDokter = str_contains($roleSlug, 'dokter');
                    $isTerapis = str_contains($roleSlug, 'terapis');
                    $isKasir = str_contains($roleSlug, 'kasir');
                    $isLogistik = str_contains($roleSlug, 'logistik') || str_contains($roleSlug, 'farmasi') || str_contains($roleSlug, 'gudang') || str_contains($roleSlug, 'apoteker');
                    $isPendaftaran = str_contains($roleSlug, 'pendaftaran') || str_contains($roleSlug, 'resepsionis');
                    $isOrangTua = str_contains($roleSlug, 'orang_tua') || str_contains($roleSlug, 'orangtua') || str_contains($roleSlug, 'wali');

                    $v = false; $c = false; $u = false; $d = false;

                    if ($isSuper) {
                        $v = true; $c = true; $u = true; $d = true;
                    } elseif ($isDokter) {
                        if (in_array($mod['code'], ['emr', 'asesmen', 'jadwal', 'dokumen'])) {
                            $v = true; $c = true; $u = true;
                        } elseif ($mod['code'] === 'pendaftaran') {
                            $v = true;
                        }
                    } elseif ($isTerapis) {
                        if (in_array($mod['code'], ['emr', 'asesmen', 'jadwal'])) {
                            $v = true; $c = true; $u = true;
                        } elseif ($mod['code'] === 'pendaftaran') {
                            $v = true;
                        }
                    } elseif ($isKasir) {
                        if ($mod['code'] === 'kasir') {
                            $v = true; $c = true; $u = true;
                        } elseif (in_array($mod['code'], ['pendaftaran', 'laporan'])) {
                            $v = true;
                        }
                    } elseif ($isLogistik) {
                        if ($mod['code'] === 'logistik') {
                            $v = true; $c = true; $u = true;
                        }
                    } elseif ($isPendaftaran) {
                        if (in_array($mod['code'], ['pendaftaran', 'jadwal', 'dokumen'])) {
                            $v = true; $c = true; $u = true;
                        }
                    } elseif ($isOrangTua) {
                        if (in_array($mod['code'], ['pendaftaran', 'emr', 'kasir'])) {
                            $v = true;
                        }
                    }

                    RoleModulePermission::create([
                        'role_id' => $r->id,
                        'module_code' => $mod['code'],
                        'module_name' => $mod['name'],
                        'can_view' => $v,
                        'can_create' => $c,
                        'can_update' => $u,
                        'can_delete' => $d,
                    ]);
                }
            }
        }

        $allPermissions = RoleModulePermission::orderBy('role_id')->orderBy('id')->get();

        return response()->json([
            'roles' => $roles,
            'modules' => $standardModules,
            'matrix' => $allPermissions,
        ]);
    }

    public function updateRoleModuleMatrix(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:role,id',
            'permissions' => 'required|array',
            'permissions.*.module_code' => 'required|string',
            'permissions.*.can_view' => 'required|boolean',
            'permissions.*.can_create' => 'required|boolean',
            'permissions.*.can_update' => 'required|boolean',
            'permissions.*.can_delete' => 'required|boolean',
        ]);

        $roleId = $request->role_id;

        foreach ($request->permissions as $p) {
            RoleModulePermission::updateOrCreate(
                [
                    'role_id' => $roleId,
                    'module_code' => $p['module_code'],
                ],
                [
                    'module_name' => $p['module_name'] ?? ucfirst($p['module_code']),
                    'can_view' => $p['can_view'],
                    'can_create' => $p['can_create'],
                    'can_update' => $p['can_update'],
                    'can_delete' => $p['can_delete'],
                ]
            );
        }

        return response()->json([
            'message' => 'Matriks hak akses peran (V, C, U, D) berhasil disimpan.',
        ]);
    }
}
