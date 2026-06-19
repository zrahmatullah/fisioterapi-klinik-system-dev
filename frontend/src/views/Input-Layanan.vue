<template>
  <div class="page">

    <!-- HEADER -->
    <div class="page-header">
      <div class="header-left">
        <div class="header-icon">
          <i class="pi pi-users"></i>
        </div>

        <div>
          <h1 class="page-title">Daftar Pasien</h1>
          <p class="page-sub">Kelola registrasi dan penjadwalan terapi pasien</p>
        </div>
      </div>

      <!-- STAT -->
      <div class="stat-group">
        <div class="stat-card">
          <p class="stat-label">Total Data</p>
          <h3 class="stat-value">{{ filteredSortedData.length }}</h3>
        </div>

        <div class="stat-card">
          <p class="stat-label">Halaman</p>
          <h3 class="stat-value">{{ currentPage }}</h3>
        </div>
      </div>
    </div>

    <!-- FILTER -->
    <div class="panel filter-panel">
      <div class="filter-grid">

        <div class="field">
          <label class="filter-label">Cari Pasien</label>

          <div class="input-wrap">
            <i class="pi pi-search input-icon"></i>
            <input
              v-model="search"
              placeholder="Cari nama anak / no registrasi..."
              class="filter-input"
            />
          </div>
        </div>

        <div class="field">
          <label class="filter-label">Filter Tanggal</label>

          <div class="input-wrap">
            <input
              type="date"
              v-model="filterTanggal"
              class="filter-input"
            />
          </div>
        </div>

        <div class="field">
          <label class="filter-label">Data Per Halaman</label>

          <div class="input-wrap">
            <i class="pi pi-list input-icon"></i>
            <input
              type="number"
              min="1"
              v-model.number="perPage"
              class="filter-input"
            />
          </div>
        </div>

      </div>
    </div>

    <!-- TABLE -->
    <div class="panel table-panel">

      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th class="th w-16">No</th>

              <th class="th th--sortable" @click="setSort('no_regis')">
                No Registrasi {{ sortIcon('no_regis') }}
              </th>

              <th class="th th--sortable" @click="setSort('tgl_regis')">
                Tanggal {{ sortIcon('tgl_regis') }}
              </th>

              <th class="th th--sortable" @click="setSort('nama_anak')">
                Nama Anak {{ sortIcon('nama_anak') }}
              </th>

              <th class="th th--sortable" @click="setSort('ruangan')">
                Ruangan {{ sortIcon('ruangan') }}
              </th>

              <th class="th th--sortable" @click="setSort('terapis')">
                Terapis {{ sortIcon('terapis') }}
              </th>

              <th class="th text-center">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(r, i) in paginatedData" :key="r.id" class="row">

              <!-- NO -->
              <td class="td">
                <div class="index-chip">
                  {{ (currentPage - 1) * perPage + i + 1 }}
                </div>
              </td>

              <!-- NO REG -->
              <td class="td">
                <p class="cell-strong cell-strong--accent">{{ r.no_regis }}</p>
                <p class="cell-faint">ID #{{ r.id }}</p>
              </td>

              <!-- TGL -->
              <td class="td">
                <span class="tag">{{ formatDate(r.tgl_regis) }}</span>
              </td>

              <!-- PASIEN -->
              <td class="td">
                <div class="patient-cell">
                  <div class="avatar">
                    {{ r.profile_anak?.nama_anak?.charAt(0)?.toUpperCase() }}
                  </div>

                  <div>
                    <p class="cell-strong">{{ r.profile_anak?.nama_anak || '-' }}</p>
                    <p class="cell-faint">Pasien terapi</p>
                  </div>
                </div>
              </td>

              <!-- RUANGAN -->
              <td class="td">
                <span class="tag tag--muted">
                  <span class="dot"></span>
                  {{ r.ruangan?.ruangan || '-' }}
                </span>
              </td>

              <!-- TERAPIS -->
              <td class="td">
                <div class="therapist-cell">
                  <div class="therapist-icon"><i class="pi pi-user"></i></div>
                  <span class="cell-strong">{{ r.terapis?.nama || '-' }}</span>
                </div>
              </td>

              <!-- ACTION -->
              <td class="td">
                <div class="flex justify-center">
                  <button @click="openModal(r)" class="btn-primary">
                    <i class="pi pi-calendar-plus"></i>
                    Input Layanan
                  </button>
                </div>
              </td>
            </tr>

            <!-- EMPTY -->
            <tr v-if="paginatedData.length === 0">
              <td colspan="7" class="py-16">
                <div class="empty-state">
                  <div class="empty-icon"><i class="pi pi-inbox"></i></div>
                  <h3 class="empty-title">Data tidak ditemukan</h3>
                  <p class="empty-sub">Tidak ada pasien sesuai filter pencarian</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div class="pagination-bar">
        <div class="pagination-info">
          Menampilkan
          <span class="cell-strong">{{ paginatedData.length }}</span>
          data dari
          <span class="cell-strong">{{ filteredSortedData.length }}</span>
        </div>

        <div class="pagination-controls">
          <button @click="prevPage" :disabled="currentPage === 1" class="pagination-btn">
            Prev
          </button>

          <button
            v-for="page in totalPages"
            :key="page"
            @click="goToPage(page)"
            class="pagination-btn"
            :class="page === currentPage ? 'active-page' : ''"
          >
            {{ page }}
          </button>

          <button @click="nextPage" :disabled="currentPage === totalPages" class="pagination-btn">
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL -->
    <Transition name="fade">
      <div v-if="showModal" class="modal-overlay">
        <div class="modal-backdrop" @click="closeModal"></div>

        <div class="modal-wrapper">
          <div class="modal-card animate-modal">

            <!-- HEADER -->
            <div class="modal-header">
              <div class="modal-header-left">
                <div class="modal-icon"><i class="pi pi-calendar-plus"></i></div>

                <div>
                  <h2 class="modal-title">Input Jadwal Layanan</h2>
                  <p class="modal-sub">Atur sesi terapi dan jadwal pasien</p>
                </div>
              </div>

              <button @click="closeModal" class="modal-close">
                <i class="pi pi-times"></i>
              </button>
            </div>

            <!-- CONTENT -->
            <div class="modal-content">

              <!-- PASIEN INFO -->
              <div class="info-card">
                <div class="patient-cell">
                  <div class="avatar avatar--lg">
                    {{ selectedRegistrasi?.profile_anak?.nama_anak?.charAt(0)?.toUpperCase() }}
                  </div>

                  <div>
                    <h3 class="info-name">{{ selectedRegistrasi?.profile_anak?.nama_anak || '-' }}</h3>
                    <p class="cell-faint">{{ selectedRegistrasi?.no_regis || '-' }}</p>
                  </div>
                </div>
              </div>

              <!-- LAYANAN -->
              <div class="info-card">
                <label class="section-label">Pilih Layanan</label>

                <div class="input-wrap mt-3">
                  <i class="pi pi-briefcase input-icon input-icon--left"></i>

                  <select v-model="selectedLayananId" class="input pl-11">
                    <option value="">-- Pilih Layanan --</option>
                    <option v-for="l in layananList" :key="l.id" :value="l.id">
                      {{ l.layanan }} ({{ l.qty }} sesi)
                    </option>
                  </select>
                </div>
              </div>

              <!-- JADWAL -->
              <div v-if="jadwal.length > 0" class="info-card">
                <div class="jadwal-header">
                  <div>
                    <h3 class="jadwal-title">Jadwal Sesi Terapi</h3>
                    <p class="cell-faint">Lengkapi tanggal dan jam terapi</p>
                  </div>

                  <div class="session-badge">{{ jadwal.length }} sesi</div>
                </div>

                <div class="jadwal-grid">
                  <div v-for="(s, i) in jadwal" :key="i" class="session-card">

                    <div class="session-card-head">
                      <div class="session-index">{{ i + 1 }}</div>

                      <div>
                        <h4 class="cell-strong">Sesi {{ i + 1 }}</h4>
                        <p class="cell-faint">Jadwal terapi pasien</p>
                      </div>
                    </div>

                    <div class="space-y-3">

                      <div class="field">
                        <label class="field-label">Tanggal</label>
                        <input type="date" v-model="s.tanggal" class="input" />
                      </div>

                      <div class="grid grid-cols-2 gap-3">
                        <div class="field">
                          <label class="field-label">Jam Mulai</label>
                          <input type="time" v-model="s.jam_mulai" class="input" />
                        </div>

                        <div class="field">
                          <label class="field-label">Jam Selesai</label>
                          <input type="time" v-model="s.jam_selesai" class="input" />
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
              <button @click="closeModal" class="btn-secondary">Batal</button>

              <button @click="submit" :disabled="!canSubmit" class="btn-primary btn-primary--lg">
                <i class="pi pi-check"></i>
                Simpan Jadwal
              </button>
            </div>

          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import api from '@/axios'

const registrasiList = ref([])
const layananList = ref([])

const showModal = ref(false)

const selectedRegistrasi = ref(null)
const selectedLayananId = ref('')
const selectedLayanan = ref(null)

const jadwal = ref([])

const search = ref('')

const sortBy = ref('tgl_regis')
const sortOrder = ref('desc')

const currentPage = ref(1)
const perPage = ref(10)

const filterTanggal = ref(
  new Date().toISOString().slice(0, 10)
)

onMounted(async () => {
  try {
    const registrasiRes = await api.get('/registrasi-anak')
    const layananRes = await api.get('/layanan')

    registrasiList.value = registrasiRes.data || []
    layananList.value = layananRes.data || []
  } catch (error) {
    console.error(error)
  }
})

watch(perPage, (val) => {
  if (!val || val < 1) {
    perPage.value = 1
  }

  currentPage.value = 1
})

watch(selectedLayananId, (id) => {
  selectedLayanan.value =
    layananList.value.find(l => l.id == id) || null

  jadwal.value = Array.from(
    {
      length: selectedLayanan.value?.qty || 0
    },
    () => ({
      tanggal: '',
      jam_mulai: '',
      jam_selesai: ''
    })
  )
})

const setSort = (field) => {
  if (sortBy.value === field) {
    sortOrder.value =
      sortOrder.value === 'asc'
        ? 'desc'
        : 'asc'
  } else {
    sortBy.value = field
    sortOrder.value = 'asc'
  }
}

const sortIcon = (field) => {
  if (sortBy.value !== field) {
    return '⇅'
  }

  return sortOrder.value === 'asc'
    ? '↑'
    : '↓'
}

const filteredSortedData = computed(() => {
  const keyword = search.value.toLowerCase()

  const data = registrasiList.value.filter((r) => {
    const cocokTanggal =
      r?.tgl_regis?.slice(0, 10) === filterTanggal.value

    const cocokSearch =
      r?.no_regis
        ?.toLowerCase()
        ?.includes(keyword) ||
      r?.profile_anak?.nama_anak
        ?.toLowerCase()
        ?.includes(keyword)

    return cocokTanggal && cocokSearch
  })

  data.sort((a, b) => {
    let va
    let vb

    switch (sortBy.value) {
      case 'nama_anak':
        va = a?.profile_anak?.nama_anak || ''
        vb = b?.profile_anak?.nama_anak || ''
        break

      case 'ruangan':
        va = a?.ruangan?.ruangan || ''
        vb = b?.ruangan?.ruangan || ''
        break

      case 'terapis':
        va = a?.terapis?.nama || ''
        vb = b?.terapis?.nama || ''
        break

      case 'tgl_regis':
        va = new Date(a?.tgl_regis)
        vb = new Date(b?.tgl_regis)
        break

      default:
        va = a?.[sortBy.value] || ''
        vb = b?.[sortBy.value] || ''
    }

    if (va < vb) {
      return sortOrder.value === 'asc'
        ? -1
        : 1
    }

    if (va > vb) {
      return sortOrder.value === 'asc'
        ? 1
        : -1
    }

    return 0
  })

  return data
})

const totalPages = computed(() => {
  return Math.max(
    1,
    Math.ceil(
      filteredSortedData.value.length /
      perPage.value
    )
  )
})

const paginatedData = computed(() => {
  const start =
    (currentPage.value - 1) * perPage.value

  return filteredSortedData.value.slice(
    start,
    start + perPage.value
  )
})

const goToPage = (page) => {
  currentPage.value = page
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) {
    return '-'
  }

  const d = new Date(dateStr)

  return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
}

const openModal = (r) => {
  selectedRegistrasi.value = r

  showModal.value = true

  selectedLayananId.value = ''

  selectedLayanan.value = null

  jadwal.value = []
}

const closeModal = () => {
  showModal.value = false
}

const canSubmit = computed(() => {
  return (
    jadwal.value.length > 0 &&
    jadwal.value.every((j) => {
      return (
        j.tanggal &&
        j.jam_mulai &&
        j.jam_selesai
      )
    })
  )
})

const submit = async () => {
  try {
    await api.post('/pelayanan-terapi-anak', {
      registrasi_anak_id:
        selectedRegistrasi.value?.id,

      layanan_id:
        selectedLayananId.value,

      tanggal_penjadwalan:
        jadwal.value
    })

    closeModal()
  } catch (error) {
    console.error(error)
  }
}
</script>

<style scoped>
/* ===============================
   TOKENS (senada dengan Sidebar.vue)
================================ */
.page {
  --bg: #FAFAFA;
  --surface: #FFFFFF;
  --border: #ECEDF1;
  --ink: #1F2128;
  --muted: #98A0AE;
  --accent: #6D5CE0;
  --accent-soft: #F1EEFC;

  min-height: 100vh;
  background: var(--bg);
  color: var(--ink);
  padding: 1.5rem;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
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
  margin-bottom: 1.5rem;
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

.stat-group {
  display: flex;
  gap: 0.75rem;
}

.stat-card {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 0.85rem;
  padding: 0.85rem 1.25rem;
  min-width: 110px;
}

.stat-label {
  font-size: 0.72rem;
  color: var(--muted);
  font-weight: 500;
}

.stat-value {
  font-size: 1.6rem;
  font-weight: 700;
  color: var(--ink);
  margin-top: 0.15rem;
}

/* ===============================
   PANEL (filter & table wrapper)
================================ */
.panel {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 1rem;
  margin-bottom: 1.5rem;
}

.filter-panel {
  padding: 1.25rem;
}

.filter-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

@media (min-width: 1024px) {
  .filter-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

.field {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.filter-label {
  font-size: 0.78rem;
  font-weight: 600;
  color: var(--ink);
}

.field-label {
  font-size: 0.78rem;
  font-weight: 500;
  color: var(--muted);
}

.section-label {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--ink);
}

.input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  right: 1rem;
  color: var(--muted);
  font-size: 0.9rem;
  pointer-events: none;
}

.input-icon--left {
  left: 1rem;
  right: auto;
}

.filter-input,
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

.filter-input {
  padding-right: 2.5rem;
}

.input.pl-11 {
  padding-left: 2.75rem;
}

.filter-input:focus,
.input:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-soft);
}

/* ===============================
   TABLE
================================ */
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

.th--sortable {
  cursor: pointer;
  transition: color 0.15s ease;
}

.th--sortable:hover {
  color: var(--accent);
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

.index-chip {
  width: 2.1rem;
  height: 2.1rem;
  border-radius: 0.6rem;
  background: var(--bg);
  border: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: var(--muted);
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
  font-size: 0.74rem;
  color: var(--muted);
  margin-top: 0.1rem;
}

.tag {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.3rem 0.75rem;
  border-radius: 999px;
  font-size: 0.74rem;
  font-weight: 500;
  background: var(--accent-soft);
  color: var(--accent);
}

.tag--muted {
  background: var(--bg);
  color: var(--ink);
  border: 1px solid var(--border);
}

.dot {
  width: 0.4rem;
  height: 0.4rem;
  border-radius: 999px;
  background: var(--accent);
}

.patient-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.avatar {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 0.7rem;
  background: var(--accent);
  color: #fff;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.avatar--lg {
  width: 3.5rem;
  height: 3.5rem;
  font-size: 1.3rem;
  border-radius: 0.85rem;
}

.therapist-cell {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.therapist-icon {
  width: 1.9rem;
  height: 1.9rem;
  border-radius: 0.5rem;
  background: var(--bg);
  border: 1px solid var(--border);
  color: var(--muted);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
}

/* ===============================
   BUTTONS
================================ */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  height: 2.6rem;
  padding: 0 1.1rem;
  border-radius: 0.65rem;
  background: var(--accent);
  color: #fff;
  font-size: 0.82rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: background 0.15s ease;
}

.btn-primary:hover {
  background: #5d4dd1;
}

.btn-primary:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.btn-primary--lg {
  height: 2.9rem;
  padding: 0 1.4rem;
}

.btn-secondary {
  height: 2.9rem;
  padding: 0 1.4rem;
  border-radius: 0.65rem;
  background: var(--bg);
  border: 1px solid var(--border);
  color: var(--ink);
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s ease;
}

.btn-secondary:hover {
  background: var(--accent-soft);
}

/* ===============================
   EMPTY STATE
================================ */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 3rem 0;
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
   PAGINATION
================================ */
.pagination-bar {
  background: var(--bg);
  border-top: 1px solid var(--border);
  padding: 1rem 1.5rem;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.pagination-info {
  font-size: 0.82rem;
  color: var(--muted);
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  flex-wrap: wrap;
}

.pagination-btn {
  min-width: 2.3rem;
  height: 2.3rem;
  padding: 0 0.6rem;
  border-radius: 0.55rem;
  border: 1px solid var(--border);
  background: var(--surface);
  font-size: 0.82rem;
  font-weight: 500;
  color: var(--ink);
  cursor: pointer;
  transition: background 0.15s ease, border-color 0.15s ease;
}

.pagination-btn:hover:not(:disabled) {
  background: var(--accent-soft);
  border-color: var(--accent);
}

.pagination-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.active-page {
  background: var(--accent);
  border-color: var(--accent);
  color: #fff;
}

/* ===============================
   MODAL
================================ */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 50;
  overflow-y: auto;
}

.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(31, 33, 40, 0.45);
}

.modal-wrapper {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
}

.modal-card {
  position: relative;
  width: 100%;
  max-width: 64rem;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 1rem;
  overflow: hidden;
}

.modal-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  padding: 1.5rem 1.75rem;
  border-bottom: 1px solid var(--border);
}

.modal-header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.modal-icon {
  width: 2.9rem;
  height: 2.9rem;
  border-radius: 0.75rem;
  background: var(--accent-soft);
  color: var(--accent);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  flex-shrink: 0;
}

.modal-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--ink);
}

.modal-sub {
  font-size: 0.82rem;
  color: var(--muted);
  margin-top: 0.15rem;
}

.modal-close {
  width: 2.3rem;
  height: 2.3rem;
  border-radius: 0.6rem;
  background: var(--bg);
  border: 1px solid var(--border);
  color: var(--muted);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.15s ease, color 0.15s ease;
}

.modal-close:hover {
  background: var(--accent-soft);
  color: var(--accent);
}

.modal-content {
  max-height: 70vh;
  overflow-y: auto;
  background: var(--bg);
  padding: 1.5rem 1.75rem;
}

.info-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 0.85rem;
  padding: 1.25rem;
  margin-bottom: 1rem;
}

.info-name {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--ink);
}

.jadwal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.25rem;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.jadwal-title {
  font-size: 1rem;
  font-weight: 700;
  color: var(--ink);
}

.session-badge {
  padding: 0.4rem 0.9rem;
  border-radius: 0.6rem;
  background: var(--accent-soft);
  color: var(--accent);
  font-size: 0.8rem;
  font-weight: 600;
}

.jadwal-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

@media (min-width: 1280px) {
  .jadwal-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

.session-card {
  border: 1px solid var(--border);
  border-radius: 0.85rem;
  padding: 1.1rem;
  background: var(--bg);
}

.session-card-head {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.session-index {
  width: 2.3rem;
  height: 2.3rem;
  border-radius: 0.6rem;
  background: var(--accent);
  color: #fff;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.modal-footer {
  background: var(--surface);
  border-top: 1px solid var(--border);
  padding: 1.1rem 1.75rem;
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
}

/* ===============================
   TRANSITIONS
================================ */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.animate-modal {
  animation: modalShow 0.2s ease;
}

@keyframes modalShow {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
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