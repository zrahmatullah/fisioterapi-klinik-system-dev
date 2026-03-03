<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assesment_1', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('registrasi_anak_id');
            $table->boolean('status_aktif')->default(true);

            $table->string('umur', 20)->nullable();

            $table->text('informasi_subjektif')->nullable();
            $table->text('informasi_objektif')->nullable();

            // Kehamilan & Kelahiran
            $table->string('gangguan_kehamilan')->nullable();
            $table->string('proses_kelahiran', 50)->nullable();
            $table->string('usia_kehamilan_lahir', 50)->nullable();
            $table->string('gangguan_melahirkan')->nullable();

            // Riwayat & Perilaku Anak (Ya / Tidak + Keterangan)
            $table->boolean('riwayat_kejang')->nullable();
            $table->text('riwayat_kejang_ket')->nullable();

            $table->boolean('konsumsi_obat_epilepsi')->nullable();
            $table->text('konsumsi_obat_epilepsi_ket')->nullable();

            $table->boolean('perkembangan_sesuai_usia')->nullable();
            $table->text('perkembangan_sesuai_usia_ket')->nullable();

            $table->boolean('disusui_ibu')->nullable();
            $table->text('disusui_ibu_ket')->nullable();

            $table->boolean('tv_gadget_addict')->nullable();
            $table->text('tv_gadget_addict_ket')->nullable();

            $table->boolean('sering_memutar_benda')->nullable();
            $table->text('sering_memutar_benda_ket')->nullable();

            $table->boolean('main_mobil_berulang')->nullable();
            $table->text('main_mobil_berulang_ket')->nullable();

            $table->boolean('flapping')->nullable();
            $table->text('flapping_ket')->nullable();

            $table->boolean('tantrum')->nullable();
            $table->text('tantrum_ket')->nullable();

            $table->boolean('kontak_mata')->nullable();
            $table->text('kontak_mata_ket')->nullable();

            $table->boolean('gangguan_makan_menelan')->nullable();
            $table->text('gangguan_makan_menelan_ket')->nullable();

            $table->boolean('komunikasi_dua_arah')->nullable();
            $table->text('komunikasi_dua_arah_ket')->nullable();

            $table->timestamps();

            $table->foreign('registrasi_anak_id')
                ->references('id')
                ->on('registrasi_anak')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assesment_1');
    }
};
