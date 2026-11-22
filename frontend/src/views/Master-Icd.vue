<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-book"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Master ICD-10 & ICD-9-CM</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Klasifikasi standar diagnosis medis (ICD-10) dan prosedur tindakan terapi anak (ICD-9-CM) standar Kemenkes</p>
        </div>
      </div>

      <div class="flex items-center gap-2 shrink-0">
        <button
          v-if="activeTab === 'icd10'"
          type="button"
          @click="openAddIcd10"
          class="btn-primary-clinical text-xs py-2 px-3.5"
        >
          <i class="pi pi-plus text-xs"></i>
          <span>Tambah Diagnosa ICD-10</span>
        </button>
        <button
          v-else
          type="button"
          @click="openAddIcd9"
          class="btn-primary-clinical text-xs py-2 px-3.5"
        >
          <i class="pi pi-plus text-xs"></i>
          <span>Tambah Prosedur ICD-9</span>
        </button>
      </div>
    </div>

    <!-- TABS -->
    <div class="flex border-b border-slate-200 bg-white px-4 rounded-xl shadow-sm gap-4 text-xs font-semibold shrink-0">
      <button
        type="button"
        @click="activeTab = 'icd10'"
        class="py-2.5 transition flex items-center gap-1.5 cursor-pointer"
        :class="activeTab === 'icd10' ? 'border-b-2 border-[#0F766E] text-[#0F766E]' : 'text-slate-500 hover:text-slate-700'"
      >
        <i class="pi pi-bookmark text-xs"></i>
        <span>1. ICD-10 (Diagnosa Klinis Anak)</span>
      </button>

      <button
        type="button"
        @click="activeTab = 'icd9'"
        class="py-2.5 transition flex items-center gap-1.5 cursor-pointer"
        :class="activeTab === 'icd9' ? 'border-b-2 border-[#0F766E] text-[#0F766E]' : 'text-slate-500 hover:text-slate-700'"
      >
        <i class="pi pi-cog text-xs"></i>
        <span>2. ICD-9-CM (Prosedur Tindakan Terapi)</span>
      </button>
    </div>

    <!-- TAB 1: ICD-10 -->
    <div v-if="activeTab === 'icd10'" class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">
      <!-- Toolbar -->
      <div class="p-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shrink-0">
        <div class="relative w-80">
          <i class="pi pi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
          <input
            v-model="searchIcd10Query"
            type="text"
            placeholder="Cari kode (mis. F80.1) atau nama diagnosa..."
            class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
            @input="fetchIcd10"
          />
        </div>
        <span class="text-xs text-slate-500 font-medium">
          Total <strong>{{ icd10List.length }}</strong> kode ICD-10 termuat
        </span>
      </div>

      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-[12.5px] text-left text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3 w-24">Kode ICD</th>
              <th class="py-2.5 px-3">Deskripsi Bahasa Indonesia</th>
              <th class="py-2.5 px-3">Deskripsi Internasional (EN)</th>
              <th class="py-2.5 px-3">Kategori</th>
              <th class="py-2.5 px-3 text-center">Spesifik Anak</th>
              <th class="py-2.5 px-3 text-right w-24">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loadingIcd10" class="text-center">
              <td colspan="6" class="py-10 text-slate-400">
                <i class="pi pi-spin pi-spinner text-xl text-[#0F766E]"></i>
                <p class="mt-2 text-xs">Memuat master ICD-10...</p>
              </td>
            </tr>
            <tr v-else-if="icd10List.length === 0" class="text-center">
              <td colspan="6" class="py-8 text-slate-400 text-xs">Tidak ada data ICD-10 yang cocok.</td>
            </tr>
            <tr v-for="item in icd10List" :key="item.id" class="hover:bg-[#F0F6F8]/60 transition">
              <td class="py-2 px-3 font-mono font-bold text-[#0F766E] text-xs">{{ item.kode }}</td>
              <td class="py-2 px-3 font-semibold text-slate-900">{{ item.deskripsi_id }}</td>
              <td class="py-2 px-3 text-slate-500 text-xs italic">{{ item.deskripsi_en }}</td>
              <td class="py-2 px-3 text-xs text-slate-600">{{ item.kategori || 'Pediatrik' }}</td>
              <td class="py-2 px-3 text-center">
                <StatusBadge status="Pediatrik" />
              </td>
              <td class="py-2 px-3 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button @click="openEditIcd10(item)" class="w-7 h-7 rounded flex items-center justify-center text-slate-500 hover:text-[#0F766E] hover:bg-[#E6F2F5] transition cursor-pointer" title="Edit">
                    <i class="pi pi-pencil text-xs"></i>
                  </button>
                  <button @click="confirmDeleteIcd10(item)" class="w-7 h-7 rounded flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                    <i class="pi pi-trash text-xs"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 2: ICD-9-CM -->
    <div v-else class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">
      <!-- Toolbar -->
      <div class="p-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shrink-0">
        <div class="relative w-80">
          <i class="pi pi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
          <input
            v-model="searchIcd9Query"
            type="text"
            placeholder="Cari kode (mis. 93.53) atau nama prosedur..."
            class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
            @input="fetchIcd9"
          />
        </div>
        <span class="text-xs text-slate-500 font-medium">
          Total <strong>{{ icd9List.length }}</strong> kode prosedur ICD-9-CM
        </span>
      </div>

      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-[12.5px] text-left text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3 w-24">Kode ICD-9</th>
              <th class="py-2.5 px-3">Prosedur Medis (Indonesia)</th>
              <th class="py-2.5 px-3">Prosedur Medis (English)</th>
              <th class="py-2.5 px-3 text-center">Spesifik Anak</th>
              <th class="py-2.5 px-3 text-right w-24">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loadingIcd9" class="text-center">
              <td colspan="5" class="py-10 text-slate-400">
                <i class="pi pi-spin pi-spinner text-xl text-[#0F766E]"></i>
                <p class="mt-2 text-xs">Memuat master ICD-9-CM...</p>
              </td>
            </tr>
            <tr v-else-if="icd9List.length === 0" class="text-center">
              <td colspan="5" class="py-8 text-slate-400 text-xs">Tidak ada data ICD-9 yang cocok.</td>
            </tr>
            <tr v-for="item in icd9List" :key="item.id" class="hover:bg-[#F0F6F8]/60 transition">
              <td class="py-2 px-3 font-mono font-bold text-[#0F766E] text-xs">{{ item.kode }}</td>
              <td class="py-2 px-3 font-semibold text-slate-900">{{ item.deskripsi_id }}</td>
              <td class="py-2 px-3 text-slate-500 text-xs italic">{{ item.deskripsi_en }}</td>
              <td class="py-2 px-3 text-center">
                <StatusBadge status="Terapi Anak" />
              </td>
              <td class="py-2 px-3 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button @click="openEditIcd9(item)" class="w-7 h-7 rounded flex items-center justify-center text-slate-500 hover:text-[#0F766E] hover:bg-[#E6F2F5] transition cursor-pointer" title="Edit">
                    <i class="pi pi-pencil text-xs"></i>
                  </button>
                  <button @click="confirmDeleteIcd9(item)" class="w-7 h-7 rounded flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                    <i class="pi pi-trash text-xs"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL FORM ICD-10 -->
    <BaseModal
      v-model="showIcd10Modal"
      :title="isEditIcd10 ? 'Edit Diagnosa ICD-10' : 'Tambah Diagnosa ICD-10'"
      subtitle="Klasifikasi standar diagnosis klinis anak Kemenkes"
      size="md"
    >
      <form @submit.prevent="saveIcd10" id="formIcd10" class="space-y-3 text-xs">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-[12px] font-medium text-slate-700 mb-1">Kode ICD-10</label>
            <input v-model="formIcd10.kode" required class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] font-mono font-bold" placeholder="mis. F80.1" />
          </div>
          <div>
            <label class="block text-[12px] font-medium text-slate-700 mb-1">Kategori Penyakit</label>
            <input v-model="formIcd10.kategori" class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]" placeholder="mis. Tumbuh Kembang" />
          </div>
        </div>
        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Deskripsi Diagnosa (Bahasa Indonesia)</label>
          <input v-model="formIcd10.deskripsi_id" required class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]" placeholder="mis. Gangguan Berbahasa Ekspresif / Keterlambatan Bicara" />
        </div>
        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Deskripsi Internasional (Bahasa Inggris)</label>
          <input v-model="formIcd10.deskripsi_en" required class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] italic" placeholder="mis. Expressive language disorder" />
        </div>
      </form>

      <template #footer>
        <button type="button" @click="showIcd10Modal = false" class="h-[34px] px-3.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-[12.5px] font-medium transition cursor-pointer">Batal</button>
        <button type="submit" form="formIcd10" class="h-[34px] px-3.5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-[12.5px] font-medium transition cursor-pointer">Simpan ICD-10</button>
      </template>
    </BaseModal>

    <!-- MODAL FORM ICD-9 -->
    <BaseModal
      v-model="showIcd9Modal"
      :title="isEditIcd9 ? 'Edit Prosedur ICD-9' : 'Tambah Prosedur ICD-9'"
      subtitle="Klasifikasi prosedur tindakan medis dan terapi okupasi/wicara"
      size="md"
    >
      <form @submit.prevent="saveIcd9" id="formIcd9" class="space-y-3 text-xs">
        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Kode Prosedur ICD-9-CM</label>
          <input v-model="formIcd9.kode" required class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] font-mono font-bold" placeholder="mis. 93.53" />
        </div>
        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Nama Tindakan / Prosedur (Indonesia)</label>
          <input v-model="formIcd9.deskripsi_id" required class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]" placeholder="mis. Terapi Wicara & Latihan Artikulasi" />
        </div>
        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Nama Tindakan / Prosedur (English)</label>
          <input v-model="formIcd9.deskripsi_en" required class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] italic" placeholder="mis. Speech therapy and language training" />
        </div>
      </form>

      <template #footer>
        <button type="button" @click="showIcd9Modal = false" class="h-[34px] px-3.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-[12.5px] font-medium transition cursor-pointer">Batal</button>
        <button type="submit" form="formIcd9" class="h-[34px] px-3.5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-[12.5px] font-medium transition cursor-pointer">Simpan ICD-9</button>
      </template>
    </BaseModal>

    <!-- CONFIRM DELETE DIALOGS -->
    <ConfirmDialog
      v-model="confirmDeleteIcdModal"
      :title="deleteType === 'icd10' ? 'Hapus Kode ICD-10' : 'Hapus Kode ICD-9'"
      :message="`Apakah Anda yakin ingin menghapus kode '${selectedItemToDelete?.kode}'?`"
      confirmText="Ya, Hapus"
      type="danger"
      @confirm="executeDeleteIcd"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../axios'
import BaseModal from '@/components/ui/BaseModal.vue'
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const activeTab = ref('icd10')

// ICD-10
const icd10List = ref([])
const loadingIcd10 = ref(false)
const searchIcd10Query = ref('')
const showIcd10Modal = ref(false)
const isEditIcd10 = ref(false)
const formIcd10 = ref({ id: null, kode: '', deskripsi_id: '', deskripsi_en: '', kategori: 'Pediatrik', is_terapi_anak: true })

// ICD-9
const icd9List = ref([])
const loadingIcd9 = ref(false)
const searchIcd9Query = ref('')
const showIcd9Modal = ref(false)
const isEditIcd9 = ref(false)
const formIcd9 = ref({ id: null, kode: '', deskripsi_id: '', deskripsi_en: '', is_terapi_anak: true })

// Delete Dialog
const confirmDeleteIcdModal = ref(false)
const deleteType = ref('icd10')
const selectedItemToDelete = ref(null)

const fetchIcd10 = async () => {
  loadingIcd10.value = true
  try {
    const res = await api.get('/pediatric-workflow/icd10', {
      params: { search: searchIcd10Query.value }
    })
    icd10List.value = res.data?.data || res.data || []
  } catch (err) {
    console.error(err)
  } finally {
    loadingIcd10.value = false
  }
}

const fetchIcd9 = async () => {
  loadingIcd9.value = true
  try {
    const res = await api.get('/pediatric-workflow/icd9', {
      params: { search: searchIcd9Query.value }
    })
    icd9List.value = res.data?.data || res.data || []
  } catch (err) {
    console.error(err)
  } finally {
    loadingIcd9.value = false
  }
}

const openAddIcd10 = () => {
  isEditIcd10.value = false
  formIcd10.value = { id: null, kode: '', deskripsi_id: '', deskripsi_en: '', kategori: 'Pediatrik', is_terapi_anak: true }
  showIcd10Modal.value = true
}

const openEditIcd10 = (item) => {
  isEditIcd10.value = true
  formIcd10.value = { ...item }
  showIcd10Modal.value = true
}

const saveIcd10 = async () => {
  try {
    const res = await api.post('/pediatric-workflow/icd10', formIcd10.value)
    toast.success(res.data.message || 'ICD-10 berhasil disimpan')
    showIcd10Modal.value = false
    fetchIcd10()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menyimpan ICD-10')
  }
}

const confirmDeleteIcd10 = (item) => {
  deleteType.value = 'icd10'
  selectedItemToDelete.value = item
  confirmDeleteIcdModal.value = true
}

const openAddIcd9 = () => {
  isEditIcd9.value = false
  formIcd9.value = { id: null, kode: '', deskripsi_id: '', deskripsi_en: '', is_terapi_anak: true }
  showIcd9Modal.value = true
}

const openEditIcd9 = (item) => {
  isEditIcd9.value = true
  formIcd9.value = { ...item }
  showIcd9Modal.value = true
}

const saveIcd9 = async () => {
  try {
    const res = await api.post('/pediatric-workflow/icd9', formIcd9.value)
    toast.success(res.data.message || 'ICD-9 berhasil disimpan')
    showIcd9Modal.value = false
    fetchIcd9()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menyimpan ICD-9')
  }
}

const confirmDeleteIcd9 = (item) => {
  deleteType.value = 'icd9'
  selectedItemToDelete.value = item
  confirmDeleteIcdModal.value = true
}

const executeDeleteIcd = async () => {
  if (!selectedItemToDelete.value) return
  try {
    if (deleteType.value === 'icd10') {
      await api.delete(`/pediatric-workflow/icd10/${selectedItemToDelete.value.id}`)
      toast.success('Kode ICD-10 berhasil dihapus')
      fetchIcd10()
    } else {
      await api.delete(`/pediatric-workflow/icd9/${selectedItemToDelete.value.id}`)
      toast.success('Kode ICD-9 berhasil dihapus')
      fetchIcd9()
    }
    confirmDeleteIcdModal.value = false
  } catch (err) {
    toast.error('Gagal menghapus data')
  }
}

onMounted(() => {
  fetchIcd10()
  fetchIcd9()
})
</script>
