<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrasi_anak', function (Blueprint $table) {
            $table->id();

            $table->foreignId('profile_anak_id')
                ->constrained('profile_anak')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('id_pasien')->unique();
            $table->string('no_regis')->unique();

            $table->date('tgl_regis');

            $table->foreignId('terapis_id')
                ->constrained('user_profile')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('ruangan_id')
                ->constrained('ruangan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrasi_anak');
    }
};
