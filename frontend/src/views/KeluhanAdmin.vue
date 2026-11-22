<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-comments"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Keluhan Orang Tua & Pasien</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Monitoring komplain, kendala sesi terapi anak, dan tanggapan admin</p>
        </div>
      </div>

      <!-- METRICS PILLS -->
      <div class="flex items-center gap-2 flex-wrap">
        <div class="bg-[#F0F6F8] border border-[#DCE8EC] px-3 py-1.5 rounded-lg flex items-center gap-2">
          <span class="text-[11px] text-slate-500 font-medium">Total:</span>
          <span class="text-xs font-bold text-slate-900 tabular-nums">{{ keluhan.length }}</span>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg flex items-center gap-2">
          <span class="text-[11px] text-emerald-700 font-medium">Ditanggapi:</span>
          <span class="text-xs font-bold text-emerald-800 tabular-nums">{{ sudahDitanggapi }}</span>
        </div>
        <div class="bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-lg flex items-center gap-2">
          <span class="text-[11px] text-amber-700 font-medium">Belum:</span>
          <span class="text-xs font-bold text-amber-800 tabular-nums">{{ belumDitanggapi }}</span>
        </div>
      </div>
    </div>

    <!-- FILTER TOOLBAR -->
    <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-2.5 shrink-0">
      <div class="flex items-center gap-2 flex-1">
        <div class="relative flex-1 max-w-sm">
          <i class="pi pi-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
          <input
            v-model="search"
            type="text"
            placeholder="Cari nomor keluhan / nama anak..."
            class="w-full pl-8 pr-3 h-[34px] text-[12.5px] bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
          />
        </div>

        <select
          v-model="filterStatus"
          class="h-[34px] px-3 text-[12.5px] bg-white border border-slate-300 rounded-lg text-slate-700 focus:outline-none focus:border-[#0F766E]"
        >
          <option value="">Semua Status</option>
          <option value="sudah">Sudah Ditanggapi</option>
          <option value="belum">Belum Ditanggapi</option>
        </select>
      </div>

      <div class="flex items-center gap-1.5">
        <button
          type="button"
          @click="toggleSortTanggal"
          class="h-[34px] px-3 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-medium inline-flex items-center gap-1 transition"
          :class="{ 'bg-teal-50 border-[#0F766E] text-[#0F766E]': sortTanggal }"
        >
          <i class="pi pi-calendar text-xs"></i>
          <span>Tanggal</span>
        </button>
        <button
          type="button"
          @click="toggleSortKategori"
          class="h-[34px] px-3 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-medium inline-flex items-center gap-1 transition"
          :class="{ 'bg-teal-50 border-[#0F766E] text-[#0F766E]': sortKategori }"
        >
          <i class="pi pi-tags text-xs"></i>
          <span>Kategori</span>
        </button>
      </div>
    </div>

    <!-- TABLE CARD -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-left border-collapse text-[12.5px]">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3">No Keluhan</th>
              <th class="py-2.5 px-3">Nama Pasien Anak</th>
              <th class="py-2.5 px-3">Kategori</th>
              <th class="py-2.5 px-3">Tanggal</th>
              <th class="py-2.5 px-3 text-center">Status</th>
              <th class="py-2.5 px-3 text-center w-36">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="k in paginatedKeluhan"
              :key="k.id"
              class="hover:bg-[#F0F6F8]/60 transition"
            >
              <td class="py-2 px-3 font-mono font-semibold text-[#0F766E] text-xs">
                {{ k.no_keluhan }}
              </td>

              <td class="py-2 px-3">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9] flex items-center justify-center font-bold text-xs shrink-0">
                    {{ (k.anak?.nama_anak || 'A').charAt(0) }}
                  </div>
                  <span class="font-semibold text-slate-800">{{ k.anak?.nama_anak || '-' }}</span>
                </div>
              </td>

              <td class="py-2 px-3">
                <span class="px-2 py-0.5 rounded-full bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9] text-[10.5px] font-semibold">
                  {{ k.kategori_keluhan }}
                </span>
              </td>

              <td class="py-2 px-3 text-slate-600 tabular-nums">
                {{ formatDate(k.tanggal_keluhan) }}
              </td>

              <td class="py-2 px-3 text-center">
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                  :class="k.status_tanggapan === 'sudah_ditanggapi' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="k.status_tanggapan === 'sudah_ditanggapi' ? 'bg-emerald-600' : 'bg-amber-600'"></span>
                  {{ k.status_tanggapan === 'sudah_ditanggapi' ? 'Sudah Ditanggapi' : 'Belum Ditanggapi' }}
                </span>
              </td>

              <td class="py-2 px-3 text-center">
                <div class="inline-flex items-center gap-1.5">
                  <button
                    type="button"
                    class="h-[28px] px-2.5 rounded-lg bg-[#0F766E] text-white text-xs font-medium hover:bg-[#115E59] active:scale-95 transition shadow-2xs cursor-pointer inline-flex items-center gap-1"
                    @click="openDetail(k)"
                  >
                    <i class="pi pi-comment text-[11px]"></i>
                    <span>Tanggapi</span>
                  </button>

                  <button
                    type="button"
                    class="h-[28px] px-2.5 rounded-lg border text-xs font-medium transition inline-flex items-center gap-1"
                    :class="k.status_tanggapan === 'sudah_ditanggapi' ? 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50 cursor-pointer' : 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed'"
                    :disabled="k.status_tanggapan !== 'sudah_ditanggapi'"
                    @click="cetakKeluhan(k)"
                  >
                    <i class="pi pi-print text-[11px]"></i>
                    <span>Cetak</span>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="filteredKeluhan.length === 0">
              <td colspan="6" class="py-12 text-center text-slate-400">
                <i class="pi pi-inbox text-2xl text-slate-300 block mb-1"></i>
                <span class="text-xs">Tidak ada data keluhan</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- FOOTER PAGINATION -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] text-slate-500 flex items-center justify-between">
        <span>Menampilkan {{ paginatedKeluhan.length }} dari {{ filteredKeluhan.length }} keluhan</span>
        <div class="flex items-center gap-1" v-if="totalPages > 1">
          <button
            type="button"
            :disabled="currentPage === 1"
            @click="currentPage--"
            class="h-7 px-2 rounded border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed text-xs"
          >
            Prev
          </button>
          <span class="px-2 text-xs font-medium text-slate-700">{{ currentPage }} / {{ totalPages }}</span>
          <button
            type="button"
            :disabled="currentPage === totalPages"
            @click="currentPage++"
            class="h-7 px-2 rounded border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed text-xs"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL DETAIL & TANGGAPAN -->
    <teleport to="body">
      <div
        v-if="activeKeluhan"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-xl rounded-xl max-h-[88dvh] flex flex-col overflow-hidden">
          <!-- HEADER -->
          <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-comments text-[#2DD4BF] text-sm"></i>
              <div>
                <h3 class="font-semibold text-[13px] leading-tight">Detail & Tanggapan Keluhan</h3>
                <p class="text-[10px] text-teal-200 font-mono">{{ activeKeluhan.no_keluhan }}</p>
              </div>
            </div>
            <button
              type="button"
              @click="activeKeluhan = null"
              class="w-6 h-6 rounded flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition"
            >
              ✕
            </button>
          </div>

          <!-- BODY -->
          <div class="p-4 flex-1 min-h-0 overflow-y-auto custom-scrollbar space-y-3.5 text-xs text-slate-700">
            <!-- INFO -->
            <div class="bg-[#F0F6F8] border border-[#DCE8EC] rounded-xl p-3 grid grid-cols-2 gap-2 text-xs">
              <div>
                <span class="text-[10px] text-slate-500 block">Nama Anak</span>
                <strong class="text-slate-900">{{ activeKeluhan.anak?.nama_anak || '-' }}</strong>
              </div>
              <div>
                <span class="text-[10px] text-slate-500 block">Kategori Keluhan</span>
                <strong class="text-[#0F766E]">{{ activeKeluhan.kategori_keluhan }}</strong>
              </div>
            </div>

            <!-- ISI KELUHAN -->
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
              <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                Isi Keluhan Orang Tua
              </label>
              <p class="text-slate-800 leading-relaxed whitespace-pre-line">
                {{ activeKeluhan.isi }}
              </p>
            </div>

            <!-- TANGGAPAN -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Tanggapan Admin / Petugas Klinik
              </label>
              <textarea
                v-model="tanggapan"
                rows="4"
                placeholder="Tuliskan tanggapan atau solusi untuk orang tua..."
                class="w-full p-2.5 text-xs rounded-lg border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 focus:border-[#0F766E] transition custom-scrollbar"
              ></textarea>
            </div>
          </div>

          <!-- FOOTER -->
          <div class="px-4 py-2.5 bg-slate-50/95 border-t border-slate-200/80 flex items-center justify-end gap-2 shrink-0">
            <button
              type="button"
              class="h-[34px] px-3.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-medium transition"
              @click="activeKeluhan = null"
            >
              Batal
            </button>
            <button
              type="button"
              class="h-[34px] px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white text-xs font-medium inline-flex items-center gap-1.5 shadow-sm transition"
              @click="kirimTanggapan"
            >
              <i class="pi pi-check text-xs"></i>
              <span>Simpan Tanggapan</span>
            </button>
          </div>
        </div>
      </div>
    </teleport>
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
  try {
    const res = await api.get('/admin/keluhan-anak')
    keluhan.value = res.data
  } catch (err) {
    console.error('Gagal mengambil keluhan:', err)
  }
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
  Math.ceil(filteredKeluhan.value.length / perPage) || 1
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
    `${import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'}/api/cetak/keluhan-anak/${k.id}`,
    '_blank'
  )
}

const formatDate = (d) =>
  d ? new Date(d).toLocaleDateString('id-ID') : '-'
</script>
