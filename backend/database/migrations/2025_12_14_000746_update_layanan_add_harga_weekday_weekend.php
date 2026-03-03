<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('layanan', function (Blueprint $table) {
            // Hapus kolom harga lama
            if (Schema::hasColumn('layanan', 'harga')) {
                $table->dropColumn('harga');
            }

            // Tambahkan kolom harga weekday dan weekend
            $table->decimal('harga_weekday', 15, 2)->default(0)->after('layanan');
            $table->decimal('harga_weekend', 15, 2)->default(0)->after('harga_weekday');
        });
    }

    public function down(): void
    {
        Schema::table('layanan', function (Blueprint $table) {
            // Kembalikan kolom harga lama
            $table->decimal('harga', 15, 2)->default(0)->after('layanan');

            // Hapus kolom weekday/weekend
            $table->dropColumn(['harga_weekday', 'harga_weekend']);
        });
    }
};
