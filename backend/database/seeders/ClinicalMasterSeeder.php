<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MasterProduk;
use Carbon\Carbon;

class ClinicalMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. MASTER VITAL PARAMETERS
        $vitals = [
            ['code' => 'TD', 'name' => 'Tekanan Darah', 'unit' => 'mmHg', 'is_core_ttv' => true, 'is_pediatric_growth' => false, 'sort_order' => 1],
            ['code' => 'HR', 'name' => 'Denyut Jantung / Nadi', 'unit' => 'bpm', 'is_core_ttv' => true, 'is_pediatric_growth' => false, 'sort_order' => 2],
            ['code' => 'TEMP', 'name' => 'Suhu Tubuh', 'unit' => '°C', 'is_core_ttv' => true, 'is_pediatric_growth' => false, 'sort_order' => 3],
            ['code' => 'RR', 'name' => 'Laju Pernapasan', 'unit' => 'x/m', 'is_core_ttv' => true, 'is_pediatric_growth' => false, 'sort_order' => 4],
            ['code' => 'SPO2', 'name' => 'Saturasi Oksigen', 'unit' => '%', 'is_core_ttv' => true, 'is_pediatric_growth' => false, 'sort_order' => 5],
            ['code' => 'BB', 'name' => 'Berat Badan', 'unit' => 'kg', 'is_core_ttv' => false, 'is_pediatric_growth' => true, 'sort_order' => 6],
            ['code' => 'TB', 'name' => 'Tinggi / Panjang Badan', 'unit' => 'cm', 'is_core_ttv' => false, 'is_pediatric_growth' => true, 'sort_order' => 7],
            ['code' => 'LK', 'name' => 'Lingkar Kepala', 'unit' => 'cm', 'is_core_ttv' => false, 'is_pediatric_growth' => true, 'sort_order' => 8],
        ];

        foreach ($vitals as $v) {
            DB::table('master_vital_parameters')->updateOrInsert(
                ['code' => $v['code']],
                array_merge($v, ['status_aktif' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // Get param IDs
        $paramMap = DB::table('master_vital_parameters')->pluck('id', 'code')->toArray();

        // 2. VITAL REFERENCE RANGES (AGE-ADJUSTED PEDIATRIC GUIDELINES)
        $ranges = [
            // 0 - 12 Bulan (Bayi)
            ['code' => 'HR', 'age_bracket' => '0–12 Bulan', 'min_age' => 0, 'max_age' => 12, 'min' => 80, 'max' => 160, 'crit_low' => 70, 'crit_high' => 180, 'notes' => 'Normal bayi usia 0-12 bln'],
            ['code' => 'TEMP', 'age_bracket' => '0–12 Bulan', 'min_age' => 0, 'max_age' => 12, 'min' => 36.5, 'max' => 37.5, 'crit_low' => 35.5, 'crit_high' => 38.5, 'notes' => 'Suhu aksila bayi'],
            ['code' => 'RR', 'age_bracket' => '0–12 Bulan', 'min_age' => 0, 'max_age' => 12, 'min' => 30, 'max' => 60, 'crit_low' => 24, 'crit_high' => 70, 'notes' => 'Laju napas bayi'],
            ['code' => 'SPO2', 'age_bracket' => '0–12 Bulan', 'min_age' => 0, 'max_age' => 12, 'min' => 95, 'max' => 100, 'crit_low' => 92, 'crit_high' => 100, 'notes' => 'SpO2 room air'],
            ['code' => 'TD', 'age_bracket' => '0–12 Bulan', 'min_age' => 0, 'max_age' => 12, 'min' => 75, 'max' => 100, 'crit_low' => 60, 'crit_high' => 110, 'notes' => 'Sistole 75-100 mmHg'],
            ['code' => 'BB', 'age_bracket' => '0–12 Bulan', 'min_age' => 0, 'max_age' => 12, 'min' => 3.0, 'max' => 11.5, 'crit_low' => 2.5, 'crit_high' => 13.0, 'notes' => 'Kurva WHO 0-12 bln'],
            ['code' => 'TB', 'age_bracket' => '0–12 Bulan', 'min_age' => 0, 'max_age' => 12, 'min' => 48, 'max' => 78, 'crit_low' => 45, 'crit_high' => 82, 'notes' => 'Panjang badan cm'],
            ['code' => 'LK', 'age_bracket' => '0–12 Bulan', 'min_age' => 0, 'max_age' => 12, 'min' => 33, 'max' => 47, 'crit_low' => 31, 'crit_high' => 49, 'notes' => 'Lingkar kepala cm'],

            // 1 - 3 Tahun (Batita)
            ['code' => 'HR', 'age_bracket' => '1–3 Tahun', 'min_age' => 13, 'max_age' => 36, 'min' => 80, 'max' => 140, 'crit_low' => 65, 'crit_high' => 160, 'notes' => 'Normal batita 1-3 thn'],
            ['code' => 'TEMP', 'age_bracket' => '1–3 Tahun', 'min_age' => 13, 'max_age' => 36, 'min' => 36.5, 'max' => 37.5, 'crit_low' => 35.5, 'crit_high' => 38.5, 'notes' => 'Suhu aksila'],
            ['code' => 'RR', 'age_bracket' => '1–3 Tahun', 'min_age' => 13, 'max_age' => 36, 'min' => 24, 'max' => 40, 'crit_low' => 18, 'crit_high' => 50, 'notes' => 'Laju napas'],
            ['code' => 'SPO2', 'age_bracket' => '1–3 Tahun', 'min_age' => 13, 'max_age' => 36, 'min' => 95, 'max' => 100, 'crit_low' => 92, 'crit_high' => 100, 'notes' => 'SpO2'],
            ['code' => 'TD', 'age_bracket' => '1–3 Tahun', 'min_age' => 13, 'max_age' => 36, 'min' => 80, 'max' => 110, 'crit_low' => 70, 'crit_high' => 120, 'notes' => 'Sistole mmHg'],
            ['code' => 'BB', 'age_bracket' => '1–3 Tahun', 'min_age' => 13, 'max_age' => 36, 'min' => 9.0, 'max' => 16.5, 'crit_low' => 7.5, 'crit_high' => 19.0, 'notes' => 'BB kg'],
            ['code' => 'TB', 'age_bracket' => '1–3 Tahun', 'min_age' => 13, 'max_age' => 36, 'min' => 75, 'max' => 100, 'crit_low' => 70, 'crit_high' => 106, 'notes' => 'TB cm'],
            ['code' => 'LK', 'age_bracket' => '1–3 Tahun', 'min_age' => 13, 'max_age' => 36, 'min' => 46, 'max' => 50, 'crit_low' => 44, 'crit_high' => 52, 'notes' => 'LK cm'],

            // 3 - 6 Tahun (Prasekolah)
            ['code' => 'HR', 'age_bracket' => '3–6 Tahun', 'min_age' => 37, 'max_age' => 72, 'min' => 70, 'max' => 120, 'crit_low' => 60, 'crit_high' => 140, 'notes' => 'Normal prasekolah'],
            ['code' => 'TEMP', 'age_bracket' => '3–6 Tahun', 'min_age' => 37, 'max_age' => 72, 'min' => 36.5, 'max' => 37.5, 'crit_low' => 35.5, 'crit_high' => 38.5, 'notes' => 'Suhu aksila'],
            ['code' => 'RR', 'age_bracket' => '3–6 Tahun', 'min_age' => 37, 'max_age' => 72, 'min' => 20, 'max' => 30, 'crit_low' => 16, 'crit_high' => 40, 'notes' => 'Laju napas'],
            ['code' => 'SPO2', 'age_bracket' => '3–6 Tahun', 'min_age' => 37, 'max_age' => 72, 'min' => 95, 'max' => 100, 'crit_low' => 93, 'crit_high' => 100, 'notes' => 'SpO2'],
            ['code' => 'TD', 'age_bracket' => '3–6 Tahun', 'min_age' => 37, 'max_age' => 72, 'min' => 90, 'max' => 115, 'crit_low' => 75, 'crit_high' => 125, 'notes' => 'Sistole mmHg'],
            ['code' => 'BB', 'age_bracket' => '3–6 Tahun', 'min_age' => 37, 'max_age' => 72, 'min' => 12.0, 'max' => 23.0, 'crit_low' => 10.0, 'crit_high' => 28.0, 'notes' => 'BB kg'],
            ['code' => 'TB', 'age_bracket' => '3–6 Tahun', 'min_age' => 37, 'max_age' => 72, 'min' => 95, 'max' => 120, 'crit_low' => 88, 'crit_high' => 128, 'notes' => 'TB cm'],
            ['code' => 'LK', 'age_bracket' => '3–6 Tahun', 'min_age' => 37, 'max_age' => 72, 'min' => 49, 'max' => 52, 'crit_low' => 47, 'crit_high' => 54, 'notes' => 'LK cm'],

            // 6 - 12 Tahun (Usia Sekolah)
            ['code' => 'HR', 'age_bracket' => '6–12 Tahun', 'min_age' => 73, 'max_age' => 144, 'min' => 65, 'max' => 110, 'crit_low' => 55, 'crit_high' => 130, 'notes' => 'Normal usia sekolah'],
            ['code' => 'TEMP', 'age_bracket' => '6–12 Tahun', 'min_age' => 73, 'max_age' => 144, 'min' => 36.5, 'max' => 37.5, 'crit_low' => 35.5, 'crit_high' => 38.5, 'notes' => 'Suhu aksila'],
            ['code' => 'RR', 'age_bracket' => '6–12 Tahun', 'min_age' => 73, 'max_age' => 144, 'min' => 16, 'max' => 24, 'crit_low' => 12, 'crit_high' => 32, 'notes' => 'Laju napas'],
            ['code' => 'SPO2', 'age_bracket' => '6–12 Tahun', 'min_age' => 73, 'max_age' => 144, 'min' => 95, 'max' => 100, 'crit_low' => 93, 'crit_high' => 100, 'notes' => 'SpO2'],
            ['code' => 'TD', 'age_bracket' => '6–12 Tahun', 'min_age' => 73, 'max_age' => 144, 'min' => 95, 'max' => 120, 'crit_low' => 80, 'crit_high' => 135, 'notes' => 'Sistole mmHg'],
            ['code' => 'BB', 'age_bracket' => '6–12 Tahun', 'min_age' => 73, 'max_age' => 144, 'min' => 18.0, 'max' => 45.0, 'crit_low' => 15.0, 'crit_high' => 55.0, 'notes' => 'BB kg'],
            ['code' => 'TB', 'age_bracket' => '6–12 Tahun', 'min_age' => 73, 'max_age' => 144, 'min' => 115, 'max' => 150, 'crit_low' => 105, 'crit_high' => 160, 'notes' => 'TB cm'],
            ['code' => 'LK', 'age_bracket' => '6–12 Tahun', 'min_age' => 73, 'max_age' => 144, 'min' => 51, 'max' => 54, 'crit_low' => 49, 'crit_high' => 56, 'notes' => 'LK cm'],

            // > 12 Tahun (Remaja)
            ['code' => 'HR', 'age_bracket' => '>12 Tahun', 'min_age' => 145, 'max_age' => 240, 'min' => 60, 'max' => 100, 'crit_low' => 50, 'crit_high' => 120, 'notes' => 'Normal remaja'],
            ['code' => 'TEMP', 'age_bracket' => '>12 Tahun', 'min_age' => 145, 'max_age' => 240, 'min' => 36.5, 'max' => 37.5, 'crit_low' => 35.5, 'crit_high' => 38.5, 'notes' => 'Suhu aksila'],
            ['code' => 'RR', 'age_bracket' => '>12 Tahun', 'min_age' => 145, 'max_age' => 240, 'min' => 12, 'max' => 20, 'crit_low' => 10, 'crit_high' => 28, 'notes' => 'Laju napas'],
            ['code' => 'SPO2', 'age_bracket' => '>12 Tahun', 'min_age' => 145, 'max_age' => 240, 'min' => 95, 'max' => 100, 'crit_low' => 93, 'crit_high' => 100, 'notes' => 'SpO2'],
            ['code' => 'TD', 'age_bracket' => '>12 Tahun', 'min_age' => 145, 'max_age' => 240, 'min' => 100, 'max' => 125, 'crit_low' => 85, 'crit_high' => 140, 'notes' => 'Sistole mmHg'],
            ['code' => 'BB', 'age_bracket' => '>12 Tahun', 'min_age' => 145, 'max_age' => 240, 'min' => 35.0, 'max' => 70.0, 'crit_low' => 30.0, 'crit_high' => 90.0, 'notes' => 'BB kg'],
            ['code' => 'TB', 'age_bracket' => '>12 Tahun', 'min_age' => 145, 'max_age' => 240, 'min' => 140, 'max' => 175, 'crit_low' => 130, 'crit_high' => 188, 'notes' => 'TB cm'],
            ['code' => 'LK', 'age_bracket' => '>12 Tahun', 'min_age' => 145, 'max_age' => 240, 'min' => 52, 'max' => 56, 'crit_low' => 50, 'crit_high' => 58, 'notes' => 'LK cm'],
        ];

        DB::table('vital_reference_ranges')->truncate();
        foreach ($ranges as $r) {
            $paramId = $paramMap[$r['code']] ?? null;
            if ($paramId) {
                DB::table('vital_reference_ranges')->insert([
                    'vital_parameter_id' => $paramId,
                    'age_bracket' => $r['age_bracket'],
                    'min_age_months' => $r['min_age'],
                    'max_age_months' => $r['max_age'],
                    'min_value' => $r['min'],
                    'max_value' => $r['max'],
                    'critical_low' => $r['crit_low'],
                    'critical_high' => $r['crit_high'],
                    'notes' => $r['notes'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. MASTER ALLERGIES
        $allergies = [
            ['code' => 'ALG-DRG-01', 'category' => 'Obat', 'allergen_name' => 'Amoxicillin', 'severity_default' => 'Sedang', 'deskripsi' => 'Antibiotik golongan penisilin'],
            ['code' => 'ALG-DRG-02', 'category' => 'Obat', 'allergen_name' => 'Penicillin & Ampicillin', 'severity_default' => 'Berat', 'deskripsi' => 'Reaksi anafilaksis penisilin'],
            ['code' => 'ALG-DRG-03', 'category' => 'Obat', 'allergen_name' => 'Paracetamol', 'severity_default' => 'Ringan', 'deskripsi' => 'Antipiretik & analgesik'],
            ['code' => 'ALG-DRG-04', 'category' => 'Obat', 'allergen_name' => 'Ibuprofen & NSAID', 'severity_default' => 'Sedang', 'deskripsi' => 'Antiinflamasi non-steroid'],
            ['code' => 'ALG-DRG-05', 'category' => 'Obat', 'allergen_name' => 'Cetirizine', 'severity_default' => 'Ringan', 'deskripsi' => 'Antihistamin generasi ke-2'],
            ['code' => 'ALG-FOD-01', 'category' => 'Makanan', 'allergen_name' => 'Susu Sapi (Laktosa)', 'severity_default' => 'Sedang', 'deskripsi' => 'Intoleransi laktosa / alergi protein susu sapi (CMPA)'],
            ['code' => 'ALG-FOD-02', 'category' => 'Makanan', 'allergen_name' => 'Telur Ayam', 'severity_default' => 'Sedang', 'deskripsi' => 'Alergi putih / kuning telur'],
            ['code' => 'ALG-FOD-03', 'category' => 'Makanan', 'allergen_name' => 'Kacang Tanah (Peanut)', 'severity_default' => 'Berat', 'deskripsi' => 'Reaksi hipersensitivitas kacang'],
            ['code' => 'ALG-FOD-04', 'category' => 'Makanan', 'allergen_name' => 'Makanan Laut (Seafood & Udang)', 'severity_default' => 'Berat', 'deskripsi' => 'Urtikaria & angioedema'],
            ['code' => 'ALG-FOD-05', 'category' => 'Makanan', 'allergen_name' => 'Gandum & Gluten', 'severity_default' => 'Sedang', 'deskripsi' => 'Celiac / gluten sensitivity'],
            ['code' => 'ALG-ENV-01', 'category' => 'Lingkungan', 'allergen_name' => 'Debu Rumah & Tungau', 'severity_default' => 'Ringan', 'deskripsi' => 'Rinitis alergi & bersin'],
            ['code' => 'ALG-ENV-02', 'category' => 'Lingkungan', 'allergen_name' => 'Suhu Dingin / Cuaca', 'severity_default' => 'Ringan', 'deskripsi' => 'Cold urticaria'],
            ['code' => 'ALG-ENV-03', 'category' => 'Lingkungan', 'allergen_name' => 'Bulu Kucing / Hewan', 'severity_default' => 'Sedang', 'deskripsi' => 'Dander hewan peliharaan'],
            ['code' => 'ALG-ENV-04', 'category' => 'Lingkungan', 'allergen_name' => 'Lateks Medis', 'severity_default' => 'Berat', 'deskripsi' => 'Sarung tangan lateks'],
        ];

        foreach ($allergies as $alg) {
            DB::table('master_allergies')->updateOrInsert(
                ['code' => $alg['code']],
                array_merge($alg, ['status_aktif' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // 4. MASTER SOAP TEMPLATES
        $soapTemplates = [
            [
                'title' => 'Terapi Wicara: Keterlambatan Bicara (Speech Delay F80.1)',
                'discipline' => 'Terapi Wicara',
                'subjective_template' => 'Anak datang diantar orang tua dalam kondisi kooperatif. Orang tua melaporkan anak mulai mencoba menirukan suara vokal baru di rumah, namun artikulasi konsonan /r/ dan /s/ masih belum jelas. Tidak ada riwayat demam atau infeksi telinga.',
                'objective_template' => 'Pemeriksaan oral-motor: tonus lidah baik, mobilitas bibir aktif. Kontak mata bertahan 5–8 detik dengan stimulasi kartu kata. Mampu menirukan 8 dari 10 kata bersuku dua (C-V-C-V) seperti "buku", "mama", "bola". Respons auditori terhadap perintah sederhana 2 tahap tercapai 80%.',
                'assessment_template' => 'Gangguan Perkembangan Artikulasi & Fonologis (ICD-10: F80.1). Menunjukkan peningkatan respon vokal 25% dibanding evaluasi awal. Oral-motor praksis stabil.',
                'planning_template' => '1. Latihan repetisi fonem alveolar /t/, /d/, dan bilabial /p/, /b/ (3 set x 10 repetisi).\n2. Stimulasi tiup gelembung dan sedotan untuk kekuatan otot labial.\n3. Home Program: Membaca buku bergambar 15 menit/hari dengan metode dialog interaktif, no gadget.'
            ],
            [
                'title' => 'Okupasi Terapi: Motorik Halus & Kemandirian (ADL F82)',
                'discipline' => 'Okupasi Terapi',
                'subjective_template' => 'Pasien anak datang ceria. Keluhan orang tua: anak masih kesulitan memegang pensil dengan tripod grasp dan sering menolak saat memakai sepatu atau mengancingkan baju sendiri.',
                'objective_template' => 'Grip strength tangan kanan 4.5 kg, kiri 4.2 kg. Pincer grasp sudah terbentuk namun belum stabil. Mampu meronce 5 manik-manik besar dalam 2 menit. Koordinasi visual motorik (VMI) setara usia 4 tahun.',
                'assessment_template' => 'Gangguan Perkembangan Koordinasi Motorik Halus (ICD-10: F82). Peningkatan stabilitas bahu dan manipulasi objek tangan bilateral.',
                'planning_template' => '1. Latihan penguatan intrinsic hand muscles menggunakan playdough dan jepitan jemuran.\n2. Latihan mengancingkan kancing besar (5 kancing) dan mengikat tali.\n3. Home Program: Aktivitas mengupas buah lunak dan meremas spons busa saat mandi.'
            ],
            [
                'title' => 'Sensori Integrasi: Hipersensitivitas Taktil & Vestibular (F88)',
                'discipline' => 'Sensori Integrasi',
                'subjective_template' => 'Anak mudah rewel saat berada di lingkungan bising dan menolak berjalan di rumput tanpa alas kaki. Sering mencari gerakan berputar (spinning) atau melompat berlebihan.',
                'objective_template' => 'Uji sensori taktil: defensiveness sedang pada area telapak kaki dan tangan. Vestibular: toleransi rotasi tinggi (tidak pusing setelah 10 putaran pada swing). Proprioceptif: regulasi postural meningkat setelah deep pressure 5 menit.',
                'assessment_template' => 'Gangguan Integrasi Sensori - Kombinasi Sensory Seeking Vestibular & Taktil Defensiveness (ICD-10: F88). Disregulasi modulasi sensorik.',
                'planning_template' => '1. Deep touch pressure menggunakan gym ball dan compression vest 2kg.\n2. Terapi ayunan linear vestibular terkontrol (suspended swing).\n3. Desensitisasi taktil dengan tekstur pasir kinetik dan busa shaving cream bertahap.\n4. Home Program: Sensory diet 20 menit sebelum tidur (heavy work activities).'
            ],
            [
                'title' => 'Fisioterapi Pediatrik: Gross Motor & Postur (G80 / F82)',
                'discipline' => 'Fisioterapi',
                'subjective_template' => 'Orang tua mengeluhkan anak sering tersandung saat berlari dan pola jalan jinjit (toe walking) saat tidak memakai sepatu. Tidak ada nyeri sendi.',
                'objective_template' => 'ROM pergelangan kaki: dorsifleksi pasif 5° (tightness tendo Achilles bilateral). Gross Motor Function Measure (GMFM) dimensi E (berlari & melompat): 72%. Keseimbangan satu kaki: kanan 4 detik, kiri 3 detik.',
                'assessment_template' => 'Pola Jalan Jinjit Fungsional & Keterlambatan Motorik Kasar (ICD-10: F82). Tightness bilateral tendo Achilles.',
                'planning_template' => '1. Passive & active stretching tendo Achilles bilateral (3 x 30 detik).\n2. Latihan berjalan di atas balance beam dan rintangan tangga busa.\n3. Latihan penguatan otot core stability di atas swiss ball.\n4. Home Program: Berdiri di atas papan miring (wedge board) saat menonton edukasi.'
            ],
            [
                'title' => 'Dokter Anak / KFR: Asesmen Tumbuh Kembang Komprehensif',
                'discipline' => 'Dokter Anak / KFR',
                'subjective_template' => 'Pasien anak datang kontrol rutin tumbuh kembang. Riwayat lahir cukup bulan (38 minggu), BBL 3100 gram, menangis langsung. Tidak ada riwayat kejang demam. Nafsu makan baik, pola tidur teratur.',
                'objective_template' => 'Status Gizi: BB/U normal, TB/U normal, LK dalam kurva normal WHO (Normocephaly). TTV: TD 100/65 mmHg, HR 88 bpm, Suhu 36.6 °C, RR 20 x/m, SpO2 99%. Pemeriksaan neurologis kranial I-XII normal, refleks fisiologis (+/+), refleks patologis (-/-).',
                'assessment_template' => 'Perkembangan Motorik Sesuai Usia, Gangguan Bahasa Ekspresif Ringan (ICD-10: F80.1). Status gizi baik.',
                'planning_template' => '1. Rekomendasi Terapi Wicara 2x seminggu x 8 sesi.\n2. Suplementasi multivitamin perkembangan otak (DHA, Curcuma, Zinc).\n3. Evaluasi ulang milestone tumbuh kembang dalam 2 bulan.'
            ]
        ];

        foreach ($soapTemplates as $st) {
            DB::table('master_soap_templates')->updateOrInsert(
                ['title' => $st['title']],
                array_merge($st, ['status_aktif' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // 5. MASTER PROCEDURES (ICD-9-CM & THERAPY PROCEDURES)
        $procedures = [
            ['icd9_code' => '93.75', 'procedure_name' => 'Terapi Wicara & Bahasa (Speech-Language Therapy)', 'category' => 'Terapi Wicara', 'tariff_umum' => 150000, 'tariff_asuransi' => 150000],
            ['icd9_code' => '93.73', 'procedure_name' => 'Terapi Oral Motorik & Latihan Menelan (Feeding Therapy)', 'category' => 'Terapi Wicara', 'tariff_umum' => 175000, 'tariff_asuransi' => 175000],
            ['icd9_code' => '93.83', 'procedure_name' => 'Okupasi Terapi Terarah (Occupational Therapy ADL)', 'category' => 'Okupasi Terapi', 'tariff_umum' => 150000, 'tariff_asuransi' => 150000],
            ['icd9_code' => '93.11', 'procedure_name' => 'Fisioterapi & Latihan Sensori Integrasi', 'category' => 'Sensori Integrasi', 'tariff_umum' => 160000, 'tariff_asuransi' => 160000],
            ['icd9_code' => '93.12', 'procedure_name' => 'Fisioterapi Pediatrik Motorik Kasar & Keseimbangan', 'category' => 'Fisioterapi', 'tariff_umum' => 150000, 'tariff_asuransi' => 150000],
            ['icd9_code' => '89.03', 'procedure_name' => 'Konsultasi & Pemeriksaan Dokter Spesialis Anak / KFR', 'category' => 'Medis / Dokter', 'tariff_umum' => 250000, 'tariff_asuransi' => 250000],
            ['icd9_code' => '93.01', 'procedure_name' => 'Evaluasi Fungsional & Asesmen Tumbuh Kembang Komprehensif', 'category' => 'Medis / Dokter', 'tariff_umum' => 200000, 'tariff_asuransi' => 200000],
            ['icd9_code' => '94.02', 'procedure_name' => 'Tes Psikologi Pediatrik & Skrining Kognitif', 'category' => 'Psikologi', 'tariff_umum' => 220000, 'tariff_asuransi' => 220000],
            ['icd9_code' => '93.89', 'procedure_name' => 'Terapi Snoezelen / Relaksasi Multi-Sensori', 'category' => 'Sensori Integrasi', 'tariff_umum' => 180000, 'tariff_asuransi' => 180000],
        ];

        foreach ($procedures as $prc) {
            DB::table('master_procedures')->updateOrInsert(
                ['icd9_code' => $prc['icd9_code']],
                array_merge($prc, ['status_aktif' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // 6. MASTER SIGNA
        $signas = [
            ['code' => 'SIG-01', 'signa_text' => '3 x 0.6 ml', 'times_per_day' => 3, 'dose_qty' => 0.6, 'unit' => 'drop'],
            ['code' => 'SIG-02', 'signa_text' => '3 x 1 cth (5 ml)', 'times_per_day' => 3, 'dose_qty' => 1.0, 'unit' => 'cth'],
            ['code' => 'SIG-03', 'signa_text' => '2 x 1 cth (5 ml)', 'times_per_day' => 2, 'dose_qty' => 1.0, 'unit' => 'cth'],
            ['code' => 'SIG-04', 'signa_text' => '1 x 1 cth (5 ml)', 'times_per_day' => 1, 'dose_qty' => 1.0, 'unit' => 'cth'],
            ['code' => 'SIG-05', 'signa_text' => '3 x 0.5 cth (2.5 ml)', 'times_per_day' => 3, 'dose_qty' => 0.5, 'unit' => 'cth'],
            ['code' => 'SIG-06', 'signa_text' => '1 x 1 btl', 'times_per_day' => 1, 'dose_qty' => 1.0, 'unit' => 'btl'],
            ['code' => 'SIG-07', 'signa_text' => '3 x 1 pulv (puyer)', 'times_per_day' => 3, 'dose_qty' => 1.0, 'unit' => 'pulv'],
            ['code' => 'SIG-08', 'signa_text' => '2 x 1 tab kunyah', 'times_per_day' => 2, 'dose_qty' => 1.0, 'unit' => 'tab'],
            ['code' => 'SIG-09', 'signa_text' => '1 x 5 tetes (drop)', 'times_per_day' => 1, 'dose_qty' => 5.0, 'unit' => 'drop'],
            ['code' => 'SIG-10', 'signa_text' => '3 x 1 sachet', 'times_per_day' => 3, 'dose_qty' => 1.0, 'unit' => 'sachet'],
        ];

        foreach ($signas as $sig) {
            DB::table('master_signa')->updateOrInsert(
                ['code' => $sig['code']],
                array_merge($sig, ['status_aktif' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // 7. MASTER INTAKE TIMES
        $intakes = [
            ['code' => 'INT-01', 'intake_time_text' => 'Sesudah Makan (p.c.)', 'latin_abbr' => 'p.c.'],
            ['code' => 'INT-02', 'intake_time_text' => 'Sebelum Makan (a.c.)', 'latin_abbr' => 'a.c.'],
            ['code' => 'INT-03', 'intake_time_text' => 'Bersama Makan (d.c.)', 'latin_abbr' => 'd.c.'],
            ['code' => 'INT-04', 'intake_time_text' => 'Malam Sebelum Tidur (h.s.)', 'latin_abbr' => 'h.s.'],
            ['code' => 'INT-05', 'intake_time_text' => 'Bila Demam / Nyeri (p.r.n.)', 'latin_abbr' => 'p.r.n.'],
            ['code' => 'INT-06', 'intake_time_text' => 'Pagi Hari Saat Perut Kosong', 'latin_abbr' => 'mane'],
        ];

        foreach ($intakes as $it) {
            DB::table('master_intake_times')->updateOrInsert(
                ['code' => $it['code']],
                array_merge($it, ['status_aktif' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // 8. MASTER UNITS
        $units = [
            ['code' => 'btl', 'name' => 'Botol (btl)'],
            ['code' => 'drop', 'name' => 'Tetes (drop)'],
            ['code' => 'cth', 'name' => 'Sendok Takar 5ml (cth)'],
            ['code' => 'tab', 'name' => 'Tablet (tab)'],
            ['code' => 'pulv', 'name' => 'Bungkus / Puyer (pulv)'],
            ['code' => 'syp', 'name' => 'Sirup (syp)'],
            ['code' => 'amp', 'name' => 'Ampul (amp)'],
            ['code' => 'strip', 'name' => 'Strip (strip)'],
            ['code' => 'sachet', 'name' => 'Sachet (sachet)'],
            ['code' => 'pcs', 'name' => 'Buah (pcs)'],
        ];

        foreach ($units as $u) {
            DB::table('master_units')->updateOrInsert(
                ['code' => $u['code']],
                array_merge($u, ['status_aktif' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // 9. MASTER ANCILLARY TESTS (LAB & RADIOLOGI)
        $tests = [
            ['code' => 'LAB-01', 'test_name' => 'Darah Lengkap & LED Pediatrik', 'category' => 'Laboratorium', 'tariff_umum' => 120000, 'tariff_asuransi' => 120000, 'specimen_or_modality' => 'Darah EDTA'],
            ['code' => 'LAB-02', 'test_name' => 'Tes Skrining Metabolisme Pediatrik', 'category' => 'Laboratorium', 'tariff_umum' => 280000, 'tariff_asuransi' => 280000, 'specimen_or_modality' => 'Darah & Urin'],
            ['code' => 'LAB-03', 'test_name' => 'Tes Alergi IgE Total Pediatrik', 'category' => 'Laboratorium', 'tariff_umum' => 350000, 'tariff_asuransi' => 350000, 'specimen_or_modality' => 'Serum Darah'],
            ['code' => 'LAB-04', 'test_name' => 'Elektrolit Serum (Na, K, Cl)', 'category' => 'Laboratorium', 'tariff_umum' => 160000, 'tariff_asuransi' => 160000, 'specimen_or_modality' => 'Serum Darah'],
            ['code' => 'RAD-01', 'test_name' => 'Rontgen Thorax AP Pediatrik', 'category' => 'Radiologi', 'tariff_umum' => 180000, 'tariff_asuransi' => 180000, 'specimen_or_modality' => 'X-Ray Digital'],
            ['code' => 'RAD-02', 'test_name' => 'USG Kepala / Neurosonografi Anak', 'category' => 'Radiologi', 'tariff_umum' => 300000, 'tariff_asuransi' => 300000, 'specimen_or_modality' => 'Ultrasonografi Fontanela'],
            ['code' => 'DIA-01', 'test_name' => 'Skrining M-CHAT-R (Autisme Pediatrik)', 'category' => 'Diagnostik Khusus', 'tariff_umum' => 150000, 'tariff_asuransi' => 150000, 'specimen_or_modality' => 'Kuesioner Klinis & Observasi'],
            ['code' => 'DIA-02', 'test_name' => 'Tes Audiometri BERA Pediatrik', 'category' => 'Diagnostik Khusus', 'tariff_umum' => 450000, 'tariff_asuransi' => 450000, 'specimen_or_modality' => 'Auditory Brainstem Response'],
            ['code' => 'DIA-03', 'test_name' => 'Sensory Profile 2 Assessment', 'category' => 'Diagnostik Khusus', 'tariff_umum' => 180000, 'tariff_asuransi' => 180000, 'specimen_or_modality' => 'Sensory Evaluation Standard'],
            ['code' => 'DIA-04', 'test_name' => 'Evaluasi Oral Motorik & Feeding', 'category' => 'Diagnostik Khusus', 'tariff_umum' => 160000, 'tariff_asuransi' => 160000, 'specimen_or_modality' => 'Observasi Struktur & Refleks Oral'],
            ['code' => 'DIA-05', 'test_name' => 'EEG Pediatrik (Elektroensefalografi)', 'category' => 'Diagnostik Khusus', 'tariff_umum' => 550000, 'tariff_asuransi' => 550000, 'specimen_or_modality' => 'Rekam Gelombang Otak'],
        ];

        foreach ($tests as $t) {
            DB::table('master_ancillary_tests')->updateOrInsert(
                ['code' => $t['code']],
                array_merge($t, ['status_aktif' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // 10. ENSURE PEDIATRIC MEDICATIONS IN master_produk
        $medicines = [
            ['kode_produk' => 'OBT-001', 'nama_produk' => 'Paracetamol Drop 15ml Pediatrik', 'nama_generik' => 'Paracetamol', 'jenis' => 'Obat Bebas', 'satuan' => 'btl', 'harga_beli' => 20000, 'harga_jual' => 28000, 'total_stok' => 42, 'is_obat' => true],
            ['kode_produk' => 'OBT-002', 'nama_produk' => 'Curcuma Plus Sirup Rasa Jeruk 200ml', 'nama_generik' => 'Curcuma & Multivitamin', 'jenis' => 'Suplemen', 'satuan' => 'btl', 'harga_beli' => 28000, 'harga_jual' => 38000, 'total_stok' => 25, 'is_obat' => true],
            ['kode_produk' => 'OBT-003', 'nama_produk' => 'Cetirizine Drop 10mg/ml Pediatrik', 'nama_generik' => 'Cetirizine HCl', 'jenis' => 'Obat Keras', 'satuan' => 'btl', 'harga_beli' => 30000, 'harga_jual' => 42000, 'total_stok' => 18, 'is_obat' => true],
            ['kode_produk' => 'OBT-004', 'nama_produk' => 'Ibuprofen Sirup 100mg/5ml (Proris)', 'nama_generik' => 'Ibuprofen', 'jenis' => 'Obat Bebas Terbatas', 'satuan' => 'btl', 'harga_beli' => 24000, 'harga_jual' => 35000, 'total_stok' => 22, 'is_obat' => true],
            ['kode_produk' => 'OBT-005', 'nama_produk' => 'Salbutamol Sirup 2mg/5ml Pediatrik', 'nama_generik' => 'Salbutamol Sulfate', 'jenis' => 'Obat Keras', 'satuan' => 'btl', 'harga_beli' => 15000, 'harga_jual' => 22000, 'total_stok' => 16, 'is_obat' => true],
            ['kode_produk' => 'OBT-006', 'nama_produk' => 'Zinc Drop 15ml Pediatrik', 'nama_generik' => 'Zinc Sulfate Monohydrate', 'jenis' => 'Suplemen', 'satuan' => 'btl', 'harga_beli' => 22000, 'harga_jual' => 32000, 'total_stok' => 30, 'is_obat' => true],
            ['kode_produk' => 'OBT-007', 'nama_produk' => 'Interlac Probiotik Drop 5ml', 'nama_generik' => 'Lactobacillus reuteri Protectis', 'jenis' => 'Suplemen', 'satuan' => 'btl', 'harga_beli' => 195000, 'harga_jual' => 245000, 'total_stok' => 14, 'is_obat' => true],
            ['kode_produk' => 'OBT-008', 'nama_produk' => 'Amoxicillin Sirup Kering 125mg/5ml', 'nama_generik' => 'Amoxicillin Trihydrate', 'jenis' => 'Obat Keras', 'satuan' => 'btl', 'harga_beli' => 12000, 'harga_jual' => 18000, 'total_stok' => 28, 'is_obat' => true],
            ['kode_produk' => 'OBT-009', 'nama_produk' => 'DHA & EPA Omega-3 Chewable Softgel (Isi 30)', 'nama_generik' => 'Fish Oil Omega-3', 'jenis' => 'Suplemen', 'satuan' => 'btl', 'harga_beli' => 85000, 'harga_jual' => 120000, 'total_stok' => 20, 'is_obat' => true],
        ];

        foreach ($medicines as $med) {
            DB::table('master_produk')->updateOrInsert(
                ['kode_produk' => $med['kode_produk']],
                array_merge($med, [
                    'produk_kategori_id' => 1,
                    'stok_minimum' => 5,
                    'status_aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // 11. SEED HISTORICAL VITALS & TREND DATA FOR PATIENT AZHAR (ID: 6) AND TEST ANAK (ID: 10 / FIRST CHILD)
        $sampleChild = DB::table('profile_anak')->first();
        if ($sampleChild) {
            $childId = $sampleChild->id;
            
            // Add default allergies to sample patient
            $laktosaAllergy = DB::table('master_allergies')->where('code', 'ALG-FOD-01')->first();
            $telurAllergy = DB::table('master_allergies')->where('code', 'ALG-FOD-02')->first();
            if ($laktosaAllergy) {
                DB::table('patient_allergies')->updateOrInsert(
                    ['profile_anak_id' => $childId, 'allergy_id' => $laktosaAllergy->id],
                    ['custom_reaction' => 'Kembung, diare & ruam merah', 'severity' => 'Sedang', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
                );
            }
            if ($telurAllergy) {
                DB::table('patient_allergies')->updateOrInsert(
                    ['profile_anak_id' => $childId, 'allergy_id' => $telurAllergy->id],
                    ['custom_reaction' => 'Gatal-gatal di pipi & leher', 'severity' => 'Ringan', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
                );
            }

            // Seed 4 sequential visits of vitals for trend charts
            $historicalVitals = [
                ['days_ago' => 30, 'td_sis' => 110, 'td_dia' => 70, 'hr' => 88, 'temp' => 36.8, 'rr' => 22, 'spo2' => 97, 'bb' => 14.2, 'tb' => 98.0, 'lk' => 49.2, 'status' => 'normal'],
                ['days_ago' => 21, 'td_sis' => 115, 'td_dia' => 75, 'hr' => 86, 'temp' => 36.7, 'rr' => 20, 'spo2' => 98, 'bb' => 14.5, 'tb' => 98.5, 'lk' => 49.4, 'status' => 'normal'],
                ['days_ago' => 14, 'td_sis' => 118, 'td_dia' => 78, 'hr' => 84, 'temp' => 36.6, 'rr' => 19, 'spo2' => 98, 'bb' => 14.8, 'tb' => 99.0, 'lk' => 49.5, 'status' => 'normal'],
                ['days_ago' => 0,  'td_sis' => 120, 'td_dia' => 80, 'hr' => 82, 'temp' => 36.5, 'rr' => 18, 'spo2' => 98, 'bb' => 15.0, 'tb' => 99.5, 'lk' => 49.8, 'status' => 'normal'],
            ];

            foreach ($historicalVitals as $hv) {
                $recDate = Carbon::now()->subDays($hv['days_ago']);
                DB::table('patient_vital_records')->insert([
                    'profile_anak_id' => $childId,
                    'recorded_at' => $recDate,
                    'td_sistole' => $hv['td_sis'],
                    'td_diastole' => $hv['td_dia'],
                    'hr' => $hv['hr'],
                    'temp' => $hv['temp'],
                    'rr' => $hv['rr'],
                    'spo2' => $hv['spo2'],
                    'bb' => $hv['bb'],
                    'tb' => $hv['tb'],
                    'lk' => $hv['lk'],
                    'vital_data' => json_encode([
                        ['code' => 'TD', 'name' => 'Tekanan Darah', 'value' => "{$hv['td_sis']}/{$hv['td_dia']}", 'unit' => 'mmHg', 'status' => 'normal'],
                        ['code' => 'HR', 'name' => 'Denyut Jantung', 'value' => $hv['hr'], 'unit' => 'bpm', 'status' => 'normal'],
                        ['code' => 'TEMP', 'name' => 'Suhu Tubuh', 'value' => $hv['temp'], 'unit' => '°C', 'status' => 'normal'],
                        ['code' => 'RR', 'name' => 'Laju Pernapasan', 'value' => $hv['rr'], 'unit' => 'x/m', 'status' => 'normal'],
                        ['code' => 'SPO2', 'name' => 'Saturasi Oksigen', 'value' => $hv['spo2'], 'unit' => '%', 'status' => 'normal'],
                        ['code' => 'BB', 'name' => 'Berat Badan', 'value' => $hv['bb'], 'unit' => 'kg', 'status' => 'normal'],
                        ['code' => 'TB', 'name' => 'Tinggi Badan', 'value' => $hv['tb'], 'unit' => 'cm', 'status' => 'normal'],
                        ['code' => 'LK', 'name' => 'Lingkar Kepala', 'value' => $hv['lk'], 'unit' => 'cm', 'status' => 'normal'],
                    ]),
                    'status' => $hv['status'],
                    'catatan' => 'Pemeriksaan rutin TTV dan antropometri klinis pediatrik',
                    'created_at' => $recDate,
                    'updated_at' => $recDate,
                ]);
            }
        }
    }
}
