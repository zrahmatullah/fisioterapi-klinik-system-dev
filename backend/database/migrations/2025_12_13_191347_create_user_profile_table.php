<?php

// database/migrations/xxxx_create_user_profile_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_profile', function (Blueprint $table) {
            $table->id();
            $table->boolean('status_aktif')->default(true);
            $table->foreignId('jenis_user_id')
                ->constrained('jenis_user')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->string('nip')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profile');
    }
};
