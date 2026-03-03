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
        Schema::table('pelayanan_terapi_anak', function (Blueprint $table) {
            $table->foreignId('terapis_id')
                ->nullable()
                ->after('layanan_id')
                ->constrained('user_profile')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('pelayanan_terapi_anak', function (Blueprint $table) {
            $table->dropForeign(['terapis_id']);
            $table->dropColumn('terapis_id');
        });
    }
};
