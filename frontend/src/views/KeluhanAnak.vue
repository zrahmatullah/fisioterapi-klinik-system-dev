<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-y-auto custom-scrollbar w-full pr-1">

    <!-- ================= HEADER ================= -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-comments"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Pengajuan & Riwayat Keluhan</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Sampaikan kendala selama proses terapi anak dan pantau respon tindak lanjut admin</p>
        </div>
      </div>
    </div>

    <!-- ================= GRID: TABEL & FORM ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-start">

      <!-- ================= TABEL KELUHAN (8 COLS) ================= -->
      <section class="lg:col-span-8 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden flex flex-col">
        <div class="p-3 border-b border-slate-100 flex items-center justify-between gap-2.5">
          <div class="relative flex-1 max-w-sm">
            <i class="pi pi-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
            <input
              v-model="search"
              type="text"
              placeholder="Cari nomor keluhan..."
              class="w-full pl-8 pr-3 h-[34px] text-[12.5px] bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
            />
          </div>
          <span class="text-xs text-slate-500 font-medium">{{ filteredKeluhan.length }} keluhan</span>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
          <table class="w-full text-left border-collapse text-[12.5px]">
            <thead class="bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
              <tr>
                <th class="py-2.5 px-3 w-12 text-center">No</th>
                <th class="py-2.5 px-3">No Keluhan</th>
                <th class="py-2.5 px-3">Kategori</th>
                <th class="py-2.5 px-3">Tanggal</th>
                <th class="py-2.5 px-3 text-center">Status</th>
                <th class="py-2.5 px-3 text-center w-24">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="(k, i) in filteredKeluhan" :key="k.id" class="hover:bg-[#F0F6F8]/60 transition">
                <td class="py-2 px-3 text-center text-slate-500 tabular-nums">{{ i + 1 }}</td>
                <td class="py-2 px-3 font-mono font-semibold text-[#0F766E] text-xs">{{ k.no_keluhan }}</td>
                <td class="py-2 px-3 text-slate-700">{{ k.kategori_keluhan }}</td>
                <td class="py-2 px-3 text-slate-600 tabular-nums">{{ formatDate(k.tanggal_keluhan) }}</td>
                <td class="py-2 px-3 text-center">
                  <span
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                    :class="k.tanggapan_keluhan ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200'"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="k.tanggapan_keluhan ? 'bg-emerald-600' : 'bg-amber-600'"></span>
                    {{ k.tanggapan_keluhan ? 'Sudah Ditanggapi' : 'Belum Ditanggapi' }}
                  </span>
                </td>
                <td class="py-2 px-3 text-center">
                  <button
                    type="button"
                    class="h-[28px] px-2.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-medium inline-flex items-center gap-1 transition cursor-pointer"
                    @click="activeKeluhan = k"
                  >
                    <i class="pi pi-eye text-[11px] text-[#0F766E]"></i>
                    <span>Detail</span>
                  </button>
                </td>
              </tr>

              <tr v-if="filteredKeluhan.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-400">
                  <i class="pi pi-folder-open text-2xl text-slate-300 block mb-1"></i>
                  <span class="text-xs">Belum ada keluhan diajukan</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- ================= FORM INPUT (4 COLS) ================= -->
      <section class="lg:col-span-4 bg-white border border-slate-200 rounded-xl shadow-xs p-4 flex flex-col gap-3">
        <div class="border-b border-slate-100 pb-2">
          <h3 class="text-sm font-bold text-[#0F172A] leading-tight flex items-center gap-2">
            <i class="pi pi-pencil text-[#0F766E] text-xs"></i>
            <span>Ajukan Keluhan Baru</span>
          </h3>
          <p class="text-[11px] text-slate-500 mt-0.5">Isi detail keluhan Anda untuk ditindaklanjuti klinik</p>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="block text-[12px] font-medium text-slate-700 mb-1">Nama Pasien Anak</label>
            <select
              v-model="form.profile_anak_id"
              class="w-full h-[34px] px-2.5 text-xs bg-slate-50 border border-slate-300 rounded-lg text-slate-700 cursor-not-allowed"
              disabled
            >
              <option v-for="a in anak" :key="a.id" :value="a.id">
                {{ a.nama_anak }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-[12px] font-medium text-slate-700 mb-1">Kategori Keluhan</label>
            <select
              v-model="form.kategori_keluhan"
              class="w-full h-[34px] px-2.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
            >
              <option value="">Pilih Kategori...</option>
              <option>Pelayanan Administrasi</option>
              <option>Kualitas Terapi</option>
              <option>Sarana & Prasarana</option>
              <option>Jadwal / Waktu Pelayanan</option>
            </select>
          </div>

          <div>
            <label class="block text-[12px] font-medium text-slate-700 mb-1">Isi Keluhan</label>
            <textarea
              v-model="form.isi"
              rows="5"
              class="w-full p-2.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 custom-scrollbar"
              placeholder="Jelaskan kendala yang dialami secara detail..."
            ></textarea>
          </div>

          <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
            <button
              type="button"
              class="h-[34px] px-3.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-medium transition cursor-pointer"
              @click="resetForm"
            >
              Reset
            </button>
            <button
              type="button"
              class="h-[34px] px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white text-xs font-semibold inline-flex items-center gap-1.5 shadow-sm transition cursor-pointer"
              @click="kirimKeluhan"
            >
              <i class="pi pi-send text-xs"></i>
              <span>Kirim Keluhan</span>
            </button>
          </div>
        </div>
      </section>
    </div>

    <!-- ================= MODAL DETAIL TELEPORTED ================= -->
    <teleport to="body">
      <div
        v-if="activeKeluhan"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-lg rounded-xl max-h-[88dvh] flex flex-col overflow-hidden">
          <!-- HEADER -->
          <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-comments text-[#2DD4BF] text-sm"></i>
              <div>
                <h3 class="font-semibold text-[13px] leading-tight">Detail Keluhan & Tanggapan</h3>
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
            <div class="bg-[#F0F6F8] border border-[#DCE8EC] rounded-xl p-3 grid grid-cols-2 gap-2 text-xs">
              <div>
                <span class="text-[10px] text-slate-500 block">Kategori</span>
                <strong class="text-[#0F766E]">{{ activeKeluhan.kategori_keluhan }}</strong>
              </div>
              <div>
                <span class="text-[10px] text-slate-500 block">Tanggal Pengajuan</span>
                <strong class="text-slate-900">{{ formatDate(activeKeluhan.tanggal_keluhan) }}</strong>
              </div>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
              <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                Isi Keluhan Anda
              </label>
              <p class="text-slate-800 leading-relaxed whitespace-pre-line">
                {{ activeKeluhan.isi }}
              </p>
            </div>

            <div class="bg-teal-50/70 border border-teal-200 rounded-lg p-3">
              <div class="flex items-center justify-between mb-1">
                <label class="text-[11px] font-bold text-teal-900 uppercase tracking-wider">
                  Tanggapan Admin Klinik
                </label>
                <span
                  v-if="!activeKeluhan.tanggapan_keluhan"
                  class="text-[10px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200"
                >
                  Menunggu Respon
                </span>
              </div>
              <p class="text-slate-800 leading-relaxed whitespace-pre-line">
                {{ activeKeluhan.tanggapan_keluhan || 'Keluhan Anda telah tercatat dan sedang dalam peninjauan oleh tim medis/administrasi.' }}
              </p>
            </div>
          </div>

          <!-- FOOTER -->
          <div class="px-4 py-2.5 bg-slate-50/95 border-t border-slate-200/80 flex items-center justify-end gap-2 shrink-0">
            <button
              type="button"
              class="h-[34px] px-4 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-medium transition"
              @click="activeKeluhan = null"
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
import { ref, onMounted, computed } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const keluhan = ref([])
const anak = ref([])
const search = ref('')
const activeKeluhan = ref(null)

const form = ref({
  profile_anak_id: '',
  kategori_keluhan: '',
  isi: ''
})

const getDataByAnak = async () => {
  if (!form.value.profile_anak_id) return

  try {
    const res = await api.get(`/profile-anak-saya/${form.value.profile_anak_id}`)
    console.log('Anak terpilih (auto):', res.data)
  } catch (err) {
    console.error('Gagal get data anak:', err)
  }
}

onMounted(async () => {
  try {
    const resAnak = await api.get('/profile-anak-saya')
    anak.value = resAnak.data

    if (anak.value.length > 0) {
      form.value.profile_anak_id = anak.value[0].id
      await getDataByAnak()
    }

    const resKeluhan = await api.get('/keluhan-anak')
    keluhan.value = resKeluhan.data
  } catch (err) {
    console.error('Gagal memuat data:', err)
  }
})

const filteredKeluhan = computed(() =>
  keluhan.value.filter(k =>
    k.no_keluhan?.toLowerCase().includes(search.value.toLowerCase())
  )
)

const kirimKeluhan = async () => {
  if (!form.value.kategori_keluhan || !form.value.isi) {
    toast.error('Mohon lengkapi kategori dan isi keluhan')
    return
  }

  try {
    await api.post('/keluhan-anak', form.value)
    toast.success('Keluhan berhasil dikirim')
    resetForm()
    const res = await api.get('/keluhan-anak')
    keluhan.value = res.data
  } catch {
    toast.error('Gagal mengirim keluhan')
  }
}

const resetForm = () => {
  form.value = {
    profile_anak_id: form.value.profile_anak_id,
    kategori_keluhan: '',
    isi: ''
  }
}

const formatDate = (d) =>
  d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-'
</script>
