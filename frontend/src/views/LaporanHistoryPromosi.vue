<template>
  <div class="min-h-screen bg-slate-100 p-6 space-y-6">

    <!-- HEADER -->
    <div
      class="bg-white rounded-2xl shadow-md p-6
             flex flex-col md:flex-row md:items-center md:justify-between gap-4"
    >
      <div>
        <h1 class="text-2xl font-bold text-indigo-600">
          Laporan Histori Promosi
        </h1>
        <p class="text-sm text-slate-500">
          Periode: {{ periode }}
        </p>
      </div>
    </div>

    <!-- FILTER -->
    <div
      class="bg-white rounded-2xl shadow-md p-6
             grid grid-cols-1 md:grid-cols-4 gap-4 items-end"
    >
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
          Search
        </button>

        <button class="btn-secondary" @click="resetFilter">
          Reset
        </button>

        <button class="btn-pdf" @click="cetakPdf">
          🖨 Cetak PDF
        </button>
      </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gradient-to-r from-indigo-50 to-slate-100">
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
      <tr
        v-for="row in paginatedRows"
        :key="row.no"
        class="border-b transition even:bg-slate-50 hover:bg-indigo-50/70"
      >
        <td class="td text-center w-12">{{ row.no }}</td>
        <td class="td w-32 font-semibold text-indigo-600">{{ row.kode_promo }}</td>
        <td class="td w-48">{{ row.nama_promo }}</td>
        <td class="td text-right w-28 font-semibold">{{ row.nominal_promo }}</td>
        <td class="td w-72">{{ row.isi_promo }}</td>
        <td class="td w-44">{{ row.tanggal_promo }}</td>
        <td class="td text-center w-24">{{ row.waktu_promo }}</td>
        <td class="td text-center w-24">
          <span
            class="inline-flex items-center justify-center
                  min-w-[40px] px-2 py-1 rounded-full
                  text-xs font-bold bg-indigo-100 text-indigo-700"
          >
            {{ row.total_dipakai }} x
          </span>
        </td>
      </tr>
    </tbody>

    </table>

    <!-- PAGINATION -->
    <div
      v-if="totalPages > 1"
      class="flex justify-between items-center
            px-6 py-4 border-t bg-slate-50"
    >
      <div class="text-sm text-slate-500">
        Halaman {{ currentPage }} dari {{ totalPages }}
      </div>

      <div class="flex gap-2">
        <button
          class="page-btn"
          :disabled="currentPage === 1"
          @click="changePage(currentPage - 1)"
        >
          Prev
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

        <button
          class="page-btn"
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
const perPage = ref(10)

const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return rows.value.slice(start, start + perPage.value)
})

const totalPages = computed(() =>
  Math.ceil(rows.value.length / perPage.value)
)

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
.label {
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 6px;
  color: #475569;
}

.th {
  padding: 12px;
  font-weight: 600;
  border-bottom: 1px solid #e5e7eb;
  white-space: normal;
}

.td {
  padding: 12px;
  white-space: normal;
}

.input {
  width: 100%;
  border: 1px solid #c7d2fe;
  border-radius: 12px;
  padding: 10px 12px;
}

.btn-primary {
  background-color: #6366f1;
  color: white;
  padding: 12px 18px;
  border-radius: 14px;
  font-weight: 600;
}

.btn-secondary {
  background-color: #e5e7eb;
  color: #374151;
  padding: 12px 18px;
  border-radius: 14px;
  font-weight: 600;
}

.btn-pdf {
  background-color: #dc2626;
  color: white;
  padding: 12px 18px;
  border-radius: 14px;
  font-weight: 600;
}

.page-btn {
  padding: 6px 12px;
  border-radius: 8px;
  background: #e5e7eb;
  font-size: 14px;
}

.page-btn.active {
  background: #6366f1;
  color: white;
  font-weight: 600;
}

.page-btn:disabled {
  opacity: 0.4;
}
</style>
