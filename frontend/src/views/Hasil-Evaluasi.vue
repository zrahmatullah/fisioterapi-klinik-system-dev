<template>
  <div class="min-h-screen bg-gray-100 p-6">

    <!-- HEADER + CETAK ALL -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-xl font-bold">Evaluasi Terapi Anak</h1>

      <!-- <button class="btn-blue" @click="cetakAll">
        Cetak Semua
      </button> -->
    </div>

    <!-- ================= EMPTY STATE / SKELETON ================= -->
    <div
      v-if="evaluasiList.length === 0"
      class="grid grid-cols-1 md:grid-cols-2 gap-6"
    >
      <div
        v-for="n in 2"
        :key="'sk-'+n"
        class="bg-white border rounded-lg p-6 shadow-sm animate-pulse"
      >
        <div class="space-y-4 text-sm">
          <div>
            <div class="h-3 w-24 bg-gray-200 rounded mb-2"></div>
            <div class="h-8 bg-gray-200 rounded"></div>
          </div>

          <div>
            <div class="h-3 w-28 bg-gray-200 rounded mb-2"></div>
            <div class="h-8 bg-gray-200 rounded"></div>
          </div>

          <div>
            <div class="h-3 w-20 bg-gray-200 rounded mb-2"></div>
            <div class="h-8 bg-gray-200 rounded"></div>
          </div>
        </div>

        <div class="mt-6 flex gap-3">
          <div class="h-8 w-28 bg-gray-200 rounded"></div>
          <div class="h-8 w-28 bg-gray-200 rounded"></div>
        </div>
      </div>

      <div class="col-span-full text-center text-gray-500 mt-2">
        Belum ada data evaluasi terapi
      </div>
    </div>

    <!-- ================= CARD LIST ASLI (TIDAK DIUBAH) ================= -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <div
        v-for="item in evaluasiList"
        :key="item.id"
        class="bg-white border rounded-lg p-6 shadow-sm"
      >
        <div class="space-y-4 text-sm">
          <div>
            <label class="label">Nama Anak</label>
            <input class="input" :value="item.nama_anak" readonly />
          </div>

          <div>
            <label class="label">Tanggal Evaluasi</label>
            <input class="input" :value="item.tanggal_evaluasi" readonly />
          </div>

          <div>
            <label class="label">Total Sesi</label>
            <input class="input" :value="item.total_sesi" readonly />
          </div>
        </div>

        <div class="mt-6 flex gap-3">
          <button class="btn-yellow" @click="openModal(item)">
            View Laporan
          </button>

          <button class="btn-blue" @click="cetakItem(item)">
            Cetak PDF
          </button>
        </div>
      </div>

    </div>

    <!-- MODAL -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    >
      <div class="bg-white w-full max-w-4xl rounded-lg p-6 max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-center border-b pb-3 mb-4">
          <h2 class="text-lg font-semibold">Hasil Evaluasi Terapi</h2>
          <button @click="closeModal">✕</button>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-6">
          <div>Nama Anak : <strong>{{ selected?.nama_anak }}</strong></div>
          <div>Tanggal Evaluasi : <strong>{{ selected?.tanggal_evaluasi }}</strong></div>
          <div>Total Sesi : <strong>{{ selected?.total_sesi }}</strong></div>
        </div>

        <div class="space-y-4 text-sm">
          <div><p class="font-semibold mb-1">Komponen Perilaku</p><div class="box">{{ selected?.komponen_perilaku }}</div></div>
          <div><p class="font-semibold mb-1">Kondisi Awal</p><div class="box">{{ selected?.kondisi_awal }}</div></div>
          <div><p class="font-semibold mb-1">Kondisi Saat Ini</p><div class="box">{{ selected?.kondisi_saat_ini }}</div></div>
          <div><p class="font-semibold mb-1">Kemampuan Sebelumnya</p><div class="box">{{ selected?.kemampuan_sebelumnya }}</div></div>
          <div><p class="font-semibold mb-1">Peningkatan Kemampuan Saat Ini</p><div class="box">{{ selected?.peningkatan_kemampuan_saat_ini }}</div></div>
          <div><p class="font-semibold mb-1">Program Lanjutan</p><div class="box">{{ selected?.program_lanjutan }}</div></div>
          <div><p class="font-semibold mb-1">Kesimpulan Hasil Follow Up</p><div class="box">{{ selected?.kesimpulan_hasil_followup }}</div></div>
          <div><p class="font-semibold mb-1">Saran Terapi</p><div class="box">{{ selected?.saran_terapi }}</div></div>
        </div>

        <div class="flex justify-end mt-6">
          <button class="border px-4 py-2 rounded hover:bg-gray-100" @click="closeModal">
            Tutup
          </button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/axios'

const evaluasiList = ref([])
const showModal = ref(false)
const selected = ref(null)

/* LOAD DATA */
onMounted(async () => {
  const res = await api.get('/hasil-evaluasi-anak')
  evaluasiList.value = Array.isArray(res.data) ? res.data : []
})

/* MODAL */
const openModal = (item) => {
  selected.value = item
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selected.value = null
}

/* CETAK PER ITEM */
const cetakItem = (item) => {
  const token = localStorage.getItem('token')

  if (!item.id || !token) {
    alert('Data tidak lengkap, silakan login ulang')
    return
  }

  window.open(
    `http://localhost:8000/api/cetak/evaluasi-anak/${item.id}/pdf?token=${token}`,
    '_blank'
  )
}

/* CETAK SEMUA */
const cetakAll = () => {
  const token = localStorage.getItem('token')

  if (!token) {
    alert('Silakan login ulang')
    return
  }

  window.open(
    `http://localhost:8000/api/cetak/evaluasi-anak/pdf?token=${token}`,
    '_blank'
  )
}
</script>

<style scoped>
.label {
  font-size: 12px;
  display: block;
  margin-bottom: 4px;
}

.input {
  width: 100%;
  border: 1px solid #ccc;
  padding: 7px;
  border-radius: 4px;
  background: #f9f9f9;
}

.btn-yellow {
  background: #fde68a;
  padding: 8px 14px;
  border-radius: 6px;
  font-size: 13px;
}

.btn-blue {
  background: #6d8bdb;
  color: #fff;
  padding: 8px 14px;
  border-radius: 6px;
  font-size: 13px;
}

.box {
  border: 1px solid #ccc;
  padding: 8px;
  border-radius: 4px;
  min-height: 50px;
  background: #fafafa;
}
</style>
