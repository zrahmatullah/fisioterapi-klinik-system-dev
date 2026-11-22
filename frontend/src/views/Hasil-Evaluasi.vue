<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- ================= HEADER ================= -->
    <header class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-wrap items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-file-check"></i>
        </div>
        <div>
          <h1 class="text-base font-bold text-[#0F172A] leading-tight">Hasil Evaluasi Terapi Anak</h1>
          <p class="text-xs text-[#64748B] mt-0.5">Ringkasan capaian klinis, progres multi-sesi, dan rekomendasi lanjutan</p>
        </div>
      </div>

      <button
        v-if="evaluasiList.length > 0"
        type="button"
        class="btn-primary-clinical text-xs py-2 px-3.5 shrink-0"
        @click="cetakAll"
      >
        <i class="pi pi-print text-xs"></i>
        <span>Cetak Semua PDF</span>
      </button>
    </header>

    <!-- ================= TABLE CARD ================= -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">

      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <!-- EMPTY STATE -->
        <div v-if="!isLoading && evaluasiList.length === 0" class="py-12 text-center text-slate-400 text-xs">
          <i class="pi pi-inbox text-2xl text-slate-300 mb-1 block"></i>
          <strong>Belum ada data evaluasi.</strong>
          <p class="mt-0.5">Catatan evaluasi anak akan muncul di sini setelah dibuat.</p>
        </div>

        <!-- TABLE -->
        <table v-else class="w-full text-[12.5px] text-left text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3 w-12 text-center">No</th>
              <th class="py-2.5 px-3">Nama Anak</th>
              <th class="py-2.5 px-3">Tanggal Evaluasi</th>
              <th class="py-2.5 px-3 text-center">Total Sesi</th>
              <th class="py-2.5 px-3 text-right w-36">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="(item, idx) in evaluasiList" :key="item.id" class="hover:bg-[#F0F6F8]/60 transition">
              <td class="py-2 px-3 text-center font-mono text-slate-400 text-xs">{{ idx + 1 }}</td>
              <td class="py-2 px-3 font-bold text-slate-900">{{ item.nama_anak }}</td>
              <td class="py-2 px-3 text-slate-600 tabular-nums">{{ formatTanggal(item.tanggal_evaluasi) }}</td>
              <td class="py-2 px-3 text-center">
                <span
                  class="px-2 py-0.5 rounded-full text-[11px] font-semibold tabular-nums"
                  :style="{ background: accentSoftFor(item.total_sesi), color: accentFor(item.total_sesi) }"
                >
                  {{ item.total_sesi }} sesi
                </span>
              </td>
              <td class="py-2 px-3 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button class="px-2.5 py-1 rounded-lg bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9] text-xs font-semibold hover:bg-[#0F766E] hover:text-white transition flex items-center gap-1 cursor-pointer" @click="openModal(item)">
                    <i class="pi pi-eye text-[10px]"></i>
                    Lihat
                  </button>
                  <button class="px-2.5 py-1 rounded-lg bg-white border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition flex items-center gap-1 cursor-pointer" @click="cetakItem(item)">
                    <i class="pi pi-print text-[10px]"></i>
                    Cetak
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>

    <!-- ================= MODAL ================= -->
    <teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="showModal"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4"
        @click.self="closeModal"
      >
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-fadeIn flex flex-col max-h-[88vh]">

          <div class="bg-[#0B3948] text-white px-5 py-3.5 flex items-center justify-between shrink-0">
            <div>
              <span class="text-[10px] text-teal-300 uppercase tracking-wider font-semibold block">Laporan Evaluasi</span>
              <h2 class="text-sm font-bold mt-0.5">{{ selected?.nama_anak }}</h2>
            </div>
            <button class="text-slate-300 hover:text-white p-1 cursor-pointer" @click="closeModal" aria-label="Tutup">
              <i class="pi pi-times text-xs"></i>
            </button>
          </div>

          <!-- Ringkasan -->
          <div class="grid grid-cols-2 gap-3 p-4 bg-slate-50 border-b border-slate-200 text-xs shrink-0">
            <div>
              <span class="text-slate-500 block text-[11px]">Tanggal Evaluasi:</span>
              <b class="text-slate-800">{{ formatTanggal(selected?.tanggal_evaluasi) }}</b>
            </div>
            <div>
              <span class="text-slate-500 block text-[11px]">Total Sesi Selesai:</span>
              <b class="text-[#0F766E]">{{ selected?.total_sesi }} sesi</b>
            </div>
          </div>

          <!-- Detail laporan -->
          <div class="p-4 overflow-y-auto custom-scrollbar flex-1 space-y-3 text-xs">
            <div v-for="field in reportFields" :key="field.key" class="bg-white border border-slate-200 rounded-lg p-3">
              <label class="font-bold text-slate-800 block text-[11.5px] mb-1">{{ field.label }}</label>
              <p class="text-slate-600 whitespace-pre-line leading-relaxed">{{ selected?.[field.key] || '—' }}</p>
            </div>
          </div>

          <div class="p-3.5 bg-slate-50 border-t border-slate-200 flex justify-end gap-2 shrink-0">
            <button class="btn-secondary-clinical text-xs py-1.5 px-3" @click="closeModal">Tutup</button>
            <button class="btn-primary-clinical text-xs py-1.5 px-4 flex items-center gap-1.5" @click="cetakItem(selected)">
              <i class="pi pi-print text-xs"></i>
              Cetak PDF
            </button>
          </div>

        </div>
      </div>
    </Transition>
    </teleport>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const evaluasiList = ref([])
const isLoading = ref(true)
const showModal = ref(false)
const selected = ref(null)

const reportFields = [
  { key: 'komponen_perilaku', label: 'Komponen Perilaku' },
  { key: 'kondisi_awal', label: 'Kondisi Awal' },
  { key: 'kondisi_saat_ini', label: 'Kondisi Saat Ini' },
  { key: 'kemampuan_sebelumnya', label: 'Kemampuan Sebelumnya' },
  { key: 'peningkatan_kemampuan_saat_ini', label: 'Peningkatan Kemampuan Saat Ini' },
  { key: 'program_lanjutan', label: 'Program Lanjutan' },
  { key: 'kesimpulan_hasil_followup', label: 'Kesimpulan Hasil Follow Up' },
  { key: 'saran_terapi', label: 'Saran Terapi' },
]

onMounted(async () => {
  try {
    const res = await api.get('/hasil-evaluasi-anak')
    evaluasiList.value = Array.isArray(res.data) ? res.data : []
  } catch (err) {
    console.error('Error fetching hasil evaluasi:', err)
  } finally {
    isLoading.value = false
  }
})

const formatTanggal = (val) => {
  if (!val) return '—'
  const d = new Date(val)
  if (Number.isNaN(d.getTime())) return val
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const accentFor = (sesi) => {
  const n = Number(sesi) || 0
  if (n >= 20) return '#0F766E'
  if (n >= 8) return '#D97706'
  return '#64748B'
}
const accentSoftFor = (sesi) => {
  const n = Number(sesi) || 0
  if (n >= 20) return '#E6F2F5'
  if (n >= 8) return '#FEF3E2'
  return '#F1F2F4'
}

const openModal = (item) => {
  selected.value = item
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selected.value = null
}

const cetakItem = (item) => {
  const token = localStorage.getItem('token')
  if (!item?.id || !token) {
    toast.error('Data tidak lengkap, silakan login ulang')
    return
  }
  window.open(
    `http://localhost:8000/api/cetak/evaluasi-anak/${item.id}/pdf?token=${token}`,
    '_blank'
  )
}

const cetakAll = () => {
  const token = localStorage.getItem('token')
  if (!token) {
    toast.error('Silakan login ulang')
    return
  }
  window.open(
    `http://localhost:8000/api/cetak/evaluasi-anak/all/pdf?token=${token}`,
    '_blank'
  )
}
</script>
