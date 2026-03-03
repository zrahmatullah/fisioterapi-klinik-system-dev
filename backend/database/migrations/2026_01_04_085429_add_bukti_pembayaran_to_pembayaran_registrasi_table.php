<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pembayaran_registrasi', function (Blueprint $table) {

            // path file bukti (jpg/png/pdf)
            $table->string('bukti_pembayaran')
                ->nullable()
                ->after('keterangan');

            // status verifikasi oleh admin
            $table->enum('status_verifikasi', [
                'menunggu',
                'diterima',
                'ditolak'
            ])
                ->default('menunggu')
                ->after('bukti_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_registrasi', function (Blueprint $table) {
            $table->dropColumn([
                'bukti_pembayaran',
                'status_verifikasi'
            ]);
        });
    }
};