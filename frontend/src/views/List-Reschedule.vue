<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-indigo-50 p-6 space-y-6">

    <!-- HEADER -->
    <div
      class="relative overflow-hidden rounded-3xl
            bg-gradient-to-r from-amber-500 via-orange-500 to-red-500
            p-7 shadow-xl"
    >
      <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
          <h1 class="text-3xl font-bold text-white">
            🔄 Request Reschedule
          </h1>

          <p class="text-white/80 mt-2">
            Daftar permintaan perubahan jadwal terapi dari orang tua
          </p>
        </div>

        <div
          class="bg-white/20 backdrop-blur-md
                px-5 py-3 rounded-2xl border border-white/20"
        >
          <p class="text-sm text-white/80">
            Pending Request
          </p>

          <h2 class="text-3xl font-bold text-white">
            {{ list.length }}
          </h2>
        </div>
      </div>

      <div
        class="absolute right-0 top-0 w-72 h-72 bg-white/10 rounded-full blur-3xl"
      />
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">

      <div class="overflow-x-auto">

        <table class="w-full text-sm">

          <thead class="bg-slate-100 sticky top-0 z-10">
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

            <tr
              v-for="(row, i) in list"
              :key="i"
              class="border-b hover:bg-orange-50/50 transition duration-200"
            >

              <!-- ANAK -->
              <td class="td">

                <div class="flex items-center gap-3">

                  <div
                    class="w-11 h-11 rounded-2xl
                          bg-gradient-to-r from-orange-400 to-red-500
                          text-white flex items-center justify-center
                          font-bold shadow"
                  >
                    {{
                      row.registrasi?.profile_anak?.nama_anak?.charAt(0) || '?'
                    }}
                  </div>

                  <div>
                    <p class="font-semibold text-gray-800">
                      {{ row.registrasi?.profile_anak?.nama_anak || '-' }}
                    </p>

                    <p class="text-xs text-gray-500 mt-1">
                      {{ row.registrasi?.no_regis || '-' }}
                    </p>
                  </div>

                </div>

              </td>

              <!-- LAYANAN -->
              <td class="td">
                <span class="badge-service">
                  {{ row.layanan?.layanan || '-' }}
                </span>
              </td>

              <!-- TERAPIS -->
              <td class="td">

                <div class="flex items-center gap-2">

                  <div
                    class="w-8 h-8 rounded-full bg-indigo-100
                          flex items-center justify-center text-indigo-600 text-xs font-bold"
                  >
                    👩‍⚕️
                  </div>

                  <span class="font-medium text-gray-700">
                    {{ row.terapis?.nama || '-' }}
                  </span>

                </div>

              </td>

              <!-- TANGGAL LAMA -->
              <td class="td">

                <div class="flex flex-col">
                  <span class="text-gray-400 text-xs">
                    Jadwal Lama
                  </span>

                  <span class="font-medium text-gray-700">
                    {{ formatDate(row.tanggal_penjadwalan) }}
                  </span>
                </div>

              </td>

              <!-- TANGGAL BARU -->
              <td class="td">

                <div class="flex flex-col">
                  <span class="text-xs text-indigo-500">
                    Request Baru
                  </span>

                  <span class="font-bold text-indigo-600">
                    {{ formatDate(row.tanggal_reschedule_request) }}
                  </span>
                </div>

              </td>

              <!-- STATUS -->
              <td class="td text-center">

                <span class="badge-pending">
                  Pending
                </span>

              </td>

              <!-- AKSI -->
              <td class="td">

                <div class="flex justify-center gap-2">

                  <button
                    @click="openModal(row, 'approve')"
                    class="btn-approve"
                  >
                    ✓ Approve
                  </button>

                  <button
                    @click="openModal(row, 'reject')"
                    class="btn-reject"
                  >
                    ✕ Reject
                  </button>

                </div>

              </td>

            </tr>

            <!-- EMPTY -->
            <tr v-if="list.length === 0">

              <td colspan="7">

                <div class="flex flex-col items-center py-16">

                  <div class="text-6xl mb-4">
                    📭
                  </div>

                  <p class="text-lg font-semibold text-gray-600">
                    Tidak ada request reschedule
                  </p>

                  <p class="text-sm text-gray-400 mt-2">
                    Semua permintaan perubahan jadwal sudah diproses
                  </p>

                </div>

              </td>

            </tr>

          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm
            flex items-center justify-center z-50 p-4"
    >

      <div
        class="bg-white rounded-3xl w-full max-w-lg
              shadow-2xl border border-gray-100
              overflow-hidden animate-modal"
      >

        <!-- HEADER MODAL -->
        <div
          :class="[
            'p-5 text-white',
            actionType === 'approve'
              ? 'bg-gradient-to-r from-green-500 to-emerald-600'
              : 'bg-gradient-to-r from-red-500 to-rose-600'
          ]"
        >

          <div class="flex justify-between items-center">

            <div>
              <h3 class="text-xl font-bold">

                {{
                  actionType === 'approve'
                    ? '✅ Konfirmasi Approve'
                    : '❌ Konfirmasi Reject'
                }}

              </h3>

              <p class="text-white/80 text-sm mt-1">

                {{
                  actionType === 'approve'
                    ? 'Setujui perubahan jadwal terapi'
                    : 'Tolak permintaan perubahan jadwal'
                }}

              </p>
            </div>

            <button
              @click="closeModal"
              class="text-white/80 hover:text-white text-xl"
            >
              ✖
            </button>

          </div>
        </div>

        <!-- CONTENT -->
        <div class="p-6">

          <div
            class="bg-gray-50 rounded-2xl p-5 border border-gray-100"
          >

            <div class="space-y-4">

              <div class="flex justify-between gap-4">
                <span class="text-gray-500">Nama Anak</span>

                <span class="font-semibold text-right">
                  {{ selectedRow?.registrasi?.profile_anak?.nama_anak || '-' }}
                </span>
              </div>

              <div class="flex justify-between gap-4">
                <span class="text-gray-500">Layanan</span>

                <span class="font-semibold text-right">
                  {{ selectedRow?.layanan?.layanan || '-' }}
                </span>
              </div>

              <div class="flex justify-between gap-4">
                <span class="text-gray-500">Tanggal Lama</span>

                <span class="font-semibold text-right">
                  {{ formatDate(selectedRow?.tanggal_penjadwalan) }}
                </span>
              </div>

              <div class="flex justify-between gap-4">
                <span class="text-gray-500">Tanggal Baru</span>

                <span class="font-bold text-indigo-600 text-right">
                  {{ formatDate(selectedRow?.tanggal_reschedule_request) }}
                </span>
              </div>

            </div>

          </div>

          <!-- FOOTER -->
          <div class="flex justify-end gap-3 mt-6">

            <button
              class="btn-cancel"
              @click="closeModal"
            >
              Batal
            </button>

            <button
              v-if="actionType === 'approve'"
              class="btn-confirm-approve"
              @click="confirmApprove"
            >
              ✓ Ya, Approve
            </button>

            <button
              v-if="actionType === 'reject'"
              class="btn-confirm-reject"
              @click="confirmReject"
            >
              ✕ Ya, Reject
            </button>

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
.th {
  @apply px-5 py-4 text-left font-semibold text-gray-600 whitespace-nowrap;
}

.td {
  @apply px-5 py-4 whitespace-nowrap;
}

.badge-service {
  @apply bg-indigo-100 text-indigo-700
  px-3 py-1 rounded-full text-xs font-semibold;
}

.badge-pending {
  @apply bg-amber-100 text-amber-700
  px-3 py-1 rounded-full text-xs font-semibold;
}

.btn-approve {
  @apply bg-green-500 text-white px-4 py-2 rounded-xl
  text-xs font-semibold hover:bg-green-600
  transition shadow-sm;
}

.btn-reject {
  @apply bg-red-500 text-white px-4 py-2 rounded-xl
  text-xs font-semibold hover:bg-red-600
  transition shadow-sm;
}

.btn-cancel {
  @apply px-5 py-2 rounded-xl bg-gray-100
  hover:bg-gray-200 transition;
}

.btn-confirm-approve {
  @apply px-5 py-2 rounded-xl bg-green-600
  text-white hover:bg-green-700 transition;
}

.btn-confirm-reject {
  @apply px-5 py-2 rounded-xl bg-red-600
  text-white hover:bg-red-700 transition;
}

.animate-modal {
  animation: modalFade .2s ease;
}

@keyframes modalFade {
  from {
    opacity: 0;
    transform: scale(.96);
  }

  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>