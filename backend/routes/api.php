<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AgamaController;
use App\Http\Controllers\Api\LandingController;
use App\Http\Controllers\Api\LayananController;
// use App\Http\Controllers\Api\PegawaiController;
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
use App\Http\Controllers\Api\DashboardAdminController;
use App\Http\Controllers\Api\EvaluasiTerapiController;
use App\Http\Controllers\Api\LaporanKeluhanController;
use App\Http\Controllers\Api\RegistrasiAnakController;
use App\Http\Controllers\Api\KategoriLayananController;
use App\Http\Controllers\Api\RiwayatEvaluasiController;
use App\Http\Controllers\Api\DashboardTerapisController;
use App\Http\Controllers\Api\TerapisToRuanganController;
use App\Http\Controllers\Api\HasilEvaluasiAnakController;
use App\Http\Controllers\Api\LaporanPembayaranController;
use App\Http\Controllers\Api\MasterUserProfileController;
use App\Http\Controllers\Api\PelayananTerapiAnakController;
use App\Http\Controllers\Api\CatatanAktivitasAnakController;
use App\Http\Controllers\Api\PembayaranRegistrasiController;
use App\Http\Controllers\Api\LaporanHistoryPromosiController;



Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password/check-username', [AuthController::class, 'checkUsername']);
// Route::post('forgot-password/check-username', [AuthController::class, 'forgotPassword']);
Route::post('forgot-password/update', [AuthController::class, 'updatePassword']);
Route::post('/reset-password', [AuthController::class, 'resetPasswordByOtp']);

Route::prefix('otp-email')->group(function () {
    Route::post('send', [OtpEmailController::class, 'send']);
    Route::post('verify', [OtpEmailController::class, 'verify']);
    Route::post('reset/send', [OtpEmailController::class, 'sendReset']);
    Route::post('reset/verify', [OtpEmailController::class, 'verifyReset']); 
});


Route::middleware('auth:api')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('refresh', [AuthController::class, 'refresh']);


    Route::get('promosi/aktif', [PromosiController::class, 'aktif']);



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


    Route::get('profile-anak-saya', [ProfileAnakController::class, 'anakSaya']);
    Route::get('dashboard-terapis', [DashboardTerapisController::class, 'index']);
    Route::get('terapis', [UserProfileController::class, 'terapis']);
    Route::get('promosi/aktif', [PromosiController::class, 'aktif']);


    Route::get(
        'pelayanan-terapi-anak/{registrasiId}',
        [PelayananTerapiAnakController::class, 'index']
    );

    Route::post('/pelayanan-terapi-anak/{id}/reschedule', [PelayananTerapiAnakController::class, 'ajukanReschedule']);
    Route::post('/pelayanan-terapi-anak/{id}/approve-reschedule', [PelayananTerapiAnakController::class, 'approveReschedule']);
    Route::post('/pelayanan-terapi-anak/{id}/reject-reschedule', [PelayananTerapiAnakController::class, 'rejectReschedule']);
    Route::get('/admin/reschedule-request', [PelayananTerapiAnakController::class, 'listPendingReschedule']);

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

    Route::get(
        'terapis-by-tanggal',
        [RegistrasiAnakController::class, 'terapisByTanggal']
    );

    Route::put('/registrasi-anak/{id}/kedatangan', [RegistrasiAnakController::class, 'updateKedatangan']);

    Route::post(
        'jadwal-user/sync',
        [JadwalUserController::class, 'sync']
    );

    Route::post('/terapis-ruangan', [TerapisToRuanganController::class, 'store']);
    Route::get('/terapis-ruangan', [TerapisToRuanganController::class, 'index']);
    Route::get('/terapis/{id}/ruangan', [TerapisToRuanganController::class, 'byTerapis']);
    Route::delete('/terapis/{terapis}/ruangan/{ruangan}', [TerapisToRuanganController::class, 'destroy']);



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
        'pembayaran/upload-bukti',
        [PembayaranRegistrasiController::class, 'uploadBukti']
    );

    Route::post(
        'pembayaran-registrasi/{id}/verifikasi-admin',
        [PembayaranRegistrasiController::class, 'verifikasiAdmin']
    );


    Route::get(
        'keluhan-anak',
        [KeluhanAnakController::class, 'indexOrangTua']
    );

    Route::post(
        'keluhan-anak',
        [KeluhanAnakController::class, 'store']
    );

    Route::get(
        'terapis-by-tanggal',
        [RegistrasiAnakController::class, 'terapisByTanggal']
    );

    Route::put('/registrasi-anak/{id}/panggil', [RegistrasiAnakController::class, 'panggil']);

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

    Route::post(
        'pembayaran-registrasi/request-verifikasi',
        [PembayaranRegistrasiController::class, 'store']
    );


    Route::post(
        'pembayaran/upload-bukti',
        [PembayaranRegistrasiController::class, 'uploadBukti']
    );


    Route::post(
        'pembayaran-registrasi/{id}/verifikasi-admin',
        [PembayaranRegistrasiController::class, 'verifikasiAdmin']
    );

    Route::get(
        'keluhan-anak',
        [KeluhanAnakController::class, 'indexOrangTua']
    );


    Route::post(
        'keluhan-anak',
        [KeluhanAnakController::class, 'store']
    );


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

        Route::get(
            'laporan/keluhan',
            [LaporanKeluhanController::class, 'index']
        );
    });
});

Route::get(
    'cetak/keluhan-anak/{id}',
    [KeluhanAnakController::class, 'cetak']
);

Route::get(
    'catatan-aktivitas-anak/cetak/{id}',
    [CatatanAktivitasAnakController::class, 'cetak']
);


Route::get(
    'cetak/evaluasi-anak',
    [HasilEvaluasiAnakController::class, 'cetakEvaluasiAnakSaya']
);

Route::get(
    'cetak/evaluasi-anak/{evaluasiId}',
    [HasilEvaluasiAnakController::class, 'cetakEvaluasiAnakPerItem']
);

Route::get(
    'cetak/evaluasi-anak/pdf',
    [HasilEvaluasiAnakController::class, 'cetakEvaluasiAnakSaya']
);

Route::get(
    'cetak/evaluasi-anak/{id}/pdf',
    [HasilEvaluasiAnakController::class, 'cetakEvaluasiAnakPerItem']
);


Route::post(
    'registrasi-anak/{id}/kirim-email-jadwal',
    [RegistrasiAnakController::class, 'kirimEmailJadwal']
);


Route::get(
    'laporan/pembayaran',
    [LaporanPembayaranController::class, 'index']
);

Route::get(
    'laporan/pembayaran/cetak-pdf',
    [LaporanPembayaranController::class, 'cetakPdf']
);
Route::get(
    'laporan/keluhan',
    [LaporanKeluhanController::class, 'index']
);
Route::get(
    'laporan/promosi',
    [LaporanHistoryPromosiController::class, 'index']
);

Route::get(
    'laporan/promosi/cetak-pdf',
    [LaporanHistoryPromosiController::class, 'cetakPdf']
);

Route::get(
    'laporan/keluhan/cetak-pdf',
    [LaporanKeluhanController::class, 'cetakPdf']
);

Route::get(
    'laporan/pembayaran/export-excel',
    [LaporanPembayaranController::class, 'exportExcel']
);

Route::get('dashboard/admin', [DashboardAdminController::class, 'index']);
Route::get('public/landing', [LandingController::class, 'index']);

Route::post(
    'master-user/public',
    [MasterUserProfileController::class, 'storePublic']
);

Route::get(
    'riwayat-evaluasi',
    [RiwayatEvaluasiController::class, 'index']
);

Route::get('/cetak/evaluasi-anak/{id}/pdf', [EvaluasiTerapiController::class, 'cetakPdf']);
