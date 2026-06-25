<template>
  <div class="min-h-screen bg-gray-50 p-6 space-y-10">

    <!-- HEADER -->
    <div>
      <h1 class="text-3xl font-bold text-gray-800">Dashboard Orang Tua</h1>
      <p class="text-gray-500 mt-1">Ringkasan informasi anak & jadwal terapi</p>
    </div>

    <!-- ================= INFORMASI ANAK ================= -->
    <section>
      <h2 class="text-xl font-semibold text-gray-700 mb-4">Informasi Anak</h2>

      <!-- JIKA ADA ANAK -->
      <div v-if="profileAnak" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

        <!-- NAMA ANAK -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
            <i class="pi pi-user text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-500">Nama Anak</p>
            <p class="text-lg font-bold text-gray-800">{{ profileAnak.nama_anak }}</p>
            <span
              class="inline-flex items-center gap-1.5 mt-1 px-3 py-1 rounded-full text-xs font-semibold"
              :class="profileAnak.status_aktif ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'"
            >
              <span class="w-1.5 h-1.5 rounded-full" :class="profileAnak.status_aktif ? 'bg-emerald-500' : 'bg-rose-500'"></span>
              {{ profileAnak.status_aktif ? 'AKTIF' : 'NONAKTIF' }}
            </span>
          </div>
        </div>

        <!-- TANGGAL LAHIR -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
            <i class="pi pi-calendar text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-500">Tanggal Lahir</p>
            <p class="text-lg font-bold text-gray-800">
              {{ formatTanggal(profileAnak.tanggal_lahir) }}
            </p>
          </div>
        </div>

        <!-- UMUR -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
            <i class="pi pi-clock text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-500">Umur</p>
            <p class="text-lg font-bold text-gray-800">{{ profileAnak.umur }} Tahun</p>
          </div>
        </div>

      </div>

      <!-- JIKA BELUM ADA ANAK -->
      <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center mb-4">
          <i class="pi pi-user text-2xl text-indigo-400"></i>
        </div>
        <p class="text-lg font-semibold text-gray-700 mb-2">Belum ada data anak</p>
        <p class="text-sm text-gray-500 mb-6">Silakan input data anak terlebih dahulu</p>

        <button
          @click="openAdd"
          class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl transition flex items-center gap-2"
        >
          <i class="pi pi-plus text-xs"></i>
          Tambah Anak
        </button>
      </div>
    </section>

    <!-- ================= JADWAL TERAPI ================= -->
    <section>
      <h2 class="text-xl font-semibold text-gray-700 mb-4">Jadwal Terapi</h2>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="px-4 py-3 text-center font-semibold">No</th>
              <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
              <th class="px-4 py-3 text-left font-semibold">Layanan</th>
              <th class="px-4 py-3 text-left font-semibold">Terapis</th>
              <th class="px-4 py-3 text-center font-semibold">Status</th>
              <th class="px-4 py-3 text-center font-semibold">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(j, i) in jadwalTerapi" :key="i" class="border-t border-gray-100 hover:bg-indigo-50/40 transition">
              <td class="px-4 py-3 text-center font-medium text-gray-500">{{ i + 1 }}</td>
              <td class="px-4 py-3 text-gray-700">{{ formatTanggal(j.tanggal) }}</td>
              <td class="px-4 py-3 font-medium text-gray-800">{{ j.layanan }}</td>
              <td class="px-4 py-3 text-gray-700">{{ j.terapis }}</td>
              <td class="px-4 py-3 text-center">
                <span
                  class="px-3 py-1 rounded-full text-xs font-semibold"
                  :class="
                    j.status === 'terjadwal'
                      ? 'bg-indigo-50 text-indigo-700'
                      : j.status === 'pending_reschedule'
                        ? 'bg-amber-50 text-amber-700'
                        : 'bg-emerald-50 text-emerald-700'
                  "
                >
                  {{ j.status.toUpperCase() }}
                </span>
              </td>

              <td class="px-4 py-3 text-center">
                <button
                  v-if="j.status === 'terjadwal'"
                  @click="openReschedule(j)"
                  class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-medium hover:bg-indigo-700 transition"
                >
                  Reschedule
                </button>

                <span v-else-if="j.status === 'pending_reschedule'" class="text-xs text-amber-600 font-semibold">
                  Menunggu Konfirmasi
                </span>
              </td>
            </tr>

            <tr v-if="jadwalTerapi.length === 0">
              <td colspan="6" class="py-10 text-center">
                <div class="flex flex-col items-center">
                  <i class="pi pi-calendar text-2xl text-gray-300 mb-2"></i>
                  <p class="text-gray-400 text-sm">Belum ada jadwal terapi</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- ================= MODAL RESCHEDULE ================= -->
    <transition name="fade">
      <div
        v-if="dialogReschedule"
        class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      >
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-xl">

          <!-- HEADER -->
          <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Reschedule Terapi</h3>
            <button
              @click="dialogReschedule=false"
              class="w-8 h-8 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition flex items-center justify-center"
              aria-label="Tutup"
            >
              <i class="pi pi-times text-sm"></i>
            </button>
          </div>

          <!-- BODY -->
          <div class="p-6 space-y-4">
            <div>
              <label class="label">Tanggal Baru</label>
              <input
                type="date"
                v-model="formReschedule.tanggal_baru"
                class="input"
              />
            </div>

            <div>
              <label class="label">Catatan (opsional)</label>
              <textarea
                v-model="formReschedule.catatan"
                rows="3"
                class="input"
              ></textarea>
            </div>
          </div>

          <!-- FOOTER -->
          <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
            <button class="btn-secondary" @click="dialogReschedule=false">
              Batal
            </button>
            <button class="btn-primary" @click="submitReschedule">
              Kirim
            </button>
          </div>

        </div>
      </div>
    </transition>


    <!-- ================= MODAL TAMBAH ANAK ================= -->
    <transition name="fade">
      <div v-if="dialog" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-6xl shadow-xl relative">

          <!-- HEADER -->
          <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Tambah Anak</h3>
            <button
              @click="dialog=false"
              class="w-8 h-8 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition flex items-center justify-center"
              aria-label="Tutup"
            >
              <i class="pi pi-times text-sm"></i>
            </button>
          </div>

          <!-- BODY -->
          <div class="p-6 max-h-[75vh] overflow-y-auto">
            <form class="grid grid-cols-1 md:grid-cols-2 gap-5">

              <div>
                <label class="label">Nama Anak</label>
                <input v-model="form.nama_anak" class="input" />
              </div>

              <!-- ORANG TUA AUTO DARI LOGIN -->
              <div>
                <label class="label">Orang Tua</label>
                <input
                  type="text"
                  class="input bg-gray-50 cursor-not-allowed text-gray-500"
                  :value="user?.nama || ''"
                  disabled
                />
              </div>

              <div>
                <label class="label">Jenis Kelamin</label>
                <select v-model="form.jenis_kelamin_id" class="input">
                  <option :value="null">Pilih</option>
                  <option v-for="jk in jenisKelamin" :key="jk.id" :value="jk.id">
                    {{ jk.nama }}
                  </option>
                </select>
              </div>

              <div>
                <label class="label">Agama</label>
                <select v-model="form.agama_id" class="input">
                  <option :value="null">Pilih</option>
                  <option v-for="ag in agama" :key="ag.id" :value="ag.id">
                    {{ ag.nama }}
                  </option>
                </select>
              </div>

              <div>
                <label class="label">NIK</label>
                <input v-model="form.NIK" class="input" />
              </div>

              <div>
                <label class="label">Umur</label>
                <input
                  type="text"
                  :value="form.umur !== null ? form.umur + ' th' : ''"
                  class="input bg-gray-50 cursor-not-allowed text-gray-500"
                  readonly
                />
              </div>

              <div class="md:col-span-2">
                <label class="label">Alamat</label>
                <textarea v-model="form.alamat" rows="2" class="input"></textarea>
              </div>

              <div>
                <label class="label">Tanggal Lahir</label>
                <input type="date" v-model="form.tanggal_lahir" class="input" />
              </div>

              <div>
                <label class="label">Tempat Lahir</label>
                <input v-model="form.tempat_lahir" class="input" />
              </div>

              <div>
                <label class="label">No HP Orang Tua</label>
                <input v-model="form.no_hp_orang_tua" class="input" />
              </div>

              <div>
                <label class="label">Status</label>
                <select v-model="form.status_aktif" class="input">
                  <option :value="true">Aktif</option>
                  <option :value="false">Nonaktif</option>
                </select>
              </div>

            </form>
          </div>

          <!-- FOOTER -->
          <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
            <button class="btn-secondary" @click="dialog=false">Batal</button>
            <button class="btn-primary" @click="save">Simpan</button>
          </div>

        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'
import { watch } from 'vue'


const formatTanggal = (dateStr) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}/${month}/${year}`
}
function hitungUmur(tanggal) {
  if (!tanggal) return null

  const today = new Date()
  const birth = new Date(tanggal)

  let umur = today.getFullYear() - birth.getFullYear()
  const m = today.getMonth() - birth.getMonth()

  if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
    umur--
  }

  return umur
}
const toast = useToast()

const profileAnak = ref(null)
const jadwalTerapi = ref([])

const dialog = ref(false)
const dialogReschedule = ref(false)

const jenisKelamin = ref([])
const agama = ref([])

const selectedJadwal = ref(null)

const formReschedule = ref({
  tanggal_baru: '',
  catatan: ''
})

const user = ref(null)

const formDefault = {
  nama_anak: '',
  id_orang_tua: null,
  jenis_kelamin_id: null,
  agama_id: null,
  NIK: '',
  alamat: '',
  umur: null,
  tanggal_lahir: '',
  tempat_lahir: '',
  no_hp_orang_tua: '',
  status_aktif: true,
}

const form = ref({ ...formDefault })


const openReschedule = (jadwal) => {
  selectedJadwal.value = jadwal
  formReschedule.value = {
    tanggal_baru: '',
    catatan: ''
  }
  dialogReschedule.value = true
}

const submitReschedule = async () => {
  try {
    if (!formReschedule.value.tanggal_baru) {
      toast.error('Tanggal baru wajib diisi')
      return
    }

    await api.post(
      `/pelayanan-terapi-anak/${selectedJadwal.value.id}/reschedule`,
      {
        tanggal_baru: formReschedule.value.tanggal_baru
      }
    )

    toast.success('Permintaan reschedule berhasil dikirim')
    dialogReschedule.value = false

    await loadJadwal()

  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal mengajukan reschedule')
    console.error(err)
  }
}

const loadJadwal = async () => {
  const pembayaranRes = await api.get('/riwayat-pembayaran-anak')
  const jadwal = []

  pembayaranRes.data.forEach(p => {
    p.registrasi?.pelayanans?.forEach(pl => {
      jadwal.push({
        id: pl.id,
        tanggal: pl.tanggal_penjadwalan,
        layanan: pl.layanan?.layanan || '-',
        terapis: pl.terapis?.nama || '-',
        status: pl.status
      })
    })
  })

  jadwalTerapi.value = jadwal.sort(
    (a, b) => new Date(a.tanggal) - new Date(b.tanggal)
  )
}

onMounted(async () => {
  try {
    const u = localStorage.getItem('user')
    user.value = u ? JSON.parse(u) : null

    if (!user.value?.user_profile_id) {
      toast.error('Orang tua tidak terdeteksi, silakan login ulang')
      return
    }

    const anakRes = await api.get('/profile-anak-saya')
    profileAnak.value = anakRes.data.length ? anakRes.data[0] : null

    jenisKelamin.value = (await api.get('/jenis-kelamin')).data
    agama.value = (await api.get('/agama')).data

    await loadJadwal()

  } catch (err) {
    toast.error('Gagal memuat data')
    console.error(err)
  }
})

/* ================= TAMBAH ANAK ================= */

const openAdd = () => {
  form.value = { ...formDefault }
  form.value.id_orang_tua = user.value?.user_profile_id
  dialog.value = true
}

const save = async () => {
  try {
    if (!form.value.id_orang_tua) {
      toast.error('Orang tua tidak terdeteksi')
      return
    }

    await api.post('/profile-anak', form.value)

    toast.success('Data anak berhasil disimpan')
    dialog.value = false

    const anakRes = await api.get('/profile-anak-saya')
    profileAnak.value = anakRes.data.length ? anakRes.data[0] : null

  } catch (err) {
    toast.error('Gagal menyimpan data anak')
    console.error(err)
  }
}

watch(
  () => form.value.tanggal_lahir,
  (val) => {
    const hasil = hitungUmur(val)
    form.value.umur = hasil
  }
)
</script>


<style scoped>
.label {
  @apply text-sm font-medium text-gray-700 mb-1.5 block;
}
.input {
  @apply w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm transition focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500;
}
.btn-primary {
  @apply bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition;
}
.btn-secondary {
  @apply bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl font-semibold transition;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>