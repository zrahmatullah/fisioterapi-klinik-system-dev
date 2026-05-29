<template>
  <div class="min-h-screen bg-slate-100 p-6 space-y-6">

    <!-- HEADER -->
    <div
      class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 p-8 shadow-2xl"
    >
      <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

        <div>
          <h1 class="text-3xl font-bold text-white">
            Laporan Keluhan
          </h1>

          <p class="text-indigo-100 mt-2">
            Monitoring data keluhan pasien & tanggapan admin
          </p>

          <div
            class="mt-4 inline-flex items-center gap-2 bg-white/15 backdrop-blur-md border border-white/20 px-4 py-2 rounded-2xl text-white text-sm"
          >
            📅 {{ periode }}
          </div>
        </div>

        <!-- CARD -->
        <div
          class="bg-white/15 backdrop-blur-xl border border-white/20 rounded-3xl p-6 min-w-[260px]"
        >
          <div class="text-indigo-100 text-sm mb-2">
            Total Keluhan
          </div>

          <div class="text-5xl font-black text-white">
            {{ filteredRows.length }}
          </div>

          <div class="mt-2 text-xs text-indigo-100">
            Data sesuai filter
          </div>
        </div>
      </div>

      <!-- ORNAMENT -->
      <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 left-0 w-52 h-52 bg-pink-500/20 rounded-full blur-3xl"></div>
    </div>

    <!-- FILTER -->
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-6">

      <div class="flex items-center justify-between mb-5">
        <div>
          <h2 class="text-lg font-bold text-slate-800">
            Filter Laporan
          </h2>

          <p class="text-sm text-slate-500">
            Gunakan filter untuk mencari data keluhan
          </p>
        </div>

        <div
          class="hidden md:flex items-center gap-2 bg-indigo-50 text-indigo-700 px-4 py-2 rounded-xl text-sm font-semibold"
        >
          📄 {{ filteredRows.length }} Data
        </div>
      </div>

      <!-- FILTER GRID -->
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">

        <!-- START DATE -->
        <div>
          <label class="label">
            Tanggal Mulai
          </label>

          <input
            type="date"
            v-model="startDate"
            class="input"
          />
        </div>

        <!-- END DATE -->
        <div>
          <label class="label">
            Tanggal Akhir
          </label>

          <input
            type="date"
            v-model="endDate"
            class="input"
          />
        </div>

        <!-- NAMA -->
        <div>
          <label class="label">
            Nama Pasien
          </label>

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
              type="text"
              v-model="filterNama"
              placeholder="Cari nama pasien"
              class="input-search"
            />
          </div>
        </div>

        <!-- KATEGORI -->
        <div>
          <label class="label">
            Kategori Keluhan
          </label>

          <select
            v-model="filterKategori"
            class="input"
          >
            <option value="">Semua Kategori</option>

            <option value="Pelayanan Administrasi">
              Pelayanan Administrasi
            </option>

            <option value="Sarana & Prasarana">
              Sarana & Prasarana
            </option>

            <option value="Kualitas Terapi">
              Kualitas Terapi
            </option>

            <option value="Jadwal / Waktu Pelayanan">
              Jadwal / Waktu Pelayanan
            </option>
          </select>
        </div>

        <!-- ACTION -->
        <div class="flex flex-wrap gap-3 items-end">

          <button
            @click="loadData"
            class="btn-primary"
          >
            🔍 Search
          </button>

          <button
            @click="resetFilter"
            class="btn-secondary"
          >
            🔄 Reset
          </button>

          <button
            @click="cetakPdf"
            class="btn-pdf"
          >
            🖨 PDF
          </button>

        </div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-200">

      <!-- HEADER TABLE -->
      <div
        class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between"
      >
        <div>
          <h3 class="font-bold text-slate-800">
            Data Keluhan Pasien
          </h3>

          <p class="text-sm text-slate-500 mt-1">
            Menampilkan seluruh data keluhan pasien
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

          <!-- TABLE HEAD -->
          <thead class="bg-slate-100 text-slate-700">
            <tr>
              <th class="th text-center">No</th>
              <th class="th">No Keluhan</th>
              <th class="th">Nama Anak</th>
              <th class="th">Kategori</th>
              <th class="th">Tanggal</th>
              <th class="th">Isi Keluhan</th>
              <th class="th text-center">Status</th>
              <th class="th">Tanggapan</th>
            </tr>
          </thead>

          <!-- BODY -->
          <tbody>

            <tr
              v-for="row in filteredRows"
              :key="row.no"
              class="border-b border-slate-100 hover:bg-indigo-50/40 transition duration-200"
            >
              <td class="td text-center font-semibold text-slate-600">
                {{ row.no }}
              </td>

              <td class="td">
                <div class="font-bold text-indigo-600">
                  {{ row.no_keluhan }}
                </div>
              </td>

              <!-- NAMA -->
              <td class="td">
                <div class="flex items-center gap-3">

                  <div
                    class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 text-white flex items-center justify-center font-bold"
                  >
                    {{ row.nama_pasien?.charAt(0) }}
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

              <!-- KATEGORI -->
              <td class="td">
                <span class="badge-indigo">
                  {{ row.kategori_keluhan }}
                </span>
              </td>

              <!-- TANGGAL -->
              <td class="td text-slate-600 whitespace-nowrap">
                {{ row.tanggal_keluhan }}
              </td>

              <!-- ISI -->
              <td class="td">
                <div class="max-w-xs whitespace-normal text-slate-600 leading-relaxed">
                  {{ row.isi_keluhan }}
                </div>
              </td>

              <!-- STATUS -->
              <td class="td text-center">
                <span
                  class="badge-status"
                  :class="
                    row.status_tanggapan === 'sudah_ditanggapi'
                      ? 'badge-success'
                      : 'badge-warning'
                  "
                >
                  <span
                    class="w-2 h-2 rounded-full"
                    :class="
                      row.status_tanggapan === 'sudah_ditanggapi'
                        ? 'bg-emerald-500'
                        : 'bg-orange-500'
                    "
                  ></span>

                  {{
                    row.status_tanggapan === 'sudah_ditanggapi'
                      ? 'Sudah Ditanggapi'
                      : 'Belum Ditanggapi'
                  }}
                </span>
              </td>

              <!-- TANGGAPAN -->
              <td class="td">
                <div
                  class="max-w-xs whitespace-normal leading-relaxed"
                >
                  <span
                    v-if="row.tanggapan"
                    class="text-slate-700"
                  >
                    {{ row.tanggapan }}
                  </span>

                  <span
                    v-else
                    class="italic text-slate-400"
                  >
                    Belum ada tanggapan
                  </span>
                </div>
              </td>
            </tr>

            <!-- EMPTY -->
            <tr v-if="filteredRows.length === 0">
              <td colspan="8" class="py-20 text-center">

                <div class="flex flex-col items-center">

                  <div
                    class="w-24 h-24 rounded-full bg-slate-100 flex items-center justify-center text-4xl mb-4"
                  >
                    📭
                  </div>

                  <div class="text-lg font-bold text-slate-500">
                    Tidak ada data keluhan
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
  const res = await api.get('/laporan/keluhan', {
    params: {
      start_date: startDate.value,
      end_date: endDate.value
    }
  })

  rows.value = res.data.data

  periode.value =
    `${startDate.value} s/d ${endDate.value}`
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
      ? r.no_keluhan?.toLowerCase()
          .includes(filterNoKeluhan.value.toLowerCase())
      : true

    const matchNama = filterNama.value
      ? r.nama_pasien?.toLowerCase()
          .includes(filterNama.value.toLowerCase())
      : true

    const matchKategori = filterKategori.value
      ? r.kategori_keluhan?.toLowerCase() ===
        filterKategori.value.toLowerCase()
      : true

    return matchNo && matchNama && matchKategori
  })
})

onMounted(loadData)
</script>

<style scoped>
/* LABEL */
.label {
  @apply block text-sm font-semibold text-slate-700 mb-2;
}

/* INPUT */
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
  @apply px-5 py-4 align-top;
}

/* BUTTON */
.btn-primary {
  @apply px-5 py-3 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-lg hover:scale-105 transition;
}

.btn-secondary {
  @apply px-5 py-3 rounded-2xl bg-slate-200 text-slate-700 font-semibold hover:bg-slate-300 transition;
}

.btn-pdf {
  @apply px-5 py-3 rounded-2xl bg-gradient-to-r from-rose-600 to-red-600 text-white font-semibold shadow-lg hover:scale-105 transition;
}

/* BADGE */
.badge-indigo {
  @apply inline-flex px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold;
}

.badge-status {
  @apply inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold;
}

.badge-success {
  @apply bg-emerald-100 text-emerald-700;
}

.badge-warning {
  @apply bg-orange-100 text-orange-700;
}
</style>