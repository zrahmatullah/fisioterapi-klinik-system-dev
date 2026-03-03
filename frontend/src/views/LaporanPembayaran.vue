<template>
  <div class="min-h-screen bg-slate-100 p-6 space-y-6">

    <!-- HEADER -->
    <div class="bg-white rounded-2xl shadow-md p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-indigo-600">
          Laporan Transaksi Pembayaran
        </h1>
        <p class="text-sm text-slate-500">
          Periode: {{ periode }}
        </p>
      </div>

      <div class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl text-sm font-semibold">
        Grand Total:
        <span class="ml-2 text-lg font-bold">
          Rp. {{ format(grandTotal) }}
        </span>
      </div>
    </div>

    <!-- FILTER RANGE -->
    <div class="bg-white rounded-2xl shadow-md p-6 grid grid-cols-1 md:grid-cols-6 gap-4 items-end">

      <div>
        <label class="label">Tanggal Mulai</label>
        <input type="date" v-model="startDate" class="input" />
      </div>

      <div>
        <label class="label">Tanggal Akhir</label>
        <input type="date" v-model="endDate" class="input" />
      </div>

      <div>
        <label class="label">Cari Nama / Invoice</label>
        <input
          v-model="search"
          type="text"
          placeholder="Nama pasien / No Invoice"
          class="input"
        />
      </div>

      <div>
        <label class="label">Kategori Layanan</label>
        <select v-model="filterKategori" class="input">
          <option value="">Semua</option>
          <option v-for="k in kategoriList" :key="k" :value="k">
            {{ k }}
          </option>
        </select>
      </div>

      <div>
        <label class="label">Jenis Layanan</label>
        <select v-model="filterJenis" class="input">
          <option value="">Semua</option>
          <option v-for="j in jenisList" :key="j" :value="j">
            {{ j }}
          </option>
        </select>
      </div>

      <!-- TOMBOL -->
      <div class="flex items-end gap-2 h-full">
        <button @click="loadData" class="btn-primary flex items-center gap-2">
          🔍
        </button>

        <button @click="resetFilter" class="btn-secondary flex items-center gap-2">
          🔄
        </button>

        <button @click="exportExcel" class="btn-excel flex items-center gap-2">
          ⬇
        </button>

        <button @click="cetakPdf" class="btn-pdf flex items-center gap-2">
          🖨
        </button>
      </div>

    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow-md overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-100 sticky top-0">
          <tr>
            <th class="th">No</th>
            <th class="th">No Invoice</th>
            <th class="th">Nama Pasien</th>
            <th class="th">Tanggal Bayar</th>
            <th class="th">Kategori Layanan</th>
            <th class="th">Jenis Layanan</th>
            <th class="th text-center">Sesi</th>
            <th class="th text-right">Total</th>
            <th class="th text-right">Sub Total</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="row in paginatedRows"
            :key="row.no"
            class="border-b even:bg-slate-50 hover:bg-indigo-50"
          >
            <td class="td">{{ row.no }}</td>
            <td class="td font-medium text-indigo-600">
              {{ row.no_invoice }}
            </td>
            <td class="td">{{ row.nama_pasien }}</td>
            <td class="td">{{ row.tanggal_bayar }}</td>
            <td class="td">{{ row.kategori_layanan }}</td>
            <td class="td">{{ row.jenis_layanan }}</td>
            <td class="td text-center">
              <span class="badge">{{ row.jumlah_sesi }}</span>
            </td>
            <td class="td text-right font-semibold">
              Rp. {{ format(row.total) }}
            </td>
            <td class="td text-right">
              Rp. {{ format(row.subtotal) }}
            </td>
          </tr>

          <tr v-if="filteredRows.length === 0">
            <td colspan="9" class="py-12 text-center text-slate-400">
              Tidak ada data
            </td>
          </tr>
        </tbody>
      </table>

      <!-- PAGINATION -->
      <div
        v-if="totalPages > 1"
        class="flex justify-between items-center px-6 py-4 border-t bg-slate-50"
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
import axios from 'axios'

const today = new Date().toISOString().slice(0, 10)

const startDate = ref(today)
const endDate = ref(today)
const rows = ref([])
const periode = ref('-')
const grandTotal = ref(0)

const search = ref('')
const filterKategori = ref('')
const filterJenis = ref('')

/* PAGINATION */
const currentPage = ref(1)
const perPage = ref(10)

/* LOAD DATA */
const loadData = async () => {
  try {
    const res = await axios.get(
      'http://localhost:8000/api/laporan/pembayaran',
      {
        params: {
          start_date: startDate.value,
          end_date: endDate.value
        }
      }
    )

    rows.value = res.data.data
    grandTotal.value = res.data.grand_total
    periode.value = `${startDate.value} s/d ${endDate.value}`
    currentPage.value = 1
  } catch (err) {
    console.error(err)
    alert('Gagal mengambil data laporan')
  }
}

/* RESET */
const resetFilter = () => {
  startDate.value = today
  endDate.value = today
  search.value = ''
  filterKategori.value = ''
  filterJenis.value = ''
  loadData()
}

/* FILTERED DATA */
const filteredRows = computed(() => {
  return rows.value.filter(r => {
    const q = search.value.toLowerCase()
    const matchSearch =
      r.nama_pasien.toLowerCase().includes(q) ||
      r.no_invoice.toLowerCase().includes(q)

    const matchKategori = filterKategori.value
      ? r.kategori_layanan === filterKategori.value
      : true

    const matchJenis = filterJenis.value
      ? r.jenis_layanan === filterJenis.value
      : true

    return matchSearch && matchKategori && matchJenis
  })
})

/* UNIQUE OPTION LIST */
const kategoriList = computed(() => {
  return [...new Set(rows.value.map(r => r.kategori_layanan))]
})

const jenisList = computed(() => {
  return [...new Set(rows.value.map(r => r.jenis_layanan))]
})

/* PAGINATION */
const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredRows.value.slice(start, start + perPage.value)
})

const totalPages = computed(() =>
  Math.ceil(filteredRows.value.length / perPage.value)
)

const changePage = (page) => {
  if (page < 1 || page > totalPages.value) return
  currentPage.value = page
}

/* EXPORT */
const exportExcel = async () => {
  const response = await axios.get(
    'http://localhost:8000/api/laporan/pembayaran/export-excel',
    {
      params: {
        start_date: startDate.value,
        end_date: endDate.value
      },
      responseType: 'blob'
    }
  )

  const blob = new Blob([response.data])
  const url = window.URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `laporan_pembayaran_${startDate.value}_${endDate.value}.xlsx`
  link.click()
}

const cetakPdf = () => {
  window.open(
    `http://localhost:8000/api/laporan/pembayaran/cetak-pdf?start_date=${startDate.value}&end_date=${endDate.value}`,
    '_blank'
  )
}

const format = (val) => {
  if (!val) return '0'
  return new Intl.NumberFormat('id-ID').format(val)
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
}

.td {
  padding: 12px;
}

.input {
  width: 100%;
  border: 1px solid #c7d2fe;
  border-radius: 12px;
  padding: 10px 12px;
}

/* === BUTTON STYLE DIRAPIHKAN === */
.btn-primary,
.btn-secondary,
.btn-excel,
.btn-pdf {
  height: 34px;              /* lebih pendek */
  min-width: 38px;           /* lebih kecil */
  padding: 0 10px;           /* rapih */
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;        /* lebih kecil */
  font-weight: 500;
  font-size: 13px;
  transition: all 0.2s ease;
}


.btn-primary {
  background-color: #6366f1;
  color: white;
}

.btn-primary:hover {
  background-color: #4f46e5;
}

.btn-secondary {
  background-color: #e5e7eb;
  color: #374151;
}

.btn-secondary:hover {
  background-color: #d1d5db;
}

.btn-excel {
  background-color: #16a34a;
  color: white;
}

.btn-excel:hover {
  background-color: #15803d;
}

.btn-pdf {
  background-color: #dc2626;
  color: white;
}

.btn-pdf:hover {
  background-color: #b91c1c;
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
  cursor: not-allowed;
}

.badge {
  background: #e0e7ff;
  color: #4338ca;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
}
</style>
