<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. MASTER ICD-10
        if (!Schema::hasTable('master_icd10')) {
            Schema::create('master_icd10', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 10)->unique();
                $table->string('deskripsi_en');
                $table->string('deskripsi_id')->nullable();
                $table->string('kategori')->nullable();
                $table->boolean('is_terapi_anak')->default(false);
                $table->timestamps();

                $table->index('kode');
                $table->index('is_terapi_anak');
            });
        }

        // 2. MASTER ICD-9-CM (TINDAKAN PROSEDUR)
        if (!Schema::hasTable('master_icd9')) {
            Schema::create('master_icd9', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 10)->unique();
                $table->string('deskripsi_en');
                $table->string('deskripsi_id')->nullable();
                $table->boolean('is_terapi_anak')->default(false);
                $table->timestamps();

                $table->index('kode');
            });
        }

        // 3. EMR RECORDS (REKAM MEDIS INDUK)
        if (!Schema::hasTable('emr_records')) {
            Schema::create('emr_records', function (Blueprint $table) {
                $table->id();
                $table->string('no_emr', 40)->unique();
                $table->unsignedBigInteger('profile_anak_id');
                $table->unsignedBigInteger('registrasi_anak_id')->nullable();
                $table->unsignedBigInteger('terapis_id')->nullable();
                $table->unsignedBigInteger('dokter_id')->nullable();
                $table->date('tanggal_pemeriksaan');
                $table->string('jenis_layanan')->default('Tumbuh Kembang'); // Wicara, Okupasi, Sensori Integrasi, Fisioterapi, ABA, Psikologi
                $table->string('status')->default('draft'); // draft, final, amended
                $table->timestamp('locked_at')->nullable();
                $table->unsignedBigInteger('signed_by_id')->nullable();
                $table->string('signature_path')->nullable();
                $table->string('sha256_hash', 64)->nullable();
                $table->softDeletes();
                $table->timestamps();

                $table->foreign('profile_anak_id')->references('id')->on('profile_anak')->onDelete('cascade');
                $table->index(['profile_anak_id', 'tanggal_pemeriksaan']);
                $table->index(['status', 'jenis_layanan']);
            });
        }

        // 4. EMR SOAP (CATATAN HARIAN SESI TERAPI)
        if (!Schema::hasTable('emr_soaps')) {
            Schema::create('emr_soaps', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('emr_record_id')->unique();
                $table->text('subjektif')->nullable();
                $table->text('objektif')->nullable();
                $table->text('asesmen')->nullable();
                $table->text('planning')->nullable();
                $table->text('target_sesi')->nullable();
                $table->text('aktivitas_terapi')->nullable();
                $table->text('respons_anak')->nullable();
                $table->text('rekomendasi_home_program')->nullable();
                $table->timestamps();

                $table->foreign('emr_record_id')->references('id')->on('emr_records')->onDelete('cascade');
            });
        }

        // 5. EMR ASSESSMENTS (ASESMEN DINAMIS MULTI-DISIPLIN)
        if (!Schema::hasTable('emr_assessments')) {
            Schema::create('emr_assessments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('emr_record_id');
                $table->string('tipe_asesmen'); // wicara, okupasi, psikologi, tumbuh_kembang, mchat, kpsp
                $table->jsonb('form_data');
                $table->timestamps();

                $table->foreign('emr_record_id')->references('id')->on('emr_records')->onDelete('cascade');
                $table->index(['emr_record_id', 'tipe_asesmen']);
            });
        }

        // 6. EMR GOAL TRACKING (TARGET JANGKA PENDEK / PANJANG)
        if (!Schema::hasTable('emr_goal_trackings')) {
            Schema::create('emr_goal_trackings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('profile_anak_id');
                $table->unsignedBigInteger('emr_record_id')->nullable();
                $table->string('tipe_goal')->default('jangka_pendek'); // jangka_pendek, jangka_panjang
                $table->string('judul_target');
                $table->text('indikator_keberhasilan')->nullable();
                $table->integer('baseline_persen')->default(0);
                $table->integer('capaian_persen')->default(0);
                $table->date('tanggal_target')->nullable();
                $table->string('status')->default('aktif'); // aktif, tercapai, revisi, dihentikan
                $table->timestamps();

                $table->foreign('profile_anak_id')->references('id')->on('profile_anak')->onDelete('cascade');
                $table->index(['profile_anak_id', 'status']);
            });
        }

        // 7. EMR ADDENDUM (JEJAK REVISI RME FINAL)
        if (!Schema::hasTable('emr_addendums')) {
            Schema::create('emr_addendums', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('emr_record_id');
                $table->unsignedBigInteger('user_id');
                $table->text('alasan_koreksi');
                $table->text('catatan_addendum');
                $table->jsonb('data_sebelumnya')->nullable();
                $table->timestamps();

                $table->foreign('emr_record_id')->references('id')->on('emr_records')->onDelete('cascade');
            });
        }

        // 8. EMR ATTACHMENTS (FOTO, VIDEO SESI, DOKUMEN LAB)
        if (!Schema::hasTable('emr_attachments')) {
            Schema::create('emr_attachments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('emr_record_id')->nullable();
                $table->unsignedBigInteger('profile_anak_id');
                $table->string('nama_file');
                $table->string('file_path');
                $table->string('file_type', 50)->nullable();
                $table->unsignedBigInteger('file_size')->default(0);
                $table->string('kategori')->default('foto_sesi'); // foto_sesi, video_sesi, lab_psikotes, rujukan
                $table->text('keterangan')->nullable();
                $table->timestamps();

                $table->foreign('profile_anak_id')->references('id')->on('profile_anak')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('emr_attachments');
        Schema::dropIfExists('emr_addendums');
        Schema::dropIfExists('emr_goal_trackings');
        Schema::dropIfExists('emr_assessments');
        Schema::dropIfExists('emr_soaps');
        Schema::dropIfExists('emr_records');
        Schema::dropIfExists('master_icd9');
        Schema::dropIfExists('master_icd10');
    }
};
