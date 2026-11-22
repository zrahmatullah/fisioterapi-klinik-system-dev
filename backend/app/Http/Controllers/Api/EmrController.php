<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmrRecord;
use App\Models\EmrSoap;
use App\Models\EmrAssessment;
use App\Models\EmrGoalTracking;
use App\Models\EmrAddendum;
use App\Models\EmrAuditLog;
use App\Models\MasterIcd10;
use App\Models\MasterIcd9;
use App\Models\ProfileAnak;
use App\Models\RegistrasiAnak;
use App\Models\PelayananTerapiAnak;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmrController extends Controller
{
    // ================= 1. TIMELINE PASIEN =================
    public function timeline($profileAnakId)
    {
        $user = auth('api')->user();
        $pasien = ProfileAnak::with([
            'jenisKelamin', 
            'agama', 
            'walis', 
            'informedConsents'
        ])->findOrFail($profileAnakId);

        // Security / Row-Level Scoping: Orang tua hanya boleh akses anak kandungnya
        if ($user && $user->role_id === 15 && strtolower($user->username) !== 'admin') {
            if ($pasien->id_orang_tua !== $user->user_profile_id) {
                return response()->json([
                    'message' => 'Akses ditolak: Anda tidak memiliki izin untuk melihat rekam medis pasien anak ini.'
                ], 403);
            }
        }

        // Catat Audit Log Pembacaan EMR (UU PDP & Permenkes 24/2022)
        $userId = auth('api')->id() ?? 1;
        EmrAuditLog::create([
            'user_id' => $userId,
            'profile_anak_id' => $profileAnakId,
            'action' => 'VIEW_TIMELINE',
            'alasan_akses' => 'Melihat timeline dan rekam medis pasien',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $records = EmrRecord::with([
            'terapis',
            'soap',
            'assessments',
            'addendums.user.userProfile',
            'attachments'
        ])
        ->where('profile_anak_id', $profileAnakId)
        ->orderBy('tanggal_pemeriksaan', 'desc')
        ->orderBy('id', 'desc')
        ->get();

        $goals = EmrGoalTracking::where('profile_anak_id', $profileAnakId)->get();

        return response()->json([
            'pasien' => $pasien,
            'timeline' => $records,
            'goals' => $goals,
        ]);
    }

    // ================= 2. STORE SOAP / SESI TERAPI =================
    public function storeSoap(Request $request)
    {
        $request->validate([
            'profile_anak_id' => 'required|exists:profile_anak,id',
            'tanggal_pemeriksaan' => 'required|date',
            'jenis_layanan' => 'required|string',
            'subjektif' => 'nullable|string',
            'objektif' => 'nullable|string',
            'asesmen' => 'nullable|string',
            'planning' => 'nullable|string',
            'target_sesi' => 'nullable|string',
            'aktivitas_terapi' => 'nullable|string',
            'respons_anak' => 'nullable|string',
            'rekomendasi_home_program' => 'nullable|string',
            'registrasi_anak_id' => 'nullable|exists:registrasi_anak,id',
            'pelayanan_terapi_anak_id' => 'nullable|exists:pelayanan_terapi_anak,id',
        ]);

        return DB::transaction(function () use ($request) {
            $user = auth('api')->user();
            $terapisId = $user?->user_profile_id;

            $today = Carbon::parse($request->tanggal_pemeriksaan)->format('Ymd');
            $countToday = EmrRecord::whereDate('created_at', now())->count() + 1;
            $noEmr = "EMR-{$today}-" . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $record = EmrRecord::create([
                'no_emr' => $noEmr,
                'profile_anak_id' => $request->profile_anak_id,
                'registrasi_anak_id' => $request->registrasi_anak_id,
                'terapis_id' => $terapisId,
                'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
                'jenis_layanan' => $request->jenis_layanan,
                'status' => 'draft',
            ]);

            $soap = EmrSoap::create([
                'emr_record_id' => $record->id,
                'subjektif' => $request->subjektif,
                'objektif' => $request->objektif,
                'asesmen' => $request->asesmen,
                'planning' => $request->planning,
                'target_sesi' => $request->target_sesi,
                'aktivitas_terapi' => $request->aktivitas_terapi,
                'respons_anak' => $request->respons_anak,
                'rekomendasi_home_program' => $request->rekomendasi_home_program,
            ]);

            // Jika ada sesi pelayanan terkait, tandai selesai
            if ($request->pelayanan_terapi_anak_id) {
                PelayananTerapiAnak::where('id', $request->pelayanan_terapi_anak_id)
                    ->update(['status' => 'selesai']);
            }

            EmrAuditLog::create([
                'user_id' => $user?->id ?? 1,
                'profile_anak_id' => $request->profile_anak_id,
                'emr_record_id' => $record->id,
                'action' => 'CREATE_SOAP',
                'details' => ['no_emr' => $noEmr, 'jenis_layanan' => $request->jenis_layanan],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return response()->json([
                'message' => 'Catatan SOAP berhasil disimpan',
                'emr_record' => $record->load('soap', 'terapis'),
            ], 201);
        });
    }

    // ================= 3. LOCK & DIGITAL SIGNATURE (FINAL RME) =================
    public function lockAndSign(Request $request, $id)
    {
        $record = EmrRecord::with('soap')->findOrFail($id);

        if ($record->status === 'final') {
            return response()->json(['message' => 'Rekam medis ini sudah berstatus Final dan terkunci.'], 422);
        }

        $user = auth('api')->user();

        // Buat hash integritas SHA-256 data rekam medis
        $payloadToHash = json_encode([
            'no_emr' => $record->no_emr,
            'pasien_id' => $record->profile_anak_id,
            'tanggal' => $record->tanggal_pemeriksaan,
            'soap' => $record->soap?->toArray(),
            'signed_by' => $user?->username,
            'timestamp' => now()->toIso8601String(),
        ]);
        $hash = hash('sha256', $payloadToHash);

        $record->update([
            'status' => 'final',
            'locked_at' => now(),
            'signed_by_id' => $user?->id,
            'sha256_hash' => $hash,
        ]);

        EmrAuditLog::create([
            'user_id' => $user?->id ?? 1,
            'profile_anak_id' => $record->profile_anak_id,
            'emr_record_id' => $record->id,
            'action' => 'LOCK_AND_SIGN',
            'details' => ['sha256_hash' => $hash],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json([
            'message' => 'Rekam Medis berhasil difinalisasi dan ditandatangani secara digital',
            'record' => $record,
            'hash' => $hash,
        ]);
    }

    // ================= 4. ADDENDUM REKAM MEDIS FINAL =================
    public function addAddendum(Request $request, $id)
    {
        $request->validate([
            'alasan_koreksi' => 'required|string|min:5',
            'catatan_addendum' => 'required|string|min:5',
        ]);

        $record = EmrRecord::with('soap')->findOrFail($id);
        $user = auth('api')->user();

        $addendum = EmrAddendum::create([
            'emr_record_id' => $record->id,
            'user_id' => $user?->id ?? 1,
            'alasan_koreksi' => $request->alasan_koreksi,
            'catatan_addendum' => $request->catatan_addendum,
            'data_sebelumnya' => $record->soap?->toArray(),
        ]);

        $record->update(['status' => 'amended']);

        EmrAuditLog::create([
            'user_id' => $user?->id ?? 1,
            'profile_anak_id' => $record->profile_anak_id,
            'emr_record_id' => $record->id,
            'action' => 'ADDENDUM',
            'details' => ['alasan' => $request->alasan_koreksi],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json([
            'message' => 'Addendum rekam medis berhasil ditambahkan',
            'addendum' => $addendum,
        ], 201);
    }

    // ================= 5. AUTOCOMPLETE ICD-10 & ICD-9 =================
    public function searchIcd10(Request $request)
    {
        $q = $request->query('q', '');
        $data = MasterIcd10::where('kode', 'ILIKE', "%{$q}%")
            ->orWhere('deskripsi_en', 'ILIKE', "%{$q}%")
            ->orWhere('deskripsi_id', 'ILIKE', "%{$q}%")
            ->limit(20)
            ->get();
        return response()->json($data);
    }

    public function searchIcd9(Request $request)
    {
        $q = $request->query('q', '');
        $data = MasterIcd9::where('kode', 'ILIKE', "%{$q}%")
            ->orWhere('deskripsi_en', 'ILIKE', "%{$q}%")
            ->orWhere('deskripsi_id', 'ILIKE', "%{$q}%")
            ->limit(20)
            ->get();
        return response()->json($data);
    }

    // ================= 6. GOAL TRACKING =================
    public function storeGoal(Request $request)
    {
        $request->validate([
            'profile_anak_id' => 'required|exists:profile_anak,id',
            'tipe_goal' => 'required|in:jangka_pendek,jangka_panjang',
            'judul_target' => 'required|string',
            'indikator_keberhasilan' => 'nullable|string',
            'baseline_persen' => 'nullable|integer|min:0|max:100',
            'capaian_persen' => 'nullable|integer|min:0|max:100',
            'tanggal_target' => 'nullable|date',
        ]);

        $goal = EmrGoalTracking::create($request->all());

        return response()->json([
            'message' => 'Target terapi berhasil ditambahkan',
            'goal' => $goal,
        ], 201);
    }

    public function updateGoalProgress(Request $request, $id)
    {
        $request->validate([
            'capaian_persen' => 'required|integer|min:0|max:100',
            'status' => 'nullable|string',
        ]);

        $goal = EmrGoalTracking::findOrFail($id);
        $goal->capaian_persen = $request->capaian_persen;
        if ($request->capaian_persen >= 100) {
            $goal->status = 'tercapai';
        } elseif ($request->filled('status')) {
            $goal->status = $request->status;
        }
        $goal->save();

        return response()->json([
            'message' => 'Progres target berhasil diperbarui',
            'goal' => $goal,
        ]);
    }
}
