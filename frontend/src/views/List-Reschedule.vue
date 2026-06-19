<template>
  <div class="page">

    <!-- HEADER -->
    <div class="page-header">
      <div class="header-left">
        <div class="header-icon">
          <i class="pi pi-calendar-times"></i>
        </div>

        <div>
          <h1 class="page-title">Request Reschedule</h1>
          <p class="page-sub">Daftar permintaan perubahan jadwal terapi dari orang tua</p>
        </div>
      </div>

      <div class="stat-card">
        <p class="stat-label">Pending Request</p>
        <h2 class="stat-value">{{ list.length }}</h2>
      </div>
    </div>

    <!-- TABLE -->
    <div class="panel table-panel">

      <div class="table-scroll">
        <table class="data-table">

          <thead>
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
    <div v-if="showModal" class="modal-overlay">
      <div class="modal-backdrop" @click="closeModal"></div>

      <div class="modal-wrapper">
        <div class="modal-card animate-modal">

          <!-- HEADER MODAL -->
          <div class="modal-header" :class="actionType === 'approve' ? 'modal-header--approve' : 'modal-header--reject'">

            <div class="modal-header-icon" :class="actionType === 'approve' ? 'modal-header-icon--approve' : 'modal-header-icon--reject'">
              <i :class="actionType === 'approve' ? 'pi pi-check' : 'pi pi-times'"></i>
            </div>

            <div class="flex-1">
              <h3 class="modal-title">
                {{ actionType === 'approve' ? 'Konfirmasi approve' : 'Konfirmasi reject' }}
              </h3>

              <p class="modal-sub">
                {{ actionType === 'approve' ? 'Setujui perubahan jadwal terapi' : 'Tolak permintaan perubahan jadwal' }}
              </p>
            </div>

            <button @click="closeModal" class="modal-close">
              <i class="pi pi-times"></i>
            </button>
          </div>

          <!-- CONTENT -->
          <div class="modal-content">

            <div class="info-card">
              <div class="info-row">
                <span class="cell-faint">Nama Anak</span>
                <span class="cell-strong">{{ selectedRow?.registrasi?.profile_anak?.nama_anak || '-' }}</span>
              </div>

              <div class="info-row">
                <span class="cell-faint">Layanan</span>
                <span class="cell-strong">{{ selectedRow?.layanan?.layanan || '-' }}</span>
              </div>

              <div class="info-row">
                <span class="cell-faint">Tanggal Lama</span>
                <span class="cell-strong">{{ formatDate(selectedRow?.tanggal_penjadwalan) }}</span>
              </div>

              <div class="info-row">
                <span class="cell-faint">Tanggal Baru</span>
                <span class="cell-strong cell-strong--accent">{{ formatDate(selectedRow?.tanggal_reschedule_request) }}</span>
              </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
              <button class="btn-secondary" @click="closeModal">Batal</button>

              <button v-if="actionType === 'approve'" class="btn-confirm-approve" @click="confirmApprove">
                <i class="pi pi-check"></i>
                Ya, approve
              </button>

              <button v-if="actionType === 'reject'" class="btn-confirm-reject" @click="confirmReject">
                <i class="pi pi-times"></i>
                Ya, reject
              </button>
            </div>

          </div>

        </div>
      </div>
    </div>

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
  --accent: #6D5CE0;
  --accent-soft: #F1EEFC;

  --amber: #B98900;
  --amber-soft: #FBF3DB;
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