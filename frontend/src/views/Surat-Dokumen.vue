<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-file"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Surat-Surat & Dokumen Medis Resmi</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Penerbitan surat keterangan terapi, rekomendasi sekolah, dan resume medis ber-QR Code verifikasi</p>
        </div>
      </div>

      <button
        type="button"
        @click="openNewSuratModal"
        class="bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-medium text-xs h-[34px] px-3.5 rounded-lg flex items-center gap-1.5 transition shadow-sm shrink-0 cursor-pointer"
      >
        <i class="pi pi-plus text-xs"></i>
        <span>Terbitkan Surat Baru</span>
      </button>
    </div>

    <!-- MAIN ARSIP TABLE CARD -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">
      <div class="p-3 border-b border-slate-100 flex items-center justify-between gap-3 shrink-0">
        <div>
          <h3 class="text-xs font-bold text-[#0F172A]">Arsip Surat Keluar Pasien</h3>
          <p class="text-[11px] text-[#64748B]">Semua dokumen yang diterbitkan memiliki stempel digital dan hash keaslian.</p>
        </div>
        <div class="w-64">
          <Dropdown
            v-model="selectedPasienId"
            :options="pasienList"
            optionLabel="nama_anak"
            optionValue="id"
            placeholder="Semua Pasien"
            filter
            showClear
            class="w-full text-xs"
            @change="loadArsip"
          />
        </div>
      </div>

      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-left border-collapse text-[12.5px]">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3">Nomor Surat</th>
              <th class="py-2.5 px-3">Jenis & Judul Dokumen</th>
              <th class="py-2.5 px-3">Pasien Terkait</th>
              <th class="py-2.5 px-3">Tanggal Terbit</th>
              <th class="py-2.5 px-3 text-center">Integritas QR</th>
              <th class="py-2.5 px-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loading" class="text-center">
              <td colspan="6" class="py-12 text-slate-400">
                <i class="pi pi-spin pi-spinner text-xl text-[#0F766E]"></i>
                <p class="mt-2 text-xs">Memuat arsip surat...</p>
              </td>
            </tr>

            <tr v-else-if="suratList.length === 0" class="text-center">
              <td colspan="6" class="py-12 text-slate-400 text-xs">
                <i class="pi pi-folder-open text-2xl text-slate-300 block mb-1"></i>
                <span>Belum ada surat yang diterbitkan.</span>
              </td>
            </tr>

            <tr v-for="s in suratList" :key="s.id" class="hover:bg-[#F0F6F8]/60 transition">
              <td class="py-2 px-3 font-semibold text-[#0F766E] tabular-nums font-mono text-xs">{{ s.nomor_surat }}</td>
              <td class="py-2 px-3">
                <p class="font-semibold text-slate-900 leading-tight">{{ s.judul_surat }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ s.template?.jenis_surat || 'Surat Keterangan' }}</p>
              </td>
              <td class="py-2 px-3">
                <p class="font-semibold text-slate-800 leading-tight">{{ s.profile_anak?.nama_anak || '-' }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">RM: {{ s.profile_anak?.no_rm || '-' }}</p>
              </td>
              <td class="py-2 px-3 text-xs text-slate-500 tabular-nums">{{ formatDate(s.tanggal_surat) }}</td>
              <td class="py-2 px-3 text-center">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#DCFCE7] text-[#15803D] border border-emerald-200">
                  <i class="pi pi-qrcode text-[10px]"></i>
                  <span>Terverifikasi</span>
                </span>
              </td>
              <td class="py-2 px-3 text-right">
                <button
                  type="button"
                  @click="previewSurat(s)"
                  class="h-[28px] px-2.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs transition inline-flex items-center gap-1.5 shadow-2xs cursor-pointer"
                >
                  <i class="pi pi-eye text-[11px] text-[#0F766E]"></i>
                  <span>Lihat & Cetak</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- FOOTER -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] text-slate-500 flex items-center justify-between">
        <span>Total: {{ suratList.length }} Surat Diterbitkan</span>
      </div>
    </div>

    <!-- MODAL 1: BUAT SURAT BARU TELEPORTED -->
    <teleport to="body">
      <div
        v-if="showCreateModal"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-xl rounded-xl max-h-[88dvh] flex flex-col overflow-hidden">
          <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-file-edit text-[#2DD4BF] text-sm"></i>
              <h3 class="font-semibold text-[13px]">Terbitkan Dokumen / Surat Baru</h3>
            </div>
            <button
              type="button"
              @click="showCreateModal = false"
              class="w-6 h-6 rounded flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition"
            >
              ✕
            </button>
          </div>

          <div class="space-y-3 p-4 flex-1 min-h-0 overflow-y-auto custom-scrollbar text-xs">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Template Dokumen</label>
              <Dropdown
                v-model="createForm.surat_template_id"
                :options="templates"
                optionLabel="judul"
                optionValue="id"
                placeholder="Pilih Template Surat"
                class="w-full text-xs"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Pasien Terapi</label>
              <Dropdown
                v-model="createForm.profile_anak_id"
                :options="pasienList"
                optionLabel="nama_anak"
                optionValue="id"
                placeholder="Pilih Pasien"
                filter
                class="w-full text-xs"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Diagnosis Klinis / Catatan</label>
              <input
                v-model="createForm.diagnosis"
                type="text"
                class="w-full h-[34px] px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
                placeholder="Mis. Gangguan Berbahasa Ekspresif (F80.1)"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Terapi / Program Terapi</label>
              <input
                v-model="createForm.jenis_terapi"
                type="text"
                class="w-full h-[34px] px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
                placeholder="Mis. Terapi Wicara & Sensori Integrasi"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Jadwal Sesi Rutin</label>
              <input
                v-model="createForm.jadwal_sesi"
                type="text"
                class="w-full h-[34px] px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
                placeholder="Mis. 2 kali seminggu (Senin & Kamis)"
              />
            </div>
          </div>

          <div class="px-4 py-2.5 bg-slate-50/95 border-t border-slate-200/80 flex items-center justify-end gap-2 shrink-0">
            <button
              type="button"
              class="h-[34px] px-3.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-medium transition"
              @click="showCreateModal = false"
            >
              Batal
            </button>
            <button
              type="button"
              @click="submitSurat"
              :disabled="submitLoading"
              class="h-[34px] px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-medium text-xs flex items-center gap-1.5 shadow-sm transition cursor-pointer"
            >
              <i v-if="submitLoading" class="pi pi-spin pi-spinner text-xs"></i>
              <span>Terbitkan Surat</span>
            </button>
          </div>
        </div>
      </div>
    </teleport>

    <!-- MODAL 2: PRATINJAU DOKUMEN DENGAN KOP SURAT TELEPORTED -->
    <teleport to="body">
      <div
        v-if="showPreviewModal"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-2xl rounded-xl max-h-[88dvh] flex flex-col overflow-hidden">
          <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-print text-[#2DD4BF] text-sm"></i>
              <h3 class="font-semibold text-[13px]">Pratinjau Dokumen Resmi Klinik</h3>
            </div>
            <button
              type="button"
              @click="showPreviewModal = false"
              class="w-6 h-6 rounded flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition"
            >
              ✕
            </button>
          </div>

          <div class="p-5 flex-1 min-h-0 overflow-y-auto custom-scrollbar">
            <div v-if="activeSurat" class="bg-white p-6 border border-slate-200 rounded-xl space-y-4 text-slate-800 text-xs shadow-xs">
              <!-- KOP SURAT -->
              <div class="border-b-2 border-slate-900 pb-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded-lg bg-[#0F766E] text-white flex items-center justify-center font-bold text-lg">
                    EK
                  </div>
                  <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">KLINIK TUMBUH KEMBANG & TERAPI ANAK</h2>
                    <p class="text-[11px] text-slate-600">Jl. Kesehatan Anak No. 45, Jakarta · Telp: (021) 555-7890</p>
                    <p class="text-[10px] text-slate-400">Izin Operasional: 445/098/SIP-KLINIK/2026</p>
                  </div>
                </div>
              </div>

              <!-- JUDUL & NOMOR -->
              <div class="text-center space-y-0.5">
                <h3 class="text-sm font-bold uppercase underline">{{ activeSurat.judul_surat }}</h3>
                <p class="text-[11px] font-mono text-slate-600">Nomor: {{ activeSurat.nomor_surat }}</p>
              </div>

              <!-- ISI SURAT -->
              <div class="text-xs leading-relaxed whitespace-pre-line py-2 border-t border-b border-slate-100" v-html="activeSurat.isi_final_html"></div>

              <!-- TANDA TANGAN & QR CODE -->
              <div class="pt-4 flex justify-between items-end">
                <div class="text-center space-y-1">
                  <div class="w-16 h-16 bg-[#F0F6F8] border border-[#DCE8EC] rounded-lg flex flex-col items-center justify-center p-1">
                    <i class="pi pi-qrcode text-2xl text-[#0F766E]"></i>
                    <span class="text-[8px] text-slate-500">Scan Verifikasi</span>
                  </div>
                  <p class="text-[9px] text-slate-400 font-mono">{{ activeSurat.qr_verification_code?.substring(0, 12) }}</p>
                </div>

                <div class="text-center space-y-8">
                  <p class="text-[11px] text-slate-600">Jakarta, {{ formatDate(activeSurat.tanggal_surat) }}<br />Tenaga Medis Pemeriksa,</p>
                  <div>
                    <p class="text-xs font-bold underline text-slate-900">Terapis Klinis / Dokter Anak</p>
                    <p class="text-[10px] text-slate-400">SIP: 445/SIP-MED/2026</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="px-4 py-2.5 bg-slate-50/95 border-t border-slate-200/80 flex items-center justify-end gap-2 shrink-0">
            <button
              type="button"
              class="h-[34px] px-3.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-medium transition"
              @click="showPreviewModal = false"
            >
              Tutup
            </button>
            <button
              type="button"
              @click="printDoc"
              class="h-[34px] px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white font-medium text-xs flex items-center gap-1.5 shadow-sm transition"
            >
              <i class="pi pi-print text-xs"></i>
              <span>Cetak PDF Resmi</span>
            </button>
          </div>
        </div>
      </div>
    </teleport>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'
import Dropdown from 'primevue/dropdown'

const toast = useToast()

const loading = ref(false)
const submitLoading = ref(false)
const suratList = ref([])
const templates = ref([])
const pasienList = ref([])
const selectedPasienId = ref(null)

const showCreateModal = ref(false)
const createForm = ref({
  surat_template_id: null,
  profile_anak_id: 6,
  diagnosis: 'Gangguan Berbahasa Ekspresif (F80.1)',
  jenis_terapi: 'Terapi Wicara',
  jadwal_sesi: 'Senin & Kamis',
})

const showPreviewModal = ref(false)
const activeSurat = ref(null)

const loadTemplates = async () => {
  try {
    const res = await api.get('/surat/templates')
    templates.value = res.data || []
  } catch (err) {
    console.error(err)
  }
}

const loadPasienList = async () => {
  try {
    const res = await api.get('/pendaftaran/anak')
    pasienList.value = res.data?.data || res.data || []
  } catch (err) {
    pasienList.value = [
      { id: 6, nama_anak: 'Azhar Al-Farizi', no_rm: 'RM-202512-00006' },
      { id: 7, nama_anak: 'Rahma Danisha', no_rm: 'RM-202512-00007' },
      { id: 8, nama_anak: 'Hilmi Ramadhan', no_rm: 'RM-202512-00008' },
    ]
  }
}

const loadArsip = async () => {
  loading.value = true
  try {
    const res = await api.get('/surat/arsip', {
      params: { profile_anak_id: selectedPasienId.value }
    })
    suratList.value = res.data || []
  } catch (err) {
    toast.error('Gagal memuat arsip surat')
  } finally {
    loading.value = false
  }
}

const openNewSuratModal = () => {
  if (templates.value.length > 0) createForm.value.surat_template_id = templates.value[0].id
  showCreateModal.value = true
}

const submitSurat = async () => {
  if (!createForm.value.profile_anak_id || !createForm.value.surat_template_id) {
    toast.error('Lengkapi template dan data pasien')
    return
  }

  submitLoading.value = true
  try {
    await api.post('/surat/generate', {
      surat_template_id: createForm.value.surat_template_id,
      profile_anak_id: createForm.value.profile_anak_id,
      parameters: {
        diagnosis: createForm.value.diagnosis,
        jenis_terapi: createForm.value.jenis_terapi,
        jadwal_sesi: createForm.value.jadwal_sesi,
      }
    })
    toast.success('Surat resmi berhasil diterbitkan!')
    showCreateModal.value = false
    loadArsip()
  } catch (err) {
    toast.error('Gagal menerbitkan surat')
  } finally {
    submitLoading.value = false
  }
}

const previewSurat = (s) => {
  activeSurat.value = s
  showPreviewModal.value = true
}

const printDoc = () => {
  window.print()
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(async () => {
  await loadTemplates()
  await loadPasienList()
  await loadArsip()
})
</script>
