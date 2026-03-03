<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_login', function (Blueprint $table) {
            // Hapus FK dan kolom pegawai_id jika ada
            if (Schema::hasColumn('user_login', 'pegawai_id')) {
                $table->dropForeign(['pegawai_id']); // hapus constraint FK
                $table->dropColumn('pegawai_id');    // hapus kolom
            }

            // Tambah kolom user_profile_id
            if (!Schema::hasColumn('user_login', 'user_profile_id')) {
                $table->unsignedBigInteger('user_profile_id')->after('id');

                // Tambah FK ke user_profile
                $table->foreign('user_profile_id')
                    ->references('id')
                    ->on('user_profile')
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_login', function (Blueprint $table) {
            // Hapus FK user_profile_id
            if (Schema::hasColumn('user_login', 'user_profile_id')) {
                $table->dropForeign(['user_profile_id']);
                $table->dropColumn('user_profile_id');
            }

            // Tambah kembali kolom pegawai_id jika perlu
            if (!Schema::hasColumn('user_login', 'pegawai_id')) {
                $table->unsignedBigInteger('pegawai_id')->after('id');
                $table->foreign('pegawai_id')
                    ->references('id')
                    ->on('pegawai')
                    ->onDelete('cascade');
            }
        });
    }
};
