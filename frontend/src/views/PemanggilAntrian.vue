<template>
  <div class="p-6 bg-gradient-to-br from-slate-100 to-slate-200 min-h-screen space-y-6">
<!-- HEADER -->
<div class="flex justify-between items-center bg-white p-5 rounded-2xl shadow">
  <div>
    <h1 class="text-3xl font-bold text-indigo-700">Pemanggil Antrian</h1>
    <p class="text-sm text-slate-500">
      Antrian hari ini • {{ todayLabel }}
    </p>
  </div>

  <!-- FILTER RUANGAN -->
  <select v-model="ruanganId" @change="loadData" class="input w-60">
    <option value="">Semua Ruangan</option>
    <option v-for="r in ruanganList" :key="r.id" :value="r.id">
      {{ r.ruangan }}
    </option>
  </select>
</div>

<!-- ANTRIAN AKTIF -->
<div v-if="current"
  class="bg-indigo-600 text-white rounded-2xl shadow-lg p-8 text-center">
  <div class="text-sm opacity-80">Sedang Dipanggil</div>
  <div class="text-6xl font-extrabold tracking-widest">
    {{ current.no_antrian }}
  </div>
  <div class="text-xl mt-2">{{ current.nama_anak }}</div>
  <div class="text-sm opacity-80">{{ current.ruangan }}</div>
</div>

<!-- TABLE -->
<div class="bg-white rounded-2xl shadow overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-indigo-50">
      <tr>
        <th class="th text-center">No</th>
        <th class="th">No Antrian</th>
        <th class="th">Nama Anak</th>
        <th class="th">Ruangan</th>
        <th class="th">Terapis</th>
        <th class="th text-center">Aksi</th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="(a, i) in antrian" :key="a.id"
          class="border-t hover:bg-indigo-50/40 transition">

        <td class="td text-center">{{ i + 1 }}</td>

        <td class="td font-bold text-indigo-600 text-lg">
          {{ a.no_antrian }}
        </td>

        <td class="td">{{ a.profile_anak?.nama_anak }}</td>
        <td class="td">{{ a.ruangan?.ruangan }}</td>
        <td class="td">{{ a.terapis?.nama }}</td>

        <!-- BUTTON ICON TELEPON -->
        <td class="td text-center">
          <button
            class="btn-call"
            @click="panggil(a)"
            title="Panggil Antrian">
            📞
          </button>
        </td>
      </tr>

      <tr v-if="antrian.length === 0">
        <td colspan="6" class="text-center py-10 text-slate-400">
          Tidak ada antrian hari ini
        </td>
      </tr>
    </tbody>
  </table>
</div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const antrian = ref([])
const ruanganList = ref([])
const ruanganId = ref('')
const current = ref(null)

const today = new Date().toISOString().slice(0, 10)
const todayLabel = new Date().toLocaleDateString('id-ID', {
  day: '2-digit',
  month: 'long',
  year: 'numeric'
})

const loadRuangan = async () => {
  const res = await api.get('/ruangan')
  ruanganList.value = res.data.data ?? res.data
}

const loadData = async () => {
  const res = await api.get('/registrasi-anak')

  antrian.value = res.data.filter(r =>
    ['belum_dilayani', 'dipanggil'].includes(r.status_pelayanan) &&
    r.tgl_regis?.slice(0,10) === today &&
    (!ruanganId.value || r.ruangan_id == ruanganId.value)
  )
}

const panggil = async (row) => {
  try {
    const res = await api.put(`/registrasi-anak/${row.id}/panggil`)

    current.value = res.data.data

    toast.success(`Memanggil ${current.value.no_antrian}`)

    await loadData()
  } catch {
    toast.error('Gagal memanggil antrian')
  }
}

onMounted(async () => {
  await loadRuangan()
  await loadData()
})
</script>

<style scoped>
.th { @apply px-4 py-3 text-xs font-semibold text-slate-600 uppercase; }
.td { @apply px-4 py-4 text-slate-700; }

.input {
  @apply border border-slate-300 rounded-xl px-3 py-2
         focus:ring-2 focus:ring-indigo-400 outline-none;
}

/* BUTTON TELEPON */
.btn-call {
  @apply bg-green-500 hover:bg-green-600
         text-white px-4 py-2 rounded-xl shadow
         flex items-center justify-center gap-2
         transition;
}
</style>
