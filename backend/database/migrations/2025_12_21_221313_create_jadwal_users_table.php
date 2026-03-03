<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_profile_id')
                ->constrained('user_profile')
                ->cascadeOnDelete();

            $table->foreignId('jadwal_master_id')
                ->constrained('jadwal_master')
                ->cascadeOnDelete();

            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->unique(['user_profile_id', 'jadwal_master_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_user');
    }
};
