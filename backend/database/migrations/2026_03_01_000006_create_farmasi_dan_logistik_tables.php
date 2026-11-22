<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. KATEGORI PRODUK
        if (!Schema::hasTable('produk_kategori')) {
            Schema::create('produk_kategori', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kategori');
                $table->string('kode_kategori', 30)->unique();
                $table->string('jenis')->default('medis'); // medis, non_medis
                $table->timestamps();
            });
        }

        // 2. SUPPLIER
        if (!Schema::hasTable('supplier')) {
            Schema::create('supplier', function (Blueprint $table) {
                $table->id();
                $table->string('nama_supplier');
                $table->string('kontak_person')->nullable();
                $table->string('no_telp')->nullable();
                $table->string('email')->nullable();
                $table->text('alamat')->nullable();
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();
            });
        }

        // 3. MASTER PRODUK
        if (!Schema::hasTable('master_produk')) {
            Schema::create('master_produk', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('produk_kategori_id')->nullable();
                $table->string('kode_produk', 30)->unique();
                $table->string('nama_produk');
                $table->string('nama_generik')->nullable();
                $table->string('jenis')->default('bhp'); // obat, suplemen, bhp, alat_terapi, mainan_edukasi, atk
                $table->string('satuan', 20)->default('pcs'); // botol, strip, tablet, pcs, box, set
                $table->decimal('harga_beli', 14, 2)->default(0);
                $table->decimal('harga_jual', 14, 2)->default(0);
                $table->integer('stok_minimum')->default(5);
                $table->integer('total_stok')->default(0);
                $table->boolean('is_obat')->default(false);
                $table->string('lokasi_rak')->nullable();
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();

                $table->foreign('produk_kategori_id')->references('id')->on('produk_kategori')->onDelete('set null');
                $table->index(['jenis', 'status_aktif']);
            });
        }

        // 4. PRODUK BATCH (FEFO)
        if (!Schema::hasTable('produk_batch')) {
            Schema::create('produk_batch', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('master_produk_id');
                $table->string('nomor_batch', 50);
                $table->date('tanggal_kedaluwarsa');
                $table->integer('stok')->default(0);
                $table->decimal('harga_beli_satuan', 14, 2)->default(0);
                $table->timestamps();

                $table->foreign('master_produk_id')->references('id')->on('master_produk')->onDelete('cascade');
                $table->index(['master_produk_id', 'tanggal_kedaluwarsa']);
            });
        }

        // 5. KARTU STOK
        if (!Schema::hasTable('kartu_stok')) {
            Schema::create('kartu_stok', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('master_produk_id');
                $table->unsignedBigInteger('produk_batch_id')->nullable();
                $table->timestamp('tanggal')->useCurrent();
                $table->string('tipe_mutasi'); // masuk_po, keluar_resep, keluar_ruangan, penyesuaian, opname, retur
                $table->integer('jumlah');
                $table->integer('stok_sebelum');
                $table->integer('stok_sesudah');
                $table->string('referensi')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();

                $table->foreign('master_produk_id')->references('id')->on('master_produk')->onDelete('cascade');
                $table->index(['master_produk_id', 'tanggal']);
            });
        }

        // 6. STOK OPNAME
        if (!Schema::hasTable('stok_opname')) {
            Schema::create('stok_opname', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_opname', 40)->unique();
                $table->date('tanggal_opname');
                $table->unsignedBigInteger('petugas_id')->nullable();
                $table->string('status')->default('draft'); // draft, disetujui
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('stok_opname_items')) {
            Schema::create('stok_opname_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('stok_opname_id');
                $table->unsignedBigInteger('master_produk_id');
                $table->unsignedBigInteger('produk_batch_id')->nullable();
                $table->integer('stok_sistem');
                $table->integer('stok_fisik');
                $table->integer('selisih');
                $table->text('alasan')->nullable();
                $table->timestamps();

                $table->foreign('stok_opname_id')->references('id')->on('stok_opname')->onDelete('cascade');
            });
        }

        // 7. PURCHASE ORDERS
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_po', 40)->unique();
                $table->unsignedBigInteger('supplier_id');
                $table->date('tanggal_po');
                $table->decimal('total_estimasi', 14, 2)->default(0);
                $table->string('status')->default('draft'); // draft, dikirim, diterima_sebagian, selesai, batal
                $table->text('catatan')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('supplier_id')->references('id')->on('supplier')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('po_items')) {
            Schema::create('po_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('purchase_order_id');
                $table->unsignedBigInteger('master_produk_id');
                $table->integer('qty_pesan');
                $table->integer('qty_diterima')->default(0);
                $table->decimal('harga_satuan', 14, 2)->default(0);
                $table->decimal('subtotal', 14, 2)->default(0);
                $table->timestamps();

                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('po_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('stok_opname_items');
        Schema::dropIfExists('stok_opname');
        Schema::dropIfExists('kartu_stok');
        Schema::dropIfExists('produk_batch');
        Schema::dropIfExists('master_produk');
        Schema::dropIfExists('supplier');
        Schema::dropIfExists('produk_kategori');
    }
};
