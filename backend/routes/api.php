<?php

use App\Http\Controllers\Api\AgamaController;
use App\Http\Controllers\Api\AIController;
use App\Http\Controllers\Api\Assesment1Controller;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatatanAktivitasAnakController;
use App\Http\Controllers\Api\DashboardAdminController;
use App\Http\Controllers\Api\DashboardTerapisController;
use App\Http\Controllers\Api\EvaluasiTerapiController;
use App\Http\Controllers\Api\HasilEvaluasiAnakController;
use App\Http\Controllers\Api\JadwalMasterController;
use App\Http\Controllers\Api\JadwalUserController;
use App\Http\Controllers\Api\JenisKelaminController;
use App\Http\Controllers\Api\JenisUserController;
use App\Http\Controllers\Api\KategoriLayananController;
use App\Http\Controllers\Api\KeluhanAnakController;
use App\Http\Controllers\Api\LandingController;
use App\Http\Controllers\Api\LaporanHistoryPromosiController;
use App\Http\Controllers\Api\LaporanKeluhanController;
use App\Http\Controllers\Api\LaporanPembayaranController;
use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\MasterUserProfileController;
use App\Http\Controllers\Api\OtpEmailController;
use App\Http\Controllers\Api\PelayananTerapiAnakController;
use App\Http\Controllers\Api\PembayaranRegistrasiController;
use App\Http\Controllers\Api\ProfileAnakController;
use App\Http\Controllers\Api\PromosiController;
use App\Http\Controllers\Api\RegistrasiAnakController;
use App\Http\Controllers\Api\RiwayatEvaluasiController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RuanganController;
use App\Http\Controllers\Api\TerapisToRuanganController;
use App\Http\Controllers\Api\UserLoginController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\EmrController;
use App\Http\Controllers\Api\KasirBillingController;
use App\Http\Controllers\Api\SuratDokumenController;
use App\Http\Controllers\Api\FarmasiLogistikController;
use App\Http\Controllers\Api\ClinicSettingsController;
use App\Http\Controllers\Api\ClinicalMasterController;
use App\Http\Controllers\Api\MappingJadwalController;
use App\Http\Controllers\Api\PediatricWorkflowController;
use Illuminate\Support\Facades\Route;



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

Route::post('/ai-command', [AIController::class, 'command']);

// ================= PUBLIC VERIFIKASI QR DOKUMEN =================
Route::get('public/surat/verify/{code}', [SuratDokumenController::class, 'verifyQr']);

// ================= E-CLINIC / SIMRS CORE ROUTES =================
Route::middleware('auth:api')->group(function () {
    // =========================================================================
    // DYNAMIC CLINICAL MASTER & VITAL PARAMETERS API (V1 & ROOT ALIASES)
    // =========================================================================
    Route::prefix('v1')->group(function () {
        // Master parameters
        Route::get('master/vital-parameters', [ClinicalMasterController::class, 'getVitalParameters']);
        Route::get('master/allergies', [ClinicalMasterController::class, 'getAllergies']);
        Route::get('master/soap-templates', [ClinicalMasterController::class, 'getSoapTemplates']);
        Route::get('master/icd10', [ClinicalMasterController::class, 'getIcd10']);
        Route::get('master/procedures', [ClinicalMasterController::class, 'getProcedures']);
        Route::get('master/medications', [ClinicalMasterController::class, 'getMedications']);
        Route::get('master/units', [ClinicalMasterController::class, 'getUnits']);
        Route::get('master/signa', [ClinicalMasterController::class, 'getSigna']);
        Route::get('master/intake-times', [ClinicalMasterController::class, 'getIntakeTimes']);
        Route::get('master/ancillary-tests', [ClinicalMasterController::class, 'getAncillaryTests']);

        // Patient vitals & allergies
        Route::get('visits/{visitId}/vitals', [ClinicalMasterController::class, 'getPatientVitals']);
        Route::get('patients/{patientId}/vitals', [ClinicalMasterController::class, 'getPatientVitals']);
        Route::post('patients/{patientId}/vitals', [ClinicalMasterController::class, 'storePatientVitals']);
        Route::get('patients/{patientId}/vitals/history', [ClinicalMasterController::class, 'getPatientVitalsHistory']);
        Route::get('patients/{patientId}/allergies', [ClinicalMasterController::class, 'getPatientAllergies']);
        Route::post('patients/{patientId}/allergies', [ClinicalMasterController::class, 'savePatientAllergies']);

        // Atomic EMR Save
        Route::post('emr/save', [ClinicalMasterController::class, 'atomicEmrSave']);
    });

    // Root-level aliases
    Route::get('master/vital-parameters', [ClinicalMasterController::class, 'getVitalParameters']);
    Route::get('master/allergies', [ClinicalMasterController::class, 'getAllergies']);
    Route::get('master/soap-templates', [ClinicalMasterController::class, 'getSoapTemplates']);
    Route::get('master/icd10', [ClinicalMasterController::class, 'getIcd10']);
    Route::get('master/procedures', [ClinicalMasterController::class, 'getProcedures']);
    Route::get('master/medications', [ClinicalMasterController::class, 'getMedications']);
    Route::get('master/units', [ClinicalMasterController::class, 'getUnits']);
    Route::get('master/signa', [ClinicalMasterController::class, 'getSigna']);
    Route::get('master/intake-times', [ClinicalMasterController::class, 'getIntakeTimes']);
    Route::get('master/ancillary-tests', [ClinicalMasterController::class, 'getAncillaryTests']);
    Route::get('patients/{patientId}/vitals', [ClinicalMasterController::class, 'getPatientVitals']);
    Route::post('patients/{patientId}/vitals', [ClinicalMasterController::class, 'storePatientVitals']);
    Route::get('patients/{patientId}/vitals/history', [ClinicalMasterController::class, 'getPatientVitalsHistory']);
    Route::get('patients/{patientId}/allergies', [ClinicalMasterController::class, 'getPatientAllergies']);
    Route::post('patients/{patientId}/allergies', [ClinicalMasterController::class, 'savePatientAllergies']);
    Route::post('emr/save', [ClinicalMasterController::class, 'atomicEmrSave']);

    // EMR & Timeline
    Route::prefix('emr')->group(function () {
        Route::get('timeline/{profileAnakId}', [EmrController::class, 'timeline']);
        Route::post('soap', [EmrController::class, 'storeSoap']);
        Route::post('{id}/lock-sign', [EmrController::class, 'lockAndSign']);
        Route::post('{id}/addendum', [EmrController::class, 'addAddendum']);
        Route::get('icd10', [EmrController::class, 'searchIcd10']);
        Route::get('icd9', [EmrController::class, 'searchIcd9']);
        Route::post('goal', [EmrController::class, 'storeGoal']);
        Route::put('goal/{id}', [EmrController::class, 'updateGoalProgress']);
    });

    // Kasir & Billing
    Route::prefix('billing')->middleware('role:super_admin,kasir')->group(function () {
        Route::get('invoices', [KasirBillingController::class, 'indexInvoices']);
        Route::post('invoices', [KasirBillingController::class, 'storeInvoice']);
        Route::post('invoices/{id}/bayar', [KasirBillingController::class, 'bayar']);
        Route::get('shift/status', [KasirBillingController::class, 'shiftStatus']);
        Route::post('shift/buka', [KasirBillingController::class, 'bukaShift']);
        Route::post('shift/tutup', [KasirBillingController::class, 'tutupShift']);
        Route::get('paket/{profileAnakId}', [KasirBillingController::class, 'paketPasien']);
        Route::get('thermal/{invoiceId}', [KasirBillingController::class, 'thermalReceipt']);
    });

    // Surat & Dokumen
    Route::prefix('surat')->middleware('role:super_admin,dokter,terapis,pendaftaran,manajemen,admin')->group(function () {
        Route::get('templates', [SuratDokumenController::class, 'indexTemplates']);
        Route::post('templates', [SuratDokumenController::class, 'storeTemplate']);
        Route::post('generate', [SuratDokumenController::class, 'generateSurat']);
        Route::get('arsip/{profileAnakId}', [SuratDokumenController::class, 'arsipSuratPasien']);
        Route::get('berkas-pasien/{profileAnakId}', [SuratDokumenController::class, 'getBerkasPasien']);
        Route::post('berkas-pasien/upload', [SuratDokumenController::class, 'uploadBerkasPasien']);
        Route::post('berkas-pasien/consent', [SuratDokumenController::class, 'storeInformedConsent']);
        Route::delete('berkas-pasien/{id}', [SuratDokumenController::class, 'deleteBerkasPasien']);
    });

    // Farmasi & Logistik (PR -> PO -> GRN -> Stock Mutasi & Kartu Stok FEFO)
    Route::prefix('farmasi')->middleware('role:super_admin,gudang,farmasi,apoteker,logistik,admin')->group(function () {
        Route::get('produk', [FarmasiLogistikController::class, 'indexProduk']);
        Route::post('produk', [FarmasiLogistikController::class, 'storeProduk']);
        Route::post('batch', [FarmasiLogistikController::class, 'tambahBatch']);
        Route::get('kartu-stok/{produkId}', [FarmasiLogistikController::class, 'kartuStok']);
        Route::get('suppliers', [FarmasiLogistikController::class, 'indexSuppliers']);
        Route::post('suppliers', [FarmasiLogistikController::class, 'storeSupplier']);
        Route::put('suppliers/{id}', [FarmasiLogistikController::class, 'updateSupplier']);
        Route::delete('suppliers/{id}', [FarmasiLogistikController::class, 'deleteSupplier']);
        
        // Alur PR (Purchase Requisition)
        Route::get('pr', [FarmasiLogistikController::class, 'indexPr']);
        Route::post('pr', [FarmasiLogistikController::class, 'storePr']);
        Route::post('pr/{id}/approve', [FarmasiLogistikController::class, 'approvePr']);

        // Alur PO (Purchase Order)
        Route::get('po', [FarmasiLogistikController::class, 'indexPurchaseOrders']);
        Route::post('po', [FarmasiLogistikController::class, 'storePurchaseOrder']);
        Route::post('po/{id}/receive', [FarmasiLogistikController::class, 'receiveGoods']);

        // Alur GRN (Goods Receipt Note)
        Route::get('grn', [FarmasiLogistikController::class, 'indexGrn']);
        Route::post('grn', [FarmasiLogistikController::class, 'storeGrn']);
    });

    // Mapping Jadwal Praktik
    Route::get('mapping-jadwal', [MappingJadwalController::class, 'index']);
    Route::post('mapping-jadwal', [MappingJadwalController::class, 'store'])->middleware('role:super_admin,pendaftaran,admin');

    // Pengaturan & Audit Log & Matriks Hak Akses (V, C, U, D)
    Route::prefix('settings')->middleware('role:super_admin,admin')->group(function () {
        Route::get('/', [ClinicSettingsController::class, 'getSettings']);
        Route::post('/', [ClinicSettingsController::class, 'updateSettings']);
        Route::get('audit-logs', [ClinicSettingsController::class, 'auditLogs']);
        Route::get('rbac-matrix', [ClinicSettingsController::class, 'rbacMatrix']);
        Route::get('rbac-matrix-full', [ClinicSettingsController::class, 'getFullRbacMatrix']);
        Route::post('rbac-matrix-full', [ClinicSettingsController::class, 'updateRoleModuleMatrix']);
    });

    // ================= PEDIATRIC THERAPY WORKFLOW (6 STAGES) =================
    Route::prefix('pediatric-workflow')->group(function () {
        // Master Data & Child Balances & Master Suite
        Route::get('guarantors', [PediatricWorkflowController::class, 'getGuarantors']);
        Route::post('guarantors', [PediatricWorkflowController::class, 'storeGuarantor'])->middleware('role:super_admin,admin');
        Route::delete('guarantors/{id}', [PediatricWorkflowController::class, 'deleteGuarantor'])->middleware('role:super_admin,admin');

        Route::get('packages', [PediatricWorkflowController::class, 'getTherapyPackages']);
        Route::post('packages', [PediatricWorkflowController::class, 'storeTherapyPackage'])->middleware('role:super_admin,admin');
        Route::delete('packages/{id}', [PediatricWorkflowController::class, 'deleteTherapyPackage'])->middleware('role:super_admin,admin');

        Route::get('icd10', [PediatricWorkflowController::class, 'getIcd10All']);
        Route::post('icd10', [PediatricWorkflowController::class, 'storeIcd10'])->middleware('role:super_admin,admin,dokter');
        Route::delete('icd10/{id}', [PediatricWorkflowController::class, 'deleteIcd10'])->middleware('role:super_admin,admin');

        Route::get('icd9', [PediatricWorkflowController::class, 'getIcd9All']);
        Route::post('icd9', [PediatricWorkflowController::class, 'storeIcd9'])->middleware('role:super_admin,admin,dokter');
        Route::delete('icd9/{id}', [PediatricWorkflowController::class, 'deleteIcd9'])->middleware('role:super_admin,admin');

        Route::get('master-stats', [PediatricWorkflowController::class, 'getMasterOverviewStats']);

        Route::get('child/{childId}/packages', [PediatricWorkflowController::class, 'getChildPackages']);
        Route::get('child/{childId}/insurances', [PediatricWorkflowController::class, 'getChildInsurances']);

        // Stage 1: Pendaftaran & Registrasi Dual-Track
        Route::post('register-session', [PediatricWorkflowController::class, 'registerChildSession'])
            ->middleware('role:super_admin,pendaftaran,admin');

        // Stage 2: Check-In & Antrian
        Route::post('checkin/{registrasiId}', [PediatricWorkflowController::class, 'checkInSession'])
            ->middleware('role:super_admin,pendaftaran,kasir,admin');

        // Stage 3: Rekomendasi Terapi Dokter (Therapy Order)
        Route::post('therapy-order', [PediatricWorkflowController::class, 'storeTherapyOrder'])
            ->middleware('role:super_admin,dokter,admin');
        Route::get('therapy-orders/{profileAnakId}', [PediatricWorkflowController::class, 'getTherapyOrders']);

        // Stage 4: Pelaksanaan Sesi & Selesai EMR Terapis (Atomic Transaction)
        Route::post('session/{registrasiId}/start', [PediatricWorkflowController::class, 'startTherapySession'])
            ->middleware('role:super_admin,terapis,admin');
        Route::post('session/{registrasiId}/complete', [PediatricWorkflowController::class, 'completeTherapySession'])
            ->middleware('role:super_admin,terapis,admin');

        // Stage 5: Kasir Dual-Track Settlement & AR Klaim Penjamin
        Route::post('billing/{invoiceId}/settle', [PediatricWorkflowController::class, 'settleDualTrackInvoice'])
            ->middleware('role:super_admin,kasir,admin');
        Route::get('billing/insurance-claims', [PediatricWorkflowController::class, 'getInsuranceClaims'])
            ->middleware('role:super_admin,kasir,admin');
        Route::post('billing/submit-claims', [PediatricWorkflowController::class, 'submitClaimBatch'])
            ->middleware('role:super_admin,kasir,admin');
        Route::post('billing/claims/{claimId}/reconcile', [PediatricWorkflowController::class, 'reconcileInsuranceClaim'])
            ->middleware('role:super_admin,kasir,admin');

        // Stage 6: Portal Orang Tua
        Route::get('parent/dashboard', [PediatricWorkflowController::class, 'getParentDashboard']);
        Route::get('parent/calendar', [PediatricWorkflowController::class, 'getParentCalendar']);
        Route::post('parent/session/{registrasiId}/confirm', [PediatricWorkflowController::class, 'confirmAttendance']);

        // Appointments & Check-In Sesi Lanjutan
        Route::get('appointments', [PediatricWorkflowController::class, 'getAppointments']);
        Route::post('appointments/{id}/check-in', [PediatricWorkflowController::class, 'checkInAppointment'])
            ->middleware('role:super_admin,pendaftaran,admin');

        // Dokter E-Resep (Signa Lengkap) & Dispensing Farmasi
        Route::post('e-resep', [PediatricWorkflowController::class, 'storeEResep'])
            ->middleware('role:super_admin,dokter,admin');
        Route::get('e-resep/by-registrasi/{registrasiId}', [PediatricWorkflowController::class, 'getEResepByRegistrasi']);
        Route::get('registration/{registrasiId}/orders-summary', [PediatricWorkflowController::class, 'getRegistrationOrdersSummary']);
        Route::get('farmasi/antrian-resep', [PediatricWorkflowController::class, 'getAntrianResepFarmasi']);
        Route::post('farmasi/dispense/{id}', [PediatricWorkflowController::class, 'dispenseResep']);

        // Verifikasi Pra-Billing: E-Resep Farmasi & Order Penunjang
        Route::get('verifikasi/stats', [PediatricWorkflowController::class, 'getVerifikasiOverviewStats']);
        Route::get('verifikasi/antrian-resep', [PediatricWorkflowController::class, 'getAntrianVerifikasiResep']);
        Route::post('verifikasi/resep/{id}/approve', [PediatricWorkflowController::class, 'verifikasiResepApprove'])
            ->middleware('role:super_admin,farmasi,admin');
        Route::post('verifikasi/resep/{id}/reject', [PediatricWorkflowController::class, 'verifikasiResepReject'])
            ->middleware('role:super_admin,farmasi,admin');

        // Dokter / Terapis Order Penunjang (Lab / Radiologi / Tindakan)
        Route::post('order-penunjang', [PediatricWorkflowController::class, 'storeOrderPenunjang'])
            ->middleware('role:super_admin,dokter,terapis,admin');
        Route::get('order-penunjang/by-registrasi/{registrasiId}', [PediatricWorkflowController::class, 'getOrderPenunjangByRegistrasi']);
        Route::get('verifikasi/antrian-penunjang', [PediatricWorkflowController::class, 'getAntrianVerifikasiPenunjang']);
        Route::post('verifikasi/penunjang/{id}/approve', [PediatricWorkflowController::class, 'verifikasiPenunjangApprove'])
            ->middleware('role:super_admin,dokter,terapis,admin');
        Route::post('verifikasi/penunjang/{id}/reject', [PediatricWorkflowController::class, 'verifikasiPenunjangReject'])
            ->middleware('role:super_admin,dokter,terapis,admin');

        // Kasir Klaim Asuransi vs Cash Selisih
        Route::post('billing/{invoiceId}/klaim-asuransi', [PediatricWorkflowController::class, 'settleAsuransiKlaim'])
            ->middleware('role:super_admin,kasir,admin');

        // =========================================================================
        // DYNAMIC CLINICAL MASTER & VITAL PARAMETERS API (V1 & STANDARD)
        // =========================================================================
        Route::prefix('v1')->group(function () {
            // Master parameters
            Route::get('master/vital-parameters', [ClinicalMasterController::class, 'getVitalParameters']);
            Route::get('master/allergies', [ClinicalMasterController::class, 'getAllergies']);
            Route::get('master/soap-templates', [ClinicalMasterController::class, 'getSoapTemplates']);
            Route::get('master/icd10', [ClinicalMasterController::class, 'getIcd10']);
            Route::get('master/procedures', [ClinicalMasterController::class, 'getProcedures']);
            Route::get('master/medications', [ClinicalMasterController::class, 'getMedications']);
            Route::get('master/units', [ClinicalMasterController::class, 'getUnits']);
            Route::get('master/signa', [ClinicalMasterController::class, 'getSigna']);
            Route::get('master/intake-times', [ClinicalMasterController::class, 'getIntakeTimes']);
            Route::get('master/ancillary-tests', [ClinicalMasterController::class, 'getAncillaryTests']);

            // Patient vitals & allergies
            Route::get('visits/{visitId}/vitals', [ClinicalMasterController::class, 'getPatientVitals']);
            Route::get('patients/{patientId}/vitals', [ClinicalMasterController::class, 'getPatientVitals']);
            Route::post('patients/{patientId}/vitals', [ClinicalMasterController::class, 'storePatientVitals']);
            Route::get('patients/{patientId}/vitals/history', [ClinicalMasterController::class, 'getPatientVitalsHistory']);
            Route::get('patients/{patientId}/allergies', [ClinicalMasterController::class, 'getPatientAllergies']);
            Route::post('patients/{patientId}/allergies', [ClinicalMasterController::class, 'savePatientAllergies']);

            // Atomic EMR Save
            Route::post('emr/save', [ClinicalMasterController::class, 'atomicEmrSave']);
        });

        // Non-v1 aliases for seamless compatibility
        Route::get('master/vital-parameters', [ClinicalMasterController::class, 'getVitalParameters']);
        Route::get('master/allergies', [ClinicalMasterController::class, 'getAllergies']);
        Route::get('master/soap-templates', [ClinicalMasterController::class, 'getSoapTemplates']);
        Route::get('master/icd10', [ClinicalMasterController::class, 'getIcd10']);
        Route::get('master/procedures', [ClinicalMasterController::class, 'getProcedures']);
        Route::get('master/medications', [ClinicalMasterController::class, 'getMedications']);
        Route::get('master/units', [ClinicalMasterController::class, 'getUnits']);
        Route::get('master/signa', [ClinicalMasterController::class, 'getSigna']);
        Route::get('master/intake-times', [ClinicalMasterController::class, 'getIntakeTimes']);
        Route::get('master/ancillary-tests', [ClinicalMasterController::class, 'getAncillaryTests']);
        Route::get('patients/{patientId}/vitals', [ClinicalMasterController::class, 'getPatientVitals']);
        Route::post('patients/{patientId}/vitals', [ClinicalMasterController::class, 'storePatientVitals']);
        Route::get('patients/{patientId}/vitals/history', [ClinicalMasterController::class, 'getPatientVitalsHistory']);
        Route::get('patients/{patientId}/allergies', [ClinicalMasterController::class, 'getPatientAllergies']);
        Route::post('patients/{patientId}/allergies', [ClinicalMasterController::class, 'savePatientAllergies']);
        Route::post('emr/save', [ClinicalMasterController::class, 'atomicEmrSave']);
    });
});
