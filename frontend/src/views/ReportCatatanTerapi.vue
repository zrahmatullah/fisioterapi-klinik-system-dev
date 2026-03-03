<template>
  <div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold text-gray-800">
      Report Catatan Terapi Anak
    </h1>

    <!-- ================= PILIH ANAK ================= -->
    <div class="bg-white p-4 rounded-xl shadow">
      <label class="label block mb-2">Pilih Anak</label>

      <select
        v-model="selectedRegistrasiId"
        class="input w-full"
      >
        <option value="">-- Pilih Nama Anak --</option>

        <option
          v-for="r in allRegistrasi"
          :key="r.id"
          :value="r.id"
        >
          {{ r.profile_anak?.nama_anak || 'Tanpa Nama' }}
          — {{ r.no_regis }}
        </option>
      </select>
    </div>

    <!-- ================= DATA ANAK ================= -->
    <div
      v-if="selectedRegistrasi"
      class="bg-white p-6 rounded-xl shadow space-y-3"
    >
      <h2 class="font-semibold text-lg">Data Anak</h2>

      <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
          Nama :
          <b>{{ selectedRegistrasi.profile_anak?.nama_anak }}</b>
        </div>

        <div>
          Jenis Kelamin :
          <b>
            {{ selectedRegistrasi.profile_anak?.jenis_kelamin?.nama || '-' }}
          </b>
        </div>

        <div>
          Tempat & Tanggal Lahir :
          <b>
            {{ selectedRegistrasi.profile_anak?.tempat_lahir || '-' }},
            {{ formatTanggalLahir(selectedRegistrasi.profile_anak?.tanggal_lahir) }}
          </b>
        </div>

        <div>
          Ruangan :
          <b>{{ selectedRegistrasi.ruangan?.ruangan || '-' }}</b>
        </div>

        <div>
          Terapis :
          <b>
            {{ selectedRegistrasi.pelayanans?.[0]?.terapis?.nama || '-' }}
          </b>
        </div>

        <div>
          Kategori Layanan :
          <b>
            {{
              selectedRegistrasi.pelayanans?.[0]?.layanan?.kategori?.kategori_layanan
                || '-'
            }}
          </b>
        </div>
      </div>
    </div>

    <!-- ================= CATATAN TERAPI PER SESI ================= -->
    <div
      v-if="selectedRegistrasi"
      class="bg-white p-6 rounded-xl shadow space-y-4"
    >
      <h2 class="font-semibold text-lg">
        Catatan Terapi per Sesi
      </h2>

      <div
        v-for="sesi in selectedRegistrasi.pelayanans"
        :key="sesi.id"
        class="border rounded-lg p-4 space-y-2"
      >
        <div class="flex justify-between items-center">
          <div class="font-semibold text-sm">
            {{ formatTanggal(sesi.tanggal_penjadwalan) }}
          </div>

          <span
            class="px-3 py-1 text-xs rounded-full font-medium"
            :class="sesi.catatan_aktivitas
              ? 'bg-green-100 text-green-700'
              : 'bg-yellow-100 text-yellow-700'"
          >
            {{ sesi.catatan_aktivitas ? 'Sudah Dicatat' : 'Belum Dicatat' }}
          </span>
        </div>

        <div class="text-sm">
          <b>Layanan:</b>
          {{ sesi.layanan?.layanan || '-' }}
        </div>

        <div class="text-sm">
          <b>Aktivitas Terapi:</b><br />
          {{ sesi.catatan_aktivitas?.aktivitas_terapi || '-' }}
        </div>

        <div class="text-sm">
          <b>Keterangan:</b><br />
          {{ sesi.catatan_aktivitas?.keterangan_terapi || '-' }}
        </div>

        <div class="text-sm">
          <b>Tugas Rumah:</b><br />
          {{ sesi.catatan_aktivitas?.tugas_rumah || '-' }}
        </div>
      </div>

      <div
        v-if="selectedRegistrasi.pelayanans.length === 0"
        class="text-gray-400 text-center"
      >
        Tidak ada sesi terapi
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../axios'

const allRegistrasi = ref([])
const selectedRegistrasiId = ref('')

onMounted(async () => {
  const res = await api.get('/dashboard-terapis')
  allRegistrasi.value = res.data
})

const selectedRegistrasi = computed(() =>
  allRegistrasi.value.find(
    r => r.id === selectedRegistrasiId.value
  )
)

/* FORMAT TANGGAL SESI (TETAP) */
const formatTanggal = (tgl) => {
  if (!tgl) return '-'
  return new Date(tgl).toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

/* FORMAT KHUSUS TANGGAL LAHIR (dd/mm/yyyy) */
const formatTanggalLahir = (tgl) => {
  if (!tgl) return '-'
  const d = new Date(tgl)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}/${month}/${year}`
}
</script>

<style scoped>
.label {
  @apply text-sm font-medium text-gray-600;
}
.input {
  @apply px-3 py-2 border rounded-lg bg-white;
}
</style>
