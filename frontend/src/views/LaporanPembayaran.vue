<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-receipt"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Laporan Transaksi Pembayaran</h2>
          <p class="text-xs text-[#64748B] mt-0.5">
            Periode: <span class="font-medium text-slate-700">{{ periode }}</span>
          </p>
        </div>
      </div>

      <!-- GRAND TOTAL PILL -->
      <div class="flex items-center gap-2">
        <div class="bg-teal-50 border border-teal-200 px-3 py-1.5 rounded-lg flex items-center gap-2">
          <span class="text-[11px] text-[#0F766E] font-medium">Grand Total:</span>
          <span class="text-sm font-bold text-teal-800 tabular-nums">Rp {{ format(grandTotal) }}</span>
        </div>
      </div>
    </div>

    <!-- FILTER TOOLBAR -->
    <div class="bg-white rounded-xl shadow-2xs border border-slate-200 p-3 shrink-0">
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-2.5 items-end text-xs">
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

        <div>
          <label class="block text-[11px] font-medium text-slate-600 mb-1">Cari Data</label>
          <input
            v-model="search"
            type="text"
            placeholder="Nama pasien / No Invoice"
            class="w-full h-[34px] px-2.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E]"
          />
        </div>

        <div>
          <label class="block text-[11px] font-medium text-slate-600 mb-1">Kategori</label>
          <select v-model="filterKategori" class="w-full h-[34px] px-2.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-700 focus:outline-none focus:border-[#0F766E]">
            <option value="">Semua Kategori</option>
            <option v-for="k in kategoriList" :key="k" :value="k">{{ k }}</option>
          </select>
        </div>

        <div>
          <label class="block text-[11px] font-medium text-slate-600 mb-1">Jenis Layanan</label>
          <select v-model="filterJenis" class="w-full h-[34px] px-2.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-700 focus:outline-none focus:border-[#0F766E]">
            <option value="">Semua Layanan</option>
            <option v-for="j in jenisList" :key="j" :value="j">{{ j }}</option>
          </select>
        </div>

        <div class="flex gap-1.5">
          <button
            type="button"
            class="h-[34px] flex-1 bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-medium text-xs rounded-lg inline-flex items-center justify-center gap-1 transition shadow-2xs cursor-pointer"
            @click="loadData"
          >
            <i class="pi pi-search text-xs"></i>
            <span>Filter</span>
          </button>

          <button
            type="button"
            class="h-[34px] px-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium text-xs rounded-lg inline-flex items-center justify-center transition cursor-pointer"
            @click="resetFilter"
            title="Reset"
          >
            <i class="pi pi-refresh text-xs"></i>
          </button>

          <button
            type="button"
            class="h-[34px] px-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-medium text-xs rounded-lg inline-flex items-center justify-center transition cursor-pointer shadow-2xs"
            @click="exportExcel"
            title="Export Excel"
          >
            <i class="pi pi-file-excel text-xs"></i>
          </button>

          <button
            type="button"
            class="h-[34px] px-2.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-medium text-xs rounded-lg inline-flex items-center justify-center transition cursor-pointer shadow-2xs"
            @click="cetakPdf"
            title="Cetak PDF"
          >
            <i class="pi pi-print text-xs"></i>
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
              <th class="py-2.5 px-3">No Invoice</th>
              <th class="py-2.5 px-3">Nama Pasien</th>
              <th class="py-2.5 px-3">Tanggal Bayar</th>
              <th class="py-2.5 px-3">Kategori</th>
              <th class="py-2.5 px-3">Jenis Layanan</th>
              <th class="py-2.5 px-3 text-center">Sesi</th>
              <th class="py-2.5 px-3 text-right">Total</th>
              <th class="py-2.5 px-3 text-right">Sub Total</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="row in paginatedRows"
              :key="row.no"
              class="hover:bg-[#F0F6F8]/60 transition duration-150"
            >
              <td class="py-2 px-3 text-center font-medium text-slate-500 tabular-nums">
                {{ row.no }}
              </td>

              <td class="py-2 px-3">
                <span class="font-bold text-[#0F766E] font-mono text-xs">
                  {{ row.no_invoice }}
                </span>
              </td>

              <td class="py-2 px-3">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full bg-[#E6F2F5] border border-[#CBE3E9] text-[#0F766E] flex items-center justify-center font-bold text-xs shrink-0">
                    {{ (row.nama_pasien || 'P').charAt(0) }}
                  </div>
                  <span class="font-semibold text-slate-800">{{ row.nama_pasien }}</span>
                </div>
              </td>

              <td class="py-2 px-3 text-slate-600 tabular-nums whitespace-nowrap">{{ row.tanggal_bayar }}</td>

              <td class="py-2 px-3">
                <span class="px-2 py-0.5 rounded-full bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9] text-[10.5px] font-semibold">
                  {{ row.kategori_layanan }}
                </span>
              </td>

              <td class="py-2 px-3">
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-[10.5px] font-semibold">
                  {{ row.jenis_layanan }}
                </span>
              </td>

              <td class="py-2 px-3 text-center">
                <span class="inline-flex items-center justify-center min-w-[24px] h-5 px-1.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold tabular-nums">
                  {{ row.jumlah_sesi }}
                </span>
              </td>

              <td class="py-2 px-3 text-right font-medium text-slate-700 tabular-nums">
                Rp {{ format(row.total_transaksi) }}
              </td>

              <td class="py-2 px-3 text-right font-bold text-slate-900 tabular-nums">
                Rp {{ format(row.sub_total) }}
              </td>
            </tr>

            <tr v-if="filteredRows.length === 0">
              <td colspan="9" class="py-12 text-center text-slate-400">
                <i class="pi pi-folder-open text-2xl text-slate-300 block mb-1"></i>
                <span class="text-xs">Tidak ada data transaksi</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- FOOTER PAGINATION -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] text-slate-500 flex items-center justify-between">
        <span>Halaman {{ currentPage }} dari {{ totalPages }} (Total: {{ filteredRows.length }} Data)</span>

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
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

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
const perPage = ref(15)

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
    toast.error('Gagal mengambil data laporan transaksi')
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
  Math.ceil(filteredRows.value.length / perPage.value) || 1
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
