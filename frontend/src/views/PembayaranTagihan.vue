<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header & Search -->
    <div class="flex justify-between items-center mb-6 max-w-6xl mx-auto">
      <h1 class="text-2xl font-bold text-indigo-600">Tagihan & Pembayaran</h1>
      <input
        v-model="search"
        type="text"
        placeholder="Cari nama anak atau no registrasi..."
        class="px-4 py-2 border rounded-xl w-64 focus:ring-2 focus:ring-indigo-500"
      />
    </div>

    <!-- Dashboard Cards -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="item in filteredRegistrasi"
        :key="item.id"
        class="bg-white rounded-2xl shadow p-4 flex flex-col justify-between"
      >
        <div>
          <h3 class="font-bold text-lg">Nama Anak : {{ item.profile_anak?.nama_anak || '-' }}</h3>
          <p class="text-s">Nama Orangtua :  {{ item.nama_ayah || '-' }} - {{ item.nama_ibu || '-' }} </p>
          <p class="text-gray-500 text-sm">No Regis: {{ item.no_regis }}</p>
          <p class="text-gray-500 text-sm">Layanan: {{ pelayanans(item).length }}</p>
          <p class="text-indigo-600 font-semibold mt-1">
            Rp {{ totalTagihan(item).toLocaleString() }}
          </p>
        </div>

        <div class="mt-4 flex justify-between items-center gap-3">
          <span
            class="px-3 py-1 rounded-full text-xs font-semibold"
            :class="item?.pembayarans?.[0]?.status === 'lunas'
              ? 'bg-green-100 text-green-700'
              : 'bg-red-100 text-red-700'"

          >
            {{ item?.pembayarans?.[0]?.status === 'lunas' ? 'LUNAS' : 'BELUM LUNAS' }}
          </span>

          <div class="flex flex-wrap xl:flex-nowrap gap-2 justify-end">
          <button
            @click="bukaDetail(item)"
            class="px-2 py-1 border rounded-lg text-sm hover:bg-gray-100"
          >
            Detail
          </button>

          <button
            v-if="!item?.pembayarans?.length"
            @click="verifikasiPembayaran(item)"
            class="px-2 py-1 bg-orange-500 text-white rounded-lg text-sm hover:bg-orange-600"
          >
            Send Bill
          </button>

          <button
            v-if="!item?.pembayarans?.length"
            @click="bukaBayar(item)"
            class="px-2 py-1 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700"
          >
            Bayar
          </button>

          <button
            v-if="item?.pembayarans?.length"
            @click="cetakInvoice(item)"
            class="px-2 py-1 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700"
          >
            Cetak
          </button>

          <button
            @click="kirimEmail(item)"
            class="px-2 py-1 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700"
          >
            Email
          </button>
        </div>

        </div>
      </div>

      <div v-if="!filteredRegistrasi.length" class="col-span-full text-center text-gray-400 py-10">
        Tidak ada data
      </div>
    </div>

    <!-- Detail Modal -->
    <div v-if="showDetail" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center overflow-y-auto">
      <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl p-6">
        <div class="border-b pb-4 mb-6 flex justify-between items-center">
          <h2 class="text-lg font-bold">Detail Tagihan Pasien</h2>
          <button @click="showDetail = false" class="px-3 py-1 rounded-lg border hover:bg-gray-100">
            Tutup
          </button>
        </div>

        <div class="mb-6">
          <h3 class="font-semibold mb-2">Informasi Pasien</h3>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <p class="text-gray-500">Nama Anak</p>
              <p class="font-medium">{{ selected.profile_anak?.nama_anak }}</p>
            </div>
            <div>
              <p class="text-gray-500">Terapis</p>
              <p class="font-medium">{{ selected.terapis?.nama }}</p>
            </div>
            <div>
              <p class="text-gray-500">No Registrasi</p>
              <p class="font-medium">{{ selected.no_regis }}</p>
            </div>
            <div>
              <p class="text-gray-500">Tanggal Registrasi</p>
              <p class="font-medium">{{ selected.tgl_regis }}</p>
            </div>
          </div>
        </div>

        <div class="mb-6 border rounded-xl">
          <div class="px-4 py-3 font-semibold border-b">Rincian Tagihan</div>
          <div class="p-4 space-y-2 text-sm">
            <div
              v-for="p in pelayanans(selected)"
              :key="p.id"
              class="flex justify-between"
            >
              <div>
                <p class="font-medium">{{ p.layanan?.layanan }}</p>
                <p class="text-xs text-gray-400">
                  {{ p.tanggal_penjadwalan }} · {{ p.terapis?.nama }}
                </p>
              </div>
              <span>Rp {{ (p.qty * p.harga).toLocaleString() }}</span>
            </div>

            <div class="border-t pt-3 flex justify-between font-bold">
              <span>Total Tagihan</span>
              <span class="text-indigo-600">
                Rp {{ totalTagihan(selected).toLocaleString() }}
              </span>
            </div>
            <!-- === TAMBAHAN DISKON & TOTAL BAYAR (DETAIL) === -->
            <div
              v-if="pembayaranAktif"
              class="border-t pt-3 mt-3 space-y-1 text-sm"
            >
              <div
                v-if="diskonDetail > 0"
                class="flex justify-between text-green-600"
              >
                <span>Diskon</span>
                <span>- Rp {{ diskonDetail.toLocaleString() }}</span>
              </div>

              <div class="flex justify-between font-bold text-indigo-700">
                <span>Total Bayar</span>
                <span>
                  Rp {{ Number(pembayaranAktif.jumlah_bayar).toLocaleString() }}
                </span>
              </div>
            </div>

            <!-- ===== BUTTON UBAH STATUS VERIFIKASI ===== -->
            <div class="mt-4 flex gap-3">

              <button
                v-if="pembayaranAktif.status_verifikasi === 'menunggu'"
                @click="ubahStatusVerifikasi('diterima')"
                class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700"
              >
                Terima Pembayaran
              </button>

              <button
                v-if="pembayaranAktif.status_verifikasi === 'menunggu'"
                @click="ubahStatusVerifikasi('ditolak')"
                class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700"
              >
                Tolak Pembayaran
              </button>

            </div>


          </div>
        </div>
        <!-- ===== STATUS VERIFIKASI PEMBAYARAN ===== -->
        <div
          v-if="pembayaranAktif"
          class="mt-6 border rounded-xl p-4 bg-gray-50"
        >
          <h3 class="font-semibold mb-3">Status Pembayaran</h3>

          <div class="flex gap-4 flex-wrap">
            <span
              class="px-3 py-1 rounded-full text-sm font-semibold"
              :class="{
                'bg-yellow-100 text-yellow-700': pembayaranAktif.status_verifikasi === 'menunggu',
                'bg-green-100 text-green-700': pembayaranAktif.status_verifikasi === 'diterima',
                'bg-red-100 text-red-700': pembayaranAktif.status_verifikasi === 'ditolak'
              }"
            >
              Verifikasi :
              {{ pembayaranAktif.status_verifikasi?.toUpperCase() || 'BELUM UPLOAD' }}
            </span>

            <span
              class="px-3 py-1 rounded-full text-sm font-semibold"
              :class="pembayaranAktif.status === 'lunas'
                ? 'bg-green-100 text-green-700'
                : 'bg-orange-100 text-orange-700'"
            >
              {{ pembayaranAktif.status?.toUpperCase() }}
            </span>
          </div>

          <!-- ===== PREVIEW BUKTI ===== -->
          <div
            v-if="pembayaranAktif.bukti_pembayaran"
            class="mt-4"
          >
            <p class="text-sm font-medium mb-2">Bukti Pembayaran</p>

            <!-- IMAGE -->
            <div
              v-if="isImage(pembayaranAktif.bukti_pembayaran)"
              class="space-y-2"
            >
              <img
                :src="fileUrl(pembayaranAktif.bukti_pembayaran)"
                class="max-h-64 rounded-lg border shadow cursor-pointer"
                @click="openImage(pembayaranAktif.bukti_pembayaran)"
              />

              <button
                @click="openImage(pembayaranAktif.bukti_pembayaran)"
                class="inline-flex items-center gap-2 px-4 py-2
                      bg-indigo-600 text-white rounded-lg
                      hover:bg-indigo-700"
              >
                <i class="pi pi-eye"></i>
                Lihat Gambar
              </button>
            </div>

            <div
            v-if="showImage"
            class="fixed inset-0 z-50 flex items-center justify-center"
          >
            <div class="absolute inset-0 bg-black/70" @click="closeImage"></div>

            <img
              :src="imageUrl"
              class="relative max-h-[90vh] max-w-[90vw] rounded-xl shadow-2xl"
            />
          </div>

            <!-- PDF -->
            <a
              v-else
              :href="fileUrl(pembayaranAktif.bukti_pembayaran)"
              target="_blank"
              class="inline-flex items-center gap-2 px-4 py-2
                    bg-indigo-600 text-white rounded-lg
                    hover:bg-indigo-700"
            >
              <i class="pi pi-download"></i>
              Download Bukti (PDF)
            </a>
          </div>
        </div>

      </div>
    </div>

    <!-- Bayar Modal -->
    <div v-if="showBayar" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center overflow-y-auto">
      <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl p-6">
        <div class="border-b pb-4 mb-6 flex justify-between items-center">
          <h2 class="text-lg font-bold">Pembayaran Pasien</h2>
          <button @click="showBayar = false" class="px-3 py-1 rounded-lg border hover:bg-gray-100">
            Batal
          </button>
        </div>

        <!-- INFO -->
        <div class="mb-6">
          <p class="text-sm"><b>Nama Anak:</b> {{ selected.profile_anak?.nama_anak }}</p>
        </div>

        <!-- RINCIAN -->
        <div class="mb-6 border rounded-xl">
          <div class="px-4 py-3 font-semibold border-b">Rincian Tagihan</div>
          <div class="p-4 space-y-2 text-sm">
            <div v-for="p in pelayanans(selected)" :key="p.id" class="flex justify-between">
              <span>{{ p.layanan?.layanan }}</span>
              <span>Rp {{ (p.qty * p.harga).toLocaleString() }}</span>
            </div>
          </div>
        </div>

        <!-- DATA PEMBAYARAN (KODE ANDA) -->
        <div class="mb-6">
          <h3 class="font-semibold mb-2">Data Pembayaran</h3>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <label class="label">Tanggal Pembayaran</label>
              <input type="date" v-model="formBayar.tanggal_bayar" class="input" />
            </div>
            <div>
              <label class="label">Metode Pembayaran</label>
              <select v-model="formBayar.metode_pembayaran" class="input">
                <option value="cash">Cash</option>
                <option value="transfer">Transfer</option>
                <option value="qris">QRIS</option>
              </select>
            </div>
            <div>
              <label class="label">Jumlah Dibayar</label>
              <input type="number" v-model="formBayar.jumlah_bayar" class="input" />
            </div>
            <div>
              <label class="label">Status Pembayaran</label>
              <select v-model="formBayar.status" class="input">
                <option value="lunas">Lunas</option>
                <option value="belum_lunas">Belum Lunas</option>
              </select>
            </div>
          </div>
        </div>

        <!-- === TAMBAHAN PROMO === -->
        <div class="mb-6">
          <label class="label">Promo</label>
          <select v-model="selectedPromo" @change="hitungDiskon" class="input">
            <option :value="null">Tanpa Promo</option>
            <option v-for="p in promos" :key="p.id" :value="p.id">
              {{ p.nama_promo }}
              ({{ p.tipe_diskon === 'persen' ? p.nilai_diskon + '%' : 'Rp ' + p.nilai_diskon.toLocaleString() }})
            </option>
          </select>
        </div>

        <!-- === RINGKASAN === -->
        <div class="bg-gray-50 rounded-xl p-4 mb-6 text-sm space-y-1">
          <div class="flex justify-between">
            <span>Total Tagihan</span>
            <span>Rp {{ formBayar.total_tagihan.toLocaleString() }}</span>
          </div>
          <div v-if="diskon > 0" class="flex justify-between text-green-600">
            <span>Diskon</span>
            <span>- Rp {{ diskon.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between font-bold text-indigo-600">
            <span>Total Bayar</span>
            <span>Rp {{ formBayar.jumlah_bayar.toLocaleString() }}</span>
          </div>
        </div>

        <div class="flex justify-end">
          <button @click="simpanBayar" class="px-5 py-2 rounded-lg bg-indigo-600 text-white">
            Simpan Pembayaran
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../axios'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const registrasiList = ref([])
const loading = ref(false)
const showDetail = ref(false)
const showBayar = ref(false)
const selected = ref(null)

const promos = ref([])
const selectedPromo = ref(null)
const diskon = ref(0)

const showImage = ref(false)
const imageUrl = ref('')

function openImage(file) {
  imageUrl.value = fileUrl(file)
  showImage.value = true
}

function closeImage() {
  showImage.value = false
  imageUrl.value = ''
}

const formBayar = ref({
  tanggal_bayar: '',
  metode_pembayaran: 'cash',
  total_tagihan: 0,
  jumlah_bayar: 0,
  status: 'lunas'
})

const search = ref('')
const baseUrl = import.meta.env.VITE_API_BASE_URL

onMounted(async () => {
  loading.value = true
  try {
    const [regisRes, promoRes] = await Promise.all([
      api.get('/registrasi-anak'),
      api.get('/promosi/aktif')
    ])
    registrasiList.value = regisRes.data || []
    promos.value = promoRes.data || []
  } catch {
    toast.error('Gagal memuat data')
  } finally {
    loading.value = false
  }
})

const pelayanans = item => Array.isArray(item?.pelayanans) ? item.pelayanans : []

const totalTagihan = item =>
  pelayanans(item).reduce((s, p) => s + Number(p.qty || 0) * Number(p.harga || 0), 0)

const pembayaranAktif = computed(() => {
  return selected.value?.pembayarans?.length
    ? selected.value.pembayarans[0]
    : null
})

const diskonDetail = computed(() => {
  if (!pembayaranAktif.value) return 0
  return Math.max(
    0,
    Number(pembayaranAktif.value.total_tagihan || 0) -
    Number(pembayaranAktif.value.jumlah_bayar || 0)
  )
})


const bukaDetail = item => {
  selected.value = item
  showDetail.value = true
}

const bukaBayar = item => {
  selected.value = item
  const total = totalTagihan(item)
  formBayar.value = {
    tanggal_bayar: new Date().toISOString().slice(0, 10),
    metode_pembayaran: 'cash',
    total_tagihan: total,
    jumlah_bayar: total,
    status: 'lunas'
  }
  selectedPromo.value = null
  diskon.value = 0
  showBayar.value = true
}

const hitungDiskon = () => {
  diskon.value = 0
  const promo = promos.value.find(p => p.id === selectedPromo.value)
  if (!promo) {
    formBayar.value.jumlah_bayar = formBayar.value.total_tagihan
    return
  }
  if (promo.tipe_diskon === 'persen') {
    diskon.value = (promo.nilai_diskon / 100) * formBayar.value.total_tagihan
  } else {
    diskon.value = promo.nilai_diskon
  }
  formBayar.value.jumlah_bayar = Math.max(0, formBayar.value.total_tagihan - diskon.value)
}

const simpanBayar = async () => {
  try {
    await api.post('/pembayaran-registrasi', {
      registrasi_anak_id: selected.value.id,
      tanggal_bayar: formBayar.value.tanggal_bayar,
      metode_pembayaran: formBayar.value.metode_pembayaran,
      total_tagihan: formBayar.value.total_tagihan,
      jumlah_bayar: formBayar.value.jumlah_bayar,
      promo_id: selectedPromo.value,
      status: formBayar.value.status
    })
    toast.success('Pembayaran berhasil')
    showBayar.value = false
    const res = await api.get('/registrasi-anak')
    registrasiList.value = res.data
  } catch {
    toast.error('Gagal menyimpan pembayaran')
  }
}

const cetakInvoice = item => {
  if (item?.pembayarans?.length) {
    window.open(`${baseUrl}/invoice/${item.id}/print`, '_blank')
  }
}

const kirimEmail = async item => {
  await axios.post(`${baseUrl}/invoice/${item.id}/send-email`)
  toast.success('Email terkirim')
}

// const filteredRegistrasi = computed(() => {
//   if (!search.value) return registrasiList.value
//   return registrasiList.value.filter(r =>
//     r.profile_anak.nama_anak.toLowerCase().includes(search.value.toLowerCase()) || r.nama_ayah.toLowerCase().includes(search.value.toLowerCase()) ||
//     r.no_regis.toLowerCase().includes(search.value.toLowerCase())
//   )
// })

const filteredRegistrasi = computed(() => {
  if (!search.value) return registrasiList.value

  const key = search.value.toLowerCase()

  return registrasiList.value.filter(r => {
    const namaAnak = r.profile_anak?.nama_anak?.toLowerCase() || ''
    const namaAyah = r.nama_ayah?.toLowerCase() || ''
    const namaIbu  = r.nama_ibu?.toLowerCase() || ''
    const noRegis  = r.no_regis?.toLowerCase() || ''

    return (
      namaAnak.includes(key) ||
      namaAyah.includes(key) ||
      namaIbu.includes(key) ||
      noRegis.includes(key)
    )
  })
})


const verifikasiPembayaran = async (item) => {
  try {
    await api.post('/pembayaran-registrasi/request-verifikasi', {
      registrasi_anak_id: item.id,
      tanggal_bayar: new Date().toISOString().slice(0, 10),
      total_tagihan: totalTagihan(item)
    })

    toast.success('Tagihan dibuat, menunggu upload bukti pembayaran')

    const res = await api.get('/registrasi-anak')
    registrasiList.value = res.data
  } catch (e) {
    toast.error('Gagal membuat tagihan pembayaran')
  }
}

const fileUrl = (path) => {
  if (!path) return ''
  return `${baseUrl}/storage/${path}`
}

const isImage = (path) => {
  return /\.(jpg|jpeg|png)$/i.test(path)
}

const ubahStatusVerifikasi = async (status) => {
  if (!pembayaranAktif.value) return

  try {
    await api.post(
      `/pembayaran-registrasi/${pembayaranAktif.value.id}/verifikasi-admin`,
      {
        status_verifikasi: status,
        status_pembayaran: status === 'diterima' ? 'lunas' : 'pending'
      }
    )

    toast.success(
      status === 'diterima'
        ? 'Pembayaran diterima & dilunasi'
        : 'Pembayaran ditolak'
    )

    // refresh data registrasi
    const res = await api.get('/registrasi-anak')
    registrasiList.value = res.data

    // update selected agar UI langsung berubah
    const updated = res.data.find(r => r.id === selected.value.id)
    if (updated) selected.value = updated

  } catch (e) {
    toast.error('Gagal mengubah status verifikasi')
  }
}



</script>

<style scoped>
.input {
  @apply w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500;
}
.label {
  @apply text-sm font-medium text-gray-600;
}

.btn {
  @apply px-4 py-2 rounded-lg text-sm whitespace-nowrap;
}

</style>
