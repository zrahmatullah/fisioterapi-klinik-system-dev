<template>
  <div class="min-h-screen bg-gray-50 p-6 space-y-8">

    <!-- HEADER -->
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Dashboard Terapis</h1>
      <p class="text-gray-500 mt-1 text-sm">Daftar pasien & status assesment hari ini</p>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
          <i class="pi pi-users text-lg"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500">Total Pasien</p>
          <p class="text-2xl font-bold text-gray-800">{{ data.length }}</p>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
          <i class="pi pi-check-circle text-lg"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500">Sudah Assesment</p>
          <p class="text-2xl font-bold text-gray-800">{{ sudahAssesment }}</p>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
          <i class="pi pi-clock text-lg"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500">Belum Assesment</p>
          <p class="text-2xl font-bold text-gray-800">{{ belumAssesment }}</p>
        </div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="px-5 py-3.5 text-left font-semibold">No Regis</th>
              <th class="px-5 py-3.5 text-left font-semibold">Nama Anak</th>
              <th class="px-5 py-3.5 text-center font-semibold">Tanggal</th>
              <th class="px-5 py-3.5 text-center font-semibold">Ruangan</th>
              <th class="px-5 py-3.5 text-center font-semibold">Status</th>
              <th class="px-5 py-3.5 text-center font-semibold w-20">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="item in data"
              :key="item.id"
              class="border-t border-gray-100 hover:bg-indigo-50/40 transition"
            >
              <td class="px-5 py-3.5 text-gray-600">{{ item.no_regis }}</td>

              <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold text-xs shrink-0">
                    {{ (item.profile_anak?.nama_anak || 'A').charAt(0) }}
                  </div>
                  <span class="font-semibold text-gray-800">{{ item.profile_anak?.nama_anak || '-' }}</span>
                </div>
              </td>

              <td class="px-5 py-3.5 text-center text-gray-600">{{ formatTanggal(item.tgl_regis) }}</td>
              <td class="px-5 py-3.5 text-center text-gray-600">{{ item.ruangan?.ruangan || '-' }}</td>

              <td class="px-5 py-3.5 text-center">
                <span
                  v-if="item.assesment"
                  class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  Sudah
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                  Belum
                </span>
              </td>

              <td class="px-5 py-3.5 text-center">
                <button
                  @click="openAssesment(item)"
                  :disabled="item.assesment"
                  class="w-9 h-9 rounded-lg transition inline-flex items-center justify-center"
                  :class="item.assesment
                    ? 'bg-gray-100 text-gray-300 cursor-not-allowed'
                    : 'bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white'"
                  :aria-label="item.assesment ? 'Assesment sudah diisi' : 'Isi assesment'"
                >
                  <i class="pi pi-file-edit text-sm"></i>
                </button>
              </td>
            </tr>

            <tr v-if="data.length === 0">
              <td colspan="6" class="py-14 text-center">
                <div class="flex flex-col items-center">
                  <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                    <i class="pi pi-inbox text-xl text-gray-300"></i>
                  </div>
                  <p class="text-gray-500 font-medium text-sm">Tidak ada data pasien</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL WIZARD ASSESMENT -->
    <transition name="fade">
      <div
        v-if="showModal"
        class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      >
        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-xl max-h-[92vh] flex flex-col overflow-hidden">

          <!-- HEADER -->
          <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
            <div>
              <h2 class="text-lg font-bold text-gray-800">Assesment Anak</h2>
              <p class="text-sm text-gray-500">
                {{ selected?.profile_anak?.nama_anak }} &middot; {{ selected?.no_regis }}
              </p>
            </div>
            <button
              @click="closeModal"
              class="w-9 h-9 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition flex items-center justify-center"
              aria-label="Tutup"
            >
              <i class="pi pi-times"></i>
            </button>
          </div>

          <!-- STEP INDICATOR -->
          <div class="px-6 pt-5 pb-1 shrink-0">
            <div class="flex items-center">
              <template v-for="(s, idx) in steps" :key="s.id">
                <div class="flex items-center gap-2.5 flex-1">
                  <div
                    class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 transition"
                    :class="currentStep === idx
                      ? 'bg-indigo-600 text-white'
                      : currentStep > idx
                        ? 'bg-emerald-50 text-emerald-600'
                        : 'bg-gray-100 text-gray-400'"
                  >
                    <i v-if="currentStep > idx" class="pi pi-check text-xs"></i>
                    <span v-else>{{ idx + 1 }}</span>
                  </div>
                  <span
                    class="text-xs font-semibold hidden sm:block"
                    :class="currentStep === idx ? 'text-gray-800' : 'text-gray-400'"
                  >
                    {{ s.label }}
                  </span>
                </div>
                <div
                  v-if="idx < steps.length - 1"
                  class="h-px flex-1 mx-1 transition"
                  :class="currentStep > idx ? 'bg-emerald-300' : 'bg-gray-200'"
                ></div>
              </template>
            </div>
          </div>

          <!-- FORM BODY -->
          <form @submit.prevent="handleSubmit" class="flex-1 overflow-y-auto px-6 py-5">

            <!-- STEP 1: DATA KELAHIRAN -->
            <div v-show="currentStep === 0" class="space-y-4">
              <p class="text-sm text-gray-500 -mt-1 mb-2">Riwayat kelahiran dan kondisi awal anak.</p>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="label">Umur Anak</label>
                  <input v-model="form.umur" class="input" />
                </div>
                <div>
                  <label class="label">Usia Kehamilan</label>
                  <input v-model="form.usia_kehamilan_lahir" class="input" />
                </div>
              </div>

              <div>
                <label class="label">Gangguan Kehamilan</label>
                <input v-model="form.gangguan_kehamilan" class="input" />
              </div>

              <div>
                <label class="label">Proses Kelahiran</label>
                <input v-model="form.proses_kelahiran" class="input" />
              </div>

              <div>
                <label class="label">Gangguan Saat Melahirkan</label>
                <input v-model="form.gangguan_melahirkan" class="input" />
              </div>
            </div>

            <!-- STEP 2: PERILAKU & PERKEMBANGAN -->
            <div v-show="currentStep === 1" class="space-y-3">
              <p class="text-sm text-gray-500 -mt-1 mb-2">Tandai sesuai kondisi anak. Pilih "Ya" untuk menambahkan keterangan.</p>

              <div
                v-for="item in booleanFields"
                :key="item.key"
                class="border border-gray-100 rounded-xl p-4 space-y-3"
              >
                <div class="flex items-center justify-between gap-3">
                  <p class="font-medium text-gray-700 text-sm">{{ item.label }}</p>

                  <div class="flex gap-1 shrink-0 bg-gray-100 rounded-lg p-1">
                    <button
                      type="button"
                      @click="form[item.key] = '1'"
                      class="px-3 py-1 text-xs font-semibold rounded-md transition"
                      :class="String(form[item.key]) === '1' ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-white'"
                    >
                      Ya
                    </button>
                    <button
                      type="button"
                      @click="form[item.key] = '0'"
                      class="px-3 py-1 text-xs font-semibold rounded-md transition"
                      :class="String(form[item.key]) === '0' ? 'bg-gray-600 text-white' : 'text-gray-500 hover:bg-white'"
                    >
                      Tidak
                    </button>
                  </div>
                </div>

                <textarea
                  v-if="String(form[item.key]) === '1'"
                  v-model="form[item.ket]"
                  class="textarea"
                  placeholder="Keterangan tambahan..."
                ></textarea>
              </div>
            </div>

            <!-- STEP 3: CATATAN KLINIS -->
            <div v-show="currentStep === 2" class="space-y-4">
              <p class="text-sm text-gray-500 -mt-1 mb-2">Catatan subjektif dan objektif dari sesi terapi.</p>

              <div>
                <label class="label">Informasi Subjektif</label>
                <textarea v-model="form.informasi_subjektif" class="textarea" placeholder="Keluhan atau informasi dari orang tua / anak..."></textarea>
              </div>

              <div>
                <label class="label">Informasi Objektif</label>
                <textarea v-model="form.informasi_objektif" class="textarea" placeholder="Observasi dan temuan klinis terapis..."></textarea>
              </div>
            </div>

          </form>

          <!-- FOOTER NAVIGATION -->
          <div class="flex items-center justify-between gap-3 px-6 py-4 border-t border-gray-100 shrink-0">
            <button
              type="button"
              @click="currentStep === 0 ? closeModal() : currentStep--"
              class="btn-secondary"
            >
              {{ currentStep === 0 ? 'Batal' : 'Kembali' }}
            </button>

            <button
              v-if="currentStep < steps.length - 1"
              type="button"
              @click="currentStep++"
              class="btn-primary"
            >
              Lanjut
              <i class="pi pi-arrow-right text-xs ml-1.5"></i>
            </button>
            <button
              v-else
              type="button"
              @click="handleSubmit"
              class="btn-primary"
            >
              <i class="pi pi-check text-xs mr-1.5"></i>
              Simpan Assesment
            </button>
          </div>

        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import api from '../axios'

const formatTanggal = (dateStr) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`
}

const data = ref([])
const showModal = ref(false)
const selected = ref(null)

const sudahAssesment = computed(() => data.value.filter(d => d.assesment).length)
const belumAssesment = computed(() => data.value.filter(d => !d.assesment).length)

/* ================= WIZARD STEP ================= */
const steps = [
  { id: 'kelahiran', label: 'Data Kelahiran' },
  { id: 'perilaku', label: 'Perilaku & Perkembangan' },
  { id: 'catatan', label: 'Catatan Klinis' }
]
const currentStep = ref(0)

const form = reactive({
  registrasi_anak_id: null,
  umur: '',
  usia_kehamilan_lahir: '',
  gangguan_kehamilan: '',
  proses_kelahiran: '',
  gangguan_melahirkan: '',
  informasi_subjektif: '',
  informasi_objektif: '',

  riwayat_kejang: null,
  riwayat_kejang_ket: '',
  konsumsi_obat_epilepsi: null,
  konsumsi_obat_epilepsi_ket: '',
  perkembangan_sesuai_usia: null,
  perkembangan_sesuai_usia_ket: '',
  disusui_ibu: null,
  disusui_ibu_ket: '',
  tv_gadget_addict: null,
  tv_gadget_addict_ket: '',
  sering_memutar_benda: null,
  sering_memutar_benda_ket: '',
  main_mobil_berulang: null,
  main_mobil_berulang_ket: '',
  flapping: null,
  flapping_ket: '',
  tantrum: null,
  tantrum_ket: '',
  kontak_mata: null,
  kontak_mata_ket: '',
  gangguan_makan_menelan: null,
  gangguan_makan_menelan_ket: '',
  komunikasi_dua_arah: null,
  komunikasi_dua_arah_ket: ''
})

const booleanFields = [
  { key: 'riwayat_kejang', ket: 'riwayat_kejang_ket', label: 'Riwayat Kejang' },
  { key: 'konsumsi_obat_epilepsi', ket: 'konsumsi_obat_epilepsi_ket', label: 'Konsumsi Obat Epilepsi' },
  { key: 'perkembangan_sesuai_usia', ket: 'perkembangan_sesuai_usia_ket', label: 'Perkembangan Sesuai Usia' },
  { key: 'disusui_ibu', ket: 'disusui_ibu_ket', label: 'Disusui Ibu' },
  { key: 'tv_gadget_addict', ket: 'tv_gadget_addict_ket', label: 'TV / Gadget Addict' },
  { key: 'sering_memutar_benda', ket: 'sering_memutar_benda_ket', label: 'Sering Memutar Benda' },
  { key: 'main_mobil_berulang', ket: 'main_mobil_berulang_ket', label: 'Main Mobil Berulang' },
  { key: 'flapping', ket: 'flapping_ket', label: 'Flapping' },
  { key: 'tantrum', ket: 'tantrum_ket', label: 'Tantrum' },
  { key: 'kontak_mata', ket: 'kontak_mata_ket', label: 'Kontak Mata' },
  { key: 'gangguan_makan_menelan', ket: 'gangguan_makan_menelan_ket', label: 'Gangguan Makan & Menelan' },
  { key: 'komunikasi_dua_arah', ket: 'komunikasi_dua_arah_ket', label: 'Komunikasi Dua Arah' }
]

const resetForm = () => {
  Object.keys(form).forEach(k => {
    form[k] = k.endsWith('_ket') ? '' : null
  })
  currentStep.value = 0
}

onMounted(async () => {
  const res = await api.get('/dashboard-terapis')
  data.value = res.data
})

const openAssesment = (item) => {
  resetForm()
  selected.value = item
  form.registrasi_anak_id = item.id
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const handleSubmit = async () => {
  await api.post('/assesment-1', form)
  alert('Assesment berhasil disimpan')
  closeModal()
  const res = await api.get('/dashboard-terapis')
  data.value = res.data
}
</script>

<style scoped>
.label {
  @apply text-sm font-medium text-gray-600 mb-1.5 block;
}
.input {
  @apply w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm transition focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500;
}
.textarea {
  @apply w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm min-h-[80px] transition focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500;
}
.btn-primary {
  @apply px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition inline-flex items-center;
}
.btn-secondary {
  @apply px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>