<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pelayanan_terapi_anak', function (Blueprint $table) {
            $table->id();

            $table->foreignId('registrasi_anak_id')
                ->constrained('registrasi_anak')
                ->cascadeOnDelete();

            // ✅ FIXED: layanan_id
            $table->foreignId('layanan_id')
                ->constrained('layanan')
                ->cascadeOnDelete();

            $table->integer('qty')->default(1);
            $table->decimal('harga', 12, 2);

            $table->date('tanggal_penjadwalan');

            $table->string('status')->default('terjadwal');
            // terjadwal | hadir | tidak_hadir | selesai | batal

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelayanan_terapi_anak');
    }
};
