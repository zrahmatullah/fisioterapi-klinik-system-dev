<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-receipt"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Tagihan & Riwayat Pembayaran</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Daftar tagihan dan riwayat histori pembayaran layanan anak</p>
        </div>
      </div>
    </div>

    <!-- TABLE CARD -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-left border-collapse text-[12.5px]">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3">Tanggal Bayar</th>
              <th class="py-2.5 px-3">Nama Anak</th>
              <th class="py-2.5 px-3">No Registrasi</th>
              <th class="py-2.5 px-3">Ruangan</th>
              <th class="py-2.5 px-3">Total</th>
              <th class="py-2.5 px-3">Status</th>
              <th class="py-2.5 px-3 text-center w-24">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="p in pembayaran"
              :key="p.id"
              class="hover:bg-[#F0F6F8]/60 transition"
            >
              <td class="py-2 px-3 text-slate-600 tabular-nums">{{ formatTanggal(p.tanggal_bayar) }}</td>
              <td class="py-2 px-3 font-semibold text-slate-800">
                {{ p.registrasi?.profile_anak?.nama_anak }}
              </td>
              <td class="py-2 px-3 font-mono text-xs text-slate-600">
                {{ p.registrasi?.no_regis }}
              </td>
              <td class="py-2 px-3 text-slate-700">
                {{ p.registrasi?.ruangan?.ruangan }}
              </td>
              <td class="py-2 px-3 font-semibold text-slate-900 tabular-nums">
                Rp {{ formatRupiah(p.total_tagihan) }}
              </td>
              <td class="py-2 px-3">
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                  :class="p.status === 'lunas'
                    ? 'bg-emerald-100 text-emerald-800 border border-emerald-200'
                    : 'bg-rose-100 text-rose-800 border border-rose-200'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="p.status === 'lunas' ? 'bg-emerald-600' : 'bg-rose-600'"></span>
                  {{ p.status?.toUpperCase() }}
                </span>
              </td>
              <td class="py-2 px-3 text-center">
                <button
                  type="button"
                  @click="openDetail(p)"
                  class="h-[28px] px-3 bg-[#0F766E] text-white rounded-lg hover:bg-[#115E59] active:scale-95 text-xs font-medium inline-flex items-center gap-1 transition shadow-2xs cursor-pointer"
                >
                  <i class="pi pi-eye text-[11px]"></i>
                  <span>Detail</span>
                </button>
              </td>
            </tr>

            <tr v-if="pembayaran.length === 0">
              <td colspan="7" class="text-center text-slate-400 py-12">
                <i class="pi pi-folder-open text-2xl text-slate-300 block mb-1"></i>
                <span class="text-xs">Belum ada riwayat pembayaran</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- FOOTER -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] text-slate-500 flex items-center justify-between">
        <span>Menampilkan {{ pembayaran.length }} data transaksi</span>
      </div>
    </div>

    <!-- MODAL DETAIL -->
    <teleport to="body">
      <div
        v-if="showDetail"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-4xl rounded-xl max-h-[88dvh] flex flex-col overflow-hidden">

          <!-- HEADER -->
          <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-receipt text-[#2DD4BF] text-sm"></i>
              <div>
                <h3 class="font-semibold text-[13px] leading-tight">Detail Pembayaran & Bukti Transaksi</h3>
                <p class="text-[10px] text-white/70 font-mono">
                  {{ selectedPembayaran?.nomor_pembayaran || '-' }}
                </p>
              </div>
            </div>
            <button
              type="button"
              @click="showDetail = false"
              class="w-6 h-6 rounded flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition"
            >
              ✕
            </button>
          </div>

          <!-- BODY -->
          <div class="px-5 py-4 flex-1 min-h-0 overflow-y-auto custom-scrollbar text-[12.5px] text-slate-700 space-y-4">
            <!-- INFO ANAK -->
            <div class="bg-[#F0F6F8] border border-[#DCE8EC] rounded-xl p-3">
              <h4 class="font-bold text-slate-800 text-xs mb-2">Informasi Pasien Anak</h4>
              <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-xs">
                <Info label="Nama Anak" :value="selected?.profile_anak?.nama_anak" />
                <Info label="No Registrasi" :value="selected?.no_regis" />
                <Info label="Tanggal Registrasi" :value="formatTanggal(selected?.tgl_regis)" />
                <Info label="Ruangan" :value="selected?.ruangan?.ruangan" />
                <Info label="Terapis" :value="selected?.terapis?.nama" />
              </div>
            </div>

            <!-- LAYANAN -->
            <div>
              <h4 class="font-bold text-slate-800 text-xs mb-2">Rincian Layanan</h4>
              <div class="border border-slate-200 rounded-lg overflow-hidden">
                <table class="w-full text-xs">
                  <thead class="bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold text-slate-600">
                    <tr>
                      <th class="px-3 py-2 text-left">Layanan</th>
                      <th class="px-3 py-2 text-left">Tanggal</th>
                      <th class="px-3 py-2 text-left">Terapis</th>
                      <th class="px-3 py-2 text-center">Qty</th>
                      <th class="px-3 py-2 text-right">Harga</th>
                      <th class="px-3 py-2 text-right">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr
                      v-for="l in selected?.pelayanans || []"
                      :key="l.id"
                      class="hover:bg-slate-50"
                    >
                      <td class="px-3 py-2 font-medium text-slate-800">{{ l.layanan?.layanan }}</td>
                      <td class="px-3 py-2 text-slate-600 tabular-nums">{{ formatTanggal(l.tanggal_penjadwalan) }}</td>
                      <td class="px-3 py-2 text-slate-700">{{ l.terapis?.nama }}</td>
                      <td class="px-3 py-2 text-center tabular-nums">{{ l.qty }}</td>
                      <td class="px-3 py-2 text-right tabular-nums">
                        Rp {{ formatRupiah(l.harga) }}
                      </td>
                      <td class="px-3 py-2 text-right font-semibold text-slate-900 tabular-nums">
                        Rp {{ formatRupiah(l.qty * l.harga) }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TOTAL SUMMARY CARD -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 space-y-2">
              <div class="flex justify-between text-xs">
                <span class="text-slate-600">Metode Pembayaran:</span>
                <span class="uppercase font-semibold text-slate-800">
                  {{ selectedPembayaran?.metode_pembayaran || 'Tunai / Cash' }}
                </span>
              </div>

              <div class="flex justify-between font-bold text-sm border-t border-slate-200 pt-2">
                <span class="text-slate-800">Total Tagihan:</span>
                <span class="text-[#0F766E] tabular-nums font-mono text-base">
                  Rp {{ formatRupiah(selectedPembayaran?.total_tagihan) }}
                </span>
              </div>
            </div>
          </div>

          <!-- FOOTER -->
          <div class="px-4 py-2.5 bg-slate-50/95 border-t border-slate-200/80 flex items-center justify-between shrink-0">
            <span
              class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold"
              :class="selectedPembayaran?.status === 'lunas'
                ? 'bg-emerald-100 text-emerald-800 border border-emerald-200'
                : 'bg-rose-100 text-rose-800 border border-rose-200'"
            >
              <span class="w-1.5 h-1.5 rounded-full" :class="selectedPembayaran?.status === 'lunas' ? 'bg-emerald-600' : 'bg-rose-600'"></span>
              {{ selectedPembayaran?.status?.toUpperCase() }}
            </span>

            <div class="flex gap-2 items-center">
              <!-- UPLOAD BUKTI -->
              <label
                v-if="selectedPembayaran?.status !== 'lunas'"
                class="h-[34px] px-3.5 bg-amber-600 text-white rounded-lg hover:bg-amber-700 active:scale-95 text-xs font-medium inline-flex items-center gap-1.5 cursor-pointer shadow-xs transition"
              >
                <i class="pi pi-upload text-xs"></i>
                <span>Upload Bukti</span>
                <input
                  type="file"
                  class="hidden"
                  accept="image/*,.pdf"
                  @change="uploadBukti($event)"
                />
              </label>

              <!-- CETAK -->
              <button
                type="button"
                v-if="selectedPembayaran?.status === 'lunas'"
                @click="cetakInvoiceFromRiwayat(selectedPembayaran)"
                class="h-[34px] px-3.5 bg-[#0F766E] text-white rounded-lg hover:bg-[#115E59] active:scale-95 text-xs font-medium inline-flex items-center gap-1.5 shadow-xs transition"
              >
                <i class="pi pi-print text-xs"></i>
                <span>Cetak Invoice</span>
              </button>

              <!-- LIHAT BUKTI -->
              <button
                type="button"
                v-if="selectedPembayaran?.bukti_pembayaran"
                @click="viewBukti(selectedPembayaran.bukti_pembayaran)"
                class="h-[34px] px-3.5 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 text-xs font-medium inline-flex items-center gap-1.5 transition"
              >
                <i class="pi pi-image text-xs text-[#0F766E]"></i>
                <span>Lihat Bukti</span>
              </button>

              <button
                type="button"
                @click="showDetail = false"
                class="h-[34px] px-3.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-medium transition"
              >
                Tutup
              </button>
            </div>
          </div>

        </div>
      </div>
    </teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'

const formatTanggal = (dateStr) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}/${month}/${year}`
}

const toast = useToast()
const baseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

const pembayaran = ref([])
const showDetail = ref(false)
const selected = ref({})
const selectedPembayaran = ref({})

onMounted(async () => {
  try {
    const res = await api.get('/riwayat-pembayaran-anak')
    pembayaran.value = res.data
  } catch (err) {
    console.error('Gagal mengambil data:', err)
  }
})

const openDetail = (p) => {
  selectedPembayaran.value = p
  selected.value = p.registrasi
  showDetail.value = true
}

const cetakInvoiceFromRiwayat = (pembayaran) => {
  if (!pembayaran?.registrasi?.id) return
  window.open(`${baseUrl}/invoice/${pembayaran.registrasi.id}/print`, '_blank')
}

const uploadBukti = async (e) => {
  const file = e?.target?.files?.[0]
  if (!file) {
    toast.error('File tidak ditemukan')
    return
  }

  const formData = new FormData()
  formData.append('pembayaran_id', selectedPembayaran.value.id)
  formData.append('bukti_pembayaran', file)

  try {
    await api.post('/pembayaran/upload-bukti', formData)
    toast.success('Bukti berhasil dikirim, menunggu verifikasi admin')
    showDetail.value = false

    const res = await api.get('/riwayat-pembayaran-anak')
    pembayaran.value = res.data
  } catch (err) {
    console.log('ERROR =', err.response?.data)
    toast.error(err.response?.data?.message || 'Gagal upload bukti pembayaran')
  }
}

const viewBukti = (path) => {
  if (!path) {
    toast.error('Bukti pembayaran belum tersedia')
    return
  }
  const url = `${baseUrl}/storage/${path}`
  window.open(url, '_blank')
}

const formatRupiah = (val) =>
  new Intl.NumberFormat('id-ID').format(val || 0)
</script>

<script>
export default {
  components: {
    Info: {
      props: ['label', 'value'],
      template: `
        <div>
          <p class="text-[11px] text-slate-500">{{ label }}</p>
          <p class="font-medium text-slate-800">{{ value || '-' }}</p>
        </div>
      `
    }
  }
}
</script>
