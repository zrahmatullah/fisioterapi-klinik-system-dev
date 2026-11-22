<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-sliders-h"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Dashboard & Antrian Order Penunjang Medis</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Pusat konfirmasi tindakan, input hasil laboratorium/radiologi, dan validasi biaya sebelum masuk ke billing tagihan registrasi</p>
        </div>
      </div>

      <div class="flex items-center gap-2 shrink-0">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]">
          <span class="w-1.5 h-1.5 rounded-full bg-[#0F766E] animate-pulse"></span>
          Petugas Penunjang Bertugas
        </span>
        <button
          type="button"
          @click="refreshData"
          class="p-2 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 text-slate-600 text-xs transition cursor-pointer"
          title="Refresh Antrian"
        >
          <i class="pi pi-refresh" :class="{ 'pi-spin': loading }"></i>
        </button>
      </div>
    </div>

    <!-- METRICS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0">
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider">Menunggu Hasil</span>
          <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
        </div>
        <h3 class="text-lg font-bold text-amber-900 tabular-nums mt-0.5">{{ countMenunggu }} Order</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Order dokter belum masuk bill</p>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-semibold text-sky-700 uppercase tracking-wider">Order Laboratorium</span>
          <i class="pi pi-filter text-xs text-sky-600"></i>
        </div>
        <h3 class="text-lg font-bold text-sky-900 tabular-nums mt-0.5">{{ countLab }} Order</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Tes darah, mikronutrien & urine</p>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-semibold text-purple-700 uppercase tracking-wider">Order Radiologi</span>
          <i class="pi pi-image text-xs text-purple-600"></i>
        </div>
        <h3 class="text-lg font-bold text-purple-900 tabular-nums mt-0.5">{{ countRad }} Order</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Rontgen thorax & diagnostik tulang</p>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">Selesai Masuk Bill</span>
          <i class="pi pi-check-circle text-xs text-emerald-600"></i>
        </div>
        <h3 class="text-lg font-bold text-emerald-900 tabular-nums mt-0.5">{{ countSelesai }} Selesai</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Hasil lengkap & ditagihkan</p>
      </div>
    </div>

    <!-- MAIN TABLE CARD -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">
      <!-- Toolbar & Filter Tabs -->
      <div class="p-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shrink-0 bg-white">
        <div class="flex items-center gap-1.5 flex-wrap">
          <button
            type="button"
            @click="activeFilter = 'menunggu'"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
            :class="activeFilter === 'menunggu' ? 'bg-[#0F766E] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
          >
            <span>Menunggu Hasil (Pra-Billing)</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeFilter === 'menunggu' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'">
              {{ countMenunggu }}
            </span>
          </button>

          <button
            type="button"
            @click="activeFilter = 'laboratorium'"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
            :class="activeFilter === 'laboratorium' ? 'bg-[#0F766E] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
          >
            <span>Laboratorium</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeFilter === 'laboratorium' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'">
              {{ countLab }}
            </span>
          </button>

          <button
            type="button"
            @click="activeFilter = 'radiologi'"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
            :class="activeFilter === 'radiologi' ? 'bg-[#0F766E] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
          >
            <span>Radiologi</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeFilter === 'radiologi' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'">
              {{ countRad }}
            </span>
          </button>

          <button
            type="button"
            @click="activeFilter = 'selesai'"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
            :class="activeFilter === 'selesai' ? 'bg-[#0F766E] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
          >
            <span>Selesai Masuk Bill</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeFilter === 'selesai' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'">
              {{ countSelesai }}
            </span>
          </button>
        </div>

        <div class="relative w-full sm:w-64">
          <i class="pi pi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari no. order, tindakan, no. RM..."
            class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
          />
        </div>
      </div>

      <!-- Table Body -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-[12.5px] text-left text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3 w-12 text-center">No</th>
              <th class="py-2.5 px-3">No. Order & Waktu</th>
              <th class="py-2.5 px-3">Pasien Anak & No. RM</th>
              <th class="py-2.5 px-3">No. Registrasi Kunjungan</th>
              <th class="py-2.5 px-3">Dokter Pemohon</th>
              <th class="py-2.5 px-3">Jenis & Nama Tindakan</th>
              <th class="py-2.5 px-3 text-right">Tarif Tindakan</th>
              <th class="py-2.5 px-3 text-center">Status Billing</th>
              <th class="py-2.5 px-3 text-right w-36">Aksi Verifikasi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loading" class="text-center">
              <td colspan="9" class="py-10 text-slate-400">
                <i class="pi pi-spin pi-spinner text-xl text-[#0F766E]"></i>
                <p class="mt-2 text-xs">Memuat antrian order penunjang...</p>
              </td>
            </tr>
            <tr v-else-if="filteredOrders.length === 0" class="text-center">
              <td colspan="9" class="py-10 text-slate-400 text-xs">
                <i class="pi pi-inbox text-2xl text-slate-300 mb-1 block"></i>
                <span>Tidak ada antrian order penunjang dalam kategori ini.</span>
              </td>
            </tr>
            <tr v-for="(o, index) in filteredOrders" :key="o.id" class="hover:bg-[#F0F6F8]/60 transition">
              <td class="py-2.5 px-3 text-center font-mono text-slate-400 text-xs">{{ index + 1 }}</td>
              <td class="py-2.5 px-3">
                <span class="font-mono font-bold text-[#0F766E] text-xs block leading-tight">{{ o.nomor_order }}</span>
                <span class="text-[11px] text-slate-400">{{ formatDate(o.created_at) }}</span>
              </td>
              <td class="py-2.5 px-3">
                <div class="flex items-center gap-2">
                  <div class="w-6 h-6 rounded bg-[#E6F2F5] text-[#0F766E] flex items-center justify-center font-bold text-xs shrink-0">
                    {{ (o.profile_anak?.nama_anak || 'A').charAt(0) }}
                  </div>
                  <div>
                    <p class="font-bold text-slate-900 leading-tight">{{ o.profile_anak?.nama_anak }}</p>
                    <span class="text-[11px] font-mono text-slate-500">RM: {{ o.profile_anak?.no_rm }}</span>
                  </div>
                </div>
              </td>
              <td class="py-2.5 px-3">
                <span class="px-2 py-0.5 rounded font-mono text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                  {{ o.registrasi_anak?.no_regis || 'REG-AKTIF' }}
                </span>
              </td>
              <td class="py-2.5 px-3 text-xs text-slate-700">
                <span class="font-medium block">{{ o.dokter?.nama || 'Dokter Spesialis' }}</span>
                <span class="text-[10.5px] text-slate-400">Order EMR</span>
              </td>
              <td class="py-2.5 px-3">
                <div class="flex items-center gap-1.5">
                  <span
                    class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                    :class="{
                      'bg-sky-50 text-sky-800 border border-sky-200': o.jenis_order === 'laboratorium',
                      'bg-purple-50 text-purple-800 border border-purple-200': o.jenis_order === 'radiologi',
                      'bg-teal-50 text-teal-800 border border-teal-200': o.jenis_order !== 'laboratorium' && o.jenis_order !== 'radiologi'
                    }"
                  >
                    {{ o.jenis_order }}
                  </span>
                  <span class="font-semibold text-slate-900 text-xs">{{ o.nama_tindakan }}</span>
                </div>
                <p v-if="o.catatan_klinis" class="text-[11px] text-slate-500 mt-0.5 italic line-clamp-1">
                  "{{ o.catatan_klinis }}"
                </p>
              </td>
              <td class="py-2.5 px-3 text-right font-mono font-semibold text-slate-800">
                {{ formatRupiah(o.tarif) }}
              </td>
              <td class="py-2.5 px-3 text-center">
                <span
                  v-if="o.status === 'selesai' || o.status === 'diverifikasi'"
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  MASUK BILL
                </span>
                <span
                  v-else-if="o.status === 'dibatalkan'"
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 text-rose-800 border border-rose-200"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                  DIBATALKAN
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-800 border border-amber-200 animate-pulse"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                  BELUM MASUK BILL
                </span>
              </td>
              <td class="py-2.5 px-3 text-right">
                <button
                  type="button"
                  @click="openVerifikasiModal(o)"
                  class="px-2.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 transition cursor-pointer shadow-2xs ml-auto"
                  :class="o.status === 'selesai' ? 'bg-[#E6F2F5] text-[#0F766E] hover:bg-teal-100 border border-[#CBE3E9]' : 'bg-[#0F766E] text-white hover:bg-[#115E59]'"
                >
                  <i :class="o.status === 'selesai' ? 'pi pi-eye' : 'pi pi-check'" class="text-[10px]"></i>
                  <span>{{ o.status === 'selesai' ? 'Lihat Hasil' : 'Proses & Input Hasil' }}</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL VERIFIKASI & INPUT HASIL PENUNJANG -->
    <div
      v-if="showModal && selectedOrder"
      class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
    >
      <div class="bg-white border border-slate-200 rounded-2xl shadow-xl w-full max-w-xl overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-150">
        <!-- Header -->
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E]">
              <i class="pi pi-sliders-h text-sm"></i>
            </div>
            <div>
              <h3 class="font-bold text-slate-900 text-sm">
                Verifikasi Order Penunjang & Input Hasil
              </h3>
              <p class="text-[11px] text-slate-500">Konfirmasi pelaksanaan tindakan medis diagnostik sebelum masuk ke faktur billing registrasi</p>
            </div>
          </div>
          <button
            type="button"
            @click="showModal = false"
            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition"
          >
            <i class="pi pi-times text-xs"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="p-5 flex-1 min-h-0 overflow-y-auto custom-scrollbar space-y-3.5 text-xs">
          <!-- Context Banner -->
          <div class="bg-[#E6F2F5] border border-[#CBE3E9] p-3.5 rounded-xl space-y-1.5">
            <div class="flex items-center justify-between">
              <span class="font-mono text-xs font-bold text-[#0F766E]">{{ selectedOrder.nomor_order }}</span>
              <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-white text-slate-800 border border-[#CBE3E9]">
                Regis: {{ selectedOrder.registrasi_anak?.no_regis || 'REG-AKTIF' }}
              </span>
            </div>
            <div class="flex items-center justify-between flex-wrap gap-2">
              <div>
                <h4 class="text-sm font-bold text-slate-900">{{ selectedOrder.profile_anak?.nama_anak }} ({{ selectedOrder.profile_anak?.no_rm }})</h4>
                <p class="text-[11px] text-slate-600">Dokter Pemohon: <strong>{{ selectedOrder.dokter?.nama || 'Dokter Spesialis' }}</strong></p>
              </div>
              <div>
                <span class="px-2 py-0.5 rounded text-[10.5px] font-bold uppercase tracking-wider bg-white border border-[#CBE3E9] text-[#0F766E]">
                  {{ selectedOrder.jenis_order }}
                </span>
              </div>
            </div>
          </div>

          <!-- Tindakan Box -->
          <div class="border border-slate-200 rounded-xl p-3.5 space-y-2 bg-slate-50">
            <div class="flex justify-between items-center">
              <span class="text-slate-500 font-medium">Tindakan / Pemeriksaan:</span>
              <span class="font-bold text-slate-900 text-xs">{{ selectedOrder.nama_tindakan }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-slate-500 font-medium">Tarif Tindakan Masuk Bill:</span>
              <span class="font-mono font-bold text-[#0F766E] text-sm">{{ formatRupiah(selectedOrder.tarif) }}</span>
            </div>
            <div v-if="selectedOrder.catatan_klinis" class="pt-2 border-t border-slate-200 text-slate-600">
              <span class="text-[11px] font-semibold text-slate-700 block mb-0.5">Catatan Indikasi Dokter:</span>
              <p class="italic text-[11.5px]">"{{ selectedOrder.catatan_klinis }}"</p>
            </div>
          </div>

          <!-- Input Hasil -->
          <div>
            <label class="block font-semibold text-slate-700 mb-1">
              Hasil Pemeriksaan / Nilai Rujukan / Interpretasi Radiologi: <span class="text-rose-500">*</span>
            </label>
            <textarea
              v-model="hasilPemeriksaan"
              rows="4"
              placeholder="Masukkan kesimpulan hasil analisis laboratorium atau bacaan radiologi..."
              class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
              :disabled="selectedOrder.status === 'selesai'"
            ></textarea>
            <p class="text-[10.5px] text-slate-500 mt-1">Hasil ini otomatis dapat dilihat oleh Dokter pada layar EMR dan terlampir pada resume medis anak.</p>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between gap-2 shrink-0 bg-white">
          <div>
            <button
              v-if="selectedOrder.status !== 'selesai' && selectedOrder.status !== 'dibatalkan'"
              type="button"
              @click="cancelOrder"
              class="px-3.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 font-semibold text-xs transition"
            >
              <i class="pi pi-times-circle text-xs mr-1"></i>
              Batalkan Order
            </button>
          </div>

          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="showModal = false"
              class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold text-xs transition"
            >
              Tutup
            </button>

            <button
              v-if="selectedOrder.status !== 'selesai'"
              type="button"
              @click="approveOrder"
              :disabled="saving"
              class="btn-primary-clinical text-xs py-2 px-4 flex items-center gap-1.5"
            >
              <i v-if="saving" class="pi pi-spin pi-spinner text-xs"></i>
              <i v-else class="pi pi-check text-xs"></i>
              <span>Verifikasi Hasil & Masukkan ke Bill Registrasi</span>
            </button>
            <span v-else class="text-xs text-emerald-700 font-bold bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">
              <i class="pi pi-check text-xs mr-1"></i> Telah Masuk ke Bill Registrasi
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const orders = ref([])
const loading = ref(false)
const saving = ref(false)
const activeFilter = ref('menunggu')
const searchQuery = ref('')
const showModal = ref(false)
const selectedOrder = ref(null)
const hasilPemeriksaan = ref('')

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(val || 0)
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const fetchOrders = async () => {
  loading.value = true
  try {
    const res = await api.get('/pediatric-workflow/verifikasi/antrian-penunjang', {
      params: { status: 'semua' }
    })
    orders.value = Array.isArray(res.data) ? res.data : []
  } catch (err) {
    console.error('Error fetching penunjang orders:', err)
  } finally {
    loading.value = false
  }
}

const refreshData = () => {
  fetchOrders()
}

const countMenunggu = computed(() => {
  return orders.value.filter(o => o.status === 'menunggu_verifikasi' || o.status === 'diajukan').length
})

const countLab = computed(() => {
  return orders.value.filter(o => o.jenis_order === 'laboratorium').length
})

const countRad = computed(() => {
  return orders.value.filter(o => o.jenis_order === 'radiologi').length
})

const countSelesai = computed(() => {
  return orders.value.filter(o => o.status === 'selesai' || o.status === 'diverifikasi').length
})

const filteredOrders = computed(() => {
  let list = orders.value

  if (activeFilter.value === 'menunggu') {
    list = list.filter(o => o.status === 'menunggu_verifikasi' || o.status === 'diajukan')
  } else if (activeFilter.value === 'laboratorium') {
    list = list.filter(o => o.jenis_order === 'laboratorium')
  } else if (activeFilter.value === 'radiologi') {
    list = list.filter(o => o.jenis_order === 'radiologi')
  } else if (activeFilter.value === 'selesai') {
    list = list.filter(o => o.status === 'selesai' || o.status === 'diverifikasi')
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    list = list.filter(o =>
      (o.nomor_order || '').toLowerCase().includes(q) ||
      (o.nama_tindakan || '').toLowerCase().includes(q) ||
      (o.profile_anak?.nama_anak || '').toLowerCase().includes(q) ||
      (o.profile_anak?.no_rm || '').toLowerCase().includes(q) ||
      (o.registrasi_anak?.no_regis || '').toLowerCase().includes(q) ||
      (o.dokter?.nama || '').toLowerCase().includes(q)
    )
  }

  return list
})

const openVerifikasiModal = (order) => {
  selectedOrder.value = order
  hasilPemeriksaan.value = order.hasil_pemeriksaan || ''
  showModal.value = true
}

const approveOrder = async () => {
  if (!selectedOrder.value) return
  if (!hasilPemeriksaan.value.trim()) {
    toast.error('Mohon isi hasil pemeriksaan atau kesimpulan diagnostik terlebih dahulu!')
    return
  }

  saving.value = true
  try {
    const res = await api.post(`/pediatric-workflow/verifikasi/penunjang/${selectedOrder.value.id}/approve`, {
      hasil_pemeriksaan: hasilPemeriksaan.value
    })
    toast.success(res.data?.message || 'Order penunjang berhasil diverifikasi & biaya masuk ke bill registrasi!')
    showModal.value = false
    fetchOrders()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal memverifikasi order penunjang.')
  } finally {
    saving.value = false
  }
}

const cancelOrder = async () => {
  const alasan = prompt('Masukkan alasan pembatalan order penunjang:')
  if (!alasan || !alasan.trim()) return

  saving.value = true
  try {
    const res = await api.post(`/pediatric-workflow/verifikasi/penunjang/${selectedOrder.value.id}/reject`, {
      alasan_batal: alasan
    })
    toast.info(res.data?.message || 'Order penunjang berhasil dibatalkan.')
    showModal.value = false
    fetchOrders()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal membatalkan order penunjang.')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  fetchOrders()
})
</script>
