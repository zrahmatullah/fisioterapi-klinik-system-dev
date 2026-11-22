<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-y-auto custom-scrollbar w-full pr-1">

    <!-- HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-file-pdf"></i>
        </div>
        <div>
          <h1 class="text-base font-bold text-[#0F172A] leading-tight">Report Catatan Terapi Anak</h1>
          <p class="text-xs text-[#64748B] mt-0.5">Rekapitulasi berkas sesi tindakan klinis per pasien anak terdaftar</p>
        </div>
      </div>
    </div>

    <!-- ================= PILIH ANAK ================= -->
    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm shrink-0">
      <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Pasien Anak / No. Registrasi</label>

      <select
        v-model="selectedRegistrasiId"
        class="w-full h-[34px] px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
      >
        <option value="">-- Pilih Nama Anak --</option>

        <option
          v-for="r in allRegistrasi"
          :key="r.id"
          :value="r.id"
        >
          {{ r.profile_anak?.nama_anak || 'Tanpa Nama' }} — {{ r.no_regis }}
        </option>
      </select>
    </div>

    <!-- ================= DATA ANAK ================= -->
    <div
      v-if="selectedRegistrasi"
      class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3 shrink-0"
    >
      <h2 class="font-bold text-sm text-[#0F172A] border-b border-slate-100 pb-2 flex items-center gap-2">
        <i class="pi pi-user text-[#0F766E] text-xs"></i>
        Identitas Pasien Anak
      </h2>

      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
        <div class="bg-[#F8FAFC] p-2.5 rounded-lg border border-slate-200">
          <span class="text-slate-500 block text-[11px]">Nama Anak:</span>
          <b class="text-slate-900">{{ selectedRegistrasi.profile_anak?.nama_anak }}</b>
        </div>

        <div class="bg-[#F8FAFC] p-2.5 rounded-lg border border-slate-200">
          <span class="text-slate-500 block text-[11px]">Jenis Kelamin:</span>
          <b class="text-slate-900">{{ selectedRegistrasi.profile_anak?.jenis_kelamin?.nama || '-' }}</b>
        </div>

        <div class="bg-[#F8FAFC] p-2.5 rounded-lg border border-slate-200">
          <span class="text-slate-500 block text-[11px]">Tempat & Tanggal Lahir:</span>
          <b class="text-slate-900">
            {{ selectedRegistrasi.profile_anak?.tempat_lahir || '-' }},
            {{ formatTanggalLahir(selectedRegistrasi.profile_anak?.tanggal_lahir) }}
          </b>
        </div>

        <div class="bg-[#F8FAFC] p-2.5 rounded-lg border border-slate-200">
          <span class="text-slate-500 block text-[11px]">Ruangan Poli:</span>
          <b class="text-slate-900">{{ selectedRegistrasi.ruangan?.ruangan || '-' }}</b>
        </div>

        <div class="bg-[#F8FAFC] p-2.5 rounded-lg border border-slate-200">
          <span class="text-slate-500 block text-[11px]">Terapis Klinis:</span>
          <b class="text-slate-900">{{ selectedRegistrasi.pelayanans?.[0]?.terapis?.nama || '-' }}</b>
        </div>

        <div class="bg-[#F8FAFC] p-2.5 rounded-lg border border-slate-200">
          <span class="text-slate-500 block text-[11px]">Kategori Layanan:</span>
          <b class="text-[#0F766E]">
            {{ selectedRegistrasi.pelayanans?.[0]?.layanan?.kategori?.kategori_layanan || '-' }}
          </b>
        </div>
      </div>
    </div>

    <!-- ================= CATATAN TERAPI PER SESI ================= -->
    <div
      v-if="selectedRegistrasi"
      class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3 shrink-0"
    >
      <h2 class="font-bold text-sm text-[#0F172A] border-b border-slate-100 pb-2 flex items-center justify-between">
        <span class="flex items-center gap-2">
          <i class="pi pi-list text-[#0F766E] text-xs"></i>
          Catatan Terapi per Sesi Kunjungan
        </span>
        <span class="text-xs text-slate-500 font-normal">
          Total <strong>{{ selectedRegistrasi.pelayanans?.length || 0 }}</strong> sesi
        </span>
      </h2>

      <div class="space-y-3">
        <div
          v-for="(sesi, idx) in selectedRegistrasi.pelayanans"
          :key="sesi.id"
          class="border border-slate-200 rounded-xl p-3.5 space-y-2 bg-slate-50/50 hover:bg-slate-50 transition"
        >
          <div class="flex justify-between items-center">
            <div class="font-bold text-xs text-slate-900 flex items-center gap-2">
              <span class="w-6 h-6 rounded-full bg-[#0F766E] text-white flex items-center justify-center text-[10px]">
                {{ idx + 1 }}
              </span>
              <span>Sesi {{ idx + 1 }} &bull; {{ formatTanggal(sesi.tanggal_penjadwalan) }}</span>
            </div>

            <span
              class="px-2.5 py-0.5 text-[11px] rounded-full font-medium"
              :class="sesi.catatan_aktivitas
                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                : 'bg-amber-50 text-amber-700 border border-amber-200'"
            >
              {{ sesi.catatan_aktivitas ? 'Sudah Dicatat' : 'Belum Dicatat' }}
            </span>
          </div>

          <div class="text-xs text-slate-700">
            <span class="text-slate-500">Layanan:</span>
            <span class="font-semibold ml-1">{{ sesi.layanan?.layanan || '-' }}</span>
          </div>

          <div class="text-xs bg-white p-2.5 rounded-lg border border-slate-200">
            <b class="text-slate-800">Aktivitas Terapi:</b>
            <p class="mt-0.5 text-slate-600">{{ sesi.catatan_aktivitas?.aktivitas_terapi || '-' }}</p>
          </div>

          <div class="text-xs bg-white p-2.5 rounded-lg border border-slate-200">
            <b class="text-slate-800">Keterangan / Respons:</b>
            <p class="mt-0.5 text-slate-600">{{ sesi.catatan_aktivitas?.keterangan_terapi || '-' }}</p>
          </div>

          <div class="text-xs bg-[#E6F2F5] p-2.5 rounded-lg border border-[#CBE3E9]">
            <b class="text-[#0F766E]">Tugas Rumah:</b>
            <p class="mt-0.5 text-slate-800">{{ sesi.catatan_aktivitas?.tugas_rumah || '-' }}</p>
          </div>
        </div>
      </div>

      <div
        v-if="selectedRegistrasi.pelayanans.length === 0"
        class="text-slate-400 text-center py-8 text-xs"
      >
        Tidak ada sesi terapi tercatat untuk registrasi ini.
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
  try {
    const res = await api.get('/dashboard-terapis')
    allRegistrasi.value = res.data || []
  } catch (err) {
    console.error('Error fetching dashboard-terapis:', err)
  }
})

const selectedRegistrasi = computed(() =>
  allRegistrasi.value.find(
    r => r.id === selectedRegistrasiId.value
  )
)

const formatTanggal = (tgl) => {
  if (!tgl) return '-'
  return new Date(tgl).toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatTanggalLahir = (tgl) => {
  if (!tgl) return '-'
  const d = new Date(tgl)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}/${month}/${year}`
}
</script>
