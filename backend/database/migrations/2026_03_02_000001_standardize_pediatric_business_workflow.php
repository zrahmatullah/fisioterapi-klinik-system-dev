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
        // =====================================================================
        // 1. TAKE OUT OBSOLETE / REDUNDANT TABLES
        // =====================================================================
        Schema::dropIfExists('users');
        Schema::dropIfExists('pembayaran_registrasi');
        Schema::dropIfExists('assesment_1');
        Schema::dropIfExists('pegawai');

        // =====================================================================
        // 2. ENHANCE EXISTING TABLES
        // =====================================================================

        // Enhance profile_anak: add parent_user_id for direct login account binding
        if (Schema::hasTable('profile_anak')) {
            Schema::table('profile_anak', function (Blueprint $table) {
                if (!Schema::hasColumn('profile_anak', 'parent_user_id')) {
                    $table->unsignedBigInteger('parent_user_id')->nullable()->after('id_orang_tua');
                    $table->foreign('parent_user_id')->references('id')->on('user_login')->nullOnDelete();
                }
            });
        }

        // Enhance paket_terapi_pasien: add upfront payment status & pricing
        if (Schema::hasTable('paket_terapi_pasien')) {
            Schema::table('paket_terapi_pasien', function (Blueprint $table) {
                if (!Schema::hasColumn('paket_terapi_pasien', 'harga_paket')) {
                    $table->decimal('harga_paket', 12, 2)->default(0)->after('nama_paket');
                }
                if (!Schema::hasColumn('paket_terapi_pasien', 'is_lunas_awal')) {
                    $table->boolean('is_lunas_awal')->default(true)->after('harga_paket');
                }
                if (!Schema::hasColumn('paket_terapi_pasien', 'status_bayar')) {
                    $table->string('status_bayar', 50)->default('lunas_di_awal')->after('is_lunas_awal');
                }
                if (!Schema::hasColumn('paket_terapi_pasien', 'metode_pembayaran_awal')) {
                    $table->string('metode_pembayaran_awal', 50)->nullable()->after('status_bayar');
                }
            });
        }

        // Enhance invoices: add insurance claim input & excess cash calculation
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'nominal_klaim_asuransi')) {
                    $table->decimal('nominal_klaim_asuransi', 12, 2)->default(0)->after('covered_amount');
                }
                if (!Schema::hasColumn('invoices', 'selisih_excess_cash')) {
                    $table->decimal('selisih_excess_cash', 12, 2)->default(0)->after('excess_amount');
                }
                if (!Schema::hasColumn('invoices', 'excess_cash_paid')) {
                    $table->boolean('excess_cash_paid')->default(false)->after('selisih_excess_cash');
                }
                if (!Schema::hasColumn('invoices', 'is_paket_lunas')) {
                    $table->boolean('is_paket_lunas')->default(false)->after('excess_cash_paid');
                }
            });
        }

        // =====================================================================
        // 3. CREATE PATIENT_APPOINTMENTS (KALENDER SESI & CHECK-IN TRACKING)
        // =====================================================================
        if (!Schema::hasTable('patient_appointments')) {
            Schema::create('patient_appointments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_anak_id')->constrained('profile_anak')->cascadeOnDelete();
                $table->unsignedBigInteger('paket_terapi_pasien_id')->nullable();
                $table->unsignedBigInteger('layanan_id')->nullable();
                $table->unsignedBigInteger('dokter_id')->nullable();
                $table->unsignedBigInteger('terapis_id')->nullable();
                $table->unsignedBigInteger('ruangan_id')->nullable();
                $table->integer('sesi_ke')->default(1);
                $table->integer('total_sesi')->default(1);
                $table->date('tanggal_appointment');
                $table->string('jam_mulai', 10)->nullable();
                $table->string('jam_selesai', 10)->nullable();
                // Status: terjadwal, checked_in, selesai, batal, reschedule
                $table->string('status', 30)->default('terjadwal');
                $table->unsignedBigInteger('registrasi_anak_id')->nullable();
                $table->text('catatan')->nullable();
                $table->timestamp('checked_in_at')->nullable();
                $table->unsignedBigInteger('checked_in_by')->nullable();
                $table->timestamps();

                $table->foreign('paket_terapi_pasien_id')->references('id')->on('paket_terapi_pasien')->nullOnDelete();
                $table->foreign('layanan_id')->references('id')->on('layanan')->nullOnDelete();
                $table->foreign('dokter_id')->references('id')->on('user_profile')->nullOnDelete();
                $table->foreign('terapis_id')->references('id')->on('user_profile')->nullOnDelete();
                $table->foreign('ruangan_id')->references('id')->on('ruangan')->nullOnDelete();
                $table->foreign('registrasi_anak_id')->references('id')->on('registrasi_anak')->nullOnDelete();
                $table->foreign('checked_in_by')->references('id')->on('user_login')->nullOnDelete();
            });
        }

        // =====================================================================
        // 4. CREATE E-RESEP & E-RESEP ITEMS (DOKTER RESEP OBAT + SIGNA LENGKAP)
        // =====================================================================
        if (!Schema::hasTable('e_resep')) {
            Schema::create('e_resep', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_resep', 50)->unique();
                $table->foreignId('registrasi_anak_id')->constrained('registrasi_anak')->cascadeOnDelete();
                $table->foreignId('profile_anak_id')->constrained('profile_anak')->cascadeOnDelete();
                $table->unsignedBigInteger('dokter_id')->nullable();
                // Status: menunggu_dispensing, sedang_disiapkan, selesai_dispensing, batal
                $table->string('status', 30)->default('menunggu_dispensing');
                $table->text('catatan')->nullable();
                $table->timestamp('dispensed_at')->nullable();
                $table->unsignedBigInteger('dispensed_by')->nullable();
                $table->timestamps();

                $table->foreign('dokter_id')->references('id')->on('user_profile')->nullOnDelete();
                $table->foreign('dispensed_by')->references('id')->on('user_login')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('e_resep_items')) {
            Schema::create('e_resep_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('e_resep_id')->constrained('e_resep')->cascadeOnDelete();
                $table->unsignedBigInteger('master_produk_id')->nullable();
                $table->string('nama_obat', 255);
                $table->integer('jumlah')->default(1);
                $table->string('satuan', 50)->default('pcs');
                $table->string('signa', 100); // Misal: 3 x 1 cth, 2 x 1/2 tab
                $table->string('waktu_minum', 50)->default('Sesudah Makan'); // Sesudah Makan, Sebelum Makan, Bersama Makan
                $table->text('catatan_racikan')->nullable();
                $table->decimal('harga_satuan', 12, 2)->default(0);
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->timestamps();

                $table->foreign('master_produk_id')->references('id')->on('master_produk')->nullOnDelete();
            });
        }

        // =====================================================================
        // 5. CREATE ORDER_PENUNJANG (LAB, RADIOLOGI, TERAPI TAMBAHAN)
        // =====================================================================
        if (!Schema::hasTable('order_penunjang')) {
            Schema::create('order_penunjang', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_order', 50)->unique();
                $table->foreignId('registrasi_anak_id')->constrained('registrasi_anak')->cascadeOnDelete();
                $table->foreignId('profile_anak_id')->constrained('profile_anak')->cascadeOnDelete();
                $table->unsignedBigInteger('dokter_id')->nullable();
                // Jenis: laboratorium, radiologi, terapi_tambahan, tindakan_medis
                $table->string('jenis_order', 50)->default('laboratorium');
                $table->string('nama_tindakan', 255);
                $table->decimal('tarif', 12, 2)->default(0);
                $table->text('catatan_klinis')->nullable();
                $table->text('hasil_pemeriksaan')->nullable();
                // Status: diajukan, diproses, selesai, batal
                $table->string('status', 30)->default('diajukan');
                $table->unsignedBigInteger('invoice_id')->nullable();
                $table->timestamps();

                $table->foreign('dokter_id')->references('id')->on('user_profile')->nullOnDelete();
                $table->foreign('invoice_id')->references('id')->on('invoices')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_penunjang');
        Schema::dropIfExists('e_resep_items');
        Schema::dropIfExists('e_resep');
        Schema::dropIfExists('patient_appointments');
    }
};
