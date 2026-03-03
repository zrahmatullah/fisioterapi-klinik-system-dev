



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
            $table->date('tanggal_reschedule_request')->nullable()->after('tanggal_penjadwalan');
        });
    }

    public function down()
    {
        Schema::table('pelayanan_terapi_anak', function (Blueprint $table) {
            $table->dropColumn('tanggal_reschedule_request');
        });
    }

};
