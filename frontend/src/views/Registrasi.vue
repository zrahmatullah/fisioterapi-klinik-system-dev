<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Registrasi Anak</h2>

    <div class="bg-white shadow-xl rounded-2xl p-6">
      <div class="flex justify-between items-center mb-6">
        <button
          @click="openAdd"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl flex items-center gap-2 shadow"
        >
          <i class="pi pi-plus" />
          Pendaftaran Baru 
        </button>

        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600">Filter Tanggal:</label>
          <input
            type="date"
            v-model="filterTanggal"
            class="border rounded-lg px-3 py-2"
            @change="load"
          />
        </div>
      </div>
      <div class="overflow-x-auto">
      <table class="min-w-full text-sm border-separate border-spacing-y-3">

        <thead class="bg-slate-50 sticky top-0 shadow-sm">
          <tr>
            <th class="th text-center w-12">No</th>
            <th class="th w-[170px]">No Regis</th>
            <th class="th w-[120px]">ID Pasien</th>
            <th class="th w-[220px]">Nama Anak</th>
            <th class="th w-[130px]">Tanggal</th>
            <th class="th w-[160px]">Terapis</th>
            <th class="th w-[120px]">Ruangan</th>
            <th class="th text-center w-[160px]">Kedatangan</th>
            <th class="th text-center w-[140px]">Aksi</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(item,index) in data"
            :key="item.id"
            class="bg-white rounded-xl shadow-sm
                  hover:shadow-md hover:bg-indigo-50/30 transition"
          >
            <td class="td text-center">{{ index + 1 }}</td>
            <td class="td font-medium text-indigo-600">{{ item.no_regis }}</td>
            <td class="td">{{ item.id_pasien }}</td>
            <td class="td truncate">{{ item.profile_anak?.nama_anak || '-' }}</td>
            <td class="td">{{ item.tgl_regis }}</td>
            <td class="td">{{ item.terapis?.nama || '-' }}</td>
            <td class="td">{{ item.ruangan?.ruangan || '-' }}</td>

            <!-- BADGE -->
            <td class="td text-center">
              <span
                class="inline-flex flex-col items-center justify-center
                      min-w-[120px] px-4 py-1.5 rounded-full
                      text-[11px] font-bold shadow-sm"
                :class="{
                  'bg-gradient-to-r from-green-500 to-emerald-500 text-white':
                    item.status_kedatangan === 'sudah_datang',
                  'bg-gradient-to-r from-red-500 to-rose-500 text-white':
                    item.status_kedatangan === 'tidak_hadir',
                  'bg-slate-200 text-slate-700':
                    item.status_kedatangan === 'belum_datang'
                }"
              >
                <span class="uppercase">
                  {{ item.status_kedatangan.replace('_',' ') }}
                </span>
                <span v-if="item.waktu_kedatangan" class="text-[10px] opacity-90">
                  {{ new Date(item.waktu_kedatangan).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}) }}
                </span>
              </span>
            </td>

            <!-- AKSI -->
            <td class="td text-center">
              <div class="flex justify-center items-center gap-2">

                <!-- PRINT -->
                <button
                  class="w-9 h-9 flex items-center justify-center
                        rounded-full border border-indigo-200
                        text-indigo-600 hover:bg-indigo-100 transition"
                  title="Cetak"
                  @click="cetak(item)"
                >
                  <i class="pi pi-print text-sm"></i>
                </button>

                <button
                  class="w-9 h-9 flex items-center justify-center
                        rounded-full border border-indigo-200
                        text-indigo-600 hover:bg-indigo-100 transition"
                  title="Cetak Antrian"
                  @click="cetakAntrian(item)"
                >
                  <i class="pi pi-print text-sm"></i>
                </button>

                <!-- HADIR -->
                <button
                  v-if="item.status_kedatangan !== 'sudah_datang'"
                  @click="updateKedatangan(item,'sudah_datang')"
                  class="w-9 h-9 flex items-center justify-center
                        rounded-full bg-green-500 text-white
                        hover:bg-green-600 shadow"
                  title="Hadir"
                >
                  ✔
                </button>

                <!-- TIDAK -->
                <button
                  v-if="item.status_kedatangan !== 'tidak_hadir'"
                  @click="updateKedatangan(item,'tidak_hadir')"
                  class="w-9 h-9 flex items-center justify-center
                        rounded-full bg-red-500 text-white
                        hover:bg-red-600 shadow"
                  title="Tidak Hadir"
                >
                  ✖
                </button>

              </div>
            </td>
          </tr>

          <tr v-if="data.length === 0">
            <td colspan="9" class="py-10 text-center text-gray-400">
              Belum ada registrasi anak untuk tanggal ini. Silakan pilih tanggal lain atau buat registrasi baru.
            </td>
          </tr>
        </tbody>
      </table>
</div>



    </div>

    <!-- MODAL -->
    <div v-if="dialog" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/50"></div>

      <div class="relative bg-white w-full max-w-3xl rounded-2xl shadow-2xl p-6">
        <div class="flex justify-between items-center mb-5">
          <h3 class="text-xl font-semibold text-gray-800">
            Form Registrasi Anak
          </h3>
          <button
            @click="dialog = false"
            class="text-gray-400 hover:text-gray-600 text-xl"
          >
            ✕
          </button>
        </div>

        <!-- DATA REGISTRASI -->
        <div class="mb-6">
          <h4 class="section-title">Data Registrasi</h4>

          <div class="grid grid-cols-2 gap-4">
          <!-- <div>
            <label class="label">
              {{ isOrtu ? 'Nama Anak' : 'Pilih Anak' }}
            </label>

            <select
              v-model="form.profile_anak_id"
              class="input"
              :disabled="isOrtu"
            >
              <option v-if="!isOrtu" value="">-- Pilih Anak --</option>

              <option
                v-for="a in anak"
                :key="a.id"
                :value="a.id"
              >
                {{ a.nama_anak }}
              </option>
            </select>
          </div> -->
          <div>
            <label class="label">
              {{ isOrtu ? 'Nama Anak' : 'Pilih Anak' }}
            </label>
            <input
              v-if="isOrtu"
              type="text"
              class="input bg-gray-100 cursor-not-allowed"
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

            <div>
              <label class="label">Tanggal Registrasi</label>
              <input type="date" v-model="form.tgl_regis" class="input" />
              <p class="helper">Terapis difilter sesuai tanggal</p>
            </div>

            <div>
              <label class="label">Terapis</label>
              <select
                v-model="form.terapis_id"
                class="input"
                :disabled="!form.tgl_regis"
              >
                <option value="">
                  {{ form.tgl_regis ? 'Pilih Terapis' : 'Pilih tanggal dahulu' }}
                </option>
                <option v-for="t in terapis" :key="t.id" :value="t.id">
                  {{ t.nama }}
                </option>
              </select>
            </div>

            <div>
              <label class="label">Ruangan</label>
              <select v-model="form.ruangan_id" class="input">
                <option value="">-- Pilih Ruangan --</option>
                <option v-for="r in ruangan" :key="r.id" :value="r.id">
                  {{ r.ruangan }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- DATA ORANG TUA -->
        <div>
          <h4 class="section-title">Data Orang Tua & Kondisi Anak</h4>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="label">Nama Ayah</label>
              <input v-model="form.nama_ayah" class="input" />
            </div>

            <div>
              <label class="label">Nama Ibu</label>
              <input v-model="form.nama_ibu" class="input" />
            </div>

            <div>
              <label class="label">No. Telepon</label>
              <input v-model="form.notelp" class="input" />
            </div>

            <div>
              <label class="label">Usia Saat Menikah</label>
              <input type="number" v-model="form.usia_saat_menikah" class="input" />
            </div>

            <div class="col-span-2">
              <label class="label">Alamat</label>
              <textarea v-model="form.alamat" rows="2" class="input"></textarea>
            </div>

            <div class="col-span-2">
              <label class="label">Keluhan Saat Ini</label>
              <textarea v-model="form.keluhan_saat_ini" rows="2" class="input"></textarea>
            </div>

            <div class="col-span-2">
              <label class="label">Kemampuan Saat Ini</label>
              <textarea v-model="form.kemampuan_saat_ini" rows="2" class="input"></textarea>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
          <button
            @click="dialog = false"
            class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200"
          >
            Batal
          </button>
          <button
            @click="save"
            class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow"
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


// async function load() {
//   // =========================
//   // DATA ANAK (DROPDOWN)
//   // =========================
//   if (isOrtu.value) {
//     const anakSayaRes = await api.get('/profile-anak-saya')
//     anak.value = anakSayaRes.data ?? []
//   } else {
//     const anakRes = await api.get('/profile-anak')
//     anak.value = anakRes.data ?? []
//   }

//   // =========================
//   // DATA REGISTRASI
//   // =========================
//   const r1 = await api.get('/registrasi-anak')
//   let allData = r1.data ?? []

//   if (isOrtu.value) {
//     const anakIds = anak.value.map(a => Number(a.id))
//     allData = allData.filter(item =>
//       anakIds.includes(Number(item.profile_anak_id))
//     )
//   }

//   data.value = allData

//   // =========================
//   // RUANGAN
//   // =========================
//   ruangan.value = (await api.get('/ruangan')).data ?? []
// }

const filterTanggal = ref('')


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

  const tanggalFilter = filterTanggal.value || new Date().toISOString().slice(0, 10)

  allData = allData.filter(item => {
    const tgl = item.tgl_regis?.slice(0, 10)
    return tgl === tanggalFilter
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

watch(() => form.value.tgl_regis, async (val) => {
  if (!val) {
    terapis.value = []
    form.value.terapis_id = ''
    return
  }

  const res = await api.get('/terapis-by-tanggal', { params: { tanggal: val } })
  terapis.value = res.data?.data ?? []
})

watch(() => form.value.terapis_id, (id) => {
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
  Object.keys(form.value).forEach(k => form.value[k] = '')

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

async function remove(item) {
  if (!confirm('Hapus registrasi ini?')) return
  await api.delete(`/registrasi-anak/${item.id}`)
  toast.success('Registrasi dihapus')
  load()
}

function cetak(item) {
  const baseUrl = import.meta.env.VITE_API_BASE_URL
  window.open(`${baseUrl}/cetak/registrasi-anak/${item.id}`, '_blank')
}

async function cetakAntrian(item) {
  const baseUrl = import.meta.env.VITE_API_BASE_URL
  window.open(`${baseUrl}/cetak/antrian/${item.id}`, '_blank')
}

async function updateKedatangan(item, status) {
  try {
    const res = await api.put(`/registrasi-anak/${item.id}/kedatangan`, {
      status_kedatangan: status
    })

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
  @apply px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase;
}

.td {
  @apply px-4 py-3 text-gray-700;
}

.label {
  @apply text-sm font-medium text-gray-700 mb-1 block;
}

.helper {
  @apply text-xs text-gray-500 mt-1;
}

.section-title {
  @apply text-sm font-semibold text-indigo-600 mb-3;
}

.input {
  @apply w-full border border-gray-300 rounded-xl px-3 py-2
         focus:ring-2 focus:ring-indigo-400 focus:outline-none;
}

.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.25s ease;
}
.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.85);
}

</style>
