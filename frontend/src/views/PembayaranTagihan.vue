<template>
  <div class="page">

    <!-- ================= HEADER ================= -->
    <div class="page-header">
      <div class="header-left">
        <div class="header-icon">
          <i class="pi pi-credit-card"></i>
        </div>

        <div>
          <h1 class="page-title">Tagihan & Pembayaran</h1>
          <p class="page-sub">Monitoring pembayaran pasien, invoice, dan verifikasi pembayaran</p>
        </div>
      </div>

      <div class="input-wrap header-search">
        <i class="pi pi-search input-icon"></i>
        <input
          v-model="search"
          type="text"
          placeholder="Cari nama anak / no registrasi..."
          class="input"
        />
      </div>
    </div>

    <!-- ================= SUMMARY ================= -->
    <div class="summary-grid">

      <div class="stat-card">
        <p class="stat-label">Total Pasien</p>
        <h2 class="stat-value">{{ registrasiList.length }}</h2>
      </div>

      <div class="stat-card">
        <p class="stat-label">Lunas</p>
        <h2 class="stat-value stat-value--green">
          {{ registrasiList.filter(r => r?.pembayarans?.[0]?.status === 'lunas').length }}
        </h2>
      </div>

      <div class="stat-card">
        <p class="stat-label">Belum Lunas</p>
        <h2 class="stat-value stat-value--red">
          {{ registrasiList.filter(r => r?.pembayarans?.[0]?.status !== 'lunas').length }}
        </h2>
      </div>

      <div class="stat-card">
        <p class="stat-label">Total Transaksi</p>
        <h2 class="stat-value">
          Rp {{ registrasiList.reduce((s, r) => s + totalTagihan(r), 0).toLocaleString() }}
        </h2>
      </div>

    </div>

    <!-- ================= TABLE ================= -->
    <div class="panel table-panel">

      <div class="table-scroll">
        <table class="data-table">

          <thead>
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

            <tr v-for="item in filteredRegistrasi" :key="item.id" class="row">

              <!-- PASIEN -->
              <td class="td">
                <div class="patient-cell">
                  <div class="avatar">{{ item.profile_anak?.nama_anak?.charAt(0) || '?' }}</div>

                  <div>
                    <p class="cell-strong">{{ item.profile_anak?.nama_anak || '-' }}</p>
                    <p class="cell-faint">{{ item.no_regis }}</p>
                  </div>
                </div>
              </td>

              <!-- ORANG TUA -->
              <td class="td">
                <div class="parent-stack">
                  <p class="cell-strong"><i class="pi pi-user parent-icon"></i>{{ item.nama_ayah || '-' }}</p>
                  <p class="cell-strong"><i class="pi pi-user parent-icon"></i>{{ item.nama_ibu || '-' }}</p>
                </div>
              </td>

              <!-- LAYANAN -->
              <td class="td">
                <div class="flex flex-wrap gap-1.5">
                  <span v-for="p in pelayanans(item)" :key="p.id" class="tag">
                    {{ p.layanan?.layanan }}
                  </span>
                </div>
              </td>

              <!-- TOTAL -->
              <td class="td">
                <p class="cell-strong cell-strong--accent">Rp {{ totalTagihan(item).toLocaleString() }}</p>
                <p class="cell-faint">{{ pelayanans(item).length }} layanan</p>
              </td>

              <!-- STATUS -->
              <td class="td text-center">
                <span
                  class="badge"
                  :class="item?.pembayarans?.[0]?.status === 'lunas' ? 'badge--green' : 'badge--red'"
                >
                  {{ item?.pembayarans?.[0]?.status === 'lunas' ? 'Lunas' : 'Belum lunas' }}
                </span>
              </td>

              <!-- AKSI -->
              <td class="td">
                <div class="flex flex-wrap justify-center gap-1.5">

                  <button @click="bukaDetail(item)" class="btn-secondary btn-secondary--sm">Detail</button>

                  <button
                    v-if="!item?.pembayarans?.length"
                    @click="verifikasiPembayaran(item)"
                    class="btn-amber"
                  >
                    Send bill
                  </button>

                  <button
                    v-if="!item?.pembayarans?.length"
                    @click="bukaBayar(item)"
                    class="btn-primary"
                  >
                    Bayar
                  </button>

                  <button
                    v-if="item?.pembayarans?.length"
                    @click="cetakInvoice(item)"
                    class="btn-accent-alt"
                  >
                    Cetak
                  </button>

                  <button @click="kirimEmail(item)" class="btn-blue">Email</button>

                </div>
              </td>

            </tr>

            <!-- EMPTY -->
            <tr v-if="!filteredRegistrasi.length">
              <td colspan="6">
                <div class="empty-state">
                  <div class="empty-icon"><i class="pi pi-inbox"></i></div>
                  <p class="empty-title">Tidak ada data pembayaran</p>
                  <p class="empty-sub">Data pasien tidak ditemukan</p>
                </div>
              </td>
            </tr>

          </tbody>
        </table>
      </div>

    </div>

    <!-- ================= DETAIL MODAL ================= -->
    <div v-if="showDetail" class="modal-overlay">
      <div class="modal-backdrop" @click="showDetail = false"></div>

      <div class="modal-wrapper">
        <div class="modal-card modal-card--wide">

          <div class="modal-header">
            <div class="flex-1">
              <h2 class="modal-title">Detail tagihan pasien</h2>
              <p class="modal-sub">Informasi pembayaran dan rincian layanan</p>
            </div>

            <button @click="showDetail = false" class="modal-close"><i class="pi pi-times"></i></button>
          </div>

          <div class="modal-content">

            <!-- INFO -->
            <div class="info-grid">

              <div class="info-card">
                <p class="info-label">Nama Anak</p>
                <p class="info-value">{{ selected.profile_anak?.nama_anak }}</p>
              </div>

              <div class="info-card">
                <p class="info-label">No Registrasi</p>
                <p class="info-value">{{ selected.no_regis }}</p>
              </div>

              <div class="info-card">
                <p class="info-label">Tanggal Registrasi</p>
                <p class="info-value">{{ selected.tgl_regis }}</p>
              </div>

              <div class="info-card">
                <p class="info-label">Status Pembayaran</p>
                <span class="badge" :class="pembayaranAktif?.status === 'lunas' ? 'badge--green' : 'badge--red'">
                  {{ pembayaranAktif?.status || 'Belum bayar' }}
                </span>
              </div>

            </div>

            <!-- TABLE LAYANAN -->
            <div class="panel mt-4">
              <div class="panel-label">Rincian Tagihan</div>

              <div class="table-scroll">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th class="th">Layanan</th>
                      <th class="th">Tanggal</th>
                      <th class="th">Terapis</th>
                      <th class="th text-right">Subtotal</th>
                    </tr>
                  </thead>

                  <tbody>
                    <tr v-for="p in pelayanans(selected)" :key="p.id" class="row">
                      <td class="td">{{ p.layanan?.layanan }}</td>
                      <td class="td">{{ p.tanggal_penjadwalan }}</td>
                      <td class="td">{{ p.terapis?.nama }}</td>
                      <td class="td text-right cell-strong">Rp {{ (p.qty * p.harga).toLocaleString() }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TOTAL -->
            <div class="info-card mt-4">

              <div class="info-row info-row--lg">
                <span>Total Tagihan</span>
                <span class="cell-strong cell-strong--accent">Rp {{ totalTagihan(selected).toLocaleString() }}</span>
              </div>

              <div v-if="diskonDetail > 0" class="info-row info-row--green">
                <span>Diskon</span>
                <span>- Rp {{ diskonDetail.toLocaleString() }}</span>
              </div>

              <div v-if="pembayaranAktif" class="info-row info-row--lg">
                <span>Total Bayar</span>
                <span class="cell-strong cell-strong--accent">Rp {{ Number(pembayaranAktif.jumlah_bayar).toLocaleString() }}</span>
              </div>

            </div>

            <!-- ============= BUKTI & VERIFIKASI PEMBAYARAN ============= -->
            <div v-if="pembayaranAktif" class="panel mt-4">

              <div class="panel-label panel-label--row">
                <span>Bukti & Verifikasi Pembayaran</span>

                <span
                  class="badge"
                  :class="
                    pembayaranAktif?.status_verifikasi === 'diterima'
                      ? 'badge--green'
                      : pembayaranAktif?.status_verifikasi === 'ditolak'
                        ? 'badge--red'
                        : 'badge--amber'
                  "
                >
                  {{ pembayaranAktif?.status_verifikasi ? pembayaranAktif.status_verifikasi : 'Menunggu' }}
                </span>
              </div>

              <div class="panel-body">

                <div v-if="pembayaranAktif?.bukti_pembayaran" class="proof-layout">

                  <div class="flex-shrink-0">
                    <img
                      v-if="isImage(pembayaranAktif.bukti_pembayaran)"
                      :src="fileUrl(pembayaranAktif.bukti_pembayaran)"
                      @click="openImage(pembayaranAktif.bukti_pembayaran)"
                      class="proof-image"
                      alt="Bukti pembayaran"
                    />

                    <a v-else :href="fileUrl(pembayaranAktif.bukti_pembayaran)" target="_blank" class="btn-secondary">
                      <i class="pi pi-paperclip"></i>
                      Lihat file bukti
                    </a>
                  </div>

                  <div class="flex-1 space-y-2">

                    <div class="info-card">
                      <p class="info-label">Tanggal Bayar</p>
                      <p class="info-value">{{ pembayaranAktif.tanggal_bayar || '-' }}</p>
                    </div>

                    <div class="info-card">
                      <p class="info-label">Metode Pembayaran</p>
                      <p class="info-value">{{ pembayaranAktif.metode_pembayaran || '-' }}</p>
                    </div>

                    <div
                      v-if="!pembayaranAktif.status_verifikasi || pembayaranAktif.status_verifikasi === 'menunggu'"
                      class="flex gap-2 pt-1"
                    >
                      <button @click="ubahStatusVerifikasi('diterima')" class="btn-accent-alt">
                        <i class="pi pi-check"></i>
                        Terima pembayaran
                      </button>

                      <button @click="ubahStatusVerifikasi('ditolak')" class="btn-danger">
                        <i class="pi pi-times"></i>
                        Tolak pembayaran
                      </button>
                    </div>

                    <p v-else class="cell-faint">
                      Pembayaran sudah {{ pembayaranAktif.status_verifikasi === 'diterima' ? 'diverifikasi & diterima' : 'ditolak' }}.
                    </p>

                  </div>

                </div>

                <div v-else class="empty-state empty-state--sm">
                  Pasien belum mengupload bukti pembayaran.
                </div>

              </div>

            </div>

          </div>

        </div>
      </div>
    </div>

    <!-- ================= MODAL BAYAR ================= -->
    <div v-if="showBayar" class="modal-overlay">
      <div class="modal-backdrop" @click="showBayar = false"></div>

      <div class="modal-wrapper">
        <div class="modal-card">

          <div class="modal-header">
            <div class="flex-1">
              <h2 class="modal-title">Input pembayaran</h2>
              <p class="modal-sub">{{ selected?.profile_anak?.nama_anak }} &middot; {{ selected?.no_regis }}</p>
            </div>

            <button @click="showBayar = false" class="modal-close"><i class="pi pi-times"></i></button>
          </div>

          <div class="modal-content">

            <div class="field">
              <label class="field-label">Tanggal Bayar</label>
              <input v-model="formBayar.tanggal_bayar" type="date" class="input" />
            </div>

            <div class="field">
              <label class="field-label">Metode Pembayaran</label>
              <select v-model="formBayar.metode_pembayaran" class="input">
                <option value="cash">Cash</option>
                <option value="transfer">Transfer</option>
                <option value="debit">Debit</option>
                <option value="qris">QRIS</option>
              </select>
            </div>

            <div class="field">
              <label class="field-label">Promo / Diskon</label>
              <select v-model="selectedPromo" @change="hitungDiskon" class="input">
                <option :value="null">Tanpa promo</option>
                <option v-for="promo in promos" :key="promo.id" :value="promo.id">
                  {{ promo.nama_promo || promo.nama }}
                  ({{ promo.tipe_diskon === 'persen' ? `${promo.nilai_diskon}%` : `Rp ${Number(promo.nilai_diskon).toLocaleString()}` }})
                </option>
              </select>
            </div>

            <div class="info-card">
              <div class="info-row">
                <span class="cell-faint">Total Tagihan</span>
                <span class="cell-strong">Rp {{ Number(formBayar.total_tagihan).toLocaleString() }}</span>
              </div>

              <div v-if="diskon > 0" class="info-row info-row--green">
                <span>Diskon</span>
                <span>- Rp {{ Number(diskon).toLocaleString() }}</span>
              </div>

              <div class="info-row info-row--lg">
                <span>Jumlah Bayar</span>
                <span class="cell-strong cell-strong--accent">Rp {{ Number(formBayar.jumlah_bayar).toLocaleString() }}</span>
              </div>
            </div>

            <div class="field">
              <label class="field-label">Status</label>
              <select v-model="formBayar.status" class="input">
                <option value="lunas">Lunas</option>
                <option value="pending">Pending / DP</option>
              </select>
            </div>

          </div>

          <div class="modal-footer">
            <button @click="showBayar = false" class="btn-secondary">Batal</button>
            <button @click="simpanBayar" class="btn-primary btn-primary--lg">Simpan pembayaran</button>
          </div>

        </div>
      </div>
    </div>

    <!-- ================= MODAL PREVIEW GAMBAR BUKTI ================= -->
    <div v-if="showImage" class="image-overlay" @click="closeImage">
      <img :src="imageUrl" class="image-preview" @click.stop />

      <button @click="closeImage" class="image-close"><i class="pi pi-times"></i></button>
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
/* ===============================
   TOKENS (senada dengan Sidebar.vue & halaman lain)
================================ */
.page {
  --bg: #FAFAFA;
  --surface: #FFFFFF;
  --border: #ECEDF1;
  --ink: #1F2128;
  --muted: #98A0AE;
  --accent: #6D5CE0;
  --accent-soft: #F1EEFC;

  --amber: #B98900;
  --amber-soft: #FBF3DB;
  --blue: #2563EB;
  --blue-soft: #E8EFFD;
  --green: #1A9469;
  --green-soft: #E5F6EE;
  --red: #DC4747;
  --red-soft: #FBEAEA;

  min-height: 100vh;
  background: var(--bg);
  color: var(--ink);
  padding: 1.5rem;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.page > * + * {
  margin-top: 1.5rem;
}

/* ===============================
   HEADER
================================ */
.page-header {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1.25rem;

  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 1rem;
  padding: 1.75rem;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.header-icon {
  width: 52px;
  height: 52px;
  border-radius: 0.85rem;
  background: var(--accent-soft);
  color: var(--accent);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  flex-shrink: 0;
}

.page-title {
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: 0.01em;
  color: var(--ink);
}

.page-sub {
  font-size: 0.85rem;
  color: var(--muted);
  margin-top: 0.2rem;
}

.header-search {
  width: 100%;
  max-width: 320px;
}

/* ===============================
   SUMMARY
================================ */
.summary-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

@media (min-width: 768px) {
  .summary-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.stat-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 1rem;
  padding: 1.25rem;
}

.stat-label {
  font-size: 0.8rem;
  color: var(--muted);
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 700;
  margin-top: 0.4rem;
  color: var(--accent);
}

.stat-value--green { color: var(--green); }
.stat-value--red { color: var(--red); }

/* ===============================
   PANEL / TABLE
================================ */
.panel {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 1rem;
}

.panel-label {
  padding: 0.9rem 1.25rem;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--ink);
  border-bottom: 1px solid var(--border);
}

.panel-label--row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.panel-body {
  padding: 1.25rem;
}

.table-panel {
  overflow: hidden;
}

.table-scroll {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  font-size: 0.85rem;
  border-collapse: collapse;
}

.data-table thead {
  background: var(--bg);
  border-bottom: 1px solid var(--border);
}

.th {
  padding: 0.9rem 1.25rem;
  text-align: left;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--muted);
  white-space: nowrap;
}

.td {
  padding: 0.9rem 1.25rem;
  color: var(--ink);
  vertical-align: middle;
  border-bottom: 1px solid var(--border);
}

.row {
  transition: background 0.15s ease;
}

.row:hover {
  background: var(--bg);
}

.cell-strong {
  font-weight: 600;
  color: var(--ink);
  font-size: 0.85rem;
}

.cell-strong--accent {
  color: var(--accent);
}

.cell-faint {
  font-size: 0.78rem;
  color: var(--muted);
}

.tag {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.65rem;
  border-radius: 999px;
  font-size: 0.72rem;
  font-weight: 500;
  background: var(--accent-soft);
  color: var(--accent);
}

.patient-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.avatar {
  width: 2.6rem;
  height: 2.6rem;
  border-radius: 0.7rem;
  background: var(--accent);
  color: #fff;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.parent-stack {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.parent-icon {
  font-size: 0.75rem;
  color: var(--muted);
  margin-right: 0.4rem;
}

/* ===============================
   BADGES
================================ */
.badge {
  display: inline-flex;
  align-items: center;
  text-transform: capitalize;
  padding: 0.3rem 0.75rem;
  border-radius: 999px;
  font-size: 0.74rem;
  font-weight: 600;
}

.badge--amber { background: var(--amber-soft); color: var(--amber); }
.badge--blue { background: var(--blue-soft); color: var(--blue); }
.badge--green { background: var(--green-soft); color: var(--green); }
.badge--red { background: var(--red-soft); color: var(--red); }

/* ===============================
   BUTTONS
================================ */
.btn-primary,
.btn-secondary,
.btn-accent-alt,
.btn-amber,
.btn-blue,
.btn-danger {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  height: 2.1rem;
  padding: 0 0.8rem;
  border-radius: 0.55rem;
  font-size: 0.74rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: background 0.15s ease;
  white-space: nowrap;
}

.btn-primary {
  background: var(--accent);
  color: #fff;
}

.btn-primary:hover { background: #5d4dd1; }

.btn-primary--lg {
  height: 2.6rem;
  padding: 0 1.25rem;
  font-size: 0.85rem;
}

.btn-secondary {
  background: var(--bg);
  color: var(--ink);
  border: 1px solid var(--border);
}

.btn-secondary:hover { background: var(--accent-soft); }

.btn-secondary--sm {
  height: 2.1rem;
}

.btn-accent-alt {
  background: var(--green);
  color: #fff;
}

.btn-accent-alt:hover { background: #157d59; }

.btn-amber {
  background: var(--amber);
  color: #fff;
}

.btn-amber:hover { background: #9c7300; }

.btn-blue {
  background: var(--blue);
  color: #fff;
}

.btn-blue:hover { background: #1d4fc4; }

.btn-danger {
  background: var(--red);
  color: #fff;
}

.btn-danger:hover { background: #c23a3a; }

/* ===============================
   EMPTY STATE
================================ */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 3rem 0;
  color: var(--muted);
  font-size: 0.85rem;
}

.empty-state--sm {
  padding: 1.5rem 0;
}

.empty-icon {
  width: 4rem;
  height: 4rem;
  border-radius: 999px;
  background: var(--bg);
  border: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: var(--muted);
  margin-bottom: 1rem;
}

.empty-title {
  font-size: 1rem;
  font-weight: 700;
  color: var(--ink);
}

.empty-sub {
  font-size: 0.82rem;
  color: var(--muted);
  margin-top: 0.2rem;
}

/* ===============================
   INPUTS / FIELDS
================================ */
.field {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  margin-bottom: 1rem;
}

.field-label {
  font-size: 0.78rem;
  font-weight: 500;
  color: var(--muted);
}

.input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 1rem;
  color: var(--muted);
  font-size: 0.85rem;
  pointer-events: none;
}

.input {
  width: 100%;
  height: 2.75rem;
  border-radius: 0.65rem;
  border: 1px solid var(--border);
  background: var(--bg);
  padding: 0 1rem;
  font-size: 0.85rem;
  color: var(--ink);
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.header-search .input {
  padding-left: 2.5rem;
}

.input:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-soft);
}

/* ===============================
   INFO CARD
================================ */
.info-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.85rem;
}

@media (min-width: 768px) {
  .info-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

.info-card {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 0.85rem;
  padding: 1rem 1.1rem;
}

.info-label {
  font-size: 0.78rem;
  color: var(--muted);
}

.info-value {
  font-weight: 700;
  color: var(--ink);
  margin-top: 0.2rem;
}

.info-row {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.4rem 0;
  font-size: 0.85rem;
}

.info-row--lg {
  font-size: 1rem;
  font-weight: 700;
}

.info-row--green {
  color: var(--green);
  font-weight: 600;
}

/* ===============================
   PROOF OF PAYMENT
================================ */
.proof-layout {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

@media (min-width: 768px) {
  .proof-layout {
    flex-direction: row;
  }
}

.proof-image {
  width: 10rem;
  height: 10rem;
  object-fit: cover;
  border-radius: 0.85rem;
  border: 1px solid var(--border);
  cursor: pointer;
  transition: opacity 0.15s ease;
}

.proof-image:hover {
  opacity: 0.85;
}

/* ===============================
   MODAL
================================ */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 50;
  overflow-y: auto;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
}

.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(31, 33, 40, 0.45);
}

.modal-wrapper {
  position: relative;
}

.modal-card {
  position: relative;
  width: 100%;
  max-width: 32rem;
  max-height: 85vh;
  display: flex;
  flex-direction: column;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 1rem;
  overflow: hidden;
}

.modal-card--wide {
  max-width: 56rem;
}

.modal-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}

.modal-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: var(--ink);
}

.modal-sub {
  font-size: 0.82rem;
  color: var(--muted);
  margin-top: 0.15rem;
}

.modal-close {
  width: 2.1rem;
  height: 2.1rem;
  border-radius: 0.55rem;
  background: var(--bg);
  border: 1px solid var(--border);
  color: var(--muted);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  flex-shrink: 0;
  transition: background 0.15s ease, color 0.15s ease;
}

.modal-close:hover {
  background: var(--accent-soft);
  color: var(--accent);
}

.modal-content {
  overflow-y: auto;
  padding: 1.5rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1.1rem 1.5rem;
  border-top: 1px solid var(--border);
  flex-shrink: 0;
}

/* ===============================
   IMAGE PREVIEW
================================ */
.image-overlay {
  position: fixed;
  inset: 0;
  z-index: 60;
  background: rgba(15, 16, 20, 0.85);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
}

.image-preview {
  max-width: 100%;
  max-height: 100%;
  border-radius: 0.85rem;
}

.image-close {
  position: absolute;
  top: 1.5rem;
  right: 1.5rem;
  width: 2.4rem;
  height: 2.4rem;
  border-radius: 0.6rem;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

/* ===============================
   SCROLLBAR
================================ */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-thumb {
  background: var(--border);
  border-radius: 999px;
}

::-webkit-scrollbar-thumb:hover {
  background: var(--muted);
}
</style>