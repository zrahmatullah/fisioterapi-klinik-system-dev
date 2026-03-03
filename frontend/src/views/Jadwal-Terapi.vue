<template>
  <div class="p-6 space-y-6">
    <!-- HEADER + SEARCH -->
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-800">
        Catatan Aktivitas Sesi Terapi Anak
      </h1>
      
      <div class="relative w-72">

        
      </div>
    </div>
    
    <div class="flex items-center justify-between gap-4">

<!-- FILTER STATUS -->
<div class="flex items-center gap-3">
  <label class="text-sm text-gray-600">Status:</label>

  <select
    v-model="filterStatus"
    class="px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400"
  >
    <option value="">Semua</option>
    <option value="sudah">Sudah Dicatat</option>
    <option value="belum">Belum Dicatat</option>
  </select>
</div>

<!-- SEARCH -->
<div class="relative w-64">
  <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
  <input
    v-model="search"
    placeholder="Cari nama anak..."
    class="pl-10 pr-3 py-2 border rounded-xl w-full text-sm
           focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400"
  />
</div>

</div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 overflow-x-auto">
      <table class="w-full text-sm table-fixed">
        <thead class="bg-gray-100 text-gray-600">
          <tr>
            <th class="px-3 py-3 text-center w-12">No</th>
             <th class="px-4 py-3 text-center w-44">Tanggal</th>
            <th class="px-4 py-3 text-left w-40">Nama Anak</th>
            <th class="px-4 py-3 text-left w-36">Orang Tua</th>
            <th class="px-4 py-3 text-center w-28">Ruangan</th>
            <th class="px-4 py-3 text-left w-52">Layanan</th>
            <th class="px-4 py-3 text-center w-28">Status</th>
            <th class="px-4 py-3 text-center w-24">Aksi</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          <tr
            v-for="(item, index) in paginatedData"
            :key="item.id"
            class="hover:bg-indigo-50/40 transition"
          >

            <td class="px-3 py-3 text-center font-medium">
              {{ (page - 1) * perPage + index + 1 }}
            </td>
            <td class="px-4 py-3 text-center whitespace-nowrap">
              {{ formatTanggal(item.tanggal_penjadwalan) }}
            </td>

            <td class="px-4 py-3 font-medium truncate">
              {{ item.registrasi_anak?.profile_anak?.nama_anak || '-' }}
            </td>

            <!-- <td class="px-4 py-3 truncate">
              {{
                item.registrasi_anak?.profile_anak?.orang_tua?.nama
                || item.registrasi_anak?.profile_anak?.id_orang_tua
                || '-'
              }}
            </td> -->
            <td class="px-4 py-3 truncate">
              {{
                item.registrasi_anak?.nama_ayah
              }}
              /
              {{
                item.registrasi_anak?.nama_ibu
              }}
            </td>

            <td class="px-4 py-3 text-center">
              {{ item.registrasi_anak?.ruangan?.ruangan || '-' }}
            </td>

            <td class="px-4 py-3 truncate">
              {{ item.layanan?.layanan || '-' }}
            </td>

            <td class="px-4 py-3 text-center">
              <span
                class="inline-flex items-center justify-center min-w-[110px]
                       px-3 py-1 text-xs rounded-full font-medium"
                :class="item.catatan_aktivitas
                  ? 'bg-green-100 text-green-700'
                  : 'bg-yellow-100 text-yellow-700'"
              >
                {{ item.catatan_aktivitas ? 'Sudah Dicatat' : 'Belum Dicatat' }}
              </span>
            </td>

            <td class="px-4 py-3 text-center">
              <button
                @click="openForm(item)"
                class="inline-flex items-center justify-center min-w-[72px]
                       px-3 py-1 rounded-lg text-sm font-medium transition"
                :class="item.catatan_aktivitas
                  ? 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                  : 'bg-indigo-600 text-white hover:bg-indigo-700'"
              >
                {{ item.catatan_aktivitas ? 'Lihat' : 'Catat' }}
              </button>
            </td>
          </tr>

          <tr v-if="paginatedData.length === 0">
            <td colspan="8" class="py-12 text-center text-gray-400">
              Data tidak ditemukan
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    <div class="flex justify-between items-center text-sm text-gray-600">
      <span>
        Menampilkan <b>{{ paginatedData.length }}</b> dari
        <b>{{ filteredData.length }}</b> data
      </span>

      <div class="flex items-center gap-2">
        <button
          @click="page--"
          :disabled="page === 1"
          class="px-3 py-1 rounded-lg border disabled:opacity-40 hover:bg-gray-50"
        >
          Prev
        </button>

        <span class="px-3 py-1 font-medium">{{ page }}</span>

        <button
          @click="page++"
          :disabled="page >= totalPage"
          class="px-3 py-1 rounded-lg border disabled:opacity-40 hover:bg-gray-50"
        >
          Next
        </button>
      </div>
    </div>

    <!-- MODAL (ASLI, TIDAK DIUBAH) -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center"
    >
      <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b">
          <h2 class="text-lg font-bold">
            {{ viewOnly ? 'Detail Catatan Terapi' : 'Catatan Aktivitas Terapi' }}
          </h2>
          <button @click="closeModal" class="text-xl">✕</button>
        </div>

        <!-- INFO -->
        <div class="grid grid-cols-2 gap-4 p-6 bg-gray-50 text-sm border-b">
          <div>
            <p class="text-gray-500">Nama Anak</p>
            <p class="font-semibold">
              {{ selected?.registrasi_anak?.profile_anak?.nama_anak || '-' }}
            </p>
          </div>

          <div>
            <p class="text-gray-500">Orang Tua</p>
            <p class="font-semibold">
              {{ selected?.registrasi_anak?.nama_ayah || '-' }} /
              {{ selected?.registrasi_anak?.nama_ibu || '-' }}
            </p>
          </div>

          <div>
            <p class="text-gray-500">Terapis</p>
            <p class="font-semibold">
              {{ selected?.terapis?.nama || '-' }}
            </p>
          </div>

          <div>
            <p class="text-gray-500">Ruangan</p>
            <p class="font-semibold">
              {{ selected?.registrasi_anak?.ruangan?.ruangan || '-' }}
            </p>
          </div>

          <div>
            <p class="text-gray-500">Tanggal</p>
            <p class="font-semibold">
              {{ formatTanggal(selected?.tanggal_penjadwalan) }}
            </p>
          </div>

          <div>
            <p class="text-gray-500">Layanan</p>
            <p class="font-semibold">
              {{ selected?.layanan?.layanan || '-' }}
            </p>
          </div>
        </div>

        <!-- FORM -->
        <form @submit.prevent="submit" class="p-6 space-y-4">
          <label class="flex items-center gap-3 text-sm">
            <input type="checkbox" v-model="form.checkin_sesi" :disabled="viewOnly" />
            Check-in Sesi
          </label>

          <textarea
            v-model="form.aktivitas_terapi"
            :disabled="viewOnly"
            class="textarea"
            placeholder="Aktivitas Terapi"
          />

          <textarea
            v-model="form.keterangan_terapi"
            :disabled="viewOnly"
            class="textarea"
            placeholder="Keterangan Terapi"
          />

          <textarea
            v-model="form.tugas_rumah"
            :disabled="viewOnly"
            class="textarea"
            placeholder="Tugas Rumah"
          />

          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="closeModal" class="btn-secondary">
              Tutup
            </button>
            <button v-if="!viewOnly" type="submit" class="btn-primary">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useToast } from 'vue-toastification'
import api from '../axios'

const toast = useToast()

const filterStatus = ref('')
const sesiTerapi = ref([])
const showModal = ref(false)
const selected = ref(null)

const search = ref('')
const page = ref(1)
const perPage = 15

watch([search, filterStatus], () => (page.value = 1))

const form = reactive({
  registrasi_anak_id: null,
  pelayanan_terapi_anak_id: null,
  aktivitas_terapi: '',
  keterangan_terapi: '',
  tugas_rumah: '',
  checkin_sesi: false
})

const viewOnly = computed(() => !!selected.value?.catatan_aktivitas)

const filteredData = computed(() =>
  sesiTerapi.value.filter(item => {
    // filter nama (yang lama)
    const cocokNama =
      item.registrasi_anak?.profile_anak?.nama_anak
        ?.toLowerCase()
        .includes(search.value.toLowerCase())

    // filter status (tambahan)
    let cocokStatus = true
    if (filterStatus.value === 'sudah') {
      cocokStatus = !!item.catatan_aktivitas
    } else if (filterStatus.value === 'belum') {
      cocokStatus = !item.catatan_aktivitas
    }

    return cocokNama && cocokStatus
  })
)

const totalPage = computed(() =>
  Math.ceil(filteredData.value.length / perPage)
)

const paginatedData = computed(() => {
  const start = (page.value - 1) * perPage
  return filteredData.value.slice(start, start + perPage)
})

/* ====== HANYA INI YANG DIUBAH ====== */
const formatTanggal = (tgl) => {
  if (!tgl) return '-'
  const d = new Date(tgl)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}/${month}/${year}`
}

onMounted(async () => {
  const res = await api.get('/dashboard-terapis')
  sesiTerapi.value = res.data.flatMap(r =>
    (r.pelayanans || []).map(p => ({
      ...p,
      registrasi_anak: r
    }))
  )
})

const openForm = (item) => {
  selected.value = item
  Object.assign(form, item.catatan_aktivitas || {})
  form.registrasi_anak_id = item.registrasi_anak_id
  form.pelayanan_terapi_anak_id = item.id
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const submit = async () => {
  try {
    await api.post('/catatan-aktivitas-anak', form)
    toast.success('Catatan berhasil disimpan')
    closeModal()
  } catch (e) {
    toast.error('Gagal menyimpan catatan')
  }
}
</script>

<style scoped>
.textarea {
  @apply w-full px-3 py-2 border rounded-xl min-h-[96px]
         focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400
         disabled:bg-gray-100;
}
.btn-primary {
  @apply px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700;
}
.btn-secondary {
  @apply px-4 py-2 rounded-xl border hover:bg-gray-50;
}
</style>
