<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('registrasi_anak', function (Blueprint $table) {
            $table->string('status_kedatangan')->default('belum_datang');
            $table->string('status_pelayanan')->default('belum_dilayani');
        });
    }

    public function down(): void
    {
        Schema::table('registrasi_anak', function (Blueprint $table) {
            $table->dropColumn(['status_kedatangan', 'status_pelayanan']);
        });
    }
};
