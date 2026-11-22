<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =========================================================================
        // 1. LOGISTIK: PURCHASE REQUISITION (PR - PERMINTAAN PENGADAAN UNIT)
        // =========================================================================
        if (!Schema::hasTable('purchase_requisitions')) {
            Schema::create('purchase_requisitions', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_pr', 50)->unique();
                $table->string('unit_pemohon'); // Instalasi Farmasi, Poli Terapi Wicara, Poli Okupasi, Gudang Umum, Medis
                $table->date('tanggal_pr');
                $table->text('alasan_kebutuhan')->nullable();
                $table->string('status', 30)->default('draft'); // draft, diajukan, disetujui, ditolak, diproses_po
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('catatan_approval')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pr_items')) {
            Schema::create('pr_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('purchase_requisition_id');
                $table->unsignedBigInteger('master_produk_id');
                $table->integer('qty_diminta');
                $table->integer('qty_disetujui')->default(0);
                $table->text('keterangan')->nullable();
                $table->timestamps();

                $table->foreign('purchase_requisition_id')->references('id')->on('purchase_requisitions')->onDelete('cascade');
                $table->foreign('master_produk_id')->references('id')->on('master_produk')->onDelete('cascade');
            });
        }

        // Link PO to PR if available
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'purchase_requisition_id')) {
                $table->unsignedBigInteger('purchase_requisition_id')->nullable();
            }
        });

        // =========================================================================
        // 2. LOGISTIK: GOODS RECEIPT NOTE (GRN - BUKTI PENERIMAAN BARANG DARI SUPPLIER)
        // =========================================================================
        if (!Schema::hasTable('goods_receipts')) {
            Schema::create('goods_receipts', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_grn', 50)->unique();
                $table->unsignedBigInteger('purchase_order_id')->nullable();
                $table->unsignedBigInteger('supplier_id');
                $table->string('nomor_surat_jalan', 60);
                $table->string('nomor_faktur_supplier', 60)->nullable();
                $table->date('tanggal_terima');
                $table->unsignedBigInteger('penerima_id')->nullable(); // Petugas gudang
                $table->string('status', 30)->default('diterima'); // diterima, diverifikasi, batal
                $table->decimal('total_nilai_barang', 14, 2)->default(0);
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
                $table->foreign('supplier_id')->references('id')->on('supplier')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('grn_items')) {
            Schema::create('grn_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('goods_receipt_id');
                $table->unsignedBigInteger('master_produk_id');
                $table->unsignedBigInteger('po_item_id')->nullable();
                $table->string('nomor_batch', 50);
                $table->date('tanggal_kedaluwarsa');
                $table->integer('qty_diterima');
                $table->decimal('harga_beli_satuan', 14, 2)->default(0);
                $table->decimal('subtotal', 14, 2)->default(0);
                $table->timestamps();

                $table->foreign('goods_receipt_id')->references('id')->on('goods_receipts')->onDelete('cascade');
                $table->foreign('master_produk_id')->references('id')->on('master_produk')->onDelete('cascade');
            });
        }

        // =========================================================================
        // 3. ROLE MATRIX AKSES (V, C, U, D - VIEW, CREATE, UPDATE, DELETE)
        // =========================================================================
        if (!Schema::hasTable('role_module_permissions')) {
            Schema::create('role_module_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('role_id');
                $table->string('module_code', 50);
                $table->string('module_name');
                $table->boolean('can_view')->default(false);
                $table->boolean('can_create')->default(false);
                $table->boolean('can_update')->default(false);
                $table->boolean('can_delete')->default(false);
                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('role')->onDelete('cascade');
                $table->unique(['role_id', 'module_code']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_module_permissions');
        Schema::dropIfExists('grn_items');
        Schema::dropIfExists('goods_receipts');
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('purchase_requisition_id');
        });
        Schema::dropIfExists('pr_items');
        Schema::dropIfExists('purchase_requisitions');
    }
};
