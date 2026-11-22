<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-comments"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Laporan Rekapitulasi Keluhan</h2>
          <p class="text-xs text-[#64748B] mt-0.5">
            Periode: <span class="font-medium text-slate-700">{{ periode }}</span>
          </p>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <div class="bg-[#F0F6F8] border border-[#DCE8EC] px-3 py-1.5 rounded-lg flex items-center gap-2">
          <span class="text-[11px] text-slate-500 font-medium">Total Keluhan:</span>
          <span class="text-xs font-bold text-slate-900 tabular-nums">{{ filteredRows.length }}</span>
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
    </div>

    <!-- FILTER TOOLBAR -->
    <div class="bg-white rounded-xl shadow-2xs border border-slate-200 p-3 shrink-0">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-2.5 items-end text-xs">
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
          <label class="block text-[11px] font-medium text-slate-600 mb-1">Nama Pasien</label>
          <input
            type="text"
            v-model="filterNama"
            placeholder="Cari nama pasien..."
            class="w-full h-[34px] px-2.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E]"
          />
        </div>

        <div>
          <label class="block text-[11px] font-medium text-slate-600 mb-1">Kategori Keluhan</label>
          <select
            v-model="filterKategori"
            class="w-full h-[34px] px-2.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-700 focus:outline-none focus:border-[#0F766E]"
          >
            <option value="">Semua Kategori</option>
            <option value="Pelayanan Administrasi">Pelayanan Administrasi</option>
            <option value="Sarana & Prasarana">Sarana & Prasarana</option>
            <option value="Kualitas Terapi">Kualitas Terapi</option>
            <option value="Jadwal / Waktu Pelayanan">Jadwal / Waktu Pelayanan</option>
          </select>
        </div>

        <div class="flex gap-2">
          <button
            type="button"
            class="h-[34px] flex-1 bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-medium text-xs rounded-lg inline-flex items-center justify-center gap-1.5 transition shadow-2xs cursor-pointer"
            @click="loadData"
          >
            <i class="pi pi-search text-xs"></i>
            <span>Cari</span>
          </button>

          <button
            type="button"
            class="h-[34px] px-3 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium text-xs rounded-lg inline-flex items-center justify-center gap-1 transition cursor-pointer"
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
              <th class="py-2.5 px-3">No Keluhan</th>
              <th class="py-2.5 px-3">Nama Anak</th>
              <th class="py-2.5 px-3">Kategori</th>
              <th class="py-2.5 px-3">Tanggal</th>
              <th class="py-2.5 px-3">Isi Keluhan</th>
              <th class="py-2.5 px-3 text-center">Status</th>
              <th class="py-2.5 px-3">Tanggapan Admin</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="row in filteredRows"
              :key="row.no"
              class="hover:bg-[#F0F6F8]/60 transition"
            >
              <td class="py-2 px-3 text-center text-slate-400 tabular-nums">{{ row.no }}</td>
              <td class="py-2 px-3 font-mono font-semibold text-[#0F766E] text-xs">{{ row.no_keluhan }}</td>
              <td class="py-2 px-3">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9] flex items-center justify-center font-bold text-xs shrink-0">
                    {{ (row.nama_pasien || 'A').charAt(0) }}
                  </div>
                  <span class="font-semibold text-slate-800">{{ row.nama_pasien }}</span>
                </div>
              </td>
              <td class="py-2 px-3">
                <span class="px-2 py-0.5 rounded-full bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9] text-[10.5px] font-semibold">
                  {{ row.kategori_keluhan }}
                </span>
              </td>
              <td class="py-2 px-3 text-slate-600 tabular-nums whitespace-nowrap">{{ row.tanggal_keluhan }}</td>
              <td class="py-2 px-3 text-slate-600 max-w-xs truncate" :title="row.isi_keluhan">{{ row.isi_keluhan }}</td>
              <td class="py-2 px-3 text-center">
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                  :class="row.status_tanggapan === 'sudah_ditanggapi' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="row.status_tanggapan === 'sudah_ditanggapi' ? 'bg-emerald-600' : 'bg-amber-600'"></span>
                  {{ row.status_tanggapan === 'sudah_ditanggapi' ? 'Sudah Ditanggapi' : 'Belum Ditanggapi' }}
                </span>
              </td>
              <td class="py-2 px-3 text-slate-600 max-w-xs truncate" :title="row.tanggapan || 'Belum ada tanggapan'">
                <span v-if="row.tanggapan" class="text-slate-700">{{ row.tanggapan }}</span>
                <span v-else class="italic text-slate-400">Belum ada tanggapan</span>
              </td>
            </tr>

            <tr v-if="filteredRows.length === 0">
              <td colspan="8" class="py-12 text-center text-slate-400">
                <i class="pi pi-folder-open text-2xl text-slate-300 block mb-1"></i>
                <span class="text-xs">Tidak ada data keluhan pada filter ini</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- FOOTER -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] text-slate-500 flex items-center justify-between">
        <span>Total: {{ filteredRows.length }} Data Keluhan</span>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/api'

const today = new Date().toISOString().slice(0, 10)

const startDate = ref(today)
const endDate = ref(today)

const rows = ref([])
const periode = ref('-')

/* FILTER */
const filterNoKeluhan = ref('')
const filterNama = ref('')
const filterKategori = ref('')

/* LOAD DATA */
const loadData = async () => {
  try {
    const res = await api.get('/laporan/keluhan', {
      params: {
        start_date: startDate.value,
        end_date: endDate.value
      }
    })

    rows.value = res.data?.data || []
    periode.value = `${startDate.value} s/d ${endDate.value}`
  } catch (err) {
    console.error('Gagal mengambil laporan keluhan:', err)
  }
}

/* RESET */
const resetFilter = () => {
  startDate.value = today
  endDate.value = today

  filterNoKeluhan.value = ''
  filterNama.value = ''
  filterKategori.value = ''

  loadData()
}

/* PDF */
const cetakPdf = () => {
  window.open(
    `/api/laporan/keluhan/cetak-pdf?start_date=${startDate.value}&end_date=${endDate.value}`,
    '_blank'
  )
}

/* FILTERED */
const filteredRows = computed(() => {
  return rows.value.filter(r => {
    const matchNo = filterNoKeluhan.value
      ? r.no_keluhan?.toLowerCase().includes(filterNoKeluhan.value.toLowerCase())
      : true

    const matchNama = filterNama.value
      ? r.nama_pasien?.toLowerCase().includes(filterNama.value.toLowerCase())
      : true

    const matchKategori = filterKategori.value
      ? r.kategori_keluhan?.toLowerCase() === filterKategori.value.toLowerCase()
      : true

    return matchNo && matchNama && matchKategori
  })
})

onMounted(loadData)
</script>
