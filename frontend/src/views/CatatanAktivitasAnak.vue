<template>
  <div class="min-h-screen bg-gray-100 p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between bg-white p-5 rounded-lg shadow-sm border">
  <div>
    <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
      Catatan Aktivitas Sesi Terapi Anak
    </h1>
    <p class="text-gray-500 text-sm mt-1">
      Rekap aktivitas terapi & tugas rumah anak secara lengkap
    </p>
  </div>

 
</div>


    <!-- SEARCH -->
    <div class="flex justify-end">
      <input
        v-model="search"
        placeholder="Cari nama anak..."
        class="border rounded-full px-4 py-2 text-sm w-56"
      />
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="w-full text-sm border border-gray-300">
        <thead class="bg-gray-100">
          <tr>
            <th class="th">No</th>

            <!-- SORT TANGGAL -->
            <th class="th cursor-pointer select-none" @click="toggleSort">
              Tanggal
              <span class="ml-1">
                <span v-if="sortOrder === 'desc'">⬇</span>
                <span v-else>⬆</span>
              </span>
            </th>

            <th class="th">Jam</th>
            <th class="th">Status</th>
            <th class="th">Aktivitas</th>
            <th class="th">Keterangan</th>
            <th class="th">Tugas Rumah</th>
            <th class="th text-center">Aksi</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(row, i) in paginatedRows"
            :key="row.id"
            class="border-t hover:bg-gray-50 align-top"
          >
            <td class="td text-center">
              {{ (currentPage - 1) * perPage + i + 1 }}
            </td>

            <td class="td">
              <div class="font-medium">{{ row.hari }}</div>
              <div class="text-xs text-gray-500">{{ formatTanggal(row.tanggal) }}</div>
            </td>

            <td class="td">
              {{ row.jam_mulai }} - {{ row.jam_selesai }}
            </td>

            <td class="td">
              <span
                class="px-3 py-1 rounded-full text-xs font-semibold"
                :class="row.status === 'hadir'
                  ? 'bg-green-100 text-green-700'
                  : 'bg-yellow-100 text-yellow-700'"
              >
                {{ row.status }}
              </span>
            </td>

            <td class="td">{{ row.aktivitas || '-' }}</td>
            <td class="td">{{ row.keterangan || '-' }}</td>
            <td class="td">{{ row.tugas_rumah || '-' }}</td>

            <td class="td text-center space-y-2">
              <button
                class="bg-indigo-600 text-white px-3 py-1 rounded text-xs w-full"
                @click="openModal(row)"
              >
                Lihat Catatan
              </button>

              <button
                class="bg-emerald-600 text-white px-3 py-1 rounded text-xs w-full"
                @click="cetak(row)"
              >
                Cetak
              </button>
            </td>
          </tr>

          <tr v-if="paginatedRows.length === 0">
            <td colspan="8" class="text-center py-6 text-gray-400">
              Tidak ada data
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    <div class="flex justify-between items-center">
      <div class="text-sm text-gray-500">
        Halaman {{ currentPage }} dari {{ totalPages }}
      </div>

      <div class="flex gap-1">
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

    <!-- MODAL DETAIL (TIDAK DIUBAH) -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    >
      <div class="bg-white w-full max-w-4xl rounded-lg shadow-lg overflow-hidden">

        <div class="bg-gray-200 px-6 py-3 font-semibold text-sm">
          CATATAN AKTIVITAS SESI TERAPI ANAK
        </div>

        <div class="p-6 space-y-6 text-sm">

          <div class="grid grid-cols-2 gap-4 border p-4 rounded">
            <div>
              <div class="text-gray-500 mb-1">Nama Anak</div>
              <div class="border px-3 py-2 bg-gray-50 rounded">
                {{ selected.nama_anak }}
              </div>
            </div>

            <div>
              <div class="text-gray-500 mb-1">Hari & Tanggal</div>
              <div class="border px-3 py-2 bg-gray-50 rounded">
                {{ selected.hari }}, {{ formatTanggal(selected.tanggal) }}
              </div>
            </div>

            <div>
              <div class="text-gray-500 mb-1">Jam</div>
              <div class="border px-3 py-2 bg-gray-50 rounded">
                {{ selected.jam_mulai }} - {{ selected.jam_selesai }}
              </div>
            </div>

            <div>
              <div class="text-gray-500 mb-1">Terapis</div>
              <div class="border px-3 py-2 bg-gray-50 rounded">
                {{ selected.terapis }}
              </div>
            </div>

            <div class="flex items-end justify-end col-span-2">
              <span class="mr-2">Status :</span>
              <span
                class="px-3 py-1 rounded text-xs font-semibold"
                :class="selected.status === 'hadir'
                  ? 'bg-green-500 text-white'
                  : 'bg-yellow-400 text-white'"
              >
                {{ selected.status }}
              </span>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <div class="font-semibold mb-1">Aktivitas Terapi</div>
              <div class="border p-3 min-h-[120px] bg-gray-50 rounded whitespace-pre-line">
                {{ selected.aktivitas }}
              </div>
            </div>

            <div>
              <div class="font-semibold mb-1">Keterangan Terapi</div>
              <div class="border p-3 min-h-[120px] bg-gray-50 rounded whitespace-pre-line">
                {{ selected.keterangan }}
              </div>
            </div>
          </div>

          <div>
            <div class="font-semibold mb-1">Tugas Rumah</div>
            <div class="border p-3 min-h-[120px] bg-gray-50 rounded whitespace-pre-line">
              {{ selected.tugas_rumah }}
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button
              class="px-4 py-2 border rounded hover:bg-gray-100"
              @click="showModal = false"
            >
              Tutup
            </button>

            <button
              class="px-4 py-2 bg-emerald-600 text-white rounded"
              @click="cetak(selected)"
            >
              Cetak
            </button>
          </div>

        </div>
      </div>
    </div>

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
.th {
  padding: 10px;
  font-weight: 600;
  color: #374151;
  border: 1px solid #d1d5db;
  text-align: center;
}

.td {
  padding: 10px;
  border: 1px solid #e5e7eb;
  vertical-align: top;
}

.page-btn {
  padding: 4px 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 12px;
}

.page-btn.active {
  background: #2563eb;
  color: white;
}
</style>
