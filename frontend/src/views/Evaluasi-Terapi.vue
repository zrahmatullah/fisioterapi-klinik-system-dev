<template>
  <div class="p-6 space-y-6">
    <!-- HEADER -->
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-800">
        Evaluasi Terapi Anak
      </h1>
    </div>

    <!-- PILIH REGISTRASI -->
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
      <label class="label">Pilih Anak / Registrasi</label>

      <select v-model="selectedRegistrasiId" class="input">
        <option value="">-- Pilih Anak --</option>
        <option
          v-for="r in registrasiList"
          :key="r.id"
          :value="r.id"
        >
          {{ r.profile_anak?.nama_anak }} — {{ r.no_regis }}
        </option>
      </select>

      <!-- INFO STATUS -->
      <div v-if="selectedRegistrasiData" class="text-sm">
        <p class="text-gray-600">
          Total Sesi :
          <b>{{ totalSesi }}</b>
        </p>

        <p v-if="!bolehEvaluasi" class="text-red-600 mt-1">
          ⚠ Semua sesi terapi harus memiliki catatan aktivitas yang lengkap sebelum membuat evaluasi.
        </p>

        <p v-if="evaluasiExist" class="text-green-600 mt-1">
          ✔ Evaluasi sudah dibuat (hanya dapat dilihat)
        </p>
      </div>
    </div>

    <!-- FORM EVALUASI -->
    <div
      v-if="selectedRegistrasiData"
      class="bg-white rounded-xl shadow p-6 space-y-6"
    >
      <!-- IDENTITAS -->
      <div class="grid grid-cols-2 gap-4 text-sm bg-gray-50 p-4 rounded-lg">
        <Info label="Nama Anak" :value="selectedRegistrasiData.profile_anak?.nama_anak" />
        <Info label="Tanggal Lahir" :value="selectedRegistrasiData.profile_anak?.tanggal_lahir" />
        <Info label="Kategori Layanan" :value="kategoriLayanan" />
        <Info label="Terapis" :value="selectedRegistrasiData.pelayanans?.[0]?.terapis?.nama" />
      </div>

      <!-- FORM -->
      <form @submit.prevent="submit" class="space-y-8">

        <!-- EVALUASI TERAPI -->
        <div class="space-y-5">
          <h3 class="section-title">Evaluasi Terapi</h3>

          <div>
            <label class="label">Komponen Perilaku</label>
            <textarea v-model="form.komponen_perilaku" :disabled="viewOnly" class="textarea" />
          </div>

          <div>
            <label class="label">Kondisi Awal</label>
            <textarea v-model="form.kondisi_awal" :disabled="viewOnly" class="textarea" />
          </div>

          <div>
            <label class="label">Kondisi Saat Ini</label>
            <textarea v-model="form.kondisi_saat_ini" :disabled="viewOnly" class="textarea" />
          </div>

          <div>
            <label class="label">Program Lanjutan</label>
            <textarea v-model="form.program_lanjutan" :disabled="viewOnly" class="textarea" />
          </div>
        </div>

        <!-- HASIL & SARAN -->
        <div class="space-y-5 pt-6 border-t">
          <h3 class="section-title">Hasil Evaluasi & Saran</h3>

          <div>
            <label class="label">Kesimpulan Hasil Follow Up</label>
            <textarea v-model="form.kesimpulan_hasil_followup" :disabled="viewOnly" class="textarea" />
          </div>

          <div>
            <label class="label">Kemampuan Sebelumnya</label>
            <textarea v-model="form.kemampuan_sebelumnya" :disabled="viewOnly" class="textarea" />
          </div>

          <div>
            <label class="label">Peningkatan Kemampuan Saat Ini</label>
            <textarea v-model="form.peningkatan_kemampuan_saat_ini" :disabled="viewOnly" class="textarea" />
          </div>

          <div>
            <label class="label">Saran Terapi</label>
            <textarea v-model="form.saran_terapi" :disabled="viewOnly" class="textarea" />
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t">
          <button type="submit" class="btn-primary" :disabled="!bolehEvaluasi || viewOnly">
            Simpan Evaluasi
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useToast } from 'vue-toastification'
import api from '../axios'

const toast = useToast()

const registrasiList = ref([])
const selectedRegistrasiId = ref('')
const evaluasiExist = ref(false)

const selectedRegistrasiData = computed(() => {
  return registrasiList.value.find(r => r.id === selectedRegistrasiId.value) || null
})

const form = reactive({
  registrasi_anak_id: null,
  komponen_perilaku: '',
  kondisi_awal: '',
  kondisi_saat_ini: '',
  program_lanjutan: '',
  kesimpulan_hasil_followup: '',
  kemampuan_sebelumnya: '',
  peningkatan_kemampuan_saat_ini: '',
  saran_terapi: ''
})

const totalSesi = computed(() =>
  selectedRegistrasiData.value?.pelayanans?.length || 0
)

const bolehEvaluasi = computed(() =>
  selectedRegistrasiData.value?.pelayanans?.length > 0 &&
  selectedRegistrasiData.value.pelayanans.every(p => p.catatan_aktivitas)
)

const viewOnly = computed(() => evaluasiExist.value)

const kategoriLayanan = computed(() =>
  selectedRegistrasiData.value?.pelayanans?.[0]?.layanan?.kategori?.kategori_layanan || '-'
)

onMounted(async () => {
  const res = await api.get('/dashboard-terapis')
  registrasiList.value = res.data
})

watch(selectedRegistrasiId, async (id) => {
  if (!id) return

  const val = registrasiList.value.find(r => r.id === id)
  if (!val) return

  form.registrasi_anak_id = val.id
  evaluasiExist.value = false

  Object.keys(form).forEach(k => {
    if (k !== 'registrasi_anak_id') form[k] = ''
  })

  const res = await api.get(`/evaluasi-terapi/by-registrasi/${val.id}`)
  if (res.data) {
    Object.assign(form, res.data)
    evaluasiExist.value = true
  }
})

const submit = async () => {
  try {
    await api.post('/evaluasi-terapi', form)
    toast.success('Evaluasi terapi berhasil disimpan')
    evaluasiExist.value = true
  } catch (e) {
    toast.error(e.response?.data?.message || 'Gagal menyimpan evaluasi')
  }
}
</script>

<style scoped>
.label {
  @apply block text-sm font-medium text-gray-600 mb-1;
}
.input {
  @apply w-full px-3 py-2 border rounded-lg focus:ring focus:ring-indigo-200;
}
.textarea {
  @apply w-full px-3 py-2 border rounded-lg min-h-[100px]
         focus:ring-2 focus:ring-indigo-200
         focus:border-indigo-400
         disabled:bg-gray-100;
}
.btn-primary {
  @apply px-4 py-2 rounded-lg bg-indigo-600 text-white
         hover:bg-indigo-700 disabled:opacity-50;
}
.section-title {
  @apply text-base font-semibold text-gray-800;
}
</style>
