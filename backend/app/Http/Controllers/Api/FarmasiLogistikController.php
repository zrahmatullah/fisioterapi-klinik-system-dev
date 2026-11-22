<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterProduk;
use App\Models\ProdukBatch;
use App\Models\KartuStok;
use App\Models\PurchaseRequisition;
use App\Models\PrItem;
use App\Models\PurchaseOrder;
use App\Models\PoItem;
use App\Models\GoodsReceipt;
use App\Models\GrnItem;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FarmasiLogistikController extends Controller
{
    // =========================================================================
    // 1. MASTER PRODUK & KARTU STOK (FEFO)
    // =========================================================================

    public function indexProduk(Request $request)
    {
        $query = MasterProduk::with(['batches' => function ($q) {
            $q->where('stok', '>', 0)->orderBy('tanggal_kedaluwarsa', 'asc');
        }]);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_produk', 'ILIKE', "%{$s}%")
                  ->orWhere('kode_produk', 'ILIKE', "%{$s}%")
                  ->orWhere('nama_generik', 'ILIKE', "%{$s}%");
            });
        }

        $produk = $query->paginate($request->query('per_page', 20));
        return response()->json($produk);
    }

    public function storeProduk(Request $request)
    {
        $request->validate([
            'kode_produk' => 'required|string|unique:master_produk,kode_produk,' . ($request->id ?? 'NULL') . ',id',
            'nama_produk' => 'required|string',
            'jenis' => 'required|string',
            'satuan' => 'required|string',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
        ]);

        $produk = MasterProduk::updateOrCreate(['id' => $request->id], $request->all());
        return response()->json(['message' => 'Produk berhasil disimpan', 'produk' => $produk], 201);
    }

    public function tambahBatch(Request $request)
    {
        $request->validate([
            'master_produk_id' => 'required|exists:master_produk,id',
            'nomor_batch' => 'required|string',
            'tanggal_kedaluwarsa' => 'required|date',
            'stok' => 'required|integer|min:1',
            'harga_beli_satuan' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $produk = MasterProduk::findOrFail($request->master_produk_id);
            $batch = ProdukBatch::create($request->all());

            $stokSebelum = $produk->total_stok;
            $stokSesudah = $stokSebelum + $request->stok;
            $produk->update(['total_stok' => $stokSesudah]);

            KartuStok::create([
                'master_produk_id' => $produk->id,
                'produk_batch_id' => $batch->id,
                'tanggal' => now(),
                'tipe_mutasi' => 'masuk_penyesuaian',
                'jumlah' => $request->stok,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'referensi' => "Batch Manual: {$request->nomor_batch}",
                'user_id' => auth('api')->id(),
            ]);

            return response()->json([
                'message' => 'Batch dan stok berhasil ditambahkan',
                'batch' => $batch,
                'produk' => $produk->fresh(),
            ], 201);
        });
    }

    public function kartuStok($masterProdukId)
    {
        $mutasi = KartuStok::where('master_produk_id', $masterProdukId)
            ->with(['batch', 'user.userProfile'])
            ->orderBy('tanggal', 'desc')
            ->limit(50)
            ->get();
        return response()->json($mutasi);
    }

    // =========================================================================
    // 2. MASTER SUPPLIER
    // =========================================================================

    public function indexSuppliers(Request $request)
    {
        $query = Supplier::orderBy('nama_supplier', 'asc');
        if ($request->has('active_only') && filter_var($request->active_only, FILTER_VALIDATE_BOOLEAN)) {
            $query->where('status_aktif', true);
        }
        $suppliers = $query->get();
        return response()->json($suppliers);
    }

    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'kontak_person' => 'nullable|string',
            'no_telp' => 'nullable|string',
            'email' => 'nullable|email',
            'alamat' => 'nullable|string',
            'status_aktif' => 'nullable|boolean',
        ]);

        if (!isset($validated['status_aktif'])) {
            $validated['status_aktif'] = true;
        }

        $supplier = Supplier::create($validated);
        return response()->json(['message' => 'Supplier berhasil ditambahkan', 'supplier' => $supplier], 201);
    }

    public function updateSupplier(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'kontak_person' => 'nullable|string',
            'no_telp' => 'nullable|string',
            'email' => 'nullable|email',
            'alamat' => 'nullable|string',
            'status_aktif' => 'nullable|boolean',
        ]);

        $supplier->update($validated);
        return response()->json(['message' => 'Supplier berhasil diperbarui', 'supplier' => $supplier]);
    }

    public function deleteSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return response()->json(['message' => 'Supplier berhasil dihapus']);
    }

    // =========================================================================
    // 3. LOGISTIK STAGE 1: PURCHASE REQUISITION (PR - PERMINTAAN PEMBELIAN)
    // =========================================================================

    public function indexPr(Request $request)
    {
        $query = PurchaseRequisition::with(['items.masterProduk', 'creator', 'approver'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate($request->query('per_page', 15)));
    }

    public function storePr(Request $request)
    {
        $request->validate([
            'unit_pemohon' => 'required|string',
            'tanggal_pr' => 'required|date',
            'alasan_kebutuhan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.master_produk_id' => 'required|exists:master_produk,id',
            'items.*.qty_diminta' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $today = date('Ymd');
            $count = PurchaseRequisition::whereDate('created_at', now())->count() + 1;
            $noPr = "PR-{$today}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

            $pr = PurchaseRequisition::create([
                'nomor_pr' => $noPr,
                'unit_pemohon' => $request->unit_pemohon,
                'tanggal_pr' => $request->tanggal_pr,
                'alasan_kebutuhan' => $request->alasan_kebutuhan,
                'status' => 'diajukan',
                'created_by' => auth('api')->user()?->user_profile_id ?? 1,
            ]);

            foreach ($request->items as $it) {
                PrItem::create([
                    'purchase_requisition_id' => $pr->id,
                    'master_produk_id' => $it['master_produk_id'],
                    'qty_diminta' => $it['qty_diminta'],
                    'qty_disetujui' => $it['qty_diminta'], // Default approve requested qty
                    'keterangan' => $it['keterangan'] ?? null,
                ]);
            }

            return response()->json([
                'message' => 'Permintaan Pengadaan (PR) berhasil diajukan.',
                'data' => $pr->load('items.masterProduk'),
            ], 201);
        });
    }

    public function approvePr(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_approval' => 'nullable|string',
        ]);

        $pr = PurchaseRequisition::findOrFail($id);
        $pr->status = $request->status;
        $pr->approved_by = auth('api')->user()?->user_profile_id ?? 1;
        $pr->approved_at = now();
        $pr->catatan_approval = $request->catatan_approval;
        $pr->save();

        return response()->json([
            'message' => "Permintaan Pengadaan {$pr->nomor_pr} berhasil " . ($request->status === 'disetujui' ? 'disetujui' : 'ditolak') . ".",
            'data' => $pr,
        ]);
    }

    // =========================================================================
    // 4. LOGISTIK STAGE 2: PURCHASE ORDER (PO - SURAT PESANAN RESMI)
    // =========================================================================

    public function indexPurchaseOrders(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'items.masterProduk', 'purchaseRequisition'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate($request->query('per_page', 15)));
    }

    public function storePurchaseOrder(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'tanggal_po' => 'required|date',
            'purchase_requisition_id' => 'nullable|exists:purchase_requisitions,id',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.master_produk_id' => 'required|exists:master_produk,id',
            'items.*.qty_pesan' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $today = date('Ymd');
            $count = PurchaseOrder::whereDate('created_at', now())->count() + 1;
            $nomorPo = "PO-{$today}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

            $totalEstimasi = 0;
            foreach ($request->items as $it) {
                $totalEstimasi += ($it['qty_pesan'] * $it['harga_satuan']);
            }

            $po = PurchaseOrder::create([
                'nomor_po' => $nomorPo,
                'purchase_requisition_id' => $request->purchase_requisition_id,
                'supplier_id' => $request->supplier_id,
                'tanggal_po' => $request->tanggal_po,
                'total_estimasi' => $totalEstimasi,
                'status' => 'dikirim',
                'catatan' => $request->catatan,
                'created_by' => auth('api')->user()?->user_profile_id ?? 1,
            ]);

            foreach ($request->items as $it) {
                PoItem::create([
                    'purchase_order_id' => $po->id,
                    'master_produk_id' => $it['master_produk_id'],
                    'qty_pesan' => $it['qty_pesan'],
                    'qty_diterima' => 0,
                    'harga_satuan' => $it['harga_satuan'],
                    'subtotal' => ($it['qty_pesan'] * $it['harga_satuan']),
                ]);
            }

            // Update linked PR status
            if ($request->purchase_requisition_id) {
                PurchaseRequisition::where('id', $request->purchase_requisition_id)
                    ->update(['status' => 'diproses_po']);
            }

            return response()->json([
                'message' => 'Surat Pesanan (PO) berhasil diterbitkan ke supplier.',
                'data' => $po->load(['supplier', 'items.masterProduk']),
            ], 201);
        });
    }

    // =========================================================================
    // 5. LOGISTIK STAGE 3: GOODS RECEIPT NOTE (GRN) & ATOMIC STOCK / KARTU STOK
    // =========================================================================

    public function indexGrn(Request $request)
    {
        $query = GoodsReceipt::with(['supplier', 'purchaseOrder', 'items.masterProduk', 'penerima'])
            ->orderBy('created_at', 'desc');

        return response()->json($query->paginate($request->query('per_page', 15)));
    }

    public function storeGrn(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'nomor_surat_jalan' => 'required|string',
            'nomor_faktur_supplier' => 'nullable|string',
            'tanggal_terima' => 'required|date',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.master_produk_id' => 'required|exists:master_produk,id',
            'items.*.nomor_batch' => 'required|string',
            'items.*.tanggal_kedaluwarsa' => 'required|date',
            'items.*.qty_diterima' => 'required|integer|min:1',
            'items.*.harga_beli_satuan' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $today = date('Ymd');
            $count = GoodsReceipt::whereDate('created_at', now())->count() + 1;
            $noGrn = "GRN-{$today}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

            $totalNilai = 0;
            foreach ($request->items as $it) {
                $totalNilai += ($it['qty_diterima'] * $it['harga_beli_satuan']);
            }

            $currentUserId = auth('api')->user()?->user_profile_id ?? 1;

            $grn = GoodsReceipt::create([
                'nomor_grn' => $noGrn,
                'purchase_order_id' => $request->purchase_order_id,
                'supplier_id' => $request->supplier_id,
                'nomor_surat_jalan' => $request->nomor_surat_jalan,
                'nomor_faktur_supplier' => $request->nomor_faktur_supplier,
                'tanggal_terima' => $request->tanggal_terima,
                'penerima_id' => $currentUserId,
                'status' => 'diterima',
                'total_nilai_barang' => $totalNilai,
                'catatan' => $request->catatan,
            ]);

            $poNomor = 'DIRECT';
            if ($request->purchase_order_id) {
                $po = PurchaseOrder::find($request->purchase_order_id);
                $poNomor = $po?->nomor_po ?? 'PO';
            }

            // ATOMIC STOCK & KARTU STOK MUTATION
            foreach ($request->items as $it) {
                $itemSubtotal = ($it['qty_diterima'] * $it['harga_beli_satuan']);

                // 1. Create GRN line item
                GrnItem::create([
                    'goods_receipt_id' => $grn->id,
                    'master_produk_id' => $it['master_produk_id'],
                    'po_item_id' => $it['po_item_id'] ?? null,
                    'nomor_batch' => $it['nomor_batch'],
                    'tanggal_kedaluwarsa' => $it['tanggal_kedaluwarsa'],
                    'qty_diterima' => $it['qty_diterima'],
                    'harga_beli_satuan' => $it['harga_beli_satuan'],
                    'subtotal' => $itemSubtotal,
                ]);

                // 2. Lock & update master_produk stock
                $produk = MasterProduk::lockForUpdate()->findOrFail($it['master_produk_id']);
                $stokSebelum = $produk->total_stok;
                $stokSesudah = $stokSebelum + $it['qty_diterima'];
                $produk->total_stok = $stokSesudah;
                $produk->harga_beli = $it['harga_beli_satuan']; // Update latest purchase price
                $produk->save();

                // 3. Upsert batch stock (FEFO tracking)
                $batch = ProdukBatch::firstOrCreate(
                    [
                        'master_produk_id' => $produk->id,
                        'nomor_batch' => $it['nomor_batch'],
                    ],
                    [
                        'tanggal_kedaluwarsa' => $it['tanggal_kedaluwarsa'],
                        'stok' => 0,
                        'harga_beli_satuan' => $it['harga_beli_satuan'],
                    ]
                );
                $batch->increment('stok', $it['qty_diterima']);

                // 4. Record Kartu Stok Mutasi
                KartuStok::create([
                    'master_produk_id' => $produk->id,
                    'produk_batch_id' => $batch->id,
                    'tanggal' => now(),
                    'tipe_mutasi' => 'masuk_grn',
                    'jumlah' => $it['qty_diterima'],
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'referensi' => "GRN: {$noGrn} | SJ: {$request->nomor_surat_jalan} | PO: {$poNomor} | Batch: {$it['nomor_batch']}",
                    'user_id' => $currentUserId,
                ]);

                // 5. Update PO Item received quantity if linked
                if (!empty($it['po_item_id'])) {
                    PoItem::where('id', $it['po_item_id'])->increment('qty_diterima', $it['qty_diterima']);
                }
            }

            // Update parent PO status if all items received
            if ($request->purchase_order_id) {
                $poItems = PoItem::where('purchase_order_id', $request->purchase_order_id)->get();
                $allReceived = $poItems->every(fn($item) => $item->qty_diterima >= $item->qty_pesan);
                PurchaseOrder::where('id', $request->purchase_order_id)
                    ->update(['status' => $allReceived ? 'selesai' : 'diterima_sebagian']);
            }

            return response()->json([
                'message' => 'Penerimaan Barang (GRN) berhasil dicatat. Stok fisik dan kartu stok otomatis dimutasi.',
                'data' => $grn->load(['supplier', 'purchaseOrder', 'items.masterProduk']),
            ], 201);
        });
    }
}
