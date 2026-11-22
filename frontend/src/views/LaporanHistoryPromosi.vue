<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-percentage"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Laporan Histori Promosi & Diskon</h2>
          <p class="text-xs text-[#64748B] mt-0.5">
            Periode: <span class="font-medium text-slate-700">{{ periode }}</span>
          </p>
        </div>
      </div>

      <button
        type="button"
        class="h-[34px] px-3.5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white text-xs font-semibold inline-flex items-center gap-1.5 shadow-sm transition shrink-0 cursor-pointer"
        @click="cetakPdf"
      >
        <i class="pi pi-print text-xs"></i>
        <span>Cetak PDF</span>
      </button>
    </div>

    <!-- SUMMARY STATS -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 shrink-0">
      <div class="bg-white rounded-xl shadow-2xs border border-slate-200 p-3 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9] flex items-center justify-center text-xs shrink-0 font-bold">
          <i class="pi pi-ticket"></i>
        </div>
        <div>
          <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Total Promosi</p>
          <p class="text-base font-bold text-slate-800 tabular-nums mt-0.5">{{ summary.totalPromo }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-2xs border border-slate-200 p-3 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-xs shrink-0 font-bold">
          <i class="pi pi-sync"></i>
        </div>
        <div>
          <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Total Dipakai</p>
          <p class="text-base font-bold text-slate-800 tabular-nums mt-0.5">{{ summary.totalDipakai }}x</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-2xs border border-slate-200 p-3 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-xs shrink-0 font-bold">
          <i class="pi pi-dollar"></i>
        </div>
        <div>
          <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Total Nominal</p>
          <p class="text-base font-bold text-slate-800 tabular-nums mt-0.5">{{ formatCurrency(summary.totalNominal) }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-2xs border border-slate-200 p-3 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-teal-50 text-[#0F766E] border border-teal-200 flex items-center justify-center text-xs shrink-0 font-bold">
          <i class="pi pi-star"></i>
        </div>
        <div class="min-w-0">
          <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Terpopuler</p>
          <p class="text-xs font-bold text-slate-800 truncate mt-0.5">{{ summary.topPromo || '-' }}</p>
        </div>
      </div>
    </div>

    <!-- FILTER -->
    <div class="bg-white rounded-xl shadow-2xs border border-slate-200 p-3 shrink-0">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end text-xs">
        <div>
          <label class="block text-[11px] font-medium text-slate-600 mb-1">Tanggal Mulai</label>
          <input
            type="date"
            v-model="startDate"
            class="w-full h-[34px] px-2.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:border-[#0F766E]"
          />
        </div>

        <div>
          <label class="block text-[11px] font-medium text-slate-600 mb-1">Tanggal Akhir</label>
          <input
            type="date"
            v-model="endDate"
            class="w-full h-[34px] px-2.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:border-[#0F766E]"
          />
        </div>

        <div class="md:col-span-2 flex gap-2">
          <button
            type="button"
            class="h-[34px] px-3.5 bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-medium text-xs rounded-lg inline-flex items-center gap-1.5 transition shadow-2xs cursor-pointer"
            @click="loadData"
          >
            <i class="pi pi-search text-xs"></i>
            <span>Terapkan Filter</span>
          </button>

          <button
            type="button"
            class="h-[34px] px-3.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium text-xs rounded-lg inline-flex items-center gap-1.5 transition cursor-pointer"
            @click="resetFilter"
          >
            <i class="pi pi-refresh text-xs"></i>
            <span>Reset</span>
          </button>
        </div>
      </div>
    </div>

    <!-- TABLE CARD -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-left border-collapse text-[12.5px]">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3 w-12 text-center">No</th>
              <th class="py-2.5 px-3">Kode Promosi</th>
              <th class="py-2.5 px-3">Nama Promosi</th>
              <th class="py-2.5 px-3 text-right">Nominal</th>
              <th class="py-2.5 px-3">Isi Promosi</th>
              <th class="py-2.5 px-3">Tanggal</th>
              <th class="py-2.5 px-3 text-center">Waktu</th>
              <th class="py-2.5 px-3 text-center">Dipakai</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr v-if="paginatedRows.length === 0">
              <td colspan="8" class="py-12 text-center text-slate-400">
                <i class="pi pi-folder-open text-2xl text-slate-300 block mb-1"></i>
                <span class="text-xs">Tidak ada data untuk periode ini</span>
              </td>
            </tr>

            <tr v-for="row in paginatedRows" :key="row.no" class="hover:bg-[#F0F6F8]/60 transition">
              <td class="py-2 px-3 text-center text-slate-400 tabular-nums">{{ row.no }}</td>
              <td class="py-2 px-3">
                <span class="font-mono text-xs font-semibold text-[#0F766E] bg-teal-50 px-2 py-0.5 rounded border border-teal-200">
                  {{ row.kode_promo }}
                </span>
              </td>
              <td class="py-2 px-3 font-semibold text-slate-800">{{ row.nama_promo }}</td>
              <td class="py-2 px-3 text-right font-semibold text-slate-900 tabular-nums">{{ row.nominal_promo }}</td>
              <td class="py-2 px-3 text-slate-600">{{ row.isi_promo }}</td>
              <td class="py-2 px-3 text-slate-600 tabular-nums">{{ row.tanggal_promo }}</td>
              <td class="py-2 px-3 text-center text-slate-600 tabular-nums">{{ row.waktu_promo }}</td>
              <td class="py-2 px-3 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                  {{ row.total_dipakai }}x
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- FOOTER PAGINATION -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] text-slate-500 flex items-center justify-between">
        <span>Halaman {{ currentPage }} dari {{ totalPages }} (Total: {{ rows.length }} Data)</span>

        <div class="flex items-center gap-1" v-if="totalPages > 1">
          <button
            type="button"
            class="h-7 px-2 rounded border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed text-xs"
            :disabled="currentPage === 1"
            @click="changePage(currentPage - 1)"
          >
            Prev
          </button>

          <span class="px-2 text-xs font-medium text-slate-700">{{ currentPage }} / {{ totalPages }}</span>

          <button
            type="button"
            class="h-7 px-2 rounded border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed text-xs"
            :disabled="currentPage === totalPages"
            @click="changePage(currentPage + 1)"
          >
            Next
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import api from '@/api'

const today = new Date().toISOString().slice(0, 10)

const startDate = ref(today)
const endDate = ref(today)
const rows = ref([])
const periode = ref('Semua Periode')

const currentPage = ref(1)
const perPage = ref(15)

const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return rows.value.slice(start, start + perPage.value)
})

const totalPages = computed(() =>
  Math.ceil(rows.value.length / perPage.value) || 1
)

const parseNominal = (val) => {
  if (typeof val === 'number') return val
  if (!val) return 0
  const cleaned = String(val).replace(/[^0-9]/g, '')
  return cleaned ? parseInt(cleaned, 10) : 0
}

const summary = computed(() => {
  if (rows.value.length === 0) {
    return { totalPromo: 0, totalDipakai: 0, totalNominal: 0, topPromo: '' }
  }

  const totalDipakai = rows.value.reduce((acc, r) => acc + (Number(r.total_dipakai) || 0), 0)
  const totalNominal = rows.value.reduce((acc, r) => acc + parseNominal(r.nominal_promo), 0)

  const top = rows.value.reduce((best, r) => {
    const usage = Number(r.total_dipakai) || 0
    return usage > (best.usage || -1) ? { name: r.nama_promo, usage } : best
  }, {})

  return {
    totalPromo: rows.value.length,
    totalDipakai,
    totalNominal,
    topPromo: top.name || ''
  }
})

const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0)
}

const changePage = (p) => {
  if (p < 1 || p > totalPages.value) return
  currentPage.value = p
}

const loadData = async () => {
  try {
    const res = await api.get('/laporan/promosi', {
      params: {
        start_date: startDate.value,
        end_date: endDate.value
      }
    })

    rows.value = res.data?.data || []
    periode.value = res.data?.periode || 'Semua Periode'
    currentPage.value = 1
  } catch (err) {
    console.error('Gagal mengambil laporan promosi:', err)
  }
}

const resetFilter = () => {
  startDate.value = today
  endDate.value = today
  loadData()
}

const cetakPdf = () => {
  window.open(
    `/api/laporan/promosi/cetak-pdf?start_date=${startDate.value}&end_date=${endDate.value}`,
    '_blank'
  )
}

loadData()
</script>
