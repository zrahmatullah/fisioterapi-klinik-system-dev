<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-100 via-slate-50 to-indigo-100 p-6">

    <!-- HEADER -->
    <div
      class="relative overflow-hidden rounded-[32px]
            bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600
            p-8 shadow-[0_20px_60px_rgba(79,70,229,0.35)]
            mb-6"
    >
      <div
        class="absolute top-0 right-0 w-72 h-72 bg-white/10
              rounded-full blur-3xl"
      ></div>

      <div
        class="absolute bottom-0 left-0 w-72 h-72 bg-white/10
              rounded-full blur-3xl"
      ></div>

      <div
        class="relative flex flex-col lg:flex-row lg:items-center
              lg:justify-between gap-5"
      >
        <div>
          <div
            class="w-16 h-16 rounded-2xl
                  bg-white/15 backdrop-blur-sm
                  border border-white/20
                  flex items-center justify-center mb-5"
          >
            <i class="pi pi-users text-3xl text-white"></i>
          </div>

          <h1 class="text-3xl font-bold text-white">
            Daftar Pasien
          </h1>

          <p class="text-indigo-100 mt-2">
            Kelola registrasi dan penjadwalan terapi pasien
          </p>
        </div>

        <!-- STAT -->
        <div class="grid grid-cols-2 gap-4">
          <div
            class="bg-white/10 backdrop-blur-sm
                  border border-white/15
                  rounded-2xl px-6 py-4"
          >
            <p class="text-indigo-100 text-sm">
              Total Data
            </p>

            <h3 class="text-3xl font-bold text-white mt-1">
              {{ filteredSortedData.length }}
            </h3>
          </div>

          <div
            class="bg-white/10 backdrop-blur-sm
                  border border-white/15
                  rounded-2xl px-6 py-4"
          >
            <p class="text-indigo-100 text-sm">
              Halaman
            </p>

            <h3 class="text-3xl font-bold text-white mt-1">
              {{ currentPage }}
            </h3>
          </div>
        </div>
      </div>
    </div>

    <!-- FILTER -->
    <div
      class="bg-white/90 backdrop-blur-sm
            border border-slate-200
            rounded-[28px]
            shadow-sm
            p-5
            mb-6"
    >
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- SEARCH -->
        <div class="space-y-2">
          <label class="filter-label">
            Cari Pasien
          </label>

          <div class="relative">
            <i
              class="pi pi-search absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"
            ></i>

            <input
              v-model="search"
              placeholder="Cari nama anak / no registrasi..."
              class="filter-input pl-11"
            />
          </div>
        </div>

        <!-- DATE -->
        <div class="space-y-2">
          <label class="filter-label">
            Filter Tanggal
          </label>

          <div class="relative">

            <input
              type="date"
              v-model="filterTanggal"
              class="filter-input pl-11"
            />
          </div>
        </div>

        <!-- PER PAGE -->
        <div class="space-y-2">
          <label class="filter-label">
            Data Per Halaman
          </label>

          <div class="relative">
            <i
              class="pi pi-list absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"
            ></i>

            <input
              type="number"
              min="1"
              v-model.number="perPage"
              class="filter-input pl-11"
            />
          </div>
        </div>

      </div>
    </div>

    <!-- TABLE -->
    <div
      class="bg-white/90 backdrop-blur-sm
            border border-slate-200
            rounded-[30px]
            shadow-[0_10px_40px_rgba(15,23,42,0.06)]
            overflow-hidden"
    >

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-100/80 border-b border-slate-200">
            <tr>
              <th class="th w-16">
                No
              </th>

              <th
                class="th cursor-pointer hover:text-indigo-600"
                @click="setSort('no_regis')"
              >
                No Registrasi {{ sortIcon('no_regis') }}
              </th>

              <th
                class="th cursor-pointer hover:text-indigo-600"
                @click="setSort('tgl_regis')"
              >
                Tanggal {{ sortIcon('tgl_regis') }}
              </th>

              <th
                class="th cursor-pointer hover:text-indigo-600"
                @click="setSort('nama_anak')"
              >
                Nama Anak {{ sortIcon('nama_anak') }}
              </th>

              <th
                class="th cursor-pointer hover:text-indigo-600"
                @click="setSort('ruangan')"
              >
                Ruangan {{ sortIcon('ruangan') }}
              </th>

              <th
                class="th cursor-pointer hover:text-indigo-600"
                @click="setSort('terapis')"
              >
                Terapis {{ sortIcon('terapis') }}
              </th>

              <th class="th text-center">
                Aksi
              </th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(r, i) in paginatedData"
              :key="r.id"
              class="border-b border-slate-100
                    hover:bg-indigo-50/40
                    transition-all duration-200"
            >
              <!-- NO -->
              <td class="td">
                <div
                  class="w-9 h-9 rounded-xl
                        bg-slate-100
                        flex items-center justify-center
                        font-semibold text-slate-700"
                >
                  {{ (currentPage - 1) * perPage + i + 1 }}
                </div>
              </td>

              <!-- NO REG -->
              <td class="td">
                <div>
                  <p class="font-semibold text-indigo-700">
                    {{ r.no_regis }}
                  </p>

                  <p class="text-xs text-slate-400 mt-1">
                    ID #{{ r.id }}
                  </p>
                </div>
              </td>

              <!-- TGL -->
              <td class="td">
                <span
                  class="px-3 py-1 rounded-full
                        bg-indigo-100 text-indigo-700
                        text-xs font-medium"
                >
                  {{ formatDate(r.tgl_regis) }}
                </span>
              </td>

              <!-- PASIEN -->
              <td class="td">
                <div class="flex items-center gap-3">
                  <div
                    class="w-11 h-11 rounded-2xl
                          bg-gradient-to-br from-indigo-500 to-violet-500
                          text-white font-bold
                          flex items-center justify-center"
                  >
                    {{
                      r.profile_anak?.nama_anak
                        ?.charAt(0)
                        ?.toUpperCase()
                    }}
                  </div>

                  <div>
                    <p class="font-semibold text-slate-800">
                      {{ r.profile_anak?.nama_anak || '-' }}
                    </p>

                    <p class="text-xs text-slate-400">
                      Pasien terapi
                    </p>
                  </div>
                </div>
              </td>

              <!-- RUANGAN -->
              <td class="td">
                <span
                  class="inline-flex items-center gap-2
                        px-3 py-1.5 rounded-full
                        bg-slate-100 text-slate-700
                        text-xs font-medium"
                >
                  <span
                    class="w-2 h-2 rounded-full bg-emerald-500"
                  ></span>

                  {{ r.ruangan?.ruangan || '-' }}
                </span>
              </td>

              <!-- TERAPIS -->
              <td class="td">
                <div class="flex items-center gap-2">
                  <div
                    class="w-8 h-8 rounded-xl
                          bg-amber-100 text-amber-600
                          flex items-center justify-center"
                  >
                    <i class="pi pi-user"></i>
                  </div>

                  <span class="font-medium text-slate-700">
                    {{ r.terapis?.nama || '-' }}
                  </span>
                </div>
              </td>

              <!-- ACTION -->
              <td class="td">
                <div class="flex justify-center">
                  <button
                    @click="openModal(r)"
                    class="h-11 px-5 rounded-2xl
                          bg-gradient-to-r from-indigo-600 to-violet-600
                          hover:from-indigo-700 hover:to-violet-700
                          text-white text-sm font-semibold
                          shadow-lg shadow-indigo-200
                          transition-all duration-200
                          hover:scale-[1.03]"
                  >
                    <span class="flex items-center gap-2">
                      <i class="pi pi-calendar-plus"></i>
                      Input Layanan
                    </span>
                  </button>
                </div>
              </td>
            </tr>

            <!-- EMPTY -->
            <tr v-if="paginatedData.length === 0">
              <td colspan="7" class="py-16">
                <div class="flex flex-col items-center justify-center">
                  <div
                    class="w-20 h-20 rounded-full
                          bg-slate-100
                          flex items-center justify-center mb-4"
                  >
                    <i
                      class="pi pi-inbox text-3xl text-slate-400"
                    ></i>
                  </div>

                  <h3 class="text-lg font-bold text-slate-700">
                    Data Tidak Ditemukan
                  </h3>

                  <p class="text-sm text-slate-500 mt-1">
                    Tidak ada pasien sesuai filter pencarian
                  </p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div
        class="bg-slate-50 border-t border-slate-200
              px-6 py-4 flex flex-col lg:flex-row
              items-center justify-between gap-4"
      >
        <div class="text-sm text-slate-500">
          Menampilkan
          <span class="font-semibold text-slate-700">
            {{ paginatedData.length }}
          </span>
          data dari
          <span class="font-semibold text-slate-700">
            {{ filteredSortedData.length }}
          </span>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
          <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="pagination-btn"
          >
            Prev
          </button>

          <button
            v-for="page in totalPages"
            :key="page"
            @click="goToPage(page)"
            class="pagination-btn"
            :class="page === currentPage ? 'active-page' : ''"
          >
            {{ page }}
          </button>

          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="pagination-btn"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL -->
    <Transition name="fade">
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 overflow-y-auto"
      >
        <!-- BACKDROP -->
        <div
          class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
          @click="closeModal"
        ></div>

        <!-- WRAPPER -->
        <div
          class="relative min-h-screen
                flex items-center justify-center
                p-4 lg:p-8"
        >
          <!-- CARD -->
          <div
            class="relative w-full max-w-5xl
                  rounded-[32px]
                  bg-white overflow-hidden
                  border border-slate-200
                  shadow-[0_25px_80px_rgba(15,23,42,0.25)]
                  animate-modal"
          >

            <!-- HEADER -->
            <div
              class="relative overflow-hidden
                    bg-gradient-to-r from-indigo-600
                    via-violet-600 to-fuchsia-600
                    px-8 py-7"
            >
              <div
                class="absolute top-0 right-0 w-64 h-64
                      bg-white/10 rounded-full blur-3xl"
              ></div>

              <div
                class="relative flex items-start justify-between"
              >
                <div>
                  <div
                    class="w-14 h-14 rounded-2xl
                          bg-white/15 backdrop-blur-sm
                          border border-white/20
                          flex items-center justify-center mb-4"
                  >
                    <i
                      class="pi pi-calendar-plus text-2xl text-white"
                    ></i>
                  </div>

                  <h2 class="text-2xl font-bold text-white">
                    Input Jadwal Layanan
                  </h2>

                  <p class="text-indigo-100 mt-1">
                    Atur sesi terapi dan jadwal pasien
                  </p>
                </div>

                <button
                  @click="closeModal"
                  class="w-11 h-11 rounded-2xl
                        bg-white/10 hover:bg-white/20
                        border border-white/20
                        text-white transition"
                >
                  ✕
                </button>
              </div>
            </div>

            <!-- CONTENT -->
            <div
              class="max-h-[75vh] overflow-y-auto
                    bg-slate-50/70 p-8"
            >

              <!-- PASIEN INFO -->
              <div
                class="bg-white rounded-3xl
                      border border-slate-200
                      p-6 shadow-sm mb-6"
              >
                <div class="flex items-center gap-4">
                  <div
                    class="w-16 h-16 rounded-2xl
                          bg-gradient-to-br
                          from-indigo-500 to-violet-500
                          text-white text-2xl font-bold
                          flex items-center justify-center"
                  >
                    {{
                      selectedRegistrasi?.profile_anak?.nama_anak
                        ?.charAt(0)
                        ?.toUpperCase()
                    }}
                  </div>

                  <div>
                    <h3 class="text-xl font-bold text-slate-800">
                      {{
                        selectedRegistrasi?.profile_anak?.nama_anak || '-'
                      }}
                    </h3>

                    <p class="text-slate-500 mt-1">
                      {{ selectedRegistrasi?.no_regis || '-' }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- LAYANAN -->
              <div
                class="bg-white rounded-3xl
                      border border-slate-200
                      p-6 shadow-sm mb-6"
              >
                <label class="section-label">
                  Pilih Layanan
                </label>

                <div class="relative mt-3">
                  <i
                    class="pi pi-briefcase absolute left-4 top-1/2
                          -translate-y-1/2 text-slate-400"
                  ></i>

                  <select
                    v-model="selectedLayananId"
                    class="input pl-11"
                  >
                    <option value="">
                      -- Pilih Layanan --
                    </option>

                    <option
                      v-for="l in layananList"
                      :key="l.id"
                      :value="l.id"
                    >
                      {{ l.layanan }} ({{ l.qty }} sesi)
                    </option>
                  </select>
                </div>
              </div>

              <!-- JADWAL -->
              <div
                v-if="jadwal.length > 0"
                class="bg-white rounded-3xl
                      border border-slate-200
                      p-6 shadow-sm"
              >
                <div
                  class="flex items-center justify-between mb-6"
                >
                  <div>
                    <h3 class="text-lg font-bold text-slate-800">
                      Jadwal Sesi Terapi
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                      Lengkapi tanggal dan jam terapi
                    </p>
                  </div>

                  <div
                    class="px-4 py-2 rounded-2xl
                          bg-indigo-100 text-indigo-700
                          text-sm font-semibold"
                  >
                    {{ jadwal.length }} sesi
                  </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                  <div
                    v-for="(s, i) in jadwal"
                    :key="i"
                    class="rounded-3xl border border-slate-200
                          p-5 bg-slate-50"
                  >
                    <div
                      class="flex items-center gap-3 mb-5"
                    >
                      <div
                        class="w-11 h-11 rounded-2xl
                              bg-indigo-600 text-white
                              flex items-center justify-center
                              font-bold"
                      >
                        {{ i + 1 }}
                      </div>

                      <div>
                        <h4 class="font-semibold text-slate-800">
                          Sesi {{ i + 1 }}
                        </h4>

                        <p class="text-xs text-slate-500">
                          Jadwal terapi pasien
                        </p>
                      </div>
                    </div>

                    <div class="space-y-4">

                      <!-- TANGGAL -->
                      <div class="space-y-2">
                        <label class="field-label">
                          Tanggal
                        </label>

                        <input
                          type="date"
                          v-model="s.tanggal"
                          class="input"
                        />
                      </div>

                      <!-- JAM -->
                      <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                          <label class="field-label">
                            Jam Mulai
                          </label>

                          <input
                            type="time"
                            v-model="s.jam_mulai"
                            class="input"
                          />
                        </div>

                        <div class="space-y-2">
                          <label class="field-label">
                            Jam Selesai
                          </label>

                          <input
                            type="time"
                            v-model="s.jam_selesai"
                            class="input"
                          />
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- FOOTER -->
            <div
              class="bg-white border-t border-slate-200
                    px-8 py-5 flex justify-end gap-3"
            >
              <button
                @click="closeModal"
                class="h-12 px-6 rounded-2xl
                      bg-slate-100 hover:bg-slate-200
                      text-slate-700 font-medium transition"
              >
                Batal
              </button>

              <button
                @click="submit"
                :disabled="!canSubmit"
                class="h-12 px-7 rounded-2xl
                      bg-gradient-to-r from-indigo-600 to-violet-600
                      hover:from-indigo-700 hover:to-violet-700
                      disabled:opacity-40 disabled:cursor-not-allowed
                      text-white font-semibold
                      shadow-lg shadow-indigo-200
                      transition-all duration-200"
              >
                <span class="flex items-center gap-2">
                  <i class="pi pi-check"></i>
                  Simpan Jadwal
                </span>
              </button>
            </div>

          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import api from '@/axios'

const registrasiList = ref([])
const layananList = ref([])

const showModal = ref(false)

const selectedRegistrasi = ref(null)
const selectedLayananId = ref('')
const selectedLayanan = ref(null)

const jadwal = ref([])

const search = ref('')

const sortBy = ref('tgl_regis')
const sortOrder = ref('desc')

const currentPage = ref(1)
const perPage = ref(10)

const filterTanggal = ref(
  new Date().toISOString().slice(0, 10)
)

onMounted(async () => {
  try {
    const registrasiRes = await api.get('/registrasi-anak')
    const layananRes = await api.get('/layanan')

    registrasiList.value = registrasiRes.data || []
    layananList.value = layananRes.data || []
  } catch (error) {
    console.error(error)
  }
})

watch(perPage, (val) => {
  if (!val || val < 1) {
    perPage.value = 1
  }

  currentPage.value = 1
})

watch(selectedLayananId, (id) => {
  selectedLayanan.value =
    layananList.value.find(l => l.id == id) || null

  jadwal.value = Array.from(
    {
      length: selectedLayanan.value?.qty || 0
    },
    () => ({
      tanggal: '',
      jam_mulai: '',
      jam_selesai: ''
    })
  )
})

const setSort = (field) => {
  if (sortBy.value === field) {
    sortOrder.value =
      sortOrder.value === 'asc'
        ? 'desc'
        : 'asc'
  } else {
    sortBy.value = field
    sortOrder.value = 'asc'
  }
}

const sortIcon = (field) => {
  if (sortBy.value !== field) {
    return '⇅'
  }

  return sortOrder.value === 'asc'
    ? '⬆️'
    : '⬇️'
}

const filteredSortedData = computed(() => {
  const keyword = search.value.toLowerCase()

  const data = registrasiList.value.filter((r) => {
    const cocokTanggal =
      r?.tgl_regis?.slice(0, 10) === filterTanggal.value

    const cocokSearch =
      r?.no_regis
        ?.toLowerCase()
        ?.includes(keyword) ||
      r?.profile_anak?.nama_anak
        ?.toLowerCase()
        ?.includes(keyword)

    return cocokTanggal && cocokSearch
  })

  data.sort((a, b) => {
    let va
    let vb

    switch (sortBy.value) {
      case 'nama_anak':
        va = a?.profile_anak?.nama_anak || ''
        vb = b?.profile_anak?.nama_anak || ''
        break

      case 'ruangan':
        va = a?.ruangan?.ruangan || ''
        vb = b?.ruangan?.ruangan || ''
        break

      case 'terapis':
        va = a?.terapis?.nama || ''
        vb = b?.terapis?.nama || ''
        break

      case 'tgl_regis':
        va = new Date(a?.tgl_regis)
        vb = new Date(b?.tgl_regis)
        break

      default:
        va = a?.[sortBy.value] || ''
        vb = b?.[sortBy.value] || ''
    }

    if (va < vb) {
      return sortOrder.value === 'asc'
        ? -1
        : 1
    }

    if (va > vb) {
      return sortOrder.value === 'asc'
        ? 1
        : -1
    }

    return 0
  })

  return data
})

const totalPages = computed(() => {
  return Math.max(
    1,
    Math.ceil(
      filteredSortedData.value.length /
      perPage.value
    )
  )
})

const paginatedData = computed(() => {
  const start =
    (currentPage.value - 1) * perPage.value

  return filteredSortedData.value.slice(
    start,
    start + perPage.value
  )
})

const goToPage = (page) => {
  currentPage.value = page
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) {
    return '-'
  }

  const d = new Date(dateStr)

  return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
}

const openModal = (r) => {
  selectedRegistrasi.value = r

  showModal.value = true

  selectedLayananId.value = ''

  selectedLayanan.value = null

  jadwal.value = []
}

const closeModal = () => {
  showModal.value = false
}

const canSubmit = computed(() => {
  return (
    jadwal.value.length > 0 &&
    jadwal.value.every((j) => {
      return (
        j.tanggal &&
        j.jam_mulai &&
        j.jam_selesai
      )
    })
  )
})

const submit = async () => {
  try {
    await api.post('/pelayanan-terapi-anak', {
      registrasi_anak_id:
        selectedRegistrasi.value?.id,

      layanan_id:
        selectedLayananId.value,

      tanggal_penjadwalan:
        jadwal.value
    })

    closeModal()
  } catch (error) {
    console.error(error)
  }
}
</script>

<style scoped>
.th {
  @apply px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-600;
}

.td {
  @apply px-6 py-4 text-slate-700 align-middle;
}

.filter-label {
  @apply text-sm font-semibold text-slate-700;
}

.section-label {
  @apply text-lg font-bold text-slate-800;
}

.field-label {
  @apply text-sm font-medium text-slate-600;
}

.filter-input,
.input {
  @apply w-full h-12 rounded-2xl
         border border-slate-200
         bg-white px-4 text-sm
         shadow-sm transition-all duration-200
         focus:ring-4 focus:ring-indigo-100
         focus:border-indigo-500
         focus:outline-none;
}

.pagination-btn {
  @apply min-w-[40px] h-10 px-3 rounded-xl
         border border-slate-200 bg-white
         hover:bg-indigo-50 hover:border-indigo-300
         text-sm font-medium text-slate-700
         transition-all duration-200
         disabled:opacity-40 disabled:cursor-not-allowed;
}

.active-page {
  @apply bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity .25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.animate-modal {
  animation: modalShow .25s ease;
}

@keyframes modalShow {
  from {
    opacity: 0;
    transform: translateY(20px) scale(.97);
  }

  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 999px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>