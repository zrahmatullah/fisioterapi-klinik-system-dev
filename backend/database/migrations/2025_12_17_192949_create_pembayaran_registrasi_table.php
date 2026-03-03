<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembayaran_registrasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('registrasi_anak_id')
                ->constrained('registrasi_anak')
                ->cascadeOnDelete();

            $table->string('nomor_pembayaran')->unique();
            $table->date('tanggal_bayar');

            $table->decimal('total_tagihan', 12, 2);
            $table->decimal('jumlah_bayar', 12, 2);

            // fleksibel dulu
            $table->string('metode_pembayaran')->nullable();
            $table->string('status')->nullable();

            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_registrasi');
    }
};
