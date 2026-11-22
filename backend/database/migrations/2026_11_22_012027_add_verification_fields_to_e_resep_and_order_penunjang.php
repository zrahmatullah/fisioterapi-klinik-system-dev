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
        Schema::table('e_resep', function (Blueprint $table) {
            if (!Schema::hasColumn('e_resep', 'catatan_telaah')) {
                $table->text('catatan_telaah')->nullable();
            }
            if (!Schema::hasColumn('e_resep', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
            if (!Schema::hasColumn('e_resep', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->foreign('verified_by')->references('id')->on('user_login')->nullOnDelete();
            }
        });

        Schema::table('order_penunjang', function (Blueprint $table) {
            if (!Schema::hasColumn('order_penunjang', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
            if (!Schema::hasColumn('order_penunjang', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->foreign('verified_by')->references('id')->on('user_login')->nullOnDelete();
            }
            if (!Schema::hasColumn('order_penunjang', 'alasan_batal')) {
                $table->text('alasan_batal')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('e_resep', function (Blueprint $table) {
            if (Schema::hasColumn('e_resep', 'verified_by')) {
                $table->dropForeign(['verified_by']);
                $table->dropColumn('verified_by');
            }
            if (Schema::hasColumn('e_resep', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
            if (Schema::hasColumn('e_resep', 'catatan_telaah')) {
                $table->dropColumn('catatan_telaah');
            }
        });

        Schema::table('order_penunjang', function (Blueprint $table) {
            if (Schema::hasColumn('order_penunjang', 'verified_by')) {
                $table->dropForeign(['verified_by']);
                $table->dropColumn('verified_by');
            }
            if (Schema::hasColumn('order_penunjang', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
            if (Schema::hasColumn('order_penunjang', 'alasan_batal')) {
                $table->dropColumn('alasan_batal');
            }
        });
    }
};
