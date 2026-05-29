<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-indigo-50 p-6 space-y-6">

    <!-- HEADER -->
    <div
      class="relative overflow-hidden rounded-3xl
            bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600
            p-7 shadow-xl"
    >
      <div class="relative z-10">
        <h1 class="text-3xl font-bold text-white">
          📊 Registrasi & Layanan Anak
        </h1>

        <p class="text-white/80 mt-2">
          Monitoring registrasi, penjadwalan terapi, dan penentuan terapis
        </p>
      </div>

      <div
        class="absolute right-0 top-0 w-72 h-72 bg-white/10 rounded-full blur-3xl"
      />
    </div>

    <!-- SUMMARY -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

      <div class="card-summary">
        <p class="summary-label">Total Registrasi</p>
        <h2 class="summary-value text-indigo-600">
          {{ rows.length }}
        </h2>
      </div>

      <div class="card-summary">
        <p class="summary-label">Terjadwal</p>
        <h2 class="summary-value text-yellow-500">
          {{ countStatus('terjadwal') }}
        </h2>
      </div>

      <div class="card-summary">
        <p class="summary-label">Proses</p>
        <h2 class="summary-value text-blue-500">
          {{ countStatus('proses') }}
        </h2>
      </div>

      <div class="card-summary">
        <p class="summary-label">Selesai</p>
        <h2 class="summary-value text-green-500">
          {{ countStatus('selesai') }}
        </h2>
      </div>
    </div>

    <!-- FILTER -->
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-5">

      <div class="flex flex-col xl:flex-row gap-4 xl:items-center justify-between">

        <div class="flex items-center gap-3">
          <span class="text-sm text-gray-500">Tampilkan</span>

          <input
            type="number"
            min="1"
            v-model.number="perPage"
            class="input-modern w-24 text-center"
          />

          <span class="text-sm text-gray-500">data</span>
        </div>

        <div class="flex flex-col md:flex-row gap-3 w-full xl:w-auto">

          <input
            type="date"
            v-model="filterTanggal"
            class="input-modern"
          />

          <input
            v-model="search"
            type="text"
            placeholder="Cari nama anak / no registrasi..."
            class="input-modern md:w-80"
          />
        </div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">

      <div class="overflow-x-auto">

        <table class="w-full text-sm">

          <thead class="bg-slate-100 sticky top-0 z-10">
            <tr>
              <th class="th">No</th>
              <th class="th">No Regis</th>

              <th
                class="th cursor-pointer hover:text-indigo-600"
                @click="sortBy('nama')"
              >
                Nama Anak ⬍
              </th>

              <th
                class="th cursor-pointer hover:text-indigo-600"
                @click="sortBy('tanggal')"
              >
                Tanggal ⬍
              </th>

              <th class="th">Layanan</th>

              <th class="th text-center">
                Total Sesi
              </th>

              <th
                class="th cursor-pointer hover:text-indigo-600"
                @click="sortBy('status')"
              >
                Status ⬍
              </th>

              <th class="th text-center">
                Aksi
              </th>
            </tr>
          </thead>

          <tbody>

            <tr
              v-for="row in paginatedRows"
              :key="row.no"
              class="border-b hover:bg-indigo-50/50 transition"
            >
              <td class="td text-center">
                {{ row.no }}
              </td>

              <td class="td font-medium">
                {{ row.no_regis }}
              </td>

              <td class="td">
                {{ row.profile_anak?.nama_anak }}
              </td>

              <td class="td">
                {{ formatDate(row.tgl_regis) }}
              </td>

              <td class="td">
                {{ row.pelayanans?.[0]?.layanan?.layanan || '-' }}
              </td>

              <td class="td text-center">
                {{ row.pelayanans?.length || 0 }}
              </td>

              <td class="td">
                <span :class="statusClass(row.pelayanans?.[0]?.status)">
                  {{ row.pelayanans?.[0]?.status || '-' }}
                </span>
              </td>

              <td class="td text-center">

                <div class="flex justify-center gap-2">

                  <button
                    @click="openDetail(row)"
                    class="btn-indigo"
                  >
                    Detail
                  </button>

                  <button
                    @click="openTerapis(row)"
                    class="btn-green"
                  >
                    Terapis
                  </button>

                </div>
              </td>
            </tr>

            <!-- EMPTY -->
            <tr v-if="paginatedRows.length === 0">
              <td colspan="8">

                <div class="flex flex-col items-center py-14">
                  <div class="text-6xl mb-4">📭</div>

                  <p class="font-semibold text-gray-600">
                    Tidak ada data ditemukan
                  </p>

                  <p class="text-sm text-gray-400 mt-1">
                    Coba ubah filter pencarian
                  </p>
                </div>

              </td>
            </tr>

          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div
        v-if="totalPages > 1"
        class="flex flex-col md:flex-row justify-between items-center gap-4 px-6 py-4 border-t bg-gray-50"
      >

        <div class="text-sm text-gray-500">
          Halaman
          <span class="font-semibold">{{ currentPage }}</span>
          dari
          <span class="font-semibold">{{ totalPages }}</span>
        </div>

        <div class="flex items-center gap-2">

          <button
            class="page-btn"
            :disabled="currentPage === 1"
            @click="changePage(currentPage - 1)"
          >
            Prev
          </button>

          <button
            v-for="p in visiblePages"
            :key="p"
            @click="changePage(p)"
            class="page-btn"
            :class="{ active: p === currentPage }"
          >
            {{ p }}
          </button>

          <button
            class="page-btn"
            :disabled="currentPage === totalPages"
            @click="changePage(currentPage + 1)"
          >
            Next
          </button>

        </div>
      </div>
    </div>

    <!-- MODAL DETAIL -->
    <div v-if="showDetail" class="modal">

      <div class="modal-box">

        <div class="modal-header">
          <h2>📋 Detail Penjadwalan</h2>

          <button
            @click="showDetail = false"
            class="text-gray-400 hover:text-red-500 transition"
          >
            ✖
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm">

            <thead class="bg-slate-100">
              <tr>
                <th class="th">Sesi</th>
                <th class="th">Tanggal</th>
                <th class="th">Terapis</th>
                <th class="th">Status</th>
              </tr>
            </thead>

            <tbody>

              <tr
                v-for="(s, i) in modalData.pelayanans"
                :key="s.id"
                class="border-b"
              >
                <td class="td">Sesi {{ i + 1 }}</td>

                <td class="td">
                  {{ formatDate(s.tanggal_penjadwalan) }}
                </td>

                <td class="td">
                  {{ s.terapis?.nama || '-' }}
                </td>

                <td class="td">
                  <span :class="statusClass(s.status)">
                    {{ s.status }}
                  </span>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL TERAPIS -->
    <div v-if="showTerapis" class="modal">

      <div class="modal-box max-w-5xl">

        <div class="modal-header">
          <h2>👩‍⚕️ Penentuan Terapis</h2>

          <button
            @click="showTerapis = false"
            class="text-gray-400 hover:text-red-500 transition"
          >
            ✖
          </button>
        </div>

        <div class="overflow-x-auto">

          <table class="w-full text-sm mb-5">

            <thead class="bg-slate-100">
              <tr>
                <th class="th">Sesi</th>
                <th class="th">Tanggal</th>
                <th class="th">Terapis</th>
              </tr>
            </thead>

            <tbody>

              <tr
                v-for="(s, i) in modalData.pelayanans"
                :key="s.id"
                class="border-b"
              >
                <td class="td">
                  Sesi {{ i + 1 }}
                </td>

                <td class="td">
                  {{ formatDate(s.tanggal_penjadwalan) }}
                </td>

                <td class="td">
                  <select
                    v-model="s.terapis_id"
                    class="input-modern w-full"
                  >
                    <option value="">
                      -- Pilih Terapis --
                    </option>

                    <option
                      v-for="t in terapisList"
                      :key="t.id"
                      :value="t.id"
                    >
                      {{ t.nama }}
                    </option>
                  </select>
                </td>
              </tr>

            </tbody>
          </table>

          <div class="flex justify-end">
            <button
              @click="saveAllTerapis"
              class="btn-green px-5 py-3 text-sm"
            >
              💾 Simpan Terapis
            </button>
          </div>

        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
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

const filterTanggal = ref(
  new Date().toISOString().slice(0, 10)
)

const toast = useToast()

const formatDate = (dateStr) => {
  if (!dateStr) return '-'

  const d = new Date(dateStr)

  if (isNaN(d)) return dateStr

  return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
}

onMounted(async () => {
  try {

    terapisList.value =
      (await api.get('/user-profile?jenis_user_id=9')).data.data

    const res = await api.get('/registrasi-anak')

    rows.value = res.data.data ?? res.data

  } catch (err) {
    console.error(err)
    toast.error('Gagal mengambil data')
  }
})

const countStatus = (status) => {
  return rows.value.filter(
    r => r.pelayanans?.[0]?.status === status
  ).length
}

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
    }
    else if (sortKey.value === 'tanggal') {
      A = a.tgl_regis || ''
      B = b.tgl_regis || ''
    }
    else {
      A = a.pelayanans?.[0]?.status || ''
      B = b.pelayanans?.[0]?.status || ''
    }

    if (A < B) return sortAsc.value ? -1 : 1
    if (A > B) return sortAsc.value ? 1 : -1

    return 0
  })
})

const paginatedRows = computed(() => {

  const start =
    (currentPage.value - 1) * perPage.value

  return sortedRows.value
    .slice(start, start + perPage.value)
    .map((row, index) => ({
      ...row,
      no: start + index + 1
    }))
})

const totalPages = computed(() =>
  Math.ceil(sortedRows.value.length / perPage.value)
)

const visiblePages = computed(() => {

  const total = totalPages.value
  const current = currentPage.value

  const range = 2

  let start = Math.max(1, current - range)
  let end = Math.min(total, current + range)

  const pages = []

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

const changePage = (page) => {
  if (page < 1 || page > totalPages.value) return
  currentPage.value = page
}

const sortBy = (key) => {

  if (sortKey.value === key) {
    sortAsc.value = !sortAsc.value
  } else {
    sortKey.value = key
    sortAsc.value = true
  }
}

watch([search, perPage], () => {

  if (!perPage.value || perPage.value < 1) {
    perPage.value = 1
  }

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

const saveAllTerapis = async () => {

  try {

    for (const s of modalData.value.pelayanans) {

      if (s.terapis_id) {

        await api.put(
          `/pelayanan-terapi-anak/${s.id}/terapis`,
          {
            terapis_id: s.terapis_id
          }
        )
      }
    }

    await kirimEmail(modalData.value)

    toast.success(
      'Terapis berhasil disimpan & email dikirim'
    )

    showTerapis.value = false

    const res = await api.get('/registrasi-anak')

    rows.value = res.data.data ?? res.data

  } catch (err) {

    console.error(err)

    toast.error(
      'Gagal menyimpan terapis / kirim email'
    )
  }
}

const kirimEmail = async (row) => {

  try {

    await api.post(
      `/registrasi-anak/${row.id}/kirim-email-jadwal`
    )

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
.th {
  @apply px-4 py-4 text-left font-semibold text-gray-600 whitespace-nowrap;
}

.td {
  @apply px-4 py-4 whitespace-nowrap;
}

.card-summary {
  @apply bg-white rounded-3xl shadow-lg border border-gray-100 p-5;
}

.summary-label {
  @apply text-sm text-gray-500;
}

.summary-value {
  @apply text-4xl font-bold mt-2;
}

.input-modern {
  @apply border border-gray-200 rounded-2xl px-4 py-2
  focus:ring-2 focus:ring-indigo-500
  focus:border-indigo-500
  outline-none transition;
}

.btn-indigo {
  @apply bg-indigo-600 text-white px-4 py-2 rounded-xl
  text-xs hover:bg-indigo-700 transition shadow-sm;
}

.btn-green {
  @apply bg-emerald-600 text-white px-4 py-2 rounded-xl
  text-xs hover:bg-emerald-700 transition shadow-sm;
}

.btn-blue {
  @apply bg-sky-600 text-white px-4 py-2 rounded-xl
  text-xs hover:bg-sky-700 transition shadow-sm;
}

.page-btn {
  @apply px-4 py-2 rounded-xl border border-gray-200
  text-sm hover:bg-gray-100 transition
  disabled:opacity-40;
}

.page-btn.active {
  @apply bg-indigo-600 text-white border-indigo-600;
}

.badge-yellow {
  @apply bg-yellow-100 text-yellow-700 px-3 py-1
  rounded-full text-xs font-semibold;
}

.badge-blue {
  @apply bg-blue-100 text-blue-700 px-3 py-1
  rounded-full text-xs font-semibold;
}

.badge-green {
  @apply bg-green-100 text-green-700 px-3 py-1
  rounded-full text-xs font-semibold;
}

.badge-red {
  @apply bg-red-100 text-red-700 px-3 py-1
  rounded-full text-xs font-semibold;
}

.badge-gray {
  @apply bg-gray-100 text-gray-600 px-3 py-1
  rounded-full text-xs font-semibold;
}

.modal {
  @apply fixed inset-0 bg-black/50 backdrop-blur-sm
  flex items-center justify-center z-50 p-4;
  animation: fadeIn .2s ease;
}

.modal-box {
  @apply bg-white w-full max-w-4xl rounded-3xl
  p-6 shadow-2xl border border-gray-100;
}

.modal-header {
  @apply flex justify-between items-center
  mb-5 text-xl font-bold;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(.98);
  }

  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>