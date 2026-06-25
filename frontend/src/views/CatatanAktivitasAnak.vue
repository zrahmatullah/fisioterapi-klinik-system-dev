<template>
  <div class="min-h-screen bg-gray-50 p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
          Catatan Aktivitas Sesi Terapi Anak
        </h1>
        <p class="text-gray-500 text-sm mt-1">
          Rekap aktivitas terapi & tugas rumah anak secara lengkap
        </p>
      </div>

      <!-- SEARCH -->
      <div class="relative w-full sm:w-64">
        <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input
          v-model="search"
          placeholder="Cari nama anak..."
          class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm transition focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500"
        />
      </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="px-5 py-3.5 text-center font-semibold">No</th>

              <!-- SORT TANGGAL -->
              <th
                class="px-5 py-3.5 text-left font-semibold cursor-pointer select-none hover:bg-gray-100 transition"
                @click="toggleSort"
              >
                <div class="flex items-center gap-2">
                  Tanggal
                  <i
                    class="pi text-[10px] text-gray-400"
                    :class="sortOrder === 'desc' ? 'pi-sort-amount-down' : 'pi-sort-amount-up'"
                  ></i>
                </div>
              </th>

              <th class="px-5 py-3.5 text-left font-semibold">Jam</th>
              <th class="px-5 py-3.5 text-center font-semibold">Status</th>
              <th class="px-5 py-3.5 text-left font-semibold">Aktivitas</th>
              <th class="px-5 py-3.5 text-left font-semibold">Keterangan</th>
              <th class="px-5 py-3.5 text-left font-semibold">Tugas Rumah</th>
              <th class="px-5 py-3.5 text-center font-semibold">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(row, i) in paginatedRows"
              :key="row.id"
              class="border-t border-gray-100 hover:bg-indigo-50/40 transition align-top"
            >
              <td class="px-5 py-4 text-center font-medium text-gray-500">
                {{ (currentPage - 1) * perPage + i + 1 }}
              </td>

              <td class="px-5 py-4">
                <div class="font-semibold text-gray-800">{{ row.hari }}</div>
                <div class="text-xs text-gray-400 mt-0.5">{{ formatTanggal(row.tanggal) }}</div>
              </td>

              <td class="px-5 py-4 text-gray-600">
                {{ row.jam_mulai }} - {{ row.jam_selesai }}
              </td>

              <td class="px-5 py-4 text-center">
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                  :class="row.status === 'hadir'
                    ? 'bg-emerald-50 text-emerald-700'
                    : 'bg-amber-50 text-amber-700'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="row.status === 'hadir' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                  {{ row.status === 'hadir' ? 'Hadir' : 'Terjadwal' }}
                </span>
              </td>

              <td class="px-5 py-4 text-gray-600 max-w-[180px] truncate">{{ row.aktivitas || '-' }}</td>
              <td class="px-5 py-4 text-gray-600 max-w-[180px] truncate">{{ row.keterangan || '-' }}</td>
              <td class="px-5 py-4 text-gray-600 max-w-[180px] truncate">{{ row.tugas_rumah || '-' }}</td>

              <td class="px-5 py-4 text-center">
                <div class="flex flex-col gap-2 items-stretch min-w-[110px]">
                  <button
                    class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-medium hover:bg-indigo-700 transition flex items-center justify-center gap-1.5"
                    @click="openModal(row)"
                  >
                    <i class="pi pi-eye text-[11px]"></i>
                    Lihat
                  </button>

                  <button
                    class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 text-xs font-medium hover:bg-gray-200 transition flex items-center justify-center gap-1.5"
                    @click="cetak(row)"
                  >
                    <i class="pi pi-print text-[11px]"></i>
                    Cetak
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="paginatedRows.length === 0">
              <td colspan="8" class="py-14 text-center">
                <div class="flex flex-col items-center">
                  <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                    <i class="pi pi-inbox text-xl text-gray-300"></i>
                  </div>
                  <p class="text-gray-500 font-medium text-sm">Tidak ada data</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between px-6 py-4 border-t border-gray-100 bg-gray-50">
        <div class="text-sm text-gray-500">
          Halaman {{ currentPage }} dari {{ totalPages || 1 }}
        </div>

        <div class="flex items-center gap-1.5 flex-wrap">
          <button
            class="page-btn"
            :disabled="currentPage === 1"
            @click="currentPage--"
          >
            Prev
          </button>

          <button
            v-for="p in totalPages"
            :key="p"
            class="page-btn"
            :class="{ active: p === currentPage }"
            @click="currentPage = p"
          >
            {{ p }}
          </button>

          <button
            class="page-btn"
            :disabled="currentPage === totalPages"
            @click="currentPage++"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL DETAIL -->
    <transition name="fade">
      <div
        v-if="showModal"
        class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      >
        <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl overflow-hidden max-h-[92vh] flex flex-col">

          <!-- HEADER -->
          <div class="bg-indigo-600 px-6 py-5 shrink-0">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-bold text-white">Catatan Aktivitas Sesi Terapi Anak</h3>
                <p class="text-indigo-100 text-sm mt-0.5">{{ selected.nama_anak }}</p>
              </div>
              <button
                @click="showModal = false"
                class="w-9 h-9 rounded-full bg-white/15 hover:bg-white/25 transition flex items-center justify-center text-white"
                aria-label="Tutup"
              >
                <i class="pi pi-times text-sm"></i>
              </button>
            </div>
          </div>

          <div class="p-6 space-y-6 text-sm overflow-y-auto">

            <!-- INFO GRID -->
            <div class="grid grid-cols-2 gap-4 bg-gray-50 border border-gray-100 p-4 rounded-2xl">
              <div>
                <div class="text-gray-400 text-xs mb-1.5">Nama Anak</div>
                <div class="bg-white border border-gray-200 px-3.5 py-2.5 rounded-xl font-medium text-gray-800">
                  {{ selected.nama_anak }}
                </div>
              </div>

              <div>
                <div class="text-gray-400 text-xs mb-1.5">Hari & Tanggal</div>
                <div class="bg-white border border-gray-200 px-3.5 py-2.5 rounded-xl font-medium text-gray-800">
                  {{ selected.hari }}, {{ formatTanggal(selected.tanggal) }}
                </div>
              </div>

              <div>
                <div class="text-gray-400 text-xs mb-1.5">Jam</div>
                <div class="bg-white border border-gray-200 px-3.5 py-2.5 rounded-xl font-medium text-gray-800">
                  {{ selected.jam_mulai }} - {{ selected.jam_selesai }}
                </div>
              </div>

              <div>
                <div class="text-gray-400 text-xs mb-1.5">Terapis</div>
                <div class="bg-white border border-gray-200 px-3.5 py-2.5 rounded-xl font-medium text-gray-800">
                  {{ selected.terapis }}
                </div>
              </div>

              <div class="flex items-center justify-end col-span-2">
                <span class="text-gray-500 mr-3 text-sm">Status :</span>
                <span
                  class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold"
                  :class="selected.status === 'hadir'
                    ? 'bg-emerald-50 text-emerald-700'
                    : 'bg-amber-50 text-amber-700'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="selected.status === 'hadir' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                  {{ selected.status === 'hadir' ? 'Hadir' : 'Terjadwal' }}
                </span>
              </div>
            </div>

            <!-- AKTIVITAS & KETERANGAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <div class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                  <i class="pi pi-bolt text-indigo-500 text-sm"></i>
                  Aktivitas Terapi
                </div>
                <div class="border border-gray-200 p-3.5 min-h-[120px] bg-gray-50 rounded-xl whitespace-pre-line text-gray-600">
                  {{ selected.aktivitas }}
                </div>
              </div>

              <div>
                <div class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                  <i class="pi pi-file-edit text-indigo-500 text-sm"></i>
                  Keterangan Terapi
                </div>
                <div class="border border-gray-200 p-3.5 min-h-[120px] bg-gray-50 rounded-xl whitespace-pre-line text-gray-600">
                  {{ selected.keterangan }}
                </div>
              </div>
            </div>

            <!-- TUGAS RUMAH -->
            <div>
              <div class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                <i class="pi pi-home text-indigo-500 text-sm"></i>
                Tugas Rumah
              </div>
              <div class="border border-gray-200 p-3.5 min-h-[100px] bg-gray-50 rounded-xl whitespace-pre-line text-gray-600">
                {{ selected.tugas_rumah }}
              </div>
            </div>

            <!-- ACTION -->
            <div class="flex justify-end gap-3 pt-2">
              <button
                class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-600 font-medium hover:bg-gray-50 transition"
                @click="showModal = false"
              >
                Tutup
              </button>

              <button
                class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition flex items-center gap-2"
                @click="cetak(selected)"
              >
                <i class="pi pi-print text-sm"></i>
                Cetak
              </button>
            </div>

          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/axios'

const rows = ref([])
const search = ref('')
const showModal = ref(false)
const selected = ref({})

const currentPage = ref(1)
const perPage = 8

const sortOrder = ref('desc') // terbaru dulu

const openModal = (row) => {
  selected.value = row
  showModal.value = true
}

const cetak = (row) => {
  window.open(`/api/catatan-aktivitas-anak/cetak/${row.id}`, '_blank')
}

const toggleSort = () => {
  sortOrder.value = sortOrder.value === 'desc' ? 'asc' : 'desc'
}

const filteredRows = computed(() => {
  if (!search.value) return rows.value
  return rows.value.filter(r =>
    r.nama_anak.toLowerCase().includes(search.value.toLowerCase())
  )
})

const sortedRows = computed(() => {
  return [...filteredRows.value].sort((a, b) => {
    const da = new Date(a.tanggal)
    const db = new Date(b.tanggal)
    return sortOrder.value === 'desc' ? db - da : da - db
  })
})

const totalPages = computed(() =>
  Math.ceil(sortedRows.value.length / perPage)
)

const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return sortedRows.value.slice(start, start + perPage)
})

const formatTanggal = (d) => {
  if (!d) return '-'
  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  }).format(new Date(d))
}

onMounted(async () => {
  const res = await api.get('/riwayat-pembayaran-anak')
  const temp = []

  res.data.forEach(p => {
    p.registrasi?.pelayanans?.forEach(pl => {
      const c = pl.catatan_aktivitas
      temp.push({
        id: pl.id,
        nama_anak: p.registrasi.profile_anak.nama_anak,
        hari: new Date(pl.tanggal_penjadwalan).toLocaleDateString('id-ID', { weekday: 'long' }),
        tanggal: pl.tanggal_penjadwalan,
        jam_mulai: pl.jam_mulai || '-',
        jam_selesai: pl.jam_selesai || '-',
        terapis: pl.terapis?.nama || '-',
        status: c?.checkin_sesi ? 'hadir' : 'terjadwal',
        aktivitas: c?.aktivitas_terapi || '-',
        keterangan: c?.keterangan_terapi || '-',
        tugas_rumah: c?.tugas_rumah || '-'
      })
    })
  })

  rows.value = temp
})
</script>

<style scoped>
.page-btn {
  @apply px-3.5 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-600 text-xs font-medium hover:bg-indigo-50 hover:border-indigo-300 transition disabled:opacity-40 disabled:cursor-not-allowed;
}

.page-btn.active {
  @apply bg-indigo-600 text-white border-transparent;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>