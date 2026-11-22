<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-calendar-times"></i>
        </div>

        <div>
          <h1 class="text-base font-bold text-[#0F172A] leading-tight">Request Reschedule Jadwal</h1>
          <p class="text-xs text-[#64748B] mt-0.5">Daftar permintaan perubahan jadwal terapi dari orang tua</p>
        </div>
      </div>

      <div class="bg-[#F0F6F8] border border-[#DCE8EC] rounded-lg px-3 py-1.5 flex items-center gap-2 shrink-0">
        <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Pending Request:</p>
        <h2 class="text-sm font-bold text-amber-700 tabular-nums">{{ list.length }}</h2>
      </div>
    </div>

    <!-- TABLE -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">

      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="data-table">

          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="th">Anak</th>
              <th class="th">Layanan</th>
              <th class="th">Terapis</th>
              <th class="th">Tanggal Lama</th>
              <th class="th">Tanggal Baru</th>
              <th class="th text-center">Status</th>
              <th class="th text-center">Aksi</th>
            </tr>
          </thead>

          <tbody>

            <tr v-for="(row, i) in list" :key="i" class="row">

              <!-- ANAK -->
              <td class="td">
                <div class="patient-cell">
                  <div class="avatar">
                    {{ row.registrasi?.profile_anak?.nama_anak?.charAt(0) || '?' }}
                  </div>

                  <div>
                    <p class="cell-strong">{{ row.registrasi?.profile_anak?.nama_anak || '-' }}</p>
                    <p class="cell-faint">{{ row.registrasi?.no_regis || '-' }}</p>
                  </div>
                </div>
              </td>

              <!-- LAYANAN -->
              <td class="td">
                <span class="tag">{{ row.layanan?.layanan || '-' }}</span>
              </td>

              <!-- TERAPIS -->
              <td class="td">
                <div class="therapist-cell">
                  <div class="therapist-icon"><i class="pi pi-user"></i></div>
                  <span class="cell-strong">{{ row.terapis?.nama || '-' }}</span>
                </div>
              </td>

              <!-- TANGGAL LAMA -->
              <td class="td">
                <div class="date-stack">
                  <span class="cell-faint">Jadwal Lama</span>
                  <span class="cell-strong">{{ formatDate(row.tanggal_penjadwalan) }}</span>
                </div>
              </td>

              <!-- TANGGAL BARU -->
              <td class="td">
                <div class="date-stack">
                  <span class="cell-faint cell-faint--accent">Request Baru</span>
                  <span class="cell-strong cell-strong--accent">{{ formatDate(row.tanggal_reschedule_request) }}</span>
                </div>
              </td>

              <!-- STATUS -->
              <td class="td text-center">
                <span class="badge badge--amber">Pending</span>
              </td>

              <!-- AKSI -->
              <td class="td">
                <div class="flex justify-center gap-2">
                  <button @click="openModal(row, 'approve')" class="btn-approve">
                    <i class="pi pi-check"></i>
                    Approve
                  </button>

                  <button @click="openModal(row, 'reject')" class="btn-reject">
                    <i class="pi pi-times"></i>
                    Reject
                  </button>
                </div>
              </td>

            </tr>

            <!-- EMPTY -->
            <tr v-if="list.length === 0">
              <td colspan="7">
                <div class="empty-state">
                  <div class="empty-icon"><i class="pi pi-inbox"></i></div>
                  <p class="empty-title">Tidak ada request reschedule</p>
                  <p class="empty-sub">Semua permintaan perubahan jadwal sudah diproses</p>
                </div>
              </td>
            </tr>

          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL -->
    <teleport to="body">
    <div v-if="showModal" class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="closeModal"></div>

      <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-fadeIn flex flex-col">

        <!-- HEADER MODAL -->
        <div class="px-5 py-3.5 flex items-center justify-between" :class="actionType === 'approve' ? 'bg-[#0F766E] text-white' : 'bg-rose-700 text-white'">
          <div class="flex items-center gap-2">
            <i :class="actionType === 'approve' ? 'pi pi-check text-teal-200' : 'pi pi-times text-rose-200'"></i>
            <h3 class="text-sm font-bold tracking-wide">
              {{ actionType === 'approve' ? 'Konfirmasi Approve Reschedule' : 'Konfirmasi Reject Reschedule' }}
            </h3>
          </div>
          <button @click="closeModal" class="text-white/80 hover:text-white p-1">
            <i class="pi pi-times text-xs"></i>
          </button>
        </div>

        <!-- CONTENT -->
        <div class="p-5 space-y-3 text-xs">
          <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 space-y-2">
            <div class="flex justify-between">
              <span class="text-slate-500">Nama Anak:</span>
              <span class="font-bold text-slate-900">{{ selectedRow?.registrasi?.profile_anak?.nama_anak || '-' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-500">Layanan:</span>
              <span class="font-semibold text-slate-800">{{ selectedRow?.layanan?.layanan || '-' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-500">Tanggal Lama:</span>
              <span class="font-mono text-slate-700">{{ formatDate(selectedRow?.tanggal_penjadwalan) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-500">Tanggal Baru:</span>
              <span class="font-mono font-bold text-[#0F766E]">{{ formatDate(selectedRow?.tanggal_reschedule_request) }}</span>
            </div>
          </div>
        </div>

        <!-- FOOTER -->
        <div class="p-3.5 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
          <button class="btn-secondary-clinical text-xs py-1.5 px-3" @click="closeModal">Batal</button>
          <button v-if="actionType === 'approve'" class="btn-primary-clinical text-xs py-1.5 px-4" @click="confirmApprove">
            <i class="pi pi-check text-xs"></i> Ya, Approve
          </button>
          <button v-if="actionType === 'reject'" class="bg-rose-600 hover:bg-rose-700 text-white text-xs py-1.5 px-4 rounded-lg font-medium transition cursor-pointer" @click="confirmReject">
            <i class="pi pi-times text-xs"></i> Ya, Reject
          </button>
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

const toast = useToast()

const list = ref([])

const showModal = ref(false)
const selectedRow = ref(null)
const actionType = ref('')

const loadData = async () => {
  try {

    const res = await api.get('/admin/reschedule-request')

    list.value = res.data

  } catch (err) {

    toast.error('Gagal memuat data reschedule')

    console.error(err)
  }
}

onMounted(loadData)

/* =========================
   MODAL
========================= */
const openModal = (row, type) => {
  selectedRow.value = row
  actionType.value = type
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedRow.value = null
  actionType.value = ''
}

/* =========================
   ACTIONS
========================= */
const confirmApprove = async () => {
  try {

    await api.post(
      `/pelayanan-terapi-anak/${selectedRow.value.id}/approve-reschedule`
    )

    toast.success('Reschedule disetujui')

    list.value = list.value.filter(
      item => item.id !== selectedRow.value.id
    )

    closeModal()

  } catch (err) {

    toast.error('Gagal approve')

    console.error(err)
  }
}

const confirmReject = async () => {
  try {

    await api.post(
      `/pelayanan-terapi-anak/${selectedRow.value.id}/reject-reschedule`
    )

    toast.success('Reschedule ditolak')

    list.value = list.value.filter(
      item => item.id !== selectedRow.value.id
    )

    closeModal()

  } catch (err) {

    toast.error('Gagal reject')

    console.error(err)
  }
}

/* =========================
   UTILS
========================= */
const formatDate = (dateStr) => {

  if (!dateStr) return '-'

  const d = new Date(dateStr)

  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()

  return `${day}/${month}/${year}`
}
</script>

<style scoped>
/* ===============================
   TOKENS (senada dengan Sidebar.vue, DaftarPasien.vue, RegistrasiLayananAnak.vue)
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

.stat-card {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 0.85rem;
  padding: 0.85rem 1.25rem;
  min-width: 140px;
}

.stat-label {
  font-size: 0.72rem;
  color: var(--muted);
  font-weight: 500;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--ink);
  margin-top: 0.15rem;
}

/* ===============================
   PANEL / TABLE
================================ */
.panel {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 1rem;
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
  font-size: 0.85rem;
}

.cell-strong--accent {
  color: var(--accent);
}

.cell-faint {
  font-size: 0.74rem;
  color: var(--muted);
}

.cell-faint--accent {
  color: var(--accent);
}

.date-stack {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

.tag {
  display: inline-flex;
  align-items: center;
  padding: 0.3rem 0.75rem;
  border-radius: 999px;
  font-size: 0.74rem;
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

/* ===============================
   BUTTONS
================================ */
.btn-approve {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  height: 2.2rem;
  padding: 0 0.85rem;
  border-radius: 0.55rem;
  background: var(--green);
  color: #fff;
  font-size: 0.76rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: background 0.15s ease;
}

.btn-approve:hover {
  background: #157d59;
}

.btn-reject {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  height: 2.2rem;
  padding: 0 0.85rem;
  border-radius: 0.55rem;
  background: var(--red);
  color: #fff;
  font-size: 0.76rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: background 0.15s ease;
}

.btn-reject:hover {
  background: #c23a3a;
}

.btn-secondary {
  height: 2.6rem;
  padding: 0 1.25rem;
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

.btn-confirm-approve {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  height: 2.6rem;
  padding: 0 1.25rem;
  border-radius: 0.65rem;
  background: var(--green);
  color: #fff;
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: background 0.15s ease;
}

.btn-confirm-approve:hover {
  background: #157d59;
}

.btn-confirm-reject {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  height: 2.6rem;
  padding: 0 1.25rem;
  border-radius: 0.65rem;
  background: var(--red);
  color: #fff;
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: background 0.15s ease;
}

.btn-confirm-reject:hover {
  background: #c23a3a;
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
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 1rem;
  overflow: hidden;
}

.modal-header {
  display: flex;
  align-items: flex-start;
  gap: 0.9rem;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border);
}

.modal-header-icon {
  width: 2.6rem;
  height: 2.6rem;
  border-radius: 0.7rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  flex-shrink: 0;
}

.modal-header-icon--approve {
  background: var(--green-soft);
  color: var(--green);
}

.modal-header-icon--reject {
  background: var(--red-soft);
  color: var(--red);
}

.modal-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--ink);
}

.modal-sub {
  font-size: 0.82rem;
  color: var(--muted);
  margin-top: 0.15rem;
}

.modal-close {
  width: 2rem;
  height: 2rem;
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
  padding: 1.5rem;
}

.info-card {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 0.85rem;
  padding: 1.25rem;
}

.info-row {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.5rem 0;
}

.info-row + .info-row {
  border-top: 1px solid var(--border);
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

/* ===============================
   TRANSITIONS
================================ */
.animate-modal {
  animation: modalFade 0.18s ease;
}

@keyframes modalFade {
  from {
    opacity: 0;
    transform: translateY(8px);
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