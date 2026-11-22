<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-chart-bar"></i>
        </div>

        <div>
          <h1 class="text-base font-bold text-[#0F172A] leading-tight">Registrasi & Layanan Anak</h1>
          <p class="text-xs text-[#64748B] mt-0.5">Monitoring registrasi, penjadwalan terapi, dan penentuan terapis</p>
        </div>
      </div>
    </div>

    <!-- SUMMARY -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 shrink-0">
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Registrasi</p>
        <h2 class="text-lg font-bold text-[#0F172A] tabular-nums mt-0.5">{{ rows.length || 0 }}</h2>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <p class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider">Terjadwal</p>
        <h2 class="text-lg font-bold text-amber-800 tabular-nums mt-0.5">{{ countStatus('terjadwal') || 0 }}</h2>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <p class="text-[11px] font-semibold text-sky-700 uppercase tracking-wider">Proses</p>
        <h2 class="text-lg font-bold text-sky-800 tabular-nums mt-0.5">{{ countStatus('proses') }}</h2>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <p class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">Selesai</p>
        <h2 class="text-lg font-bold text-emerald-800 tabular-nums mt-0.5">{{ countStatus('selesai') }}</h2>
      </div>
    </div>

    <!-- FILTER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm shrink-0">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3">

        <div class="flex items-center gap-2">
          <span class="text-xs text-slate-500 font-medium">Tampilkan</span>
          <input type="number" min="1" v-model.number="perPage" class="w-16 h-[34px] px-2 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E] text-center" />
          <span class="text-xs text-slate-500 font-medium">data</span>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
          <input type="date" v-model="filterTanggal" class="h-[34px] px-2.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]" />

          <div class="relative flex-1 sm:w-72">
            <i class="pi pi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            <input
              v-model="search"
              type="text"
              placeholder="Cari nama anak / no registrasi..."
              class="w-full pl-8 pr-3 h-[34px] border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">

      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="data-table">

          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="th">No</th>
              <th class="th">No Regis</th>

              <th class="th th--sortable" @click="sortBy('nama')">
                Nama Anak <span class="sort-icon">⇅</span>
              </th>

              <th class="th th--sortable" @click="sortBy('tanggal')">
                Tanggal <span class="sort-icon">⇅</span>
              </th>

              <th class="th">Layanan</th>
              <th class="th text-center">Total Sesi</th>

              <th class="th th--sortable" @click="sortBy('status')">
                Status <span class="sort-icon">⇅</span>
              </th>

              <th class="th text-center">Aksi</th>
            </tr>
          </thead>

          <tbody>

            <tr v-for="row in paginatedRows" :key="row.no" class="row">
              <td class="td text-center cell-faint">{{ row.no }}</td>

              <td class="td cell-strong cell-strong--accent">{{ row.no_regis }}</td>

              <td class="td cell-strong">{{ row.profile_anak?.nama_anak }}</td>

              <td class="td">{{ formatDate(row.tgl_regis) }}</td>

              <td class="td">{{ row.pelayanans?.[0]?.layanan?.layanan || '-' }}</td>

              <td class="td text-center">{{ row.pelayanans?.length || 0 }}</td>

              <td class="td">
                <span :class="statusClass(row.pelayanans?.[0]?.status)">
                  {{ row.pelayanans?.[0]?.status || '-' }}
                </span>
              </td>

              <td class="td">
                <div class="flex justify-center gap-2">
                  <button @click="openDetail(row)" class="btn-primary">Detail</button>
                  <button @click="openTerapis(row)" class="btn-accent-alt">Terapis</button>
                </div>
              </td>
            </tr>

            <!-- EMPTY -->
            <tr v-if="paginatedRows.length === 0">
              <td colspan="8">
                <div class="empty-state">
                  <div class="empty-icon"><i class="pi pi-inbox"></i></div>
                  <p class="empty-title">Tidak ada data ditemukan</p>
                  <p class="empty-sub">Coba ubah filter pencarian</p>
                </div>
              </td>
            </tr>

          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div v-if="totalPages > 1" class="pagination-bar">

        <div class="pagination-info">
          Halaman <span class="cell-strong">{{ currentPage }}</span> dari
          <span class="cell-strong">{{ totalPages }}</span>
        </div>

        <div class="pagination-controls">

          <button class="pagination-btn" :disabled="currentPage === 1" @click="changePage(currentPage - 1)">
            Prev
          </button>

          <button
            v-for="p in visiblePages"
            :key="p"
            @click="changePage(p)"
            class="pagination-btn"
            :class="{ 'active-page': p === currentPage }"
          >
            {{ p }}
          </button>

          <button class="pagination-btn" :disabled="currentPage === totalPages" @click="changePage(currentPage + 1)">
            Next
          </button>

        </div>
      </div>
    </div>

    <!-- MODAL DETAIL -->
    <teleport to="body">
    <div v-if="showDetail" class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showDetail = false"></div>

      <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-fadeIn flex flex-col max-h-[85vh]">

        <div class="bg-[#0B3948] text-white px-5 py-3.5 flex items-center justify-between shrink-0">
          <h2 class="text-sm font-bold tracking-wide">Detail Penjadwalan</h2>

          <button @click="showDetail = false" class="text-slate-300 hover:text-white p-1">
            <i class="pi pi-times text-xs"></i>
          </button>
        </div>

        <div class="p-4 overflow-y-auto custom-scrollbar flex-1">
          <table class="w-full text-[12.5px] text-left text-slate-700">
            <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
              <tr>
                <th class="py-2 px-3">Sesi</th>
                <th class="py-2 px-3">Tanggal</th>
                <th class="py-2 px-3">Terapis</th>
                <th class="py-2 px-3">Status</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
              <tr v-for="(s, i) in modalData.pelayanans" :key="s.id" class="hover:bg-slate-50">
                <td class="py-2 px-3 font-semibold">Sesi {{ i + 1 }}</td>
                <td class="py-2 px-3 text-slate-600">{{ formatDate(s.tanggal_penjadwalan) }}</td>
                <td class="py-2 px-3">{{ s.terapis?.nama || '-' }}</td>
                <td class="py-2 px-3">
                  <span :class="statusClass(s.status)" class="text-xs">{{ s.status }}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="p-3 bg-slate-50 border-t border-slate-200 flex justify-end shrink-0">
          <button @click="showDetail = false" class="btn-secondary-clinical text-xs py-1.5 px-3">Tutup</button>
        </div>

      </div>
    </div>
    </teleport>

    <!-- MODAL TERAPIS -->
    <teleport to="body">
    <div v-if="showTerapis" class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showTerapis = false"></div>

      <div class="relative bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-fadeIn flex flex-col max-h-[85vh]">

        <div class="bg-[#0B3948] text-white px-5 py-3.5 flex items-center justify-between shrink-0">
          <h2 class="text-sm font-bold tracking-wide">Penentuan Terapis</h2>

          <button @click="showTerapis = false" class="text-slate-300 hover:text-white p-1">
            <i class="pi pi-times text-xs"></i>
          </button>
        </div>

        <div class="p-4 overflow-y-auto custom-scrollbar flex-1 space-y-3">
          <table class="w-full text-[12.5px] text-left text-slate-700">
            <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
              <tr>
                <th class="py-2 px-3">Sesi</th>
                <th class="py-2 px-3">Tanggal</th>
                <th class="py-2 px-3">Terapis</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
              <tr v-for="(s, i) in modalData.pelayanans" :key="s.id" class="hover:bg-slate-50">
                <td class="py-2 px-3 font-semibold">Sesi {{ i + 1 }}</td>
                <td class="py-2 px-3 text-slate-600">{{ formatDate(s.tanggal_penjadwalan) }}</td>

                <td class="py-2 px-3">
                  <select v-model="s.terapis_id" class="w-full h-[32px] px-2 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] bg-white">
                    <option value="">-- Pilih Terapis --</option>
                    <option v-for="t in terapisList" :key="t.id" :value="t.id">
                      {{ t.nama }}
                    </option>
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="p-3 bg-slate-50 border-t border-slate-200 flex justify-end gap-2 shrink-0">
          <button @click="showTerapis = false" class="btn-secondary-clinical text-xs py-1.5 px-3">Batal</button>
          <button @click="saveAllTerapis" class="btn-primary-clinical text-xs py-1.5 px-4 flex items-center gap-1.5">
            <i class="pi pi-save"></i>
            Simpan Terapis
          </button>
        </div>

      </div>
    </div>
    </teleport>

  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'

const rows = ref([])
const currentPage = ref(1)
const perPage = ref(10)

const search = ref('')
const sortKey = ref('')
const sortAsc = ref(true)

const modalData = ref({ pelayanans: [] })

const showDetail = ref(false)
const showTerapis = ref(false)

const terapisList = ref([])

const filterTanggal = ref(
  new Date().toISOString().slice(0, 10)
)

const toast = useToast()

const formatDate = (dateStr) => {
  if (!dateStr) return '-'

  const d = new Date(dateStr)

  if (isNaN(d)) return dateStr

  return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
}

onMounted(async () => {
  try {

    terapisList.value =
      (await api.get('/user-profile?jenis_user_id=9')).data.data

    const res = await api.get('/registrasi-anak')

    rows.value = res.data.data ?? res.data

  } catch (err) {
    console.error(err)
    toast.error('Gagal mengambil data')
  }
})

const countStatus = (status) => {
  return rows.value.filter(
    r => r.pelayanans?.[0]?.status === status
  ).length
}

const filteredRows = computed(() => {

  const q = search.value.toLowerCase()

  return rows.value.filter(r => {

    const cocokTanggal =
      r.tgl_regis?.slice(0, 10) === filterTanggal.value

    const cocokSearch =
      !search.value ||
      r.profile_anak?.nama_anak?.toLowerCase().includes(q) ||
      r.no_regis?.toLowerCase().includes(q)

    return cocokTanggal && cocokSearch
  })
})

const sortedRows = computed(() => {

  if (!sortKey.value) return filteredRows.value

  return [...filteredRows.value].sort((a, b) => {

    let A, B

    if (sortKey.value === 'nama') {
      A = a.profile_anak?.nama_anak || ''
      B = b.profile_anak?.nama_anak || ''
    }
    else if (sortKey.value === 'tanggal') {
      A = a.tgl_regis || ''
      B = b.tgl_regis || ''
    }
    else {
      A = a.pelayanans?.[0]?.status || ''
      B = b.pelayanans?.[0]?.status || ''
    }

    if (A < B) return sortAsc.value ? -1 : 1
    if (A > B) return sortAsc.value ? 1 : -1

    return 0
  })
})

const paginatedRows = computed(() => {

  const start =
    (currentPage.value - 1) * perPage.value

  return sortedRows.value
    .slice(start, start + perPage.value)
    .map((row, index) => ({
      ...row,
      no: start + index + 1
    }))
})

const totalPages = computed(() =>
  Math.ceil(sortedRows.value.length / perPage.value)
)

const visiblePages = computed(() => {

  const total = totalPages.value
  const current = currentPage.value

  const range = 2

  let start = Math.max(1, current - range)
  let end = Math.min(total, current + range)

  const pages = []

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

const changePage = (page) => {
  if (page < 1 || page > totalPages.value) return
  currentPage.value = page
}

const sortBy = (key) => {

  if (sortKey.value === key) {
    sortAsc.value = !sortAsc.value
  } else {
    sortKey.value = key
    sortAsc.value = true
  }
}

watch([search, perPage], () => {

  if (!perPage.value || perPage.value < 1) {
    perPage.value = 1
  }

  currentPage.value = 1
})

const openDetail = (row) => {
  modalData.value = JSON.parse(JSON.stringify(row))
  showDetail.value = true
}

const openTerapis = (row) => {
  modalData.value = JSON.parse(JSON.stringify(row))
  showTerapis.value = true
}

const saveAllTerapis = async () => {

  try {

    for (const s of modalData.value.pelayanans) {

      if (s.terapis_id) {

        await api.put(
          `/pelayanan-terapi-anak/${s.id}/terapis`,
          {
            terapis_id: s.terapis_id
          }
        )
      }
    }

    await kirimEmail(modalData.value)

    toast.success(
      'Terapis berhasil disimpan & email dikirim'
    )

    showTerapis.value = false

    const res = await api.get('/registrasi-anak')

    rows.value = res.data.data ?? res.data

  } catch (err) {

    console.error(err)

    toast.error(
      'Gagal menyimpan terapis / kirim email'
    )
  }
}

const kirimEmail = async (row) => {

  try {

    await api.post(
      `/registrasi-anak/${row.id}/kirim-email-jadwal`
    )

  } catch {
    toast.error('Gagal mengirim email')
  }
}

const statusClass = (status) => ({
  terjadwal: 'badge badge--amber',
  proses: 'badge badge--blue',
  selesai: 'badge badge--green',
  batal: 'badge badge--red'
}[status] || 'badge badge--gray')
</script>

<style scoped>
/* ===============================
   TOKENS (senada dengan Sidebar.vue & DaftarPasien.vue)
================================ */
.page {
  --bg: #FAFAFA;
  --surface: #FFFFFF;
  --border: #ECEDF1;
  --ink: #1F2128;
  --muted: #98A0AE;
  --accent: #0F766E;
  --accent-soft: #E6F2F5;

  width: 100%;
  height: 100%;
}

/* ===============================
   HEADER
================================ */
.page-header {
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
  font-size: 2rem;
  font-weight: 700;
  margin-top: 0.4rem;
  color: var(--accent);
}

.stat-value--amber { color: var(--amber); }
.stat-value--blue { color: var(--blue); }
.stat-value--green { color: var(--green); }

/* ===============================
   PANEL
================================ */
.panel {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 1rem;
}

.filter-panel {
  padding: 1.25rem;
}

.filter-row {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  justify-content: space-between;
}

@media (min-width: 1280px) {
  .filter-row {
    flex-direction: row;
    align-items: center;
  }
}

.per-page-field {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.filter-text {
  font-size: 0.85rem;
  color: var(--muted);
}

.filter-inputs {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  width: 100%;
}

@media (min-width: 768px) {
  .filter-inputs {
    flex-direction: row;
    width: auto;
  }
}

/* ===============================
   INPUTS
================================ */
.input {
  height: 2.75rem;
  border-radius: 0.65rem;
  border: 1px solid var(--border);
  background: var(--bg);
  padding: 0 1rem;
  font-size: 0.85rem;
  color: var(--ink);
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.input--center {
  text-align: center;
}

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
  padding: 0.9rem 1rem;
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

.sort-icon {
  font-size: 0.7rem;
  opacity: 0.7;
}

.td {
  padding: 0.9rem 1rem;
  color: var(--ink);
  vertical-align: middle;
  border-bottom: 1px solid var(--border);
  white-space: nowrap;
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
}

.cell-strong--accent {
  color: var(--accent);
}

.cell-faint {
  color: var(--muted);
}

/* ===============================
   BADGES
================================ */
.badge {
  display: inline-flex;
  align-items: center;
  padding: 0.3rem 0.75rem;
  border-radius: 999px;
  font-size: 0.74rem;
  font-weight: 600;
}

.badge--amber { background: var(--amber-soft); color: var(--amber); }
.badge--blue { background: var(--blue-soft); color: var(--blue); }
.badge--green { background: var(--green-soft); color: var(--green); }
.badge--red { background: var(--red-soft); color: var(--red); }
.badge--gray { background: var(--bg); color: var(--muted); border: 1px solid var(--border); }

/* ===============================
   BUTTONS
================================ */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  height: 2.2rem;
  padding: 0 0.9rem;
  border-radius: 0.55rem;
  background: var(--accent);
  color: #fff;
  font-size: 0.76rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: background 0.15s ease;
}

.btn-primary:hover {
  background: #5d4dd1;
}

.btn-accent-alt {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  height: 2.2rem;
  padding: 0 0.9rem;
  border-radius: 0.55rem;
  background: var(--green);
  color: #fff;
  font-size: 0.76rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: background 0.15s ease;
}

.btn-accent-alt:hover {
  background: #157d59;
}

.btn-accent-alt--lg {
  height: 2.6rem;
  padding: 0 1.25rem;
  font-size: 0.85rem;
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
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--ink);
}

.empty-sub {
  font-size: 0.8rem;
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
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  animation: fadeIn 0.18s ease;
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
  max-width: 56rem;
  max-height: 85vh;
  display: flex;
  flex-direction: column;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 1rem;
  overflow: hidden;
}

.modal-card--wide {
  max-width: 64rem;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}

.modal-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--ink);
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

.modal-content--flush {
  background: var(--surface);
}

/* ===============================
   TRANSITIONS
================================ */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
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