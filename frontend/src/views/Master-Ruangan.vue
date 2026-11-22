<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-building"></i>
        </div>
        <div>
          <h1 class="text-[15px] font-bold text-[#0F172A] leading-tight">Master Ruangan Terapi</h1>
          <p class="text-[11.5px] text-[#64748B] mt-0.5">Kelola ruangan praktik terapi, kapasitas, dan status ketersediaan</p>
        </div>
      </div>

      <button
        type="button"
        @click="openAdd"
        class="btn-primary-clinical text-[12.5px] shrink-0"
      >
        <i class="pi pi-plus text-xs"></i>
        <span>Tambah Ruangan</span>
      </button>
    </div>

    <!-- MAIN DATA TABLE CARD -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
      <!-- TABLE TOOLBAR -->
      <div class="shrink-0 p-3 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2.5 bg-white">
        <div class="relative w-full sm:w-72">
          <i class="pi pi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
          <input
            v-model="search"
            type="text"
            placeholder="Cari nama ruangan..."
            class="w-full h-[34px] text-[12.5px] pl-8 pr-2.5 border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 transition bg-white text-slate-800 placeholder:text-slate-400"
          />
        </div>
        <span class="text-[11.5px] text-slate-500 font-medium">
          Total <strong>{{ filteredList.length }}</strong> ruangan tercatat
        </span>
      </div>

      <!-- TABLE BODY -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="min-w-full text-[12.5px] text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2 px-3 text-center w-12">No</th>
              <th class="py-2 px-3">Nama Ruangan Terapi</th>
              <th class="py-2 px-3 text-center">Status</th>
              <th class="py-2 px-3 text-center w-24">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(item, index) in filteredList"
              :key="item.id"
              class="hover:bg-[#F0F6F8]/60 transition-colors"
            >
              <td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]">{{ index + 1 }}</td>
              <td class="py-2 px-3 font-semibold text-slate-900">{{ item.ruangan }}</td>
              <td class="py-2 px-3 text-center">
                <StatusBadge
                  :label="item.status_aktif ? 'Aktif' : 'Non Aktif'"
                  :type="item.status_aktif ? 'success' : 'neutral'"
                />
              </td>
              <td class="py-2 px-3 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    type="button"
                    @click="openEdit(item)"
                    class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 flex items-center justify-center transition shadow-2xs"
                    title="Edit Ruangan"
                  >
                    <i class="pi pi-pencil text-[11px]"></i>
                  </button>
                  <button
                    type="button"
                    @click="confirmDeleteAction(item)"
                    class="w-7 h-7 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition shadow-2xs"
                    title="Hapus"
                  >
                    <i class="pi pi-trash text-[11px]"></i>
                  </button>
                </div>
              </td>
            </tr>

            <!-- EMPTY -->
            <tr v-if="filteredList.length === 0">
              <td colspan="4" class="py-12 text-center text-slate-400">
                <div class="flex flex-col items-center justify-center">
                  <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                    <i class="pi pi-folder-open text-lg"></i>
                  </div>
                  <h4 class="text-[13px] font-semibold text-slate-800">Ruangan Tidak Ditemukan</h4>
                  <p class="text-[11.5px] text-slate-500 mt-0.5">Belum ada data ruangan atau tidak sesuai pencarian.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- TABLE FOOTER -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] text-slate-500 flex items-center justify-between">
        <span>Menampilkan {{ filteredList.length }} ruangan</span>
        <span>SIMRS Fasilitas Ruangan</span>
      </div>
    </div>

    <!-- MODAL FORM (COMPACT TIER 'sm': max-w-[420px]) -->
    <BaseModal
      v-model="dialog"
      :title="isEdit ? 'Edit Ruangan' : 'Tambah Ruangan'"
      :subtitle="isEdit ? 'Perbarui informasi ruangan terapi' : 'Tambah ruangan terapi baru ke sistem'"
      icon="pi pi-building"
      size="sm"
      :loading="saving"
      @submit="save"
    >
      <form @submit.prevent="save" class="space-y-3">
        <div>
          <label class="label-clinical">Nama Ruangan <span class="text-rose-500">*</span></label>
          <input
            v-model="form.ruangan"
            required
            class="input-clinical"
            placeholder="mis. Ruang Terapi Wicara 01"
          />
        </div>

        <div>
          <label class="label-clinical">Status Ruangan</label>
          <select v-model="form.status_aktif" class="input-clinical">
            <option :value="true">Aktif</option>
            <option :value="false">Non Aktif</option>
          </select>
        </div>
      </form>
    </BaseModal>

    <!-- CONFIRM DELETE DIALOG -->
    <ConfirmDialog
      v-model="confirmDeleteModal"
      title="Hapus Ruangan"
      :message="`Apakah Anda yakin ingin menghapus ruangan '${itemToDelete?.ruangan}'?`"
      type="danger"
      confirmText="Ya, Hapus"
      :loading="deleting"
      @confirm="executeDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'
import BaseModal from '../components/ui/BaseModal.vue'
import ConfirmDialog from '../components/ui/ConfirmDialog.vue'
import StatusBadge from '../components/ui/StatusBadge.vue'

const toast = useToast()
const ruanganList = ref([])
const search = ref('')
const loading = ref(false)
const dialog = ref(false)
const isEdit = ref(false)
const saving = ref(false)

const confirmDeleteModal = ref(false)
const itemToDelete = ref(null)
const deleting = ref(false)

const form = ref({ id: null, ruangan: '', status_aktif: true })

onMounted(loadData)

async function loadData() {
  loading.value = true
  try {
    const res = await api.get('/ruangan')
    ruanganList.value = res.data.data ?? res.data
  } catch (err) {
    toast.error('Gagal mengambil data ruangan')
  } finally {
    loading.value = false
  }
}

const filteredList = computed(() => {
  if (!search.value) return ruanganList.value
  const q = search.value.toLowerCase()
  return ruanganList.value.filter(r => (r.ruangan || '').toLowerCase().includes(q))
})

const openAdd = () => {
  isEdit.value = false
  form.value = { id: null, ruangan: '', status_aktif: true }
  dialog.value = true
}

const openEdit = (data) => {
  isEdit.value = true
  form.value = { ...data }
  dialog.value = true
}

const save = async () => {
  if (!form.value.ruangan) {
    toast.warning('Nama ruangan wajib diisi')
    return
  }

  saving.value = true
  try {
    if (isEdit.value) {
      await api.put(`/ruangan/${form.value.id}`, form.value)
      toast.success('Ruangan berhasil diupdate')
    } else {
      await api.post('/ruangan', form.value)
      toast.success('Ruangan berhasil ditambahkan')
    }
    dialog.value = false
    loadData()
  } catch (err) {
    toast.error('Gagal menyimpan ruangan')
  } finally {
    saving.value = false
  }
}

const confirmDeleteAction = (data) => {
  itemToDelete.value = data
  confirmDeleteModal.value = true
}

const executeDelete = async () => {
  if (!itemToDelete.value) return
  deleting.value = true
  try {
    await api.delete(`/ruangan/${itemToDelete.value.id}`)
    toast.success('Ruangan berhasil dihapus')
    confirmDeleteModal.value = false
    loadData()
  } catch (err) {
    toast.error('Gagal menghapus ruangan')
  } finally {
    deleting.value = false
  }
}
</script>
