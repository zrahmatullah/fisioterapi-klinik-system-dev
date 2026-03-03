<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // 1️⃣ Hapus NOT NULL constraint
        DB::statement('
            ALTER TABLE pembayaran_registrasi
            ALTER COLUMN status_verifikasi DROP NOT NULL
        ');

        // 2️⃣ Hapus DEFAULT jika ada
        DB::statement('
            ALTER TABLE pembayaran_registrasi
            ALTER COLUMN status_verifikasi DROP DEFAULT
        ');
    }

    public function down()
    {
        // ⚠️ rollback: set kembali NOT NULL + default
        DB::statement("
            ALTER TABLE pembayaran_registrasi
            ALTER COLUMN status_verifikasi SET DEFAULT 'menunggu'
        ");

        DB::statement('
            UPDATE pembayaran_registrasi
            SET status_verifikasi = \'menunggu\'
            WHERE status_verifikasi IS NULL
        ');

        DB::statement('
            ALTER TABLE pembayaran_registrasi
            ALTER COLUMN status_verifikasi SET NOT NULL
        ');
    }
};