<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-3.5 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] border border-slate-200 shrink-0">
      <div>
        <h1 class="text-base font-bold text-[#0F172A] tracking-tight">
          Catatan Sesi Terapi Anak
        </h1>
        <p class="text-xs text-[#64748B] mt-0.5">
          Rekap aktivitas sesi klinis, catatan respons anak, dan evaluasi tugas rumah
        </p>
      </div>

      <!-- SEARCH -->
      <div class="relative w-full sm:w-64">
        <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input
          v-model="search"
          placeholder="Cari nama pasien..."
          class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs transition focus:outline-none focus:ring-2 focus:ring-[#0F766E]/15 focus:border-[#0F766E]"
        />
      </div>
    </div>

    <!-- TABLE -->
    <div class="flex-1 min-h-0 flex flex-col bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-xs">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="px-4 py-3 text-center font-bold text-[11px] uppercase tracking-wider w-12">No</th>

              <!-- SORT TANGGAL -->
              <th
                class="px-4 py-3 text-left font-bold text-[11px] uppercase tracking-wider cursor-pointer select-none hover:bg-slate-100 transition"
                @click="toggleSort"
              >
                <div class="flex items-center gap-1.5">
                  Tanggal
                  <i
                    class="pi text-[10px] text-slate-400"
                    :class="sortOrder === 'desc' ? 'pi-sort-amount-down' : 'pi-sort-amount-up'"
                  ></i>
                </div>
              </th>

              <th class="px-4 py-3 text-left font-bold text-[11px] uppercase tracking-wider">Jam</th>
              <th class="px-4 py-3 text-center font-bold text-[11px] uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-left font-bold text-[11px] uppercase tracking-wider">Aktivitas Terapi</th>
              <th class="px-4 py-3 text-left font-bold text-[11px] uppercase tracking-wider">Keterangan / Respons</th>
              <th class="px-4 py-3 text-left font-bold text-[11px] uppercase tracking-wider">Tugas Rumah</th>
              <th class="px-4 py-3 text-center font-bold text-[11px] uppercase tracking-wider w-28">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(row, i) in paginatedRows"
              :key="row.id"
              class="border-b border-slate-100 hover:bg-[#F0F6F8]/60 transition align-top"
            >
              <td class="px-4 py-3 text-center font-medium text-slate-400 tabular-nums">
                {{ (currentPage - 1) * perPage + i + 1 }}
              </td>

              <td class="px-4 py-3">
                <div class="font-bold text-[#0F172A]">{{ row.hari }}</div>
                <div class="text-[11px] text-slate-500 font-medium tabular-nums">{{ formatTanggal(row.tanggal) }}</div>
              </td>

              <td class="px-4 py-3 text-slate-700 font-mono text-[11px] tabular-nums">
                {{ row.jam_mulai }} - {{ row.jam_selesai }}
              </td>

              <td class="px-4 py-3 text-center">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                  :class="row.status === 'hadir'
                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                    : 'bg-amber-50 text-amber-700 border border-amber-200'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="row.status === 'hadir' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                  {{ row.status === 'hadir' ? 'Hadir' : 'Terjadwal' }}
                </span>
              </td>

              <td class="px-4 py-3 text-slate-700 max-w-[180px] truncate">{{ row.aktivitas || '-' }}</td>
              <td class="px-4 py-3 text-slate-700 max-w-[180px] truncate">{{ row.keterangan || '-' }}</td>
              <td class="px-4 py-3 text-slate-700 max-w-[180px] truncate">{{ row.tugas_rumah || '-' }}</td>

              <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    class="px-2.5 py-1 rounded-lg bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9] text-xs font-semibold hover:bg-[#0F766E] hover:text-white transition flex items-center justify-center gap-1 cursor-pointer"
                    @click="openModal(row)"
                    title="Detail Catatan"
                  >
                    <i class="pi pi-eye text-[10px]"></i>
                    Detail
                  </button>

                  <button
                    class="px-2 py-1 rounded-lg bg-white border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition flex items-center justify-center cursor-pointer"
                    @click="cetak(row)"
                    title="Cetak Catatan"
                  >
                    <i class="pi pi-print text-[10px]"></i>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="paginatedRows.length === 0">
              <td colspan="8" class="py-12 text-center">
                <div class="flex flex-col items-center">
                  <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mb-2">
                    <i class="pi pi-inbox text-xl text-slate-400"></i>
                  </div>
                  <p class="text-slate-500 font-medium text-xs">Belum ada catatan aktivitas sesi terapi</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between px-4 py-2.5 border-t border-slate-200 bg-[#F8FAFC] text-[11.5px] shrink-0">
        <div class="text-slate-500">
          Halaman {{ currentPage }} dari {{ totalPages || 1 }}
        </div>

        <div class="flex items-center gap-1.5 flex-wrap">
          <button
            class="px-2.5 py-1 border border-slate-300 rounded text-xs hover:bg-slate-50 disabled:opacity-40"
            :disabled="currentPage === 1"
            @click="currentPage--"
          >
            Prev
          </button>

          <button
            v-for="p in totalPages"
            :key="p"
            class="px-2.5 py-1 border rounded text-xs"
            :class="p === currentPage ? 'bg-[#0F766E] text-white border-[#0F766E]' : 'border-slate-300 hover:bg-slate-50'"
            @click="currentPage = p"
          >
            {{ p }}
          </button>

          <button
            class="px-2.5 py-1 border border-slate-300 rounded text-xs hover:bg-slate-50 disabled:opacity-40"
            :disabled="currentPage === totalPages"
            @click="currentPage++"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <teleport to="body">
    <!-- MODAL DETAIL -->
    <transition name="fade">
      <div
        v-if="showModal"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4"
      >
        <div class="bg-white w-full max-w-3xl rounded-xl shadow-2xl border border-slate-200 overflow-hidden max-h-[92vh] flex flex-col animate-fadeIn">

          <!-- HEADER -->
          <div class="bg-[#0B3948] px-6 py-4 shrink-0 flex items-center justify-between text-white">
            <div>
              <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="pi pi-book text-teal-300"></i>
                Catatan Sesi Terapi Pasien
              </h3>
              <p class="text-xs text-teal-200/80 mt-0.5">Pasien: {{ selected.nama_anak }}</p>
            </div>
            <button
              @click="showModal = false"
              class="w-7 h-7 rounded-lg text-slate-300 hover:text-white hover:bg-white/10 transition flex items-center justify-center cursor-pointer"
              aria-label="Tutup"
            >
              <i class="pi pi-times text-xs"></i>
            </button>
          </div>

          <div class="p-6 space-y-4 text-xs overflow-y-auto">

            <!-- INFO GRID -->
            <div class="grid grid-cols-2 gap-3 bg-[#E6F2F5] border border-[#CBE3E9] p-3.5 rounded-xl">
              <div>
                <div class="text-[11px] font-semibold text-[#15576B] uppercase tracking-wider mb-1">Nama Pasien Anak</div>
                <div class="bg-white border border-[#CBE3E9] px-3 py-2 rounded-lg font-bold text-slate-800">
                  {{ selected.nama_anak }}
                </div>
              </div>

              <div>
                <div class="text-[11px] font-semibold text-[#15576B] uppercase tracking-wider mb-1">Hari & Tanggal Sesi</div>
                <div class="bg-white border border-[#CBE3E9] px-3 py-2 rounded-lg font-bold text-slate-800 tabular-nums">
                  {{ selected.hari }}, {{ formatTanggal(selected.tanggal) }}
                </div>
              </div>

              <div>
                <div class="text-[11px] font-semibold text-[#15576B] uppercase tracking-wider mb-1">Jam Sesi</div>
                <div class="bg-white border border-[#CBE3E9] px-3 py-2 rounded-lg font-bold text-slate-800 tabular-nums font-mono">
                  {{ selected.jam_mulai }} - {{ selected.jam_selesai }}
                </div>
              </div>

              <div>
                <div class="text-[11px] font-semibold text-[#15576B] uppercase tracking-wider mb-1">Terapis Bertugas</div>
                <div class="bg-white border border-[#CBE3E9] px-3 py-2 rounded-lg font-bold text-slate-800">
                  {{ selected.terapis }}
                </div>
              </div>

              <div class="col-span-2">
                <div class="text-[11px] font-semibold text-[#15576B] uppercase tracking-wider mb-1">Layanan Terapi</div>
                <div class="bg-white border border-[#CBE3E9] px-3 py-2 rounded-lg font-bold text-[#0F766E]">
                  {{ selected.layanan }}
                </div>
              </div>
            </div>

            <!-- AKTIVITAS -->
            <div>
              <label class="font-bold text-slate-800 mb-1.5 block">Aktivitas Terapi yang Dijalankan</label>
              <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 whitespace-pre-line leading-relaxed">
                {{ selected.aktivitas || 'Tidak ada catatan aktivitas.' }}
              </div>
            </div>

            <!-- KETERANGAN / RESPONS -->
            <div>
              <label class="font-bold text-slate-800 mb-1.5 block">Keterangan / Respons Klinis Anak</label>
              <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 whitespace-pre-line leading-relaxed">
                {{ selected.keterangan || 'Tidak ada catatan keterangan respons.' }}
              </div>
            </div>

            <!-- TUGAS RUMAH -->
            <div>
              <label class="font-bold text-[#0F766E] mb-1.5 block flex items-center gap-1.5">
                <i class="pi pi-home"></i>
                Tugas Rumah untuk Orang Tua (Home Program)
              </label>
              <div class="p-3 bg-[#E6F2F5] border border-[#CBE3E9] rounded-lg text-slate-800 whitespace-pre-line leading-relaxed">
                {{ selected.tugas_rumah || 'Tidak ada tugas rumah khusus untuk sesi ini.' }}
              </div>
            </div>

          </div>

          <!-- FOOTER -->
          <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex justify-between items-center shrink-0">
            <button
              @click="cetak(selected)"
              class="px-4 py-2 rounded-lg bg-white border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-100 transition inline-flex items-center gap-2 cursor-pointer"
            >
              <i class="pi pi-print"></i>
              Cetak Dokumen
            </button>

            <button
              @click="showModal = false"
              class="px-5 py-2 rounded-lg bg-[#0F766E] text-white font-semibold text-xs hover:bg-[#115E59] transition cursor-pointer"
            >
              Tutup
            </button>
          </div>

        </div>
      </div>
    </transition>
    </teleport>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/axios'

const rows = ref([])
const search = ref('')
const sortOrder = ref('desc')

const currentPage = ref(1)
const perPage = 10

const showModal = ref(false)
const selected = ref({})

const loadData = async () => {
  try {
    const res = await api.get('/catatan-aktivitas-anak')
    rows.value = (res.data || []).map(item => ({
      id: item.id,
      hari: item.hari || '-',
      tanggal: item.tanggal,
      jam_mulai: item.jam_mulai,
      jam_selesai: item.jam_selesai,
      status: item.checkin_sesi ? 'hadir' : 'terjadwal',
      aktivitas: item.aktivitas_terapi,
      keterangan: item.keterangan_terapi,
      tugas_rumah: item.tugas_rumah,
      nama_anak: item.registrasi_anak?.profile_anak?.nama_anak || '-',
      terapis: item.pelayanan_terapi_anak?.terapis?.nama || '-',
      layanan: item.pelayanan_terapi_anak?.layanan?.layanan || '-'
    }))
  } catch (err) {
    console.error('Gagal mengambil data catatan terapi:', err)
  }
}

onMounted(() => {
  loadData()
})

const filteredRows = computed(() => {
  let data = rows.value

  if (search.value) {
    data = data.filter(r =>
      r.nama_anak.toLowerCase().includes(search.value.toLowerCase()) ||
      r.aktivitas?.toLowerCase().includes(search.value.toLowerCase())
    )
  }

  return data.sort((a, b) => {
    const dateA = new Date(a.tanggal)
    const dateB = new Date(b.tanggal)
    return sortOrder.value === 'asc' ? dateA - dateB : dateB - dateA
  })
})

const totalPages = computed(() =>
  Math.ceil(filteredRows.value.length / perPage)
)

const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredRows.value.slice(start, start + perPage)
})

const toggleSort = () => {
  sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
}

const openModal = (row) => {
  selected.value = row
  showModal.value = true
}

const formatTanggal = (d) => {
  if (!d) return '-'
  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  }).format(new Date(d))
}

const cetak = (row) => {
  window.open(`/api/cetak/catatan-terapi/${row.id}`, '_blank')
}
</script>
