<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profile_anak', function (Blueprint $table) {
            $table->foreignId('jenis_kelamin_id')
                ->after('tanggal_lahir')
                ->constrained('jenis_kelamin')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('agama_id')
                ->after('jenis_kelamin_id')
                ->constrained('agama_m')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profile_anak', function (Blueprint $table) {
            // drop foreign key dulu
            $table->dropForeign(['jenis_kelamin_id']);
            $table->dropForeign(['agama_id']);

            // lalu drop kolom
            $table->dropColumn([
                'jenis_kelamin_id',
                'agama_id'
            ]);
        });
    }
};
