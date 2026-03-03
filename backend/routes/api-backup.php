<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AgamaController;
use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\PromosiController;
use App\Http\Controllers\Api\RuanganController;
use App\Http\Controllers\Api\OtpEmailController;
use App\Http\Controllers\Api\JenisUserController;
use App\Http\Controllers\Api\UserLoginController;
use App\Http\Controllers\Api\Assesment1Controller;
use App\Http\Controllers\Api\JadwalUserController;
use App\Http\Controllers\Api\KeluhanAnakController;
use App\Http\Controllers\Api\ProfileAnakController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\JadwalMasterController;
use App\Http\Controllers\Api\JenisKelaminController;
use App\Http\Controllers\Api\EvaluasiTerapiController;
use App\Http\Controllers\Api\RegistrasiAnakController;
use App\Http\Controllers\Api\KategoriLayananController;
use App\Http\Controllers\Api\DashboardTerapisController;
use App\Http\Controllers\Api\HasilEvaluasiAnakController;
use App\Http\Controllers\Api\MasterUserProfileController;
use App\Http\Controllers\Api\CetakRegistrasiAnakController;
use App\Http\Controllers\Api\PelayananTerapiAnakController;
use App\Http\Controllers\Api\CatatanAktivitasAnakController;
use App\Http\Controllers\Api\PembayaranRegistrasiController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('forgot-password/check-username', [AuthController::class, 'checkUsername']);
Route::post('forgot-password/update', [AuthController::class, 'updatePassword']);

Route::prefix('otp-email')->group(function () {
    Route::post('/send', [OtpEmailController::class, 'send']);
    Route::post('/verify', [OtpEmailController::class, 'verify']);
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    /*
    |--------------------------------------------------------------------------
    | 🔥 PROMOSI
    |--------------------------------------------------------------------------
    */
    Route::get('promosi/aktif', [PromosiController::class, 'aktif']);

    /*
    |--------------------------------------------------------------------------
    | RESOURCE
    |--------------------------------------------------------------------------
    */
    Route::apiResources([
        'jenis-user'        => JenisUserController::class,
        'user-profile'      => UserProfileController::class,
        'role'              => RoleController::class,
        'user-login'        => UserLoginController::class,
        'master-user'       => MasterUserProfileController::class,
        'profile-anak'      => ProfileAnakController::class,
        'ruangan'           => RuanganController::class,
        'kategori-layanan'  => KategoriLayananController::class,
        'layanan'           => LayananController::class,
        'agama'             => AgamaController::class,
        'jenis-kelamin'     => JenisKelaminController::class,
        'registrasi-anak'   => RegistrasiAnakController::class,
        'assesment-1'       => Assesment1Controller::class,
        'promosi'           => PromosiController::class,
        'jadwal-master'     => JadwalMasterController::class,
        'jadwal-user'       => JadwalUserController::class,
    ]);

    /*
    |--------------------------------------------------------------------------
    | CUSTOM ROUTE
    |--------------------------------------------------------------------------
    */
    Route::get('profile-anak-saya', [ProfileAnakController::class, 'anakSaya']);
    Route::get('dashboard-terapis', [DashboardTerapisController::class, 'index']);
    Route::get('terapis', [UserProfileController::class, 'terapis']);
    Route::get('promosi/aktif', [PromosiController::class, 'aktif']);


    Route::get(
        'pelayanan-terapi-anak/{registrasiId}',
        [PelayananTerapiAnakController::class, 'index']
    );

    Route::post(
        'pelayanan-terapi-anak',
        [PelayananTerapiAnakController::class, 'store']
    );

    Route::put(
        'pelayanan-terapi-anak/{id}/status',
        [PelayananTerapiAnakController::class, 'updateStatus']
    );

    Route::put(
        'pelayanan-terapi-anak/{id}/terapis',
        [PelayananTerapiAnakController::class, 'updateTerapis']
    );

    Route::delete(
        'pelayanan-terapi-anak/{id}',
        [PelayananTerapiAnakController::class, 'destroy']
    );

    Route::apiResource(
        'pembayaran-registrasi',
        PembayaranRegistrasiController::class
    );

    Route::get(
        'pembayaran-registrasi/by-registrasi/{registrasiId}',
        [PembayaranRegistrasiController::class, 'byRegistrasi']
    );

    Route::get(
        'riwayat-pembayaran-anak',
        [PembayaranRegistrasiController::class, 'riwayatPembayaranAnak']
    );

    Route::post(
        'jadwal-user/sync',
        [JadwalUserController::class, 'sync']
    );

    Route::get(
        'terapis-by-tanggal',
        [RegistrasiAnakController::class, 'terapisByTanggal']
    );

    Route::get(
        'catatan-aktivitas-anak/by-registrasi/{registrasiId}',
        [CatatanAktivitasAnakController::class, 'byRegistrasi']
    );

    Route::post(
        'catatan-aktivitas-anak',
        [CatatanAktivitasAnakController::class, 'store']
    );

    Route::post(
        'evaluasi-terapi',
        [EvaluasiTerapiController::class, 'store']
    );

    Route::get(
        'evaluasi-terapi/by-registrasi/{registrasiId}',
        [EvaluasiTerapiController::class, 'showByRegistrasi']
    );

    Route::get(
        'hasil-evaluasi-anak',
        [HasilEvaluasiAnakController::class, 'evaluasiAnakSaya']
    );

    /*
    |--------------------------------------------------------------------------
    | ================= TAMBAHAN PEMBAYARAN =================
    |--------------------------------------------------------------------------
    */

    // 1️⃣ ORANG TUA: klik "Verifikasi Pembayaran" → CREATE tagihan (pending)
    Route::post(
        'pembayaran-registrasi/request-verifikasi',
        [PembayaranRegistrasiController::class, 'store']
    );

    // 2️⃣ ORANG TUA: upload bukti pembayaran
    Route::post(
        'pembayaran/upload-bukti',
        [PembayaranRegistrasiController::class, 'uploadBukti']
    );

    // 3️⃣ ADMIN: verifikasi pembayaran (set lunas / ditolak)
    Route::post(
        'pembayaran-registrasi/{id}/verifikasi-admin',
        [PembayaranRegistrasiController::class, 'verifikasiAdmin']
    );

    /*
|--------------------------------------------------------------------------
| ================= MODUL KELUHAN ANAK =================
|--------------------------------------------------------------------------
*/

    // ================= ORANG TUA =================

    // list keluhan milik orang tua
    Route::get(
        'keluhan-anak',
        [KeluhanAnakController::class, 'indexOrangTua']
    );

    // kirim keluhan baru
    Route::post(
        'keluhan-anak',
        [KeluhanAnakController::class, 'store']
    );


    // ================= ADMIN =================

    //     // list semua keluhan
    //     Route::get(
    //         'admin/keluhan-anak',
    //         [KeluhanAnakController::class, 'indexAdmin']
    //     );

    //     // detail keluhan
    //     Route::get(
    //         'admin/keluhan-anak/{id}',
    //         [KeluhanAnakController::class, 'show']
    //     );

    //     // admin memberi tanggapan
    //     Route::post(
    //         'admin/keluhan-anak/{id}/tanggapi',
    //         [KeluhanAnakController::class, 'tanggapi']
    //     );

    //     // nonaktifkan keluhan (opsional)
    //     Route::put(
    //         'admin/keluhan-anak/{id}/nonaktifkan',
    //         [KeluhanAnakController::class, 'nonaktifkan']
    //     );

    //     Route::get(
    //         'cetak/keluhan-anak/{id}',
    //         [KeluhanAnakController::class, 'cetak']
    //     );


    //     /*
    // |--------------------------------------------------------------------------
    // | ================= MODUL KELUHAN ANAK (ADMIN) =================
    // |--------------------------------------------------------------------------
    // */
    //     Route::prefix('admin')->group(function () {

    //         // list semua keluhan
    //         Route::get(
    //             'keluhan-anak',
    //             [KeluhanAnakController::class, 'indexAdmin']
    //         );

    //         // detail keluhan
    //         Route::get(
    //             'keluhan-anak/{id}',
    //             [KeluhanAnakController::class, 'show']
    //         );

    //         // admin memberi tanggapan
    //         Route::post(
    //             'keluhan-anak/{id}/tanggapi',
    //             [KeluhanAnakController::class, 'tanggapi']
    //         );

    //         // nonaktifkan keluhan (opsional)
    //         Route::put(
    //             'keluhan-anak/{id}/nonaktifkan',
    //             [KeluhanAnakController::class, 'nonaktifkan']
    //         );
    //     });

    /*
|--------------------------------------------------------------------------
| ================= MODUL KELUHAN ANAK =================
|--------------------------------------------------------------------------
*/

    // ================= ORANG TUA =================

    Route::get(
        'keluhan-anak',
        [KeluhanAnakController::class, 'indexOrangTua']
    );

    Route::post(
        'keluhan-anak',
        [KeluhanAnakController::class, 'store']
    );

    // ================= CETAK (GLOBAL) =================
    Route::get(
        'cetak/keluhan-anak/{id}',
        [KeluhanAnakController::class, 'cetak']
    );

    // ================= ADMIN =================
    Route::prefix('admin')->group(function () {

        Route::get(
            'keluhan-anak',
            [KeluhanAnakController::class, 'indexAdmin']
        );

        Route::get(
            'keluhan-anak/{id}',
            [KeluhanAnakController::class, 'show']
        );

        Route::post(
            'keluhan-anak/{id}/tanggapi',
            [KeluhanAnakController::class, 'tanggapi']
        );

        Route::put(
            'keluhan-anak/{id}/nonaktifkan',
            [KeluhanAnakController::class, 'nonaktifkan']
        );
    });
});