<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

/* ======================
   STATE
====================== */
const dataList = ref([])
const search = ref('')

const showModal = ref(false)
const selected = ref(null)

/* ======================
   LOAD DATA
====================== */
const loadData = async () => {
  try {
    const res = await axios.get(
      'http://localhost:8000/api/riwayat-evaluasi',
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      }
    )
    dataList.value = res.data
  } catch (e) {
    toast.error('Gagal memuat data evaluasi')
  }
}

onMounted(loadData)

/* ======================
   FILTER
====================== */
const filtered = () => {
  if (!search.value) return dataList.value
  return dataList.value.filter(d =>
    d.nama_anak.toLowerCase().includes(search.value.toLowerCase())
  )
}

/* ======================
   MODAL
====================== */
const openModal = (row) => {
  selected.value = row
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selected.value = null
}

/* ======================
   CETAK PDF PER ITEM
====================== */
const cetakItem = (id) => {
  if (!id) {
    toast.error('ID evaluasi tidak ditemukan')
    return
  }

  const baseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

  window.open(
    `${baseUrl}/api/cetak/evaluasi-anak/${id}/pdf`,
    '_blank'
  )
}
</script>

<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- HEADER / TOOLBAR -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-history"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Riwayat Pembuatan Laporan Evaluasi</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Daftar arsip laporan evaluasi klinis dan perkembangan sesi anak</p>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <div class="relative w-64">
          <i class="pi pi-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
          <input
            v-model="search"
            type="text"
            placeholder="Cari nama anak..."
            class="w-full pl-8 pr-3 h-[34px] text-[12.5px] bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
          />
        </div>
      </div>
    </div>

    <!-- TABLE CARD -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-left border-collapse text-[12.5px]">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3 w-12 text-center">No</th>
              <th class="py-2.5 px-3">Id Pendaftaran</th>
              <th class="py-2.5 px-3">Nama Anak</th>
              <th class="py-2.5 px-3">Tanggal Pembuatan</th>
              <th class="py-2.5 px-3">Kategori Layanan</th>
              <th class="py-2.5 px-3 text-center">Jumlah Sesi</th>
              <th class="py-2.5 px-3 text-center w-36">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(d, i) in filtered()"
              :key="d.id"
              class="hover:bg-[#F0F6F8]/60 transition"
            >
              <td class="py-2 px-3 text-center text-slate-500 tabular-nums">{{ i + 1 }}</td>
              <td class="py-2 px-3 font-mono font-medium text-slate-800">{{ d.registrasi_anak_id }}</td>
              <td class="py-2 px-3 font-medium text-[#0F172A]">{{ d.nama_anak }}</td>
              <td class="py-2 px-3 text-slate-600 tabular-nums">{{ d.tanggal_pembuatan }}</td>
              <td class="py-2 px-3 text-slate-700">{{ d.kategori_layanan ?? '-' }}</td>
              <td class="py-2 px-3 text-center font-semibold text-slate-800 tabular-nums">{{ d.total_sesi }}</td>
              <td class="py-2 px-3 text-center">
                <div class="inline-flex items-center gap-1.5">
                  <button
                    type="button"
                    class="h-[28px] px-2.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-medium inline-flex items-center gap-1 transition"
                    @click="openModal(d)"
                  >
                    <i class="pi pi-eye text-[11px] text-[#0F766E]"></i>
                    <span>View</span>
                  </button>
                  <button
                    type="button"
                    class="h-[28px] px-2.5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-medium inline-flex items-center gap-1 shadow-sm transition"
                    @click="cetakItem(d.id)"
                  >
                    <i class="pi pi-print text-[11px]"></i>
                    <span>Cetak</span>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="filtered().length === 0">
              <td colspan="7" class="text-center py-12 text-slate-400">
                <i class="pi pi-folder-open text-2xl text-slate-300 block mb-1"></i>
                <span class="text-xs">Data tidak tersedia</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- FOOTER -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] text-slate-500 flex items-center justify-between">
        <span>Menampilkan {{ filtered().length }} data</span>
        <span class="font-medium text-slate-700">Total: {{ dataList.length }} Evaluasi</span>
      </div>
    </div>

    <!-- ======================
         MODAL DETAIL TELEPORTED
    ======================= -->
    <teleport to="body">
      <div
        v-if="showModal"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-3xl rounded-xl max-h-[88dvh] flex flex-col overflow-hidden">

          <!-- HEADER -->
          <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-file text-[#2DD4BF] text-sm"></i>
              <h3 class="font-semibold text-[13px]">Detail Hasil Evaluasi Klinis</h3>
            </div>
            <button
              type="button"
              @click="closeModal"
              class="w-6 h-6 rounded flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition"
            >
              ✕
            </button>
          </div>

          <!-- BODY -->
          <div class="px-5 py-4 flex-1 min-h-0 overflow-y-auto custom-scrollbar text-[12.5px] text-slate-700 space-y-4">
            <!-- INFO CARD -->
            <div class="bg-[#F0F6F8] border border-[#DCE8EC] rounded-xl p-3 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
              <div>
                <span class="text-[11px] text-slate-500 block">Nama Anak</span>
                <strong class="text-slate-900 text-[13px]">{{ selected?.nama_anak }}</strong>
              </div>
              <div>
                <span class="text-[11px] text-slate-500 block">Tanggal Pembuatan</span>
                <strong class="text-slate-900">{{ selected?.tanggal_pembuatan }}</strong>
              </div>
              <div>
                <span class="text-[11px] text-slate-500 block">Kategori Layanan</span>
                <strong class="text-slate-900">{{ selected?.kategori_layanan || '-' }}</strong>
              </div>
              <div>
                <span class="text-[11px] text-slate-500 block">Total Sesi</span>
                <strong class="text-teal-700 font-bold">{{ selected?.total_sesi }} Sesi</strong>
              </div>
            </div>

            <!-- DETAIL GRID -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
              <div
                v-for="(label, key) in {
                  komponen_perilaku: 'Komponen Perilaku',
                  kondisi_awal: 'Kondisi Awal',
                  kondisi_saat_ini: 'Kondisi Saat Ini',
                  kemampuan_sebelumnya: 'Kemampuan Sebelumnya',
                  peningkatan_kemampuan_saat_ini: 'Peningkatan Kemampuan Saat Ini',
                  program_lanjutan: 'Program Lanjutan',
                  kesimpulan_hasil_followup: 'Kesimpulan Follow Up',
                  saran_terapi: 'Saran Terapi'
                }"
                :key="key"
                class="bg-slate-50 border border-slate-200 rounded-lg p-2.5"
              >
                <p class="font-semibold text-slate-700 text-[11px] uppercase tracking-wider mb-1">{{ label }}</p>
                <div class="text-slate-800 leading-relaxed whitespace-pre-line">
                  {{ selected?.[key] || '-' }}
                </div>
              </div>
            </div>
          </div>

          <!-- FOOTER -->
          <div class="px-4 py-2.5 bg-slate-50/95 border-t border-slate-200/80 flex items-center justify-end gap-2 shrink-0">
            <button
              type="button"
              class="h-[34px] px-4 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-medium transition"
              @click="closeModal"
            >
              Tutup
            </button>
            <button
              type="button"
              class="h-[34px] px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-medium inline-flex items-center gap-1.5 shadow-sm transition"
              @click="cetakItem(selected?.id)"
            >
              <i class="pi pi-print text-xs"></i>
              <span>Cetak Laporan</span>
            </button>
          </div>

        </div>
      </div>
    </teleport>

  </div>
</template>
