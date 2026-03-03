<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-100 to-slate-200 p-6 space-y-6">

    <!-- HEADER -->
    <div class="bg-white rounded-2xl shadow-lg p-6 flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-indigo-700">Daftar Pasien</h2>
        <p class="text-sm text-slate-500">Kelola registrasi & jadwal layanan terapi</p>
      </div>
      <div class="text-sm text-slate-400">
        Total: {{ filteredSortedData.length }} data
      </div>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white rounded-2xl shadow p-4 flex flex-wrap items-center justify-between gap-4">

      <!-- PER PAGE (LEFT) -->
      <div class="flex items-center gap-2">
        <span class="text-sm text-slate-600">Tampilkan</span>
        <input
          type="number"
          min="1"
          v-model.number="perPage"
          class="border rounded-lg px-2 py-1 w-20 text-center"
        />
        <span class="text-sm text-slate-600">data</span>
      </div>

      <div class="flex items-center gap-2">
        <span class="text-sm text-slate-600">Tanggal:</span>
        <input
          type="date"
          v-model="filterTanggal"
          class="border rounded-lg px-3 py-1"
        />
      </div>

      <!-- SEARCH (RIGHT) -->
      <div class="flex items-center gap-2 w-full md:w-1/3">
        <span class="text-slate-500"></span>
        <input
          v-model="search"
          placeholder="Cari nama anak / no registrasi..."
          class="w-full border rounded-xl px-4 py-2 focus:ring-2 focus:ring-indigo-400 outline-none"
        />
      </div>

    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-indigo-50 sticky top-0 z-10">
            <tr>
              <th class="px-5 py-3 text-left font-semibold text-slate-700 text-xs uppercase tracking-wide">No</th>

              <th
                class="px-5 py-3 text-left font-semibold text-slate-700 text-xs uppercase tracking-wide cursor-pointer hover:text-indigo-600"
                @click="setSort('no_regis')"
              >
                No Registrasi {{ sortIcon('no_regis') }}
              </th>

              <th
                class="px-5 py-3 text-left font-semibold text-slate-700 text-xs uppercase tracking-wide cursor-pointer hover:text-indigo-600"
                @click="setSort('tgl_regis')"
              >
                Tanggal {{ sortIcon('tgl_regis') }}
              </th>

              <th
                class="px-5 py-3 text-left font-semibold text-slate-700 text-xs uppercase tracking-wide cursor-pointer hover:text-indigo-600"
                @click="setSort('nama_anak')"
              >
                Nama Anak {{ sortIcon('nama_anak') }}
              </th>

              <th
                class="px-5 py-3 text-left font-semibold text-slate-700 text-xs uppercase tracking-wide cursor-pointer hover:text-indigo-600"
                @click="setSort('ruangan')"
              >
                Ruangan {{ sortIcon('ruangan') }}
              </th>

              <th
                class="px-5 py-3 text-left font-semibold text-slate-700 text-xs uppercase tracking-wide cursor-pointer hover:text-indigo-600"
                @click="setSort('terapis')"
              >
                Terapis {{ sortIcon('terapis') }}
              </th>

              <th class="px-5 py-3 text-center font-semibold text-slate-700 text-xs uppercase tracking-wide">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(r, i) in paginatedData"
              :key="r.id"
              class="border-b hover:bg-indigo-50 transition"
            >
              <td class="px-5 py-3 text-slate-700">
                {{ (currentPage - 1) * perPage + i + 1 }}
              </td>
              <td class="px-5 py-3 text-slate-700">{{ r.no_regis }}</td>
              <td class="px-5 py-3 text-slate-700">{{ formatDate(r.tgl_regis) }}</td>
              <td class="px-5 py-3 text-slate-700 font-medium">{{ r.profile_anak.nama_anak }}</td>
              <td class="px-5 py-3 text-slate-700">{{ r.ruangan.ruangan }}</td>
              <td class="px-5 py-3 text-slate-700">{{ r.terapis.nama }}</td>
              <td class="px-5 py-3 text-center">
                <button
                  @click="openModal(r)"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-lg text-xs shadow"
                >
                  Input Layanan
                </button>
              </td>
            </tr>

            <tr v-if="paginatedData.length === 0">
              <td colspan="7" class="text-center py-10 text-slate-400">
                Tidak ada data ditemukan
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div class="flex justify-between items-center p-4 bg-slate-50">

        <div class="text-sm text-slate-600">
          Halaman {{ currentPage }} dari {{ totalPages }}
        </div>

        <div class="flex items-center gap-1">

          <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="px-3 py-1 border rounded-lg text-sm bg-white hover:bg-indigo-50 disabled:opacity-40"
          >
            Prev
          </button>

          <button
            v-for="page in totalPages"
            :key="page"
            @click="goToPage(page)"
            class="px-3 py-1 border rounded-lg text-sm"
            :class="page === currentPage
              ? 'bg-indigo-600 text-white border-indigo-600'
              : 'bg-white hover:bg-indigo-50'"
          >
            {{ page }}
          </button>

          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 border rounded-lg text-sm bg-white hover:bg-indigo-50 disabled:opacity-40"
          >
            Next
          </button>

        </div>
      </div>
    </div>

    <!-- MODAL -->
    <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <div class="bg-white w-full max-w-3xl rounded-2xl p-6 space-y-6 shadow-xl">

        <div class="flex justify-between items-center border-b pb-3">
          <h3 class="text-xl font-bold text-indigo-700">Input Layanan</h3>
          <button @click="closeModal" class="text-xl">✕</button>
        </div>

        <select v-model="selectedLayananId" class="w-full border rounded-xl px-4 py-2">
          <option value="">-- Pilih Layanan --</option>
          <option v-for="l in layananList" :key="l.id" :value="l.id">
            {{ l.layanan }} ({{ l.qty }} sesi)
          </option>
        </select>

        <div v-if="jadwal.length" class="space-y-3 bg-indigo-50 p-4 rounded-xl">
          <div
            v-for="(s, i) in jadwal"
            :key="i"
            class="grid grid-cols-4 gap-3 items-center"
          >
            <div class="font-semibold text-sm">Sesi {{ i + 1 }}</div>
            <input type="date" v-model="s.tanggal" class="border rounded-lg px-2 py-1 text-sm" />
            <input type="time" v-model="s.jam_mulai" class="border rounded-lg px-2 py-1 text-sm" />
            <input type="time" v-model="s.jam_selesai" class="border rounded-lg px-2 py-1 text-sm" />
          </div>
        </div>

        <div class="flex justify-end gap-3">
          <button @click="closeModal" class="px-5 py-2 bg-slate-200 rounded-xl">Batal</button>
          <button
            @click="submit"
            :disabled="!canSubmit"
            class="px-6 py-2 bg-indigo-600 text-white rounded-xl disabled:opacity-50"
          >
            Simpan
          </button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import api from '@/axios'

const registrasiList = ref([])
const layananList = ref([])

const showModal = ref(false)
const selectedRegistrasi = ref(null)
const selectedLayananId = ref(null)
const selectedLayanan = ref(null)
const jadwal = ref([])
// const filterTanggal = ref('')
const filterTanggal = ref(new Date().toISOString().slice(0, 10))



const search = ref('')

const sortBy = ref('tgl_regis')
const sortOrder = ref('desc')

const currentPage = ref(1)
const perPage = ref(10)

onMounted(async () => {
  registrasiList.value = (await api.get('/registrasi-anak')).data
  layananList.value = (await api.get('/layanan')).data
})

watch(perPage, (val) => {
  if (!val || val < 1) perPage.value = 1
  currentPage.value = 1
})

const setSort = (field) => {
  if (sortBy.value === field) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = field
    sortOrder.value = 'asc'
  }
}

const sortIcon = (field) =>
  sortBy.value !== field ? '⇅' : sortOrder.value === 'asc' ? '⬆️' : '⬇️'

const filteredSortedData = computed(() => {
  const keyword = search.value.toLowerCase()

  let data = registrasiList.value.filter(r => {

    // 📅 WAJIB sesuai tanggal registrasi
    const cocokTanggal =
      r.tgl_regis?.slice(0, 10) === filterTanggal.value

    // 🔎 SEARCH
    const cocokSearch =
      r.no_regis.toLowerCase().includes(keyword) ||
      r.profile_anak.nama_anak.toLowerCase().includes(keyword)

    return cocokTanggal && cocokSearch
  })

  // 🔽 SORTING (tetap)
  data.sort((a, b) => {
    let va, vb

    switch (sortBy.value) {
      case 'nama_anak':
        va = a.profile_anak.nama_anak
        vb = b.profile_anak.nama_anak
        break
      case 'ruangan':
        va = a.ruangan.ruangan
        vb = b.ruangan.ruangan
        break
      case 'terapis':
        va = a.terapis.nama
        vb = b.terapis.nama
        break
      case 'tgl_regis':
        va = new Date(a.tgl_regis)
        vb = new Date(b.tgl_regis)
        break
      default:
        va = a[sortBy.value]
        vb = b[sortBy.value]
    }

    if (va < vb) return sortOrder.value === 'asc' ? -1 : 1
    if (va > vb) return sortOrder.value === 'asc' ? 1 : -1
    return 0
  })

  return data
})

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredSortedData.value.length / perPage.value))
)

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredSortedData.value.slice(start, start + perPage.value)
})

const goToPage = (page) => currentPage.value = page
const nextPage = () => currentPage.value < totalPages.value && currentPage.value++
const prevPage = () => currentPage.value > 1 && currentPage.value--

const formatDate = (dateStr) => {
  const d = new Date(dateStr)
  return `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`
}

/* MODAL LOGIC */

const openModal = r => {
  selectedRegistrasi.value = r
  showModal.value = true
  selectedLayananId.value = null
  jadwal.value = []
}

const closeModal = () => showModal.value = false

watch(selectedLayananId, id => {
  selectedLayanan.value = layananList.value.find(l => l.id === id)

  jadwal.value = Array.from(
    { length: selectedLayanan.value?.qty || 0 },
    () => ({
      tanggal: '',
      jam_mulai: '',
      jam_selesai: ''
    })
  )
})

const canSubmit = computed(() =>
  jadwal.value.length &&
  jadwal.value.every(j => j.tanggal && j.jam_mulai && j.jam_selesai)
)

const submit = async () => {
  await api.post('/pelayanan-terapi-anak', {
    registrasi_anak_id: selectedRegistrasi.value.id,
    layanan_id: selectedLayananId.value,
    tanggal_penjadwalan: jadwal.value
  })
  closeModal()
}
</script>
