<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('registrasi_anak', function (Blueprint $table) {
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('notelp', 20)->nullable();
            $table->integer('usia_saat_menikah')->nullable();
            $table->text('alamat')->nullable();
            $table->text('keluhan_saat_ini')->nullable();
            $table->text('kemampuan_saat_ini')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('registrasi_anak', function (Blueprint $table) {
            $table->dropColumn([
                'nama_ayah',
                'nama_ibu',
                'notelp',
                'usia_saat_menikah',
                'alamat',
                'keluhan_saat_ini',
                'kemampuan_saat_ini',
            ]);
        });
    }
};