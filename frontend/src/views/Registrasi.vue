<template>
  <div class="min-h-screen bg-slate-50 p-6">
    <!-- HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-semibold text-slate-800 tracking-tight">
          Registrasi Anak
        </h1>
        <p class="text-sm text-slate-500 mt-1">
          Kelola data registrasi, kedatangan, dan antrian terapi anak
        </p>
      </div>

      <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative">
          <i class="pi pi-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
          <input
            type="date"
            v-model="filterTanggal"
            class="pl-9 pr-3 py-2.5 rounded-lg border border-slate-200 bg-white text-sm
                   focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none"
            @change="load"
          />
        </div>

        <button
          @click="openAdd"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg
                 flex items-center justify-center gap-2 text-sm font-medium transition-colors"
        >
          <i class="pi pi-plus text-xs" />
          Pendaftaran baru
        </button>
      </div>
    </div>

    <!-- STATISTICS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white border border-slate-200 rounded-xl p-5">
        <p class="text-sm text-slate-500">Total registrasi</p>
        <h3 class="text-2xl font-semibold text-slate-800 mt-1">{{ data.length }}</h3>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-5">
        <p class="text-sm text-slate-500 flex items-center gap-1.5">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
          Sudah hadir
        </p>
        <h3 class="text-2xl font-semibold text-slate-800 mt-1">
          {{ data.filter(x => x.status_kedatangan === 'sudah_datang').length }}
        </h3>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-5">
        <p class="text-sm text-slate-500 flex items-center gap-1.5">
          <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
          Tidak hadir
        </p>
        <h3 class="text-2xl font-semibold text-slate-800 mt-1">
          {{ data.filter(x => x.status_kedatangan === 'tidak_hadir').length }}
        </h3>
      </div>
    </div>

    <!-- TABLE CARD -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="border-b border-slate-200">
              <th class="th text-center w-12">No</th>
              <th class="th">No regis</th>
              <th class="th">ID pasien</th>
              <th class="th">Nama anak</th>
              <th class="th">Tanggal</th>
              <th class="th">Terapis</th>
              <th class="th">Ruangan</th>
              <th class="th text-center">Kedatangan</th>
              <th class="th text-center">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(item, index) in data"
              :key="item.id"
              class="border-b border-slate-100 hover:bg-slate-50 transition-colors"
            >
              <td class="td text-center text-slate-400">{{ index + 1 }}</td>
              <td class="td font-medium text-indigo-600">{{ item.no_regis }}</td>
              <td class="td text-slate-600">{{ item.id_pasien }}</td>

              <td class="td">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-medium text-sm flex-shrink-0">
                    {{ item.profile_anak?.nama_anak?.charAt(0) || 'A' }}
                  </div>
                  <p class="font-medium text-slate-800">{{ item.profile_anak?.nama_anak || '-' }}</p>
                </div>
              </td>

              <td class="td text-slate-600">{{ item.tgl_regis }}</td>
              <td class="td text-slate-600">{{ item.terapis?.nama || '-' }}</td>

              <td class="td">
                <span class="px-2.5 py-1 rounded-md text-xs bg-slate-100 text-slate-600">
                  {{ item.ruangan?.ruangan || '-' }}
                </span>
              </td>

              <!-- STATUS -->
              <td class="td text-center">
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium"
                  :class="{
                    'bg-emerald-50 text-emerald-700': item.status_kedatangan === 'sudah_datang',
                    'bg-rose-50 text-rose-700': item.status_kedatangan === 'tidak_hadir',
                    'bg-slate-100 text-slate-500': item.status_kedatangan === 'belum_datang'
                  }"
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full"
                    :class="{
                      'bg-emerald-500': item.status_kedatangan === 'sudah_datang',
                      'bg-rose-500': item.status_kedatangan === 'tidak_hadir',
                      'bg-slate-400': item.status_kedatangan === 'belum_datang'
                    }"
                  ></span>
                  {{ item.status_kedatangan.replace('_', ' ') }}
                </span>
              </td>

              <!-- ACTION -->
              <td class="td">
                <div class="flex justify-center gap-1.5">
                  <button class="action-btn" title="Cetak" @click="cetak(item)">
                    <i class="pi pi-print text-sm"></i>
                  </button>

                  <button class="action-btn" title="Cetak antrian" @click="cetakAntrian(item)">
                    <i class="pi pi-ticket text-sm"></i>
                  </button>

                  <button
                    v-if="item.status_kedatangan !== 'sudah_datang'"
                    @click="updateKedatangan(item, 'sudah_datang')"
                    title="Tandai hadir"
                    class="w-8 h-8 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white
                           flex items-center justify-center transition-colors"
                  >
                    <i class="pi pi-check text-xs"></i>
                  </button>

                  <button
                    v-if="item.status_kedatangan !== 'tidak_hadir'"
                    @click="updateKedatangan(item, 'tidak_hadir')"
                    title="Tandai tidak hadir"
                    class="w-8 h-8 rounded-lg bg-rose-500 hover:bg-rose-600 text-white
                           flex items-center justify-center transition-colors"
                  >
                    <i class="pi pi-times text-xs"></i>
                  </button>
                </div>
              </td>
            </tr>

            <!-- EMPTY -->
            <tr v-if="data.length === 0">
              <td colspan="9" class="py-16">
                <div class="flex flex-col items-center justify-center text-center">
                  <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                    <i class="pi pi-inbox text-2xl text-slate-400"></i>
                  </div>
                  <h3 class="text-base font-medium text-slate-700">Belum ada registrasi</h3>
                  <p class="text-sm text-slate-500 mt-1">Belum ada data registrasi pada tanggal ini</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL -->
    <div v-if="dialog" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/40" @click="closeDialog"></div>

      <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-xl flex flex-col max-h-[90vh] animate-fadeIn">
        <!-- HEADER -->
        <div class="flex justify-between items-center px-7 pt-6 pb-5 border-b border-slate-100">
          <div>
            <h3 class="text-lg font-semibold text-slate-800">Form registrasi anak</h3>
            <p class="text-sm text-slate-500 mt-0.5">
              {{ step === 1 ? 'Langkah 1 dari 2 — Data registrasi' : 'Langkah 2 dari 2 — Orang tua & kondisi anak' }}
            </p>
          </div>

          <button @click="closeDialog" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center transition-colors flex-shrink-0">
            <i class="pi pi-times text-slate-500 text-sm"></i>
          </button>
        </div>

        <!-- STEP INDICATOR -->
        <div class="flex items-center gap-2 px-7 pt-5">
          <div class="flex items-center gap-2 flex-1">
            <div
              class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-medium flex-shrink-0"
              :class="step >= 1 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400'"
            >
              <i v-if="step > 1" class="pi pi-check text-xs"></i>
              <span v-else>1</span>
            </div>
            <span class="text-xs font-medium" :class="step >= 1 ? 'text-slate-700' : 'text-slate-400'">Data registrasi</span>
          </div>

          <div class="h-px flex-1 mx-1" :class="step >= 2 ? 'bg-indigo-600' : 'bg-slate-200'"></div>

          <div class="flex items-center gap-2 flex-1 justify-end">
            <span class="text-xs font-medium" :class="step >= 2 ? 'text-slate-700' : 'text-slate-400'">Orang tua & kondisi</span>
            <div
              class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-medium flex-shrink-0"
              :class="step >= 2 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400'"
            >
              2
            </div>
          </div>
        </div>

        <!-- BODY (scrollable) -->
        <div class="px-7 py-6 overflow-y-auto flex-1">

          <!-- STEP 1: DATA REGISTRASI -->
          <div v-if="step === 1" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="label">{{ isOrtu ? 'Nama anak' : 'Pilih anak' }}</label>

              <input
                v-if="isOrtu"
                type="text"
                class="input bg-slate-50 text-slate-500"
                :value="anak[0]?.nama_anak || '-'"
                readonly
              />

              <select v-else v-model="form.profile_anak_id" class="input">
                <option value="">Pilih anak</option>
                <option v-for="a in anak" :key="a.id" :value="a.id">{{ a.nama_anak }}</option>
              </select>
            </div>

            <div>
              <label class="label">Tanggal registrasi</label>
              <input type="date" v-model="form.tgl_regis" class="input" />
              <p class="helper">Terapis difilter sesuai tanggal</p>
            </div>

            <div>
              <label class="label">Terapis</label>
              <select v-model="form.terapis_id" class="input" :disabled="!form.tgl_regis">
                <option value="">{{ form.tgl_regis ? 'Pilih terapis' : 'Pilih tanggal dahulu' }}</option>
                <option v-for="t in terapis" :key="t.id" :value="t.id">{{ t.nama }}</option>
              </select>
            </div>

            <div>
              <label class="label">Ruangan</label>
              <select v-model="form.ruangan_id" class="input">
                <option value="">Pilih ruangan</option>
                <option v-for="r in ruangan" :key="r.id" :value="r.id">{{ r.ruangan }}</option>
              </select>
            </div>
          </div>

          <!-- STEP 2: ORANG TUA & KONDISI -->
          <div v-else class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="label">Nama ayah</label>
                <input v-model="form.nama_ayah" class="input" />
              </div>

              <div>
                <label class="label">Nama ibu</label>
                <input v-model="form.nama_ibu" class="input" />
              </div>

              <div>
                <label class="label">No telepon</label>
                <input v-model="form.notelp" class="input" />
              </div>

              <div>
                <label class="label">Usia saat menikah</label>
                <input type="number" v-model="form.usia_saat_menikah" class="input" />
              </div>
            </div>

            <div>
              <label class="label">Alamat</label>
              <textarea v-model="form.alamat" rows="2" class="input"></textarea>
            </div>

            <div>
              <label class="label">Keluhan saat ini</label>
              <textarea v-model="form.keluhan_saat_ini" rows="2" class="input"></textarea>
            </div>

            <div>
              <label class="label">Kemampuan saat ini</label>
              <textarea v-model="form.kemampuan_saat_ini" rows="2" class="input"></textarea>
            </div>
          </div>
        </div>

        <!-- FOOTER -->
        <div class="flex items-center justify-between px-7 py-5 border-t border-slate-100 flex-shrink-0">
          <button
            v-if="step === 2"
            @click="step = 1"
            :disabled="saving || saved"
            class="px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors flex items-center gap-1.5
                   disabled:opacity-0 disabled:pointer-events-none"
          >
            <i class="pi pi-arrow-left text-xs"></i>
            Kembali
          </button>
          <button v-else @click="closeDialog" class="px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">
            Batal
          </button>

          <button
            v-if="step === 1"
            @click="step = 2"
            class="px-5 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition-colors flex items-center gap-1.5"
          >
            Lanjut
            <i class="pi pi-arrow-right text-xs"></i>
          </button>
          <button
            v-else
            @click="save"
            :disabled="saving || saved"
            class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-medium
                   transition-all duration-200 flex items-center justify-center gap-2 min-w-[160px]
                   disabled:cursor-not-allowed"
            :class="saved ? 'bg-emerald-600' : 'hover:bg-indigo-700'"
          >
            <span v-if="saved" class="checkmark-pop flex items-center gap-2">
              <i class="pi pi-check text-xs"></i>
              Tersimpan
            </span>
            <span v-else-if="saving" class="flex items-center gap-2">
              <span class="spinner"></span>
              Menyimpan...
            </span>
            <span v-else>Simpan registrasi</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const data = ref([])
const anak = ref([])
const terapis = ref([])
const ruangan = ref([])
const dialog = ref(false)
const step = ref(1)
const saving = ref(false)
const saved = ref(false)
const user = ref(null)

const filterTanggal = ref(new Date().toISOString().slice(0, 10))

const isOrtu = computed(() => Number(user.value?.role_id) === 15)

const form = ref({
  profile_anak_id: '',
  tgl_regis: '',
  terapis_id: '',
  ruangan_id: '',
  nama_ayah: '',
  nama_ibu: '',
  notelp: '',
  usia_saat_menikah: '',
  alamat: '',
  keluhan_saat_ini: '',
  kemampuan_saat_ini: ''
})

onMounted(async () => {
  const u = localStorage.getItem('user')
  user.value = u ? JSON.parse(u) : null
  await load()
})

async function load() {
  if (isOrtu.value) {
    const anakSayaRes = await api.get('/profile-anak-saya')
    anak.value = anakSayaRes.data ?? []
  } else {
    const anakRes = await api.get('/profile-anak')
    anak.value = anakRes.data ?? []
  }

  const r1 = await api.get('/registrasi-anak')
  let allData = r1.data ?? []

  allData = allData.filter(item => {
    const tgl = item.tgl_regis?.slice(0, 10)
    return tgl === filterTanggal.value
  })

  if (isOrtu.value) {
    const anakIds = anak.value.map(a => Number(a.id))
    allData = allData.filter(item => anakIds.includes(Number(item.profile_anak_id)))
  }

  data.value = allData

  ruangan.value = (await api.get('/ruangan')).data ?? []
}

watch(() => form.value.tgl_regis, async val => {
  if (!val) {
    terapis.value = []
    form.value.terapis_id = ''
    return
  }

  const res = await api.get('/terapis-by-tanggal', { params: { tanggal: val } })
  terapis.value = res.data?.data ?? []
})

watch(() => form.value.terapis_id, id => {
  if (!id) {
    ruangan.value = []
    form.value.ruangan_id = ''
    return
  }

  const t = terapis.value.find(x => Number(x.id) === Number(id))

  if (t && t.ruangans && t.ruangans.length) {
    ruangan.value = t.ruangans
    form.value.ruangan_id = t.ruangans[0].id
  } else {
    ruangan.value = []
    form.value.ruangan_id = ''
  }
})

function openAdd() {
  Object.keys(form.value).forEach(k => (form.value[k] = ''))

  if (isOrtu.value && anak.value.length > 0) {
    form.value.profile_anak_id = anak.value[0].id
  }

  step.value = 1
  saving.value = false
  saved.value = false
  dialog.value = true
}

function closeDialog() {
  if (saving.value) return
  dialog.value = false
}

async function save() {
  saving.value = true

  try {
    await api.post('/registrasi-anak', form.value)

    saving.value = false
    saved.value = true

    // tampilkan checkmark sebentar sebelum modal tertutup
    setTimeout(() => {
      toast.success('Registrasi berhasil')
      dialog.value = false
      saved.value = false
      load()
    }, 700)
  } catch (err) {
    saving.value = false
    console.error(err)
    toast.error('Gagal menyimpan registrasi')
  }
}

function cetak(item) {
  const baseUrl = import.meta.env.VITE_API_BASE_URL
  window.open(`${baseUrl}/cetak/registrasi-anak/${item.id}`, '_blank')
}

function cetakAntrian(item) {
  const baseUrl = import.meta.env.VITE_API_BASE_URL
  window.open(`${baseUrl}/cetak/antrian/${item.id}`, '_blank')
}

async function updateKedatangan(item, status) {
  try {
    const res = await api.put(`/registrasi-anak/${item.id}/kedatangan`, { status_kedatangan: status })
    item.status_kedatangan = res.data.data.status_kedatangan
    item.waktu_kedatangan = res.data.data.waktu_kedatangan
    toast.success('Status kedatangan diperbarui')
  } catch (err) {
    console.error(err)
    toast.error('Gagal update status')
  }
}
</script>

<style scoped>
.th {
  @apply px-5 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-slate-500;
}

.td {
  @apply px-5 py-3.5 text-slate-700 align-middle;
}

.label {
  @apply text-sm font-medium text-slate-600 mb-1.5 block;
}

.helper {
  @apply text-xs text-slate-400 mt-1;
}

.input {
  @apply w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm
         transition-colors
         focus:ring-2 focus:ring-indigo-100
         focus:border-indigo-400
         focus:outline-none;
}

.action-btn {
  @apply w-8 h-8 rounded-lg
         border border-slate-200
         bg-white hover:bg-slate-50
         text-slate-500 hover:text-indigo-600
         flex items-center justify-center
         transition-colors;
}

.animate-fadeIn {
  animation: fadeIn 0.15s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.98) translateY(4px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.spinner {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255, 255, 255, 0.4);
  border-top-color: white;
  border-radius: 50%;
  display: inline-block;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.checkmark-pop {
  animation: popIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes popIn {
  from {
    opacity: 0;
    transform: scale(0.6);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>