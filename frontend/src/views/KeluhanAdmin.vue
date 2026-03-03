<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <h2 class="text-2xl font-bold mb-6 text-indigo-700">
      Keluhan Orang Tua (Admin)
    </h2>

    <!-- FILTER -->
    <div class="flex gap-4 mb-4 flex-wrap">
      <select v-model="filterStatus" class="input w-60">
        <option value="">Semua Status</option>
        <option value="belum">Belum Ditanggapi</option>
        <option value="sudah">Sudah Ditanggapi</option>
      </select>

      <input
        v-model="search"
        placeholder="Cari No Keluhan/ Nama Anak..."
        class="input w-64"
      />
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow p-4">
      <table class="w-full text-sm border">
        <thead class="bg-gray-100">
          <tr>
            <th class="border px-3 py-2">No</th>
            <th class="border px-3 py-2">No Keluhan</th>
            <th class="border px-3 py-2">Nama Anak</th>

            <!-- SORT KATEGORI -->
            <th
              class="border px-3 py-2 cursor-pointer select-none hover:bg-gray-200"
              @click="toggleSortKategori"
            >
              Kategori
              <span class="ml-1 text-xs">
                {{ sortKategori === 'desc' ? '⬇' : sortKategori === 'asc' ? '⬆' : '⇅' }}
              </span>
            </th>

            <!-- SORT TANGGAL -->
            <th
              class="border px-3 py-2 cursor-pointer select-none hover:bg-gray-200"
              @click="toggleSortTanggal"
            >
              Tanggal Tanggapan
              <span class="ml-1 text-xs">
                {{ sortTanggal === 'desc' ? '⬇' : sortTanggal === 'asc' ? '⬆' : '⇅' }}
              </span>
            </th>

            <th class="border px-3 py-2">Status</th>
            <th class="border px-3 py-2">Aksi</th>
            <th class="border px-3 py-2">Cetak</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(k, i) in paginatedKeluhan"
            :key="k.id"
            class="hover:bg-gray-50"
          >
            <td class="border px-3 py-2 text-center">
              {{ (currentPage - 1) * perPage + i + 1 }}
            </td>
            <td class="border px-3 py-2">{{ k.no_keluhan }}</td>
            <td class="border px-3 py-2">{{ k.anak?.nama_anak || '-' }}</td>
            <td class="border px-3 py-2">{{ k.kategori_keluhan }}</td>
            <td class="border px-3 py-2">{{ formatDate(k.tanggal_keluhan) }}</td>

            <td class="border px-3 py-2 text-center">
              <span
                class="text-xs px-3 py-1 rounded-full"
                :class="
                  k.status_tanggapan === 'sudah_ditanggapi'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-orange-100 text-orange-700'
                "
              >
                {{ k.status_tanggapan === 'sudah_ditanggapi'
                  ? 'Sudah Ditanggapi'
                  : 'Belum Ditanggapi' }}
              </span>
            </td>

            <td class="border px-3 py-2 text-center">
              <button
                class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700"
                @click="openDetail(k)"
              >
                Detail
              </button>
            </td>

            <td class="border px-3 py-2 text-center">
              <button
                class="px-3 py-2 rounded text-white"
                :class="
                  k.status_tanggapan === 'sudah_ditanggapi'
                    ? 'bg-green-600 hover:bg-green-700'
                    : 'bg-gray-400 cursor-not-allowed'
                "
                :disabled="k.status_tanggapan !== 'sudah_ditanggapi'"
                @click="cetakKeluhan(k)"
              >
                Cetak
              </button>
            </td>
          </tr>

          <tr v-if="filteredKeluhan.length === 0">
            <td colspan="8" class="text-center py-6 text-gray-400">
              Tidak ada data keluhan
            </td>
          </tr>
        </tbody>
      </table>

      <!-- PAGINATION -->
      <div class="flex justify-between items-center mt-4 text-sm">
        <div>Halaman {{ currentPage }} dari {{ totalPages }}</div>

        <div class="flex gap-1">
          <button class="page-btn" :disabled="currentPage===1" @click="currentPage--">Prev</button>

          <button
            v-for="p in totalPages"
            :key="p"
            class="page-btn"
            :class="{ active: p===currentPage }"
            @click="currentPage=p"
          >
            {{ p }}
          </button>

          <button class="page-btn" :disabled="currentPage===totalPages" @click="currentPage++">Next</button>
        </div>
      </div>
    </div>

    <!-- MODAL DETAIL (TETAP) -->
    <div v-if="activeKeluhan" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-lg space-y-4">
        <div class="border rounded-xl p-4 bg-blue-50">
          <h3 class="font-semibold text-blue-700 mb-2">Isi Keluhan</h3>
          <p class="text-sm whitespace-pre-line">{{ activeKeluhan.isi }}</p>
        </div>

        <div class="border rounded-xl p-4">
          <h3 class="font-semibold mb-2">Tanggapan Admin</h3>

          <textarea v-model="tanggapan" rows="4" class="input"></textarea>

          <div class="flex justify-end gap-2 mt-4">
            <button class="px-4 py-2 border rounded" @click="activeKeluhan=null">Batal</button>
            <button class="px-4 py-2 bg-green-600 text-white rounded" @click="kirimTanggapan">
              Simpan Tanggapan
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const keluhan = ref([])
const search = ref('')
const filterStatus = ref('')
const activeKeluhan = ref(null)
const tanggapan = ref('')

const currentPage = ref(1)
const perPage = 20

const sortTanggal = ref('')
const sortKategori = ref('')

onMounted(loadData)

async function loadData () {
  const res = await api.get('/admin/keluhan-anak')
  keluhan.value = res.data
}

const toggleSortTanggal = () => {
  sortKategori.value = ''
  sortTanggal.value = sortTanggal.value === '' ? 'desc' : sortTanggal.value === 'desc' ? 'asc' : ''
}

const toggleSortKategori = () => {
  sortTanggal.value = ''
  sortKategori.value = sortKategori.value === '' ? 'desc' : sortKategori.value === 'desc' ? 'asc' : ''
}

const filteredKeluhan = computed(() => {
  let data = keluhan.value.filter(k => {
    const matchSearch =
      k.no_keluhan?.toLowerCase().includes(search.value.toLowerCase()) ||
      (k.anak?.nama_anak || '').toLowerCase().includes(search.value.toLowerCase())

    const matchStatus =
      filterStatus.value === ''
        ? true
        : filterStatus.value === 'sudah'
          ? k.status_tanggapan === 'sudah_ditanggapi'
          : k.status_tanggapan === 'belum_ditanggapi'

    return matchSearch && matchStatus
  })

  if (sortTanggal.value) {
    data.sort((a,b)=> sortTanggal.value==='desc'
      ? new Date(b.tanggal_keluhan)-new Date(a.tanggal_keluhan)
      : new Date(a.tanggal_keluhan)-new Date(b.tanggal_keluhan))
  }

  if (sortKategori.value) {
    const count = {}
    data.forEach(d => count[d.kategori_keluhan] = (count[d.kategori_keluhan]||0)+1)

    data.sort((a,b)=>{
      const ca = count[a.kategori_keluhan]
      const cb = count[b.kategori_keluhan]
      return sortKategori.value==='desc' ? cb-ca : ca-cb
    })
  }

  return data
})

const totalPages = computed(() =>
  Math.ceil(filteredKeluhan.value.length / perPage)
)

const paginatedKeluhan = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredKeluhan.value.slice(start, start + perPage)
})

const openDetail = (k) => {
  activeKeluhan.value = k
  tanggapan.value = k.tanggapan_keluhan || ''
}

const kirimTanggapan = async () => {
  try {
    await api.post(`/admin/keluhan-anak/${activeKeluhan.value.id}/tanggapi`, {
      tanggapan_keluhan: tanggapan.value
    })
    toast.success('Tanggapan berhasil disimpan')
    activeKeluhan.value = null
    await loadData()
  } catch {
    toast.error('Gagal menyimpan tanggapan')
  }
}

const cetakKeluhan = (k) => {
  if (k.status_tanggapan !== 'sudah_ditanggapi') {
    toast.warning('Keluhan belum ditanggapi')
    return
  }

  window.open(`${import.meta.env.VITE_API_BASE_URL}/api/cetak/keluhan-anak/${k.id}`, '_blank')
}

const formatDate = (d) => new Date(d).toLocaleDateString('id-ID')
</script>

<style scoped>
.input {
  @apply w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-400;
}
.page-btn {
  @apply px-3 py-1 border rounded text-sm hover:bg-gray-100 disabled:opacity-40;
}
.page-btn.active {
  @apply bg-indigo-600 text-white border-indigo-600;
}
</style>
