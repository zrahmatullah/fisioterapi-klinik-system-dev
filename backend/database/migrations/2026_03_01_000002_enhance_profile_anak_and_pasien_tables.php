<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TAMBAH KOLOM NO_RM & DATA PENDUKUNG PADA profile_anak
        Schema::table('profile_anak', function (Blueprint $table) {
            if (!Schema::hasColumn('profile_anak', 'no_rm')) {
                $table->string('no_rm', 30)->nullable()->unique();
            }
            if (!Schema::hasColumn('profile_anak', 'foto_anak')) {
                $table->string('foto_anak')->nullable();
            }
            if (!Schema::hasColumn('profile_anak', 'sekolah')) {
                $table->string('sekolah')->nullable();
            }
            if (!Schema::hasColumn('profile_anak', 'catatan_alergi')) {
                $table->text('catatan_alergi')->nullable();
            }
            if (!Schema::hasColumn('profile_anak', 'diagnosis_awal')) {
                $table->text('diagnosis_awal')->nullable();
            }
            if (!Schema::hasColumn('profile_anak', 'rujukan_dari')) {
                $table->string('rujukan_dari')->nullable();
            }
            if (!Schema::hasColumn('profile_anak', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Generate No. RM untuk data anak eksisting secara aman
        $existingKids = DB::table('profile_anak')->whereNull('no_rm')->orderBy('id', 'asc')->get();
        foreach ($existingKids as $index => $kid) {
            $nomor = str_pad($kid->id, 5, '0', STR_PAD_LEFT);
            $tahunBulan = date('Ym', strtotime($kid->created_at ?? now()));
            $noRm = "RM-{$tahunBulan}-{$nomor}";
            DB::table('profile_anak')->where('id', $kid->id)->update(['no_rm' => $noRm]);
        }

        // 2. TABEL MULTI-WALI & KONTAK DARURAT
        if (!Schema::hasTable('pasien_wali')) {
            Schema::create('pasien_wali', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('profile_anak_id');
                $table->unsignedBigInteger('user_profile_id')->nullable();
                $table->string('nama_wali');
                $table->string('hubungan'); // Ayah, Ibu, Kakek, Nenek, Wali Hukum
                $table->string('no_hp');
                $table->string('pekerjaan')->nullable();
                $table->string('email')->nullable();
                $table->text('alamat')->nullable();
                $table->boolean('is_kontak_darurat')->default(false);
                $table->boolean('is_wali_utama')->default(false);
                $table->timestamps();

                $table->foreign('profile_anak_id')->references('id')->on('profile_anak')->onDelete('cascade');
                $table->index(['profile_anak_id', 'is_wali_utama']);
            });
        }

        // 3. TABEL INFORMED CONSENT & DIGITAL SIGNATURE
        if (!Schema::hasTable('informed_consents')) {
            Schema::create('informed_consents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('profile_anak_id');
                $table->unsignedBigInteger('registrasi_anak_id')->nullable();
                $table->string('judul_persetujuan');
                $table->string('tipe_persetujuan')->default('umum'); // umum, tindakan_terapi, penggunaan_data_pdp
                $table->text('pernyataan');
                $table->string('nama_penandatangan');
                $table->string('hubungan_wali');
                $table->text('tanda_tangan_base64');
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('signed_at')->useCurrent();
                $table->timestamps();

                $table->foreign('profile_anak_id')->references('id')->on('profile_anak')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('informed_consents');
        Schema::dropIfExists('pasien_wali');
        Schema::table('profile_anak', function (Blueprint $table) {
            $table->dropColumn(['no_rm', 'foto_anak', 'sekolah', 'catatan_alergi', 'diagnosis_awal', 'rujukan_dari', 'deleted_at']);
        });
    }
};
