<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <!-- HEADER -->
<div class="bg-white rounded-xl shadow-sm p-5 mb-6">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

    <!-- TITLE -->
    <div>
      <h2 class="text-2xl font-semibold text-gray-800">
        Tagihan & Riwayat Pembayaran
      </h2>
      <p class="text-sm text-gray-500 mt-1">
        Daftar tagihan dan histori pembayaran layanan anak
      </p>
    </div>

    <!-- OPTIONAL ACTION (kalau mau nanti) -->
    <!--
    <div>
      <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
        Export
      </button>
    </div>
    -->

  </div>
</div>


    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-3 text-left">Tanggal Bayar</th>
            <th class="px-4 py-3 text-left">Nama Anak</th>
            <th class="px-4 py-3 text-left">No Registrasi</th>
            <th class="px-4 py-3 text-left">Ruangan</th>
            <th class="px-4 py-3 text-left">Total</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-center">Aksi</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="p in pembayaran"
            :key="p.id"
            class="border-b hover:bg-gray-50"
          >
          <td class="px-4 py-3">{{ formatTanggal(p.tanggal_bayar) }}</td>
            <td class="px-4 py-3">
              {{ p.registrasi?.profile_anak?.nama_anak }}
            </td>
            <td class="px-4 py-3">
              {{ p.registrasi?.no_regis }}
            </td>
            <td class="px-4 py-3">
              {{ p.registrasi?.ruangan?.ruangan }}
            </td>
            <td class="px-4 py-3 font-semibold">
              Rp {{ formatRupiah(p.total_tagihan) }}
            </td>
            <td class="px-4 py-3">
              <span
                :class="p.status === 'lunas'
                  ? 'text-green-600 font-semibold'
                  : 'text-orange-500 font-semibold'"
              >
                {{ p.status?.toUpperCase() }}
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <button
                @click="openDetail(p)"
                class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700"
              >
                Detail
              </button>
            </td>
          </tr>

          <tr v-if="pembayaran.length === 0">
            <td colspan="7" class="text-center text-gray-400 py-6">
              Belum ada riwayat pembayaran
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL DETAIL -->
    <transition name="fade">
      <div
        v-if="showDetail"
        class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center"
      >
        <div class="bg-white w-full max-w-5xl rounded-2xl shadow-xl p-6">

          <!-- HEADER -->
          <div class="border-b pb-4 mb-6 flex justify-between items-center">
            <div>
              <h2 class="text-lg font-bold">Detail Pembayaran</h2>
              <p class="text-sm text-gray-500">
                {{ selectedPembayaran?.nomor_pembayaran || '-' }}
              </p>
            </div>
            <button
              @click="showDetail = false"
              class="px-3 py-1 rounded-lg border hover:bg-gray-100"
            >
              Tutup
            </button>
          </div>

          <!-- INFO ANAK -->
          <div class="mb-6">
            <h3 class="font-semibold mb-2">Informasi Anak</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <Info label="Nama Anak" :value="selected?.profile_anak?.nama_anak" />
              <Info label="No Registrasi" :value="selected?.no_regis" />
              <Info label="Tanggal Registrasi" :value="formatTanggal(selected?.tgl_regis)" />
              <Info label="Ruangan" :value="selected?.ruangan?.ruangan" />
              <Info label="Terapis" :value="selected?.terapis?.nama" />
            </div>
          </div>

          <!-- LAYANAN -->
          <div class="mb-6">
            <h3 class="font-semibold mb-2">Detail Layanan</h3>
            <table class="w-full text-sm border rounded-xl overflow-hidden">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-3 py-2 text-left">Layanan</th>
                  <th class="px-3 py-2 text-left">Tanggal</th>
                  <th class="px-3 py-2 text-left">Terapis</th>
                  <th class="px-3 py-2 text-center">Qty</th>
                  <th class="px-3 py-2 text-right">Harga</th>
                  <th class="px-3 py-2 text-right">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="l in selected?.pelayanans || []"
                  :key="l.id"
                  class="border-t"
                >
                  <td class="px-3 py-2">{{ l.layanan?.layanan }}</td>
                  <td class="px-3 py-2">{{ formatTanggal(l.tanggal_penjadwalan) }}</td>
                  <td class="px-3 py-2">{{ l.terapis?.nama }}</td>
                  <td class="px-3 py-2 text-center">{{ l.qty }}</td>
                  <td class="px-3 py-2 text-right">
                    Rp {{ formatRupiah(l.harga) }}
                  </td>
                  <td class="px-3 py-2 text-right font-semibold">
                    Rp {{ formatRupiah(l.qty * l.harga) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- TOTAL -->
          <div class="border rounded-xl p-4">
            <div class="flex justify-between mb-2">
              <span>Metode Pembayaran</span>
              <span class="uppercase font-medium">
                {{ selectedPembayaran?.metode_pembayaran }}
              </span>
            </div>

            <div class="flex justify-between font-bold text-lg border-t pt-3">
              <span>Total</span>
              <span class="text-indigo-600">
                Rp {{ formatRupiah(selectedPembayaran?.total_tagihan) }}
              </span>
            </div>
          </div>

          <!-- FOOTER -->
          <div class="mt-6 flex justify-between items-center">
            <span
              class="inline-block px-4 py-2 rounded-full text-sm font-semibold"
              :class="
                selectedPembayaran?.status === 'lunas'
                  ? 'bg-green-100 text-green-700'
                  : 'bg-red-100 text-red-700'
              "
            >
              {{ selectedPembayaran?.status?.toUpperCase() }}
            </span>

            <div class="flex gap-3 items-center">
              <!-- UPLOAD BUKTI -->
              <label
                v-if="selectedPembayaran?.status !== 'lunas'"
                class="px-6 py-2 bg-orange-500 text-white rounded-xl hover:bg-orange-600 cursor-pointer"
              >
                Upload Bukti
                <input
                  type="file"
                  class="hidden"
                  accept="image/*,.pdf"
                  @change="uploadBukti($event)"
                />
              </label>


              <!-- CETAK -->
              <button
                v-if="selectedPembayaran?.status === 'lunas'"
                @click="cetakInvoiceFromRiwayat(selectedPembayaran)"
                class="px-6 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700"
              >
                Cetak Invoice
              </button>

              <!-- LIHAT BUKTI -->
              <button
                v-if="selectedPembayaran?.bukti_pembayaran"
                @click="viewBukti(selectedPembayaran.bukti_pembayaran)"
                class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700"
              >
                Lihat Bukti
              </button>

            </div>
          </div>

        </div>
      </div>
    </transition>
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
const baseUrl = import.meta.env.VITE_API_BASE_URL

const pembayaran = ref([])
const showDetail = ref(false)
const selected = ref({})
const selectedPembayaran = ref({})

onMounted(async () => {
  const res = await api.get('/riwayat-pembayaran-anak')
  pembayaran.value = res.data
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
  console.log('FILE =', file)

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
          <p class="text-gray-500">{{ label }}</p>
          <p class="font-medium">{{ value || '-' }}</p>
        </div>
      `
    }
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
