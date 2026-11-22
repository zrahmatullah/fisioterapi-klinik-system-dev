<template>
  <div class="page-catatan">

    <!-- ================= HEADER ================= -->
    <header class="ct-header">
      <span class="ct-eyebrow">Terapis · Sesi Harian</span>
      <h1 class="ct-title">Catatan Aktivitas Sesi Terapi Anak</h1>
      <p class="ct-subtitle">Catat aktivitas, keterangan, dan tugas rumah untuk setiap sesi terapi.</p>
    </header>

    <!-- ================= TOOLBAR ================= -->
    <div class="ct-toolbar">
      <div class="ct-filter">
        <label for="filter-status">Status</label>
        <select id="filter-status" v-model="filterStatus" class="ct-select">
          <option value="">Semua</option>
          <option value="sudah">Sudah Dicatat</option>
          <option value="belum">Belum Dicatat</option>
        </select>
      </div>

      <div class="ct-search">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="7"/>
          <path d="m21 21-4.3-4.3"/>
        </svg>
        <input v-model="search" type="text" placeholder="Cari nama anak..." />
      </div>

      <span class="ct-count">{{ filteredData.length }} sesi</span>
    </div>

    <!-- ================= TABLE ================= -->
    <div class="ct-table-card">
      <div class="ct-table-scroll">
        <table class="ct-table">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="col-no">No</th>
              <th class="col-date">Tanggal</th>
              <th>Nama Anak</th>
              <th>Orang Tua</th>
              <th class="col-center">Ruangan</th>
              <th>Layanan</th>
              <th class="col-center">Status</th>
              <th class="col-center">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item, index) in paginatedData" :key="item.id">
              <td class="col-no">{{ (page - 1) * perPage + index + 1 }}</td>
              <td class="col-date cell-date">{{ formatTanggal(item.tanggal_penjadwalan) }}</td>
              <td class="cell-strong">{{ item.registrasi_anak?.profile_anak?.nama_anak || '-' }}</td>
              <td class="cell-soft">{{ item.registrasi_anak?.nama_ayah || '-' }} / {{ item.registrasi_anak?.nama_ibu || '-' }}</td>
              <td class="col-center cell-soft">{{ item.registrasi_anak?.ruangan?.ruangan || '-' }}</td>
              <td class="cell-soft">{{ item.layanan?.layanan || '-' }}</td>
              <td class="col-center">
                <span
                  class="status-badge"
                  :class="item.catatan_aktivitas ? 'status-done' : 'status-pending'"
                >
                  <span class="status-dot"></span>
                  {{ item.catatan_aktivitas ? 'Sudah Dicatat' : 'Belum Dicatat' }}
                </span>
              </td>
              <td class="col-center">
                <button
                  @click="openForm(item)"
                  class="btn btn-sm"
                  :class="item.catatan_aktivitas ? 'btn-outline' : 'btn-primary'"
                >
                  {{ item.catatan_aktivitas ? 'Lihat' : 'Catat' }}
                </button>
              </td>
            </tr>

            <tr v-if="paginatedData.length === 0">
              <td colspan="8" class="ct-empty-row">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M9 12h6m-6 4h6M9 8h1M5 21h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/>
                </svg>
                Data tidak ditemukan
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================= PAGINATION ================= -->
    <div class="ct-pagination">
      <span class="ct-pagination-info">
        Menampilkan <strong>{{ paginatedData.length }}</strong> dari <strong>{{ filteredData.length }}</strong> data
      </span>

      <div class="ct-pagination-controls">
        <button class="btn btn-outline btn-sm" :disabled="page === 1" @click="page--">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
          Prev
        </button>
        <span class="ct-page-pill">{{ page }} / {{ totalPage || 1 }}</span>
        <button class="btn btn-outline btn-sm" :disabled="page >= totalPage" @click="page++">
          Next
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </button>
      </div>
    </div>

    <!-- ================= MODAL ================= -->
    <teleport to="body">
    <Transition name="ct-fade">
      <div v-if="showModal" class="ct-overlay" @click.self="closeModal">
        <div class="ct-modal" role="dialog" aria-modal="true" aria-label="Catatan aktivitas terapi">

          <div class="ct-modal-header">
            <div>
              <span class="ct-eyebrow">{{ viewOnly ? 'Detail Catatan' : 'Catatan Aktivitas' }}</span>
              <h2>{{ selected?.registrasi_anak?.profile_anak?.nama_anak || '-' }}</h2>
            </div>
            <button class="icon-btn" @click="closeModal" aria-label="Tutup">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6 6 18M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Ringkasan sebagai tabel 2 kolom, konsisten dengan halaman lain -->
          <table class="ct-summary-table">
            <tbody>
              <tr>
                <th>Orang Tua</th>
                <td>{{ selected?.registrasi_anak?.nama_ayah || '-' }} / {{ selected?.registrasi_anak?.nama_ibu || '-' }}</td>
                <th>Terapis</th>
                <td>{{ selected?.terapis?.nama || '-' }}</td>
              </tr>
              <tr>
                <th>Ruangan</th>
                <td>{{ selected?.registrasi_anak?.ruangan?.ruangan || '-' }}</td>
                <th>Tanggal</th>
                <td>{{ formatTanggal(selected?.tanggal_penjadwalan) }}</td>
              </tr>
              <tr>
                <th>Layanan</th>
                <td colspan="3">{{ selected?.layanan?.layanan || '-' }}</td>
              </tr>
            </tbody>
          </table>

          <!-- FORM -->
          <form @submit.prevent="submit" class="ct-modal-body">
            <label class="ct-checkbox-row">
              <input type="checkbox" v-model="form.checkin_sesi" :disabled="viewOnly" />
              <span>Check-in Sesi</span>
            </label>

            <div class="ct-field">
              <label>Aktivitas Terapi</label>
              <textarea
                v-model="form.aktivitas_terapi"
                :disabled="viewOnly"
                class="ct-textarea"
                placeholder="Jelaskan aktivitas yang dilakukan selama sesi..."
              ></textarea>
            </div>

            <div class="ct-field">
              <label>Keterangan Terapi</label>
              <textarea
                v-model="form.keterangan_terapi"
                :disabled="viewOnly"
                class="ct-textarea"
                placeholder="Catatan tambahan mengenai kondisi atau respons anak..."
              ></textarea>
            </div>

            <div class="ct-field">
              <label>Tugas Rumah</label>
              <textarea
                v-model="form.tugas_rumah"
                :disabled="viewOnly"
                class="ct-textarea"
                placeholder="Tugas atau latihan yang perlu dilakukan di rumah..."
              ></textarea>
            </div>

            <div class="ct-modal-footer">
              <button type="button" class="btn btn-outline" @click="closeModal">Tutup</button>
              <button v-if="!viewOnly" type="submit" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                  <path d="M17 21v-8H7v8M7 3v5h8"/>
                </svg>
                Simpan
              </button>
            </div>
          </form>

        </div>
      </div>
    </Transition>
    </teleport>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useToast } from 'vue-toastification'
import api from '../axios'

const toast = useToast()

const filterStatus = ref('')
const sesiTerapi = ref([])
const showModal = ref(false)
const selected = ref(null)

const search = ref('')
const page = ref(1)
const perPage = 15

watch([search, filterStatus], () => (page.value = 1))

const form = reactive({
  registrasi_anak_id: null,
  pelayanan_terapi_anak_id: null,
  aktivitas_terapi: '',
  keterangan_terapi: '',
  tugas_rumah: '',
  checkin_sesi: false
})

const viewOnly = computed(() => !!selected.value?.catatan_aktivitas)

const filteredData = computed(() =>
  sesiTerapi.value.filter(item => {
    const cocokNama =
      item.registrasi_anak?.profile_anak?.nama_anak
        ?.toLowerCase()
        .includes(search.value.toLowerCase())

    let cocokStatus = true
    if (filterStatus.value === 'sudah') {
      cocokStatus = !!item.catatan_aktivitas
    } else if (filterStatus.value === 'belum') {
      cocokStatus = !item.catatan_aktivitas
    }

    return cocokNama && cocokStatus
  })
)

const totalPage = computed(() =>
  Math.ceil(filteredData.value.length / perPage)
)

const paginatedData = computed(() => {
  const start = (page.value - 1) * perPage
  return filteredData.value.slice(start, start + perPage)
})

const formatTanggal = (tgl) => {
  if (!tgl) return '-'
  const d = new Date(tgl)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}/${month}/${year}`
}

onMounted(async () => {
  const res = await api.get('/dashboard-terapis')
  sesiTerapi.value = res.data.flatMap(r =>
    (r.pelayanans || []).map(p => ({
      ...p,
      registrasi_anak: r
    }))
  )
})

const openForm = (item) => {
  selected.value = item
  Object.assign(form, item.catatan_aktivitas || {})
  form.registrasi_anak_id = item.registrasi_anak_id
  form.pelayanan_terapi_anak_id = item.id
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const submit = async () => {
  try {
    await api.post('/catatan-aktivitas-anak', form)
    toast.success('Catatan berhasil disimpan')
    closeModal()
  } catch (e) {
    toast.error('Gagal menyimpan catatan')
  }
}
</script>

<style scoped>
/* ============ TOKENS — Modern Clinical Teal & Slate ============ */
.page-catatan {
  --ink: #0F172A;
  --ink-soft: #64748B;
  --bg: #F3F6F8;
  --surface: #FFFFFF;
  --border: #E2E8F0;
  --row-hover: #F8FAFC;
  --teal: #0F766E;
  --teal-deep: #115E59;
  --teal-soft: #E6F2F5;
  --amber: #D97706;
  --amber-soft: #FEF3C7;
  --emerald: #15803D;
  --emerald-soft: #DCFCE7;
  --slate-soft: #F1F5F9;

  width: 100%;
  flex: 1;
  min-height: 0;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  overflow: hidden;
  color: var(--ink);
  font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;
}

.ct-header {
  width: 100%;
  flex-shrink: 0;
  background: white;
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 12px 16px;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.ct-eyebrow {
  font-size: 11px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--teal);
  font-weight: 600;
}
.ct-title {
  font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
  font-size: 15px;
  font-weight: 600;
  letter-spacing: -0.01em;
  margin: 2px 0;
  color: var(--ink);
}
.ct-subtitle {
  font-size: 11.5px;
  color: var(--ink-soft);
  margin: 0;
}

/* ============ TOOLBAR ============ */
.ct-toolbar {
  width: 100%;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.ct-filter {
  display: flex;
  align-items: center;
  gap: 8px;
}
.ct-filter label {
  font-size: 13px;
  color: var(--ink-soft, #6B7280);
  font-weight: 600;
  white-space: nowrap;
}

.ct-select {
  border: 1px solid var(--border, #E5E7EB);
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 13.5px;
  color: var(--ink, #1E1B2E);
  background: var(--surface, #FFFFFF);
  font-family: inherit;
}
.ct-select:focus {
  outline: none;
  border-color: var(--teal, #0F766E);
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}

.ct-search {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--surface, #FFFFFF);
  border: 1px solid var(--border, #E5E7EB);
  border-radius: 8px;
  padding: 8px 12px;
  flex: 1;
  max-width: 280px;
  color: var(--ink-soft, #6B7280);
}
.ct-search svg { flex-shrink: 0; }
.ct-search input {
  border: none;
  background: transparent;
  outline: none;
  font-size: 13.5px;
  color: var(--ink, #1E1B2E);
  width: 100%;
  font-family: inherit;
}
.ct-search input::placeholder { color: var(--ink-soft, #6B7280); }

.ct-count {
  margin-left: auto;
  font-family: 'JetBrains Mono', monospace;
  font-size: 12px;
  color: var(--ink-soft, #6B7280);
  white-space: nowrap;
}

/* ============ TABLE ============ */
.ct-table-card {
  width: 100%;
  margin: 0;
  flex: 1;
  min-height: 0;
  display: flex;
  flex-direction: column;
  background: var(--surface, #FFFFFF);
  border: 1px solid var(--border, #E5E7EB);
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
  overflow: hidden;
}

.ct-table-scroll {
  flex: 1;
  min-height: 0;
  overflow: auto;
}

.ct-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13.5px;
  table-layout: fixed;
}
.ct-table thead th {
  text-align: left;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--ink-soft, #6B7280);
  font-weight: 700;
  padding: 12px 14px;
  background: var(--bg, #F8F8FC);
  border-bottom: 1px solid var(--border, #E5E7EB);
  white-space: nowrap;
}
.ct-table tbody tr { border-bottom: 1px solid var(--border, #E5E7EB); }
.ct-table tbody tr:last-child { border-bottom: none; }
.ct-table tbody tr:hover { background: var(--row-hover, #F5F5FB); }
.ct-table tbody td {
  padding: 12px 14px;
  vertical-align: middle;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.col-no { width: 44px; text-align: center; color: var(--ink-soft, #6B7280); font-family: 'JetBrains Mono', monospace; font-size: 12.5px; }
.col-date { width: 110px; }
.col-center { text-align: center; }

.cell-strong { font-weight: 700; color: var(--ink, #1E1B2E); }
.cell-soft { color: var(--ink-soft, #6B7280); }
.cell-date {
  font-family: 'JetBrains Mono', monospace;
  font-size: 12.5px;
  color: var(--ink-soft, #6B7280);
}

.ct-empty-row {
  text-align: center;
  padding: 56px 16px;
  color: var(--ink-soft, #6B7280);
  font-size: 13.5px;
  white-space: normal;
}
.ct-empty-row svg {
  display: block;
  margin: 0 auto 10px;
  color: var(--teal, #0F766E);
  opacity: 0.5;
}

/* ============ STATUS BADGE ============ */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 11.5px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 999px;
  white-space: nowrap;
}
.status-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.status-done { background: var(--emerald-soft, #ECFDF5); color: var(--emerald, #059669); }
.status-done .status-dot { background: var(--emerald, #059669); }
.status-pending { background: var(--amber-soft, #FEF3E2); color: var(--amber, #D97706); }
.status-pending .status-dot { background: var(--amber, #D97706); }

/* ============ BUTTONS ============ */
/* Selector digandakan (button.btn) agar menang melawan reset global
   seperti Tailwind preflight (`button { background-color: transparent }`). */
button.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  font-size: 13.5px;
  font-weight: 600;
  padding: 9px 16px;
  border-radius: 8px;
  border: 1px solid transparent;
  cursor: pointer;
  transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
  white-space: nowrap;
  line-height: 1.2;
}
button.btn:active { transform: translateY(1px); }
button.btn:disabled { opacity: 0.45; cursor: not-allowed; }
button.btn:focus-visible {
  outline: 2px solid #4F46E5;
  outline-offset: 2px;
}
button.btn-sm { padding: 6px 12px; font-size: 12.5px; min-width: 72px; }

button.btn-primary {
  background: var(--teal, #0F766E);
  color: #fff;
  box-shadow: 0 1px 2px rgba(79, 70, 229, 0.25);
}
button.btn-primary:hover:not(:disabled) { background: var(--teal-deep, #115E59); }

button.btn-outline {
  background: var(--surface, #FFFFFF);
  border: 1px solid var(--border, #E5E7EB);
  color: var(--ink-soft, #6B7280);
}
button.btn-outline:hover:not(:disabled) { background: var(--slate-soft, #F1F2F4); }

/* ============ PAGINATION ============ */
.ct-pagination {
  max-width: 1280px;
  margin: 16px auto 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}
.ct-pagination-info {
  font-size: 13px;
  color: var(--ink-soft, #6B7280);
}
.ct-pagination-info strong { color: var(--ink, #1E1B2E); }

.ct-pagination-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}
.ct-page-pill {
  font-family: 'JetBrains Mono', monospace;
  font-size: 12.5px;
  font-weight: 600;
  color: var(--ink, #0F172A);
  background: var(--teal-soft, #E6F2F5);
  padding: 6px 12px;
  border-radius: 999px;
}

/* ============ MODAL ============ */
.ct-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.35);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  z-index: 9999;
}

.ct-modal {
  background: var(--surface, #FFFFFF);
  width: 100%;
  max-width: 720px;
  max-height: 90vh;
  border-radius: 16px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 24px 60px rgba(30, 27, 46, 0.22);
}

.ct-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 22px 26px 16px;
  border-bottom: 1px solid var(--border, #E5E7EB);
  flex-shrink: 0;
}
.ct-modal-header h2 {
  font-family: 'Fraunces', Georgia, serif;
  font-size: 20px;
  margin: 4px 0 0;
  color: var(--ink, #1E1B2E);
}

.icon-btn {
  border: none;
  background: var(--bg, #F8F8FC);
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--ink-soft, #6B7280);
  cursor: pointer;
  flex-shrink: 0;
}
.icon-btn:hover { background: var(--border, #E5E7EB); color: var(--ink, #1E1B2E); }
.icon-btn:focus-visible { outline: 2px solid #4F46E5; outline-offset: 2px; }

.ct-summary-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
  flex-shrink: 0;
}
.ct-summary-table tr { border-bottom: 1px solid var(--border, #E5E7EB); }
.ct-summary-table th, .ct-summary-table td { padding: 9px 18px; text-align: left; }
.ct-summary-table th {
  width: 100px;
  color: var(--ink-soft, #6B7280);
  font-weight: 600;
  background: var(--bg, #F8F8FC);
  white-space: nowrap;
}
.ct-summary-table td { color: var(--ink, #1E1B2E); font-weight: 500; }

.ct-modal-body {
  padding: 18px 26px 22px;
  overflow-y: auto;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.ct-checkbox-row {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13.5px;
  font-weight: 600;
  color: var(--ink, #1E1B2E);
  cursor: pointer;
}
.ct-checkbox-row input {
  width: 17px;
  height: 17px;
  accent-color: var(--teal, #0F766E);
  cursor: pointer;
}

.ct-field label {
  display: block;
  font-size: 12.5px;
  font-weight: 600;
  color: var(--ink-soft, #6B7280);
  margin-bottom: 6px;
}

.ct-textarea {
  width: 100%;
  border: 1px solid var(--border, #E5E7EB);
  border-radius: 10px;
  padding: 10px 12px;
  font-size: 13.5px;
  color: var(--ink, #1E1B2E);
  background: var(--surface, #FFFFFF);
  font-family: inherit;
  min-height: 80px;
  resize: vertical;
  line-height: 1.5;
  transition: border-color 0.12s ease, box-shadow 0.12s ease;
}
.ct-textarea:focus {
  outline: none;
  border-color: var(--teal, #0F766E);
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}
.ct-textarea:disabled {
  background: var(--bg, #F8F8FC);
  color: var(--ink-soft, #6B7280);
  cursor: not-allowed;
}

.ct-modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding-top: 8px;
  border-top: 1px solid var(--border, #E5E7EB);
}

/* Transition */
.ct-fade-enter-active, .ct-fade-leave-active { transition: opacity 0.18s ease; }
.ct-fade-enter-from, .ct-fade-leave-to { opacity: 0; }

/* ============ RESPONSIVE ============ */
@media (max-width: 720px) {
  .page-catatan { padding: 24px 16px 48px; }
  .ct-table { min-width: 760px; }
  .ct-count { margin-left: 0; }
  .ct-summary-table th { width: 90px; }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  button.btn, .ct-table tbody tr, .ct-textarea {
    transition: none;
  }
}
</style>