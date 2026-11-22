<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. INVOICES
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_invoice', 40)->unique();
                $table->unsignedBigInteger('profile_anak_id');
                $table->unsignedBigInteger('registrasi_anak_id')->nullable();
                $table->decimal('subtotal', 14, 2)->default(0);
                $table->decimal('diskon', 14, 2)->default(0);
                $table->decimal('pajak', 14, 2)->default(0);
                $table->decimal('total_tagihan', 14, 2)->default(0);
                $table->decimal('total_terbayar', 14, 2)->default(0);
                $table->decimal('sisa_tagihan', 14, 2)->default(0);
                $table->string('status')->default('belum_bayar'); // draft, belum_bayar, bayar_sebagian, lunas, dibatalkan
                $table->string('jenis_penjamin')->default('Umum'); // Umum, Asuransi, BPJS, Yayasan
                $table->string('nomor_penjamin')->nullable();
                $table->text('catatan')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->softDeletes();
                $table->timestamps();

                $table->foreign('profile_anak_id')->references('id')->on('profile_anak')->onDelete('cascade');
                $table->index(['profile_anak_id', 'status']);
                $table->index('nomor_invoice');
            });
        }

        // 2. INVOICE ITEMS
        if (!Schema::hasTable('invoice_items')) {
            Schema::create('invoice_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id');
                $table->string('tipe_item'); // layanan_terapi, paket_terapi, produk_farmasi, bhp, administrasi
                $table->unsignedBigInteger('item_id')->nullable();
                $table->string('deskripsi');
                $table->integer('qty')->default(1);
                $table->decimal('harga_satuan', 14, 2)->default(0);
                $table->decimal('diskon', 14, 2)->default(0);
                $table->decimal('subtotal', 14, 2)->default(0);
                $table->timestamps();

                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            });
        }

        // 3. SHIFT KASIR (CLOSING & REKONSILIASI)
        if (!Schema::hasTable('shift_kasir')) {
            Schema::create('shift_kasir', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('kasir_id');
                $table->timestamp('waktu_buka')->useCurrent();
                $table->timestamp('waktu_tutup')->nullable();
                $table->decimal('saldo_awal', 14, 2)->default(0);
                $table->decimal('total_tunai_sistem', 14, 2)->default(0);
                $table->decimal('total_non_tunai_sistem', 14, 2)->default(0);
                $table->decimal('saldo_akhir_fisik', 14, 2)->nullable();
                $table->decimal('selisih', 14, 2)->nullable();
                $table->string('status')->default('open'); // open, closed
                $table->text('catatan_shift')->nullable();
                $table->timestamps();

                $table->index(['kasir_id', 'status']);
            });
        }

        // 4. PEMBAYARAN TRANSAKSI
        if (!Schema::hasTable('pembayaran_transaksi')) {
            Schema::create('pembayaran_transaksi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id');
                $table->string('nomor_transaksi', 40)->unique();
                $table->unsignedBigInteger('kasir_id')->nullable();
                $table->unsignedBigInteger('shift_kasir_id')->nullable();
                $table->string('metode_bayar')->default('tunai'); // tunai, transfer, qris, kartu_debit, kartu_kredit
                $table->decimal('jumlah_bayar', 14, 2);
                $table->decimal('kembalian', 14, 2)->default(0);
                $table->string('nomor_referensi')->nullable();
                $table->string('bukti_bayar')->nullable();
                $table->string('status')->default('sukses'); // sukses, verifikasi, batal
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            });
        }

        // 5. PAKET TERAPI PASIEN
        if (!Schema::hasTable('paket_terapi_pasien')) {
            Schema::create('paket_terapi_pasien', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('profile_anak_id');
                $table->unsignedBigInteger('layanan_id');
                $table->unsignedBigInteger('invoice_id')->nullable();
                $table->string('nomor_paket', 40)->unique();
                $table->string('nama_paket');
                $table->integer('total_sesi');
                $table->integer('sesi_terpakai')->default(0);
                $table->integer('sisa_sesi');
                $table->date('tanggal_mulai');
                $table->date('tanggal_berakhir')->nullable();
                $table->string('status')->default('aktif'); // aktif, habis, kedaluwarsa
                $table->timestamps();

                $table->foreign('profile_anak_id')->references('id')->on('profile_anak')->onDelete('cascade');
                $table->index(['profile_anak_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_terapi_pasien');
        Schema::dropIfExists('pembayaran_transaksi');
        Schema::dropIfExists('shift_kasir');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
