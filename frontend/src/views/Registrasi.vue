<template>
  <div
    class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50 to-violet-50 p-6"
  >
    <!-- HEADER -->
    <div
      class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8"
    >
      <div>
        <h1 class="text-3xl font-bold text-slate-800 tracking-tight">
          Registrasi Anak
        </h1>

        <p class="text-sm text-slate-500 mt-1">
          Kelola data registrasi, kedatangan, dan antrian terapi anak
        </p>
      </div>

      <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative">
          <input
            type="date"
            v-model="filterTanggal"
            class="pl-10 pr-3 py-2.5 rounded-xl border border-slate-200
                  focus:ring-2 focus:ring-indigo-400 bg-white shadow-sm"
            @change="load"
          />
          <i class="pi pi-calendar absolute left-3 top-3 text-slate-400"></i>
        </div>

        <button
          @click="openAdd"
          class="bg-gradient-to-r from-indigo-600 to-violet-600
                hover:from-indigo-700 hover:to-violet-700
                text-white px-5 py-2.5 rounded-xl
                flex items-center justify-center gap-2
                shadow-lg shadow-indigo-200 transition-all duration-200"
        >
          <i class="pi pi-plus" />
          Pendaftaran Baru
        </button>
      </div>
    </div>

    <!-- STATISTICS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div
        class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm"
      >
        <p class="text-sm text-slate-500">Total Registrasi</p>
        <h3 class="text-3xl font-bold text-slate-800 mt-2">
          {{ data.length }}
        </h3>
      </div>

      <div
        class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5 shadow-sm"
      >
        <p class="text-sm text-emerald-600">Sudah Hadir</p>
        <h3 class="text-3xl font-bold text-emerald-700 mt-2">
          {{
            data.filter(x => x.status_kedatangan === 'sudah_datang').length
          }}
        </h3>
      </div>

      <div
        class="bg-rose-50 border border-rose-100 rounded-2xl p-5 shadow-sm"
      >
        <p class="text-sm text-rose-600">Tidak Hadir</p>
        <h3 class="text-3xl font-bold text-rose-700 mt-2">
          {{
            data.filter(x => x.status_kedatangan === 'tidak_hadir').length
          }}
        </h3>
      </div>
    </div>

    <!-- CARD -->
    <div
      class="bg-white/90 backdrop-blur-sm
            border border-slate-200
            shadow-[0_10px_40px_rgba(0,0,0,0.06)]
            rounded-3xl p-6"
    >
      <div class="overflow-hidden rounded-2xl border border-slate-200">
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-100/80">
              <tr>
                <th class="th text-center w-12">No</th>
                <th class="th">No Regis</th>
                <th class="th">ID Pasien</th>
                <th class="th">Nama Anak</th>
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
                class="group border-b border-slate-100
                      hover:bg-indigo-50/40 transition-all duration-200"
              >
                <td class="td text-center">
                  {{ index + 1 }}
                </td>

                <td class="td font-semibold text-indigo-600">
                  {{ item.no_regis }}
                </td>

                <td class="td">
                  {{ item.id_pasien }}
                </td>

                <td class="td">
                  <div class="flex items-center gap-3">
                    <div
                      class="w-10 h-10 rounded-full bg-indigo-100
                            flex items-center justify-center
                            text-indigo-600 font-bold"
                    >
                      {{
                        item.profile_anak?.nama_anak?.charAt(0) || 'A'
                      }}
                    </div>

                    <div>
                      <p class="font-medium text-slate-800">
                        {{ item.profile_anak?.nama_anak || '-' }}
                      </p>
                    </div>
                  </div>
                </td>

                <td class="td">
                  {{ item.tgl_regis }}
                </td>

                <td class="td">
                  {{ item.terapis?.nama || '-' }}
                </td>

                <td class="td">
                  <span
                    class="px-3 py-1 rounded-full text-xs
                          bg-slate-100 text-slate-700"
                  >
                    {{ item.ruangan?.ruangan || '-' }}
                  </span>
                </td>

                <!-- STATUS -->
                <td class="td text-center">
                  <span
                    class="inline-flex items-center gap-2
                          px-4 py-2 rounded-full
                          text-xs font-semibold"
                    :class="{
                      'bg-emerald-100 text-emerald-700':
                        item.status_kedatangan === 'sudah_datang',

                      'bg-rose-100 text-rose-700':
                        item.status_kedatangan === 'tidak_hadir',

                      'bg-slate-100 text-slate-600':
                        item.status_kedatangan === 'belum_datang'
                    }"
                  >
                    <span
                      class="w-2 h-2 rounded-full"
                      :class="{
                        'bg-emerald-500':
                          item.status_kedatangan === 'sudah_datang',

                        'bg-rose-500':
                          item.status_kedatangan === 'tidak_hadir',

                        'bg-slate-400':
                          item.status_kedatangan === 'belum_datang'
                      }"
                    ></span>

                    {{ item.status_kedatangan.replace('_', ' ') }}
                  </span>
                </td>

                <!-- ACTION -->
                <td class="td">
                  <div class="flex justify-center gap-2">
                    <!-- PRINT -->
                    <button
                      class="action-btn"
                      title="Cetak"
                      @click="cetak(item)"
                    >
                      <i class="pi pi-print"></i>
                    </button>

                    <!-- PRINT ANTRIAN -->
                    <button
                      class="action-btn"
                      title="Cetak Antrian"
                      @click="cetakAntrian(item)"
                    >
                      <i class="pi pi-ticket"></i>
                    </button>

                    <!-- HADIR -->
                    <button
                      v-if="item.status_kedatangan !== 'sudah_datang'"
                      @click="updateKedatangan(item, 'sudah_datang')"
                      class="w-10 h-10 rounded-xl bg-emerald-500
                            hover:bg-emerald-600 text-white
                            flex items-center justify-center
                            shadow-sm transition-all duration-200
                            hover:scale-105"
                    >
                      ✔
                    </button>

                    <!-- TIDAK HADIR -->
                    <button
                      v-if="item.status_kedatangan !== 'tidak_hadir'"
                      @click="updateKedatangan(item, 'tidak_hadir')"
                      class="w-10 h-10 rounded-xl bg-rose-500
                            hover:bg-rose-600 text-white
                            flex items-center justify-center
                            shadow-sm transition-all duration-200
                            hover:scale-105"
                    >
                      ✖
                    </button>
                  </div>
                </td>
              </tr>

              <!-- EMPTY -->
              <tr v-if="data.length === 0">
                <td colspan="9" class="py-16">
                  <div
                    class="flex flex-col items-center justify-center text-center"
                  >
                    <div
                      class="w-20 h-20 rounded-full bg-slate-100
                            flex items-center justify-center mb-4"
                    >
                      <i class="pi pi-inbox text-3xl text-slate-400"></i>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-700">
                      Belum Ada Registrasi
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                      Belum ada data registrasi pada tanggal ini
                    </p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL -->
    <div
      v-if="dialog"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
      <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

      <div
        class="relative bg-white w-full max-w-5xl rounded-3xl shadow-2xl p-8 animate-fadeIn"
      >
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-8">
          <div>
            <h3 class="text-2xl font-bold text-slate-800">
              Form Registrasi Anak
            </h3>

            <p class="text-sm text-slate-500 mt-1">
              Lengkapi data registrasi dan kondisi anak
            </p>
          </div>

          <button
            @click="dialog = false"
            class="w-10 h-10 rounded-xl hover:bg-slate-100 transition"
          >
            ✕
          </button>
        </div>

        <!-- DATA REGISTRASI -->
        <div class="mb-8">
          <h4 class="section-title">
            Data Registrasi
          </h4>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- ANAK -->
            <div>
              <label class="label">
                {{ isOrtu ? 'Nama Anak' : 'Pilih Anak' }}
              </label>

              <input
                v-if="isOrtu"
                type="text"
                class="input bg-slate-100"
                :value="anak[0]?.nama_anak || '-'"
                readonly
              />

              <select
                v-else
                v-model="form.profile_anak_id"
                class="input"
              >
                <option value="">-- Pilih Anak --</option>

                <option
                  v-for="a in anak"
                  :key="a.id"
                  :value="a.id"
                >
                  {{ a.nama_anak }}
                </option>
              </select>
            </div>

            <!-- TANGGAL -->
            <div>
              <label class="label">
                Tanggal Registrasi
              </label>

              <input
                type="date"
                v-model="form.tgl_regis"
                class="input"
              />

              <p class="helper">
                Terapis difilter sesuai tanggal
              </p>
            </div>

            <!-- TERAPIS -->
            <div>
              <label class="label">
                Terapis
              </label>

              <select
                v-model="form.terapis_id"
                class="input"
                :disabled="!form.tgl_regis"
              >
                <option value="">
                  {{
                    form.tgl_regis
                      ? 'Pilih Terapis'
                      : 'Pilih tanggal dahulu'
                  }}
                </option>

                <option
                  v-for="t in terapis"
                  :key="t.id"
                  :value="t.id"
                >
                  {{ t.nama }}
                </option>
              </select>
            </div>

            <!-- RUANGAN -->
            <div>
              <label class="label">
                Ruangan
              </label>

              <select
                v-model="form.ruangan_id"
                class="input"
              >
                <option value="">-- Pilih Ruangan --</option>

                <option
                  v-for="r in ruangan"
                  :key="r.id"
                  :value="r.id"
                >
                  {{ r.ruangan }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- DATA ORANG TUA -->
        <div>
          <h4 class="section-title">
            Data Orang Tua & Kondisi Anak
          </h4>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="label">Nama Ayah</label>
              <input v-model="form.nama_ayah" class="input" />
            </div>

            <div>
              <label class="label">Nama Ibu</label>
              <input v-model="form.nama_ibu" class="input" />
            </div>

            <div>
              <label class="label">No Telepon</label>
              <input v-model="form.notelp" class="input" />
            </div>

            <div>
              <label class="label">Usia Saat Menikah</label>
              <input
                type="number"
                v-model="form.usia_saat_menikah"
                class="input"
              />
            </div>

            <div class="md:col-span-2">
              <label class="label">Alamat</label>

              <textarea
                v-model="form.alamat"
                rows="3"
                class="input"
              ></textarea>
            </div>

            <div class="md:col-span-2">
              <label class="label">Keluhan Saat Ini</label>

              <textarea
                v-model="form.keluhan_saat_ini"
                rows="3"
                class="input"
              ></textarea>
            </div>

            <div class="md:col-span-2">
              <label class="label">Kemampuan Saat Ini</label>

              <textarea
                v-model="form.kemampuan_saat_ini"
                rows="3"
                class="input"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- FOOTER -->
        <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
          <button
            @click="dialog = false"
            class="px-5 py-3 rounded-2xl bg-slate-100
                  hover:bg-slate-200 transition"
          >
            Batal
          </button>

          <button
            @click="save"
            class="px-6 py-3 rounded-2xl
                  bg-gradient-to-r from-indigo-600 to-violet-600
                  hover:from-indigo-700 hover:to-violet-700
                  text-white shadow-lg shadow-indigo-200
                  transition-all duration-200"
          >
            Simpan Registrasi
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
const user = ref(null)

const filterTanggal = ref(
  new Date().toISOString().slice(0, 10)
)

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

    allData = allData.filter(item =>
      anakIds.includes(Number(item.profile_anak_id))
    )
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

  const res = await api.get('/terapis-by-tanggal', {
    params: { tanggal: val }
  })

  terapis.value = res.data?.data ?? []
})

watch(() => form.value.terapis_id, id => {
  if (!id) {
    ruangan.value = []
    form.value.ruangan_id = ''
    return
  }

  const t = terapis.value.find(
    x => Number(x.id) === Number(id)
  )

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

  dialog.value = true
}

async function save() {
  await api.post('/registrasi-anak', form.value)

  toast.success('Registrasi berhasil')

  dialog.value = false

  load()
}

function cetak(item) {
  const baseUrl = import.meta.env.VITE_API_BASE_URL

  window.open(
    `${baseUrl}/cetak/registrasi-anak/${item.id}`,
    '_blank'
  )
}

function cetakAntrian(item) {
  const baseUrl = import.meta.env.VITE_API_BASE_URL

  window.open(
    `${baseUrl}/cetak/antrian/${item.id}`,
    '_blank'
  )
}

async function updateKedatangan(item, status) {
  try {
    const res = await api.put(
      `/registrasi-anak/${item.id}/kedatangan`,
      {
        status_kedatangan: status
      }
    )

    item.status_kedatangan =
      res.data.data.status_kedatangan

    item.waktu_kedatangan =
      res.data.data.waktu_kedatangan

    toast.success('Status kedatangan diperbarui')
  } catch (err) {
    console.error(err)
    toast.error('Gagal update status')
  }
}
</script>

<style scoped>
.th {
  @apply px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-600;
}

.td {
  @apply px-5 py-4 text-slate-700 align-middle;
}

.label {
  @apply text-sm font-semibold text-slate-700 mb-2 block;
}

.helper {
  @apply text-xs text-slate-500 mt-1;
}

.section-title {
  @apply text-lg font-bold text-indigo-700 mb-5;
}

.input {
  @apply w-full rounded-2xl border border-slate-200
         bg-white px-4 py-3 text-sm
         shadow-sm transition-all duration-200
         focus:ring-4 focus:ring-indigo-100
         focus:border-indigo-500
         focus:outline-none;
}

.action-btn {
  @apply w-10 h-10 rounded-xl
         border border-slate-200
         bg-white hover:bg-indigo-50
         text-slate-600 hover:text-indigo-600
         flex items-center justify-center
         transition-all duration-200
         hover:scale-105;
}

.animate-fadeIn {
  animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.96);
  }

  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>