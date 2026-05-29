<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-indigo-50 p-6">

    <!-- ================= HEADER ================= -->
    <div
      class="relative overflow-hidden rounded-3xl
            bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600
            p-7 shadow-xl mb-6"
    >
      <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <div>
          <h1 class="text-3xl font-bold text-white">
            💳 Tagihan & Pembayaran
          </h1>

          <p class="text-white/80 mt-2">
            Monitoring pembayaran pasien, invoice, dan verifikasi pembayaran
          </p>
        </div>

        <div class="flex flex-col md:flex-row gap-3">

          <input
            v-model="search"
            type="text"
            placeholder="Cari nama anak / no registrasi..."
            class="bg-white/20 backdrop-blur-md
                  border border-white/20
                  text-white placeholder:text-white/70
                  px-4 py-3 rounded-2xl w-80
                  focus:outline-none focus:ring-2 focus:ring-white"
          />

        </div>

      </div>

      <div
        class="absolute right-0 top-0 w-72 h-72
              bg-white/10 rounded-full blur-3xl"
      />
    </div>

    <!-- ================= SUMMARY ================= -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

      <div class="summary-card">
        <p class="summary-label">Total Pasien</p>

        <h2 class="summary-value text-indigo-600">
          {{ registrasiList.length }}
        </h2>
      </div>

      <div class="summary-card">
        <p class="summary-label">Lunas</p>

        <h2 class="summary-value text-green-600">
          {{
            registrasiList.filter(
              r => r?.pembayarans?.[0]?.status === 'lunas'
            ).length
          }}
        </h2>
      </div>

      <div class="summary-card">
        <p class="summary-label">Belum Lunas</p>

        <h2 class="summary-value text-red-500">
          {{
            registrasiList.filter(
              r => r?.pembayarans?.[0]?.status !== 'lunas'
            ).length
          }}
        </h2>
      </div>

      <div class="summary-card">
        <p class="summary-label">Total Transaksi</p>

        <h2 class="summary-value text-fuchsia-600">
          Rp
          {{
            registrasiList
              .reduce((s, r) => s + totalTagihan(r), 0)
              .toLocaleString()
          }}
        </h2>
      </div>

    </div>

    <!-- ================= TABLE ================= -->
    <div
      class="bg-white rounded-3xl shadow-xl
            border border-gray-100 overflow-hidden"
    >

      <div class="overflow-x-auto">

        <table class="w-full text-sm">

          <thead class="bg-slate-100 sticky top-0 z-10">

            <tr>

              <th class="th">Pasien</th>
              <th class="th">Orang Tua</th>
              <th class="th">Layanan</th>
              <th class="th">Total Tagihan</th>
              <th class="th text-center">Status</th>
              <th class="th text-center">Aksi</th>

            </tr>

          </thead>

          <tbody>

            <tr
              v-for="item in filteredRegistrasi"
              :key="item.id"
              class="border-b hover:bg-indigo-50/40 transition"
            >

              <!-- PASIEN -->
              <td class="td">

                <div class="flex items-center gap-3">

                  <div
                    class="w-12 h-12 rounded-2xl
                          bg-gradient-to-r from-indigo-500 to-fuchsia-500
                          flex items-center justify-center
                          text-white font-bold shadow"
                  >
                    {{
                      item.profile_anak?.nama_anak?.charAt(0) || '?'
                    }}
                  </div>

                  <div>
                    <p class="font-semibold text-gray-800">
                      {{ item.profile_anak?.nama_anak || '-' }}
                    </p>

                    <p class="text-xs text-gray-500 mt-1">
                      {{ item.no_regis }}
                    </p>
                  </div>

                </div>

              </td>

              <!-- ORANG TUA -->
              <td class="td">

                <div class="space-y-1">

                  <p class="font-medium text-gray-700">
                    👨 {{ item.nama_ayah || '-' }}
                  </p>

                  <p class="font-medium text-gray-700">
                    👩 {{ item.nama_ibu || '-' }}
                  </p>

                </div>

              </td>

              <!-- LAYANAN -->
              <td class="td">

                <div class="flex flex-wrap gap-2">

                  <span
                    v-for="p in pelayanans(item)"
                    :key="p.id"
                    class="px-3 py-1 rounded-full
                          bg-indigo-100 text-indigo-700
                          text-xs font-semibold"
                  >
                    {{ p.layanan?.layanan }}
                  </span>

                </div>

              </td>

              <!-- TOTAL -->
              <td class="td">

                <div>
                  <p class="font-bold text-indigo-600 text-base">
                    Rp {{ totalTagihan(item).toLocaleString() }}
                  </p>

                  <p class="text-xs text-gray-400 mt-1">
                    {{ pelayanans(item).length }} layanan
                  </p>
                </div>

              </td>

              <!-- STATUS -->
              <td class="td text-center">

                <span
                  class="badge-status"
                  :class="
                    item?.pembayarans?.[0]?.status === 'lunas'
                      ? 'badge-success'
                      : 'badge-danger'
                  "
                >

                  {{
                    item?.pembayarans?.[0]?.status === 'lunas'
                      ? 'LUNAS'
                      : 'BELUM LUNAS'
                  }}

                </span>

              </td>

              <!-- AKSI -->
              <td class="td">

                <div class="flex flex-wrap justify-center gap-2">

                  <!-- DETAIL -->
                  <button
                    @click="bukaDetail(item)"
                    class="btn-outline"
                  >
                    Detail
                  </button>

                  <!-- SEND BILL -->
                  <button
                    v-if="!item?.pembayarans?.length"
                    @click="verifikasiPembayaran(item)"
                    class="btn-warning"
                  >
                    Send Bill
                  </button>

                  <!-- BAYAR -->
                  <button
                    v-if="!item?.pembayarans?.length"
                    @click="bukaBayar(item)"
                    class="btn-primary"
                  >
                    Bayar
                  </button>

                  <!-- CETAK -->
                  <button
                    v-if="item?.pembayarans?.length"
                    @click="cetakInvoice(item)"
                    class="btn-success"
                  >
                    Cetak
                  </button>

                  <!-- EMAIL -->
                  <button
                    @click="kirimEmail(item)"
                    class="btn-info"
                  >
                    Email
                  </button>

                </div>

              </td>

            </tr>

            <!-- EMPTY -->
            <tr v-if="!filteredRegistrasi.length">

              <td colspan="6">

                <div class="flex flex-col items-center py-16">

                  <div class="text-6xl mb-4">
                    📭
                  </div>

                  <p class="text-lg font-semibold text-gray-600">
                    Tidak ada data pembayaran
                  </p>

                  <p class="text-sm text-gray-400 mt-2">
                    Data pasien tidak ditemukan
                  </p>

                </div>

              </td>

            </tr>

          </tbody>

        </table>

      </div>

    </div>

    <!-- ================= DETAIL MODAL ================= -->
    <div
      v-if="showDetail"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50
            flex items-center justify-center overflow-y-auto p-4"
    >

      <div class="bg-white w-full max-w-5xl rounded-3xl shadow-2xl p-6">

        <div class="flex justify-between items-center border-b pb-4 mb-6">

          <div>
            <h2 class="text-2xl font-bold text-gray-800">
              📄 Detail Tagihan Pasien
            </h2>

            <p class="text-sm text-gray-500 mt-1">
              Informasi pembayaran dan rincian layanan
            </p>
          </div>

          <button
            @click="showDetail = false"
            class="btn-outline"
          >
            Tutup
          </button>

        </div>

        <!-- INFO -->
        <div class="grid md:grid-cols-2 gap-5 mb-6">

          <div class="info-card">
            <p class="info-label">Nama Anak</p>
            <p class="info-value">
              {{ selected.profile_anak?.nama_anak }}
            </p>
          </div>

          <div class="info-card">
            <p class="info-label">No Registrasi</p>
            <p class="info-value">
              {{ selected.no_regis }}
            </p>
          </div>

          <div class="info-card">
            <p class="info-label">Tanggal Registrasi</p>
            <p class="info-value">
              {{ selected.tgl_regis }}
            </p>
          </div>

          <div class="info-card">
            <p class="info-label">Status Pembayaran</p>

            <span
              class="badge-status"
              :class="
                pembayaranAktif?.status === 'lunas'
                  ? 'badge-success'
                  : 'badge-danger'
              "
            >
              {{ pembayaranAktif?.status || 'BELUM BAYAR' }}
            </span>
          </div>

        </div>

        <!-- TABLE LAYANAN -->
        <div class="border rounded-2xl overflow-hidden">

          <div
            class="bg-slate-100 px-5 py-4
                  font-semibold text-gray-700"
          >
            Rincian Tagihan
          </div>

          <table class="w-full text-sm">

            <thead class="bg-gray-50">

              <tr>

                <th class="th">Layanan</th>
                <th class="th">Tanggal</th>
                <th class="th">Terapis</th>
                <th class="th text-right">Subtotal</th>

              </tr>

            </thead>

            <tbody>

              <tr
                v-for="p in pelayanans(selected)"
                :key="p.id"
                class="border-t"
              >

                <td class="td">
                  {{ p.layanan?.layanan }}
                </td>

                <td class="td">
                  {{ p.tanggal_penjadwalan }}
                </td>

                <td class="td">
                  {{ p.terapis?.nama }}
                </td>

                <td class="td text-right font-semibold">
                  Rp {{ (p.qty * p.harga).toLocaleString() }}
                </td>

              </tr>

            </tbody>

          </table>

        </div>

        <!-- TOTAL -->
        <div class="mt-6 bg-indigo-50 rounded-2xl p-5">

          <div class="flex justify-between text-lg font-bold">

            <span>Total Tagihan</span>

            <span class="text-indigo-700">
              Rp {{ totalTagihan(selected).toLocaleString() }}
            </span>

          </div>

          <div
            v-if="diskonDetail > 0"
            class="flex justify-between mt-2 text-green-600 font-semibold"
          >

            <span>Diskon</span>

            <span>
              - Rp {{ diskonDetail.toLocaleString() }}
            </span>

          </div>

          <div
            v-if="pembayaranAktif"
            class="flex justify-between mt-2 text-xl font-bold text-fuchsia-700"
          >

            <span>Total Bayar</span>

            <span>
              Rp {{ Number(pembayaranAktif.jumlah_bayar).toLocaleString() }}
            </span>

          </div>

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

const pelayanans = item =>
  Array.isArray(item?.pelayanans)
    ? item.pelayanans
    : []

const totalTagihan = item =>
  pelayanans(item).reduce(
    (s, p) => s + Number(p.qty || 0) * Number(p.harga || 0),
    0
  )

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

  const promo = promos.value.find(
    p => p.id === selectedPromo.value
  )

  if (!promo) {

    formBayar.value.jumlah_bayar =
      formBayar.value.total_tagihan

    return
  }

  if (promo.tipe_diskon === 'persen') {

    diskon.value =
      (promo.nilai_diskon / 100) *
      formBayar.value.total_tagihan

  } else {

    diskon.value = promo.nilai_diskon
  }

  formBayar.value.jumlah_bayar = Math.max(
    0,
    formBayar.value.total_tagihan - diskon.value
  )
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
    window.open(
      `${baseUrl}/invoice/${item.id}/print`,
      '_blank'
    )
  }
}

const kirimEmail = async item => {

  await axios.post(
    `${baseUrl}/invoice/${item.id}/send-email`
  )

  toast.success('Email terkirim')
}

const filteredRegistrasi = computed(() => {

  if (!search.value) return registrasiList.value

  const key = search.value.toLowerCase()

  return registrasiList.value.filter(r => {

    const namaAnak =
      r.profile_anak?.nama_anak?.toLowerCase() || ''

    const namaAyah =
      r.nama_ayah?.toLowerCase() || ''

    const namaIbu =
      r.nama_ibu?.toLowerCase() || ''

    const noRegis =
      r.no_regis?.toLowerCase() || ''

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

    await api.post(
      '/pembayaran-registrasi/request-verifikasi',
      {
        registrasi_anak_id: item.id,
        tanggal_bayar: new Date().toISOString().slice(0, 10),
        total_tagihan: totalTagihan(item)
      }
    )

    toast.success(
      'Tagihan dibuat, menunggu upload bukti pembayaran'
    )

    const res = await api.get('/registrasi-anak')

    registrasiList.value = res.data

  } catch {

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
        status_pembayaran:
          status === 'diterima'
            ? 'lunas'
            : 'pending'
      }
    )

    toast.success(
      status === 'diterima'
        ? 'Pembayaran diterima & dilunasi'
        : 'Pembayaran ditolak'
    )

    const res = await api.get('/registrasi-anak')

    registrasiList.value = res.data

    const updated = res.data.find(
      r => r.id === selected.value.id
    )

    if (updated) selected.value = updated

  } catch {

    toast.error('Gagal mengubah status verifikasi')
  }
}
</script>

<style scoped>
.th {
  @apply px-5 py-4 text-left
  font-semibold text-gray-600 whitespace-nowrap;
}

.td {
  @apply px-5 py-4 whitespace-nowrap;
}

.summary-card {
  @apply bg-white rounded-3xl
  shadow-lg border border-gray-100 p-5;
}

.summary-label {
  @apply text-sm text-gray-500;
}

.summary-value {
  @apply text-3xl font-bold mt-2;
}

.badge-status {
  @apply px-4 py-1 rounded-full
  text-xs font-bold;
}

.badge-success {
  @apply bg-green-100 text-green-700;
}

.badge-danger {
  @apply bg-red-100 text-red-700;
}

.btn-outline {
  @apply px-3 py-2 rounded-xl border
  border-gray-200 text-gray-700
  hover:bg-gray-100 transition
  text-xs font-semibold;
}

.btn-primary {
  @apply px-3 py-2 rounded-xl
  bg-indigo-600 text-white
  hover:bg-indigo-700 transition
  text-xs font-semibold shadow-sm;
}

.btn-success {
  @apply px-3 py-2 rounded-xl
  bg-green-600 text-white
  hover:bg-green-700 transition
  text-xs font-semibold shadow-sm;
}

.btn-warning {
  @apply px-3 py-2 rounded-xl
  bg-orange-500 text-white
  hover:bg-orange-600 transition
  text-xs font-semibold shadow-sm;
}

.btn-info {
  @apply px-3 py-2 rounded-xl
  bg-sky-600 text-white
  hover:bg-sky-700 transition
  text-xs font-semibold shadow-sm;
}

.info-card {
  @apply bg-gray-50 rounded-2xl
  p-4 border border-gray-100;
}

.info-label {
  @apply text-sm text-gray-500;
}

.info-value {
  @apply font-bold text-gray-800 mt-1;
}

.input {
  @apply w-full border rounded-lg px-3 py-2
  text-sm focus:ring-2 focus:ring-indigo-500;
}

.label {
  @apply text-sm font-medium text-gray-600;
}
</style>