<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make id_orang_tua nullable so Master Anak does not force parent registration
        DB::statement('ALTER TABLE profile_anak ALTER COLUMN id_orang_tua DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE profile_anak ALTER COLUMN id_orang_tua SET NOT NULL');
    }
};
