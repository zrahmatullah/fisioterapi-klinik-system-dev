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
        // Make registrasi_anak_id nullable in e_resep and order_penunjang
        // using raw SQL for Postgres safety
        DB::statement('ALTER TABLE e_resep ALTER COLUMN registrasi_anak_id DROP NOT NULL;');
        DB::statement('ALTER TABLE order_penunjang ALTER COLUMN registrasi_anak_id DROP NOT NULL;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to NOT NULL if needed
    }
};
