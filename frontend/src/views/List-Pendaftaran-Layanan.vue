<template>
  <div class="p-6 bg-gray-50 min-h-screen space-y-6">

    <h1 class="text-2xl font-bold">📊 Registrasi & Layanan Anak</h1>

    <!-- SEARCH + PER PAGE -->
    <div class="bg-white rounded-2xl shadow p-4 flex flex-col md:flex-row justify-between items-center gap-4">

      <div class="flex items-center gap-2">
        <span class="text-sm text-gray-600">Tampilkan</span>

        <!-- DIUBAH: SELECT → INPUT -->
        <input
          type="number"
          min="1"
          v-model.number="perPage"
          class="border rounded px-2 py-1 w-20 text-center"
          placeholder="10"
        />

        <span class="text-sm text-gray-600">data</span>
      </div>

       <input
        type="date"
        v-model="filterTanggal"
        class="border rounded px-3 py-2"
      />

      <input
        v-model="search"
        type="text"
        placeholder="Cari nama anak / no regis..."
        class="border rounded px-3 py-2 w-full md:w-64"
      />
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="th">No</th>
            <th class="th">No Regis</th>
            <th class="th cursor-pointer" @click="sortBy('nama')">Nama Anak ⬍</th>
            <th class="th cursor-pointer" @click="sortBy('tanggal')">Tanggal ⬍</th>
            <th class="th">Layanan</th>
            <th class="th text-center">Total Sesi</th>
            <th class="th cursor-pointer" @click="sortBy('status')">Status ⬍</th>
            <th class="th text-center">Aksi</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="row in paginatedRows" :key="row.no" class="border-b hover:bg-gray-50">
            <td class="td text-center">{{ row.no }}</td>
            <td class="td">{{ row.no_regis }}</td>
            <td class="td">{{ row.profile_anak?.nama_anak }}</td>
            <td class="td">{{ formatDate(row.tgl_regis) }}</td>
            <td class="td">{{ row.pelayanans?.[0]?.layanan?.layanan || '-' }}</td>
            <td class="td text-center">{{ row.pelayanans?.length || 0 }}</td>

            <td class="td">
              <span :class="statusClass(row.pelayanans?.[0]?.status)">
                {{ row.pelayanans?.[0]?.status || '-' }}
              </span>
            </td>

            <td class="td text-center space-x-2">
              <button @click="openDetail(row)" class="btn-indigo">Detail</button>
              <button @click="openTerapis(row)" class="btn-green">Terapis</button>
              <!-- <button @click="kirimEmail(row)" class="btn-blue">Email</button> -->
            </td>
          </tr>

          <tr v-if="paginatedRows.length === 0">
            <td colspan="8" class="text-center py-8 text-gray-400">Tidak ada data</td>
          </tr>
        </tbody>
      </table>

      <!-- PAGINATION -->
      <div v-if="totalPages > 1" class="flex justify-between items-center px-6 py-4 border-t bg-gray-50">
        <div class="text-sm text-gray-500">
          Halaman {{ currentPage }} dari {{ totalPages }}
        </div>

        <div class="flex items-center gap-2">
          <button class="page-btn" :disabled="currentPage === 1" @click="changePage(currentPage - 1)">Prev</button>

          <span v-if="visiblePages[0] > 1">...</span>

          <button
            v-for="p in visiblePages"
            :key="p"
            @click="changePage(p)"
            class="page-btn"
            :class="{ active: p === currentPage }"
          >
            {{ p }}
          </button>

          <span v-if="visiblePages.at(-1) < totalPages">...</span>

          <button class="page-btn" :disabled="currentPage === totalPages" @click="changePage(currentPage + 1)">Next</button>
        </div>
      </div>
    </div>

    <!-- MODAL DETAIL -->
    <div v-if="showDetail" class="modal">
      <div class="modal-box">
        <div class="modal-header">
          <h2>📋 Detail Penjadwalan</h2>
          <button @click="showDetail = false">✖</button>
        </div>

        <table class="w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="th">Sesi</th>
              <th class="th">Tanggal</th>
              <th class="th">Terapis</th>
              <th class="th">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(s, i) in modalData.pelayanans" :key="s.id">
              <td class="td">Sesi {{ i + 1 }}</td>
              <td class="td">{{ formatDate(s.tanggal_penjadwalan) }}</td>
              <td class="td">{{ s.terapis?.nama || '-' }}</td>
              <td class="td">
                <span :class="statusClass(s.status)">{{ s.status }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL TERAPIS -->
    <div v-if="showTerapis" class="modal">
      <div class="modal-box max-w-4xl">
        <div class="modal-header">
          <h2>👩‍⚕️ Penentuan Terapis</h2>
          <button @click="showTerapis = false">✖</button>
        </div>

        <table class="w-full text-sm mb-4">
          <thead class="bg-gray-100">
            <tr>
              <th class="th">Sesi</th>
              <th class="th">Tanggal</th>
              <th class="th">Terapis</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(s, i) in modalData.pelayanans" :key="s.id">
              <td class="td">Sesi {{ i + 1 }}</td>
              <td class="td">{{ formatDate(s.tanggal_penjadwalan) }}</td>

              <td class="td">
                <select v-model="s.terapis_id" class="border rounded px-2 py-1 w-full">
                  <option value="">-- Pilih Terapis --</option>
                  <option v-for="t in terapisList" :key="t.id" :value="t.id">
                    {{ t.nama }}
                  </option>
                </select>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="flex justify-end">
          <button @click="saveAllTerapis" class="btn-green px-4 py-2">💾 Simpan Terapis</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
/* SCRIPT KAMU ASLI – TIDAK DIUBAH */
import { ref, onMounted, computed, watch } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'

const rows = ref([])
const currentPage = ref(1)
const perPage = ref(10)

const search = ref('')
const sortKey = ref('')
const sortAsc = ref(true)

const modalData = ref({ pelayanans: [] })
const showDetail = ref(false)
const showTerapis = ref(false)
const terapisList = ref([])
const filterTanggal = ref(new Date().toISOString().slice(0, 10))


const toast = useToast()

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  if (isNaN(d)) return dateStr
  return `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`
}

onMounted(async () => {
  terapisList.value = (await api.get('/user-profile?jenis_user_id=9')).data.data
  const res = await api.get('/registrasi-anak')
  rows.value = res.data.data ?? res.data
})

const filteredRows = computed(() => {
  const q = search.value.toLowerCase()

  return rows.value.filter(r => {
    const cocokTanggal =
      r.tgl_regis?.slice(0, 10) === filterTanggal.value
    const cocokSearch =
      !search.value ||
      r.profile_anak?.nama_anak?.toLowerCase().includes(q) ||
      r.no_regis?.toLowerCase().includes(q)

    return cocokTanggal && cocokSearch
  })
})


const sortedRows = computed(() => {
  if (!sortKey.value) return filteredRows.value

  return [...filteredRows.value].sort((a, b) => {
    let A, B
    if (sortKey.value === 'nama') {
      A = a.profile_anak?.nama_anak || ''
      B = b.profile_anak?.nama_anak || ''
    } else if (sortKey.value === 'tanggal') {
      A = a.tgl_regis || ''
      B = b.tgl_regis || ''
    } else {
      A = a.pelayanans?.[0]?.status || ''
      B = b.pelayanans?.[0]?.status || ''
    }

    if (A < B) return sortAsc.value ? -1 : 1
    if (A > B) return sortAsc.value ? 1 : -1
    return 0
  })
})

const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return sortedRows.value.slice(start, start + perPage.value).map((row, index) => ({
    ...row,
    no: start + index + 1
  }))
})

const totalPages = computed(() => Math.ceil(sortedRows.value.length / perPage.value))

const visiblePages = computed(() => {
  const total = totalPages.value
  const current = currentPage.value
  const range = 2

  let start = Math.max(1, current - range)
  let end = Math.min(total, current + range)

  if (end - start < 4) {
    if (start === 1) end = Math.min(total, start + 4)
    else if (end === total) start = Math.max(1, end - 4)
  }

  const pages = []
  for (let i = start; i <= end; i++) pages.push(i)
  return pages
})

const changePage = (page) => {
  if (page < 1 || page > totalPages.value) return
  currentPage.value = page
}

const sortBy = (key) => {
  if (sortKey.value === key) sortAsc.value = !sortAsc.value
  else {
    sortKey.value = key
    sortAsc.value = true
  }
}

watch([search, perPage], () => {
  if (!perPage.value || perPage.value < 1) perPage.value = 1
  currentPage.value = 1
})

const openDetail = (row) => {
  modalData.value = JSON.parse(JSON.stringify(row))
  showDetail.value = true
}

const openTerapis = (row) => {
  modalData.value = JSON.parse(JSON.stringify(row))
  showTerapis.value = true
}

// const saveAllTerapis = async () => {
//   try {
//     for (const s of modalData.value.pelayanans) {
//       if (s.terapis_id) {
//         await api.put(`/pelayanan-terapi-anak/${s.id}/terapis`, { terapis_id: s.terapis_id })
//       }
//     }
//     toast.success('Terapis berhasil disimpan')
//     showTerapis.value = false

//     const res = await api.get('/registrasi-anak')
//     rows.value = res.data.data ?? res.data
//   } catch {
//     toast.error('Gagal menyimpan terapis')
//   }
// }

const saveAllTerapis = async () => {
  try {
    for (const s of modalData.value.pelayanans) {
      if (s.terapis_id) {
        await api.put(
          `/pelayanan-terapi-anak/${s.id}/terapis`,
          { terapis_id: s.terapis_id }
        )
      }
    }

    await kirimEmail(modalData.value)

    toast.success('Terapis berhasil disimpan & email dikirim')
    showTerapis.value = false

    const res = await api.get('/registrasi-anak')
    rows.value = res.data.data ?? res.data

  } catch (err) {
    console.error(err)
    toast.error('Gagal menyimpan terapis / kirim email')
  }
}

const kirimEmail = async (row) => {
  try {
    await api.post(`/registrasi-anak/${row.id}/kirim-email-jadwal`)
    toast.success('Email berhasil dikirim')
  } catch {
    toast.error('Gagal mengirim email')
  }
}

const statusClass = (status) => ({
  terjadwal: 'badge-yellow',
  proses: 'badge-blue',
  selesai: 'badge-green',
  batal: 'badge-red'
}[status] || 'badge-gray')
</script>

<style scoped>
/* STYLE ASLI – TIDAK DIUBAH */
.th { @apply px-4 py-3 text-left font-semibold text-gray-600; }
.td { @apply px-4 py-3; }

.btn-indigo { @apply bg-indigo-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-indigo-700; }
.btn-green { @apply bg-green-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-green-700; }
.btn-blue { @apply bg-blue-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-blue-700; }

.page-btn { @apply px-3 py-1 rounded border text-sm hover:bg-gray-100 disabled:opacity-40; }
.page-btn.active { @apply bg-indigo-600 text-white border-indigo-600; }

.badge-yellow { @apply bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs; }
.badge-blue { @apply bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs; }
.badge-green { @apply bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs; }
.badge-red { @apply bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs; }
.badge-gray { @apply bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs; }

.modal { @apply fixed inset-0 bg-black/40 flex items-center justify-center z-50; }
.modal-box { @apply bg-white w-full max-w-3xl rounded-2xl p-6; }
.modal-header { @apply flex justify-between mb-4 text-lg font-semibold; }
</style>
