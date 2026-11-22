<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
      <div>
        <h1 class="text-[17px] font-bold text-[#0F172A] tracking-tight flex items-center gap-2">
          <span>Dashboard & Pasien Terapi Saya</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]">
            Stage 4: Sesi & EMR Terapi
          </span>
        </h1>
        <p class="text-xs text-[#64748B] mt-0.5">
          Kelola sesi terapi terjadwal, pencatatan SOAP harian, pemotongan kuota paket otomatis, dan penugasan Home Program orang tua
        </p>
      </div>

      <div class="flex items-center gap-2">
        <button
          type="button"
          @click="loadData"
          class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer shadow-2xs"
        >
          <i class="pi pi-refresh text-xs text-slate-400"></i>
          <span>Refresh Data</span>
        </button>
      </div>
    </div>

    <!-- SUMMARY METRICS -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 shrink-0">
      <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-3.5 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9] flex items-center justify-center shrink-0">
          <i class="pi pi-users text-sm"></i>
        </div>
        <div>
          <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Pasien</p>
          <p class="text-lg font-bold text-[#0F172A] tabular-nums mt-0.5">{{ data.length }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-3.5 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-teal-50 text-[#0F766E] border border-teal-200 flex items-center justify-center shrink-0">
          <i class="pi pi-clock text-sm"></i>
        </div>
        <div>
          <p class="text-[11px] font-semibold text-[#0F766E] uppercase tracking-wider">Menunggu Terapi</p>
          <p class="text-lg font-bold text-[#0F766E] tabular-nums mt-0.5">
            {{ data.filter(x => x.status_sesi === 'menunggu' || (x.status_kedatangan === 'sudah_datang' && x.status_sesi !== 'selesai_terapi')).length }}
          </p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-3.5 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0">
          <i class="pi pi-spin pi-spinner text-sm"></i>
        </div>
        <div>
          <p class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">Sedang Terapi</p>
          <p class="text-lg font-bold text-amber-700 tabular-nums mt-0.5">
            {{ data.filter(x => x.status_sesi === 'sedang_terapi').length }}
          </p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-3.5 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0">
          <i class="pi pi-check-circle text-sm"></i>
        </div>
        <div>
          <p class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Selesai Hari Ini</p>
          <p class="text-lg font-bold text-emerald-700 tabular-nums mt-0.5">
            {{ data.filter(x => x.status_sesi === 'selesai_terapi').length }}
          </p>
        </div>
      </div>
    </div>

    <!-- TABLE PASIEN TERAPIS -->
    <div class="flex-1 min-h-0 flex flex-col bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-xs">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="px-4 py-3 text-left font-bold text-[11px] uppercase tracking-wider">Antrian & Regis</th>
              <th class="px-4 py-3 text-left font-bold text-[11px] uppercase tracking-wider">Nama Pasien Anak</th>
              <th class="px-4 py-3 text-left font-bold text-[11px] uppercase tracking-wider">Program & Sesi</th>
              <th class="px-4 py-3 text-center font-bold text-[11px] uppercase tracking-wider">Ruangan</th>
              <th class="px-4 py-3 text-center font-bold text-[11px] uppercase tracking-wider">Status Kedatangan</th>
              <th class="px-4 py-3 text-center font-bold text-[11px] uppercase tracking-wider">Status Sesi</th>
              <th class="px-4 py-3 text-center font-bold text-[11px] uppercase tracking-wider w-48">Aksi Terapi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="item in data"
              :key="item.id"
              class="border-b border-slate-100 hover:bg-[#F0F6F8]/60 transition"
            >
              <!-- Antrian & Regis -->
              <td class="px-4 py-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded font-bold font-mono text-xs bg-[#0B3948] text-white mr-1.5 shadow-2xs">
                  {{ item.antrian_nomor || item.no_antrian || 'TW-01' }}
                </span>
                <span class="text-slate-500 font-mono text-[11px]">{{ item.no_regis }}</span>
              </td>

              <!-- Pasien Anak -->
              <td class="px-4 py-3">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-[#E6F2F5] border border-[#CBE3E9] text-[#0F766E] flex items-center justify-center font-bold text-xs shrink-0">
                    {{ (item.profile_anak?.nama_anak || 'A').charAt(0) }}
                  </div>
                  <div>
                    <span class="font-bold text-slate-800 text-xs block leading-tight">{{ item.profile_anak?.nama_anak || '-' }}</span>
                    <span class="text-[10px] text-slate-500 font-mono">
                      {{ item.profile_anak?.no_rm || item.id_pasien }} &bull; {{ item.profile_anak?.usia_pediatrik || item.profile_anak?.usia_lengkap || 'Anak' }}
                    </span>
                  </div>
                </div>
              </td>

              <!-- Program & Sesi -->
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-800 text-xs">
                  Sesi #{{ item.sesi_ke || 1 }} <span class="text-slate-400 font-normal">dari {{ item.total_sesi || 8 }}</span>
                </div>
                <div class="text-[10px] text-teal-700 font-medium">
                  {{ item.payment_track === 'umum_package' ? 'Paket Terapi (Multi-Sesi)' : (item.payment_track === 'asuransi' ? 'Jaminan Asuransi (GL)' : 'Umum (Per Kunjungan)') }}
                </div>
              </td>

              <!-- Ruangan -->
              <td class="px-4 py-3 text-center text-slate-600 font-medium">{{ item.ruangan?.ruangan || '-' }}</td>

              <!-- Kedatangan -->
              <td class="px-4 py-3 text-center">
                <span
                  v-if="item.status_kedatangan === 'sudah_datang'"
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  Hadir
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200"
                >
                  Belum Hadir
                </span>
              </td>

              <!-- Status Sesi -->
              <td class="px-4 py-3 text-center">
                <span
                  v-if="item.status_sesi === 'selesai_terapi'"
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"
                >
                  <i class="pi pi-check text-[9px]"></i>
                  Selesai
                </span>
                <span
                  v-else-if="item.status_sesi === 'sedang_terapi'"
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                  Sedang Sesi
                </span>
                <span
                  v-else-if="item.status_sesi === 'menunggu' || item.status_kedatangan === 'sudah_datang'"
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                  Siap Terapi
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200"
                >
                  Terjadwal
                </span>
              </td>

              <!-- Aksi Terapi -->
              <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <!-- Start Session Button -->
                  <button
                    v-if="item.status_sesi !== 'sedang_terapi' && item.status_sesi !== 'selesai_terapi'"
                    @click="startSession(item)"
                    type="button"
                    class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#0F766E] hover:bg-[#115E59] text-white flex items-center gap-1 transition cursor-pointer shadow-2xs"
                    title="Mulai sesi terapi dengan anak"
                  >
                    <i class="pi pi-play text-[9px]"></i>
                    Mulai Sesi
                  </button>

                  <!-- Complete Session Button (prominent) -->
                  <button
                    v-if="item.status_sesi === 'sedang_terapi'"
                    @click="openCompleteModal(item)"
                    type="button"
                    class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-600 hover:bg-emerald-700 text-white flex items-center gap-1 transition cursor-pointer shadow-2xs animate-pulse"
                    title="Selesaikan sesi, isi SOAP & Home Program"
                  >
                    <i class="pi pi-check-circle text-[10px]"></i>
                    Selesaikan Sesi
                  </button>

                  <!-- Session Completed View Button -->
                  <span
                    v-if="item.status_sesi === 'selesai_terapi'"
                    class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1"
                  >
                    <i class="pi pi-lock text-[10px]"></i>
                    EMR Terkunci
                  </span>

                  <!-- Intake Assessment Button -->
                  <button
                    @click="openAssesment(item)"
                    :disabled="item.assesment"
                    class="w-7 h-7 rounded-lg transition inline-flex items-center justify-center cursor-pointer ml-1"
                    :class="item.assesment
                      ? 'bg-slate-100 text-slate-300 cursor-not-allowed'
                      : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100'"
                    :title="item.assesment ? 'Asesmen awal sudah diisi' : 'Isi Asesmen Awal Pasien'"
                  >
                    <i class="pi pi-file-edit text-[11px]"></i>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="data.length === 0">
              <td colspan="7" class="py-12 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2">
                  <i class="pi pi-inbox text-xl text-slate-400"></i>
                </div>
                <p class="text-slate-500 font-medium text-xs">Tidak ada antrian pasien terapi yang ditugaskan kepada Anda saat ini</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL PELAKSANAAN SESI & CATATAN EMR HARIAN (STAGE 4: ATOMIC TRANSACTION) -->
    <!-- ========================================================================= -->
    <teleport to="body">
    <div v-if="showCompleteModal" class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-200 max-h-[92vh] flex flex-col overflow-hidden animate-fadeIn">
        
        <!-- HEADER -->
        <div class="px-6 py-4 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
          <div>
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
              <i class="pi pi-heart text-teal-300 text-sm"></i>
              Catatan EMR Harian & Selesai Sesi Terapi
            </h3>
            <p class="text-xs text-teal-200/80 mt-0.5">
              Pasien: {{ activeSession?.profile_anak?.nama_anak }} &middot; Sesi #{{ activeSession?.sesi_ke }} dari {{ activeSession?.total_sesi }}
            </p>
          </div>
          <button @click="showCompleteModal = false" class="w-7 h-7 rounded-lg text-slate-300 hover:text-white hover:bg-white/10 transition flex items-center justify-center cursor-pointer">
            <i class="pi pi-times text-xs"></i>
          </button>
        </div>

        <!-- BODY -->
        <form @submit.prevent="submitCompleteSession" class="flex-1 overflow-y-auto px-6 py-4 space-y-4 text-xs">
          
          <!-- Banner Child & Session Quota -->
          <div class="p-3 rounded-xl bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-between">
            <div>
              <span class="text-[10px] text-[#15576B] uppercase font-bold tracking-wider block">Pemeriksaan Klinis Terapi</span>
              <span class="text-xs font-bold text-[#0F172A]">{{ activeSession?.profile_anak?.nama_anak }}</span>
              <span class="text-slate-500 ml-1 font-mono">({{ activeSession?.profile_anak?.no_rm || activeSession?.id_pasien }})</span>
            </div>
            <div class="text-right">
              <span class="text-[10px] text-teal-700 font-bold block">POTONG KUOTA OTOMATIS</span>
              <span class="text-xs font-semibold text-[#0F766E]">
                {{ activeSession?.payment_track === 'umum_package' ? 'Paket Terapi Pasien (-1 Sesi)' : (activeSession?.payment_track === 'asuransi' ? 'Jaminan Asuransi / GL (-1 Sesi)' : 'Per Kunjungan') }}
              </span>
            </div>
          </div>

          <!-- Mood Anak & Durasi -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="label">Kondisi & Mood Anak Saat Sesi</label>
              <select v-model="sessionForm.mood_anak" required class="input">
                <option value="kooperatif">Kooperatif (Fokus & Ceria)</option>
                <option value="fokus_baik">Fokus Sangat Baik</option>
                <option value="tantrum">Tantrum / Menolak Instruksi</option>
                <option value="cemas">Cemas / Menangis</option>
                <option value="lelah">Lelah / Mengantuk</option>
              </select>
            </div>

            <div>
              <label class="label">Durasi Terapi (Menit)</label>
              <input type="number" v-model="sessionForm.durasi_menit" min="15" max="180" class="input" />
            </div>
          </div>

          <!-- Aktivitas & Respons Anak -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="label">Aktivitas Stimulasi Terapi</label>
              <textarea
                v-model="sessionForm.aktivitas_terapi"
                rows="3"
                placeholder="Misal: Stimulasi motorik oral, puzzle warna, latihan meniup gelembung..."
                class="textarea"
              ></textarea>
            </div>

            <div>
              <label class="label">Respons & Capaian Anak</label>
              <textarea
                v-model="sessionForm.respons_anak"
                rows="3"
                placeholder="Misal: Mampu menirukan bunyi konsonan B dan M sebanyak 8 repetisi..."
                class="textarea"
              ></textarea>
            </div>
          </div>

          <!-- SOAP Klinis Ringkas -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="label">Catatan Subjektif & Objektif</label>
              <textarea
                v-model="sessionForm.subjektif"
                rows="2"
                placeholder="Observasi fisik dan perilaku anak..."
                class="textarea"
              ></textarea>
            </div>

            <div>
              <label class="label">Asesmen & Rencana Pertemuan Berikutnya</label>
              <textarea
                v-model="sessionForm.planning"
                rows="2"
                placeholder="Target kemampuan yang akan dilatih selanjutnya..."
                class="textarea"
              ></textarea>
            </div>
          </div>

          <!-- HOME PROGRAM UNTUK ORANG TUA (CRITICAL FOR PARENT PORTAL) -->
          <div class="p-3.5 rounded-xl border border-teal-200 bg-teal-50/70 space-y-2">
            <div class="flex items-center gap-2">
              <i class="pi pi-home text-[#0F766E] text-sm"></i>
              <label class="text-xs font-bold text-[#0F766E] uppercase tracking-wider block">
                Home Program / PR untuk Orang Tua di Rumah (Wajib)
              </label>
            </div>
            <p class="text-[11px] text-teal-800">
              Instruksi ini akan otomatis dipublikasikan ke <strong>Portal Orang Tua</strong> agar orang tua dapat melanjutkan latihan stimulasi di rumah.
            </p>
            <textarea
              v-model="sessionForm.home_program_catatan"
              rows="3"
              required
              placeholder="Contoh: Orang tua diharapkan melatih anak meniup lilin berjarak 20 cm 10x setiap pagi, dan mengajak komunikasi dua arah di depan cermin selama 10 menit..."
              class="textarea bg-white"
            ></textarea>
          </div>

          <!-- FOOTER ACTION BUTTONS -->
          <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
            <button
              type="button"
              @click="showCompleteModal = false"
              class="btn-secondary"
            >
              Batal
            </button>

            <button
              type="submit"
              :disabled="savingComplete"
              class="btn-primary"
            >
              <span v-if="savingComplete" class="flex items-center gap-1.5">
                <i class="pi pi-spin pi-spinner text-xs"></i>
                Menyimpan Transaksi...
              </span>
              <span v-else class="flex items-center gap-1.5">
                <i class="pi pi-lock text-xs"></i>
                Selesaikan Sesi & Kunci EMR
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
    </teleport>

    <!-- MODAL INTAKE ASSESMENT 1 (EXISTING) -->
    <teleport to="body">
    <div
      v-if="showModal"
      class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4"
    >
      <div class="bg-white w-full max-w-2xl rounded-xl shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col overflow-hidden animate-fadeIn">
        <div class="flex justify-between items-center px-6 py-4 bg-[#0B3948] text-white shrink-0">
          <div>
            <h2 class="text-sm font-bold text-white flex items-center gap-2">
              <i class="pi pi-clipboard text-teal-300"></i>
              Form Asesmen Klinis Awal Pasien
            </h2>
            <p class="text-xs text-teal-200/80 mt-0.5">
              {{ selected?.profile_anak?.nama_anak }} &middot; No. Regis: {{ selected?.no_regis }}
            </p>
          </div>
          <button @click="closeModal" class="w-7 h-7 rounded-lg text-slate-300 hover:text-white hover:bg-white/10 transition flex items-center justify-center cursor-pointer">
            <i class="pi pi-times text-xs"></i>
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="flex-1 overflow-y-auto px-6 py-4 space-y-3 text-xs">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label">Usia Kehamilan Saat Lahir</label>
              <input v-model="form.usia_kehamilan_lahir" placeholder="Contoh: 38 minggu" class="input" />
            </div>
            <div>
              <label class="label">Proses Kelahiran</label>
              <input v-model="form.proses_kelahiran" placeholder="Normal / Caesar" class="input" />
            </div>
          </div>

          <div>
            <label class="label">Riwayat Gangguan Kelahiran</label>
            <input v-model="form.gangguan_melahirkan" placeholder="Asfiksia, kuning, dll..." class="input" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label">Kontak Mata</label>
              <select v-model="form.kontak_mata" class="input">
                <option value="1">Ada (Baik)</option>
                <option value="0">Kurang / Menghindar</option>
              </select>
            </div>
            <div>
              <label class="label">Komunikasi Dua Arah</label>
              <select v-model="form.komunikasi_dua_arah" class="input">
                <option value="1">Mampu</option>
                <option value="0">Belum Mampu</option>
              </select>
            </div>
          </div>

          <div>
            <label class="label">Catatan Perkembangan Anak</label>
            <textarea v-model="form.informasi_subjektif" rows="3" placeholder="Deskripsi keluhan dan observasi awal terapis..." class="textarea"></textarea>
          </div>

          <div class="pt-3 border-t border-slate-200 flex justify-end gap-2">
            <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
            <button type="submit" class="btn-primary">Simpan Asesmen Awal</button>
          </div>
        </form>
      </div>
    </div>
    </teleport>

  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const data = ref([])
const showModal = ref(false)
const selected = ref(null)

// Complete Session Modal (Stage 4)
const showCompleteModal = ref(false)
const activeSession = ref(null)
const savingComplete = ref(false)

const sessionForm = reactive({
  mood_anak: 'kooperatif',
  durasi_menit: 60,
  aktivitas_terapi: '',
  respons_anak: '',
  subjektif: '',
  planning: '',
  home_program_catatan: ''
})

const form = reactive({
  registrasi_anak_id: null,
  usia_kehamilan_lahir: '',
  proses_kelahiran: '',
  gangguan_melahirkan: '',
  kontak_mata: '1',
  komunikasi_dua_arah: '1',
  informasi_subjektif: ''
})

onMounted(async () => {
  await loadData()
})

async function loadData() {
  try {
    const res = await api.get('/dashboard-terapis')
    data.value = res.data ?? []
  } catch (err) {
    console.error('Error fetching therapist data:', err)
  }
}

async function startSession(item) {
  try {
    const res = await api.post(`/pediatric-workflow/session/${item.id}/start`)
    toast.success('Sesi terapi dimulai!')
    item.status_sesi = 'sedang_terapi'
    await loadData()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal memulai sesi')
  }
}

function openCompleteModal(item) {
  activeSession.value = item
  sessionForm.mood_anak = item.mood_anak || 'kooperatif'
  sessionForm.durasi_menit = item.durasi_menit || 60
  sessionForm.aktivitas_terapi = ''
  sessionForm.respons_anak = ''
  sessionForm.subjektif = `Kondisi anak stabil saat sesi ke-${item.sesi_ke}.`
  sessionForm.planning = `Lanjut sesi ke-${(item.sesi_ke || 1) + 1}.`
  sessionForm.home_program_catatan = item.home_program_catatan || ''
  showCompleteModal.value = true
}

async function submitCompleteSession() {
  if (!sessionForm.home_program_catatan) {
    toast.error('Harap isi Home Program untuk orang tua di rumah.')
    return
  }

  savingComplete.value = true
  try {
    const res = await api.post(`/pediatric-workflow/session/${activeSession.value.id}/complete`, sessionForm)
    toast.success('Sesi selesai! Catatan EMR tersimpan, kuota terpotong & draf kasir diperbarui.')
    showCompleteModal.value = false
    await loadData()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menyelesaikan sesi')
  } finally {
    savingComplete.value = false
  }
}

function openAssesment(item) {
  selected.value = item
  form.registrasi_anak_id = item.id
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

async function handleSubmit() {
  try {
    await api.post('/assesment-1', form)
    toast.success('Asesmen awal berhasil disimpan')
    closeModal()
    await loadData()
  } catch (err) {
    toast.error('Gagal menyimpan asesmen')
  }
}
</script>

<style scoped>
.label {
  @apply text-xs font-semibold text-slate-700 mb-1 block;
}
.input {
  @apply w-full px-3 py-2 border border-slate-300 rounded-lg text-xs transition focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 focus:border-[#0F766E];
}
.textarea {
  @apply w-full px-3 py-2 border border-slate-300 rounded-lg text-xs min-h-[70px] transition focus:outline-none focus:ring-2 focus:ring-[#0F766E]/20 focus:border-[#0F766E];
}
.btn-primary {
  @apply px-4 py-2 rounded-lg bg-[#0F766E] text-white font-semibold text-xs hover:bg-[#115E59] active:scale-95 transition inline-flex items-center gap-1.5 shadow-sm cursor-pointer;
}
.btn-secondary {
  @apply px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 font-semibold text-xs hover:bg-slate-50 transition cursor-pointer;
}
.animate-fadeIn {
  animation: fadeIn 0.15s ease;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.98) translateY(4px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
</style>
