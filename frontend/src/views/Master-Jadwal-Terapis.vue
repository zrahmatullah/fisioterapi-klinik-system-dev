<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-calendar"></i>
        </div>
        <div>
          <h1 class="text-[15px] font-bold text-[#0F172A] leading-tight">Master Jadwal & Penugasan Terapis</h1>
          <p class="text-[11.5px] text-[#64748B] mt-0.5">Konfigurasi slot master hari/jam serta penugasan jadwal kerja tenaga medis</p>
        </div>
      </div>

      <!-- TAB SELECTION -->
      <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-lg shrink-0">
        <button
          type="button"
          @click="activeTab = 'master'"
          class="h-7 px-3 rounded-md text-[12px] font-semibold transition cursor-pointer"
          :class="activeTab === 'master' ? 'bg-white text-[#0F766E] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
        >
          Master Slot Waktu
        </button>
        <button
          type="button"
          @click="activeTab = 'user'"
          class="h-7 px-3 rounded-md text-[12px] font-semibold transition cursor-pointer"
          :class="activeTab === 'user' ? 'bg-white text-[#0F766E] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
        >
          Penugasan Terapis
        </button>
      </div>
    </div>

    <!-- TAB 1: MASTER JADWAL -->
    <div v-if="activeTab === 'master'" class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
      <div class="shrink-0 p-3 border-b border-slate-200 flex justify-between items-center bg-white">
        <span class="text-[12px] font-bold text-slate-800 uppercase tracking-wider">Slot Waktu Hari & Jam Praktik</span>
        <button
          type="button"
          @click="openMasterCreate"
          class="btn-primary-clinical text-[12px]"
        >
          <i class="pi pi-plus text-xs"></i>
          <span>Tambah Slot Jadwal</span>
        </button>
      </div>

      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="min-w-full text-[12.5px] text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2 px-3 text-center w-12">No</th>
              <th class="py-2 px-3">Hari Praktik</th>
              <th class="py-2 px-3">Rentang Jam</th>
              <th class="py-2 px-3 text-center">Status</th>
              <th class="py-2 px-3 text-center w-24">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(j, idx) in jadwalMaster"
              :key="j.id || idx"
              class="hover:bg-[#F0F6F8]/60 transition-colors"
            >
              <td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]">{{ idx + 1 }}</td>
              <td class="py-2 px-3 font-semibold text-slate-900">{{ hariLabel(j.hari) }}</td>
              <td class="py-2 px-3 font-mono text-[12px] tabular-nums text-slate-800">
                {{ j.jam_mulai }} - {{ j.jam_selesai }}
              </td>
              <td class="py-2 px-3 text-center">
                <StatusBadge
                  :label="j.status_aktif ? 'Aktif' : 'Nonaktif'"
                  :type="j.status_aktif ? 'success' : 'neutral'"
                />
              </td>
              <td class="py-2 px-3 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    type="button"
                    @click="openMasterEdit(j)"
                    class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center transition shadow-2xs"
                    title="Edit Slot"
                  >
                    <i class="pi pi-pencil text-[11px]"></i>
                  </button>
                  <button
                    type="button"
                    @click="deleteMaster(j.id)"
                    class="w-7 h-7 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition shadow-2xs"
                    title="Hapus"
                  >
                    <i class="pi pi-trash text-[11px]"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 2: JADWAL TERAPIS -->
    <div v-if="activeTab === 'user'" class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden">
      <!-- PILIH TERAPIS CARD -->
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex items-center gap-3 shrink-0">
        <label class="text-[12px] font-semibold text-slate-700 shrink-0">Pilih Tenaga Terapis:</label>
        <div class="w-72">
          <select v-model="selectedUser" @change="fetchJadwalUser" class="input-clinical">
            <option value="">-- Pilih Terapis --</option>
            <option v-for="u in terapis" :key="u.id" :value="u.id">
              {{ u.nama }}
            </option>
          </select>
        </div>
      </div>

      <!-- ATUR JADWAL CONTENT -->
      <div v-if="selectedUser" class="flex-1 min-h-0 grid grid-cols-1 md:grid-cols-2 gap-3 overflow-hidden">
        <!-- CURRENT SCHEDULE -->
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col overflow-hidden">
          <h3 class="text-[13px] font-bold text-slate-900 pb-2 border-b border-slate-100 shrink-0">
            Jadwal Terapis Saat Ini
          </h3>
          <div class="flex-1 min-h-0 overflow-auto custom-scrollbar pt-2 space-y-1.5">
            <div
              v-for="j in jadwalUserDetail"
              :key="j.id"
              class="bg-[#F0F6F8] border border-[#DCE8EC] rounded-lg p-2.5 flex items-center justify-between"
            >
              <div>
                <span class="font-bold text-slate-900 text-xs block">{{ hariLabel(j.jadwal_master.hari) }}</span>
                <span class="text-[11px] text-slate-500 font-mono">{{ j.jadwal_master.jam_mulai }} - {{ j.jadwal_master.jam_selesai }}</span>
              </div>
              <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-teal-50 text-[#0F766E] border border-teal-200">
                Ditugaskan
              </span>
            </div>
            <p v-if="!jadwalUserDetail.length" class="text-xs text-slate-400 italic py-4 text-center">
              Belum ada slot waktu yang ditugaskan.
            </p>
          </div>
        </div>

        <!-- SYNC / EDIT SCHEDULE -->
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col overflow-hidden">
          <div class="flex items-center justify-between pb-2 border-b border-slate-100 shrink-0">
            <h3 class="text-[13px] font-bold text-slate-900">Pilih Slot Jadwal Praktik</h3>
            <button
              type="button"
              @click="saveJadwalUser"
              class="btn-primary-clinical text-xs"
            >
              <i class="pi pi-check text-xs"></i>
              <span>Simpan Sinkronisasi</span>
            </button>
          </div>

          <div class="flex-1 min-h-0 overflow-auto custom-scrollbar pt-2 space-y-2">
            <label
              v-for="j in jadwalMaster"
              :key="j.id"
              class="flex items-center gap-3 p-2 rounded-lg border border-slate-200 hover:bg-[#F0F6F8]/60 transition cursor-pointer"
            >
              <input type="checkbox" :value="j.id" v-model="jadwalUser" class="w-4 h-4 text-[#0F766E] rounded" />
              <div>
                <div class="font-bold text-slate-900 text-xs">{{ hariLabel(j.hari) }}</div>
                <div class="text-[11px] text-slate-500 font-mono">{{ j.jam_mulai }} - {{ j.jam_selesai }}</div>
              </div>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL FORM MASTER JADWAL (COMPACT TIER 'sm': max-w-[420px]) -->
    <BaseModal
      v-model="showMasterModal"
      :title="editMasterId ? 'Edit Slot Jadwal' : 'Tambah Slot Jadwal'"
      :subtitle="editMasterId ? 'Perbarui jam dan hari slot master' : 'Buat slot waktu operasional baru'"
      icon="pi pi-calendar"
      size="sm"
      @submit="saveMaster"
    >
      <form @submit.prevent="saveMaster" class="space-y-3">
        <div>
          <label class="label-clinical">Hari Praktik <span class="text-rose-500">*</span></label>
          <select v-model="masterForm.hari" required class="input-clinical">
            <option v-for="(h, i) in hariList" :key="i" :value="i">{{ h }}</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label-clinical">Jam Mulai <span class="text-rose-500">*</span></label>
            <input type="time" v-model="masterForm.jam_mulai" required class="input-clinical" />
          </div>
          <div>
            <label class="label-clinical">Jam Selesai <span class="text-rose-500">*</span></label>
            <input type="time" v-model="masterForm.jam_selesai" required class="input-clinical" />
          </div>
        </div>

        <div>
          <label class="label-clinical">Status Slot</label>
          <select v-model="masterForm.status_aktif" class="input-clinical">
            <option :value="true">Aktif</option>
            <option :value="false">Nonaktif</option>
          </select>
        </div>
      </form>
    </BaseModal>

    <!-- CONFIRM DELETE DIALOG -->
    <ConfirmDialog
      v-model="confirmDeleteModal"
      title="Hapus Jadwal Master"
      message="Apakah Anda yakin ingin menghapus slot jadwal master ini?"
      confirmText="Ya, Hapus Jadwal"
      type="danger"
      @confirm="executeDeleteMaster"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'
import BaseModal from '../components/ui/BaseModal.vue'
import ConfirmDialog from '../components/ui/ConfirmDialog.vue'
import StatusBadge from '../components/ui/StatusBadge.vue'

const toast = useToast()

const activeTab = ref('master')
const jadwalMaster = ref([])
const terapis = ref([])
const selectedUser = ref('')
const jadwalUser = ref([])
const jadwalUserDetail = ref([])

const confirmDeleteModal = ref(false)
const selectedScheduleIdToDelete = ref(null)

const showMasterModal = ref(false)
const editMasterId = ref(null)

const masterForm = ref({ hari: 1, jam_mulai: '', jam_selesai: '', status_aktif: true })
const hariList = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
const hariLabel = (h) => hariList[h]

const fetchMaster = async () => {
  try {
    const r = await api.get('/jadwal-master')
    jadwalMaster.value = r.data.data ?? r.data
  } catch (e) {
    console.error(e)
  }
}

const fetchTerapis = async () => {
  try {
    const r = await api.get('/user-profile?jenis_user_id=9')
    terapis.value = r.data.data ?? r.data
  } catch (e) {
    console.error(e)
  }
}

const fetchJadwalUser = async () => {
  if (!selectedUser.value) return
  try {
    const r = await api.get(`/jadwal-user?user_profile_id=${selectedUser.value}`)
    const d = r.data.data ?? r.data
    jadwalUser.value = d.map((j) => j.jadwal_master_id)
    jadwalUserDetail.value = d
  } catch (e) {
    console.error(e)
  }
}

const saveJadwalUser = async () => {
  try {
    await api.post('/jadwal-user/sync', {
      user_profile_id: selectedUser.value,
      jadwal_master_id: jadwalUser.value
    })
    toast.success('Jadwal terapis berhasil disinkronkan')
    fetchJadwalUser()
  } catch (e) {
    toast.error('Gagal sinkronisasi jadwal')
  }
}

const openMasterCreate = () => {
  editMasterId.value = null
  masterForm.value = { hari: 1, jam_mulai: '08:00', jam_selesai: '09:00', status_aktif: true }
  showMasterModal.value = true
}

const openMasterEdit = (j) => {
  editMasterId.value = j.id
  masterForm.value = { ...j }
  showMasterModal.value = true
}

const saveMaster = async () => {
  try {
    if (editMasterId.value) {
      await api.put(`/jadwal-master/${editMasterId.value}`, masterForm.value)
      toast.success('Jadwal berhasil diupdate')
    } else {
      await api.post('/jadwal-master', masterForm.value)
      toast.success('Jadwal berhasil ditambahkan')
    }
    showMasterModal.value = false
    fetchMaster()
  } catch (e) {
    toast.error('Gagal menyimpan jadwal')
  }
}

const deleteMaster = (id) => {
  selectedScheduleIdToDelete.value = id
  confirmDeleteModal.value = true
}

const executeDeleteMaster = async () => {
  if (!selectedScheduleIdToDelete.value) return
  try {
    await api.delete(`/jadwal-master/${selectedScheduleIdToDelete.value}`)
    toast.success('Jadwal berhasil dihapus')
    confirmDeleteModal.value = false
    fetchMaster()
  } catch (e) {
    toast.error('Gagal menghapus jadwal')
  }
}

onMounted(() => {
  fetchMaster()
  fetchTerapis()
})
</script>
