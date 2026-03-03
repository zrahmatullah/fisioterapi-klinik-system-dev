<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_anak', function (Blueprint $table) {
            $table->id();
            $table->boolean('status_aktif')->default(true);
            $table->string('nama_anak');
            $table->foreignId('id_orang_tua')->constrained('user_profile')->onDelete('cascade'); // FK ke user_profile
            $table->string('NIK')->unique();
            $table->text('alamat')->nullable();
            $table->integer('umur')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->string('no_hp_orang_tua')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_anak');
    }
};
