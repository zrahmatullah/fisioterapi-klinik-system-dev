<template>
  <div class="min-h-screen bg-gray-50 p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Request Reschedule</h1>
        <p class="text-sm text-gray-500">Daftar permintaan ubah jadwal dari orang tua</p>
      </div>
      <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-sm font-semibold">
        {{ list.length }} Pending
      </span>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow overflow-hidden">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-3 text-left">Anak</th>
            <th class="px-4 py-3 text-left">Layanan</th>
            <th class="px-4 py-3 text-left">Terapis</th>
            <th class="px-4 py-3 text-left">Tanggal Lama</th>
            <th class="px-4 py-3 text-left">Tanggal Baru</th>
            <th class="px-4 py-3 text-center">Aksi</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(row, i) in list"
            :key="i"
            class="border-b hover:bg-gray-50"
          >
            <td class="px-4 py-3">
              <p class="font-semibold">{{ row.registrasi?.profile_anak?.nama_anak || '-' }}</p>
              <p class="text-xs text-gray-500">{{ row.registrasi?.no_regis || '' }}</p>
            </td>

            <td class="px-4 py-3">{{ row.layanan?.layanan || '-' }}</td>
            <td class="px-4 py-3">{{ row.terapis?.nama || '-' }}</td>

            <td class="px-4 py-3">
              {{ formatDate(row.tanggal_penjadwalan) }}
            </td>

            <td class="px-4 py-3 font-semibold text-indigo-600">
              {{ formatDate(row.tanggal_reschedule_request) }}
            </td>

            <td class="px-4 py-3 text-center space-x-2">
              <button
                @click="openModal(row, 'approve')"
                class="px-3 py-1 rounded-lg bg-green-500 text-white text-xs hover:bg-green-600"
              >
                Approve
              </button>
              <button
                @click="openModal(row, 'reject')"
                class="px-3 py-1 rounded-lg bg-red-500 text-white text-xs hover:bg-red-600"
              >
                Reject
              </button>
            </td>
          </tr>

          <tr v-if="list.length === 0">
            <td colspan="6" class="py-10 text-center text-gray-400">
              Tidak ada request reschedule
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL KONFIRMASI -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-2">
          {{ actionType === 'approve' ? 'Konfirmasi Approve' : 'Konfirmasi Reject' }}
        </h3>

        <p class="text-sm text-gray-600 mb-4">
          {{
            actionType === 'approve'
              ? 'Apakah Anda yakin ingin menyetujui perubahan jadwal ini?'
              : 'Apakah Anda yakin ingin menolak permintaan reschedule ini?'
          }}
        </p>

        <div class="bg-gray-50 rounded-lg p-3 mb-4 text-sm">
          <p><b>Anak:</b> {{ selectedRow?.registrasi?.profile_anak?.nama_anak || '-' }}</p>
          <p><b>Layanan:</b> {{ selectedRow?.layanan?.layanan || '-' }}</p>
          <p><b>Tanggal Lama:</b> {{ formatDate(selectedRow?.tanggal_penjadwalan) }}</p>
          <p><b>Tanggal Baru:</b> {{ formatDate(selectedRow?.tanggal_reschedule_request) }}</p>
        </div>

        <div class="flex justify-end gap-3">
          <button
            class="px-4 py-2 rounded-lg bg-gray-200"
            @click="closeModal"
          >
            Batal
          </button>

          <button
            v-if="actionType === 'approve'"
            class="px-4 py-2 rounded-lg bg-green-600 text-white"
            @click="confirmApprove"
          >
            Ya, Approve
          </button>

          <button
            v-if="actionType === 'reject'"
            class="px-4 py-2 rounded-lg bg-red-600 text-white"
            @click="confirmReject"
          >
            Ya, Reject
          </button>
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
const actionType = ref('') // approve | reject

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

/* =====================
   MODAL HANDLER
===================== */
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

/* =====================
   ACTIONS
===================== */
const confirmApprove = async () => {
  try {
    await api.post(`/pelayanan-terapi-anak/${selectedRow.value.id}/approve-reschedule`)
    toast.success('Reschedule disetujui')

    list.value = list.value.filter(item => item.id !== selectedRow.value.id)
    closeModal()
  } catch (err) {
    toast.error('Gagal approve')
    console.error(err)
  }
}

const confirmReject = async () => {
  try {
    await api.post(`/pelayanan-terapi-anak/${selectedRow.value.id}/reject-reschedule`)
    toast.success('Reschedule ditolak')

    list.value = list.value.filter(item => item.id !== selectedRow.value.id)
    closeModal()
  } catch (err) {
    toast.error('Gagal reject')
    console.error(err)
  }
}

/* =====================
   UTILS
===================== */
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
tr {
  transition: background 0.2s ease;
}
</style>
