<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-y-auto custom-scrollbar w-full pr-1">
    <!-- HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-wrap justify-between items-center gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-check-circle"></i>
        </div>
        <div>
          <h1 class="text-base font-bold text-[#0F172A] leading-tight">
            Evaluasi & Monitoring Terapi Anak
          </h1>
          <p class="text-xs text-[#64748B] mt-0.5">Penilaian capaian klinis, progres multi-sesi, dan rekap riwayat evaluasi terpadu</p>
        </div>
      </div>

      <!-- TAB PILLS -->
      <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-lg text-xs font-semibold">
        <button
          type="button"
          @click="activeTab = 'input'"
          class="px-3 py-1.5 rounded-md transition cursor-pointer flex items-center gap-1.5"
          :class="activeTab === 'input' ? 'bg-white text-[#0F766E] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
        >
          <i class="pi pi-file-edit text-xs"></i>
          <span>Form Input Evaluasi</span>
        </button>

        <button
          type="button"
          @click="activeTab = 'riwayat'"
          class="px-3 py-1.5 rounded-md transition cursor-pointer flex items-center gap-1.5"
          :class="activeTab === 'riwayat' ? 'bg-white text-[#0F766E] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
        >
          <i class="pi pi-history text-xs"></i>
          <span>Riwayat & Monitoring ({{ riwayatList.length }})</span>
        </button>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: FORM INPUT EVALUASI -->
    <!-- ========================================================================= -->
    <div v-if="activeTab === 'input'" class="space-y-3">
      <!-- PILIH REGISTRASI -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3.5 space-y-3 shrink-0">
        <label class="block text-xs font-semibold text-slate-700">Pilih Pasien Anak / No. Registrasi</label>

        <select v-model="selectedRegistrasiId" class="w-full h-[34px] px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]">
          <option value="">-- Pilih Anak --</option>
          <option
            v-for="r in registrasiList"
            :key="r.id"
            :value="r.id"
          >
            {{ r.profile_anak?.nama_anak }} — {{ r.no_regis }}
          </option>
        </select>

        <!-- INFO STATUS -->
        <div v-if="selectedRegistrasiData" class="text-xs flex items-center justify-between bg-slate-50 p-2.5 rounded-lg border border-slate-200">
          <p class="text-slate-600">
            Total Sesi Selesai: <b class="text-slate-900">{{ totalSesi }}</b>
          </p>

          <p v-if="!bolehEvaluasi" class="text-amber-700 font-medium">
            ⚠ Semua sesi terapi harus memiliki catatan aktivitas sebelum membuat evaluasi.
          </p>

          <p v-if="evaluasiExist" class="text-emerald-700 font-medium">
            ✔ Evaluasi sudah dibuat (mode lihat / pratinjau)
          </p>
        </div>
      </div>

      <!-- FORM EVALUASI -->
      <div
        v-if="selectedRegistrasiData"
        class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 space-y-4 shrink-0"
      >
        <!-- IDENTITAS -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-[#F0F6F8] p-3 rounded-lg border border-[#DCE8EC]">
          <div>
            <span class="text-slate-500 block text-[11px]">Nama Anak:</span>
            <b class="text-slate-900">{{ selectedRegistrasiData.profile_anak?.nama_anak }}</b>
          </div>
          <div>
            <span class="text-slate-500 block text-[11px]">Tanggal Lahir:</span>
            <b class="text-slate-900">{{ selectedRegistrasiData.profile_anak?.tanggal_lahir || '-' }}</b>
          </div>
          <div>
            <span class="text-slate-500 block text-[11px]">Kategori Layanan:</span>
            <b class="text-[#0F766E]">{{ kategoriLayanan }}</b>
          </div>
          <div>
            <span class="text-slate-500 block text-[11px]">Terapis Bertugas:</span>
            <b class="text-slate-900">{{ selectedRegistrasiData.pelayanans?.[0]?.terapis?.nama || '-' }}</b>
          </div>
        </div>

        <!-- FORM -->
        <form @submit.prevent="submit" class="space-y-4">
          <!-- EVALUASI TERAPI -->
          <div class="space-y-3">
            <h3 class="text-xs font-bold text-[#0F766E] uppercase tracking-wider border-b border-slate-100 pb-1">
              Evaluasi Kondisi & Komponen Klinis
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
              <div>
                <label class="block text-[12px] font-medium text-slate-700 mb-1">Komponen Perilaku</label>
                <textarea v-model="form.komponen_perilaku" :disabled="viewOnly" rows="3" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] disabled:bg-slate-50" placeholder="Respons perilaku anak saat sesi..." />
              </div>

              <div>
                <label class="block text-[12px] font-medium text-slate-700 mb-1">Kondisi Awal</label>
                <textarea v-model="form.kondisi_awal" :disabled="viewOnly" rows="3" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] disabled:bg-slate-50" placeholder="Kondisi klinis anak sebelum program terapi..." />
              </div>

              <div>
                <label class="block text-[12px] font-medium text-slate-700 mb-1">Kondisi Saat Ini</label>
                <textarea v-model="form.kondisi_saat_ini" :disabled="viewOnly" rows="3" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] disabled:bg-slate-50" placeholder="Perkembangan kemampuan saat ini..." />
              </div>

              <div>
                <label class="block text-[12px] font-medium text-slate-700 mb-1">Program Lanjutan yang Direkomendasikan</label>
                <textarea v-model="form.program_lanjutan" :disabled="viewOnly" rows="3" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] disabled:bg-slate-50" placeholder="Rekomendasi paket atau sesi lanjutan..." />
              </div>
            </div>
          </div>

          <!-- HASIL & SARAN -->
          <div class="space-y-3 pt-3 border-t border-slate-100">
            <h3 class="text-xs font-bold text-[#0F766E] uppercase tracking-wider border-b border-slate-100 pb-1">
              Hasil Evaluasi & Saran
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
              <div>
                <label class="block text-[12px] font-medium text-slate-700 mb-1">Kesimpulan Hasil Follow Up</label>
                <textarea v-model="form.kesimpulan_hasil_followup" :disabled="viewOnly" rows="3" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] disabled:bg-slate-50" placeholder="Ringkasan hasil akhir follow up terapi..." />
              </div>

              <div>
                <label class="block text-[12px] font-medium text-slate-700 mb-1">Kemampuan Sebelumnya</label>
                <textarea v-model="form.kemampuan_sebelumnya" :disabled="viewOnly" rows="3" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] disabled:bg-slate-50" placeholder="Kapasitas dasar sebelum intervensi..." />
              </div>

              <div>
                <label class="block text-[12px] font-medium text-slate-700 mb-1">Peningkatan Kemampuan Saat Ini</label>
                <textarea v-model="form.peningkatan_kemampuan_saat_ini" :disabled="viewOnly" rows="3" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] disabled:bg-slate-50" placeholder="Capaian positif yang sudah dikuasai..." />
              </div>

              <div>
                <label class="block text-[12px] font-medium text-slate-700 mb-1">Saran Terapi untuk Orang Tua</label>
                <textarea v-model="form.saran_terapi" :disabled="viewOnly" rows="3" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] disabled:bg-slate-50" placeholder="Saran latihan di rumah / sekolah..." />
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100">
            <button
              type="submit"
              class="h-[34px] px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white text-xs font-semibold shadow-sm transition disabled:opacity-50 cursor-pointer"
              :disabled="!bolehEvaluasi || viewOnly"
            >
              Simpan Evaluasi
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: MONITORING & RIWAYAT EVALUASI -->
    <!-- ========================================================================= -->
    <div v-else class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">
      <!-- Toolbar -->
      <div class="p-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
        <div>
          <h2 class="text-xs font-bold text-slate-900 leading-tight">Daftar Riwayat Evaluasi Klinis</h2>
          <p class="text-[11px] text-slate-500 mt-0.5">Pencatatan perkembangan periodik seluruh pasien terapi anak</p>
        </div>

        <div class="relative w-full sm:w-64">
          <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input
            v-model="searchRiwayat"
            placeholder="Cari nama anak..."
            class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs transition focus:outline-none focus:border-[#0F766E]"
          />
        </div>
      </div>

      <!-- Table -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <div v-if="loadingRiwayat" class="py-12 text-center text-slate-400 text-xs">
          <i class="pi pi-spin pi-spinner text-2xl text-teal-600 mb-2 block"></i>
          <span>Memuat riwayat evaluasi...</span>
        </div>

        <div v-else-if="filteredRiwayat.length === 0" class="py-12 text-center text-slate-400 text-xs">
          <i class="pi pi-inbox text-2xl text-slate-300 mb-1 block"></i>
          <strong>Belum ada catatan riwayat evaluasi.</strong>
          <p class="mt-0.5">Data evaluasi anak yang telah tersimpan akan muncul di sini.</p>
        </div>

        <table v-else class="w-full text-xs text-left text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3 w-12 text-center">No</th>
              <th class="py-2.5 px-3">Nama Anak</th>
              <th class="py-2.5 px-3">Tanggal Evaluasi</th>
              <th class="py-2.5 px-3">Komponen Perilaku</th>
              <th class="py-2.5 px-3">Rekomendasi Lanjutan</th>
              <th class="py-2.5 px-3 text-right w-28">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="(item, idx) in filteredRiwayat" :key="item.id" class="hover:bg-slate-50 transition">
              <td class="py-2 px-3 text-center font-mono text-slate-400 text-xs">{{ idx + 1 }}</td>
              <td class="py-2 px-3">
                <span class="font-bold text-slate-900 block">{{ item.nama_anak || item.registrasi_anak?.profile_anak?.nama_anak || 'Pasien Anak' }}</span>
                <span class="text-[10px] text-slate-500 font-mono">{{ item.registrasi_anak?.profile_anak?.no_rm }}</span>
              </td>
              <td class="py-2 px-3 text-slate-600 tabular-nums">{{ formatDate(item.created_at || item.tanggal_evaluasi) }}</td>
              <td class="py-2 px-3 text-slate-700 max-w-xs truncate">{{ item.komponen_perilaku || '-' }}</td>
              <td class="py-2 px-3 text-slate-700 max-w-xs truncate">{{ item.program_lanjutan || '-' }}</td>
              <td class="py-2 px-3 text-right">
                <button
                  type="button"
                  @click="openDetail(item)"
                  class="px-2.5 py-1 rounded bg-[#E6F2F5] hover:bg-[#d6ebf0] text-[#0F766E] border border-[#CBE3E9] text-[11px] font-bold inline-flex items-center gap-1 transition cursor-pointer"
                >
                  <i class="pi pi-eye text-[10px]"></i>
                  <span>Detail</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL DETAIL EVALUASI -->
    <teleport to="body">
      <div v-if="showDetailModal" class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn">
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-xl rounded-xl max-h-[88dvh] flex flex-col overflow-hidden">
          <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-file-check text-[#2DD4BF] text-sm"></i>
              <h3 class="font-semibold text-[13px]">Detail Hasil Evaluasi Terapi Anak</h3>
            </div>
            <button
              type="button"
              @click="showDetailModal = false"
              class="w-6 h-6 rounded flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition"
            >
              ✕
            </button>
          </div>

          <div v-if="selectedDetail" class="p-4 space-y-3 flex-1 min-h-0 overflow-y-auto custom-scrollbar text-xs">
            <div class="bg-[#E6F2F5] border border-[#CBE3E9] p-3 rounded-xl flex justify-between items-center">
              <div>
                <span class="text-[10px] text-[#15576B] uppercase font-bold tracking-wider block">Pasien Anak</span>
                <span class="text-sm font-bold text-slate-900 leading-tight block">{{ selectedDetail.nama_anak || selectedDetail.registrasi_anak?.profile_anak?.nama_anak || 'Pasien Anak' }}</span>
              </div>
              <span class="text-[11px] text-slate-600 tabular-nums">Tanggal: <strong>{{ formatDate(selectedDetail.created_at || selectedDetail.tanggal_evaluasi) }}</strong></span>
            </div>

            <div class="space-y-2">
              <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                <span class="font-bold text-[#0F766E] block mb-0.5">Komponen Perilaku:</span>
                <p class="text-slate-700 leading-relaxed">{{ selectedDetail.komponen_perilaku || '-' }}</p>
              </div>

              <div class="grid grid-cols-2 gap-2">
                <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                  <span class="font-bold text-slate-700 block mb-0.5">Kondisi Awal:</span>
                  <p class="text-slate-600 leading-relaxed">{{ selectedDetail.kondisi_awal || '-' }}</p>
                </div>
                <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                  <span class="font-bold text-slate-700 block mb-0.5">Kondisi Saat Ini:</span>
                  <p class="text-slate-600 leading-relaxed">{{ selectedDetail.kondisi_saat_ini || '-' }}</p>
                </div>
              </div>

              <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                <span class="font-bold text-slate-700 block mb-0.5">Rekomendasi Program Lanjutan:</span>
                <p class="text-slate-600 leading-relaxed">{{ selectedDetail.program_lanjutan || '-' }}</p>
              </div>

              <div class="p-2.5 bg-emerald-50/70 border border-emerald-200 rounded-lg">
                <span class="font-bold text-emerald-800 block mb-0.5">Saran Terapi untuk Orang Tua:</span>
                <p class="text-emerald-900 leading-relaxed">{{ selectedDetail.saran_terapi || '-' }}</p>
              </div>
            </div>
          </div>

          <div class="px-4 py-2.5 bg-slate-50/95 border-t border-slate-200/80 flex items-center justify-between shrink-0">
            <button
              type="button"
              @click="cetakDetail"
              class="h-[34px] px-3.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-1.5 transition"
            >
              <i class="pi pi-print text-xs"></i>
              <span>Cetak Ringkasan Evaluasi</span>
            </button>
            <button
              type="button"
              @click="showDetailModal = false"
              class="h-[34px] px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold transition"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
    </teleport>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useToast } from 'vue-toastification'
import api from '../axios'

const toast = useToast()

const activeTab = ref('input')
const registrasiList = ref([])
const selectedRegistrasiId = ref('')
const evaluasiExist = ref(false)

const riwayatList = ref([])
const searchRiwayat = ref('')
const loadingRiwayat = ref(false)
const showDetailModal = ref(false)
const selectedDetail = ref(null)

const selectedRegistrasiData = computed(() => {
  return registrasiList.value.find(r => r.id === selectedRegistrasiId.value) || null
})

const form = reactive({
  registrasi_anak_id: null,
  komponen_perilaku: '',
  kondisi_awal: '',
  kondisi_saat_ini: '',
  program_lanjutan: '',
  kesimpulan_hasil_followup: '',
  kemampuan_sebelumnya: '',
  peningkatan_kemampuan_saat_ini: '',
  saran_terapi: ''
})

const totalSesi = computed(() =>
  selectedRegistrasiData.value?.pelayanans?.length || 0
)

const bolehEvaluasi = computed(() =>
  selectedRegistrasiData.value?.pelayanans?.length > 0 &&
  selectedRegistrasiData.value.pelayanans.every(p => p.catatan_aktivitas)
)

const viewOnly = computed(() => evaluasiExist.value)

const kategoriLayanan = computed(() =>
  selectedRegistrasiData.value?.pelayanans?.[0]?.layanan?.kategori?.kategori_layanan || '-'
)

const filteredRiwayat = computed(() => {
  if (!searchRiwayat.value) return riwayatList.value
  const q = searchRiwayat.value.toLowerCase()
  return riwayatList.value.filter(r => {
    const nama = (r.nama_anak || r.registrasi_anak?.profile_anak?.nama_anak || '').toLowerCase()
    return nama.includes(q)
  })
})

const loadRiwayat = async () => {
  loadingRiwayat.value = true
  try {
    const res = await api.get('/riwayat-evaluasi')
    riwayatList.value = res.data || []
  } catch (err) {
    // fallback
    riwayatList.value = []
  } finally {
    loadingRiwayat.value = false
  }
}

const openDetail = (item) => {
  selectedDetail.value = item
  showDetailModal.value = true
}

const cetakDetail = () => {
  toast.info(`Mencetak ringkasan hasil evaluasi pasien: ${selectedDetail.value?.nama_anak || 'Anak'}`)
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

onMounted(async () => {
  try {
    const res = await api.get('/dashboard-terapis')
    registrasiList.value = res.data || []
  } catch (err) {
    console.error('Error fetching dashboard-terapis:', err)
  }
  await loadRiwayat()
})

watch(selectedRegistrasiId, async (id) => {
  if (!id) return

  const val = registrasiList.value.find(r => r.id === id)
  if (!val) return

  form.registrasi_anak_id = val.id
  evaluasiExist.value = false

  Object.keys(form).forEach(k => {
    if (k !== 'registrasi_anak_id') form[k] = ''
  })

  try {
    const res = await api.get(`/evaluasi-terapi/by-registrasi/${val.id}`)
    if (res.data) {
      Object.assign(form, res.data)
      evaluasiExist.value = true
    }
  } catch (err) {
    // not evaluated yet
  }
})

const submit = async () => {
  try {
    await api.post('/evaluasi-terapi', form)
    toast.success('Evaluasi terapi berhasil disimpan!')
    evaluasiExist.value = true
    await loadRiwayat()
  } catch (e) {
    toast.error(e.response?.data?.message || 'Gagal menyimpan evaluasi')
  }
}
</script>
