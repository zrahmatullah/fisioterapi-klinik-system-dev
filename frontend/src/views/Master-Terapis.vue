<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER & TABS -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-user-plus"></i>
        </div>
        <div>
          <h1 class="text-[15px] font-bold text-[#0F172A] leading-tight">Master Tenaga Terapis</h1>
          <p class="text-[11.5px] text-[#64748B] mt-0.5">Kelola daftar terapis, spesialisasi klinis, dan pemetaan ke ruangan praktik</p>
        </div>
      </div>

      <!-- TAB BUTTONS -->
      <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-lg">
        <button
          type="button"
          @click="tab = 'terapis'"
          class="h-7 px-3 rounded-md text-[12px] font-semibold transition cursor-pointer"
          :class="tab === 'terapis' ? 'bg-white text-[#0F766E] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
        >
          Daftar Terapis
        </button>
        <button
          type="button"
          @click="tab = 'mapping'"
          class="h-7 px-3 rounded-md text-[12px] font-semibold transition cursor-pointer"
          :class="tab === 'mapping' ? 'bg-white text-[#0F766E] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
        >
          Terapis ke Ruangan
        </button>
      </div>
    </div>

    <!-- TAB 1: MASTER TERAPIS -->
    <div v-if="tab === 'terapis'" class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
      <!-- TABLE TOOLBAR -->
      <div class="shrink-0 p-3 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2.5 bg-white">
        <div class="relative w-full sm:w-72">
          <i class="pi pi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
          <input
            v-model="search"
            type="text"
            placeholder="Cari nama terapis..."
            class="w-full h-[34px] text-[12.5px] pl-8 pr-2.5 border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 transition bg-white text-slate-800 placeholder:text-slate-400"
          />
        </div>
        <span class="text-[11.5px] text-slate-500 font-medium">
          Total <strong>{{ filteredTerapis.length }}</strong> terapis aktif
        </span>
      </div>

      <!-- TABLE BODY -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="min-w-full text-[12.5px] text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2 px-3 text-center w-12">No</th>
              <th class="py-2 px-3 cursor-pointer" @click="sortBy('nama')">
                Nama Terapis <i class="pi pi-sort-alt text-[9px] text-slate-400 ml-1"></i>
              </th>
              <th class="py-2 px-3">Jenis Kelamin</th>
              <th class="py-2 px-3">No. Telepon</th>
              <th class="py-2 px-3">Spesialisasi</th>
              <th class="py-2 px-3">Jadwal Praktik</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(u, index) in paginatedTerapis"
              :key="u.id"
              class="hover:bg-[#F0F6F8]/60 transition-colors"
            >
              <td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="py-2 px-3 font-semibold text-[#0F766E]">{{ u.nama }}</td>
              <td class="py-2 px-3 text-slate-600">{{ u.jenis_kelamin?.nama || '-' }}</td>
              <td class="py-2 px-3 text-slate-600 font-mono text-[11.5px]">{{ u.no_telepon || '-' }}</td>
              <td class="py-2 px-3">
                <span class="px-2 py-0.5 rounded-md bg-teal-50 text-[#0F766E] border border-teal-200 text-[11px] font-medium">
                  {{ u.spesialisasi || 'Terapis Umum' }}
                </span>
              </td>
              <td class="py-2 px-3">
                <div v-if="u.jadwal_user?.length" class="flex flex-wrap gap-1">
                  <span
                    v-for="j in u.jadwal_user"
                    :key="j.id"
                    class="bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9] px-2 py-0.5 rounded text-[10.5px] font-medium"
                  >
                    {{ hariLabel(j.jadwal_master.hari) }} ({{ j.jadwal_master.jam_mulai }}-{{ j.jadwal_master.jam_selesai }})
                  </span>
                </div>
                <span v-else class="text-[11px] text-slate-400 italic">Belum ada jadwal</span>
              </td>
            </tr>

            <!-- EMPTY -->
            <tr v-if="filteredTerapis.length === 0">
              <td colspan="6" class="py-12 text-center text-slate-400">
                <div class="flex flex-col items-center justify-center">
                  <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                    <i class="pi pi-folder-open text-lg"></i>
                  </div>
                  <h4 class="text-[13px] font-semibold text-slate-800">Terapis Tidak Ditemukan</h4>
                  <p class="text-[11.5px] text-slate-500 mt-0.5">Tidak ada data yang cocok dengan kata kunci pencarian.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- TABLE FOOTER -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] text-slate-600 flex items-center justify-between">
        <div>Halaman {{ currentPage }} dari {{ totalPages || 1 }}</div>
        <div class="flex items-center gap-1">
          <button
            class="h-7 px-2 rounded border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 disabled:opacity-40"
            :disabled="currentPage === 1"
            @click="currentPage--"
          >
            <i class="pi pi-chevron-left text-[9px]"></i>
          </button>
          <span class="px-2 font-medium tabular-nums">{{ currentPage }} / {{ totalPages || 1 }}</span>
          <button
            class="h-7 px-2 rounded border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 disabled:opacity-40"
            :disabled="currentPage >= totalPages"
            @click="currentPage++"
          >
            <i class="pi pi-chevron-right text-[9px]"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- TAB 2: MAPPING TERAPIS TO RUANGAN -->
    <div v-if="tab === 'mapping'" class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden shadow-[0_1px_3px_rgba(15,23,42,0.04)] p-4 space-y-4">
      <div class="border-b border-slate-100 pb-3">
        <h3 class="text-[14px] font-bold text-[#0F172A] flex items-center gap-2">
          <i class="pi pi-link text-[#0F766E]"></i>
          Mapping Terapis ke Ruangan Praktik
        </h3>
        <p class="text-[11.5px] text-slate-500 mt-0.5">Tentukan ruangan spesifik tempat terapis melayani sesi harian</p>
      </div>

      <!-- FORM ADD MAPPING -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200/80 shrink-0">
        <div>
          <label class="label-clinical">Pilih Terapis</label>
          <select v-model="map.terapis_id" class="input-clinical">
            <option value="">Pilih Terapis...</option>
            <option v-for="t in terapis" :key="t.id" :value="t.id">{{ t.nama }}</option>
          </select>
        </div>

        <div>
          <label class="label-clinical">Pilih Ruangan Terapi</label>
          <select v-model="map.ruangan_id" class="input-clinical">
            <option value="">Pilih Ruangan...</option>
            <option v-for="r in allRuangan" :key="r.id" :value="r.id">{{ r.ruangan }}</option>
          </select>
        </div>

        <div class="flex items-end">
          <button
            type="button"
            class="btn-primary-clinical w-full text-xs"
            @click="saveMap"
          >
            <i class="pi pi-plus text-xs"></i>
            <span>Simpan Mapping</span>
          </button>
        </div>
      </div>

      <!-- TABLE MAPPINGS -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar border border-slate-200 rounded-lg">
        <table class="min-w-full text-[12.5px] text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2 px-3 text-center w-12">No</th>
              <th class="py-2 px-3">Nama Tenaga Terapis</th>
              <th class="py-2 px-3">Ruangan Praktik Ditugaskan</th>
              <th class="py-2 px-3 text-center w-24">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(m, idx) in mappings"
              :key="m.id || idx"
              class="hover:bg-[#F0F6F8]/60 transition-colors"
            >
              <td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]">{{ idx + 1 }}</td>
              <td class="py-2 px-3 font-semibold text-slate-900">{{ m.terapis }}</td>
              <td class="py-2 px-3 text-slate-700">{{ m.ruangan }}</td>
              <td class="py-2 px-3 text-center">
                <button
                  type="button"
                  class="w-7 h-7 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 inline-flex items-center justify-center transition shadow-2xs"
                  title="Hapus Mapping"
                  @click="hapusMap(m)"
                >
                  <i class="pi pi-trash text-[11px]"></i>
                </button>
              </td>
            </tr>

            <tr v-if="mappings.length === 0">
              <td colspan="4" class="py-8 text-center text-slate-400 text-xs">
                Belum ada data penugasan ruangan untuk terapis.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- CONFIRM DELETE DIALOG -->
    <ConfirmDialog
      v-model="confirmDeleteModal"
      title="Hapus Mapping Ruangan"
      message="Apakah Anda yakin ingin menghapus penugasan ruangan untuk terapis ini?"
      confirmText="Ya, Hapus Mapping"
      type="danger"
      @confirm="executeHapusMap"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue'

const toast = useToast()
const tab = ref('terapis')

const terapis = ref([])
const allRuangan = ref([])
const mappings = ref([])

const map = ref({ terapis_id: '', ruangan_id: '' })
const search = ref('')

const confirmDeleteModal = ref(false)
const selectedMapToDelete = ref(null)
const sortKey = ref('')
const sortAsc = ref(true)

const currentPage = ref(1)
const perPage = 10

const hariList = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
const hariLabel = (h) => hariList[h]

const fetchTerapis = async () => {
  try {
    terapis.value = (await api.get('/terapis')).data.data
  } catch (e) {
    console.error(e)
  }
}

const fetchRuangan = async () => {
  try {
    allRuangan.value = (await api.get('/ruangan')).data
  } catch (e) {
    console.error(e)
  }
}

const fetchMapping = async () => {
  try {
    mappings.value = (await api.get('/terapis-ruangan')).data
  } catch (e) {
    console.error(e)
  }
}

const filteredTerapis = computed(() =>
  !search.value
    ? terapis.value
    : terapis.value.filter((t) =>
        t.nama?.toLowerCase().includes(search.value.toLowerCase())
      )
)

const sortedTerapis = computed(() => {
  if (!sortKey.value) return filteredTerapis.value
  return [...filteredTerapis.value].sort((a, b) => {
    const A = a[sortKey.value] || ''
    const B = b[sortKey.value] || ''
    if (A < B) return sortAsc.value ? -1 : 1
    if (A > B) return sortAsc.value ? 1 : -1
    return 0
  })
})

const totalPages = computed(() => Math.ceil(sortedTerapis.value.length / perPage))
const paginatedTerapis = computed(() => {
  const s = (currentPage.value - 1) * perPage
  return sortedTerapis.value.slice(s, s + perPage)
})

const sortBy = (key) => {
  if (sortKey.value === key) sortAsc.value = !sortAsc.value
  else {
    sortKey.value = key
    sortAsc.value = true
  }
}

const saveMap = async () => {
  if (!map.value.terapis_id || !map.value.ruangan_id) {
    toast.warning('Pilih terapis & ruangan terlebih dahulu')
    return
  }
  try {
    await api.post('/terapis-ruangan', map.value)
    toast.success('Mapping berhasil disimpan')
    map.value = { terapis_id: '', ruangan_id: '' }
    fetchMapping()
  } catch (e) {
    toast.error('Gagal menyimpan mapping')
  }
}

const hapusMap = (m) => {
  selectedMapToDelete.value = m
  confirmDeleteModal.value = true
}

const executeHapusMap = async () => {
  if (!selectedMapToDelete.value) return
  const m = selectedMapToDelete.value
  try {
    await api.delete(`/terapis/${m.terapis_id}/ruangan/${m.ruangan_id}`)
    toast.success('Mapping ruangan berhasil dihapus')
    confirmDeleteModal.value = false
    fetchMapping()
  } catch {
    toast.error('Gagal menghapus mapping')
  }
}

onMounted(async () => {
  await fetchTerapis()
  await fetchRuangan()
  await fetchMapping()
})
</script>
