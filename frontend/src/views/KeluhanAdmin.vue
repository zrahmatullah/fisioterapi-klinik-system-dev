<template>
  <div class="min-h-screen bg-slate-100 p-6">
    <!-- HEADER -->
    <div
      class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 p-8 shadow-2xl mb-6"
    >
      <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
        <div>
          <h1 class="text-3xl font-bold text-white">
            Keluhan Orang Tua
          </h1>
          <p class="text-indigo-100 mt-1">
            Monitoring dan tanggapan keluhan orang tua siswa
          </p>
        </div>

        <div class="flex gap-4 flex-wrap">
          <div class="bg-white/15 backdrop-blur-md rounded-2xl px-5 py-3 border border-white/20">
            <div class="text-white text-sm">Total Keluhan</div>
            <div class="text-2xl font-bold text-white">
              {{ keluhan.length }}
            </div>
          </div>

          <div class="bg-white/15 backdrop-blur-md rounded-2xl px-5 py-3 border border-white/20">
            <div class="text-white text-sm">Sudah Ditanggapi</div>
            <div class="text-2xl font-bold text-white">
              {{ sudahDitanggapi }}
            </div>
          </div>

          <div class="bg-white/15 backdrop-blur-md rounded-2xl px-5 py-3 border border-white/20">
            <div class="text-white text-sm">Belum Ditanggapi</div>
            <div class="text-2xl font-bold text-white">
              {{ belumDitanggapi }}
            </div>
          </div>
        </div>
      </div>

      <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 left-0 w-52 h-52 bg-pink-400/20 rounded-full blur-3xl"></div>
    </div>

    <!-- FILTER -->
    <div class="bg-white rounded-3xl shadow-lg border border-slate-200 p-5 mb-6">
      <div class="flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between">
        <div class="flex flex-col md:flex-row gap-4 flex-1">
          <!-- SEARCH -->
          <div class="relative flex-1">
            <svg
              class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-4.35-4.35M16 10.5a5.5 5.5 0 11-11 0 5.5 5.5 0 0111 0z"
              />
            </svg>

            <input
              v-model="search"
              type="text"
              placeholder="Cari No Keluhan / Nama Anak..."
              class="w-full pl-12 pr-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition"
            />
          </div>

          <!-- FILTER -->
          <select
            v-model="filterStatus"
            class="px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition"
          >
            <option value="">Semua Status</option>
            <option value="belum">Belum Ditanggapi</option>
            <option value="sudah">Sudah Ditanggapi</option>
          </select>
        </div>

        <div class="text-sm text-slate-500 font-medium">
          Total Data : {{ filteredKeluhan.length }}
        </div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-200">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-100 text-slate-700">
            <tr>
              <th class="px-5 py-4 text-center font-semibold">No</th>
              <th class="px-5 py-4 text-left font-semibold">No Keluhan</th>
              <th class="px-5 py-4 text-left font-semibold">Nama Anak</th>

              <!-- SORT KATEGORI -->
              <th
                class="px-5 py-4 text-left font-semibold cursor-pointer hover:bg-slate-200 transition"
                @click="toggleSortKategori"
              >
                <div class="flex items-center gap-2">
                  Kategori
                  <span class="text-xs">
                    {{
                      sortKategori === 'desc'
                        ? '⬇'
                        : sortKategori === 'asc'
                        ? '⬆'
                        : '⇅'
                    }}
                  </span>
                </div>
              </th>

              <!-- SORT TANGGAL -->
              <th
                class="px-5 py-4 text-left font-semibold cursor-pointer hover:bg-slate-200 transition"
                @click="toggleSortTanggal"
              >
                <div class="flex items-center gap-2">
                  Tanggal
                  <span class="text-xs">
                    {{
                      sortTanggal === 'desc'
                        ? '⬇'
                        : sortTanggal === 'asc'
                        ? '⬆'
                        : '⇅'
                    }}
                  </span>
                </div>
              </th>

              <th class="px-5 py-4 text-center font-semibold">Status</th>
              <th class="px-5 py-4 text-center font-semibold">Aksi</th>
              <th class="px-5 py-4 text-center font-semibold">Cetak</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(k, i) in paginatedKeluhan"
              :key="k.id"
              class="border-t border-slate-100 hover:bg-indigo-50/40 transition duration-200"
            >
              <td class="px-5 py-4 text-center font-medium text-slate-600">
                {{ (currentPage - 1) * perPage + i + 1 }}
              </td>

              <td class="px-5 py-4">
                <div class="font-semibold text-slate-800">
                  {{ k.no_keluhan }}
                </div>
              </td>

              <td class="px-5 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 flex items-center justify-center text-white font-bold"
                  >
                    {{ (k.anak?.nama_anak || 'A').charAt(0) }}
                  </div>

                  <div>
                    <div class="font-semibold text-slate-800">
                      {{ k.anak?.nama_anak || '-' }}
                    </div>
                  </div>
                </div>
              </td>

              <td class="px-5 py-4">
                <span
                  class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold"
                >
                  {{ k.kategori_keluhan }}
                </span>
              </td>

              <td class="px-5 py-4 text-slate-600">
                {{ formatDate(k.tanggal_keluhan) }}
              </td>

              <td class="px-5 py-4 text-center">
                <span
                  class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold"
                  :class="
                    k.status_tanggapan === 'sudah_ditanggapi'
                      ? 'bg-emerald-100 text-emerald-700'
                      : 'bg-orange-100 text-orange-700'
                  "
                >
                  <span
                    class="w-2 h-2 rounded-full"
                    :class="
                      k.status_tanggapan === 'sudah_ditanggapi'
                        ? 'bg-emerald-500'
                        : 'bg-orange-500'
                    "
                  ></span>

                  {{
                    k.status_tanggapan === 'sudah_ditanggapi'
                      ? 'Sudah Ditanggapi'
                      : 'Belum Ditanggapi'
                  }}
                </span>
              </td>

              <td class="px-5 py-4 text-center">
                <button
                  class="px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-medium shadow hover:scale-105 transition"
                  @click="openDetail(k)"
                >
                  Detail
                </button>
              </td>

              <td class="px-5 py-4 text-center">
                <button
                  class="px-4 py-2 rounded-xl text-white font-medium shadow transition"
                  :class="
                    k.status_tanggapan === 'sudah_ditanggapi'
                      ? 'bg-emerald-600 hover:bg-emerald-700 hover:scale-105'
                      : 'bg-slate-300 cursor-not-allowed'
                  "
                  :disabled="k.status_tanggapan !== 'sudah_ditanggapi'"
                  @click="cetakKeluhan(k)"
                >
                  Cetak
                </button>
              </td>
            </tr>

            <tr v-if="filteredKeluhan.length === 0">
              <td colspan="8" class="py-16 text-center">
                <div class="flex flex-col items-center">
                  <div
                    class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4"
                  >
                    📭
                  </div>

                  <div class="text-slate-500 text-lg font-semibold">
                    Tidak ada data keluhan
                  </div>

                  <div class="text-slate-400 text-sm mt-1">
                    Data akan muncul di sini
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div
        class="flex flex-col md:flex-row gap-4 md:items-center md:justify-between px-6 py-5 border-t border-slate-100 bg-slate-50"
      >
        <div class="text-sm text-slate-600 font-medium">
          Halaman {{ currentPage }} dari {{ totalPages }}
        </div>

        <div class="flex items-center gap-2 flex-wrap">
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

    <!-- MODAL -->
    <Transition name="fade">
      <div
        v-if="activeKeluhan"
        class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
      >
        <div
          class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden"
        >
          <!-- HEADER -->
          <div
            class="bg-gradient-to-r from-indigo-600 to-violet-600 p-6 text-white"
          >
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-2xl font-bold">
                  Detail Keluhan
                </h3>

                <p class="text-indigo-100 mt-1">
                  {{ activeKeluhan.no_keluhan }}
                </p>
              </div>

              <button
                @click="activeKeluhan = null"
                class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 transition"
              >
                ✕
              </button>
            </div>
          </div>

          <!-- CONTENT -->
          <div class="p-6 space-y-5">
            <!-- KELUHAN -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
              <div class="flex items-center gap-2 mb-3">
                <div
                  class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center"
                >
                  📝
                </div>

                <h4 class="font-bold text-slate-800">
                  Isi Keluhan
                </h4>
              </div>

              <p class="text-slate-600 leading-relaxed whitespace-pre-line">
                {{ activeKeluhan.isi }}
              </p>
            </div>

            <!-- TANGGAPAN -->
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">
                Tanggapan Admin
              </label>

              <textarea
                v-model="tanggapan"
                rows="5"
                placeholder="Masukkan tanggapan..."
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition"
              ></textarea>
            </div>

            <!-- ACTION -->
            <div class="flex justify-end gap-3 pt-2">
              <button
                class="px-5 py-3 rounded-2xl border border-slate-300 hover:bg-slate-100 transition"
                @click="activeKeluhan = null"
              >
                Batal
              </button>

              <button
                class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-green-600 text-white font-semibold shadow-lg hover:scale-105 transition"
                @click="kirimTanggapan"
              >
                Simpan Tanggapan
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
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

async function loadData() {
  const res = await api.get('/admin/keluhan-anak')
  keluhan.value = res.data
}

const sudahDitanggapi = computed(() =>
  keluhan.value.filter(k => k.status_tanggapan === 'sudah_ditanggapi').length
)

const belumDitanggapi = computed(() =>
  keluhan.value.filter(k => k.status_tanggapan === 'belum_ditanggapi').length
)

const toggleSortTanggal = () => {
  sortKategori.value = ''
  sortTanggal.value =
    sortTanggal.value === ''
      ? 'desc'
      : sortTanggal.value === 'desc'
      ? 'asc'
      : ''
}

const toggleSortKategori = () => {
  sortTanggal.value = ''
  sortKategori.value =
    sortKategori.value === ''
      ? 'desc'
      : sortKategori.value === 'desc'
      ? 'asc'
      : ''
}

const filteredKeluhan = computed(() => {
  let data = keluhan.value.filter(k => {
    const matchSearch =
      k.no_keluhan?.toLowerCase().includes(search.value.toLowerCase()) ||
      (k.anak?.nama_anak || '')
        .toLowerCase()
        .includes(search.value.toLowerCase())

    const matchStatus =
      filterStatus.value === ''
        ? true
        : filterStatus.value === 'sudah'
        ? k.status_tanggapan === 'sudah_ditanggapi'
        : k.status_tanggapan === 'belum_ditanggapi'

    return matchSearch && matchStatus
  })

  if (sortTanggal.value) {
    data.sort((a, b) =>
      sortTanggal.value === 'desc'
        ? new Date(b.tanggal_keluhan) - new Date(a.tanggal_keluhan)
        : new Date(a.tanggal_keluhan) - new Date(b.tanggal_keluhan)
    )
  }

  if (sortKategori.value) {
    const count = {}

    data.forEach(d => {
      count[d.kategori_keluhan] =
        (count[d.kategori_keluhan] || 0) + 1
    })

    data.sort((a, b) => {
      const ca = count[a.kategori_keluhan]
      const cb = count[b.kategori_keluhan]

      return sortKategori.value === 'desc'
        ? cb - ca
        : ca - cb
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
    await api.post(
      `/admin/keluhan-anak/${activeKeluhan.value.id}/tanggapi`,
      {
        tanggapan_keluhan: tanggapan.value
      }
    )

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

  window.open(
    `${import.meta.env.VITE_API_BASE_URL}/api/cetak/keluhan-anak/${k.id}`,
    '_blank'
  )
}

const formatDate = (d) =>
  new Date(d).toLocaleDateString('id-ID')
</script>

<style scoped>
.page-btn {
  @apply px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 font-medium hover:bg-indigo-50 hover:border-indigo-300 transition disabled:opacity-40 disabled:cursor-not-allowed;
}

.page-btn.active {
  @apply bg-gradient-to-r from-indigo-600 to-violet-600 text-white border-transparent;
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>