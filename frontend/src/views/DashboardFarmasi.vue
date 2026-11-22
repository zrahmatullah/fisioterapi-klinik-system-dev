<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-box"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Dashboard Farmasi & Antrian E-Resep Dokter</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Ruang kerja verifikasi telaah resep dokter, validasi ketersediaan obat FEFO, dan penyerahan obat sebelum masuk billing tagihan registrasi</p>
        </div>
      </div>

      <div class="flex items-center gap-2 shrink-0">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]">
          <span class="w-1.5 h-1.5 rounded-full bg-[#0F766E] animate-pulse"></span>
          Apoteker Bertugas
        </span>
        <button
          type="button"
          @click="refreshData"
          class="p-2 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 text-slate-600 text-xs transition cursor-pointer"
          title="Refresh Antrian"
        >
          <i class="pi pi-refresh" :class="{ 'pi-spin': loading }"></i>
        </button>
      </div>
    </div>

    <!-- METRICS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0">
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider">Menunggu Telaah</span>
          <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
        </div>
        <h3 class="text-lg font-bold text-amber-900 tabular-nums mt-0.5">{{ countMenunggu }} Antrian</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Resep dokter belum masuk bill</p>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-semibold text-sky-700 uppercase tracking-wider">Sedang Disiapkan</span>
          <i class="pi pi-clock text-xs text-sky-600"></i>
        </div>
        <h3 class="text-lg font-bold text-sky-900 tabular-nums mt-0.5">{{ countSedangDisiapkan }} Resep</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Peracikan obat di apotek</p>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-semibold text-teal-700 uppercase tracking-wider">Masuk Bill Registrasi</span>
          <i class="pi pi-check-circle text-xs text-teal-600"></i>
        </div>
        <h3 class="text-lg font-bold text-teal-900 tabular-nums mt-0.5">{{ countSelesai }} Resep</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Diverifikasi & ditagihkan</p>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">Total Nilai Obat Ditagihkan</span>
          <i class="pi pi-wallet text-xs text-emerald-600"></i>
        </div>
        <h3 class="text-lg font-bold text-emerald-900 tabular-nums mt-0.5">{{ formatRupiah(totalNominalSelesai) }}</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Akumulasi biaya obat masuk billing</p>
      </div>
    </div>

    <!-- MAIN TABLE CARD -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">
      <!-- Toolbar & Filter Tabs -->
      <div class="p-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shrink-0 bg-white">
        <div class="flex items-center gap-1.5 flex-wrap">
          <button
            type="button"
            @click="activeFilter = 'menunggu'"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
            :class="activeFilter === 'menunggu' ? 'bg-[#0F766E] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
          >
            <span>Menunggu Telaah (Pra-Billing)</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeFilter === 'menunggu' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'">
              {{ countMenunggu }}
            </span>
          </button>

          <button
            type="button"
            @click="activeFilter = 'selesai'"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
            :class="activeFilter === 'selesai' ? 'bg-[#0F766E] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
          >
            <span>Selesai Masuk Bill</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeFilter === 'selesai' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'">
              {{ countSelesai }}
            </span>
          </button>

          <button
            type="button"
            @click="activeFilter = 'semua'"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
            :class="activeFilter === 'semua' ? 'bg-[#0F766E] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
          >
            <span>Semua Resep</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeFilter === 'semua' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'">
              {{ resepList.length }}
            </span>
          </button>
        </div>

        <div class="relative w-full sm:w-64">
          <i class="pi pi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari no. resep, nama anak, no. RM..."
            class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
          />
        </div>
      </div>

      <!-- Table Body -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-[12.5px] text-left text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3 w-12 text-center">No</th>
              <th class="py-2.5 px-3">No. Resep & Waktu Order</th>
              <th class="py-2.5 px-3">Pasien Anak & No. RM</th>
              <th class="py-2.5 px-3">No. Registrasi Kunjungan</th>
              <th class="py-2.5 px-3">Dokter Peresep</th>
              <th class="py-2.5 px-3">Rincian Obat & Signa</th>
              <th class="py-2.5 px-3 text-right">Subtotal Obat</th>
              <th class="py-2.5 px-3 text-center">Status Billing</th>
              <th class="py-2.5 px-3 text-right w-36">Aksi Telaah</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loading" class="text-center">
              <td colspan="9" class="py-10 text-slate-400">
                <i class="pi pi-spin pi-spinner text-xl text-[#0F766E]"></i>
                <p class="mt-2 text-xs">Memuat antrian E-Resep Farmasi...</p>
              </td>
            </tr>
            <tr v-else-if="filteredResep.length === 0" class="text-center">
              <td colspan="9" class="py-10 text-slate-400 text-xs">
                <i class="pi pi-inbox text-2xl text-slate-300 mb-1 block"></i>
                <span>Tidak ada antrian resep dalam kategori ini.</span>
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
              <td class="py-2.5 px-3">
                <span class="px-2 py-0.5 rounded font-mono text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                  {{ r.registrasi_anak?.no_regis || 'REG-TERKUNCI' }}
                </span>
              </td>
              <td class="py-2.5 px-3 text-xs text-slate-700">
                <span class="font-medium block">{{ r.dokter?.nama || 'Dokter Spesialis' }}</span>
                <span class="text-[10.5px] text-slate-400">Order EMR</span>
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
                  MASUK BILL
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
                <button
                  type="button"
                  @click="openTelaahModal(r)"
                  class="px-2.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 transition cursor-pointer shadow-2xs ml-auto"
                  :class="r.status === 'selesai_dispensing' ? 'bg-[#E6F2F5] text-[#0F766E] hover:bg-teal-100 border border-[#CBE3E9]' : 'bg-[#0F766E] text-white hover:bg-[#115E59]'"
                >
                  <i :class="r.status === 'selesai_dispensing' ? 'pi pi-eye' : 'pi pi-check'" class="text-[10px]"></i>
                  <span>{{ r.status === 'selesai_dispensing' ? 'Lihat Telaah' : 'Telaah & Verifikasi' }}</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL TELAAH & VERIFIKASI RESEP FARMASI -->
    <div
      v-if="showModal && selectedResep"
      class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
    >
      <div class="bg-white border border-slate-200 rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-150">
        <!-- Header -->
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E]">
              <i class="pi pi-box text-sm"></i>
            </div>
            <div>
              <h3 class="font-bold text-slate-900 text-sm">
                Telaah Resep & Verifikasi Masuk Billing
              </h3>
              <p class="text-[11px] text-slate-500">Verifikasi apoteker untuk menerbitkan beban biaya obat ke faktur registrasi pasien</p>
            </div>
          </div>
          <button
            type="button"
            @click="showModal = false"
            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition"
          >
            <i class="pi pi-times text-xs"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="p-5 flex-1 min-h-0 overflow-y-auto custom-scrollbar space-y-3.5 text-xs">
          <!-- Context Banner -->
          <div class="bg-[#E6F2F5] border border-[#CBE3E9] p-3.5 rounded-xl space-y-1.5">
            <div class="flex items-center justify-between">
              <span class="font-mono text-xs font-bold text-[#0F766E]">{{ selectedResep.nomor_resep }}</span>
              <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-white text-slate-800 border border-[#CBE3E9]">
                Regis: {{ selectedResep.registrasi_anak?.no_regis || 'REG-AKTIF' }}
              </span>
            </div>
            <div class="flex items-center justify-between flex-wrap gap-2">
              <div>
                <h4 class="text-sm font-bold text-slate-900">{{ selectedResep.profile_anak?.nama_anak }} ({{ selectedResep.profile_anak?.no_rm }})</h4>
                <p class="text-[11px] text-slate-600">Dokter Peresep: <strong>{{ selectedResep.dokter?.nama || 'Dokter Spesialis' }}</strong></p>
              </div>
              <div class="text-right">
                <span class="text-[10.5px] text-slate-500 block">Riwayat Alergi Pasien:</span>
                <span class="px-2 py-0.5 rounded text-[10.5px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                  {{ selectedResep.profile_anak?.alergi || 'Tidak ada riwayat alergi' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Checklist 3 Aspek -->
          <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 space-y-2">
            <span class="text-[11px] font-bold text-slate-800 uppercase tracking-wider block">
              Lembar Konfirmasi Telaah Apoteker:
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-slate-700">
              <label class="flex items-center gap-1.5 cursor-pointer select-none">
                <input type="checkbox" checked class="rounded border-slate-300 text-[#0F766E]" />
                <span class="text-[11px] font-medium">Aspek Administratif Sah</span>
              </label>
              <label class="flex items-center gap-1.5 cursor-pointer select-none">
                <input type="checkbox" checked class="rounded border-slate-300 text-[#0F766E]" />
                <span class="text-[11px] font-medium">Dosis Sesuai Usia/BB</span>
              </label>
              <label class="flex items-center gap-1.5 cursor-pointer select-none">
                <input type="checkbox" checked class="rounded border-slate-300 text-[#0F766E]" />
                <span class="text-[11px] font-medium">Bebas Interaksi Obat</span>
              </label>
            </div>
          </div>

          <!-- Items Table -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <span class="font-bold text-slate-800">Daftar Obat Yang Diberikan:</span>
              <span class="text-[11px] text-slate-500">{{ (selectedResep.items || []).length }} item</span>
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
                    <td colspan="5" class="py-2 px-3 text-right text-slate-700">Total Biaya Obat Masuk Bill Registrasi:</td>
                    <td class="py-2 px-3 text-right text-[#0F766E] font-mono text-sm">
                      {{ formatRupiah(calculateResepTotal(selectedResep)) }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <!-- Catatan Telaah -->
          <div>
            <label class="block font-semibold text-slate-700 mb-1">
              Catatan Telaah Apoteker:
            </label>
            <textarea
              v-model="catatanTelaah"
              rows="2"
              placeholder="Catatan telaah farmasi dan instruksi khusus penyerahan obat..."
              class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
              :disabled="selectedResep.status === 'selesai_dispensing'"
            ></textarea>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between gap-2 shrink-0 bg-white">
          <div>
            <button
              v-if="selectedResep.status !== 'selesai_dispensing' && selectedResep.status !== 'ditolak'"
              type="button"
              @click="rejectResep"
              class="px-3.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 font-semibold text-xs transition"
            >
              <i class="pi pi-times-circle text-xs mr-1"></i>
              Tolak / Revisi ke Dokter
            </button>
          </div>

          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="showModal = false"
              class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold text-xs transition"
            >
              Tutup
            </button>

            <button
              v-if="selectedResep.status !== 'selesai_dispensing'"
              type="button"
              @click="approveResep"
              :disabled="saving"
              class="btn-primary-clinical text-xs py-2 px-4 flex items-center gap-1.5"
            >
              <i v-if="saving" class="pi pi-spin pi-spinner text-xs"></i>
              <i v-else class="pi pi-check text-xs"></i>
              <span>Verifikasi & Masukkan ke Bill Registrasi</span>
            </button>
            <span v-else class="text-xs text-emerald-700 font-bold bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">
              <i class="pi pi-check text-xs mr-1"></i> Telah Masuk ke Bill Registrasi
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

const resepList = ref([])
const loading = ref(false)
const saving = ref(false)
const activeFilter = ref('menunggu')
const searchQuery = ref('')
const showModal = ref(false)
const selectedResep = ref(null)
const catatanTelaah = ref('')

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

const fetchResepList = async () => {
  loading.value = true
  try {
    const res = await api.get('/pediatric-workflow/verifikasi/antrian-resep', {
      params: { status: 'semua' }
    })
    resepList.value = Array.isArray(res.data) ? res.data : []
  } catch (err) {
    console.error('Error fetching resep:', err)
  } finally {
    loading.value = false
  }
}

const refreshData = () => {
  fetchResepList()
}

const countMenunggu = computed(() => {
  return resepList.value.filter(r => r.status === 'menunggu_verifikasi' || r.status === 'menunggu_dispensing').length
})

const countSedangDisiapkan = computed(() => {
  return resepList.value.filter(r => r.status === 'sedang_disiapkan').length
})

const countSelesai = computed(() => {
  return resepList.value.filter(r => r.status === 'selesai_dispensing' || r.status === 'diverifikasi').length
})

const totalNominalSelesai = computed(() => {
  return resepList.value
    .filter(r => r.status === 'selesai_dispensing' || r.status === 'diverifikasi')
    .reduce((sum, r) => sum + calculateResepTotal(r), 0)
})

const filteredResep = computed(() => {
  let list = resepList.value

  if (activeFilter.value === 'menunggu') {
    list = list.filter(r => r.status === 'menunggu_verifikasi' || r.status === 'menunggu_dispensing')
  } else if (activeFilter.value === 'selesai') {
    list = list.filter(r => r.status === 'selesai_dispensing' || r.status === 'diverifikasi')
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    list = list.filter(r =>
      (r.nomor_resep || '').toLowerCase().includes(q) ||
      (r.profile_anak?.nama_anak || '').toLowerCase().includes(q) ||
      (r.profile_anak?.no_rm || '').toLowerCase().includes(q) ||
      (r.registrasi_anak?.no_regis || '').toLowerCase().includes(q) ||
      (r.dokter?.nama || '').toLowerCase().includes(q)
    )
  }

  return list
})

const openTelaahModal = (resep) => {
  selectedResep.value = resep
  catatanTelaah.value = resep.catatan_telaah || 'Telaah klinis administratif, farmasetik, dan ketiadaan interaksi obat telah disetujui apoteker.'
  showModal.value = true
}

const approveResep = async () => {
  if (!selectedResep.value) return
  saving.value = true
  try {
    const res = await api.post(`/pediatric-workflow/verifikasi/resep/${selectedResep.value.id}/approve`, {
      catatan_telaah: catatanTelaah.value
    })
    toast.success(res.data?.message || 'E-Resep berhasil diverifikasi & obat masuk ke tagihan registrasi!')
    showModal.value = false
    fetchResepList()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal memverifikasi resep.')
  } finally {
    saving.value = false
  }
}

const rejectResep = async () => {
  const alasan = prompt('Masukkan alasan penolakan/revisi E-Resep ke dokter:')
  if (!alasan || !alasan.trim()) return

  saving.value = true
  try {
    const res = await api.post(`/pediatric-workflow/verifikasi/resep/${selectedResep.value.id}/reject`, {
      alasan_penolakan: alasan
    })
    toast.info(res.data?.message || 'E-Resep ditolak/dikembalikan ke dokter.')
    showModal.value = false
    fetchResepList()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menolak resep.')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  fetchResepList()
})
</script>
