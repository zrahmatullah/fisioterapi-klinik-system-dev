<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catatan_aktivitas_anak', function (Blueprint $table) {
            $table->id();

            $table->foreignId('registrasi_anak_id')
                ->constrained('registrasi_anak')
                ->cascadeOnDelete();

            $table->foreignId('pelayanan_terapi_anak_id')
                ->constrained('pelayanan_terapi_anak')
                ->cascadeOnDelete();

            $table->text('aktivitas_terapi')->nullable();
            $table->text('keterangan_terapi')->nullable();
            $table->text('tugas_rumah')->nullable();

            $table->boolean('checkin_sesi')->default(false);

            $table->timestamps();

            $table->unique(
                ['pelayanan_terapi_anak_id'],
                'unique_catatan_per_sesi'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_aktivitas_anak');
    }
};
