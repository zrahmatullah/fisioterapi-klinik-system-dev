<template>
  <div class="p-6 space-y-6">
    <!-- TITLE -->
    <h1 class="text-2xl font-bold text-gray-800">Dashboard Terapis</h1>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="px-4 py-3 text-left">No Regis</th>
            <th class="px-4 py-3 text-left">Nama Anak</th>
            <th class="px-4 py-3 text-center">Tanggal</th>
            <th class="px-4 py-3 text-center">Ruangan</th>
            <th class="px-4 py-3 text-center">Status</th>
            <th class="px-4 py-3 text-center w-20">Aksi</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="item in data"
            :key="item.id"
            class="border-t hover:bg-gray-50 transition"
          >
            <td class="px-4 py-3">{{ item.no_regis }}</td>
            <td class="px-4 py-3 font-semibold">
              {{ item.profile_anak?.nama_anak }}
            </td>
            <td class="px-4 py-3 text-center">{{ item.tgl_regis }}</td>
            <td class="px-4 py-3 text-center">{{ item.ruangan?.ruangan }}</td>

            <td class="px-4 py-3 text-center">
              <span
                v-if="item.assesment"
                class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700"
              >
                Sudah
              </span>
              <span
                v-else
                class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700"
              >
                Belum
              </span>
            </td>

            <!-- ICON BUTTON -->
            <td class="px-4 py-3 text-center">
              <button
                @click="openAssesment(item)"
                :disabled="item.assesment"
                class="p-2 rounded-lg transition"
                :class="item.assesment
                  ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                  : 'bg-indigo-100 text-indigo-600 hover:bg-indigo-200'"
              >
                <i class="pi pi-file-edit text-lg"></i>
              </button>
            </td>
          </tr>

          <tr v-if="data.length === 0">
            <td colspan="6" class="py-8 text-center text-gray-400">
              Tidak ada data
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center"
    >
      <div
        class="bg-white w-full max-w-6xl rounded-2xl shadow-xl max-h-[90vh] overflow-hidden"
      >
        <!-- HEADER -->
        <div class="flex justify-between items-center px-6 py-4 border-b">
          <div>
            <h2 class="text-lg font-bold">Assesment Anak</h2>
            <p class="text-sm text-gray-500">
              {{ selected?.profile_anak?.nama_anak }} • {{ selected?.no_regis }}
            </p>
          </div>
          <button
            @click="closeModal"
            class="text-gray-500 hover:text-red-500 text-xl"
          >
            ✕
          </button>
        </div>

        <!-- FORM -->
        <form
          @submit.prevent="submitAssesment"
          class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 overflow-y-auto max-h-[75vh]"
        >
          <!-- LEFT -->
          <div class="space-y-4">
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

            <div>
              <label class="label">Informasi Subjektif</label>
              <textarea v-model="form.informasi_subjektif" class="textarea"></textarea>
            </div>

            <div>
              <label class="label">Informasi Objektif</label>
              <textarea v-model="form.informasi_objektif" class="textarea"></textarea>
            </div>
          </div>

          <!-- RIGHT -->
          <div class="space-y-4">
            <div
              v-for="item in booleanFields"
              :key="item.key"
              class="border rounded-xl p-4 space-y-3"
            >
              <p class="font-medium text-gray-700">{{ item.label }}</p>

              <div class="flex gap-6 text-sm">
                <label class="flex items-center gap-2">
                  <input
                    type="radio"
                    :name="item.key"
                    value="1"
                    v-model="form[item.key]"
                  />
                  Ya
                </label>
                <label class="flex items-center gap-2">
                  <input
                    type="radio"
                    :name="item.key"
                    value="0"
                    v-model="form[item.key]"
                  />
                  Tidak
                </label>
              </div>

              <textarea
                v-if="form[item.key] == 1"
                v-model="form[item.ket]"
                class="textarea"
                placeholder="Keterangan"
              ></textarea>
            </div>
          </div>

          <!-- ACTION -->
          <div class="md:col-span-2 flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="closeModal" class="btn-secondary">
              Batal
            </button>
            <button type="submit" class="btn-primary">
              Simpan Assesment
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '../axios'

const formatTanggal = (dateStr) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`
}


const data = ref([])
const showModal = ref(false)
const selected = ref(null)

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

const submitAssesment = async () => {
  await api.post('/assesment-1', form)
  alert('Assesment berhasil disimpan')
  closeModal()
  const res = await api.get('/dashboard-terapis')
  data.value = res.data
}
</script>

<style scoped>
.label {
  @apply text-sm font-medium text-gray-600;
}
.input {
  @apply w-full mt-1 px-3 py-2 border rounded-lg focus:ring focus:ring-indigo-200;
}
.textarea {
  @apply w-full mt-1 px-3 py-2 border rounded-lg min-h-[80px] focus:ring focus:ring-indigo-200;
}
.btn-primary {
  @apply px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700;
}
.btn-secondary {
  @apply px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100;
}
</style>
