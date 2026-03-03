<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('registrasi_anak', function (Blueprint $table) {
            $table->timestamp('waktu_kedatangan')->nullable()->after('status_kedatangan');
        });
    }

    public function down()
    {
        Schema::table('registrasi_anak', function (Blueprint $table) {
            $table->dropColumn('waktu_kedatangan');
        });
    }

};
