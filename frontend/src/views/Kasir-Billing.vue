<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- TOP SUMMARY STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 shrink-0">
      <!-- 1. KASIR POS STATUS & REKAP HARIAN -->
      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex items-center justify-between">
        <div>
          <span class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider block">Status Kasir POS</span>
          <div class="flex items-center gap-2 mt-1">
            <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
            <h3 class="text-base font-bold text-[#0F172A] leading-tight">
              POS Siap Melayani
            </h3>
          </div>
          <span class="text-[10px] text-slate-500 block mt-0.5">Transaksi langsung tanpa blokir shift</span>
        </div>
        <button
          type="button"
          @click="router.push('/laporan-pembayaran')"
          class="border text-xs font-semibold px-3 py-1.5 rounded-lg transition cursor-pointer shadow-2xs border-teal-200 bg-[#E6F2F5] text-[#0F766E] hover:bg-teal-100 flex items-center gap-1.5"
          title="Rekap Pendapatan Harian Kasir"
        >
          <i class="pi pi-chart-bar text-xs"></i>
          <span>Rekap Harian</span>
        </button>
      </div>

      <!-- 2. TOTAL TAGIHAN -->
      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
        <span class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider block">Total Tagihan Aktif</span>
        <h3 class="text-lg font-bold text-[#0F172A] tabular-nums mt-1 leading-tight">{{ formatRupiah(summary.totalTagihan) }}</h3>
        <p class="text-[11px] text-[#64748B] mt-0.5">{{ invoices.length }} faktur tercatat</p>
      </div>

      <!-- 3. TERBAYAR -->
      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
        <span class="text-[11px] font-medium text-[#15803D] uppercase tracking-wider block">Sudah Terbayar</span>
        <h3 class="text-lg font-bold text-[#15803D] tabular-nums mt-1 leading-tight">{{ formatRupiah(summary.totalTerbayar) }}</h3>
        <p class="text-[11px] text-[#64748B] mt-0.5">{{ invoices.filter(x => x.status === 'lunas').length }} invoice lunas</p>
      </div>

      <!-- 4. PIUTANG / BELUM BAYAR -->
      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
        <span class="text-[11px] font-medium text-[#DC2626] uppercase tracking-wider block">Sisa Piutang / Belum Bayar</span>
        <h3 class="text-lg font-bold text-[#DC2626] tabular-nums mt-1 leading-tight">{{ formatRupiah(summary.totalSisa) }}</h3>
        <p class="text-[11px] text-[#64748B] mt-0.5">{{ invoices.filter(x => x.status !== 'lunas').length }} invoice menunggu</p>
      </div>
    </div>

    <!-- MAIN CARD WITH TABS -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">
      
      <!-- TAB NAVIGATION -->
      <div class="flex items-center gap-1 p-2 bg-slate-50 border-b border-slate-200 overflow-x-auto custom-scrollbar shrink-0">
        <button
          type="button"
          @click="activeTab = 'invoices'"
          class="px-4 py-2 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-2"
          :class="activeTab === 'invoices' ? 'bg-white text-[#0F766E] shadow-2xs border border-slate-200' : 'text-slate-600 hover:text-slate-900'"
        >
          <i class="pi pi-receipt text-xs"></i>
          <span>Faktur & Pembayaran Kasir</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-700 font-mono">
            {{ invoices.length }}
          </span>
        </button>

        <button
          type="button"
          @click="activeTab = 'claims'"
          class="px-4 py-2 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-2"
          :class="activeTab === 'claims' ? 'bg-white text-[#0F766E] shadow-2xs border border-slate-200' : 'text-slate-600 hover:text-slate-900'"
        >
          <i class="pi pi-shield text-xs text-[#0F766E]"></i>
          <span>Piutang Asuransi (AR Penjamin)</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-[#E6F2F5] text-[#0F766E] font-mono">
            {{ claims.length }}
          </span>
        </button>

        <button
          type="button"
          @click="activeTab = 'packages'"
          class="px-4 py-2 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-2"
          :class="activeTab === 'packages' ? 'bg-white text-[#0F766E] shadow-2xs border border-slate-200' : 'text-slate-600 hover:text-slate-900'"
        >
          <i class="pi pi-box text-xs text-[#0F766E]"></i>
          <span>Katalog Paket Terapi</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-teal-50 text-teal-700 font-mono">
            {{ packagesList.length }}
          </span>
        </button>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 1: DAFTAR INVOICE & PEMBAYARAN KASIR -->
      <!-- ========================================================================= -->
      <div v-if="activeTab === 'invoices'" class="flex-1 min-h-0 flex flex-col overflow-hidden">
        <!-- Toolbar -->
        <div class="p-3 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3 shrink-0">
          <div>
            <h2 class="text-[14px] font-semibold text-[#0F172A] leading-tight flex items-center gap-2">
              <span>Daftar Faktur Kasir & Voucher Sesi</span>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]">
                Dual-Track
              </span>
            </h2>
            <p class="text-[11px] text-[#64748B] mt-0.5">Kelola penagihan sesi tunggal, voucher potong kuota paket (Rp 0), dan klaim selisih excess asuransi.</p>
          </div>

          <div class="flex items-center gap-2">
            <select v-model="filterStatus" @change="loadInvoices" class="input-filter h-[34px] text-xs">
              <option value="">Semua Status Tagihan</option>
              <option value="lunas">Lunas</option>
              <option value="belum_bayar">Belum Bayar</option>
              <option value="bayar_sebagian">Bayar Sebagian</option>
            </select>

            <button
              type="button"
              @click="router.push('/verifikasi-resep-penunjang')"
              class="bg-[#E6F2F5] hover:bg-[#d6ebf0] text-[#0F766E] border border-[#CBE3E9] font-semibold text-xs h-[34px] px-3 rounded-lg flex items-center gap-1.5 transition cursor-pointer"
              title="Telaah Resep & Verifikasi Order Penunjang Pra-Billing"
            >
              <i class="pi pi-check-circle text-xs"></i>
              <span>Verifikasi Resep & Penunjang</span>
            </button>

            <button
              type="button"
              @click="openNewInvoiceModal"
              class="bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs h-[34px] px-3.5 rounded-lg flex items-center gap-1.5 transition shadow-sm cursor-pointer"
            >
              <i class="pi pi-plus text-[10px]"></i>
              <span>Buat Tagihan Baru</span>
            </button>
          </div>
        </div>

        <!-- Table Invoices -->
        <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
          <table class="w-full text-xs text-left">
            <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
              <tr>
                <th class="py-3 px-4">No. Invoice</th>
                <th class="py-3 px-4">Pasien Anak & No. RM</th>
                <th class="py-3 px-4">Metode / Track</th>
                <th class="py-3 px-4">Total Tagihan</th>
                <th class="py-3 px-4">Status & AR</th>
                <th class="py-3 px-4 text-center">Status Bayar</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="inv in invoices" :key="inv.id" class="hover:bg-slate-50 transition">
                <td class="py-3 px-4 font-mono font-bold text-[#0F766E] text-xs">
                  {{ inv.nomor_invoice }}
                </td>

                <td class="py-3 px-4">
                  <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-[#E6F2F5] text-[#0F766E] font-bold text-xs flex items-center justify-center shrink-0">
                      {{ (inv.profile_anak?.nama_anak || 'A').charAt(0) }}
                    </div>
                    <div>
                      <span class="font-bold text-slate-800 text-xs block leading-tight">{{ inv.profile_anak?.nama_anak || '-' }}</span>
                      <span class="text-[10px] text-slate-500 font-mono">{{ inv.profile_anak?.no_rm }}</span>
                    </div>
                  </div>
                </td>

                <td class="py-3 px-4">
                  <span
                    v-if="inv.payment_track === 'umum_package' || inv.total_tagihan == 0"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]"
                  >
                    <i class="pi pi-box text-[9px]"></i>
                    Voucher Kuota (Rp 0)
                  </span>
                  <span
                    v-else-if="inv.payment_track === 'asuransi' || inv.guarantor_id"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]"
                  >
                    <i class="pi pi-shield text-[9px]"></i>
                    Asuransi (Excess: {{ formatRupiah(inv.excess_amount) }})
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200"
                  >
                    <i class="pi pi-ticket text-[9px]"></i>
                    Umum Single
                  </span>
                </td>

                <td class="py-3 px-4 font-semibold tabular-nums text-slate-800">
                  {{ formatRupiah(inv.total_tagihan) }}
                </td>

                <td class="py-3 px-4">
                  <span
                    v-if="inv.ar_status === 'piutang_asuransi'"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200"
                  >
                    Piutang Penjamin (AR)
                  </span>
                  <span
                    v-else-if="inv.ar_status === 'lunas_penjamin'"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200"
                  >
                    Klaim Lunas
                  </span>
                  <span v-else class="text-slate-400 text-[11px]">-</span>
                </td>

                <td class="py-3 px-4 text-center">
                  <span
                    v-if="inv.is_paket_lunas || (inv.payment_track === 'umum_package' && inv.sisa_tagihan == 0)"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-[#0F766E] border border-[#CBE3E9]"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0F766E]"></span>
                    LUNAS (PAKET)
                  </span>
                  <span
                    v-else-if="inv.status === 'terjamin_asuransi'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span>
                    TERJAMIN ASURANSI
                  </span>
                  <span
                    v-else-if="inv.status === 'lunas_dengan_excess_cash'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    LUNAS + EXCESS
                  </span>
                  <span
                    v-else-if="inv.status === 'menunggu_pelunasan_excess'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                    EXCESS BELUM BAYAR
                  </span>
                  <span
                    v-else-if="inv.status === 'lunas'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    LUNAS
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                    BELUM BAYAR
                  </span>
                </td>

                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      v-if="inv.status !== 'lunas'"
                      type="button"
                      @click="openBayarModal(inv)"
                      class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#0F766E] hover:bg-[#115E59] text-white flex items-center gap-1 transition cursor-pointer shadow-2xs"
                    >
                      <i class="pi pi-credit-card text-[10px]"></i>
                      Proses Bayar
                    </button>

                    <button
                      type="button"
                      @click="previewThermal(inv)"
                      title="Cetak Struk Kasir / Bukti Layanan"
                      class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 hover:text-teal-700 flex items-center justify-center transition cursor-pointer"
                    >
                      <i class="pi pi-print text-xs"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="invoices.length === 0">
                <td colspan="7" class="py-12 text-center text-slate-400">
                  <i class="pi pi-inbox text-2xl text-slate-300 mb-1 block"></i>
                  <span>Tidak ada data faktur kasir</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 2: PIUTANG ASURANSI & AR KLAIM PENJAMIN -->
      <!-- ========================================================================= -->
      <div v-if="activeTab === 'claims'" class="flex-1 min-h-0 flex flex-col overflow-hidden p-3 gap-2.5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[#E6F2F5] border border-[#CBE3E9] p-3 rounded-xl shrink-0">
          <div>
            <h3 class="text-xs font-bold text-[#0F172A] flex items-center gap-2">
              <i class="pi pi-shield text-[#0F766E]"></i>
              Manajemen Klaim & Piutang Penjamin (Corporate / Insurance AR)
            </h3>
            <p class="text-[11px] text-slate-600 mt-0.5">
              Monitoring tagihan sesi terapi yang dijamin asuransi (BPJS, Prudential, Mandiri Inhealth, AdMedika) dan batch submission klaim.
            </p>
          </div>

          <button
            type="button"
            @click="submitBatchClaims"
            :disabled="selectedClaims.length === 0"
            class="bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs h-[32px] px-3.5 rounded-lg flex items-center gap-1.5 transition disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer shadow-sm shrink-0"
          >
            <i class="pi pi-send text-[10px]"></i>
            <span>Ajukan Batch Klaim ({{ selectedClaims.length }})</span>
          </button>
        </div>

        <div class="flex-1 min-h-0 overflow-auto border border-slate-200 rounded-xl custom-scrollbar">
          <table class="w-full text-xs text-left">
            <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
              <tr>
                <th class="py-3 px-4 w-10 text-center">
                  <input type="checkbox" @change="toggleSelectAllClaims" :checked="isAllClaimsSelected" />
                </th>
                <th class="py-3 px-4">No. Pengajuan</th>
                <th class="py-3 px-4">Penjamin Asuransi</th>
                <th class="py-3 px-4">Pasien & No. GL</th>
                <th class="py-3 px-4">Nominal Klaim</th>
                <th class="py-3 px-4 text-center">Status Klaim</th>
                <th class="py-3 px-4 text-right">Rekonsiliasi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="clm in claims" :key="clm.id" class="hover:bg-slate-50 transition">
                <td class="py-3 px-4 text-center">
                  <input type="checkbox" :value="clm.id" v-model="selectedClaims" :disabled="clm.status === 'lunas'" />
                </td>
                <td class="py-3 px-4 font-mono font-bold text-[#0F766E]">{{ clm.nomor_pengajuan }}</td>
                <td class="py-3 px-4 font-bold text-slate-800">{{ clm.guarantor?.nama_penjamin || 'Asuransi' }}</td>
                <td class="py-3 px-4">
                  <span class="font-semibold text-slate-800 block">{{ clm.profile_anak?.nama_anak }}</span>
                  <span class="text-[10px] text-slate-500 font-mono">GL: {{ clm.nomor_gl || '-' }}</span>
                </td>
                <td class="py-3 px-4 font-semibold text-slate-800 tabular-nums">
                  {{ formatRupiah(clm.nominal_diajukan) }}
                </td>
                <td class="py-3 px-4 text-center">
                  <span
                    v-if="clm.status === 'lunas'"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"
                  >
                    Lunas Penjamin
                  </span>
                  <span
                    v-else-if="clm.status === 'diajukan'"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-[#0F766E] border border-[#CBE3E9]"
                  >
                    Klaim Diajukan
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200"
                  >
                    Draft
                  </span>
                </td>
                <td class="py-3 px-4 text-right">
                  <button
                    v-if="clm.status !== 'lunas'"
                    type="button"
                    @click="openReconcileModal(clm)"
                    class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#0F766E] hover:bg-[#115E59] text-white flex items-center gap-1 ml-auto transition cursor-pointer shadow-2xs"
                  >
                    <i class="pi pi-check text-[10px]"></i>
                    Rekonsiliasi
                  </button>
                  <span v-else class="text-[11px] text-emerald-700 font-semibold">Telah Direkonsiliasi</span>
                </td>
              </tr>

              <tr v-if="claims.length === 0">
                <td colspan="7" class="py-12 text-center text-slate-400">
                  <i class="pi pi-inbox text-2xl text-slate-300 mb-1 block"></i>
                  <span>Belum ada data klaim asuransi</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 3: KATALOG MASTER PAKET TERAPI -->
      <!-- ========================================================================= -->
      <div v-if="activeTab === 'packages'" class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-3.5 space-y-3">
        <div class="flex items-center justify-between shrink-0">
          <div>
            <h3 class="text-xs font-bold text-[#0F172A] flex items-center gap-2">
              <i class="pi pi-box text-[#0F766E]"></i>
              Katalog Master Paket Terapi (Multi-Sesi Bundles)
            </h3>
            <p class="text-[11px] text-slate-600 mt-0.5">
              Pilihan paket terapi 4x, 8x, dan 12x sesi untuk pasien mandiri (pre-paid wallet balance).
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
          <div
            v-for="pkg in packagesList"
            :key="pkg.id"
            class="p-3.5 rounded-xl border border-slate-200 bg-white shadow-xs hover:border-teal-400 transition flex flex-col justify-between"
          >
            <div>
              <div class="w-7 h-7 rounded-lg bg-[#E6F2F5] text-[#0F766E] flex items-center justify-center font-bold text-xs mb-2">
                <i class="pi pi-star text-xs"></i>
              </div>
              <h4 class="font-bold text-slate-900 text-xs leading-tight">{{ pkg.nama_paket }}</h4>
              <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">{{ pkg.deskripsi }}</p>

              <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Jumlah Sesi:</span>
                <span class="font-bold text-slate-900">{{ pkg.jumlah_sesi }} Pertemuan</span>
              </div>
              <div class="mt-1 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Masa Berlaku:</span>
                <span class="font-semibold text-slate-700">{{ pkg.masa_berlaku_hari }} Hari</span>
              </div>
            </div>

            <div class="mt-4 pt-2.5 border-t border-slate-100">
              <div class="text-sm font-bold text-[#0F766E] tabular-nums mb-2">
                {{ formatRupiah(pkg.harga_paket) }}
              </div>
              <button
                type="button"
                @click="buyPackageForChild(pkg)"
                class="w-full bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs py-1.5 rounded-lg flex items-center justify-center gap-1.5 transition cursor-pointer shadow-xs"
              >
                <i class="pi pi-shopping-cart text-[10px]"></i>
                <span>Beli Paket di Kasir</span>
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- ========================================================================= -->
    <!-- MODAL PEMBAYARAN KASIR (DUAL-TRACK: UMUM, ASURANSI KLAIM & PAKET LUNAS) -->
    <!-- ========================================================================= -->
    <teleport to="body">
    <div v-if="showBayarModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fadeIn">
      <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 max-h-[90dvh] flex flex-col overflow-hidden">
        <div class="px-6 py-4 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
          <div class="flex items-center gap-2.5">
            <i class="pi pi-credit-card text-teal-300 text-base"></i>
            <h3 class="font-bold text-base text-white">Proses Pelunasan Tagihan Kasir</h3>
          </div>
          <button
            type="button"
            @click="showBayarModal = false"
            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-white hover:bg-white/10 transition cursor-pointer"
          >
            ✕
          </button>
        </div>

        <div v-if="selectedInvoice" class="p-6 flex-1 min-h-0 overflow-y-auto custom-scrollbar space-y-4 text-sm text-slate-700">
          <!-- Context Banner -->
          <div class="bg-[#E6F2F5] border border-[#CBE3E9] p-3 rounded-xl space-y-1">
            <div class="flex items-center justify-between">
              <span class="text-xs text-[#0F766E] font-bold">{{ selectedInvoice.nomor_invoice }}</span>
              <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-white border border-[#CBE3E9] text-[#0B3948]">
                {{ selectedInvoice.payment_track === 'umum_package' ? 'Paket Terapi' : (selectedInvoice.payment_track === 'asuransi' ? 'Asuransi / Penjamin' : 'Umum Lepas') }}
              </span>
            </div>
            <p class="text-sm font-bold text-slate-900 leading-tight">{{ selectedInvoice.profile_anak?.nama_anak }} ({{ selectedInvoice.profile_anak?.no_rm }})</p>
            <div class="pt-2 border-t border-[#CBE3E9]/60 flex justify-between font-semibold">
              <span class="text-slate-600">Total Nilai Tagihan:</span>
              <span class="text-slate-900 font-bold tabular-nums">{{ formatRupiah(selectedInvoice.total_tagihan) }}</span>
            </div>
          </div>

          <!-- Alert jika ada resep / penunjang belum diverifikasi (Pra-Billing) -->
          <div
            v-if="pendingOrdersSummary.total_resep_menunggu > 0 || pendingOrdersSummary.total_penunjang_menunggu > 0"
            class="p-3 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 flex items-start gap-2.5 animate-fadeIn"
          >
            <i class="pi pi-exclamation-triangle text-amber-600 text-base mt-0.5 shrink-0"></i>
            <div class="flex-1 text-xs">
              <div class="font-bold text-amber-950">Perhatian Kasir (Order Dokter Belum Masuk Bill):</div>
              <p class="text-[11px] mt-0.5 text-amber-800 leading-relaxed">
                Terdapat
                <span v-if="pendingOrdersSummary.total_resep_menunggu > 0" class="font-bold text-amber-950">{{ pendingOrdersSummary.total_resep_menunggu }} E-Resep Obat Farmasi</span>
                <span v-if="pendingOrdersSummary.total_resep_menunggu > 0 && pendingOrdersSummary.total_penunjang_menunggu > 0"> dan </span>
                <span v-if="pendingOrdersSummary.total_penunjang_menunggu > 0" class="font-bold text-amber-950">{{ pendingOrdersSummary.total_penunjang_menunggu }} Order Penunjang Lab/Rad</span>
                yang masih menunggu verifikasi apoteker / petugas. Item ini <strong>belum masuk</strong> ke rincian tagihan faktur ini.
              </p>
              <div class="mt-2 flex items-center gap-2">
                <button
                  type="button"
                  @click="goToVerification"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-700 hover:bg-amber-800 text-white rounded-lg text-[10px] font-bold transition shadow-xs cursor-pointer"
                >
                  <i class="pi pi-external-link text-[9px]"></i>
                  <span>Buka Ruang Verifikasi Order</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Rincian Item Tagihan Saat Ini (Verified & Invoiced Items) -->
          <div v-if="selectedInvoice.items && selectedInvoice.items.length > 0" class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-slate-800">
              <span class="flex items-center gap-1.5">
                <i class="pi pi-list text-[#0F766E]"></i>
                <span>Rincian Item Tagihan Faktur:</span>
              </span>
              <span class="text-[11px] text-slate-500 font-normal">({{ selectedInvoice.items.length }} item tercatat)</span>
            </div>

            <div class="divide-y divide-slate-200 border border-slate-200 rounded-lg bg-white overflow-hidden text-[11px] max-h-44 overflow-y-auto custom-scrollbar">
              <div
                v-for="(it, idx) in selectedInvoice.items"
                :key="idx"
                class="p-2 flex items-center justify-between gap-2"
              >
                <div class="flex-1">
                  <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="font-semibold text-slate-900">{{ it.deskripsi || it.nama_item }}</span>
                    <!-- Origin Badge -->
                    <span
                      v-if="it.tipe_item === 'obat_resep'"
                      class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-teal-100 text-[#0F766E] border border-teal-200"
                    >
                      [Farmasi - Resep Dokter]
                    </span>
                    <span
                      v-else-if="it.tipe_item === 'order_penunjang'"
                      class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-sky-100 text-sky-800 border border-sky-200"
                    >
                      [Penunjang - Lab/Rad]
                    </span>
                    <span
                      v-else
                      class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-slate-100 text-slate-700 border border-slate-200"
                    >
                      [Layanan / Sesi]
                    </span>
                  </div>
                  <span class="text-[10px] text-slate-500">{{ it.qty || it.kuantitas || 1 }}x @ {{ formatRupiah(it.harga_satuan) }}</span>
                </div>
                <div class="font-bold text-slate-900 tabular-nums">
                  {{ formatRupiah(it.subtotal) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Pathway Tabs -->
          <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 rounded-lg">
            <button
              type="button"
              @click="bayarMode = 'paket'"
              class="py-1.5 text-center font-bold text-[11px] rounded-md transition"
              :class="bayarMode === 'paket' ? 'bg-white text-[#0F766E] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
            >
              1. Paket Lunas
            </button>
            <button
              type="button"
              @click="bayarMode = 'asuransi'"
              class="py-1.5 text-center font-bold text-[11px] rounded-md transition"
              :class="bayarMode === 'asuransi' ? 'bg-white text-[#0F766E] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
            >
              2. Asuransi Klaim
            </button>
            <button
              type="button"
              @click="bayarMode = 'umum'"
              class="py-1.5 text-center font-bold text-[11px] rounded-md transition"
              :class="bayarMode === 'umum' ? 'bg-white text-[#0F766E] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
            >
              3. Umum Lepas
            </button>
          </div>

          <!-- JALUR 1: PAKET LUNAS DI AWAL -->
          <div v-if="bayarMode === 'paket'" class="space-y-3">
            <div class="p-3 rounded-lg bg-teal-50 border border-teal-200 text-teal-900 space-y-1.5">
              <div class="flex items-center gap-1.5 font-bold text-xs text-teal-800">
                <i class="pi pi-check-circle"></i>
                <span>Voucher Potong Sesi Paket (Lunas di Awal)</span>
              </div>
              <p class="text-[11px] leading-relaxed">
                Pasien telah melunasi seluruh paket terapi di awal. Sesi terapi hari ini bernilai <strong>Rp 0</strong> dan kuota sesi paket otomatis terpotong saat check-in.
              </p>
            </div>

            <div v-if="selectedInvoice.sisa_tagihan > 0" class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-[11px] space-y-1.5">
              <span class="font-bold block">Biaya Ekstra Tambahan (Obat Resep / Tindakan Lab):</span>
              <p>Terdapat item ekstra sebesar <strong class="text-amber-800">{{ formatRupiah(selectedInvoice.sisa_tagihan) }}</strong> di luar kuota sesi paket.</p>
              
              <div class="pt-2">
                <label class="block font-semibold mb-1">Metode Bayar Item Ekstra</label>
                <select v-model="bayarForm.metode_bayar" class="input-select">
                  <option value="qris">QRIS Statis / Dinamis</option>
                  <option value="tunai">Tunai / Cash</option>
                  <option value="transfer">Transfer Bank</option>
                  <option value="kartu_debit">Kartu Debit EDC</option>
                </select>
              </div>
            </div>
          </div>

          <!-- JALUR 2: PASIEN ASURANSI (KLAIM VS EXCESS CASH) -->
          <div v-else-if="bayarMode === 'asuransi'" class="space-y-3">
            <div class="p-3 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] space-y-1 text-[11px]">
              <span class="font-bold text-[#0B3948] block">Penjamin: {{ selectedInvoice.guarantor?.nama_penjamin || 'Asuransi Rekanan' }}</span>
              <p class="text-slate-600">Surat Jaminan (GL): <strong class="font-mono">{{ selectedInvoice.nomor_penjamin || selectedInvoice.registrasi_anak?.patient_insurance?.no_surat_jaminan || 'GL-TERDAFTAR' }}</strong></p>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Nominal yang Dijamin Asuransi (Rp)</label>
              <input
                v-model.number="asuransiForm.nominal_klaim_asuransi"
                type="number"
                min="0"
                class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] font-bold tabular-nums"
                placeholder="Masukkan nominal yang dicover asuransi..."
              />
            </div>

            <!-- Dynamic Computation Banner -->
            <div
              v-if="selisihAsuransi === 0"
              class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-2"
            >
              <i class="pi pi-check-circle text-base"></i>
              <div>
                <span class="font-bold block">Full Cover Asuransi</span>
                <span class="text-[10px]">Pasien tidak dikenakan biaya selisih (Rp 0). Biaya tercatat sebagai piutang penjamin.</span>
              </div>
            </div>

            <div
              v-else
              class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-900 space-y-2"
            >
              <div class="flex items-center justify-between">
                <span class="font-bold flex items-center gap-1.5">
                  <i class="pi pi-exclamation-triangle text-rose-600"></i>
                  <span>Selisih (Excess Cash) yang Wajib Dibayar Pasien:</span>
                </span>
                <span class="text-sm font-bold text-rose-700 tabular-nums">{{ formatRupiah(selisihAsuransi) }}</span>
              </div>

              <div class="grid grid-cols-2 gap-2 pt-1 border-t border-rose-200/60">
                <div>
                  <label class="block font-semibold text-[10px] text-slate-700 mb-0.5">Metode Bayar Selisih</label>
                  <select v-model="asuransiForm.metode_bayar_selisih" class="input-select text-xs">
                    <option value="tunai">Tunai / Cash</option>
                    <option value="qris">QRIS (BCA/Mandiri)</option>
                    <option value="kartu_debit">Kartu Debit EDC</option>
                    <option value="transfer">Transfer Bank</option>
                  </select>
                </div>
                <div>
                  <label class="block font-semibold text-[10px] text-slate-700 mb-0.5">No. Referensi / Struk</label>
                  <input
                    v-model="asuransiForm.nomor_referensi"
                    placeholder="EDC-12938 / QRIS Ref"
                    class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
                  />
                </div>
              </div>

              <label class="flex items-center gap-2 pt-1 text-[11px] font-semibold text-slate-800 cursor-pointer">
                <input type="checkbox" v-model="asuransiForm.bayar_selisih_cash" class="rounded text-[#0F766E]" />
                <span>Pasien melunasi selisih tunai/non-tunai di kasir sekarang</span>
              </label>
            </div>
          </div>

          <!-- JALUR 3: UMUM LEPAS (STANDAR) -->
          <div v-else class="space-y-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Metode Pembayaran</label>
              <select v-model="bayarForm.metode_bayar" class="input-select">
                <option value="qris">QRIS Statis / Dinamis (BCA, Mandiri, GoPay)</option>
                <option value="tunai">Tunai / Cash</option>
                <option value="transfer">Transfer Bank (BCA / Mandiri)</option>
                <option value="kartu_debit">Kartu Debit EDC</option>
                <option value="kartu_kredit">Kartu Kredit EDC</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Jumlah Bayar (Rp)</label>
              <input
                v-model.number="bayarForm.jumlah_bayar"
                type="number"
                min="0"
                class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] tabular-nums font-bold"
              />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Nomor Referensi Transaksi</label>
              <input
                v-model="bayarForm.nomor_referensi"
                placeholder="Contoh: QRIS-982312 atau No. Struk EDC"
                class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
              />
            </div>
          </div>

          <!-- Footer Actions -->
          <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-3 shrink-0">
            <button type="button" @click="showBayarModal = false" class="btn-secondary-clinical">Batal</button>
            <button
              type="button"
              @click="submitBayar"
              :disabled="submittingBayar"
              class="btn-primary-clinical"
            >
              <span v-if="submittingBayar" class="flex items-center gap-1.5">
                <i class="pi pi-spin pi-spinner text-sm"></i>
                Memproses...
              </span>
              <span v-else class="flex items-center gap-1.5">
                <i class="pi pi-check text-sm"></i>
                <span>{{ bayarMode === 'asuransi' ? (selisihAsuransi === 0 ? 'Verifikasi Terjamin Asuransi' : 'Pelunasan Excess & Piutang') : (bayarMode === 'paket' ? 'Cetak Bukti Sesi Paket (Rp 0)' : 'Konfirmasi Pelunasan') }}</span>
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
    </teleport>

    <!-- ========================================================================= -->
    <!-- MODAL REKONSILIASI KLAIM ASURANSI (STAGE 5 AR) -->
    <!-- ========================================================================= -->
    <teleport to="body">
    <div v-if="showReconcileModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fadeIn">
      <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 max-h-[90dvh] flex flex-col overflow-hidden">
        <div class="px-6 py-4 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
          <div class="flex items-center gap-2.5">
            <i class="pi pi-check-circle text-teal-300 text-base"></i>
            <h3 class="font-bold text-base text-white">Rekonsiliasi Pembayaran Klaim Asuransi</h3>
          </div>
          <button
            type="button"
            @click="showReconcileModal = false"
            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-white hover:bg-white/10 transition cursor-pointer"
          >
            ✕
          </button>
        </div>

        <div v-if="selectedClaim" class="p-6 flex-1 min-h-0 overflow-y-auto custom-scrollbar space-y-4 text-sm text-slate-700">
          <div class="bg-[#E6F2F5] border border-[#CBE3E9] p-4 rounded-xl space-y-1.5">
            <span class="font-bold text-[#0B3948] text-sm block">{{ selectedClaim.guarantor?.nama_penjamin }}</span>
            <p class="text-slate-700 text-sm">No. Pengajuan: <strong>{{ selectedClaim.nomor_pengajuan }}</strong></p>
            <p class="text-slate-700 text-sm">Nominal Diajukan: <strong class="text-slate-900 tabular-nums">{{ formatRupiah(selectedClaim.nominal_diajukan) }}</strong></p>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1.5 text-xs uppercase tracking-wide">Nominal Disetujui Penjamin (Rp)</label>
            <input
              v-model.number="reconcileForm.nominal_disetujui"
              type="number"
              class="w-full h-[38px] px-3.5 py-2 text-sm border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] font-bold tabular-nums"
            />
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1.5 text-xs uppercase tracking-wide">Catatan Rekonsiliasi Bank</label>
            <textarea
              v-model="reconcileForm.catatan"
              rows="2"
              placeholder="Contoh: Diterima via transfer rekening operasional klinik..."
              class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
            ></textarea>
          </div>

          <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
            <button type="button" @click="showReconcileModal = false" class="btn-secondary-clinical">Batal</button>
            <button type="button" @click="submitReconcile" class="btn-primary-clinical">
              Simpan Rekonsiliasi
            </button>
          </div>
        </div>
      </div>
    </div>
    </teleport>

    <!-- ========================================================================= -->
    <!-- MODAL PREVIEW STRUK THERMAL POS KASIR 58MM -->
    <!-- ========================================================================= -->
    <teleport to="body">
    <div v-if="showThermalModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fadeIn">
      <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl border border-slate-200 max-h-[90dvh] flex flex-col overflow-hidden">
        <div class="px-6 py-4 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
          <div class="flex items-center gap-2.5">
            <i class="pi pi-print text-teal-300 text-base"></i>
            <h3 class="font-bold text-base text-white">Preview Struk Kasir / Bukti Sesi</h3>
          </div>
          <button
            type="button"
            @click="showThermalModal = false"
            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-white hover:bg-white/10 transition cursor-pointer"
          >
            ✕
          </button>
        </div>

        <div class="p-4 flex-1 min-h-0 overflow-y-auto custom-scrollbar">
          <div class="bg-slate-50 p-4 font-mono text-[11px] border border-dashed border-slate-300 rounded-lg space-y-2 select-all leading-tight">
            <div class="text-center border-b border-dashed border-slate-300 pb-2">
              <p class="font-bold text-xs text-slate-900">KLINIK TERAPI TUMBUH KEMBANG ANAK</p>
              <p class="text-[9px] text-slate-500">SIMRS Clinical Information System</p>
              <p class="text-[9px] text-slate-500">Jl. Kesehatan Raya No. 45, Jakarta</p>
            </div>

            <div class="space-y-0.5 text-[10px]">
              <p>Invoice : {{ thermalData?.nomor_invoice }}</p>
              <p>Tanggal : {{ formatDate(thermalData?.created_at) }}</p>
              <p>Pasien  : {{ thermalData?.profile_anak?.nama_anak }}</p>
              <p>No. RM  : {{ thermalData?.profile_anak?.no_rm }}</p>
              <p>Track   : {{ thermalData?.payment_track === 'umum_package' ? 'Paket Terapi' : (thermalData?.payment_track === 'asuransi' ? 'Asuransi / Penjamin' : 'Umum Single') }}</p>
            </div>

            <div class="border-t border-b border-dashed border-slate-300 py-1.5 space-y-1">
              <div v-for="(it, i) in thermalData?.items" :key="i" class="flex justify-between text-[10px]">
                <span class="truncate max-w-[180px]">{{ it.deskripsi }}</span>
                <span class="tabular-nums">{{ formatRupiah(it.subtotal) }}</span>
              </div>
            </div>

            <div class="space-y-0.5 text-right font-bold text-[10px]">
              <div class="flex justify-between">
                <span>TOTAL BIAYA:</span>
                <span>{{ formatRupiah(thermalData?.total_tagihan) }}</span>
              </div>
              <div class="flex justify-between text-emerald-700">
                <span>TERBAYAR / DITANGGUNG:</span>
                <span>{{ formatRupiah(thermalData?.total_terbayar || thermalData?.total_tagihan) }}</span>
              </div>
              <div class="flex justify-between text-slate-500">
                <span>SISA:</span>
                <span>{{ formatRupiah(thermalData?.sisa_tagihan) }}</span>
              </div>
            </div>

            <div class="text-center pt-2 border-t border-dashed border-slate-300 text-[9px] text-slate-400">
              <p>Terima Kasih & Semoga Ananda Tumbuh Optimal</p>
            </div>
          </div>
        </div>

        <div class="px-4 py-2.5 bg-slate-50/95 border-t border-slate-200/80 flex items-center justify-end gap-2 shrink-0">
          <button type="button" @click="showThermalModal = false" class="btn-secondary h-[34px] px-3.5 text-xs">Tutup</button>
          <button type="button" @click="printThermalDirect" class="btn-primary h-[34px] px-4 text-xs font-semibold">
            <i class="pi pi-print text-xs"></i>
            <span>Cetak Printer POS</span>
          </button>
        </div>
      </div>
    </div>
    </teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '@/axios'

const router = useRouter()
const toast = useToast()

const activeTab = ref('invoices')
const invoices = ref([])
const claims = ref([])
const packagesList = ref([])
const loading = ref(false)
const filterStatus = ref('')
const shiftInfo = ref({ is_open: true })
const thermalData = ref(null)

const showBayarModal = ref(false)
const showReconcileModal = ref(false)
const showThermalModal = ref(false)
const selectedInvoice = ref(null)
const selectedClaim = ref(null)
const selectedClaims = ref([])
const submittingBayar = ref(false)
const pendingOrdersSummary = ref({ total_resep_menunggu: 0, total_penunjang_menunggu: 0 })

const summary = ref({
  totalTagihan: 0,
  totalTerbayar: 0,
  totalSisa: 0
})

const bayarMode = ref('umum')
const asuransiForm = ref({
  nominal_klaim_asuransi: 0,
  bayar_selisih_cash: true,
  metode_bayar_selisih: 'tunai',
  nomor_referensi: '',
  catatan: ''
})

const selisihAsuransi = computed(() => {
  if (!selectedInvoice.value) return 0
  const tot = Number(selectedInvoice.value.total_tagihan || 0)
  const klaim = Number(asuransiForm.value.nominal_klaim_asuransi || 0)
  return Math.max(0, tot - klaim)
})

const bayarForm = ref({
  metode_bayar: 'qris',
  jumlah_bayar: 0,
  nomor_referensi: ''
})

const reconcileForm = ref({
  nominal_disetujui: 0,
  catatan: ''
})

const isAllClaimsSelected = computed(() => {
  return claims.value.length > 0 && selectedClaims.value.length === claims.value.length
})

onMounted(async () => {
  await Promise.all([
    loadInvoices(),
    loadClaims(),
    loadPackages()
  ])
})

async function loadInvoices() {
  loading.value = true
  try {
    const res = await api.get('/billing/invoices', { params: { status: filterStatus.value } })
    invoices.value = res.data?.data || res.data || []
    calculateSummary()
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function loadClaims() {
  try {
    const res = await api.get('/pediatric-workflow/billing/insurance-claims')
    claims.value = res.data?.data || res.data || []
  } catch (e) {
    console.error(e)
  }
}

async function loadPackages() {
  try {
    const res = await api.get('/pediatric-workflow/packages')
    packagesList.value = res.data || []
  } catch (e) {
    console.error(e)
  }
}

function calculateSummary() {
  let tagihan = 0, bayar = 0, sisa = 0
  invoices.value.forEach(x => {
    tagihan += Number(x.total_tagihan || 0)
    bayar += Number(x.total_terbayar || 0)
    sisa += Number(x.sisa_tagihan || 0)
  })
  summary.value = { totalTagihan: tagihan, totalTerbayar: bayar, totalSisa: sisa }
}

const goToVerification = () => {
  showBayarModal.value = false
  router.push('/verifikasi-resep-penunjang')
}

async function openBayarModal(inv) {
  selectedInvoice.value = inv
  pendingOrdersSummary.value = { total_resep_menunggu: 0, total_penunjang_menunggu: 0 }

  if (inv.payment_track === 'asuransi' || inv.guarantor_id) {
    bayarMode.value = 'asuransi'
  } else if (inv.is_paket_lunas || inv.payment_track === 'umum_package') {
    bayarMode.value = 'paket'
  } else {
    bayarMode.value = 'umum'
  }

  asuransiForm.value = {
    nominal_klaim_asuransi: Number(inv.covered_amount || inv.nominal_klaim_asuransi || inv.total_tagihan || 0),
    bayar_selisih_cash: true,
    metode_bayar_selisih: 'tunai',
    nomor_referensi: '',
    catatan: 'Verifikasi klaim asuransi kasir'
  }

  bayarForm.value = {
    metode_bayar: 'qris',
    jumlah_bayar: Number(inv.sisa_tagihan || 0),
    nomor_referensi: ''
  }
  showBayarModal.value = true

  // Fetch live invoice items and pending orders if registrasi_anak_id is present
  try {
    const regId = inv.registrasi_anak_id || inv.profile_anak_id
    if (regId) {
      const ordRes = await api.get(`/pediatric-workflow/registration/${regId}/orders-summary`)
      if (ordRes.data) {
        pendingOrdersSummary.value = {
          total_resep_menunggu: ordRes.data.total_resep_menunggu || 0,
          total_penunjang_menunggu: ordRes.data.total_penunjang_menunggu || 0
        }
        if (ordRes.data.invoice && ordRes.data.invoice.items) {
          selectedInvoice.value.items = ordRes.data.invoice.items
          selectedInvoice.value.total_tagihan = ordRes.data.invoice.total_tagihan
          selectedInvoice.value.sisa_tagihan = ordRes.data.invoice.sisa_tagihan
          bayarForm.value.jumlah_bayar = Number(ordRes.data.invoice.sisa_tagihan || 0)
        }
      }
    }
  } catch (err) {
    console.error('Error fetching registration orders summary for kasir:', err)
  }
}

async function submitBayar() {
  submittingBayar.value = true
  try {
    if (bayarMode.value === 'asuransi') {
      const res = await api.post(`/pediatric-workflow/billing/${selectedInvoice.value.id}/klaim-asuransi`, asuransiForm.value)
      toast.success(res.data?.message || 'Proses jaminan asuransi & excess berhasil disimpan!')
    } else if (bayarMode.value === 'paket' && selectedInvoice.value.sisa_tagihan == 0) {
      await api.post(`/pediatric-workflow/billing/${selectedInvoice.value.id}/settle`, {
        metode_bayar: 'transfer',
        jumlah_bayar: 0,
        catatan: 'Voucher Potong Sesi Paket (Lunas di Awal)'
      })
      toast.success('Bukti potong sesi paket berhasil diverifikasi (Rp 0).')
    } else {
      await api.post(`/pediatric-workflow/billing/${selectedInvoice.value.id}/settle`, bayarForm.value)
      toast.success('Pembayaran kasir berhasil diselesaikan!')
    }

    showBayarModal.value = false
    await loadInvoices()
    await loadClaims()
  } catch (e) {
    toast.error('Gagal memproses pembayaran: ' + (e.response?.data?.message || e.message))
  } finally {
    submittingBayar.value = false
  }
}

function toggleSelectAllClaims(e) {
  if (e.target.checked) {
    selectedClaims.value = claims.value.filter(c => c.status !== 'lunas').map(c => c.id)
  } else {
    selectedClaims.value = []
  }
}

async function submitBatchClaims() {
  if (selectedClaims.value.length === 0) return
  try {
    await api.post('/pediatric-workflow/billing/submit-claims', { claim_ids: selectedClaims.value })
    toast.success(`${selectedClaims.value.length} klaim berhasil diajukan ke asuransi penjamin!`)
    selectedClaims.value = []
    await loadClaims()
  } catch (e) {
    toast.error('Gagal mengajukan klaim: ' + (e.response?.data?.message || e.message))
  }
}

function openReconcileModal(clm) {
  selectedClaim.value = clm
  reconcileForm.value = {
    nominal_disetujui: Number(clm.nominal_diajukan),
    catatan: 'Disetujui penuh oleh verifikator penjamin'
  }
  showReconcileModal.value = true
}

async function submitReconcile() {
  try {
    await api.post(`/pediatric-workflow/billing/claims/${selectedClaim.value.id}/reconcile`, reconcileForm.value)
    toast.success('Rekonsiliasi pembayaran klaim asuransi berhasil dicatat!')
    showReconcileModal.value = false
    await loadClaims()
    await loadInvoices()
  } catch (e) {
    toast.error('Gagal rekonsiliasi klaim: ' + (e.response?.data?.message || e.message))
  }
}

async function buyPackageForChild(pkg) {
  try {
    // Generate an invoice for this therapy package
    const anakRes = await api.get('/profile-anak')
    const anakList = anakRes.data?.data || anakRes.data || []
    if (anakList.length === 0) {
      toast.error('Belum ada pasien anak terdaftar untuk pembelian paket.')
      return
    }
    const child = anakList[0]

    const invPayload = {
      profile_anak_id: child.id,
      jenis_penjamin: 'Umum',
      items: [
        {
          tipe_item: 'paket_terapi',
          item_id: pkg.layanan_id,
          deskripsi: pkg.nama_paket,
          qty: 1,
          harga_satuan: pkg.harga_paket,
          total_sesi: pkg.jumlah_sesi
        }
      ]
    }
    await api.post('/billing/invoices', invPayload)
    toast.success(`Paket ${pkg.nama_paket} berhasil dibuatkan tagihan untuk ${child.nama_anak}!`)
    activeTab.value = 'invoices'
    await loadInvoices()
  } catch (e) {
    toast.error('Gagal membuat tagihan paket: ' + (e.response?.data?.message || e.message))
  }
}

function previewThermal(inv) {
  thermalData.value = inv
  showThermalModal.value = true
}

function printThermalDirect() {
  toast.success('Mencetak struk thermal ke printer kasir 58mm...')
  showThermalModal.value = false
}

function formatRupiah(val) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0)
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}
</script>

<style scoped>
.input-filter {
  @apply px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs outline-none focus:ring-2 focus:ring-[#0F766E]/20 focus:border-[#0F766E];
}
.input-select {
  @apply w-full px-3 py-2 rounded-lg border border-slate-300 bg-white text-xs outline-none focus:ring-2 focus:ring-[#0F766E]/20 focus:border-[#0F766E];
}
.btn-primary {
  @apply px-4 py-2 rounded-lg bg-[#0F766E] text-white font-semibold text-xs hover:bg-[#115E59] active:scale-95 transition inline-flex items-center gap-1.5 shadow-sm cursor-pointer;
}
.btn-secondary {
  @apply px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 font-semibold text-xs hover:bg-slate-50 transition cursor-pointer;
}
.animate-fadeIn {
  animation: fadeIn 0.15s ease;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.98) translateY(4px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
</style>
