<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('terapis_to_ruangan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('terapis_id')
                ->constrained('user_profile')
                ->cascadeOnDelete();

            $table->foreignId('ruangan_id')
                ->constrained('ruangan')
                ->cascadeOnDelete();

            $table->unique(['terapis_id', 'ruangan_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terapis_to_ruangan');
    }
};
