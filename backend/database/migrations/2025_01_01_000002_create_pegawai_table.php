<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->boolean('status_aktif')->default(true);
            $table->foreignId('jenis_pegawai_id')->constrained('jenis_pegawai')->onDelete('cascade');
            $table->string('nama_pegawai', 150);
            $table->string('alamat')->nullable();
            $table->string('nip', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
