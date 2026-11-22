<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-check-circle"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Verifikasi E-Resep & Order Penunjang</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Telaah administratif farmasi obat dan validasi hasil laboratorium/radiologi sebelum masuk ke faktur billing kasir</p>
        </div>
      </div>

      <div class="flex items-center gap-2 shrink-0">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]">
          <span class="w-1.5 h-1.5 rounded-full bg-[#0F766E] animate-pulse"></span>
          Kendali Mutu Pra-Billing
        </span>
        <button
          type="button"
          @click="refreshAll"
          class="p-2 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 text-slate-600 text-xs transition cursor-pointer"
          title="Refresh Antrian"
        >
          <i class="pi pi-refresh" :class="{ 'pi-spin': loadingStats }"></i>
        </button>
      </div>
    </div>

    <!-- METRICS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0">
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider">Telaah E-Resep</span>
          <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
        </div>
        <h3 class="text-lg font-bold text-amber-900 tabular-nums mt-0.5">{{ stats.resep_menunggu ?? 0 }} Antrian</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Menunggu verifikasi apoteker</p>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-semibold text-sky-700 uppercase tracking-wider">Verifikasi Penunjang</span>
          <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
        </div>
        <h3 class="text-lg font-bold text-sky-900 tabular-nums mt-0.5">{{ stats.penunjang_menunggu ?? 0 }} Order</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Lab, radiologi & diagnostik</p>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-semibold text-teal-700 uppercase tracking-wider">Resep Masuk Billing</span>
          <i class="pi pi-check text-xs text-teal-600"></i>
        </div>
        <h3 class="text-lg font-bold text-teal-900 tabular-nums mt-0.5">{{ stats.resep_selesai ?? 0 }} Resep</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Diverifikasi & ditagihkan</p>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">Penunjang Selesai</span>
          <i class="pi pi-check text-xs text-emerald-600"></i>
        </div>
        <h3 class="text-lg font-bold text-emerald-900 tabular-nums mt-0.5">{{ stats.penunjang_selesai ?? 0 }} Selesai</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Hasil lengkap & ditagihkan</p>
      </div>
    </div>

    <!-- MAIN CARD WITH TABS -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">
      <!-- TAB SELECTOR -->
      <div class="px-3.5 pt-3 border-b border-slate-200 flex items-center justify-between gap-2 shrink-0 bg-[#F8FAFC]">
        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="activeTab = 'resep'"
            class="px-3.5 py-2 rounded-t-lg text-xs font-bold transition flex items-center gap-2 cursor-pointer border-t-2"
            :class="activeTab === 'resep' ? 'bg-white text-[#0F766E] border-[#0F766E] shadow-2xs' : 'text-slate-600 hover:text-slate-900 border-transparent'"
          >
            <i class="pi pi-file-edit text-xs"></i>
            <span>1. Verifikasi E-Resep Farmasi</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold" :class="activeTab === 'resep' ? 'bg-[#E6F2F5] text-[#0F766E]' : 'bg-slate-200 text-slate-700'">
              {{ resepList.length }}
            </span>
          </button>

          <button
            type="button"
            @click="activeTab = 'penunjang'"
            class="px-3.5 py-2 rounded-t-lg text-xs font-bold transition flex items-center gap-2 cursor-pointer border-t-2"
            :class="activeTab === 'penunjang' ? 'bg-white text-[#0F766E] border-[#0F766E] shadow-2xs' : 'text-slate-600 hover:text-slate-900 border-transparent'"
          >
            <i class="pi pi-sliders-h text-xs"></i>
            <span>2. Verifikasi Order Penunjang</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold" :class="activeTab === 'penunjang' ? 'bg-[#E6F2F5] text-[#0F766E]' : 'bg-slate-200 text-slate-700'">
              {{ penunjangList.length }}
            </span>
          </button>
        </div>

        <span class="text-[11px] text-[#0F766E] font-medium hidden md:inline-flex items-center gap-1">
          <i class="pi pi-shield text-[10px]"></i>
          <span>Biaya belum masuk ke tagihan kasir sebelum diverifikasi</span>
        </span>
      </div>

      <!-- ================= TAB 1: E-RESEP FARMASI ================= -->
      <div v-if="activeTab === 'resep'" class="flex-1 min-h-0 flex flex-col overflow-hidden">
        <!-- Toolbar -->
        <div class="p-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shrink-0 bg-white">
          <div class="flex items-center gap-2.5 flex-wrap">
            <div class="relative w-72">
              <i class="pi pi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
              <input
                v-model="searchResep"
                type="text"
                placeholder="Cari nomor resep, nama anak, atau no RM..."
                class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
              />
            </div>

            <select
              v-model="filterStatusResep"
              @change="fetchResepList"
              class="text-xs border border-slate-300 rounded-lg px-3 py-1.5 bg-white text-slate-700 outline-none focus:border-[#0F766E]"
            >
              <option value="">Semua Status Antrian</option>
              <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
              <option value="sedang_disiapkan">Sedang Disiapkan</option>
              <option value="selesai_dispensing">Selesai (Masuk Billing)</option>
              <option value="ditolak">Ditolak</option>
            </select>
          </div>

          <span class="text-xs text-slate-500 font-medium">
            Ditemukan <strong>{{ filteredResep.length }}</strong> resep
          </span>
        </div>

        <!-- Resep Table -->
        <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
          <table class="w-full text-[12.5px] text-left text-slate-700">
            <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
              <tr>
                <th class="py-2.5 px-3 w-12 text-center">No</th>
                <th class="py-2.5 px-3">No. Resep & Waktu</th>
                <th class="py-2.5 px-3">Pasien Anak & No. RM</th>
                <th class="py-2.5 px-3">Dokter Peresep</th>
                <th class="py-2.5 px-3">Item Obat & Signa</th>
                <th class="py-2.5 px-3 text-right">Estimasi Biaya</th>
                <th class="py-2.5 px-3 text-center">Status Pra-Billing</th>
                <th class="py-2.5 px-3 text-right w-36">Aksi Telaah</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="loadingResep" class="text-center">
                <td colspan="8" class="py-10 text-slate-400">
                  <i class="pi pi-spin pi-spinner text-xl text-[#0F766E]"></i>
                  <p class="mt-2 text-xs">Memuat antrian E-Resep...</p>
                </td>
              </tr>
              <tr v-else-if="filteredResep.length === 0" class="text-center">
                <td colspan="8" class="py-10 text-slate-400 text-xs">
                  <i class="pi pi-inbox text-2xl text-slate-300 mb-1 block"></i>
                  <span>Tidak ada data antrian E-Resep sesuai filter.</span>
                </td>
              </tr>
              <tr v-for="(r, index) in filteredResep" :key="r.id" class="hover:bg-[#F0F6F8]/60 transition">
                <td class="py-2.5 px-3 text-center font-mono text-slate-400 text-xs">{{ index + 1 }}</td>
                <td class="py-2.5 px-3">
                  <span class="font-mono font-bold text-[#0F766E] text-xs block leading-tight">{{ r.nomor_resep }}</span>
                  <span class="text-[11px] text-slate-400">{{ formatDate(r.created_at) }}</span>
                </td>
                <td class="py-2.5 px-3">
                  <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded bg-[#E6F2F5] text-[#0F766E] flex items-center justify-center font-bold text-xs shrink-0">
                      {{ (r.profile_anak?.nama_anak || 'A').charAt(0) }}
                    </div>
                    <div>
                      <p class="font-bold text-slate-900 leading-tight">{{ r.profile_anak?.nama_anak }}</p>
                      <span class="text-[11px] font-mono text-slate-500">RM: {{ r.profile_anak?.no_rm }}</span>
                    </div>
                  </div>
                </td>
                <td class="py-2.5 px-3 text-xs text-slate-700">
                  <span class="font-medium block">{{ r.dokter?.nama || 'Dokter Spesialis Anak' }}</span>
                  <span class="text-[10.5px] text-slate-400">Klinik Tumbuh Kembang</span>
                </td>
                <td class="py-2.5 px-3">
                  <div class="space-y-1">
                    <div v-for="it in (r.items || []).slice(0, 2)" :key="it.id" class="text-xs">
                      <span class="font-semibold text-slate-800">{{ it.nama_obat }}</span>
                      <span class="text-slate-500 text-[11px]"> ({{ it.jumlah }} {{ it.satuan }} &bull; {{ it.signa }})</span>
                    </div>
                    <span v-if="(r.items || []).length > 2" class="text-[11px] text-[#0F766E] font-medium block">
                      +{{ (r.items || []).length - 2 }} item lainnya
                    </span>
                  </div>
                </td>
                <td class="py-2.5 px-3 text-right font-mono font-semibold text-slate-800">
                  {{ formatRupiah(calculateResepTotal(r)) }}
                </td>
                <td class="py-2.5 px-3 text-center">
                  <span
                    v-if="r.status === 'selesai_dispensing' || r.status === 'diverifikasi'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    MASUK BILLING
                  </span>
                  <span
                    v-else-if="r.status === 'ditolak'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 text-rose-800 border border-rose-200"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    DITOLAK
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-800 border border-amber-200 animate-pulse"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    BELUM MASUK BILL
                  </span>
                </td>
                <td class="py-2.5 px-3 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      type="button"
                      @click="openTelaahResepModal(r)"
                      class="px-2.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 transition cursor-pointer shadow-2xs"
                      :class="r.status === 'selesai_dispensing' ? 'bg-[#E6F2F5] text-[#0F766E] hover:bg-teal-100 border border-[#CBE3E9]' : 'bg-[#0F766E] text-white hover:bg-[#115E59]'"
                    >
                      <i :class="r.status === 'selesai_dispensing' ? 'pi pi-eye' : 'pi pi-check'" class="text-[10px]"></i>
                      <span>{{ r.status === 'selesai_dispensing' ? 'Lihat Telaah' : 'Verifikasi' }}</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ================= TAB 2: ORDER PENUNJANG ================= -->
      <div v-else-if="activeTab === 'penunjang'" class="flex-1 min-h-0 flex flex-col overflow-hidden">
        <!-- Toolbar -->
        <div class="p-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shrink-0 bg-white">
          <div class="flex items-center gap-2.5 flex-wrap">
            <div class="relative w-72">
              <i class="pi pi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
              <input
                v-model="searchPenunjang"
                type="text"
                placeholder="Cari nomor order, nama tindakan, pasien..."
                class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
              />
            </div>

            <select
              v-model="filterJenisPenunjang"
              @change="fetchPenunjangList"
              class="text-xs border border-slate-300 rounded-lg px-3 py-1.5 bg-white text-slate-700 outline-none focus:border-[#0F766E]"
            >
              <option value="">Semua Jenis Penunjang</option>
              <option value="laboratorium">Laboratorium</option>
              <option value="radiologi">Radiologi</option>
              <option value="terapi_tambahan">Tindakan / Terapi Tambahan</option>
            </select>

            <select
              v-model="filterStatusPenunjang"
              @change="fetchPenunjangList"
              class="text-xs border border-slate-300 rounded-lg px-3 py-1.5 bg-white text-slate-700 outline-none focus:border-[#0F766E]"
            >
              <option value="">Semua Status Antrian</option>
              <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
              <option value="selesai">Selesai (Masuk Billing)</option>
              <option value="dibatalkan">Dibatalkan</option>
            </select>
          </div>

          <span class="text-xs text-slate-500 font-medium">
            Ditemukan <strong>{{ filteredPenunjang.length }}</strong> order penunjang
          </span>
        </div>

        <!-- Penunjang Table -->
        <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
          <table class="w-full text-[12.5px] text-left text-slate-700">
            <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
              <tr>
                <th class="py-2.5 px-3 w-12 text-center">No</th>
                <th class="py-2.5 px-3">No. Order & Waktu</th>
                <th class="py-2.5 px-3">Pasien Anak & No. RM</th>
                <th class="py-2.5 px-3">Dokter Pemohon</th>
                <th class="py-2.5 px-3">Jenis & Nama Tindakan</th>
                <th class="py-2.5 px-3 text-right">Tarif Tindakan</th>
                <th class="py-2.5 px-3 text-center">Status Pra-Billing</th>
                <th class="py-2.5 px-3 text-right w-36">Aksi Verifikasi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="loadingPenunjang" class="text-center">
                <td colspan="8" class="py-10 text-slate-400">
                  <i class="pi pi-spin pi-spinner text-xl text-[#0F766E]"></i>
                  <p class="mt-2 text-xs">Memuat antrian order penunjang...</p>
                </td>
              </tr>
              <tr v-else-if="filteredPenunjang.length === 0" class="text-center">
                <td colspan="8" class="py-10 text-slate-400 text-xs">
                  <i class="pi pi-inbox text-2xl text-slate-300 mb-1 block"></i>
                  <span>Tidak ada antrian order penunjang sesuai filter.</span>
                </td>
              </tr>
              <tr v-for="(o, index) in filteredPenunjang" :key="o.id" class="hover:bg-[#F0F6F8]/60 transition">
                <td class="py-2.5 px-3 text-center font-mono text-slate-400 text-xs">{{ index + 1 }}</td>
                <td class="py-2.5 px-3">
                  <span class="font-mono font-bold text-[#0F766E] text-xs block leading-tight">{{ o.nomor_order }}</span>
                  <span class="text-[11px] text-slate-400">{{ formatDate(o.created_at) }}</span>
                </td>
                <td class="py-2.5 px-3">
                  <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded bg-[#E6F2F5] text-[#0F766E] flex items-center justify-center font-bold text-xs shrink-0">
                      {{ (o.profile_anak?.nama_anak || 'A').charAt(0) }}
                    </div>
                    <div>
                      <p class="font-bold text-slate-900 leading-tight">{{ o.profile_anak?.nama_anak }}</p>
                      <span class="text-[11px] font-mono text-slate-500">RM: {{ o.profile_anak?.no_rm }}</span>
                    </div>
                  </div>
                </td>
                <td class="py-2.5 px-3 text-xs text-slate-700">
                  <span class="font-medium block">{{ o.dokter?.nama || 'Dokter Spesialis' }}</span>
                  <span class="text-[10.5px] text-slate-400">Pengirim Order</span>
                </td>
                <td class="py-2.5 px-3">
                  <div class="flex items-center gap-1.5">
                    <span
                      class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                      :class="{
                        'bg-sky-50 text-sky-800 border border-sky-200': o.jenis_order === 'laboratorium',
                        'bg-purple-50 text-purple-800 border border-purple-200': o.jenis_order === 'radiologi',
                        'bg-teal-50 text-teal-800 border border-teal-200': o.jenis_order !== 'laboratorium' && o.jenis_order !== 'radiologi'
                      }"
                    >
                      {{ o.jenis_order }}
                    </span>
                    <span class="font-semibold text-slate-900 text-xs">{{ o.nama_tindakan }}</span>
                  </div>
                  <p v-if="o.catatan_klinis" class="text-[11px] text-slate-500 mt-0.5 line-clamp-1 italic">
                    "{{ o.catatan_klinis }}"
                  </p>
                </td>
                <td class="py-2.5 px-3 text-right font-mono font-semibold text-slate-800">
                  {{ formatRupiah(o.tarif) }}
                </td>
                <td class="py-2.5 px-3 text-center">
                  <span
                    v-if="o.status === 'selesai' || o.status === 'diverifikasi'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    MASUK BILLING
                  </span>
                  <span
                    v-else-if="o.status === 'dibatalkan'"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 text-rose-800 border border-rose-200"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    DIBATALKAN
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-800 border border-amber-200 animate-pulse"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    BELUM MASUK BILL
                  </span>
                </td>
                <td class="py-2.5 px-3 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      type="button"
                      @click="openVerifikasiPenunjangModal(o)"
                      class="px-2.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 transition cursor-pointer shadow-2xs"
                      :class="o.status === 'selesai' ? 'bg-[#E6F2F5] text-[#0F766E] hover:bg-teal-100 border border-[#CBE3E9]' : 'bg-[#0F766E] text-white hover:bg-[#115E59]'"
                    >
                      <i :class="o.status === 'selesai' ? 'pi pi-eye' : 'pi pi-check'" class="text-[10px]"></i>
                      <span>{{ o.status === 'selesai' ? 'Lihat Hasil' : 'Verifikasi' }}</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ================= MODAL TELAAH & VERIFIKASI E-RESEP ================= -->
    <div
      v-if="showResepModal && selectedResep"
      class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
    >
      <div class="bg-white border border-slate-200 rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E]">
              <i class="pi pi-file-edit text-sm"></i>
            </div>
            <div>
              <h3 class="font-bold text-slate-900 text-sm">
                Telaah & Verifikasi E-Resep Obat
              </h3>
              <p class="text-[11px] text-slate-500">Validasi telaah klinis apoteker sebelum biaya obat masuk ke tagihan billing pasien</p>
            </div>
          </div>
          <button
            type="button"
            @click="showResepModal = false"
            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition"
          >
            <i class="pi pi-times text-xs"></i>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-5 flex-1 min-h-0 overflow-y-auto custom-scrollbar space-y-3.5 text-xs">
          <!-- Patient Banner -->
          <div class="bg-[#E6F2F5] border border-[#CBE3E9] p-3 rounded-xl flex items-center justify-between flex-wrap gap-2">
            <div>
              <span class="text-[10.5px] font-mono font-bold text-[#0F766E]">{{ selectedResep.nomor_resep }}</span>
              <h4 class="text-sm font-bold text-slate-900 leading-tight">
                {{ selectedResep.profile_anak?.nama_anak }} ({{ selectedResep.profile_anak?.no_rm }})
              </h4>
              <p class="text-[11px] text-slate-600 mt-0.5">
                Dokter Peresep: <strong>{{ selectedResep.dokter?.nama || 'Dokter Spesialis' }}</strong> &bull;
                Usia: <strong>{{ selectedResep.profile_anak?.usia_tahun || 5 }} Thn</strong>
              </p>
            </div>

            <div class="text-right">
              <span class="text-[11px] text-slate-500 block">Riwayat Alergi:</span>
              <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                {{ selectedResep.profile_anak?.alergi || 'Tidak ada riwayat alergi obat' }}
              </span>
            </div>
          </div>

          <!-- TELAAH 3 ASPEK STANDAR KEMENKES -->
          <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 space-y-2">
            <span class="text-[11px] font-bold text-slate-800 uppercase tracking-wider block">
              Lembar Checklist Telaah Farmasetik & Klinis:
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-slate-700">
              <label class="flex items-center gap-1.5 cursor-pointer select-none">
                <input type="checkbox" checked class="rounded border-slate-300 text-[#0F766E]" />
                <span class="text-[11px] font-medium">1. Aspek Administratif</span>
              </label>
              <label class="flex items-center gap-1.5 cursor-pointer select-none">
                <input type="checkbox" checked class="rounded border-slate-300 text-[#0F766E]" />
                <span class="text-[11px] font-medium">2. Dosis & Frekuensi Anak</span>
              </label>
              <label class="flex items-center gap-1.5 cursor-pointer select-none">
                <input type="checkbox" checked class="rounded border-slate-300 text-[#0F766E]" />
                <span class="text-[11px] font-medium">3. Interaksi & Duplikasi Obat</span>
              </label>
            </div>
          </div>

          <!-- Prescription Items -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <span class="font-bold text-slate-800">Daftar Item Obat & Aturan Pakai:</span>
              <span class="text-[11px] text-slate-500">{{ (selectedResep.items || []).length }} item obat</span>
            </div>

            <div class="border border-slate-200 rounded-xl overflow-hidden">
              <table class="w-full text-xs text-left">
                <thead class="bg-[#F8FAFC] border-b border-slate-200 text-[10.5px] font-semibold uppercase text-slate-600">
                  <tr>
                    <th class="py-2 px-3">Nama Obat</th>
                    <th class="py-2 px-3">Signa / Aturan</th>
                    <th class="py-2 px-3">Waktu</th>
                    <th class="py-2 px-3 text-center">Jumlah</th>
                    <th class="py-2 px-3 text-right">Harga</th>
                    <th class="py-2 px-3 text-right">Subtotal</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="it in selectedResep.items" :key="it.id" class="hover:bg-slate-50">
                    <td class="py-2 px-3">
                      <span class="font-bold text-slate-900 block leading-tight">{{ it.nama_obat }}</span>
                      <span v-if="it.catatan_racikan" class="text-[10px] text-[#0F766E] italic block">
                        {{ it.catatan_racikan }}
                      </span>
                    </td>
                    <td class="py-2 px-3 font-medium text-slate-700">{{ it.signa }}</td>
                    <td class="py-2 px-3 text-slate-600">{{ it.waktu_minum }}</td>
                    <td class="py-2 px-3 text-center font-bold text-slate-800">{{ it.jumlah }} {{ it.satuan }}</td>
                    <td class="py-2 px-3 text-right tabular-nums text-slate-600">{{ formatRupiah(it.harga_satuan) }}</td>
                    <td class="py-2 px-3 text-right tabular-nums font-bold text-slate-900">{{ formatRupiah(it.subtotal) }}</td>
                  </tr>
                </tbody>
                <tfoot class="bg-[#F8FAFC] border-t border-slate-200 font-bold">
                  <tr>
                    <td colspan="5" class="py-2 px-3 text-right text-slate-700">Total Biaya Obat Masuk Billing:</td>
                    <td class="py-2 px-3 text-right text-[#0F766E] font-mono text-sm">
                      {{ formatRupiah(calculateResepTotal(selectedResep)) }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <!-- Catatan Telaah Apoteker -->
          <div>
            <label class="block font-semibold text-slate-700 mb-1">
              Catatan Telaah Farmasi (Apoteker):
            </label>
            <textarea
              v-model="catatanTelaah"
              rows="2"
              placeholder="Contoh: Dosis sudah diverifikasi sesuai BB 15 kg. Tidak ditemukan interaksi obat. Siap dispensing."
              class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
              :disabled="selectedResep.status === 'selesai_dispensing'"
            ></textarea>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between gap-2 shrink-0 bg-white">
          <div class="flex items-center gap-2">
            <button
              v-if="selectedResep.status !== 'selesai_dispensing' && selectedResep.status !== 'ditolak'"
              type="button"
              @click="openRejectResepModal"
              class="px-3.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 font-semibold text-xs transition"
            >
              <i class="pi pi-times-circle text-xs mr-1"></i>
              Tolak / Revisi ke Dokter
            </button>
          </div>

          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="showResepModal = false"
              class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold text-xs transition"
            >
              Tutup
            </button>

            <button
              v-if="selectedResep.status !== 'selesai_dispensing'"
              type="button"
              @click="approveResep"
              :disabled="processingResep"
              class="btn-primary-clinical text-xs py-2 px-4 flex items-center gap-1.5"
            >
              <i v-if="processingResep" class="pi pi-spin pi-spinner text-xs"></i>
              <i v-else class="pi pi-check text-xs"></i>
              <span>Verifikasi & Masukkan ke Billing</span>
            </button>
            <span v-else class="text-xs text-emerald-700 font-bold bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">
              <i class="pi pi-check text-xs mr-1"></i> Telah Masuk ke Billing
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- ================= MODAL VERIFIKASI ORDER PENUNJANG ================= -->
    <div
      v-if="showPenunjangModal && selectedPenunjang"
      class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
    >
      <div class="bg-white border border-slate-200 rounded-2xl shadow-xl w-full max-w-xl overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E]">
              <i class="pi pi-sliders-h text-sm"></i>
            </div>
            <div>
              <h3 class="font-bold text-slate-900 text-sm">
                Verifikasi Order Penunjang Klinis
              </h3>
              <p class="text-[11px] text-slate-500">Konfirmasi pelaksanaan tindakan & input hasil sebelum biaya masuk ke tagihan billing kasir</p>
            </div>
          </div>
          <button
            type="button"
            @click="showPenunjangModal = false"
            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition"
          >
            <i class="pi pi-times text-xs"></i>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-5 flex-1 min-h-0 overflow-y-auto custom-scrollbar space-y-3.5 text-xs">
          <!-- Patient Banner -->
          <div class="bg-[#E6F2F5] border border-[#CBE3E9] p-3 rounded-xl flex items-center justify-between flex-wrap gap-2">
            <div>
              <span class="text-[10.5px] font-mono font-bold text-[#0F766E]">{{ selectedPenunjang.nomor_order }}</span>
              <h4 class="text-sm font-bold text-slate-900 leading-tight">
                {{ selectedPenunjang.profile_anak?.nama_anak }} ({{ selectedPenunjang.profile_anak?.no_rm }})
              </h4>
              <p class="text-[11px] text-slate-600 mt-0.5">
                Dokter Pemohon: <strong>{{ selectedPenunjang.dokter?.nama || 'Dokter Spesialis' }}</strong>
              </p>
            </div>

            <div class="text-right">
              <span class="px-2 py-0.5 rounded text-[10.5px] font-bold uppercase tracking-wider bg-white border border-[#CBE3E9] text-[#0F766E]">
                {{ selectedPenunjang.jenis_order }}
              </span>
            </div>
          </div>

          <!-- Order Detail Box -->
          <div class="border border-slate-200 rounded-xl p-3.5 space-y-2 bg-slate-50">
            <div class="flex justify-between items-center">
              <span class="text-slate-500 font-medium">Nama Tindakan / Pemeriksaan:</span>
              <span class="font-bold text-slate-900 text-xs">{{ selectedPenunjang.nama_tindakan }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-slate-500 font-medium">Tarif Tindakan:</span>
              <span class="font-mono font-bold text-[#0F766E] text-sm">{{ formatRupiah(selectedPenunjang.tarif) }}</span>
            </div>
            <div v-if="selectedPenunjang.catatan_klinis" class="pt-2 border-t border-slate-200 text-slate-600">
              <span class="text-[11px] font-semibold text-slate-700 block mb-0.5">Catatan Indikasi Medis Dokter:</span>
              <p class="italic text-[11.5px]">"{{ selectedPenunjang.catatan_klinis }}"</p>
            </div>
          </div>

          <!-- Form Hasil Pemeriksaan -->
          <div>
            <label class="block font-semibold text-slate-700 mb-1">
              Hasil Pemeriksaan / Interpretasi Penunjang: <span class="text-rose-500">*</span>
            </label>
            <textarea
              v-model="hasilPemeriksaan"
              rows="4"
              placeholder="Contoh: Hasil pemeriksaan laboratorium CBC dalam batas normal pediatrik. Hemoglobin: 12.4 g/dL, Leukosit: 7.200 /uL. Tidak ditemukan tanda inflamasi akut."
              class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
              :disabled="selectedPenunjang.status === 'selesai'"
            ></textarea>
            <p class="text-[10.5px] text-slate-500 mt-1">Hasil ini akan terlampir pada dokumen EMR rekam medis anak dan ringkasan klinis.</p>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between gap-2 shrink-0 bg-white">
          <div class="flex items-center gap-2">
            <button
              v-if="selectedPenunjang.status !== 'selesai' && selectedPenunjang.status !== 'dibatalkan'"
              type="button"
              @click="openCancelPenunjangModal"
              class="px-3.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 font-semibold text-xs transition"
            >
              <i class="pi pi-times-circle text-xs mr-1"></i>
              Batalkan Order
            </button>
          </div>

          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="showPenunjangModal = false"
              class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold text-xs transition"
            >
              Tutup
            </button>

            <button
              v-if="selectedPenunjang.status !== 'selesai'"
              type="button"
              @click="approvePenunjang"
              :disabled="processingPenunjang"
              class="btn-primary-clinical text-xs py-2 px-4 flex items-center gap-1.5"
            >
              <i v-if="processingPenunjang" class="pi pi-spin pi-spinner text-xs"></i>
              <i v-else class="pi pi-check text-xs"></i>
              <span>Verifikasi & Masukkan ke Billing</span>
            </button>
            <span v-else class="text-xs text-emerald-700 font-bold bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">
              <i class="pi pi-check text-xs mr-1"></i> Telah Masuk ke Billing
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const activeTab = ref('resep')
const loadingStats = ref(false)
const stats = ref({})

// Tab 1: Resep states
const resepList = ref([])
const loadingResep = ref(false)
const searchResep = ref('')
const filterStatusResep = ref('')
const showResepModal = ref(false)
const selectedResep = ref(null)
const catatanTelaah = ref('')
const processingResep = ref(false)

// Tab 2: Penunjang states
const penunjangList = ref([])
const loadingPenunjang = ref(false)
const searchPenunjang = ref('')
const filterJenisPenunjang = ref('')
const filterStatusPenunjang = ref('')
const showPenunjangModal = ref(false)
const selectedPenunjang = ref(null)
const hasilPemeriksaan = ref('')
const processingPenunjang = ref(false)

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(val || 0)
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const calculateResepTotal = (resep) => {
  if (!resep || !resep.items) return 0
  return resep.items.reduce((sum, it) => sum + Number(it.subtotal || (it.harga_satuan * it.jumlah) || 0), 0)
}

const fetchStats = async () => {
  loadingStats.value = true
  try {
    const res = await api.get('/pediatric-workflow/verifikasi/stats')
    stats.value = res.data || {}
  } catch (err) {
    console.error('Error fetching verification stats:', err)
  } finally {
    loadingStats.value = false
  }
}

const fetchResepList = async () => {
  loadingResep.value = true
  try {
    const params = {}
    if (filterStatusResep.value) params.status = filterStatusResep.value
    const res = await api.get('/pediatric-workflow/verifikasi/antrian-resep', { params })
    resepList.value = Array.isArray(res.data) ? res.data : []
  } catch (err) {
    console.error('Error fetching resep list:', err)
  } finally {
    loadingResep.value = false
  }
}

const fetchPenunjangList = async () => {
  loadingPenunjang.value = true
  try {
    const params = {}
    if (filterJenisPenunjang.value) params.jenis_order = filterJenisPenunjang.value
    if (filterStatusPenunjang.value) params.status = filterStatusPenunjang.value
    const res = await api.get('/pediatric-workflow/verifikasi/antrian-penunjang', { params })
    penunjangList.value = Array.isArray(res.data) ? res.data : []
  } catch (err) {
    console.error('Error fetching penunjang list:', err)
  } finally {
    loadingPenunjang.value = false
  }
}

const refreshAll = async () => {
  await Promise.all([fetchStats(), fetchResepList(), fetchPenunjangList()])
}

// Filtered lists
const filteredResep = computed(() => {
  let list = resepList.value
  if (searchResep.value.trim()) {
    const q = searchResep.value.toLowerCase().trim()
    list = list.filter(r =>
      (r.nomor_resep || '').toLowerCase().includes(q) ||
      (r.profile_anak?.nama_anak || '').toLowerCase().includes(q) ||
      (r.profile_anak?.no_rm || '').toLowerCase().includes(q) ||
      (r.dokter?.nama || '').toLowerCase().includes(q)
    )
  }
  return list
})

const filteredPenunjang = computed(() => {
  let list = penunjangList.value
  if (searchPenunjang.value.trim()) {
    const q = searchPenunjang.value.toLowerCase().trim()
    list = list.filter(o =>
      (o.nomor_order || '').toLowerCase().includes(q) ||
      (o.nama_tindakan || '').toLowerCase().includes(q) ||
      (o.profile_anak?.nama_anak || '').toLowerCase().includes(q) ||
      (o.profile_anak?.no_rm || '').toLowerCase().includes(q) ||
      (o.dokter?.nama || '').toLowerCase().includes(q)
    )
  }
  return list
})

// Modal Actions Resep
const openTelaahResepModal = (resep) => {
  selectedResep.value = resep
  catatanTelaah.value = resep.catatan_telaah || 'Telaah klinis administratif, farmasetik, dan ketiadaan interaksi obat telah disetujui apoteker.'
  showResepModal.value = true
}

const approveResep = async () => {
  if (!selectedResep.value) return
  processingResep.value = true
  try {
    const res = await api.post(`/pediatric-workflow/verifikasi/resep/${selectedResep.value.id}/approve`, {
      catatan_telaah: catatanTelaah.value
    })
    toast.success(res.data?.message || 'E-Resep berhasil diverifikasi & obat masuk ke Billing!')
    showResepModal.value = false
    await refreshAll()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal memverifikasi E-Resep.')
  } finally {
    processingResep.value = false
  }
}

const openRejectResepModal = async () => {
  const alasan = prompt('Masukkan alasan penolakan/revisi E-Resep untuk dokter pengirim:')
  if (!alasan || !alasan.trim()) return

  processingResep.value = true
  try {
    const res = await api.post(`/pediatric-workflow/verifikasi/resep/${selectedResep.value.id}/reject`, {
      alasan_penolakan: alasan
    })
    toast.info(res.data?.message || 'E-Resep telah ditolak/dikembalikan ke dokter.')
    showResepModal.value = false
    await refreshAll()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menolak resep.')
  } finally {
    processingResep.value = false
  }
}

// Modal Actions Penunjang
const openVerifikasiPenunjangModal = (order) => {
  selectedPenunjang.value = order
  hasilPemeriksaan.value = order.hasil_pemeriksaan || ''
  showPenunjangModal.value = true
}

const approvePenunjang = async () => {
  if (!selectedPenunjang.value) return
  if (!hasilPemeriksaan.value.trim()) {
    toast.error('Mohon isi hasil pemeriksaan / kesimpulan penunjang terlebih dahulu!')
    return
  }

  processingPenunjang.value = true
  try {
    const res = await api.post(`/pediatric-workflow/verifikasi/penunjang/${selectedPenunjang.value.id}/approve`, {
      hasil_pemeriksaan: hasilPemeriksaan.value
    })
    toast.success(res.data?.message || 'Order penunjang berhasil diverifikasi & biaya masuk ke Billing!')
    showPenunjangModal.value = false
    await refreshAll()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal memverifikasi order penunjang.')
  } finally {
    processingPenunjang.value = false
  }
}

const openCancelPenunjangModal = async () => {
  const alasan = prompt('Masukkan alasan pembatalan order penunjang:')
  if (!alasan || !alasan.trim()) return

  processingPenunjang.value = true
  try {
    const res = await api.post(`/pediatric-workflow/verifikasi/penunjang/${selectedPenunjang.value.id}/reject`, {
      alasan_batal: alasan
    })
    toast.info(res.data?.message || 'Order penunjang berhasil dibatalkan.')
    showPenunjangModal.value = false
    await refreshAll()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal membatalkan order penunjang.')
  } finally {
    processingPenunjang.value = false
  }
}

onMounted(() => {
  refreshAll()
})
</script>
