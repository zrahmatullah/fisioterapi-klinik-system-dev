<template>
  <div class="min-h-screen bg-slate-100 p-6 space-y-6">

    <!-- HEADER -->
    <div
      class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 p-8 shadow-2xl"
    >
      <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

        <div>
          <h1 class="text-3xl font-bold text-white">
            Laporan Transaksi Pembayaran
          </h1>

          <p class="text-indigo-100 mt-2">
            Monitoring seluruh transaksi pembayaran pasien
          </p>

          <div class="mt-4 inline-flex items-center gap-2 bg-white/15 backdrop-blur-md border border-white/20 px-4 py-2 rounded-2xl text-white text-sm">
            📅 {{ periode }}
          </div>
        </div>

        <!-- GRAND TOTAL -->
        <div
          class="bg-white/15 backdrop-blur-xl border border-white/20 rounded-3xl p-6 min-w-[280px]"
        >
          <div class="text-indigo-100 text-sm mb-2">
            Grand Total Pembayaran
          </div>

          <div class="text-4xl font-black text-white tracking-tight">
            Rp {{ format(grandTotal) }}
          </div>

          <div class="mt-3 text-xs text-indigo-100">
            Total seluruh transaksi pembayaran
          </div>
        </div>
      </div>

      <!-- ORNAMENT -->
      <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 left-0 w-56 h-56 bg-pink-500/20 rounded-full blur-3xl"></div>
    </div>

    <!-- FILTER -->
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-6">

      <div class="flex items-center justify-between mb-5">
        <div>
          <h2 class="text-lg font-bold text-slate-800">
            Filter Laporan
          </h2>

          <p class="text-sm text-slate-500">
            Gunakan filter untuk mencari data transaksi
          </p>
        </div>

        <div class="hidden md:flex items-center gap-2 bg-indigo-50 text-indigo-700 px-4 py-2 rounded-xl text-sm font-semibold">
          📊 {{ filteredRows.length }} Data
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">

        <!-- START DATE -->
        <div>
          <label class="label">Tanggal Mulai</label>

          <div class="relative">
            <input
              type="date"
              v-model="startDate"
              class="input"
            />
          </div>
        </div>

        <!-- END DATE -->
        <div>
          <label class="label">Tanggal Akhir</label>

          <input
            type="date"
            v-model="endDate"
            class="input"
          />
        </div>

        <!-- SEARCH -->
        <div class="xl:col-span-2">
          <label class="label">Cari Data</label>

          <div class="relative">
            <svg
              class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-4.35-4.35M16 10.5a5.5 5.5 0 11-11 0 5.5 5.5 0 0111 0z"
              />
            </svg>

            <input
              v-model="search"
              type="text"
              placeholder="Nama pasien / No Invoice"
              class="input-search"
            />
          </div>
        </div>

        <!-- KATEGORI -->
        <div>
          <label class="label">Kategori</label>

          <select v-model="filterKategori" class="input">
            <option value="">Semua</option>

            <option
              v-for="k in kategoriList"
              :key="k"
              :value="k"
            >
              {{ k }}
            </option>
          </select>
        </div>

        <!-- JENIS -->
        <div>
          <label class="label">Jenis Layanan</label>

          <select v-model="filterJenis" class="input">
            <option value="">Semua</option>

            <option
              v-for="j in jenisList"
              :key="j"
              :value="j"
            >
              {{ j }}
            </option>
          </select>
        </div>
      </div>

      <!-- ACTION BUTTON -->
      <div class="flex flex-wrap gap-3 mt-6">

        <button
          @click="loadData"
          class="btn-primary"
        >
          🔍 Filter
        </button>

        <button
          @click="resetFilter"
          class="btn-secondary"
        >
          🔄 Reset
        </button>

        <button
          @click="exportExcel"
          class="btn-excel"
        >
          📗 Export Excel
        </button>

        <button
          @click="cetakPdf"
          class="btn-pdf"
        >
          🖨 Cetak PDF
        </button>
      </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-200">

      <!-- TABLE HEADER -->
      <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
        <div>
          <h3 class="font-bold text-slate-800">
            Data Transaksi
          </h3>

          <p class="text-sm text-slate-500 mt-1">
            Menampilkan seluruh transaksi pembayaran
          </p>
        </div>

        <div class="hidden md:flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></div>
          <span class="text-sm text-slate-500">
            Data realtime
          </span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">

          <thead class="bg-slate-100 text-slate-700">
            <tr>
              <th class="th text-center">No</th>
              <th class="th">No Invoice</th>
              <th class="th">Nama Pasien</th>
              <th class="th">Tanggal Bayar</th>
              <th class="th">Kategori</th>
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
              class="border-b border-slate-100 hover:bg-indigo-50/40 transition duration-200"
            >
              <td class="td text-center font-semibold text-slate-600">
                {{ row.no }}
              </td>

              <td class="td">
                <div class="font-bold text-indigo-600">
                  {{ row.no_invoice }}
                </div>
              </td>

              <td class="td">
                <div class="flex items-center gap-3">

                  <div
                    class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 text-white flex items-center justify-center font-bold"
                  >
                    {{ row.nama_pasien.charAt(0) }}
                  </div>

                  <div>
                    <div class="font-semibold text-slate-800">
                      {{ row.nama_pasien }}
                    </div>

                    <div class="text-xs text-slate-400">
                      Pasien
                    </div>
                  </div>
                </div>
              </td>

              <td class="td text-slate-600">
                {{ row.tanggal_bayar }}
              </td>

              <td class="td">
                <span class="badge-indigo">
                  {{ row.kategori_layanan }}
                </span>
              </td>

              <td class="td">
                <span class="badge-slate">
                  {{ row.jenis_layanan }}
                </span>
              </td>

              <td class="td text-center">
                <span class="badge-session">
                  {{ row.jumlah_sesi }}
                </span>
              </td>

              <td class="td text-right">
                <div class="font-bold text-slate-800">
                  Rp {{ format(row.total) }}
                </div>
              </td>

              <td class="td text-right">
                <div class="font-bold text-emerald-600">
                  Rp {{ format(row.subtotal) }}
                </div>
              </td>
            </tr>

            <!-- EMPTY -->
            <tr v-if="filteredRows.length === 0">
              <td colspan="9" class="py-20 text-center">

                <div class="flex flex-col items-center">
                  <div
                    class="w-24 h-24 rounded-full bg-slate-100 flex items-center justify-center text-4xl mb-4"
                  >
                    📄
                  </div>

                  <div class="text-lg font-bold text-slate-500">
                    Tidak ada data transaksi
                  </div>

                  <div class="text-sm text-slate-400 mt-1">
                    Silakan ubah filter pencarian
                  </div>
                </div>

              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div
        v-if="totalPages > 1"
        class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 px-6 py-5 border-t border-slate-200 bg-slate-50"
      >
        <div class="text-sm text-slate-500">
          Menampilkan halaman
          <span class="font-bold text-slate-700">
            {{ currentPage }}
          </span>
          dari
          <span class="font-bold text-slate-700">
            {{ totalPages }}
          </span>
        </div>

        <div class="flex items-center gap-2 flex-wrap">

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

/* FILTER */
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

/* OPTION */
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

  link.download =
    `laporan_pembayaran_${startDate.value}_${endDate.value}.xlsx`

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
  @apply block text-sm font-semibold text-slate-700 mb-2;
}

.input {
  @apply w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500;
}

.input-search {
  @apply w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-4 py-3 outline-none transition focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500;
}

/* TABLE */
.th {
  @apply px-5 py-4 font-bold text-left whitespace-nowrap;
}

.td {
  @apply px-5 py-4 whitespace-nowrap;
}

/* BUTTON */
.btn-primary {
  @apply px-5 py-3 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-lg hover:scale-105 transition;
}

.btn-secondary {
  @apply px-5 py-3 rounded-2xl bg-slate-200 text-slate-700 font-semibold hover:bg-slate-300 transition;
}

.btn-excel {
  @apply px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-green-600 text-white font-semibold shadow-lg hover:scale-105 transition;
}

.btn-pdf {
  @apply px-5 py-3 rounded-2xl bg-gradient-to-r from-rose-600 to-red-600 text-white font-semibold shadow-lg hover:scale-105 transition;
}

/* BADGE */
.badge-indigo {
  @apply inline-flex px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold;
}

.badge-slate {
  @apply inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold;
}

.badge-session {
  @apply inline-flex items-center justify-center min-w-[32px] h-8 px-3 rounded-full bg-emerald-100 text-emerald-700 text-sm font-bold;
}

/* PAGINATION */
.page-btn {
  @apply px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 font-medium hover:bg-indigo-50 hover:border-indigo-300 transition disabled:opacity-40 disabled:cursor-not-allowed;
}

.page-btn.active {
  @apply bg-gradient-to-r from-indigo-600 to-violet-600 text-white border-transparent;
}
</style>