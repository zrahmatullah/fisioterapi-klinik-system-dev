<template>
  <div class="min-h-screen bg-slate-100 p-6 space-y-6">

    <!-- HEADER -->
    <div class="bg-white rounded-2xl shadow-md p-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-indigo-600">
          Laporan Keluhan
        </h1>
        <p class="text-sm text-slate-500">
          Periode: {{ periode }}
        </p>
      </div>
    </div>

    <!-- FILTER -->
    <div class="bg-white rounded-2xl shadow-md p-6 flex flex-wrap gap-4 items-end">

      <!-- Tanggal -->
      <div>
        <input type="date" v-model="startDate" class="input" />
      </div>

      <div>
        <input type="date" v-model="endDate" class="input" />
      </div>

      <!-- Nama Pasien -->
      <div>
        <input
          type="text"
          v-model="filterNama"
          placeholder="Nama Pasien"
          class="input"
        />
      </div>
      
      <!-- Kategori Keluhan -->
      <div>
        <select v-model="filterKategori" class="input">
          <option value="">Semua Kategori</option>
          <option value="Pelayanan Administrasi">Pelayanan Administrasi</option>
          <option value="Sarana & Prasarana">Sarana & Prasarana</option>
          <option value="Kualitas Terapi">Kualitas Terapi</option>
          <option value="Jadwal / Waktu Pelayanan">Jadwal / Waktu Pelayanan</option>
        </select>
      </div>


      <!-- BUTTON -->
      <div class="flex gap-3">
        <button @click="loadData" class="btn-primary">
          Search
        </button>

        <button @click="resetFilter" class="btn-secondary">
          Reset
        </button>

        <button @click="cetakPdf" class="btn-primary">
          Cetak PDF
        </button>
      </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow-md overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-100">
          <tr>
            <th class="th">No</th>
            <th class="th">No Keluhan</th>
            <th class="th">Nama Anak</th>
            <th class="th">Kategori Keluhan</th>
            <th class="th">Tanggal Keluhan</th>
            <th class="th">Isi Keluhan</th>
            <th class="th">Status</th>
            <th class="th">Tanggapan</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="row in filteredRows"
            :key="row.no"
            class="border-b hover:bg-slate-50"
          >
            <td class="td text-center">{{ row.no }}</td>
            <td class="td font-medium text-indigo-600">
              {{ row.no_keluhan }}
            </td>
            <td class="td">{{ row.nama_pasien }}</td>
            <td class="td">{{ row.kategori_keluhan }}</td>
            <td class="td">{{ row.tanggal_keluhan }}</td>
            <td class="td">{{ row.isi_keluhan }}</td>
            <td class="td">
              <span
                class="badge"
                :class="row.status_tanggapan === 'sudah_ditanggapi'
                  ? 'badge-success'
                  : 'badge-warning'"
              >
                {{ row.status_tanggapan }}
              </span>
            </td>
            <td class="td">{{ row.tanggapan }}</td>
          </tr>

          <!-- EMPTY STATE -->
          <tr v-if="filteredRows.length === 0">
            <td colspan="8" class="py-12 text-center text-slate-400">
              <div class="text-4xl mb-2">📄</div>
              Tidak ada data sesuai filter
            </td>
          </tr>
        </tbody>
      </table>
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

/* FILTER STATE */
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
  periode.value = `${startDate.value} s/d ${endDate.value}`
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

/* CETAK PDF */
const cetakPdf = () => {
  window.open(
    `/api/laporan/keluhan/cetak-pdf?start_date=${startDate.value}&end_date=${endDate.value}`,
    '_blank'
  )
}

/* FILTERED DATA */
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

<style scoped>
.th {
  padding: 12px;
  font-weight: 600;
  border-bottom: 1px solid #e5e7eb;
  white-space: nowrap;
}

.td {
  padding: 12px;
  vertical-align: top;
}

.input {
  border: 1px solid #c7d2fe;
  border-radius: 10px;
  padding: 8px 12px;
  min-width: 160px;
}

.btn-primary {
  background-color: #6366f1;
  color: white;
  padding: 8px 16px;
  border-radius: 10px;
  font-weight: 600;
}

.btn-secondary {
  background-color: #e5e7eb;
  color: #374151;
  padding: 8px 16px;
  border-radius: 10px;
  font-weight: 600;
}

.badge {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
}

.badge-success {
  background: #dcfce7;
  color: #166534;
}

.badge-warning {
  background: #ffedd5;
  color: #9a3412;
}
</style>
