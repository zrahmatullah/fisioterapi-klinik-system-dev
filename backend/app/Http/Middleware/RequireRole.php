<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth('api')->check()) {
            return response()->json([
                'message' => 'Sesi tidak valid atau telah berakhir. Silakan login kembali.'
            ], 401);
        }

        $user = auth('api')->user();

        // Super Admin (Role 13 / username 'admin') has 100% unrestricted access
        if ($user->role_id === 13 || strtolower($user->username) === 'admin') {
            return $next($request);
        }

        // Role mapping lookup
        $roleMap = [
            'admin' => [13],
            'super_admin' => [13],
            'terapis' => [14],
            'orang_tua' => [15],
            'pendaftaran' => [16],
            'dokter' => [17],
            'farmasi' => [18],
            'apoteker' => [18],
            'kasir' => [19],
            'gudang' => [20],
            'logistik' => [20],
            'manajemen' => [21],
        ];

        $allowedRoleIds = [];
        foreach ($roles as $r) {
            $rLower = strtolower(trim($r));
            if (is_numeric($r)) {
                $allowedRoleIds[] = (int)$r;
            } elseif (isset($roleMap[$rLower])) {
                $allowedRoleIds = array_merge($allowedRoleIds, $roleMap[$rLower]);
            }
        }

        if (!in_array($user->role_id, $allowedRoleIds)) {
            return response()->json([
                'message' => 'Akses ditolak: Peran akun Anda tidak memiliki otorisasi untuk resource ini.',
                'user_role' => $user->role_id,
                'required_roles' => $roles
            ], 403);
        }

        return $next($request);
    }
}
