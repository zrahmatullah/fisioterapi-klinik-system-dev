<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\MasterVitalParameter;
use App\Models\VitalReferenceRange;
use App\Models\PatientVitalRecord;
use App\Models\MasterAllergy;
use App\Models\PatientAllergy;
use App\Models\MasterSoapTemplate;
use App\Models\MasterProcedure;
use App\Models\MasterSigna;
use App\Models\MasterIntakeTime;
use App\Models\MasterUnit;
use App\Models\MasterAncillaryTest;
use App\Models\MasterProduk;
use App\Models\MasterIcd10;
use App\Models\MasterIcd9;
use App\Models\ProfileAnak;
use App\Models\RegistrasiAnak;
use App\Models\EmrRecord;
use App\Models\EmrSoap;
use App\Models\EResep;
use App\Models\EResepItem;
use App\Models\OrderPenunjang;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class ClinicalMasterController extends Controller
{
    // =========================================================================
    // 1. MASTER TTV & REFERENCE RANGES
    // =========================================================================

    public function getVitalParameters()
    {
        $parameters = MasterVitalParameter::with('referenceRanges')
            ->where('status_aktif', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $parameters,
        ]);
    }

    public function getPatientVitals(Request $request, $patientId)
    {
        $patient = ProfileAnak::findOrFail($patientId);

        // Compute age in months
        $ageMonths = 36; // Default fallback
        if ($patient->tanggal_lahir) {
            $birth = Carbon::parse($patient->tanggal_lahir);
            $ageMonths = max(0, $birth->diffInMonths(Carbon::now()));
        }

        // Latest vital record
        $vitalRecord = PatientVitalRecord::where('profile_anak_id', $patientId)
            ->orderBy('recorded_at', 'desc')
            ->first();

        // All parameters
        $parameters = MasterVitalParameter::with(['referenceRanges' => function ($q) use ($ageMonths) {
            $q->where('min_age_months', '<=', $ageMonths)
              ->where('max_age_months', '>=', $ageMonths);
        }])
        ->where('status_aktif', true)
        ->orderBy('sort_order', 'asc')
        ->get();

        // Calculate evaluation
        $evaluated = [];
        $overallStatus = 'normal';

        foreach ($parameters as $param) {
            $range = $param->referenceRanges->first();
            $val = null;

            if ($vitalRecord) {
                switch ($param->code) {
                    case 'TD':
                        $val = ($vitalRecord->td_sistole && $vitalRecord->td_diastole) 
                            ? "{$vitalRecord->td_sistole}/{$vitalRecord->td_diastole}" 
                            : null;
                        break;
                    case 'HR': $val = $vitalRecord->hr; break;
                    case 'TEMP': $val = $vitalRecord->temp; break;
                    case 'RR': $val = $vitalRecord->rr; break;
                    case 'SPO2': $val = $vitalRecord->spo2; break;
                    case 'BB': $val = $vitalRecord->bb; break;
                    case 'TB': $val = $vitalRecord->tb; break;
                    case 'LK': $val = $vitalRecord->lk; break;
                }
            }

            // Default fallback if no vital record yet
            if ($val === null) {
                switch ($param->code) {
                    case 'TD': $val = '120/80'; break;
                    case 'HR': $val = 82; break;
                    case 'TEMP': $val = 36.5; break;
                    case 'RR': $val = 18; break;
                    case 'SPO2': $val = 98; break;
                    case 'BB': $val = $patient->umur ? ($patient->umur * 2.5 + 8) : 15.0; break;
                    case 'TB': $val = 99.5; break;
                    case 'LK': $val = 49.8; break;
                }
            }

            $itemStatus = 'normal';
            if ($range && $val !== null) {
                $numVal = is_numeric($val) ? (float)$val : (float)explode('/', (string)$val)[0];
                if ($range->critical_low !== null && $numVal < $range->critical_low) {
                    $itemStatus = 'abnormal_kritis';
                    $overallStatus = 'abnormal_kritis';
                } elseif ($range->critical_high !== null && $numVal > $range->critical_high) {
                    $itemStatus = 'abnormal_kritis';
                    $overallStatus = 'abnormal_kritis';
                } elseif ($numVal < $range->min_value || $numVal > $range->max_value) {
                    $itemStatus = 'perlu_perhatian';
                    if ($overallStatus !== 'abnormal_kritis') {
                        $overallStatus = 'perlu_perhatian';
                    }
                }
            }

            $evaluated[] = [
                'parameter_id' => $param->id,
                'code' => $param->code,
                'name' => $param->name,
                'unit' => $param->unit,
                'is_core_ttv' => $param->is_core_ttv,
                'is_pediatric_growth' => $param->is_pediatric_growth,
                'value' => $val,
                'status' => $itemStatus,
                'reference_range' => $range ? [
                    'age_bracket' => $range->age_bracket,
                    'min_value' => $range->min_value,
                    'max_value' => $range->max_value,
                    'critical_low' => $range->critical_low,
                    'critical_high' => $range->critical_high,
                    'notes' => $range->notes,
                ] : null,
            ];
        }

        return response()->json([
            'patient_id' => (int)$patientId,
            'child_age_months' => $ageMonths,
            'overall_status' => $overallStatus,
            'recorded_at' => $vitalRecord ? $vitalRecord->recorded_at->toIso8601String() : now()->toIso8601String(),
            'vitals' => $evaluated,
            'raw_record' => $vitalRecord,
        ]);
    }

    public function storePatientVitals(Request $request, $patientId)
    {
        $patient = ProfileAnak::findOrFail($patientId);

        $validated = $request->validate([
            'registrasi_anak_id' => 'nullable|exists:registrasi_anak,id',
            'emr_record_id' => 'nullable|exists:emr_records,id',
            'td_sistole' => 'nullable|integer',
            'td_diastole' => 'nullable|integer',
            'hr' => 'nullable|numeric',
            'temp' => 'nullable|numeric',
            'rr' => 'nullable|numeric',
            'spo2' => 'nullable|numeric',
            'bb' => 'nullable|numeric',
            'tb' => 'nullable|numeric',
            'lk' => 'nullable|numeric',
            'catatan' => 'nullable|string',
        ]);

        $ageMonths = 36;
        if ($patient->tanggal_lahir) {
            $birth = Carbon::parse($patient->tanggal_lahir);
            $ageMonths = max(0, $birth->diffInMonths(Carbon::now()));
        }

        // Evaluate status
        $parameters = MasterVitalParameter::with(['referenceRanges' => function ($q) use ($ageMonths) {
            $q->where('min_age_months', '<=', $ageMonths)
              ->where('max_age_months', '>=', $ageMonths);
        }])->get();

        $overallStatus = 'normal';
        $structuredData = [];

        foreach ($parameters as $param) {
            $range = $param->referenceRanges->first();
            $val = null;
            switch ($param->code) {
                case 'TD':
                    $val = ($request->td_sistole && $request->td_diastole) 
                        ? "{$request->td_sistole}/{$request->td_diastole}" 
                        : null;
                    break;
                case 'HR': $val = $request->hr; break;
                case 'TEMP': $val = $request->temp; break;
                case 'RR': $val = $request->rr; break;
                case 'SPO2': $val = $request->spo2; break;
                case 'BB': $val = $request->bb; break;
                case 'TB': $val = $request->tb; break;
                case 'LK': $val = $request->lk; break;
            }

            $itemStatus = 'normal';
            if ($range && $val !== null) {
                $numVal = is_numeric($val) ? (float)$val : (float)explode('/', (string)$val)[0];
                if ($range->critical_low !== null && $numVal < $range->critical_low) {
                    $itemStatus = 'abnormal_kritis';
                    $overallStatus = 'abnormal_kritis';
                } elseif ($range->critical_high !== null && $numVal > $range->critical_high) {
                    $itemStatus = 'abnormal_kritis';
                    $overallStatus = 'abnormal_kritis';
                } elseif ($numVal < $range->min_value || $numVal > $range->max_value) {
                    $itemStatus = 'perlu_perhatian';
                    if ($overallStatus !== 'abnormal_kritis') {
                        $overallStatus = 'perlu_perhatian';
                    }
                }
            }

            if ($val !== null) {
                $structuredData[] = [
                    'code' => $param->code,
                    'name' => $param->name,
                    'value' => $val,
                    'unit' => $param->unit,
                    'status' => $itemStatus,
                ];
            }
        }

        $user = auth('api')->user();
        $recordedById = $user?->user_profile_id;
        if ($recordedById && !DB::table('user_profile')->where('id', $recordedById)->exists()) {
            $recordedById = null;
        }
        if (!$recordedById) {
            $recordedById = DB::table('user_profile')->value('id');
        }

        $record = PatientVitalRecord::create([
            'profile_anak_id' => $patientId,
            'registrasi_anak_id' => $request->registrasi_anak_id,
            'emr_record_id' => $request->emr_record_id,
            'recorded_at' => now(),
            'recorded_by_id' => $recordedById,
            'td_sistole' => $request->td_sistole,
            'td_diastole' => $request->td_diastole,
            'hr' => $request->hr,
            'temp' => $request->temp,
            'rr' => $request->rr,
            'spo2' => $request->spo2,
            'bb' => $request->bb,
            'tb' => $request->tb,
            'lk' => $request->lk,
            'vital_data' => $structuredData,
            'status' => $overallStatus,
            'catatan' => $request->catatan,
        ]);

        return response()->json([
            'message' => 'Data Tanda-Tanda Vital & Pertumbuhan berhasil disimpan',
            'status' => $overallStatus,
            'record' => $record,
        ], 201);
    }

    public function getPatientVitalsHistory($patientId)
    {
        $records = PatientVitalRecord::where('profile_anak_id', $patientId)
            ->orderBy('recorded_at', 'asc')
            ->take(15)
            ->get();

        $labels = [];
        $hrData = [];
        $rrData = [];
        $tempData = [];
        $spo2Data = [];
        $bbData = [];
        $tbData = [];

        foreach ($records as $r) {
            $dateLabel = Carbon::parse($r->recorded_at)->format('d M Y');
            $labels[] = $dateLabel;
            $hrData[] = $r->hr;
            $rrData[] = $r->rr;
            $tempData[] = $r->temp;
            $spo2Data[] = $r->spo2;
            $bbData[] = $r->bb;
            $tbData[] = $r->tb;
        }

        return response()->json([
            'patient_id' => (int)$patientId,
            'total_records' => $records->count(),
            'chart_data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Denyut Jantung (HR bpm)',
                        'data' => $hrData,
                        'borderColor' => '#0F766E',
                        'backgroundColor' => 'rgba(15, 118, 110, 0.1)',
                        'yAxisID' => 'y',
                    ],
                    [
                        'label' => 'Laju Napas (RR x/m)',
                        'data' => $rrData,
                        'borderColor' => '#0284C7',
                        'backgroundColor' => 'rgba(2, 132, 199, 0.1)',
                        'yAxisID' => 'y',
                    ],
                    [
                        'label' => 'Suhu (°C)',
                        'data' => $tempData,
                        'borderColor' => '#F59E0B',
                        'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                        'yAxisID' => 'yTemp',
                    ],
                    [
                        'label' => 'SpO2 (%)',
                        'data' => $spo2Data,
                        'borderColor' => '#10B981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'yAxisID' => 'y',
                    ],
                    [
                        'label' => 'Berat Badan (kg)',
                        'data' => $bbData,
                        'borderColor' => '#6366F1',
                        'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                        'yAxisID' => 'y',
                    ],
                ]
            ],
            'records' => $records,
        ]);
    }

    // =========================================================================
    // 2. MASTER ALERGI
    // =========================================================================

    public function getAllergies(Request $request)
    {
        $query = MasterAllergy::where('status_aktif', true);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('allergen_name', 'ILIKE', "%{$q}%")
                    ->orWhere('code', 'ILIKE', "%{$q}%")
                    ->orWhere('deskripsi', 'ILIKE', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->orderBy('category')->orderBy('allergen_name')->get());
    }

    public function getPatientAllergies($patientId)
    {
        $allergies = PatientAllergy::with('allergy')
            ->where('profile_anak_id', $patientId)
            ->where('is_active', true)
            ->get();

        return response()->json($allergies);
    }

    public function savePatientAllergies(Request $request, $patientId)
    {
        $patient = ProfileAnak::findOrFail($patientId);

        $validated = $request->validate([
            'allergy_ids' => 'required|array',
            'allergy_ids.*' => 'exists:master_allergies,id',
            'custom_reaction' => 'nullable|string',
            'severity' => 'nullable|string',
        ]);

        // Sync patient allergies
        PatientAllergy::where('profile_anak_id', $patientId)->delete();

        $names = [];
        foreach ($validated['allergy_ids'] as $algId) {
            $master = MasterAllergy::find($algId);
            if ($master) {
                $names[] = $master->allergen_name;
                PatientAllergy::create([
                    'profile_anak_id' => $patientId,
                    'allergy_id' => $algId,
                    'custom_reaction' => $request->custom_reaction,
                    'severity' => $request->severity ?? $master->severity_default,
                    'is_active' => true,
                ]);
            }
        }

        // Update profile_anak summary string
        $summary = count($names) > 0 ? implode(' & ', $names) : 'Tidak ada riwayat alergi';
        $patient->update(['catatan_alergi' => $summary]);

        return response()->json([
            'message' => 'Data alergi pasien berhasil diperbarui',
            'summary' => $summary,
            'allergies' => PatientAllergy::with('allergy')->where('profile_anak_id', $patientId)->get(),
        ]);
    }

    // =========================================================================
    // 3. MASTER TEMPLATE SOAP
    // =========================================================================

    public function getSoapTemplates(Request $request)
    {
        $query = MasterSoapTemplate::where('status_aktif', true);

        if ($request->filled('discipline')) {
            $query->where('discipline', 'ILIKE', "%{$request->discipline}%");
        }

        return response()->json($query->orderBy('discipline')->orderBy('title')->get());
    }

    // =========================================================================
    // 4. MASTER ICD-10 & ICD-9 PROCEDURES
    // =========================================================================

    public function getIcd10(Request $request)
    {
        $q = $request->query('q', '');
        $data = MasterIcd10::where(function ($sub) use ($q) {
            $sub->where('kode', 'ILIKE', "%{$q}%")
                ->orWhere('deskripsi_id', 'ILIKE', "%{$q}%")
                ->orWhere('deskripsi_en', 'ILIKE', "%{$q}%");
        })
        ->orderBy('kode')
        ->limit(30)
        ->get();

        return response()->json($data);
    }

    public function getProcedures(Request $request)
    {
        $query = MasterProcedure::where('status_aktif', true);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('procedure_name', 'ILIKE', "%{$q}%")
                    ->orWhere('icd9_code', 'ILIKE', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->orderBy('category')->orderBy('procedure_name')->get());
    }

    // =========================================================================
    // 5. MASTER FARMASI (OBAT, SIGNA, INTAKE TIME, UNITS)
    // =========================================================================

    public function getMedications(Request $request)
    {
        $query = MasterProduk::where('status_aktif', true);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_produk', 'ILIKE', "%{$q}%")
                    ->orWhere('nama_generik', 'ILIKE', "%{$q}%")
                    ->orWhere('kode_produk', 'ILIKE', "%{$q}%");
            });
        }

        // Return live stock & prices
        $meds = $query->orderBy('is_obat', 'desc')
            ->orderBy('nama_produk')
            ->limit(50)
            ->get(['id', 'kode_produk', 'nama_produk', 'nama_generik', 'jenis', 'satuan', 'total_stok', 'harga_jual', 'is_obat']);

        return response()->json($meds);
    }

    public function getUnits()
    {
        return response()->json(MasterUnit::where('status_aktif', true)->orderBy('name')->get());
    }

    public function getSigna()
    {
        return response()->json(MasterSigna::where('status_aktif', true)->orderBy('code')->get());
    }

    public function getIntakeTimes()
    {
        return response()->json(MasterIntakeTime::where('status_aktif', true)->orderBy('code')->get());
    }

    // =========================================================================
    // 6. MASTER PEMERIKSAAN PENUNJANG (LAB & RADIOLOGI)
    // =========================================================================

    public function getAncillaryTests(Request $request)
    {
        $query = MasterAncillaryTest::where('status_aktif', true);

        if ($request->filled('type')) {
            $type = strtolower($request->type);
            if ($type === 'lab') {
                $query->where('category', 'Laboratorium');
            } elseif ($type === 'radiologi') {
                $query->where('category', 'Radiologi');
            } elseif ($type === 'diagnostik') {
                $query->where('category', 'Diagnostik Khusus');
            }
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('test_name', 'ILIKE', "%{$q}%")
                    ->orWhere('code', 'ILIKE', "%{$q}%");
            });
        }

        return response()->json($query->orderBy('category')->orderBy('test_name')->get());
    }

    // =========================================================================
    // 7. ATOMIC EMR SAVE (TRANSACTIONAL INTEGRITY)
    // =========================================================================

    public function atomicEmrSave(Request $request)
    {
        $validated = $request->validate([
            'profile_anak_id' => 'required|exists:profile_anak,id',
            'registrasi_anak_id' => 'nullable|exists:registrasi_anak,id',
            'tanggal_pemeriksaan' => 'required|date',
            'jenis_layanan' => 'required|string',
            'subjektif' => 'nullable|string',
            'objektif' => 'nullable|string',
            'asesmen' => 'nullable|string',
            'planning' => 'nullable|string',
            'icd10_primary_id' => 'nullable|exists:master_icd10,id',
            'icd10_secondary_ids' => 'nullable|array',
            'vitals' => 'nullable|array',
            'allergy_ids' => 'nullable|array',
            'procedures' => 'nullable|array',
            'prescriptions' => 'nullable|array',
            'ancillary_orders' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($request) {
            $user = auth('api')->user();
            $defaultProfileId = DB::table('user_profile')->value('id');
            $profileId = $user?->user_profile_id;
            if (!$profileId || !DB::table('user_profile')->where('id', $profileId)->exists()) {
                $profileId = $defaultProfileId;
            }

            $regId = $request->registrasi_anak_id;
            if (!$regId) {
                $regId = DB::table('registrasi_anak')
                    ->where('profile_anak_id', $request->profile_anak_id)
                    ->latest('id')
                    ->value('id');
            }

            $today = Carbon::parse($request->tanggal_pemeriksaan)->format('Ymd');
            $countToday = EmrRecord::whereDate('created_at', now())->count() + 1;
            $noEmr = "EMR-{$today}-" . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            // 1. Create EMR Record
            $record = EmrRecord::create([
                'no_emr' => $noEmr,
                'profile_anak_id' => $request->profile_anak_id,
                'registrasi_anak_id' => $regId,
                'terapis_id' => $profileId,
                'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
                'jenis_layanan' => $request->jenis_layanan,
                'status' => 'final',
                'locked_at' => now(),
                'signed_by_id' => $profileId,
            ]);

            // 2. Create SOAP Note
            $soap = EmrSoap::create([
                'emr_record_id' => $record->id,
                'subjektif' => $request->subjektif,
                'objektif' => $request->objektif,
                'asesmen' => $request->asesmen,
                'planning' => $request->planning,
            ]);

            // 3. Save Vitals Record if provided
            if ($request->filled('vitals')) {
                $vitals = $request->vitals;
                PatientVitalRecord::create([
                    'profile_anak_id' => $request->profile_anak_id,
                    'registrasi_anak_id' => $regId,
                    'emr_record_id' => $record->id,
                    'recorded_at' => now(),
                    'recorded_by_id' => $profileId,
                    'td_sistole' => $vitals['td_sistole'] ?? null,
                    'td_diastole' => $vitals['td_diastole'] ?? null,
                    'hr' => $vitals['hr'] ?? null,
                    'temp' => $vitals['temp'] ?? null,
                    'rr' => $vitals['rr'] ?? null,
                    'spo2' => $vitals['spo2'] ?? null,
                    'bb' => $vitals['bb'] ?? null,
                    'tb' => $vitals['tb'] ?? null,
                    'lk' => $vitals['lk'] ?? null,
                    'status' => $vitals['status'] ?? 'normal',
                    'vital_data' => $vitals['structured'] ?? null,
                ]);
            }

            // 4. Save Allergies if provided
            if ($request->filled('allergy_ids') && is_array($request->allergy_ids)) {
                PatientAllergy::where('profile_anak_id', $request->profile_anak_id)->delete();
                $names = [];
                foreach ($request->allergy_ids as $algId) {
                    $alg = MasterAllergy::find($algId);
                    if ($alg) {
                        $names[] = $alg->allergen_name;
                        PatientAllergy::create([
                            'profile_anak_id' => $request->profile_anak_id,
                            'allergy_id' => $algId,
                            'severity' => $alg->severity_default,
                            'is_active' => true,
                        ]);
                    }
                }
                if (count($names) > 0) {
                    ProfileAnak::where('id', $request->profile_anak_id)
                        ->update(['catatan_alergi' => implode(' & ', $names)]);
                }
            }

            // 5. Save Prescriptions (E-Resep) if provided
            $resepCreated = null;
            if ($request->filled('prescriptions') && count($request->prescriptions) > 0) {
                $countResep = EResep::whereDate('created_at', now())->count() + 1;
                $noResep = "RSP-{$today}-" . str_pad($countResep, 4, '0', STR_PAD_LEFT);

                $resepCreated = EResep::create([
                    'nomor_resep' => $noResep,
                    'registrasi_anak_id' => $regId,
                    'profile_anak_id' => $request->profile_anak_id,
                    'dokter_id' => $profileId,
                    'status' => 'menunggu_telaah',
                    'catatan' => 'Resep elektronik dari integrasi RME klinis',
                ]);

                foreach ($request->prescriptions as $rx) {
                    $qty = $rx['qty'] ?? 1;
                    $harga = $rx['harga_satuan'] ?? 30000;
                    EResepItem::create([
                        'e_resep_id' => $resepCreated->id,
                        'master_produk_id' => $rx['medication_id'] ?? null,
                        'nama_obat' => $rx['nama_obat'],
                        'jumlah' => $qty,
                        'satuan' => $rx['satuan'] ?? 'btl',
                        'signa' => $rx['signa'] ?? '3 x 1 cth (5 ml)',
                        'waktu_minum' => $rx['waktu_minum'] ?? 'Sesudah Makan (p.c.)',
                        'catatan_racikan' => $rx['catatan_racikan'] ?? null,
                        'harga_satuan' => $harga,
                        'subtotal' => $qty * $harga,
                    ]);
                }
            }

            // 6. Save Ancillary Orders (Lab & Radiologi) if provided
            $ordersCreated = [];
            if ($request->filled('ancillary_orders') && count($request->ancillary_orders) > 0) {
                foreach ($request->ancillary_orders as $ord) {
                    $countOrd = OrderPenunjang::whereDate('created_at', now())->count() + 1;
                    $noOrd = "ORD-{$today}-" . str_pad($countOrd, 4, '0', STR_PAD_LEFT);

                    $ordersCreated[] = OrderPenunjang::create([
                        'nomor_order' => $noOrd,
                        'registrasi_anak_id' => $regId,
                        'profile_anak_id' => $request->profile_anak_id,
                        'dokter_id' => $profileId,
                        'jenis_order' => $ord['jenis_order'] ?? 'laboratorium',
                        'nama_tindakan' => $ord['nama_tindakan'],
                        'catatan_klinis' => $ord['catatan_klinis'] ?? 'Pemeriksaan penunjang terintegrasi RME',
                        'tarif' => $ord['tarif'] ?? 150000,
                        'status' => 'menunggu_verifikasi',
                    ]);
                }
            }

            return response()->json([
                'message' => 'Rekam Medis Elektronik (RME) & seluruh parameter klinis berhasil disimpan secara atomik!',
                'emr_record' => $record->load('soap'),
                'e_resep' => $resepCreated,
                'ancillary_orders' => $ordersCreated,
            ], 201);
        });
    }
}
