<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfileAnak;
use App\Models\RegistrasiAnak;
use App\Models\Guarantor;
use App\Models\PatientInsurance;
use App\Models\TherapyPackage;
use App\Models\PaketTerapiPasien;
use App\Models\TherapyOrder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PembayaranTransaksi;
use App\Models\InsuranceClaim;
use App\Models\EmrRecord;
use App\Models\EmrSoap;
use App\Models\Ruangan;
use App\Models\Layanan;
use App\Models\ShiftKasir;
use App\Models\UserProfile;
use App\Models\MasterIcd10;
use App\Models\MasterIcd9;
use App\Models\PatientAppointment;
use App\Models\EResep;
use App\Models\EResepItem;
use App\Models\OrderPenunjang;
use App\Models\MasterProduk;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PediatricWorkflowController extends Controller
{
    // =========================================================================
    // MASTER DATA HELPERS
    // =========================================================================

    public function getGuarantors()
    {
        $guarantors = Guarantor::where('status_aktif', true)->orderBy('nama_penjamin')->get();
        return response()->json($guarantors);
    }

    public function storeGuarantor(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'kode_penjamin' => 'required|string|max:50',
            'nama_penjamin' => 'required|string|max:255',
            'tipe' => 'required|string',
            'kontak_klaim' => 'nullable|string',
            'email_klaim' => 'nullable|email',
            'alamat' => 'nullable|string',
            'status_aktif' => 'nullable|boolean',
        ]);

        $guarantor = Guarantor::updateOrCreate(
            ['id' => $request->id],
            $validated
        );

        return response()->json([
            'message' => 'Data Master Penjamin / Asuransi berhasil disimpan.',
            'data' => $guarantor,
        ], 201);
    }

    public function deleteGuarantor($id)
    {
        $guarantor = Guarantor::findOrFail($id);
        $guarantor->update(['status_aktif' => false]);
        return response()->json(['message' => 'Penjamin berhasil dinonaktifkan.']);
    }

    public function getTherapyPackages()
    {
        $packages = TherapyPackage::with('layanan')->where('status_aktif', true)->get();
        return response()->json($packages);
    }

    public function storeTherapyPackage(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'nama_paket' => 'required|string|max:255',
            'layanan_id' => 'required|exists:layanan,id',
            'jumlah_sesi' => 'required|integer|min:1',
            'harga_paket' => 'required|numeric|min:0',
            'masa_berlaku_hari' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'status_aktif' => 'nullable|boolean',
        ]);

        $package = TherapyPackage::updateOrCreate(
            ['id' => $request->id],
            $validated
        );

        return response()->json([
            'message' => 'Data Master Paket Terapi berhasil disimpan.',
            'data' => $package->load('layanan'),
        ], 201);
    }

    public function deleteTherapyPackage($id)
    {
        $package = TherapyPackage::findOrFail($id);
        $package->update(['status_aktif' => false]);
        return response()->json(['message' => 'Paket Terapi berhasil dinonaktifkan.']);
    }

    public function getIcd10All(Request $request)
    {
        $query = MasterIcd10::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('kode', 'ILIKE', "%{$s}%")
                  ->orWhere('deskripsi_id', 'ILIKE', "%{$s}%")
                  ->orWhere('deskripsi_en', 'ILIKE', "%{$s}%");
            });
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        return response()->json($query->orderBy('kode')->paginate($request->query('per_page', 25)));
    }

    public function storeIcd10(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'kode' => 'required|string|max:20',
            'deskripsi_en' => 'required|string',
            'deskripsi_id' => 'required|string',
            'kategori' => 'nullable|string',
            'is_terapi_anak' => 'nullable|boolean',
        ]);

        $icd = MasterIcd10::updateOrCreate(['id' => $request->id], $validated);
        return response()->json(['message' => 'Master ICD-10 berhasil disimpan.', 'data' => $icd], 201);
    }

    public function deleteIcd10($id)
    {
        MasterIcd10::destroy($id);
        return response()->json(['message' => 'Kode ICD-10 berhasil dihapus.']);
    }

    public function getIcd9All(Request $request)
    {
        $query = MasterIcd9::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('kode', 'ILIKE', "%{$s}%")
                  ->orWhere('deskripsi_id', 'ILIKE', "%{$s}%")
                  ->orWhere('deskripsi_en', 'ILIKE', "%{$s}%");
            });
        }
        return response()->json($query->orderBy('kode')->paginate($request->query('per_page', 25)));
    }

    public function storeIcd9(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'kode' => 'required|string|max:20',
            'deskripsi_en' => 'required|string',
            'deskripsi_id' => 'required|string',
            'is_terapi_anak' => 'nullable|boolean',
        ]);

        $icd = MasterIcd9::updateOrCreate(['id' => $request->id], $validated);
        return response()->json(['message' => 'Master ICD-9 Prosedur berhasil disimpan.', 'data' => $icd], 201);
    }

    public function deleteIcd9($id)
    {
        MasterIcd9::destroy($id);
        return response()->json(['message' => 'Kode ICD-9 berhasil dihapus.']);
    }

    public function getMasterOverviewStats()
    {
        return response()->json([
            'total_pasien_anak' => ProfileAnak::count(),
            'total_user' => DB::table('user_login')->count(),
            'total_role' => DB::table('role')->count(),
            'total_terapis' => UserProfile::count(),
            'total_ruangan' => Ruangan::count(),
            'total_layanan' => Layanan::count(),
            'total_kategori_layanan' => DB::table('kategori_layanan')->count(),
            'total_guarantor' => Guarantor::count(),
            'total_paket_terapi' => TherapyPackage::count(),
            'total_produk_farmasi' => DB::table('master_produk')->count(),
            'total_promosi' => DB::table('promosi')->count(),
            'total_icd10' => MasterIcd10::count(),
            'total_icd9' => MasterIcd9::count(),
            'total_supplier' => DB::table('supplier')->count(),
        ]);
    }

    public function getChildPackages($childId)
    {
        $packages = PaketTerapiPasien::where('profile_anak_id', $childId)
            ->where('status', 'aktif')
            ->where('sisa_sesi', '>', 0)
            ->with('layanan')
            ->get();
        return response()->json($packages);
    }

    public function getChildInsurances($childId)
    {
        $today = now()->toDateString();
        $insurances = PatientInsurance::where('profile_anak_id', $childId)
            ->where('status_aktif', true)
            ->where('sisa_kuota_sesi', '>', 0)
            ->where('tgl_berlaku_selesai', '>=', $today)
            ->with('guarantor')
            ->get();
        return response()->json($insurances);
    }

    // =========================================================================
    // STAGE 1: REGISTRASI & PENDAFTARAN DUAL-TRACK
    // =========================================================================

    public function registerChildSession(Request $request)
    {
        $request->validate([
            'profile_anak_id' => 'nullable|exists:profile_anak,id',
            'nama_anak' => 'required_without:profile_anak_id|nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin_id' => 'nullable|exists:jenis_kelamin,id',
            'NIK' => 'nullable|string|max:30',
            'nama_orang_tua' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'nama_ayah' => 'nullable|string|max:255',
            'notelp' => 'nullable|string|max:25',
            'alamat' => 'nullable|string',
            'tgl_regis' => 'required|date',
            'terapis_id' => 'required|exists:user_profile,id',
            'ruangan_id' => 'required|exists:ruangan,id',
            'payment_track' => 'required|in:umum_single,umum_package,asuransi',
            // Track Umum Package fields
            'patient_therapy_package_id' => 'nullable|exists:paket_terapi_pasien,id',
            'therapy_package_id' => 'nullable|exists:therapy_packages,id',
            // Track Asuransi fields
            'guarantor_id' => 'nullable|exists:guarantors,id',
            'no_polis' => 'nullable|string',
            'no_surat_jaminan' => 'nullable|string',
            'kuota_sesi' => 'nullable|integer',
            'tgl_berlaku_mulai' => 'nullable|date',
            'tgl_berlaku_selesai' => 'nullable|date',
            // Jadwal berulang
            'jadwal_hari' => 'nullable|string',
            'jadwal_jam' => 'nullable|string',
            'keluhan_saat_ini' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $child = null;
            $parentAccount = null;

            if ($request->filled('profile_anak_id')) {
                $child = ProfileAnak::findOrFail($request->profile_anak_id);
            } else {
                // Auto create child in Pendaftaran Baru (No dropdown barrier)
                $ym = date('Ym');
                $countRm = ProfileAnak::where('no_rm', 'like', "RM-{$ym}-%")->count() + 1;
                $noRm = 'RM-' . $ym . '-' . str_pad($countRm, 4, '0', STR_PAD_LEFT);

                // Auto create Akun Portal Orang Tua (role_id = 15)
                $parentName = $request->nama_orang_tua ?? ($request->nama_ibu ?? ($request->nama_ayah ?? 'Orang Tua ' . $request->nama_anak));
                $phone = $request->notelp ?? ($request->no_wa_orang_tua ?? '-');
                $cleanChild = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->nama_anak));
                $username = 'ortu_' . substr($cleanChild, 0, 8) . rand(10, 99);
                $rawPassword = $request->password_ortu ?: '123456';

                while (\App\Models\UserLogin::where('username', $username)->exists()) {
                    $username = 'ortu_' . substr($cleanChild, 0, 8) . rand(100, 999);
                }

                $parentProfile = \App\Models\UserProfile::create([
                    'nama' => $parentName,
                    'no_telepon' => $phone,
                    'email' => $username . '@klinikku.id',
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

                $nik = $request->NIK ?: ('NIK-' . date('Ymd') . rand(1000, 9999));

                $child = ProfileAnak::create([
                    'nama_anak' => $request->nama_anak,
                    'no_rm' => $noRm,
                    'NIK' => $nik,
                    'tanggal_lahir' => $request->tanggal_lahir ?: now()->subYears(4)->toDateString(),
                    'jenis_kelamin_id' => $request->jenis_kelamin_id ?: 1,
                    'agama_id' => $request->agama_id ?: 1,
                    'alamat' => $request->alamat,
                    'id_orang_tua' => $parentProfile->id,
                    'parent_user_id' => $parentLogin->id,
                    'no_hp_orang_tua' => $phone,
                    'diagnosis_awal' => $request->keluhan_saat_ini,
                    'status_aktif' => true,
                ]);

                \App\Models\PasienWali::create([
                    'profile_anak_id' => $child->id,
                    'user_profile_id' => $parentProfile->id,
                    'nama_wali' => $parentName,
                    'hubungan' => $request->filled('nama_ibu') ? 'Ibu Kandung' : ($request->filled('nama_ayah') ? 'Ayah Kandung' : 'Wali'),
                    'no_hp' => $phone,
                    'alamat' => $request->alamat,
                    'is_wali_utama' => true,
                    'is_kontak_darurat' => true,
                ]);

                $parentAccount = [
                    'username' => $username,
                    'password' => $rawPassword,
                    'nama_wali' => $parentName,
                    'no_wa' => $phone,
                    'no_rm' => $noRm,
                ];
            }

            // Determine discipline prefix for queue number
            $ruangan = Ruangan::find($request->ruangan_id);
            $defaultLayananId = Layanan::first()?->id ?? 3;
            $layananId = $ruangan?->id_layanan ?? ($ruangan?->layanan_id ?? $defaultLayananId);
            $prefix = 'TW'; // Default Terapi Wicara
            $roomName = strtolower($ruangan ? $ruangan->ruangan : '');
            if (str_contains($roomName, 'okupasi') || str_contains($roomName, 'ot')) {
                $prefix = 'OT';
            } elseif (str_contains($roomName, 'fisio') || str_contains($roomName, 'ft')) {
                $prefix = 'FT';
            } elseif (str_contains($roomName, 'sensori') || str_contains($roomName, 'si')) {
                $prefix = 'SI';
            } elseif (str_contains($roomName, 'psiko') || str_contains($roomName, 'pa')) {
                $prefix = 'PK';
            }

            $today = Carbon::parse($request->tgl_regis)->toDateString();
            $countToday = RegistrasiAnak::whereDate('tgl_regis', $today)->count() + 1;
            $queueNumber = "{$prefix}-" . str_pad($countToday, 2, '0', STR_PAD_LEFT);

            // Generate Registration Number
            $ymd = Carbon::parse($request->tgl_regis)->format('Ymd');
            $noRegis = "REG-{$ymd}-" . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            // Handle Track A: Umum Paket verification & session count
            $sesiKe = 1;
            $totalSesi = 1;
            $packageId = null;

            if ($request->payment_track === 'umum_package') {
                if ($request->filled('patient_therapy_package_id')) {
                    $pkg = PaketTerapiPasien::where('id', $request->patient_therapy_package_id)
                        ->where('profile_anak_id', $child->id)
                        ->first();
                    if (!$pkg || $pkg->sisa_sesi <= 0) {
                        return response()->json([
                            'message' => 'Kuota paket terapi ini sudah habis atau tidak valid. Silakan beli paket baru atau pilih per kunjungan.',
                        ], 422);
                    }
                    $packageId = $pkg->id;
                    $sesiKe = $pkg->sesi_terpakai + 1;
                    $totalSesi = $pkg->total_sesi;
                } elseif ($request->filled('therapy_package_id')) {
                    $masterPkg = TherapyPackage::find($request->therapy_package_id);
                    $tot = $masterPkg ? $masterPkg->jumlah_sesi : 8;
                    $noPaket = "PKT-{$ymd}-" . rand(1000, 9999);
                    $pkg = PaketTerapiPasien::create([
                        'profile_anak_id' => $child->id,
                        'layanan_id' => $masterPkg?->layanan_id ?? $defaultLayananId,
                        'nomor_paket' => $noPaket,
                        'nama_paket' => $masterPkg?->nama_paket ?? "Paket Terapi ({$tot} Sesi)",
                        'total_sesi' => $tot,
                        'sesi_terpakai' => 1,
                        'sisa_sesi' => $tot - 1,
                        'harga_paket' => $masterPkg?->harga_paket ?? 1500000,
                        'is_lunas_awal' => true,
                        'status_bayar' => 'lunas_di_awal',
                        'tanggal_mulai' => $today,
                        'tanggal_berakhir' => Carbon::parse($today)->addDays($masterPkg?->masa_berlaku_hari ?? 180)->toDateString(),
                        'status' => 'aktif',
                    ]);
                    $packageId = $pkg->id;
                    $sesiKe = 1;
                    $totalSesi = $tot;
                } else {
                    return response()->json([
                        'message' => 'Harap pilih paket terapi aktif untuk anak ini.',
                    ], 422);
                }
            }

            // Handle Track B: Asuransi verification or registration
            $insuranceId = null;
            $guarantorId = $request->guarantor_id;

            if ($request->payment_track === 'asuransi') {
                if (!$guarantorId) {
                    return response()->json(['message' => 'Pilih perusahaan penjamin / asuransi.'], 422);
                }

                // Check or create patient insurance GL record
                if ($request->filled('patient_insurance_id')) {
                    $pIns = PatientInsurance::where('id', $request->patient_insurance_id)
                        ->where('profile_anak_id', $child->id)
                        ->first();
                    if (!$pIns || !$pIns->isValid()) {
                        return response()->json([
                            'message' => 'Surat Jaminan (GL) asuransi tidak aktif atau masa berlakunya sudah berakhir.',
                        ], 422);
                    }
                    $insuranceId = $pIns->id;
                    $sesiKe = ($pIns->kuota_sesi - $pIns->sisa_kuota_sesi) + 1;
                    $totalSesi = $pIns->kuota_sesi;
                } else {
                    // Create new GL record on the fly
                    $pIns = PatientInsurance::create([
                        'profile_anak_id' => $child->id,
                        'guarantor_id' => $guarantorId,
                        'no_polis' => $request->no_polis ?? '-',
                        'no_surat_jaminan' => $request->no_surat_jaminan ?? ('GL-' . strtoupper(uniqid())),
                        'tgl_berlaku_mulai' => $request->tgl_berlaku_mulai ?? now()->toDateString(),
                        'tgl_berlaku_selesai' => $request->tgl_berlaku_selesai ?? now()->addMonths(3)->toDateString(),
                        'kuota_sesi' => $request->kuota_sesi ?? 10,
                        'sisa_kuota_sesi' => $request->kuota_sesi ?? 10,
                        'status_aktif' => true,
                    ]);
                    $insuranceId = $pIns->id;
                    $sesiKe = 1;
                    $totalSesi = $pIns->kuota_sesi;
                }
            }

            // Generate Sequential Unique id_pasien for Registrasi
            $lastPasien = RegistrasiAnak::where('profile_anak_id', $child->id)
                ->orderBy('id', 'desc')
                ->first();
            $seqNumber = 1;
            if ($lastPasien && preg_match('/-(\d+)$/', $lastPasien->id_pasien, $matches)) {
                $seqNumber = (int)$matches[1] + 1;
            } elseif ($lastPasien) {
                $seqNumber = RegistrasiAnak::where('profile_anak_id', $child->id)->count() + 1;
            }
            $idPasien = "PA-{$child->id}-" . str_pad($seqNumber, 3, '0', STR_PAD_LEFT);

            $registrasi = RegistrasiAnak::create([
                'profile_anak_id' => $child->id,
                'id_pasien' => $idPasien,
                'no_regis' => $noRegis,
                'tgl_regis' => $request->tgl_regis,
                'terapis_id' => $request->terapis_id,
                'ruangan_id' => $request->ruangan_id,
                'no_antrian' => $queueNumber,
                'antrian_nomor' => $queueNumber,
                'payment_track' => $request->payment_track,
                'guarantor_id' => $guarantorId,
                'patient_insurance_id' => $insuranceId,
                'patient_therapy_package_id' => $packageId,
                'status_sesi' => 'terjadwal',
                'status_kedatangan' => 'belum_datang',
                'status_pelayanan' => 'belum_dilayani',
                'sesi_ke' => $sesiKe,
                'total_sesi' => $totalSesi,
                'keluhan_saat_ini' => $request->keluhan_saat_ini,
                'jadwal_hari' => $request->jadwal_hari,
                'jadwal_jam' => $request->jadwal_jam,
            ]);

            // Buat Appointment untuk Sesi #1 (ditandai checked_in)
            $appt1 = PatientAppointment::create([
                'profile_anak_id' => $child->id,
                'paket_terapi_pasien_id' => $packageId,
                'layanan_id' => $layananId,
                'terapis_id' => $request->terapis_id,
                'ruangan_id' => $request->ruangan_id,
                'sesi_ke' => $sesiKe,
                'total_sesi' => $totalSesi,
                'tanggal_appointment' => $today,
                'jam_mulai' => $request->jadwal_jam ?? '09:00',
                'status' => 'checked_in',
                'registrasi_anak_id' => $registrasi->id,
                'checked_in_at' => now(),
                'checked_in_by' => auth('api')->id(),
            ]);

            // Jika Paket Multi-Sesi & Sesi #1: Otomatis jadwalkan Sesi #2 s/d #totalSesi ke Kalender Appointment!
            if ($packageId && $totalSesi > 1 && $sesiKe === 1) {
                for ($s = 2; $s <= $totalSesi; $s++) {
                    $nextDate = Carbon::parse($today)->addWeeks($s - 1)->toDateString();
                    PatientAppointment::create([
                        'profile_anak_id' => $child->id,
                        'paket_terapi_pasien_id' => $packageId,
                        'layanan_id' => $layananId,
                        'terapis_id' => $request->terapis_id,
                        'ruangan_id' => $request->ruangan_id,
                        'sesi_ke' => $s,
                        'total_sesi' => $totalSesi,
                        'tanggal_appointment' => $nextDate,
                        'jam_mulai' => $request->jadwal_jam ?? '09:00',
                        'status' => 'terjadwal',
                        'registrasi_anak_id' => null, // Belum Check-In!
                    ]);
                }
            }

            return response()->json([
                'message' => 'Registrasi kunjungan pasien anak berhasil disimpan! No Antrian: ' . $queueNumber . ($parentAccount ? ' (Akun Portal Ortu Diterbitkan)' : ''),
                'data' => $registrasi->load(['profileAnak', 'terapis', 'ruangan', 'guarantor', 'patientTherapyPackage']),
                'appointment' => $appt1,
                'parent_account' => $parentAccount,
                'child' => $child,
            ], 201);
        });
    }

    // =========================================================================
    // STAGE 2: CHECK-IN APPOINTMENT & MANAJEMEN ANTRIAN
    // =========================================================================

    public function getAppointments(Request $request)
    {
        $query = PatientAppointment::with(['profileAnak', 'layanan', 'terapis', 'ruangan', 'paketTerapiPasien', 'registrasiAnak'])
            ->orderBy('tanggal_appointment', 'asc');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_appointment', $request->tanggal);
        }
        if ($request->filled('profile_anak_id')) {
            $query->where('profile_anak_id', $request->profile_anak_id);
        }
        if ($request->filled('terapis_id')) {
            $query->where('terapis_id', $request->terapis_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate($request->query('per_page', 50)));
    }

    public function checkInAppointment($appointmentId)
    {
        return DB::transaction(function () use ($appointmentId) {
            $appt = PatientAppointment::with(['profileAnak', 'paketTerapiPasien', 'ruangan'])->lockForUpdate()->findOrFail($appointmentId);

            if ($appt->status === 'checked_in' && $appt->registrasi_anak_id) {
                return response()->json([
                    'message' => 'Sesi appointment ini sudah di-check-in sebelumnya.',
                    'data' => $appt->load('registrasiAnak'),
                ]);
            }

            $child = $appt->profileAnak;
            $pkg = $appt->paketTerapiPasien;

            // Aturan Bisnis: Kuota HANYA terpotong tepat saat Check-In diklik hari H
            if ($pkg) {
                if ($pkg->sisa_sesi <= 0) {
                    return response()->json([
                        'message' => 'Peringatan: Kuota paket terapi anak ini sudah 0. Harap lakukan pembelian paket baru di Kasir.',
                    ], 422);
                }
                $pkg->sesi_terpakai += 1;
                $pkg->sisa_sesi -= 1;
                if ($pkg->sisa_sesi <= 0) {
                    $pkg->status = 'habis';
                }
                $pkg->save();
            }

            // Terbitkan ID Pendaftaran Baru untuk Kunjungan Hari Ini
            $today = now()->toDateString();
            $ymd = now()->format('Ymd');
            $countToday = RegistrasiAnak::whereDate('tgl_regis', $today)->count() + 1;
            $noRegis = "REG-{$ymd}-" . str_pad($countToday, 4, '0', STR_PAD_LEFT);
            $queueNumber = "A-" . str_pad($countToday, 2, '0', STR_PAD_LEFT);

            $lastPasien = RegistrasiAnak::where('profile_anak_id', $child->id)->orderBy('id', 'desc')->first();
            $seqNumber = 1;
            if ($lastPasien && preg_match('/-(\d+)$/', $lastPasien->id_pasien, $matches)) {
                $seqNumber = (int)$matches[1] + 1;
            }
            $idPasien = "PA-{$child->id}-" . str_pad($seqNumber, 3, '0', STR_PAD_LEFT);

            $newReg = RegistrasiAnak::create([
                'profile_anak_id' => $child->id,
                'id_pasien' => $idPasien,
                'no_regis' => $noRegis,
                'tgl_regis' => $today,
                'terapis_id' => $appt->terapis_id,
                'ruangan_id' => $appt->ruangan_id,
                'no_antrian' => $queueNumber,
                'antrian_nomor' => $queueNumber,
                'payment_track' => $pkg ? 'umum_package' : 'umum_single',
                'patient_therapy_package_id' => $pkg?->id,
                'status_sesi' => 'menunggu',
                'status_kedatangan' => 'sudah_datang',
                'status_pelayanan' => 'belum_dilayani',
                'sesi_ke' => $appt->sesi_ke,
                'total_sesi' => $appt->total_sesi,
                'waktu_kedatangan' => now(),
            ]);

            // Update status appointment
            $appt->status = 'checked_in';
            $appt->registrasi_anak_id = $newReg->id;
            $appt->checked_in_at = now();
            $appt->checked_in_by = auth('api')->id();
            $appt->save();

            // Otomatis buat draf invoice sesi hari itu (Paket Lunas di Awal bernilai Rp 0)
            $isLunasAwal = $pkg ? ($pkg->is_lunas_awal ?? true) : false;
            $standardTariff = $isLunasAwal ? 0 : 200000;
            $invStatus = $isLunasAwal ? 'lunas' : 'belum_bayar';

            $noInv = "INV-{$ymd}-" . strtoupper(substr(uniqid(), -4)) . '-' . rand(100, 999);
            $invoice = Invoice::create([
                'nomor_invoice' => $noInv,
                'profile_anak_id' => $child->id,
                'registrasi_anak_id' => $newReg->id,
                'subtotal' => $standardTariff,
                'diskon' => 0,
                'pajak' => 0,
                'total_tagihan' => $standardTariff,
                'total_terbayar' => 0,
                'sisa_tagihan' => $standardTariff,
                'status' => $invStatus,
                'jenis_penjamin' => $pkg ? 'Paket Lunas' : 'Umum',
                'payment_track' => $newReg->payment_track,
                'is_paket_lunas' => $isLunasAwal,
                'created_by' => auth('api')->id(),
                'catatan' => $isLunasAwal ? "Voucher Potong Sesi Paket #{$appt->sesi_ke} (Lunas di Awal)" : "Sesi Terapi #{$appt->sesi_ke}",
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'tipe_item' => 'layanan_terapi',
                'deskripsi' => "Sesi Terapi #{$appt->sesi_ke} dari {$appt->total_sesi}" . ($isLunasAwal ? " (Lunas Paket)" : ""),
                'qty' => 1,
                'harga_satuan' => $standardTariff,
                'subtotal' => $standardTariff,
            ]);

            return response()->json([
                'message' => "Pasien {$child->nama_anak} berhasil Check-In Sesi #{$appt->sesi_ke}. ID Pendaftaran: {$newReg->no_regis}, No Antrian: {$queueNumber}.",
                'data' => $newReg->load(['profileAnak', 'terapis', 'ruangan', 'patientTherapyPackage']),
                'appointment' => $appt,
                'invoice' => $invoice,
            ]);
        });
    }

    public function checkInSession($registrasiId)
    {
        return DB::transaction(function () use ($registrasiId) {
            $reg = RegistrasiAnak::with(['profileAnak', 'patientTherapyPackage', 'patientInsurance'])->findOrFail($registrasiId);

            // Pre-session validation for package
            if ($reg->payment_track === 'umum_package' && $reg->patient_therapy_package_id) {
                $pkg = PaketTerapiPasien::find($reg->patient_therapy_package_id);
                if (!$pkg || $pkg->sisa_sesi <= 0) {
                    return response()->json([
                        'message' => 'Peringatan: Kuota paket terapi anak ini sudah 0. Harap perpanjang paket di Kasir sebelum masuk terapi.',
                    ], 422);
                }
            }

            // Pre-session validation for insurance GL
            if ($reg->payment_track === 'asuransi' && $reg->patient_insurance_id) {
                $ins = PatientInsurance::find($reg->patient_insurance_id);
                if (!$ins || !$ins->isValid()) {
                    return response()->json([
                        'message' => 'Peringatan: Surat Jaminan (GL) asuransi telah kedaluwarsa atau sisa kuota jaminan habis.',
                    ], 422);
                }
            }

            $reg->status_kedatangan = 'sudah_datang';
            $reg->status_sesi = 'menunggu';
            $reg->waktu_kedatangan = now();
            $reg->save();

            return response()->json([
                'message' => "Pasien {$reg->profileAnak?->nama_anak} berhasil Check-In. Nomor antrian: {$reg->antrian_nomor}.",
                'data' => $reg,
            ]);
        });
    }

    // =========================================================================
    // STAGE 3: ASESMEN & THERAPY ORDER DOKTER SP.A / SP.KFR
    // =========================================================================

    public function storeTherapyOrder(Request $request)
    {
        $request->validate([
            'profile_anak_id' => 'required|exists:profile_anak,id',
            'registrasi_anak_id' => 'nullable|exists:registrasi_anak,id',
            'diagnosis_klinis' => 'required|string',
            'icd10_kode' => 'nullable|string',
            'layanan_id' => 'nullable|exists:layanan,id',
            'terapis_id' => 'nullable|exists:user_profile,id',
            'frekuensi_mingguan' => 'required|string',
            'total_sesi_rekomendasi' => 'required|integer|min:1',
            'target_jangka_pendek' => 'nullable|string',
            'target_jangka_panjang' => 'nullable|string',
            'catatan_khusus' => 'nullable|string',
        ]);

        $order = TherapyOrder::create([
            'profile_anak_id' => $request->profile_anak_id,
            'registrasi_anak_id' => $request->registrasi_anak_id,
            'dokter_id' => auth('api')->user()?->user_profile_id ?? 1,
            'terapis_id' => $request->terapis_id,
            'layanan_id' => $request->layanan_id,
            'diagnosis_klinis' => $request->diagnosis_klinis,
            'icd10_kode' => $request->icd10_kode,
            'frekuensi_mingguan' => $request->frekuensi_mingguan,
            'total_sesi_rekomendasi' => $request->total_sesi_rekomendasi,
            'target_jangka_pendek' => $request->target_jangka_pendek,
            'target_jangka_panjang' => $request->target_jangka_panjang,
            'catatan_khusus' => $request->catatan_khusus,
            'status' => 'aktif',
        ]);

        return response()->json([
            'message' => 'Rekomendasi Program Terapi Dokter berhasil disimpan.',
            'data' => $order->load(['dokter', 'terapis', 'layanan']),
        ], 201);
    }

    public function getTherapyOrders($profileAnakId)
    {
        $orders = TherapyOrder::where('profile_anak_id', $profileAnakId)
            ->with(['dokter', 'terapis', 'layanan'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($orders);
    }

    // =========================================================================
    // STAGE 4: PELAKSANAAN SESI & EMR OLEH TERAPIS (ATOMIC TRANSACTION)
    // =========================================================================

    public function startTherapySession($registrasiId)
    {
        $reg = RegistrasiAnak::findOrFail($registrasiId);
        $reg->status_sesi = 'sedang_terapi';
        $reg->status_pelayanan = 'sedang_dilayani';
        $reg->save();

        return response()->json([
            'message' => 'Sesi terapi dimulai.',
            'data' => $reg,
        ]);
    }

    public function completeTherapySession(Request $request, $registrasiId)
    {
        $request->validate([
            'mood_anak' => 'required|in:kooperatif,tantrum,lelah,cemas,fokus_baik',
            'durasi_menit' => 'nullable|integer|min:15|max:180',
            'subjektif' => 'nullable|string',
            'objektif' => 'nullable|string',
            'asesmen' => 'nullable|string',
            'planning' => 'nullable|string',
            'aktivitas_terapi' => 'nullable|string',
            'respons_anak' => 'nullable|string',
            'home_program_catatan' => 'required|string', // Program stimulasi di rumah untuk ortu
        ]);

        return DB::transaction(function () use ($request, $registrasiId) {
            $reg = RegistrasiAnak::with(['profileAnak', 'patientTherapyPackage', 'patientInsurance', 'ruangan'])
                ->lockForUpdate()
                ->findOrFail($registrasiId);

            $child = $reg->profileAnak;
            $currentUserId = auth('api')->user()?->user_profile_id ?? 1;

            // 1. RECORD EMR INDUK & SOAP HARIAN
            $today = now()->format('Ymd');
            $noEmr = "EMR-{$today}-" . strtoupper(substr(uniqid(), -4)) . '-' . rand(100, 999);

            $emr = EmrRecord::create([
                'no_emr' => $noEmr,
                'profile_anak_id' => $child->id,
                'registrasi_anak_id' => $reg->id,
                'terapis_id' => $currentUserId,
                'tanggal_pemeriksaan' => now()->toDateString(),
                'jenis_layanan' => $reg->ruangan?->ruangan ?? 'Terapi Anak',
                'status' => 'final',
                'locked_at' => now(),
                'signed_by_id' => $currentUserId,
                'sha256_hash' => hash('sha256', "{$noEmr}|{$child->id}|" . now()->toIso8601String()),
            ]);

            EmrSoap::create([
                'emr_record_id' => $emr->id,
                'subjektif' => $request->subjektif ?? "Kondisi anak saat kedatangan: {$request->mood_anak}.",
                'objektif' => $request->objektif ?? "Aktivitas terapi: {$request->aktivitas_terapi}",
                'asesmen' => $request->asesmen ?? "Evaluasi sesi: {$request->respons_anak}",
                'planning' => $request->planning ?? "Lanjut program sesi berikutnya.",
                'target_sesi' => "Sesi ke-{$reg->sesi_ke} dari {$reg->total_sesi}",
                'aktivitas_terapi' => $request->aktivitas_terapi,
                'respons_anak' => $request->respons_anak,
                'rekomendasi_home_program' => $request->home_program_catatan,
            ]);

            // 2. ATOMIC QUOTA DEDUCTION & CASHIER DRAFT GENERATION
            $standardTariff = 200000; // Standar tarif sesi terapi Rp 200.000
            $coveredAmount = 0;
            $excessAmount = 0;
            $invoiceStatus = 'belum_bayar';
            $arStatus = 'none';
            $deskripsiItem = "Sesi Terapi Anak ({$reg->ruangan?->ruangan}) - Sesi #{$reg->sesi_ke}";

            if ($reg->payment_track === 'umum_package') {
                // Deduct 1 session from child's active package
                if ($reg->patient_therapy_package_id) {
                    $pkg = PaketTerapiPasien::lockForUpdate()->find($reg->patient_therapy_package_id);
                    if ($pkg && $pkg->sisa_sesi > 0) {
                        $pkg->sisa_sesi -= 1;
                        $pkg->sesi_terpakai += 1;
                        if ($pkg->sisa_sesi <= 0) {
                            $pkg->status = 'habis';
                        }
                        $pkg->save();
                    }
                }
                // Package sessions are prepaid; generate Rp 0 quota-deduction voucher invoice
                $standardTariff = 0;
                $deskripsiItem = "Voucher Sesi Paket Terapi - Sesi #{$reg->sesi_ke} dari {$reg->total_sesi} (Sisa: " . ($reg->patientTherapyPackage?->sisa_sesi ?? 0) . " sesi)";
                $invoiceStatus = 'lunas';
            } elseif ($reg->payment_track === 'asuransi') {
                // Deduct 1 session from insurance GL authorization
                if ($reg->patient_insurance_id) {
                    $pIns = PatientInsurance::lockForUpdate()->find($reg->patient_insurance_id);
                    if ($pIns && $pIns->sisa_kuota_sesi > 0) {
                        $pIns->sisa_kuota_sesi -= 1;
                        $pIns->save();
                    }
                }
                // Insurance split: misal Rp 160.000 ditanggung asuransi, Rp 40.000 excess dibayar orang tua
                $coveredAmount = 160000;
                $excessAmount = 40000;
                $standardTariff = 200000;
                $arStatus = 'piutang_asuransi';
                $invoiceStatus = ($excessAmount > 0) ? 'belum_bayar' : 'lunas';
            } else {
                // Track A: Pay-per-visit (Umum Single)
                $standardTariff = 200000;
                $invoiceStatus = 'belum_bayar';
            }

            // Create or update Invoice for Kasir
            $noInv = "INV-{$today}-" . strtoupper(substr(uniqid(), -4)) . '-' . rand(100, 999);

            $invoice = Invoice::create([
                'nomor_invoice' => $noInv,
                'profile_anak_id' => $child->id,
                'registrasi_anak_id' => $reg->id,
                'subtotal' => $standardTariff,
                'diskon' => 0,
                'pajak' => 0,
                'total_tagihan' => $standardTariff,
                'total_terbayar' => ($reg->payment_track === 'umum_package') ? 0 : 0,
                'sisa_tagihan' => ($reg->payment_track === 'umum_package') ? 0 : $standardTariff,
                'status' => $invoiceStatus,
                'jenis_penjamin' => ($reg->payment_track === 'asuransi') ? 'Asuransi' : 'Umum',
                'payment_track' => $reg->payment_track,
                'guarantor_id' => $reg->guarantor_id,
                'patient_insurance_id' => $reg->patient_insurance_id,
                'covered_amount' => $coveredAmount,
                'excess_amount' => $excessAmount,
                'ar_status' => $arStatus,
                'created_by' => $currentUserId,
                'catatan' => "Selesai Sesi Terapi - #{$reg->sesi_ke}",
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'tipe_item' => 'layanan_terapi',
                'deskripsi' => $deskripsiItem,
                'qty' => 1,
                'harga_satuan' => $standardTariff,
                'diskon' => 0,
                'subtotal' => $standardTariff,
            ]);

            // If insurance track, create AR claim entry
            if ($reg->payment_track === 'asuransi' && $reg->guarantor_id) {
                InsuranceClaim::create([
                    'nomor_pengajuan' => "CLM-{$today}-" . rand(1000, 9999),
                    'guarantor_id' => $reg->guarantor_id,
                    'invoice_id' => $invoice->id,
                    'profile_anak_id' => $child->id,
                    'nomor_gl' => $reg->patientInsurance?->no_surat_jaminan ?? 'GL-AUTO',
                    'nominal_diajukan' => $coveredAmount,
                    'status' => 'draft',
                    'tgl_pengajuan' => now()->toDateString(),
                    'catatan' => "Klaim Sesi #{$reg->sesi_ke} - {$child->nama_anak}",
                ]);
            }

            // 3. UPDATE REGISTRASI ANAK TO COMPLETED
            $reg->status_sesi = 'selesai_terapi';
            $reg->status_pelayanan = 'selesai';
            $reg->mood_anak = $request->mood_anak;
            $reg->durasi_menit = $request->durasi_menit ?? 60;
            $reg->home_program_catatan = $request->home_program_catatan;
            $reg->save();

            return response()->json([
                'message' => 'Sesi terapi berhasil diselesaikan. Catatan EMR tersimpan, kuota terpotong, dan draf kasir telah diperbarui.',
                'invoice' => $invoice,
                'emr' => $emr,
                'registrasi' => $reg,
            ]);
        });
    }

    // =========================================================================
    // STAGE 5: KASIR DUAL-TRACK SETTLEMENT & AR PENJAMIN
    // =========================================================================

    public function settleDualTrackInvoice(Request $request, $invoiceId)
    {
        $request->validate([
            'metode_bayar' => 'required|in:tunai,transfer,qris,kartu_debit,kartu_kredit',
            'jumlah_bayar' => 'required|numeric|min:0',
            'nomor_referensi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $invoiceId) {
            $invoice = Invoice::with(['items', 'profileAnak', 'guarantor'])->findOrFail($invoiceId);
            $user = auth('api')->user();
            $kasirId = $user?->user_profile_id ?? 1;

            $today = now()->format('Ymd');
            $noTrx = "TRX-{$today}-" . rand(10000, 99999);

            // Cek shift kasir
            $shift = ShiftKasir::where('kasir_id', $kasirId)
                ->where('status', 'open')
                ->latest()
                ->first();

            // Track A: Paket Purchase at Cashier (Upfront package purchase)
            $hasPackageItem = $invoice->items->firstWhere('tipe_item', 'paket_terapi');
            if ($hasPackageItem && $invoice->status !== 'lunas') {
                // If this is a package purchase invoice, top-up/create child package balance
                $noPaket = "PKT-{$today}-" . rand(1000, 9999);
                PaketTerapiPasien::create([
                    'profile_anak_id' => $invoice->profile_anak_id,
                    'layanan_id' => $hasPackageItem->item_id ?? 1,
                    'invoice_id' => $invoice->id,
                    'nomor_paket' => $noPaket,
                    'nama_paket' => $hasPackageItem->deskripsi,
                    'total_sesi' => 8, // Standard default or from item
                    'sesi_terpakai' => 0,
                    'sisa_sesi' => 8,
                    'tanggal_mulai' => now()->toDateString(),
                    'tanggal_berakhir' => now()->addMonths(6)->toDateString(),
                    'status' => 'aktif',
                ]);
            }

            // Record payment transaction
            $transaksi = PembayaranTransaksi::create([
                'invoice_id' => $invoice->id,
                'nomor_transaksi' => $noTrx,
                'kasir_id' => $kasirId,
                'shift_kasir_id' => $shift?->id,
                'metode_bayar' => $request->metode_bayar,
                'jumlah_bayar' => $request->jumlah_bayar,
                'kembalian' => max(0, $request->jumlah_bayar - $invoice->sisa_tagihan),
                'nomor_referensi' => $request->nomor_referensi,
                'status' => 'sukses',
                'catatan' => $request->catatan,
            ]);

            if ($shift) {
                if ($request->metode_bayar === 'tunai') {
                    $shift->increment('total_tunai_sistem', $request->jumlah_bayar);
                } else {
                    $shift->increment('total_non_tunai_sistem', $request->jumlah_bayar);
                }
            }

            // Update invoice amounts
            $invoice->total_terbayar += $request->jumlah_bayar;
            $invoice->sisa_tagihan = max(0, $invoice->total_tagihan - $invoice->total_terbayar);
            if ($invoice->sisa_tagihan <= 0) {
                $invoice->status = 'lunas';
            } else {
                $invoice->status = 'bayar_sebagian';
            }
            $invoice->save();

            return response()->json([
                'message' => 'Pembayaran billing berhasil diselesaikan.',
                'transaksi' => $transaksi,
                'invoice' => $invoice->fresh(['items', 'profileAnak', 'payments', 'guarantor']),
            ]);
        });
    }

    public function getInsuranceClaims(Request $request)
    {
        $query = InsuranceClaim::with(['guarantor', 'invoice', 'profileAnak'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('guarantor_id')) {
            $query->where('guarantor_id', $request->guarantor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate($request->query('per_page', 15)));
    }

    public function submitClaimBatch(Request $request)
    {
        $request->validate([
            'claim_ids' => 'required|array|min:1',
            'claim_ids.*' => 'exists:insurance_claims,id',
        ]);

        InsuranceClaim::whereIn('id', $request->claim_ids)->update([
            'status' => 'diajukan',
            'tgl_pengajuan' => now()->toDateString(),
        ]);

        return response()->json(['message' => 'Klaim terpilih berhasil diajukan ke pihak penjamin/asuransi.']);
    }

    public function reconcileInsuranceClaim(Request $request, $claimId)
    {
        $request->validate([
            'nominal_disetujui' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $claimId) {
            $claim = InsuranceClaim::with('invoice')->findOrFail($claimId);
            $claim->nominal_disetujui = $request->nominal_disetujui;
            $claim->status = 'lunas';
            $claim->tgl_pembayaran = now()->toDateString();
            $claim->catatan = $request->catatan;
            $claim->save();

            if ($claim->invoice) {
                $claim->invoice->ar_status = 'lunas_penjamin';
                $claim->invoice->tgl_klaim_lunas = now()->toDateString();
                $claim->invoice->save();
            }

            return response()->json([
                'message' => 'Rekonsiliasi pembayaran klaim asuransi berhasil dicatat.',
                'data' => $claim,
            ]);
        });
    }

    // =========================================================================
    // STAGE 6: PORTAL ORANG TUA (SCOPED STRICTLY TO AUTHENTICATED PARENT)
    // =========================================================================

    public function getParentDashboard(Request $request)
    {
        $user = auth('api')->user();
        $parentId = $user?->user_profile_id;

        // Find linked children
        $children = ProfileAnak::where('id_orang_tua', $parentId)
            ->with(['jenisKelamin', 'agama'])
            ->get();

        if ($children->isEmpty()) {
            return response()->json([
                'children' => [],
                'active_child' => null,
                'packages' => [],
                'insurances' => [],
                'upcoming_sessions' => [],
                'home_programs' => [],
                'invoices' => [],
            ]);
        }

        $childIds = $children->pluck('id')->toArray();
        $activeChild = $children->first();

        // 1. Paket terapi aktif dengan persentase sisa sesi
        $packages = PaketTerapiPasien::whereIn('profile_anak_id', $childIds)
            ->where('status', 'aktif')
            ->with('layanan')
            ->get()
            ->map(function ($pkg) {
                $pkg->persentase_terpakai = $pkg->total_sesi > 0
                    ? round(($pkg->sesi_terpakai / $pkg->total_sesi) * 100)
                    : 0;
                return $pkg;
            });

        // 2. Status Jaminan Asuransi (GL)
        $insurances = PatientInsurance::whereIn('profile_anak_id', $childIds)
            ->where('status_aktif', true)
            ->with('guarantor')
            ->get();

        // 3. Jadwal sesi mendatang & riwayat sesi
        $upcomingSessions = RegistrasiAnak::whereIn('profile_anak_id', $childIds)
            ->whereDate('tgl_regis', '>=', now()->toDateString())
            ->with(['terapis', 'ruangan', 'profileAnak'])
            ->orderBy('tgl_regis', 'asc')
            ->limit(5)
            ->get();

        // 4. Catatan Home Program dari Terapis
        $homePrograms = RegistrasiAnak::whereIn('profile_anak_id', $childIds)
            ->whereNotNull('home_program_catatan')
            ->where('home_program_catatan', '!=', '')
            ->with(['terapis', 'ruangan', 'profileAnak'])
            ->orderBy('tgl_regis', 'desc')
            ->limit(6)
            ->get();

        // 5. Riwayat Billing / Voucher Sesi
        $invoices = Invoice::whereIn('profile_anak_id', $childIds)
            ->with(['items', 'guarantor'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'children' => $children,
            'active_child' => $activeChild,
            'packages' => $packages,
            'insurances' => $insurances,
            'upcoming_sessions' => $upcomingSessions,
            'home_programs' => $homePrograms,
            'invoices' => $invoices,
        ]);
    }

    public function confirmAttendance(Request $request, $registrasiId)
    {
        $user = auth('api')->user();
        $parentId = $user?->user_profile_id;

        $reg = RegistrasiAnak::whereHas('profileAnak', function ($q) use ($parentId) {
            $q->where('id_orang_tua', $parentId);
        })->findOrFail($registrasiId);

        $reg->parent_confirmed_at = now();
        $reg->save();

        return response()->json([
            'message' => 'Konfirmasi kehadiran berhasil dikirim ke klinik.',
            'data' => $reg,
        ]);
    }

    // =========================================================================
    // DOKTER E-RESEP (SIGNA LENGKAP) & AGREGASI BILLING HARIAN
    // =========================================================================

    public function storeEResep(Request $request)
    {
        $request->validate([
            'registrasi_anak_id' => 'required|exists:registrasi_anak,id',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nama_obat' => 'required|string',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.satuan' => 'nullable|string',
            'items.*.signa' => 'required|string',
            'items.*.waktu_minum' => 'required|string',
            'items.*.catatan_racikan' => 'nullable|string',
            'items.*.master_produk_id' => 'nullable|exists:master_produk,id',
            'items.*.harga_satuan' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $reg = RegistrasiAnak::with('profileAnak')->findOrFail($request->registrasi_anak_id);
            $user = auth('api')->user();
            $dokterId = $user?->user_profile_id ?? 1;

            $today = now()->format('Ymd');
            $noResep = "RSP-{$today}-" . strtoupper(substr(uniqid(), -4));

            $eResep = EResep::create([
                'nomor_resep' => $noResep,
                'registrasi_anak_id' => $reg->id,
                'profile_anak_id' => $reg->profile_anak_id,
                'dokter_id' => $dokterId,
                'status' => 'menunggu_verifikasi',
                'catatan' => $request->catatan,
            ]);

            $totalObat = 0;
            foreach ($request->items as $it) {
                $master = isset($it['master_produk_id']) ? MasterProduk::find($it['master_produk_id']) : null;
                $harga = $it['harga_satuan'] ?? ($master?->harga_jual ?? 35000);
                $subtotal = $harga * $it['jumlah'];
                $totalObat += $subtotal;

                EResepItem::create([
                    'e_resep_id' => $eResep->id,
                    'master_produk_id' => $it['master_produk_id'] ?? null,
                    'nama_obat' => $it['nama_obat'],
                    'jumlah' => $it['jumlah'],
                    'satuan' => $it['satuan'] ?? ($master?->satuan ?? 'pcs'),
                    'signa' => $it['signa'],
                    'waktu_minum' => $it['waktu_minum'],
                    'catatan_racikan' => $it['catatan_racikan'] ?? null,
                    'harga_satuan' => $harga,
                    'subtotal' => $subtotal,
                ]);
            }

            return response()->json([
                'message' => 'E-Resep berhasil diterbitkan ke Farmasi untuk telaah & verifikasi sebelum masuk ke Billing pasien.',
                'e_resep' => $eResep->load('items'),
            ], 201);
        });
    }

    public function getEResepByRegistrasi($registrasiId)
    {
        $resep = EResep::where('registrasi_anak_id', $registrasiId)
            ->with(['items.masterProduk', 'dokter', 'verifiedBy'])
            ->latest()
            ->first();

        return response()->json($resep);
    }

    public function getRegistrationOrdersSummary($registrasiId)
    {
        $reseps = EResep::where('registrasi_anak_id', $registrasiId)
            ->with(['items.masterProduk', 'dokter', 'verifiedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        $penunjang = OrderPenunjang::where('registrasi_anak_id', $registrasiId)
            ->with(['dokter', 'verifiedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        $invoice = Invoice::where('registrasi_anak_id', $registrasiId)
            ->with(['items'])
            ->first();

        return response()->json([
            'registrasi_id' => $registrasiId,
            'reseps' => $reseps,
            'penunjang' => $penunjang,
            'invoice' => $invoice,
            'total_resep_menunggu' => $reseps->whereIn('status', ['menunggu_verifikasi', 'menunggu_dispensing', 'diajukan'])->count(),
            'total_penunjang_menunggu' => $penunjang->whereIn('status', ['menunggu_verifikasi', 'diajukan'])->count(),
        ]);
    }

    public function getAntrianResepFarmasi()
    {
        $reseps = EResep::with(['profileAnak', 'dokter', 'items.masterProduk', 'registrasiAnak'])
            ->whereIn('status', ['menunggu_verifikasi', 'menunggu_dispensing', 'sedang_disiapkan'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($reseps);
    }

    public function getAntrianVerifikasiResep(Request $request)
    {
        $query = EResep::with(['profileAnak', 'dokter', 'items.masterProduk', 'registrasiAnak.ruangan', 'verifiedBy']);

        if ($request->has('status') && $request->status !== 'semua' && $request->status !== '') {
            $query->where('status', $request->status);
        } else if (!$request->has('status')) {
            $query->whereIn('status', ['menunggu_verifikasi', 'menunggu_dispensing', 'sedang_disiapkan']);
        }

        if ($request->has('search') && $request->search != '') {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_resep', 'ilike', "%{$s}%")
                  ->orWhereHas('profileAnak', function ($sub) use ($s) {
                      $sub->where('nama_anak', 'ilike', "%{$s}%")
                          ->orWhere('no_rm', 'ilike', "%{$s}%");
                  });
            });
        }

        $reseps = $query->orderBy('created_at', 'desc')->get();
        return response()->json($reseps);
    }

    public function verifikasiResepApprove(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $resep = EResep::with(['items.masterProduk', 'registrasiAnak', 'profileAnak'])->findOrFail($id);
            $userId = auth('api')->id() ?? 1;

            $resep->status = 'selesai_dispensing';
            $resep->catatan_telaah = $request->catatan_telaah ?? 'Telaah administratif, farmasetik, dan klinis telah disetujui apoteker.';
            $resep->verified_at = now();
            $resep->verified_by = $userId;
            $resep->dispensed_at = now();
            $resep->dispensed_by = $userId;
            $resep->save();

            // Masukkan biaya obat ke Invoice Billing pasien
            $today = now()->format('Ymd');
            $invoice = Invoice::firstOrCreate(
                ['registrasi_anak_id' => $resep->registrasi_anak_id],
                [
                    'nomor_invoice' => "INV-{$today}-" . strtoupper(substr(uniqid(), -4)) . '-' . rand(100, 999),
                    'profile_anak_id' => $resep->profile_anak_id,
                    'subtotal' => 0,
                    'total_tagihan' => 0,
                    'sisa_tagihan' => 0,
                    'status' => 'belum_bayar',
                    'created_by' => $userId,
                ]
            );

            // Cek apakah item resep ini sudah pernah masuk invoice sebelumnya agar tidak terduplikasi
            foreach ($resep->items as $item) {
                $deskripsi = "Obat: {$item->nama_obat} ({$item->signa} - {$item->waktu_minum})";
                $exists = InvoiceItem::where('invoice_id', $invoice->id)
                    ->where('tipe_item', 'obat_resep')
                    ->where('deskripsi', $deskripsi)
                    ->exists();

                if (!$exists) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'tipe_item' => 'obat_resep',
                        'item_id' => $item->master_produk_id,
                        'deskripsi' => $deskripsi,
                        'qty' => $item->jumlah,
                        'harga_satuan' => $item->harga_satuan,
                        'subtotal' => $item->subtotal,
                    ]);
                }
            }

            // Recalculate invoice
            $allSubtotal = $invoice->items()->sum('subtotal');
            $invoice->subtotal = $allSubtotal;
            $invoice->total_tagihan = $allSubtotal - ($invoice->diskon ?? 0);
            $invoice->sisa_tagihan = max(0, $invoice->total_tagihan - $invoice->total_terbayar);
            if ($invoice->sisa_tagihan > 0 && $invoice->status !== 'lunas') {
                $invoice->status = 'belum_bayar';
            }
            $invoice->save();

            return response()->json([
                'message' => "E-Resep {$resep->nomor_resep} berhasil diverifikasi Farmasi dan resmi masuk ke Billing pasien.",
                'data' => $resep->load(['items', 'verifiedBy']),
                'invoice' => $invoice->fresh('items'),
            ]);
        });
    }

    public function verifikasiResepReject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string',
        ]);

        $resep = EResep::findOrFail($id);
        $resep->status = 'ditolak';
        $resep->catatan_telaah = $request->alasan_penolakan;
        $resep->verified_at = now();
        $resep->verified_by = auth('api')->id();
        $resep->save();

        return response()->json([
            'message' => "E-Resep {$resep->nomor_resep} ditolak/dikembalikan ke dokter pengirim.",
            'data' => $resep,
        ]);
    }

    public function dispenseResep($id)
    {
        $resep = EResep::findOrFail($id);
        $resep->status = 'selesai_dispensing';
        $resep->dispensed_at = now();
        $resep->dispensed_by = auth('api')->id();
        $resep->save();

        return response()->json([
            'message' => 'Obat berhasil disiapkan dan diserahkan kepada keluarga pasien.',
            'data' => $resep,
        ]);
    }

    // =========================================================================
    // DOKTER / TERAPIS ORDER PENUNJANG & AGREGASI BILLING HARIAN
    // =========================================================================

    public function storeOrderPenunjang(Request $request)
    {
        $request->validate([
            'registrasi_anak_id' => 'required|exists:registrasi_anak,id',
            'jenis_order' => 'required|in:laboratorium,radiologi,terapi_tambahan,tindakan_medis',
            'nama_tindakan' => 'required|string',
            'tarif' => 'required|numeric|min:0',
            'catatan_klinis' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $reg = RegistrasiAnak::findOrFail($request->registrasi_anak_id);
            $user = auth('api')->user();
            $dokterId = $user?->user_profile_id ?? 1;

            $today = now()->format('Ymd');
            $noOrder = "ORD-{$today}-" . strtoupper(substr(uniqid(), -4));

            $order = OrderPenunjang::create([
                'nomor_order' => $noOrder,
                'registrasi_anak_id' => $reg->id,
                'profile_anak_id' => $reg->profile_anak_id,
                'dokter_id' => $dokterId,
                'jenis_order' => $request->jenis_order,
                'nama_tindakan' => $request->nama_tindakan,
                'tarif' => $request->tarif,
                'catatan_klinis' => $request->catatan_klinis,
                'status' => 'menunggu_verifikasi',
            ]);

            return response()->json([
                'message' => 'Order penunjang berhasil diajukan untuk verifikasi klinis & hasil pemeriksaan sebelum masuk ke Billing.',
                'order' => $order,
            ], 201);
        });
    }

    public function getOrderPenunjangByRegistrasi($registrasiId)
    {
        $orders = OrderPenunjang::where('registrasi_anak_id', $registrasiId)
            ->with(['dokter', 'verifiedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function getAntrianVerifikasiPenunjang(Request $request)
    {
        $query = OrderPenunjang::with(['profileAnak', 'dokter', 'registrasiAnak.ruangan', 'verifiedBy', 'invoice']);

        if ($request->has('status') && $request->status !== 'semua' && $request->status !== '') {
            $query->where('status', $request->status);
        } else if (!$request->has('status')) {
            $query->whereIn('status', ['menunggu_verifikasi', 'diajukan', 'sedang_diperiksa']);
        }

        if ($request->has('jenis_order') && $request->jenis_order !== '') {
            $query->where('jenis_order', $request->jenis_order);
        }

        if ($request->has('search') && $request->search != '') {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_order', 'ilike', "%{$s}%")
                  ->orWhere('nama_tindakan', 'ilike', "%{$s}%")
                  ->orWhereHas('profileAnak', function ($sub) use ($s) {
                      $sub->where('nama_anak', 'ilike', "%{$s}%")
                          ->orWhere('no_rm', 'ilike', "%{$s}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->get();
        return response()->json($orders);
    }

    public function verifikasiPenunjangApprove(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $order = OrderPenunjang::with(['registrasiAnak', 'profileAnak'])->findOrFail($id);
            $userId = auth('api')->id() ?? 1;

            if ($request->has('hasil_pemeriksaan') && $request->hasil_pemeriksaan !== '') {
                $order->hasil_pemeriksaan = $request->hasil_pemeriksaan;
            }
            if ($request->has('tarif') && is_numeric($request->tarif)) {
                $order->tarif = $request->tarif;
            }

            $order->status = 'selesai';
            $order->verified_at = now();
            $order->verified_by = $userId;

            // Masukkan biaya penunjang ke Invoice Billing pasien
            $today = now()->format('Ymd');
            $invoice = Invoice::firstOrCreate(
                ['registrasi_anak_id' => $order->registrasi_anak_id],
                [
                    'nomor_invoice' => "INV-{$today}-" . strtoupper(substr(uniqid(), -4)) . '-' . rand(100, 999),
                    'profile_anak_id' => $order->profile_anak_id,
                    'subtotal' => 0,
                    'total_tagihan' => 0,
                    'sisa_tagihan' => 0,
                    'status' => 'belum_bayar',
                    'created_by' => $userId,
                ]
            );

            $order->invoice_id = $invoice->id;
            $order->save();

            // Cek duplikasi item penunjang
            $deskripsi = strtoupper($order->jenis_order) . ": {$order->nama_tindakan}";
            $exists = InvoiceItem::where('invoice_id', $invoice->id)
                ->where('tipe_item', 'order_penunjang')
                ->where('deskripsi', $deskripsi)
                ->exists();

            if (!$exists) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'tipe_item' => 'order_penunjang',
                    'deskripsi' => $deskripsi,
                    'qty' => 1,
                    'harga_satuan' => $order->tarif,
                    'subtotal' => $order->tarif,
                ]);
            }

            // Recalculate invoice
            $allSubtotal = $invoice->items()->sum('subtotal');
            $invoice->subtotal = $allSubtotal;
            $invoice->total_tagihan = $allSubtotal - ($invoice->diskon ?? 0);
            $invoice->sisa_tagihan = max(0, $invoice->total_tagihan - $invoice->total_terbayar);
            if ($invoice->sisa_tagihan > 0 && $invoice->status !== 'lunas') {
                $invoice->status = 'belum_bayar';
            }
            $invoice->save();

            return response()->json([
                'message' => "Order Penunjang {$order->nomor_order} berhasil diverifikasi & biaya tindakan resmi masuk ke Billing pasien.",
                'data' => $order->load('verifiedBy'),
                'invoice' => $invoice->fresh('items'),
            ]);
        });
    }

    public function verifikasiPenunjangReject(Request $request, $id)
    {
        $request->validate([
            'alasan_batal' => 'required|string',
        ]);

        $order = OrderPenunjang::findOrFail($id);
        $order->status = 'dibatalkan';
        $order->alasan_batal = $request->alasan_batal;
        $order->verified_at = now();
        $order->verified_by = auth('api')->id();
        $order->save();

        return response()->json([
            'message' => "Order penunjang {$order->nomor_order} berhasil dibatalkan.",
            'data' => $order,
        ]);
    }

    public function getVerifikasiOverviewStats()
    {
        $resepMenunggu = EResep::whereIn('status', ['menunggu_verifikasi', 'menunggu_dispensing', 'diajukan', 'sedang_disiapkan'])->count();
        $resepSelesai = EResep::whereIn('status', ['selesai_dispensing', 'diverifikasi'])->count();
        $resepDitolak = EResep::where('status', 'ditolak')->count();

        $penunjangMenunggu = OrderPenunjang::whereIn('status', ['menunggu_verifikasi', 'diajukan', 'sedang_diperiksa'])->count();
        $penunjangSelesai = OrderPenunjang::whereIn('status', ['selesai', 'diverifikasi'])->count();
        $penunjangDitolak = OrderPenunjang::where('status', 'dibatalkan')->count();

        return response()->json([
            'resep_menunggu' => $resepMenunggu,
            'resep_selesai' => $resepSelesai,
            'resep_ditolak' => $resepDitolak,
            'penunjang_menunggu' => $penunjangMenunggu,
            'penunjang_selesai' => $penunjangSelesai,
            'penunjang_ditolak' => $penunjangDitolak,
        ]);
    }

    // =========================================================================
    // KASIR INPUT KLAIM ASURANSI VS EXCESS CASH
    // =========================================================================

    public function settleAsuransiKlaim(Request $request, $invoiceId)
    {
        $request->validate([
            'nominal_klaim_asuransi' => 'required|numeric|min:0',
            'bayar_selisih_cash' => 'nullable|boolean',
            'metode_bayar_selisih' => 'nullable|in:tunai,transfer,qris,kartu_debit,kartu_kredit',
            'nomor_referensi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $invoiceId) {
            $invoice = Invoice::with(['items', 'profileAnak', 'guarantor'])->findOrFail($invoiceId);
            $user = auth('api')->user();

            $nominalKlaim = $request->nominal_klaim_asuransi;
            $selisih = max(0, $invoice->total_tagihan - $nominalKlaim);

            $invoice->nominal_klaim_asuransi = $nominalKlaim;
            $invoice->covered_amount = $nominalKlaim;
            $invoice->selisih_excess_cash = $selisih;
            $invoice->excess_amount = $selisih;

            if ($selisih == 0) {
                // Full Cover
                $invoice->status = 'terjamin_asuransi';
                $invoice->excess_cash_paid = true;
                $invoice->sisa_tagihan = 0;
            } else {
                // Ada selisih yang wajib dibayar Cash saat ini
                if ($request->bayar_selisih_cash) {
                    $metode = $request->metode_bayar_selisih ?? 'tunai';
                    $today = now()->format('Ymd');
                    $noTrx = "TRX-EXC-{$today}-" . rand(10000, 99999);

                    PembayaranTransaksi::create([
                        'invoice_id' => $invoice->id,
                        'nomor_transaksi' => $noTrx,
                        'kasir_id' => $user?->user_profile_id,
                        'metode_bayar' => $metode,
                        'jumlah_bayar' => $selisih,
                        'kembalian' => 0,
                        'nomor_referensi' => $request->nomor_referensi,
                        'status' => 'sukses',
                        'catatan' => 'Pelunasan Selisih Excess Klaim Asuransi: ' . ($request->catatan ?? ''),
                    ]);

                    $invoice->total_terbayar += $selisih;
                    $invoice->sisa_tagihan = 0;
                    $invoice->status = 'lunas_dengan_excess_cash';
                    $invoice->excess_cash_paid = true;
                } else {
                    $invoice->sisa_tagihan = $selisih;
                    $invoice->status = 'menunggu_pelunasan_excess';
                    $invoice->excess_cash_paid = false;
                }
            }

            $invoice->ar_status = 'piutang_asuransi';
            $invoice->save();

            // Catat pengajuan klaim ke asuransi
            if ($invoice->guarantor_id && $nominalKlaim > 0) {
                InsuranceClaim::updateOrCreate(
                    ['invoice_id' => $invoice->id],
                    [
                        'nomor_pengajuan' => "CLM-" . date('Ymd') . "-" . rand(1000, 9999),
                        'guarantor_id' => $invoice->guarantor_id,
                        'profile_anak_id' => $invoice->profile_anak_id,
                        'nomor_gl' => $invoice->nomor_penjamin ?? 'GL-AUTO',
                        'nominal_diajukan' => $nominalKlaim,
                        'status' => 'draft',
                        'tgl_pengajuan' => now()->toDateString(),
                        'catatan' => 'Pengajuan Klaim Asuransi Kunjungan ' . ($invoice->profileAnak?->nama_anak ?? ''),
                    ]
                );
            }

            return response()->json([
                'message' => $selisih == 0 
                    ? 'Klaim Asuransi Full Cover berhasil diverifikasi. Status: TERJAMIN ASURANSI.'
                    : ($invoice->excess_cash_paid 
                        ? 'Selisih klaim asuransi (excess) berhasil dilunasi pasien. Status: LUNAS + Piutang Klaim.'
                        : 'Nominal klaim tersimpan. Selisih tagihan wajib dilunasi pasien di Kasir.'),
                'invoice' => $invoice->fresh(['items', 'profileAnak', 'payments']),
            ]);
        });
    }

    // =========================================================================
    // PORTAL ORANG TUA: INTERACTIVE CALENDAR & MONITORING
    // =========================================================================

    public function getParentCalendar(Request $request)
    {
        $user = auth('api')->user();
        $parentId = $user?->user_profile_id;

        $children = ProfileAnak::where('id_orang_tua', $parentId)
            ->orWhere('parent_user_id', $user?->id)
            ->get();

        $childIds = $children->pluck('id')->toArray();

        $appointments = PatientAppointment::whereIn('profile_anak_id', $childIds)
            ->with(['profileAnak', 'layanan', 'terapis', 'ruangan', 'paketTerapiPasien'])
            ->orderBy('tanggal_appointment', 'asc')
            ->get();

        $resepList = EResep::whereIn('profile_anak_id', $childIds)
            ->with(['items.masterProduk', 'dokter'])
            ->latest()
            ->limit(10)
            ->get();

        $packages = PaketTerapiPasien::whereIn('profile_anak_id', $childIds)
            ->where('status', 'aktif')
            ->with('layanan')
            ->get();

        return response()->json([
            'children' => $children,
            'appointments' => $appointments,
            'resep_obat' => $resepList,
            'packages' => $packages,
        ]);
    }
}
