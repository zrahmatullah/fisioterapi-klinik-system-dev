<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('keluhan_anak', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('profile_anak_id');

            $table->boolean('status_aktif')->default(true);

            $table->string('no_keluhan')->unique();
            $table->string('kategori_keluhan');

            $table->date('tanggal_keluhan');
            $table->text('isi');

            $table->enum('status_tanggapan', [
                'belum_ditanggapi',
                'sudah_ditanggapi'
            ])->default('belum_ditanggapi');

            $table->text('tanggapan_keluhan')->nullable();

            $table->timestamps();

            $table->foreign('profile_anak_id')
                ->references('id')
                ->on('profile_anak')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keluhan_anak');
    }
};