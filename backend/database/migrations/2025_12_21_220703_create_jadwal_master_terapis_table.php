<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_master', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('hari');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->index(['hari', 'status_aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_master');
    }
};
