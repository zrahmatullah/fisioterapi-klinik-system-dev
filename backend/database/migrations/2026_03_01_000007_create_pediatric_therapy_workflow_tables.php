<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. MASTER PENJAMIN / ASURANSI (GUARANTORS)
        if (!Schema::hasTable('guarantors')) {
            Schema::create('guarantors', function (Blueprint $table) {
                $table->id();
                $table->string('kode_penjamin', 30)->unique();
                $table->string('nama_penjamin');
                $table->string('tipe', 30)->default('asuransi_swasta'); // bpjs, asuransi_swasta, perusahaan, yayasan
                $table->string('kontak_klaim')->nullable();
                $table->string('email_klaim')->nullable();
                $table->text('alamat')->nullable();
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();
            });
        }

        // 2. MASTER KATALOG PAKET TERAPI
        if (!Schema::hasTable('therapy_packages')) {
            Schema::create('therapy_packages', function (Blueprint $table) {
                $table->id();
                $table->string('nama_paket');
                $table->unsignedBigInteger('layanan_id')->nullable();
                $table->integer('jumlah_sesi')->default(8);
                $table->decimal('harga_paket', 14, 2)->default(0);
                $table->integer('masa_berlaku_hari')->default(180); // 6 bulan default
                $table->text('deskripsi')->nullable();
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();

                $table->foreign('layanan_id')->references('id')->on('layanan')->onDelete('set null');
            });
        }

        // 3. JAMINAN ASURANSI PASIEN ANAK (PATIENT INSURANCES / GL)
        if (!Schema::hasTable('patient_insurances')) {
            Schema::create('patient_insurances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('profile_anak_id');
                $table->unsignedBigInteger('guarantor_id');
                $table->string('no_polis', 50);
                $table->string('no_surat_jaminan', 60)->nullable(); // GL / LoG
                $table->date('tgl_berlaku_mulai');
                $table->date('tgl_berlaku_selesai');
                $table->decimal('plafon_nominal', 14, 2)->default(0);
                $table->integer('kuota_sesi')->default(10);
                $table->integer('sisa_kuota_sesi')->default(10);
                $table->string('dokumen_gl_path')->nullable();
                $table->text('catatan')->nullable();
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();

                $table->foreign('profile_anak_id')->references('id')->on('profile_anak')->onDelete('cascade');
                $table->foreign('guarantor_id')->references('id')->on('guarantors')->onDelete('cascade');
                $table->index(['profile_anak_id', 'status_aktif']);
            });
        }

        // 4. THERAPY ORDERS (REKOMENDASI PROGRAM TERAPI OLEH DOKTER SP.A / SP.KFR)
        if (!Schema::hasTable('therapy_orders')) {
            Schema::create('therapy_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('profile_anak_id');
                $table->unsignedBigInteger('registrasi_anak_id')->nullable();
                $table->unsignedBigInteger('dokter_id')->nullable();
                $table->unsignedBigInteger('terapis_id')->nullable();
                $table->unsignedBigInteger('layanan_id')->nullable();
                $table->string('diagnosis_klinis');
                $table->string('icd10_kode', 20)->nullable();
                $table->string('frekuensi_mingguan', 50)->default('2x seminggu');
                $table->integer('total_sesi_rekomendasi')->default(8);
                $table->text('target_jangka_pendek')->nullable();
                $table->text('target_jangka_panjang')->nullable();
                $table->text('catatan_khusus')->nullable();
                $table->string('status', 30)->default('aktif'); // aktif, selesai, batal
                $table->timestamps();

                $table->foreign('profile_anak_id')->references('id')->on('profile_anak')->onDelete('cascade');
                $table->foreign('layanan_id')->references('id')->on('layanan')->onDelete('set null');
            });
        }

        // 5. ENHANCE registrasi_anak UNTUK DUAL-TRACK WORKFLOW & SESI
        Schema::table('registrasi_anak', function (Blueprint $table) {
            if (!Schema::hasColumn('registrasi_anak', 'payment_track')) {
                $table->string('payment_track', 30)->default('umum_single'); // umum_single, umum_package, asuransi
            }
            if (!Schema::hasColumn('registrasi_anak', 'guarantor_id')) {
                $table->unsignedBigInteger('guarantor_id')->nullable();
            }
            if (!Schema::hasColumn('registrasi_anak', 'patient_insurance_id')) {
                $table->unsignedBigInteger('patient_insurance_id')->nullable();
            }
            if (!Schema::hasColumn('registrasi_anak', 'patient_therapy_package_id')) {
                $table->unsignedBigInteger('patient_therapy_package_id')->nullable();
            }
            if (!Schema::hasColumn('registrasi_anak', 'therapy_order_id')) {
                $table->unsignedBigInteger('therapy_order_id')->nullable();
            }
            if (!Schema::hasColumn('registrasi_anak', 'antrian_nomor')) {
                $table->string('antrian_nomor', 20)->nullable(); // e.g. TW-01, OT-02, FT-01
            }
            if (!Schema::hasColumn('registrasi_anak', 'status_sesi')) {
                $table->string('status_sesi', 30)->default('terjadwal'); // terjadwal, menunggu, sedang_terapi, selesai_terapi, batal
            }
            if (!Schema::hasColumn('registrasi_anak', 'sesi_ke')) {
                $table->integer('sesi_ke')->default(1);
            }
            if (!Schema::hasColumn('registrasi_anak', 'total_sesi')) {
                $table->integer('total_sesi')->default(1);
            }
            if (!Schema::hasColumn('registrasi_anak', 'mood_anak')) {
                $table->string('mood_anak', 50)->nullable(); // kooperatif, tantrum, lelah, cemas, fokus_baik
            }
            if (!Schema::hasColumn('registrasi_anak', 'durasi_menit')) {
                $table->integer('durasi_menit')->default(60);
            }
            if (!Schema::hasColumn('registrasi_anak', 'home_program_catatan')) {
                $table->text('home_program_catatan')->nullable();
            }
            if (!Schema::hasColumn('registrasi_anak', 'jadwal_hari')) {
                $table->string('jadwal_hari', 50)->nullable();
            }
            if (!Schema::hasColumn('registrasi_anak', 'jadwal_jam')) {
                $table->string('jadwal_jam', 20)->nullable();
            }
            if (!Schema::hasColumn('registrasi_anak', 'parent_confirmed_at')) {
                $table->timestamp('parent_confirmed_at')->nullable();
            }
        });

        // 6. ENHANCE invoices UNTUK ASURANSI, EXCESS CLAIM & PIUTANG PENJAMIN (AR)
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'payment_track')) {
                $table->string('payment_track', 30)->default('umum_single'); // umum_single, umum_package, asuransi
            }
            if (!Schema::hasColumn('invoices', 'guarantor_id')) {
                $table->unsignedBigInteger('guarantor_id')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'patient_insurance_id')) {
                $table->unsignedBigInteger('patient_insurance_id')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'covered_amount')) {
                $table->decimal('covered_amount', 14, 2)->default(0); // Bagian yang ditanggung asuransi
            }
            if (!Schema::hasColumn('invoices', 'excess_amount')) {
                $table->decimal('excess_amount', 14, 2)->default(0); // Selisih yang harus dibayar orang tua (co-pay)
            }
            if (!Schema::hasColumn('invoices', 'ar_status')) {
                $table->string('ar_status', 30)->default('none'); // none, piutang_asuransi, klaim_diajukan, lunas_penjamin, ditolak
            }
            if (!Schema::hasColumn('invoices', 'nomor_klaim')) {
                $table->string('nomor_klaim', 60)->nullable();
            }
            if (!Schema::hasColumn('invoices', 'tgl_klaim_diajukan')) {
                $table->date('tgl_klaim_diajukan')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'tgl_klaim_lunas')) {
                $table->date('tgl_klaim_lunas')->nullable();
            }
        });

        // 7. TABEL AR KLAIM ASURANSI & REKONSILIASI
        if (!Schema::hasTable('insurance_claims')) {
            Schema::create('insurance_claims', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_pengajuan', 50)->unique();
                $table->unsignedBigInteger('guarantor_id');
                $table->unsignedBigInteger('invoice_id');
                $table->unsignedBigInteger('profile_anak_id');
                $table->string('nomor_gl', 60)->nullable();
                $table->decimal('nominal_diajukan', 14, 2);
                $table->decimal('nominal_disetujui', 14, 2)->default(0);
                $table->string('status', 30)->default('draft'); // draft, diajukan, disetujui, lunas, ditolak
                $table->date('tgl_pengajuan')->nullable();
                $table->date('tgl_pembayaran')->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->foreign('guarantor_id')->references('id')->on('guarantors')->onDelete('cascade');
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
                $table->foreign('profile_anak_id')->references('id')->on('profile_anak')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'payment_track', 'guarantor_id', 'patient_insurance_id',
                'covered_amount', 'excess_amount', 'ar_status',
                'nomor_klaim', 'tgl_klaim_diajukan', 'tgl_klaim_lunas'
            ]);
        });
        Schema::table('registrasi_anak', function (Blueprint $table) {
            $table->dropColumn([
                'payment_track', 'guarantor_id', 'patient_insurance_id',
                'patient_therapy_package_id', 'therapy_order_id',
                'antrian_nomor', 'status_sesi', 'sesi_ke', 'total_sesi',
                'mood_anak', 'durasi_menit', 'home_program_catatan',
                'jadwal_hari', 'jadwal_jam', 'parent_confirmed_at'
            ]);
        });
        Schema::dropIfExists('therapy_orders');
        Schema::dropIfExists('patient_insurances');
        Schema::dropIfExists('therapy_packages');
        Schema::dropIfExists('guarantors');
    }
};
