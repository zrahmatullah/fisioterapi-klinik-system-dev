<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-y-auto custom-scrollbar w-full pr-1">

    <!-- TAB SELECTOR -->
    <div class="bg-white border border-slate-200 rounded-xl p-2.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex items-center justify-between flex-wrap gap-2">
      <div class="flex items-center gap-1.5 flex-wrap">
        <button
          type="button"
          @click="activeTab = 'katalog'"
          class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
          :class="activeTab === 'katalog' ? 'bg-[#0F766E] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
        >
          <i class="pi pi-box text-xs"></i>
          <span>1. Katalog Stok & FEFO</span>
        </button>

        <button
          type="button"
          @click="activeTab = 'pr'"
          class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
          :class="activeTab === 'pr' ? 'bg-[#0F766E] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
        >
          <i class="pi pi-file-edit text-xs"></i>
          <span>2. Permintaan Pengadaan (PR)</span>
        </button>

        <button
          type="button"
          @click="activeTab = 'po'"
          class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
          :class="activeTab === 'po' ? 'bg-[#0F766E] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
        >
          <i class="pi pi-shopping-cart text-xs"></i>
          <span>3. Surat Pesanan (PO)</span>
        </button>

        <button
          type="button"
          @click="activeTab = 'grn'"
          class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
          :class="activeTab === 'grn' ? 'bg-[#0F766E] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
        >
          <i class="pi pi-inbox text-xs"></i>
          <span>4. Penerimaan Barang (GRN)</span>
        </button>

        <button
          type="button"
          @click="activeTab = 'suppliers'"
          class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
          :class="activeTab === 'suppliers' ? 'bg-[#0F766E] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
        >
          <i class="pi pi-truck text-xs"></i>
          <span>5. Supplier Rekanan</span>
        </button>
      </div>

      <div class="text-[11px] text-[#0F766E] font-medium bg-[#E6F2F5] px-2.5 py-1 rounded-full border border-[#CBE3E9] flex items-center gap-1">
        <i class="pi pi-sync text-[10px]"></i>
        <span>Alur Logistik FEFO & Kartu Stok Terintegrasi</span>
      </div>
    </div>

    <!-- ================= TAB 1: KATALOG STOK & FEFO ================= -->
    <div v-if="activeTab === 'katalog'" class="space-y-4">
      <!-- TOP KPI TILES -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
          <span class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider block">Total Katalog Produk</span>
          <h3 class="text-lg font-bold text-[#0F172A] tabular-nums mt-1 leading-tight">{{ produkList.length }} Item</h3>
          <p class="text-[11px] text-[#64748B] mt-0.5">BHP, Alat Terapi, & Suplemen</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
          <span class="text-[11px] font-medium text-[#DC2626] uppercase tracking-wider block">Stok Kritis (&lt;= Min)</span>
          <h3 class="text-lg font-bold text-[#DC2626] tabular-nums mt-1 leading-tight">
            {{ produkList.filter(p => p.total_stok <= (p.stok_minimum || 5)).length }} Item
          </h3>
          <p class="text-[11px] text-[#DC2626] mt-0.5 font-medium">Segera ajukan PR pengadaan</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
          <span class="text-[11px] font-medium text-[#0F766E] uppercase tracking-wider block">Metode Pengeluaran</span>
          <h3 class="text-base font-bold text-[#0F766E] mt-1 leading-tight">FEFO Prioritas</h3>
          <p class="text-[11px] text-[#64748B] mt-0.5">First Expired, First Out</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
          <span class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider block">Total Fisik Persediaan</span>
          <h3 class="text-lg font-bold text-[#0F172A] tabular-nums mt-1 leading-tight">
            {{ produkList.reduce((sum, p) => sum + Number(p.total_stok || 0), 0) }} Satuan
          </h3>
          <p class="text-[11px] text-[#64748B] mt-0.5">Terdaftar di master gudang</p>
        </div>
      </div>

      <!-- MAIN PRODUCT CATALOG CARD -->
      <div class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div class="flex items-center gap-2.5 flex-wrap">
            <div class="relative w-72">
              <i class="pi pi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari nama produk atau kode..."
                class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
                @input="loadProduk"
              />
            </div>
            <select
              v-model="filterJenis"
              class="text-xs border border-slate-300 rounded-lg px-3 py-1.5 bg-white text-slate-700 outline-none focus:border-[#0F766E]"
              @change="loadProduk"
            >
              <option value="">Semua Kategori</option>
              <option value="bhp">Bahan Habis Pakai (BHP)</option>
              <option value="alat_terapi">Alat & Instrumen Terapi</option>
              <option value="mainan_edukasi">Mainan Edukasi Sensori</option>
              <option value="suplemen">Obat & Suplemen</option>
            </select>
          </div>

          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="openAddBatchModal"
              class="px-3 py-1.5 rounded-lg bg-[#E6F2F5] hover:bg-[#d5eaf0] text-[#0F766E] border border-[#CBE3E9] text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer"
            >
              <i class="pi pi-plus text-xs"></i>
              <span>Tambah Batch Manual</span>
            </button>
            <button
              type="button"
              @click="openAddProductModal"
              class="btn-primary-clinical text-xs py-1.5 px-3"
            >
              <i class="pi pi-plus text-xs"></i>
              <span>Tambah Produk</span>
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-[13px] text-left">
            <thead class="bg-[#F8FAFC] text-xs font-semibold text-slate-600 border-b border-slate-100">
              <tr>
                <th class="py-2.5 px-3">Kode & Nama Produk</th>
                <th class="py-2.5 px-3">Kategori</th>
                <th class="py-2.5 px-3">Harga Beli / Jual</th>
                <th class="py-2.5 px-3 text-center">Total Stok</th>
                <th class="py-2.5 px-3">Batch Terdekat (FEFO)</th>
                <th class="py-2.5 px-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="loading" class="text-center">
                <td colspan="6" class="py-10 text-slate-400">
                  <i class="pi pi-spin pi-spinner text-xl text-[#0F766E]"></i>
                  <p class="mt-2 text-xs">Memuat katalog persediaan...</p>
                </td>
              </tr>
              <tr v-else-if="produkList.length === 0" class="text-center">
                <td colspan="6" class="py-10 text-slate-400 text-xs">Tidak ada produk yang cocok dengan pencarian.</td>
              </tr>
              <tr v-for="p in produkList" :key="p.id" class="hover:bg-slate-50/80 transition">
                <td class="py-2.5 px-3">
                  <p class="font-bold text-slate-900 leading-tight">{{ p.nama_produk }}</p>
                  <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ p.kode_produk }} · {{ p.satuan }}</p>
                </td>
                <td class="py-2.5 px-3">
                  <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 capitalize">
                    {{ (p.jenis || '').replace('_', ' ') }}
                  </span>
                </td>
                <td class="py-2.5 px-3 text-xs tabular-nums">
                  <p class="text-slate-400">Beli: {{ formatRupiah(p.harga_beli) }}</p>
                  <p class="font-bold text-slate-800">Jual: {{ formatRupiah(p.harga_jual) }}</p>
                </td>
                <td class="py-2.5 px-3 text-center">
                  <span
                    class="px-2.5 py-0.5 rounded-full text-xs font-bold tabular-nums border"
                    :class="p.total_stok <= (p.stok_minimum || 5) ? 'bg-red-50 text-[#DC2626] border-red-200' : 'bg-[#DCFCE7] text-[#15803D] border-emerald-200'"
                  >
                    {{ p.total_stok }} {{ p.satuan }}
                  </span>
                </td>
                <td class="py-2.5 px-3 text-xs">
                  <div v-if="p.batches && p.batches.length > 0">
                    <span class="font-mono text-[#0F766E] font-semibold text-[11px] block">{{ p.batches[0].nomor_batch }}</span>
                    <span class="text-slate-400 text-[10px]">Exp: {{ formatDate(p.batches[0].tanggal_kedaluwarsa) }} ({{ p.batches[0].stok }} {{ p.satuan }})</span>
                  </div>
                  <span v-else class="text-slate-400 text-[11px]">-</span>
                </td>
                <td class="py-2.5 px-3 text-right">
                  <button
                    type="button"
                    @click="viewKartuStok(p)"
                    class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-[#E6F2F5] text-slate-700 hover:text-[#0F766E] font-medium text-xs transition inline-flex items-center gap-1 shadow-sm cursor-pointer"
                  >
                    <i class="pi pi-list text-[11px] text-slate-500"></i>
                    <span>Kartu Stok</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ================= TAB 2: PERMINTAAN PENGADAAN (PR) ================= -->
    <div v-else-if="activeTab === 'pr'" class="space-y-4">
      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h3 class="text-sm font-bold text-slate-900 leading-tight">Daftar Purchase Requisition (PR)</h3>
          <p class="text-xs text-slate-500 mt-0.5">Permintaan pengadaan persediaan dari unit klinik sebelum diterbitkan PO</p>
        </div>
        <button
          type="button"
          @click="showPrModal = true"
          class="btn-primary-clinical text-xs py-2 px-3.5"
        >
          <i class="pi pi-plus text-xs"></i>
          <span>Buat PR Pengadaan Baru</span>
        </button>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-[13px] text-left">
          <thead class="bg-[#F8FAFC] text-xs font-semibold text-slate-600 border-b border-slate-200">
            <tr>
              <th class="py-2.5 px-3.5">Nomor PR</th>
              <th class="py-2.5 px-3.5">Unit Pemohon</th>
              <th class="py-2.5 px-3.5">Tanggal PR</th>
              <th class="py-2.5 px-3.5">Alasan Kebutuhan</th>
              <th class="py-2.5 px-3.5">Item Diminta</th>
              <th class="py-2.5 px-3.5 text-center">Status</th>
              <th class="py-2.5 px-3.5 text-right">Approval Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="prList.length === 0" class="text-center">
              <td colspan="7" class="py-8 text-slate-400 text-xs">Belum ada pengajuan Purchase Requisition (PR).</td>
            </tr>
            <tr v-for="pr in prList" :key="pr.id" class="hover:bg-slate-50/80 transition">
              <td class="py-2.5 px-3.5 font-mono font-bold text-[#0F766E]">{{ pr.nomor_pr }}</td>
              <td class="py-2.5 px-3.5 font-semibold text-slate-800">{{ pr.unit_pemohon }}</td>
              <td class="py-2.5 px-3.5 text-slate-600 tabular-nums">{{ formatDate(pr.tanggal_pr) }}</td>
              <td class="py-2.5 px-3.5 text-slate-600 text-xs">{{ pr.alasan_kebutuhan || '-' }}</td>
              <td class="py-2.5 px-3.5 text-xs">
                <span class="font-semibold text-slate-800">{{ pr.items?.length || 0 }} Item Produk</span>
                <p v-if="pr.items && pr.items[0]" class="text-[11px] text-slate-500">
                  {{ pr.items[0].master_produk?.nama_produk }} ({{ pr.items[0].qty_diminta }}x)
                </p>
              </td>
              <td class="py-2.5 px-3.5 text-center">
                <span
                  class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                  :class="{
                    'bg-amber-50 text-amber-700 border border-amber-200': pr.status === 'diajukan',
                    'bg-emerald-50 text-emerald-700 border border-emerald-200': pr.status === 'disetujui',
                    'bg-sky-50 text-sky-700 border border-sky-200': pr.status === 'diproses_po',
                    'bg-red-50 text-red-700 border border-red-200': pr.status === 'ditolak'
                  }"
                >
                  {{ pr.status }}
                </span>
              </td>
              <td class="py-2.5 px-3.5 text-right">
                <div v-if="pr.status === 'diajukan'" class="flex items-center justify-end gap-1.5">
                  <button
                    type="button"
                    @click="approvePr(pr.id, 'disetujui')"
                    class="px-2.5 py-1 rounded bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-medium inline-flex items-center gap-1 transition"
                  >
                    <i class="pi pi-check text-[10px]"></i> Setujui
                  </button>
                  <button
                    type="button"
                    @click="approvePr(pr.id, 'ditolak')"
                    class="px-2.5 py-1 rounded bg-white border border-red-300 text-red-600 hover:bg-red-50 text-xs font-medium inline-flex items-center gap-1 transition"
                  >
                    Tolak
                  </button>
                </div>
                <div v-else-if="pr.status === 'disetujui'" class="flex items-center justify-end">
                  <button
                    type="button"
                    @click="createPoFromPr(pr)"
                    class="px-2.5 py-1 rounded bg-sky-600 hover:bg-sky-700 text-white text-xs font-medium inline-flex items-center gap-1 transition"
                  >
                    <i class="pi pi-shopping-cart text-[10px]"></i> Terbitkan PO
                  </button>
                </div>
                <span v-else class="text-xs text-slate-400 font-mono">-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================= TAB 3: SURAT PESANAN (PO) ================= -->
    <div v-else-if="activeTab === 'po'" class="space-y-4">
      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h3 class="text-sm font-bold text-slate-900 leading-tight">Daftar Purchase Order (PO - Surat Pesanan)</h3>
          <p class="text-xs text-slate-500 mt-0.5">Surat pesanan resmi yang dikirim ke distributor/supplier farmasi & logistik</p>
        </div>
        <button
          type="button"
          @click="showPoModal = true"
          class="btn-primary-clinical text-xs py-2 px-3.5"
        >
          <i class="pi pi-plus text-xs"></i>
          <span>Buat PO Baru</span>
        </button>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-[13px] text-left">
          <thead class="bg-[#F8FAFC] text-xs font-semibold text-slate-600 border-b border-slate-200">
            <tr>
              <th class="py-2.5 px-3.5">Nomor PO</th>
              <th class="py-2.5 px-3.5">Supplier / PBF</th>
              <th class="py-2.5 px-3.5">Tanggal Pesan</th>
              <th class="py-2.5 px-3.5">Total Estimasi</th>
              <th class="py-2.5 px-3.5">PR Terkait</th>
              <th class="py-2.5 px-3.5 text-center">Status</th>
              <th class="py-2.5 px-3.5 text-right">Aksi GRN</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="poList.length === 0" class="text-center">
              <td colspan="7" class="py-8 text-slate-400 text-xs">Belum ada data Purchase Order.</td>
            </tr>
            <tr v-for="po in poList" :key="po.id" class="hover:bg-slate-50/80 transition">
              <td class="py-2.5 px-3.5 font-mono font-bold text-[#0F766E]">{{ po.nomor_po }}</td>
              <td class="py-2.5 px-3.5 font-semibold text-slate-800">{{ po.supplier?.nama_supplier || po.nama_supplier || 'PT Supplier Utama' }}</td>
              <td class="py-2.5 px-3.5 text-slate-600 tabular-nums">{{ formatDate(po.tanggal_po) }}</td>
              <td class="py-2.5 px-3.5 font-bold text-slate-900 tabular-nums">{{ formatRupiah(po.total_estimasi) }}</td>
              <td class="py-2.5 px-3.5 font-mono text-xs text-slate-500">
                {{ po.purchase_requisition?.nomor_pr || (po.purchase_requisition_id ? 'PR #' + po.purchase_requisition_id : 'Direct PO') }}
              </td>
              <td class="py-2.5 px-3.5 text-center">
                <span
                  class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                  :class="{
                    'bg-emerald-50 text-emerald-700 border border-emerald-200': po.status === 'selesai' || po.status === 'RECEIVED',
                    'bg-sky-50 text-sky-700 border border-sky-200': po.status === 'diterima_sebagian',
                    'bg-amber-50 text-amber-700 border border-amber-200': po.status === 'dikirim' || po.status === 'PENDING'
                  }"
                >
                  {{ po.status }}
                </span>
              </td>
              <td class="py-2.5 px-3.5 text-right">
                <button
                  v-if="po.status !== 'selesai' && po.status !== 'RECEIVED'"
                  type="button"
                  @click="openGrnFromPo(po)"
                  class="px-3 py-1.5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-medium inline-flex items-center gap-1 transition shadow-sm cursor-pointer"
                >
                  <i class="pi pi-inbox text-xs"></i>
                  <span>Terima Barang (GRN)</span>
                </button>
                <span v-else class="text-xs text-emerald-600 font-semibold flex items-center justify-end gap-1">
                  <i class="pi pi-verified text-xs"></i>
                  <span>Telah Diterima</span>
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================= TAB 4: PENERIMAAN BARANG (GRN) ================= -->
    <div v-else-if="activeTab === 'grn'" class="space-y-4">
      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h3 class="text-sm font-bold text-slate-900 leading-tight">Goods Receipt Note (GRN - Penerimaan Barang)</h3>
          <p class="text-xs text-slate-500 mt-0.5">Catatan fisik penerimaan barang dengan Nomor Batch & Tanggal Expired FEFO</p>
        </div>
        <button
          type="button"
          @click="openDirectGrnModal"
          class="btn-primary-clinical text-xs py-2 px-3.5"
        >
          <i class="pi pi-plus text-xs"></i>
          <span>Catat GRN Langsung</span>
        </button>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-[13px] text-left">
          <thead class="bg-[#F8FAFC] text-xs font-semibold text-slate-600 border-b border-slate-200">
            <tr>
              <th class="py-2.5 px-3.5">Nomor GRN</th>
              <th class="py-2.5 px-3.5">No. Surat Jalan</th>
              <th class="py-2.5 px-3.5">Supplier / Mitra</th>
              <th class="py-2.5 px-3.5">Tanggal Terima</th>
              <th class="py-2.5 px-3.5">PO Terkait</th>
              <th class="py-2.5 px-3.5">Total Nilai</th>
              <th class="py-2.5 px-3.5 text-center">Status Mutasi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="grnList.length === 0" class="text-center">
              <td colspan="7" class="py-8 text-slate-400 text-xs">Belum ada catatan penerimaan barang (GRN).</td>
            </tr>
            <tr v-for="g in grnList" :key="g.id" class="hover:bg-slate-50/80 transition">
              <td class="py-2.5 px-3.5 font-mono font-bold text-[#0F766E]">{{ g.nomor_grn }}</td>
              <td class="py-2.5 px-3.5 font-mono font-semibold text-slate-800">{{ g.nomor_surat_jalan }}</td>
              <td class="py-2.5 px-3.5 text-slate-700">{{ g.supplier?.nama_supplier || '-' }}</td>
              <td class="py-2.5 px-3.5 text-slate-600 tabular-nums">{{ formatDate(g.tanggal_terima) }}</td>
              <td class="py-2.5 px-3.5 font-mono text-xs text-slate-500">{{ g.purchase_order?.nomor_po || 'Direct' }}</td>
              <td class="py-2.5 px-3.5 font-bold text-slate-900 tabular-nums">{{ formatRupiah(g.total_nilai_barang) }}</td>
              <td class="py-2.5 px-3.5 text-center">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#DCFCE7] text-[#15803D] border border-emerald-200 flex items-center justify-center gap-1">
                  <i class="pi pi-check text-[10px]"></i> Stok & Kartu Mutasi
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================= TAB 5: SUPPLIER REKANAN ================= -->
    <div v-else-if="activeTab === 'suppliers'" class="space-y-4">
      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h3 class="text-sm font-bold text-slate-900 leading-tight">Daftar Distributor & Supplier Resmi</h3>
          <p class="text-xs text-slate-500 mt-0.5">Kelola data mitra penyedia obat, BHP, dan alat terapi klinik</p>
        </div>
        <button
          type="button"
          @click="showSupplierModal = true"
          class="btn-primary-clinical text-xs py-2 px-3.5"
        >
          <i class="pi pi-plus text-xs"></i>
          <span>Tambah Supplier Baru</span>
        </button>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-[13px] text-left">
          <thead class="bg-[#F8FAFC] text-xs font-semibold text-slate-600 border-b border-slate-200">
            <tr>
              <th class="py-2.5 px-3.5">Nama Supplier / PT</th>
              <th class="py-2.5 px-3.5">Kontak Person</th>
              <th class="py-2.5 px-3.5">No. Telepon</th>
              <th class="py-2.5 px-3.5">Email</th>
              <th class="py-2.5 px-3.5">Alamat</th>
              <th class="py-2.5 px-3.5 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="s in supplierList" :key="s.id" class="hover:bg-slate-50/80 transition">
              <td class="py-2.5 px-3.5 font-bold text-slate-900">{{ s.nama_supplier }}</td>
              <td class="py-2.5 px-3.5 text-slate-700">{{ s.kontak_person || '-' }}</td>
              <td class="py-2.5 px-3.5 font-mono text-slate-700">{{ s.no_telp || '-' }}</td>
              <td class="py-2.5 px-3.5 text-slate-600">{{ s.email || '-' }}</td>
              <td class="py-2.5 px-3.5 text-slate-500 text-xs">{{ s.alamat || '-' }}</td>
              <td class="py-2.5 px-3.5 text-center">
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  Aktif
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================= MODAL: KARTU STOK MUTASI ================= -->
    <teleport to="body">
    <div v-if="showKartuStokModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showKartuStokModal = false"></div>
      <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-2xl w-full overflow-hidden animate-fadeIn">
        <div class="bg-[#0B3948] text-white px-6 py-4 flex justify-between items-center">
          <div>
            <h4 class="text-base font-bold text-white">Kartu Stok Ledger: {{ activeProduk?.nama_produk }}</h4>
            <p class="text-xs text-teal-200/90 font-mono mt-0.5">Kode: {{ activeProduk?.kode_produk }} · Total Fisik: {{ activeProduk?.total_stok }} {{ activeProduk?.satuan }}</p>
          </div>
          <button @click="showKartuStokModal = false" class="text-slate-300 hover:text-white p-1"><i class="pi pi-times text-sm"></i></button>
        </div>
        <div class="p-6 max-h-96 overflow-y-auto">
          <table class="w-full text-sm text-left">
            <thead class="bg-slate-100 text-slate-700 font-semibold border-b">
              <tr>
                <th class="p-2.5 text-xs font-bold uppercase tracking-wider">Waktu / Tanggal</th>
                <th class="p-2.5 text-xs font-bold uppercase tracking-wider">Mutasi & Referensi</th>
                <th class="p-2.5 text-right text-xs font-bold uppercase tracking-wider">Perubahan</th>
                <th class="p-2.5 text-right text-xs font-bold uppercase tracking-wider">Stok Sebelum</th>
                <th class="p-2.5 text-right font-bold text-xs uppercase tracking-wider">Stok Sesudah</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="kartuStokList.length === 0" class="text-center">
                <td colspan="5" class="py-8 text-slate-400">Belum ada riwayat mutasi untuk produk ini.</td>
              </tr>
              <tr v-for="k in kartuStokList" :key="k.id" class="hover:bg-slate-50 transition">
                <td class="p-2.5 font-mono text-slate-500">{{ formatDate(k.tanggal) }}</td>
                <td class="p-2.5">
                  <span class="font-semibold text-slate-800 capitalize">{{ (k.tipe_mutasi || '').replace('_', ' ') }}</span>
                  <p class="text-xs text-slate-500 truncate max-w-xs">{{ k.referensi || '-' }}</p>
                </td>
                <td class="p-2.5 text-right font-bold" :class="k.jumlah >= 0 ? 'text-emerald-700' : 'text-red-700'">
                  {{ k.jumlah >= 0 ? '+' + k.jumlah : k.jumlah }}
                </td>
                <td class="p-2.5 text-right text-slate-500 tabular-nums">{{ k.stok_sebelum }}</td>
                <td class="p-2.5 text-right font-bold text-slate-900 tabular-nums">{{ k.stok_sesudah }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end">
          <button @click="showKartuStokModal = false" class="btn-secondary-clinical">Tutup</button>
        </div>
      </div>
    </div>
    </teleport>

    <!-- ================= MODAL: BUAT PURCHASE REQUISITION (PR) ================= -->
    <teleport to="body">
    <div v-if="showPrModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showPrModal = false"></div>
      <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden animate-fadeIn">
        <div class="bg-[#0B3948] text-white px-6 py-4 flex justify-between items-center">
          <h4 class="text-base font-bold text-white">Buat Permintaan Pengadaan (PR)</h4>
          <button @click="showPrModal = false" class="text-slate-300 hover:text-white p-1"><i class="pi pi-times text-sm"></i></button>
        </div>
        <form @submit.prevent="submitPr" class="p-6 space-y-4 text-sm">
          <div>
            <label class="label-clinical">Unit Pemohon / Ruangan</label>
            <input v-model="prForm.unit_pemohon" required class="input-clinical" placeholder="mis. Poli Terapi Wicara" />
          </div>
          <div>
            <label class="label-clinical">Tanggal Pengajuan</label>
            <input v-model="prForm.tanggal_pr" type="date" required class="input-clinical" />
          </div>
          <div>
            <label class="label-clinical">Alasan Kebutuhan</label>
            <textarea v-model="prForm.alasan_kebutuhan" rows="2" class="input-clinical" placeholder="mis. Stok habis terpakai sesi terapi sensorik"></textarea>
          </div>
          <div class="border-t pt-3 space-y-2">
            <label class="label-clinical">Pilih Barang yang Diminta</label>
            <div class="grid grid-cols-3 gap-2">
              <div class="col-span-2">
                <select v-model="prFormItem.master_produk_id" required class="input-clinical">
                  <option v-for="p in produkList" :key="p.id" :value="p.id">
                    {{ p.nama_produk }} (Stok: {{ p.total_stok }})
                  </option>
                </select>
              </div>
              <div>
                <input v-model.number="prFormItem.qty_diminta" type="number" min="1" required class="input-clinical font-bold" placeholder="Qty" />
              </div>
            </div>
          </div>
          <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
            <button type="button" @click="showPrModal = false" class="btn-secondary-clinical">Batal</button>
            <button type="submit" class="btn-primary-clinical">Ajukan PR</button>
          </div>
        </form>
      </div>
    </div>
    </teleport>

    <!-- ================= MODAL: BUAT PURCHASE ORDER (PO) ================= -->
    <teleport to="body">
    <div v-if="showPoModal" class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showPoModal = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden animate-fadeIn">
        <div class="bg-[#0B3948] text-white px-5 py-3.5 flex justify-between items-center">
          <h4 class="text-sm font-bold">Terbitkan Surat Pesanan (PO)</h4>
          <button @click="showPoModal = false" class="text-slate-300 hover:text-white"><i class="pi pi-times"></i></button>
        </div>
        <form @submit.prevent="submitPo" class="p-5 space-y-3 text-xs">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label-clinical">Pilih Supplier / PBF</label>
              <select v-model="poForm.supplier_id" required class="input-clinical">
                <option v-for="s in supplierList" :key="s.id" :value="s.id">{{ s.nama_supplier }}</option>
              </select>
            </div>
            <div>
              <label class="label-clinical">Tanggal Pemesanan</label>
              <input v-model="poForm.tanggal_po" type="date" required class="input-clinical" />
            </div>
          </div>

          <div class="border-t pt-2 space-y-2">
            <label class="label-clinical">Barang yang Dipesan</label>
            <div class="grid grid-cols-3 gap-2">
              <div class="col-span-2">
                <select v-model="poFormItem.master_produk_id" required class="input-clinical" @change="onPoProductChange">
                  <option v-for="p in produkList" :key="p.id" :value="p.id">{{ p.nama_produk }}</option>
                </select>
              </div>
              <div>
                <input v-model.number="poFormItem.qty_pesan" type="number" min="1" required class="input-clinical font-bold" placeholder="Qty" />
              </div>
            </div>
            <div>
              <label class="label-clinical">Harga Satuan Beli (Rp)</label>
              <input v-model.number="poFormItem.harga_satuan" type="number" min="0" required class="input-clinical font-mono" />
            </div>
          </div>

          <div>
            <label class="label-clinical">Catatan Pemesanan</label>
            <textarea v-model="poForm.catatan" rows="2" class="input-clinical" placeholder="Instruksi pengiriman..."></textarea>
          </div>

          <div class="pt-3 border-t flex justify-end gap-2">
            <button type="button" @click="showPoModal = false" class="btn-secondary-clinical text-xs py-2 px-3">Batal</button>
            <button type="submit" class="btn-primary-clinical text-xs py-2 px-4">Terbitkan PO</button>
          </div>
        </form>
      </div>
    </div>
    </teleport>

    <!-- ================= MODAL: TERIMA BARANG (GRN) DENGAN FEFO BATCH ================= -->
    <teleport to="body">
    <div v-if="showGrnModal" class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showGrnModal = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl border border-slate-200 max-w-xl w-full overflow-hidden animate-fadeIn">
        <div class="bg-[#0B3948] text-white px-5 py-3.5 flex justify-between items-center">
          <div>
            <h4 class="text-sm font-bold">Penerimaan Barang (GRN) & Update FEFO</h4>
            <p class="text-[11px] text-teal-200">Input Surat Jalan & Nomor Batch untuk mutasi stok otomatis</p>
          </div>
          <button @click="showGrnModal = false" class="text-slate-300 hover:text-white"><i class="pi pi-times"></i></button>
        </div>
        <form @submit.prevent="submitGrn" class="p-5 space-y-3 text-xs">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label-clinical">Supplier</label>
              <select v-model="grnForm.supplier_id" required class="input-clinical">
                <option v-for="s in supplierList" :key="s.id" :value="s.id">{{ s.nama_supplier }}</option>
              </select>
            </div>
            <div>
              <label class="label-clinical">Tanggal Terima Fisik</label>
              <input v-model="grnForm.tanggal_terima" type="date" required class="input-clinical" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label-clinical">Nomor Surat Jalan (DO)</label>
              <input v-model="grnForm.nomor_surat_jalan" required class="input-clinical font-mono" placeholder="SJ-..." />
            </div>
            <div>
              <label class="label-clinical">Nomor Faktur Supplier</label>
              <input v-model="grnForm.nomor_faktur_supplier" class="input-clinical font-mono" placeholder="INV-..." />
            </div>
          </div>

          <div class="p-3 bg-[#F0F6F8] rounded-xl border border-[#DCE8EC] space-y-2.5">
            <h5 class="font-bold text-[#0F766E] flex items-center gap-1.5">
              <i class="pi pi-box text-xs"></i> Rincian Barang Diterima (FEFO Tracking)
            </h5>
            <div>
              <label class="label-clinical">Pilih Produk</label>
              <select v-model="grnFormItem.master_produk_id" required class="input-clinical" @change="onGrnProductChange">
                <option v-for="p in produkList" :key="p.id" :value="p.id">{{ p.nama_produk }} ({{ p.kode_produk }})</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="label-clinical">Nomor Batch Pabrik</label>
                <input v-model="grnFormItem.nomor_batch" required class="input-clinical font-mono font-bold" placeholder="BATCH-..." />
              </div>
              <div>
                <label class="label-clinical">Tanggal Kadaluwarsa (ED)</label>
                <input v-model="grnFormItem.tanggal_kedaluwarsa" type="date" required class="input-clinical" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="label-clinical">Qty Diterima</label>
                <input v-model.number="grnFormItem.qty_diterima" type="number" min="1" required class="input-clinical font-bold" />
              </div>
              <div>
                <label class="label-clinical">Harga Beli Satuan (Rp)</label>
                <input v-model.number="grnFormItem.harga_beli_satuan" type="number" min="0" required class="input-clinical font-mono" />
              </div>
            </div>
          </div>

          <div>
            <label class="label-clinical">Catatan Penerima Gudang</label>
            <input v-model="grnForm.catatan" class="input-clinical" placeholder="Kemasan baik, segel utuh..." />
          </div>

          <div class="pt-3 border-t flex justify-end gap-2">
            <button type="button" @click="showGrnModal = false" class="btn-secondary-clinical text-xs py-2 px-3">Batal</button>
            <button type="submit" class="btn-primary-clinical text-xs py-2 px-4">
              <i class="pi pi-check text-xs"></i> Simpan GRN & Mutasi Stok
            </button>
          </div>
        </form>
      </div>
    </div>
    </teleport>

    <!-- ================= MODAL: TAMBAH PRODUK BARU ================= -->
    <teleport to="body">
    <div v-if="showProductModal" class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showProductModal = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden animate-fadeIn">
        <div class="bg-[#0B3948] text-white px-5 py-3.5 flex justify-between items-center">
          <h4 class="text-sm font-bold">Tambah Produk / BHP Baru</h4>
          <button @click="showProductModal = false" class="text-slate-300 hover:text-white"><i class="pi pi-times"></i></button>
        </div>
        <form @submit.prevent="submitProduct" class="p-5 space-y-3 text-xs">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label-clinical">Kode Produk</label>
              <input v-model="prodForm.kode_produk" required class="input-clinical font-mono" />
            </div>
            <div>
              <label class="label-clinical">Kategori</label>
              <select v-model="prodForm.jenis" class="input-clinical">
                <option value="bhp">Bahan Habis Pakai (BHP)</option>
                <option value="alat_terapi">Alat & Instrumen Terapi</option>
                <option value="mainan_edukasi">Mainan Edukasi Sensori</option>
                <option value="suplemen">Obat & Suplemen</option>
              </select>
            </div>
          </div>
          <div>
            <label class="label-clinical">Nama Produk / Barang</label>
            <input v-model="prodForm.nama_produk" required class="input-clinical" />
          </div>
          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="label-clinical">Satuan</label>
              <input v-model="prodForm.satuan" required class="input-clinical" placeholder="box, botol" />
            </div>
            <div>
              <label class="label-clinical">Harga Beli</label>
              <input v-model.number="prodForm.harga_beli" type="number" required class="input-clinical" />
            </div>
            <div>
              <label class="label-clinical">Harga Jual</label>
              <input v-model.number="prodForm.harga_jual" type="number" required class="input-clinical" />
            </div>
          </div>
          <div class="pt-3 border-t flex justify-end gap-2">
            <button type="button" @click="showProductModal = false" class="btn-secondary-clinical text-xs py-2 px-3">Batal</button>
            <button type="submit" class="btn-primary-clinical text-xs py-2 px-4">Simpan Produk</button>
          </div>
        </form>
      </div>
    </div>
    </teleport>

    <!-- ================= MODAL: TAMBAH SUPPLIER ================= -->
    <teleport to="body">
    <div v-if="showSupplierModal" class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showSupplierModal = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden animate-fadeIn">
        <div class="bg-[#0B3948] text-white px-5 py-3.5 flex justify-between items-center">
          <h4 class="text-sm font-bold">Tambah Rekanan Supplier Baru</h4>
          <button @click="showSupplierModal = false" class="text-slate-300 hover:text-white"><i class="pi pi-times"></i></button>
        </div>
        <form @submit.prevent="submitSupplier" class="p-5 space-y-3 text-xs">
          <div>
            <label class="label-clinical">Nama Supplier / PT</label>
            <input v-model="supForm.nama_supplier" required class="input-clinical" placeholder="PT. Kimia Farma..." />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label-clinical">Kontak Person</label>
              <input v-model="supForm.kontak_person" class="input-clinical" />
            </div>
            <div>
              <label class="label-clinical">No. Telepon</label>
              <input v-model="supForm.no_telp" class="input-clinical font-mono" />
            </div>
          </div>
          <div>
            <label class="label-clinical">Email</label>
            <input v-model="supForm.email" type="email" class="input-clinical" />
          </div>
          <div>
            <label class="label-clinical">Alamat</label>
            <textarea v-model="supForm.alamat" rows="2" class="input-clinical"></textarea>
          </div>
          <div class="pt-3 border-t flex justify-end gap-2">
            <button type="button" @click="showSupplierModal = false" class="btn-secondary-clinical text-xs py-2 px-3">Batal</button>
            <button type="submit" class="btn-primary-clinical text-xs py-2 px-4">Simpan Supplier</button>
          </div>
        </form>
      </div>
    </div>
    </teleport>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const activeTab = ref('katalog')
const loading = ref(false)
const produkList = ref([])
const searchQuery = ref('')
const filterJenis = ref('')

const prList = ref([])
const poList = ref([])
const grnList = ref([])
const supplierList = ref([])

// Form states
const showProductModal = ref(false)
const prodForm = ref({ kode_produk: '', nama_produk: '', jenis: 'bhp', satuan: 'pcs', harga_beli: 0, harga_jual: 0 })

const showKartuStokModal = ref(false)
const activeProduk = ref(null)
const kartuStokList = ref([])

const showPrModal = ref(false)
const prForm = ref({
  unit_pemohon: 'Poli Terapi Anak',
  tanggal_pr: new Date().toISOString().substring(0, 10),
  alasan_kebutuhan: '',
})
const prFormItem = ref({ master_produk_id: null, qty_diminta: 5 })

const showPoModal = ref(false)
const poForm = ref({
  supplier_id: null,
  purchase_requisition_id: null,
  tanggal_po: new Date().toISOString().substring(0, 10),
  catatan: ''
})
const poFormItem = ref({ master_produk_id: null, qty_pesan: 10, harga_satuan: 50000 })

const showGrnModal = ref(false)
const grnForm = ref({
  supplier_id: null,
  purchase_order_id: null,
  nomor_surat_jalan: '',
  nomor_faktur_supplier: '',
  tanggal_terima: new Date().toISOString().substring(0, 10),
  catatan: ''
})
const grnFormItem = ref({
  master_produk_id: null,
  nomor_batch: '',
  tanggal_kedaluwarsa: new Date(Date.now() + 365*24*60*60*1000).toISOString().substring(0, 10),
  qty_diterima: 10,
  harga_beli_satuan: 50000
})

const showSupplierModal = ref(false)
const supForm = ref({ nama_supplier: '', kontak_person: '', no_telp: '', email: '', alamat: '' })

// ================= LOADERS =================
const loadProduk = async () => {
  loading.value = true
  try {
    const res = await api.get('/farmasi/produk', {
      params: { search: searchQuery.value, jenis: filterJenis.value }
    })
    produkList.value = res.data?.data || res.data || []
    if (produkList.value.length > 0) {
      if (!prFormItem.value.master_produk_id) prFormItem.value.master_produk_id = produkList.value[0].id
      if (!poFormItem.value.master_produk_id) {
        poFormItem.value.master_produk_id = produkList.value[0].id
        poFormItem.value.harga_satuan = produkList.value[0].harga_beli || 50000
      }
      if (!grnFormItem.value.master_produk_id) {
        grnFormItem.value.master_produk_id = produkList.value[0].id
        grnFormItem.value.harga_beli_satuan = produkList.value[0].harga_beli || 50000
      }
    }
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const loadPr = async () => {
  try {
    const res = await api.get('/farmasi/pr')
    prList.value = res.data?.data || res.data || []
  } catch (err) {
    console.error(err)
  }
}

const loadPo = async () => {
  try {
    const res = await api.get('/farmasi/po')
    poList.value = res.data?.data || res.data || []
  } catch (err) {
    console.error(err)
  }
}

const loadGrn = async () => {
  try {
    const res = await api.get('/farmasi/grn')
    grnList.value = res.data?.data || res.data || []
  } catch (err) {
    console.error(err)
  }
}

const loadSuppliers = async () => {
  try {
    const res = await api.get('/farmasi/suppliers')
    supplierList.value = res.data || []
    if (supplierList.value.length > 0) {
      if (!poForm.value.supplier_id) poForm.value.supplier_id = supplierList.value[0].id
      if (!grnForm.value.supplier_id) grnForm.value.supplier_id = supplierList.value[0].id
    }
  } catch (err) {
    console.error(err)
  }
}

// ================= HANDLERS =================
const viewKartuStok = async (p) => {
  activeProduk.value = p
  try {
    const res = await api.get(`/farmasi/kartu-stok/${p.id}`)
    kartuStokList.value = res.data || []
    showKartuStokModal.value = true
  } catch (err) {
    toast.error('Gagal memuat kartu stok')
  }
}

const openAddProductModal = () => {
  prodForm.value = {
    kode_produk: 'PRD-' + Math.floor(1000 + Math.random() * 9000),
    nama_produk: '',
    jenis: 'bhp',
    satuan: 'pcs',
    harga_beli: 0,
    harga_jual: 0
  }
  showProductModal.value = true
}

const submitProduct = async () => {
  try {
    await api.post('/farmasi/produk', prodForm.value)
    toast.success('Produk berhasil ditambahkan ke katalog')
    showProductModal.value = false
    loadProduk()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menyimpan produk')
  }
}

const openAddBatchModal = () => {
  grnForm.value = {
    supplier_id: supplierList.value[0]?.id || 1,
    purchase_order_id: null,
    nomor_surat_jalan: 'SJ-MANUAL-' + Math.floor(1000 + Math.random() * 9000),
    nomor_faktur_supplier: '',
    tanggal_terima: new Date().toISOString().substring(0, 10),
    catatan: 'Penambahan stok/batch manual FEFO'
  }
  grnFormItem.value = {
    master_produk_id: produkList.value[0]?.id || 1,
    nomor_batch: 'BATCH-' + new Date().getFullYear() + '-' + Math.floor(100 + Math.random() * 900),
    tanggal_kedaluwarsa: new Date(Date.now() + 365*24*60*60*1000).toISOString().substring(0, 10),
    qty_diterima: 10,
    harga_beli_satuan: produkList.value[0]?.harga_beli || 50000
  }
  showGrnModal.value = true
}

// PR Handlers
const submitPr = async () => {
  try {
    const payload = {
      unit_pemohon: prForm.value.unit_pemohon,
      tanggal_pr: prForm.value.tanggal_pr,
      alasan_kebutuhan: prForm.value.alasan_kebutuhan,
      items: [
        {
          master_produk_id: prFormItem.value.master_produk_id,
          qty_diminta: prFormItem.value.qty_diminta
        }
      ]
    }
    const res = await api.post('/farmasi/pr', payload)
    toast.success(res.data.message || 'PR berhasil diajukan')
    showPrModal.value = false
    loadPr()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal mengajukan PR')
  }
}

const approvePr = async (id, status) => {
  try {
    const res = await api.post(`/farmasi/pr/${id}/approve`, {
      status,
      catatan_approval: status === 'disetujui' ? 'Disetujui oleh Kepala Logistik' : 'Ditolak'
    })
    toast.success(res.data.message)
    loadPr()
  } catch (err) {
    toast.error('Gagal memproses approval PR')
  }
}

const createPoFromPr = (pr) => {
  poForm.value.purchase_requisition_id = pr.id
  poForm.value.supplier_id = supplierList.value[0]?.id || 1
  if (pr.items && pr.items[0]) {
    poFormItem.value.master_produk_id = pr.items[0].master_produk_id
    poFormItem.value.qty_pesan = pr.items[0].qty_disetujui || pr.items[0].qty_diminta || 10
    poFormItem.value.harga_satuan = pr.items[0].master_produk?.harga_beli || 50000
  }
  activeTab.value = 'po'
  showPoModal.value = true
}

// PO Handlers
const onPoProductChange = () => {
  const p = produkList.value.find(x => x.id === poFormItem.value.master_produk_id)
  if (p) {
    poFormItem.value.harga_satuan = p.harga_beli || 50000
  }
}

const submitPo = async () => {
  try {
    const payload = {
      supplier_id: poForm.value.supplier_id,
      purchase_requisition_id: poForm.value.purchase_requisition_id,
      tanggal_po: poForm.value.tanggal_po,
      catatan: poForm.value.catatan,
      items: [
        {
          master_produk_id: poFormItem.value.master_produk_id,
          qty_pesan: poFormItem.value.qty_pesan,
          harga_satuan: poFormItem.value.harga_satuan
        }
      ]
    }
    const res = await api.post('/farmasi/po', payload)
    toast.success(res.data.message)
    showPoModal.value = false
    loadPo()
    loadPr()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal membuat purchase order')
  }
}

const openGrnFromPo = (po) => {
  grnForm.value.supplier_id = po.supplier_id
  grnForm.value.purchase_order_id = po.id
  grnForm.value.nomor_surat_jalan = 'SJ-' + Math.floor(10000 + Math.random() * 90000)
  grnForm.value.nomor_faktur_supplier = 'INV-' + Math.floor(1000 + Math.random() * 9000)
  
  if (po.items && po.items[0]) {
    grnFormItem.value.master_produk_id = po.items[0].master_produk_id
    grnFormItem.value.qty_diterima = po.items[0].qty_pesan
    grnFormItem.value.harga_beli_satuan = po.items[0].harga_satuan
    grnFormItem.value.nomor_batch = 'BATCH-' + new Date().getFullYear() + '-' + Math.floor(100 + Math.random() * 900)
  }
  activeTab.value = 'grn'
  showGrnModal.value = true
}

const openDirectGrnModal = () => {
  grnForm.value = {
    supplier_id: supplierList.value[0]?.id || 1,
    purchase_order_id: null,
    nomor_surat_jalan: 'SJ-' + Math.floor(10000 + Math.random() * 90000),
    nomor_faktur_supplier: 'INV-' + Math.floor(1000 + Math.random() * 9000),
    tanggal_terima: new Date().toISOString().substring(0, 10),
    catatan: 'Penerimaan langsung logistik'
  }
  grnFormItem.value = {
    master_produk_id: produkList.value[0]?.id || 1,
    nomor_batch: 'BATCH-' + new Date().getFullYear() + '-' + Math.floor(100 + Math.random() * 900),
    tanggal_kedaluwarsa: new Date(Date.now() + 365*24*60*60*1000).toISOString().substring(0, 10),
    qty_diterima: 10,
    harga_beli_satuan: produkList.value[0]?.harga_beli || 50000
  }
  showGrnModal.value = true
}

const onGrnProductChange = () => {
  const p = produkList.value.find(x => x.id === grnFormItem.value.master_produk_id)
  if (p) {
    grnFormItem.value.harga_beli_satuan = p.harga_beli || 50000
  }
}

// GRN Handlers
const submitGrn = async () => {
  try {
    const payload = {
      supplier_id: grnForm.value.supplier_id,
      purchase_order_id: grnForm.value.purchase_order_id,
      nomor_surat_jalan: grnForm.value.nomor_surat_jalan,
      nomor_faktur_supplier: grnForm.value.nomor_faktur_supplier,
      tanggal_terima: grnForm.value.tanggal_terima,
      catatan: grnForm.value.catatan,
      items: [
        {
          master_produk_id: grnFormItem.value.master_produk_id,
          nomor_batch: grnFormItem.value.nomor_batch,
          tanggal_kedaluwarsa: grnFormItem.value.tanggal_kedaluwarsa,
          qty_diterima: grnFormItem.value.qty_diterima,
          harga_beli_satuan: grnFormItem.value.harga_beli_satuan
        }
      ]
    }
    const res = await api.post('/farmasi/grn', payload)
    toast.success(res.data.message || 'GRN berhasil dicatat dan stok fisik dimutasi!')
    showGrnModal.value = false
    loadGrn()
    loadProduk()
    loadPo()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menyimpan penerimaan barang')
  }
}

// Supplier Handlers
const submitSupplier = async () => {
  try {
    const res = await api.post('/farmasi/suppliers', supForm.value)
    toast.success(res.data.message)
    showSupplierModal.value = false
    loadSuppliers()
  } catch (err) {
    toast.error('Gagal menambahkan supplier')
  }
}

const formatRupiah = (val) => 'Rp ' + Number(val || 0).toLocaleString('id-ID')
const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(() => {
  loadProduk()
  loadPr()
  loadPo()
  loadGrn()
  loadSuppliers()
})
</script>
