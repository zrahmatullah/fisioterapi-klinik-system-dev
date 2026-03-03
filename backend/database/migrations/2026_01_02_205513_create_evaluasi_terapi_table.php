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
        Schema::create('evaluasi_terapi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_anak_id')
                ->constrained('registrasi_anak')
                ->cascadeOnDelete();

            $table->integer('total_sesi');

            $table->text('komponen_perilaku')->nullable();
            $table->text('kondisi_awal')->nullable();
            $table->text('kondisi_saat_ini')->nullable();
            $table->text('program_lanjutan')->nullable();
            $table->text('kesimpulan_hasil_followup')->nullable();
            $table->text('kemampuan_sebelumnya')->nullable();
            $table->text('peningkatan_kemampuan_saat_ini')->nullable();
            $table->text('saran_terapi')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_terapi');
    }
};
