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
        Schema::table('pembayaran_registrasi', function (Blueprint $table) {
            $table->foreignId('promo_id')
                ->nullable()
                ->after('registrasi_anak_id')
                ->constrained('promosi')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_registrasi', function (Blueprint $table) {
            $table->dropForeign(['promo_id']);
            $table->dropColumn('promo_id');
        });
    }
};
