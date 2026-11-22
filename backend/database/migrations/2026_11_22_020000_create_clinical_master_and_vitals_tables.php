<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. MASTER VITAL PARAMETERS
        Schema::create('master_vital_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // TD, HR, TEMP, RR, SPO2, BB, TB, LK
            $table->string('name', 100);
            $table->string('unit', 20); // mmHg, bpm, °C, x/m, %, kg, cm
            $table->boolean('is_core_ttv')->default(true);
            $table->boolean('is_pediatric_growth')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        // 2. VITAL REFERENCE RANGES (AGE-ADJUSTED PEDIATRIC THRESHOLDS)
        Schema::create('vital_reference_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vital_parameter_id')->constrained('master_vital_parameters')->onDelete('cascade');
            $table->string('age_bracket', 50); // 0-12 Bulan, 1-3 Tahun, 3-6 Tahun, 6-12 Tahun, >12 Tahun
            $table->integer('min_age_months')->default(0);
            $table->integer('max_age_months')->default(240);
            $table->decimal('min_value', 8, 2);
            $table->decimal('max_value', 8, 2);
            $table->decimal('critical_low', 8, 2)->nullable();
            $table->decimal('critical_high', 8, 2)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 3. PATIENT VITAL RECORDS
        Schema::create('patient_vital_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_anak_id')->constrained('profile_anak')->onDelete('cascade');
            $table->foreignId('registrasi_anak_id')->nullable()->constrained('registrasi_anak')->onDelete('set null');
            $table->foreignId('emr_record_id')->nullable()->constrained('emr_records')->onDelete('set null');
            $table->dateTime('recorded_at')->useCurrent();
            $table->foreignId('recorded_by_id')->nullable()->constrained('user_profile')->onDelete('set null');
            
            // Core TTV & Growth numerical columns for fast queries/charting
            $table->integer('td_sistole')->nullable();
            $table->integer('td_diastole')->nullable();
            $table->decimal('hr', 6, 1)->nullable(); // Heart rate bpm
            $table->decimal('temp', 4, 1)->nullable(); // Suhu °C
            $table->decimal('rr', 5, 1)->nullable(); // Respiratory rate x/m
            $table->decimal('spo2', 5, 1)->nullable(); // SpO2 %
            $table->decimal('bb', 5, 2)->nullable(); // Berat badan kg
            $table->decimal('tb', 5, 2)->nullable(); // Tinggi/panjang badan cm
            $table->decimal('lk', 5, 2)->nullable(); // Lingkar kepala cm
            
            // Structured parameter items [{ parameter_id, code, value, unit, status }]
            $table->jsonb('vital_data')->nullable();
            $table->string('status', 30)->default('normal'); // normal, perlu_perhatian, abnormal_kritis
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 4. MASTER ALLERGIES
        Schema::create('master_allergies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('category', 50); // Obat, Makanan, Lingkungan
            $table->string('allergen_name', 150);
            $table->string('severity_default', 30)->default('Sedang'); // Ringan, Sedang, Berat
            $table->text('deskripsi')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        // 5. PATIENT ALLERGIES
        Schema::create('patient_allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_anak_id')->constrained('profile_anak')->onDelete('cascade');
            $table->foreignId('allergy_id')->constrained('master_allergies')->onDelete('cascade');
            $table->string('custom_reaction', 255)->nullable(); // e.g. Urtikaria, Sesak, Syok Anafilaksis
            $table->string('severity', 30)->default('Sedang');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. MASTER SOAP TEMPLATES
        Schema::create('master_soap_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('discipline', 80); // Terapi Wicara, Okupasi Terapi, Sensori Integrasi, Fisioterapi, Dokter Anak / KFR
            $table->text('subjective_template');
            $table->text('objective_template');
            $table->text('assessment_template');
            $table->text('planning_template');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        // 7. MASTER PROCEDURES (ICD-9-CM & CLINICAL PROCEDURES)
        Schema::create('master_procedures', function (Blueprint $table) {
            $table->id();
            $table->string('icd9_code', 20)->nullable();
            $table->string('procedure_name', 200);
            $table->string('category', 80); // Terapi Wicara, Okupasi Terapi, Fisioterapi, Sensori Integrasi, Medis / Dokter, Psikologi
            $table->decimal('tariff_umum', 12, 2)->default(0);
            $table->decimal('tariff_asuransi', 12, 2)->default(0);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        // 8. MASTER SIGNA
        Schema::create('master_signa', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('signa_text', 100); // 3 x 1 cth (5 ml), 3 x 0.6 ml, 1 x 1 btl, etc.
            $table->integer('times_per_day')->default(3);
            $table->decimal('dose_qty', 8, 2)->default(1);
            $table->string('unit', 30)->default('cth');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        // 9. MASTER INTAKE TIMES
        Schema::create('master_intake_times', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('intake_time_text', 100); // Sesudah Makan (p.c.), Sebelum Makan (a.c.), etc.
            $table->string('latin_abbr', 20)->nullable(); // p.c., a.c., d.c., h.s., p.r.n.
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        // 10. MASTER UNITS
        Schema::create('master_units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        // 11. MASTER ANCILLARY TESTS (LAB & RADIOLOGI)
        Schema::create('master_ancillary_tests', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('test_name', 150);
            $table->string('category', 50); // Laboratorium, Radiologi, Diagnostik Khusus
            $table->decimal('tariff_umum', 12, 2)->default(0);
            $table->decimal('tariff_asuransi', 12, 2)->default(0);
            $table->string('specimen_or_modality', 100)->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_ancillary_tests');
        Schema::dropIfExists('master_units');
        Schema::dropIfExists('master_intake_times');
        Schema::dropIfExists('master_signa');
        Schema::dropIfExists('master_procedures');
        Schema::dropIfExists('master_soap_templates');
        Schema::dropIfExists('patient_allergies');
        Schema::dropIfExists('master_allergies');
        Schema::dropIfExists('patient_vital_records');
        Schema::dropIfExists('vital_reference_ranges');
        Schema::dropIfExists('master_vital_parameters');
    }
};
