<template>
  <div class="min-h-screen bg-slate-50 p-4 md:p-8 space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div>
        <div class="flex items-center gap-2 text-xs font-semibold text-indigo-500 uppercase tracking-wide mb-1">
          <i class="ti ti-discount-2"></i>
          Laporan
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
          Histori Promosi
        </h1>
        <p class="text-sm text-slate-500 mt-0.5">
          Periode: <span class="font-medium text-slate-700">{{ periode }}</span>
        </p>
      </div>

      <button class="btn-pdf" @click="cetakPdf">
        <i class="ti ti-printer"></i>
        Cetak PDF
      </button>
    </div>

    <!-- SUMMARY STATS -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="stat-card">
        <div class="stat-icon bg-indigo-50 text-indigo-600">
          <i class="ti ti-ticket"></i>
        </div>
        <div>
          <p class="stat-label">Total Promosi</p>
          <p class="stat-value">{{ summary.totalPromo }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon bg-emerald-50 text-emerald-600">
          <i class="ti ti-repeat"></i>
        </div>
        <div>
          <p class="stat-label">Total Dipakai</p>
          <p class="stat-value">{{ summary.totalDipakai }}x</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon bg-amber-50 text-amber-600">
          <i class="ti ti-coin"></i>
        </div>
        <div>
          <p class="stat-label">Total Nominal</p>
          <p class="stat-value">{{ formatCurrency(summary.totalNominal) }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon bg-rose-50 text-rose-600">
          <i class="ti ti-trending-up"></i>
        </div>
        <div>
          <p class="stat-label">Promosi Terpopuler</p>
          <p class="stat-value text-base truncate">{{ summary.topPromo || '-' }}</p>
        </div>
      </div>
    </div>

    <!-- FILTER -->
    <div class="card p-5">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
          <label class="label">Tanggal Mulai</label>
          <input type="date" v-model="startDate" class="input" />
        </div>

        <div>
          <label class="label">Tanggal Akhir</label>
          <input type="date" v-model="endDate" class="input" />
        </div>

        <div class="md:col-span-2 flex gap-3">
          <button class="btn-primary" @click="loadData">
            <i class="ti ti-search"></i>
            Cari
          </button>

          <button class="btn-secondary" @click="resetFilter">
            <i class="ti ti-refresh"></i>
            Reset
          </button>
        </div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr>
              <th class="th text-center w-12">No</th>
              <th class="th w-32">Kode Promosi</th>
              <th class="th w-48">Nama Promosi</th>
              <th class="th text-right w-28">Nominal</th>
              <th class="th w-72">Isi Promosi</th>
              <th class="th w-44">Tanggal</th>
              <th class="th text-center w-24">Waktu</th>
              <th class="th text-center w-24">Dipakai</th>
            </tr>
          </thead>

          <tbody>
            <tr v-if="paginatedRows.length === 0">
              <td colspan="8" class="py-12 text-center text-slate-400">
                <i class="ti ti-mood-empty text-3xl block mb-2"></i>
                Tidak ada data untuk periode ini
              </td>
            </tr>

            <tr v-for="row in paginatedRows" :key="row.no" class="row">
              <td class="td text-center text-slate-400">{{ row.no }}</td>
              <td class="td">
                <span class="code-badge">{{ row.kode_promo }}</span>
              </td>
              <td class="td font-medium text-slate-700">{{ row.nama_promo }}</td>
              <td class="td text-right font-semibold text-slate-700">{{ row.nominal_promo }}</td>
              <td class="td text-slate-500">{{ row.isi_promo }}</td>
              <td class="td text-slate-500">{{ row.tanggal_promo }}</td>
              <td class="td text-center text-slate-500">{{ row.waktu_promo }}</td>
              <td class="td text-center">
                <span class="usage-badge">{{ row.total_dipakai }}x</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div v-if="totalPages > 1" class="flex justify-between items-center px-5 py-4 border-t border-slate-100">
        <div class="text-sm text-slate-500">
          Halaman {{ currentPage }} dari {{ totalPages }}
        </div>

        <div class="flex gap-1.5">
          <button class="page-btn" :disabled="currentPage === 1" @click="changePage(currentPage - 1)">
            <i class="ti ti-chevron-left"></i>
          </button>

          <button
            v-for="p in totalPages"
            :key="p"
            @click="changePage(p)"
            class="page-btn"
            :class="{ active: p === currentPage }"
          >
            {{ p }}
          </button>

          <button class="page-btn" :disabled="currentPage === totalPages" @click="changePage(currentPage + 1)">
            <i class="ti ti-chevron-right"></i>
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
const perPage = ref(10)

const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return rows.value.slice(start, start + perPage.value)
})

const totalPages = computed(() =>
  Math.ceil(rows.value.length / perPage.value)
)

// Parse nominal string (e.g. "Rp 50.000" or "50000") into a number for aggregation
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
  const res = await api.get('/laporan/promosi', {
    params: {
      start_date: startDate.value,
      end_date: endDate.value
    }
  })

  rows.value = res.data.data
  periode.value = res.data.periode
  currentPage.value = 1
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

<style scoped>
.card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #eef0f4;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}

.stat-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #eef0f4;
  padding: 16px 18px;
  display: flex;
  align-items: center;
  gap: 14px;
}

.stat-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.stat-label {
  font-size: 12px;
  font-weight: 600;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  margin-bottom: 2px;
}

.stat-value {
  font-size: 20px;
  font-weight: 700;
  color: #1e293b;
  line-height: 1.2;
}

.label {
  display: block;
  font-size: 0.8rem;
  font-weight: 600;
  margin-bottom: 6px;
  color: #64748b;
}

.th {
  padding: 14px 12px;
  font-weight: 600;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.02em;
  color: #94a3b8;
  background: #f8fafc;
  border-bottom: 1px solid #eef0f4;
  white-space: nowrap;
  text-align: left;
}

.td {
  padding: 14px 12px;
  white-space: normal;
}

.row {
  border-bottom: 1px solid #f1f4f8;
  transition: background-color 0.15s ease;
}

.row:hover {
  background-color: #f8fafc;
}

.row:last-child {
  border-bottom: none;
}

.code-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 8px;
  background: #eef0ff;
  color: #4f46e5;
  font-weight: 600;
  font-size: 12.5px;
}

.usage-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 38px;
  padding: 4px 10px;
  border-radius: 999px;
  background: #ecfdf5;
  color: #059669;
  font-size: 12.5px;
  font-weight: 700;
}

.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 10px 12px;
  font-size: 14px;
  color: #334155;
  background: #fff;
  transition: border-color 0.15s ease;
}

.input:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}

.btn-primary,
.btn-secondary,
.btn-pdf {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 10px 18px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 14px;
  border: none;
  cursor: pointer;
  transition: opacity 0.15s ease, transform 0.1s ease;
}

.btn-primary:active,
.btn-secondary:active,
.btn-pdf:active {
  transform: scale(0.98);
}

.btn-primary {
  background-color: #4f46e5;
  color: white;
}

.btn-primary:hover {
  background-color: #4338ca;
}

.btn-secondary {
  background-color: #f1f5f9;
  color: #475569;
}

.btn-secondary:hover {
  background-color: #e2e8f0;
}

.btn-pdf {
  background-color: #fff;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.btn-pdf:hover {
  background-color: #fef2f2;
}

.page-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 34px;
  height: 34px;
  padding: 0 8px;
  border-radius: 8px;
  background: #f8fafc;
  color: #475569;
  font-size: 13px;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.page-btn:hover:not(:disabled) {
  background: #eef0ff;
}

.page-btn.active {
  background: #4f46e5;
  color: white;
  font-weight: 600;
}

.page-btn:disabled {
  opacity: 0.4;
  cursor: default;
}
</style>