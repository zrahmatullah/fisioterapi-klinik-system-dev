<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. SURAT TEMPLATES
        if (!Schema::hasTable('surat_templates')) {
            Schema::create('surat_templates', function (Blueprint $table) {
                $table->id();
                $table->string('kode_template', 50)->unique();
                $table->string('judul');
                $table->string('jenis_surat'); // keterangan_terapi, rujukan_keluar, balasan_rujukan, rekomendasi_sekolah, laporan_asesmen, consent, resume_medis, kwitansi
                $table->boolean('kop_surat_aktif')->default(true);
                $table->string('format_nomor')->default('{SEQ}/KLINIK-SKT/{ROMAN_MONTH}/{YEAR}');
                $table->text('template_content');
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();
            });
        }

        // 2. SURAT KELUAR / ARSIP SURAT
        if (!Schema::hasTable('surat_keluar')) {
            Schema::create('surat_keluar', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_surat', 60)->unique();
                $table->unsignedBigInteger('surat_template_id');
                $table->unsignedBigInteger('profile_anak_id');
                $table->unsignedBigInteger('registrasi_anak_id')->nullable();
                $table->unsignedBigInteger('penandatangan_id')->nullable();
                $table->string('judul_surat');
                $table->jsonb('parameter_data')->nullable();
                $table->text('isi_final_html')->nullable();
                $table->string('qr_verification_code', 64)->unique();
                $table->string('file_pdf_path')->nullable();
                $table->string('status')->default('final'); // draft, final, dibatalkan
                $table->date('tanggal_surat');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('profile_anak_id')->references('id')->on('profile_anak')->onDelete('cascade');
                $table->foreign('surat_template_id')->references('id')->on('surat_templates')->onDelete('cascade');
                $table->index(['profile_anak_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_keluar');
        Schema::dropIfExists('surat_templates');
    }
};
