<template>
  <div class="p-6 bg-gray-50 min-h-screen">

    <!-- HEADER -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">

        <!-- KOLOM KIRI : TITLE + BUTTON + SEARCH -->
        <div class="space-y-3">
          <!-- TITLE -->
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Master Kategori Layanan</h1>
            <p class="text-sm text-gray-500">Kelola kategori layanan yang tersedia</p>
          </div>

          <!-- ACTIONS -->
          <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
            <Button
              @click="openAdd"
              class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-md
                     bg-indigo-600 hover:bg-indigo-700
                     transition active:scale-95 border-0 text-white"
            >
              <i class="pi pi-plus text-sm"></i>
              <span>Tambah Kategori</span>
            </Button>
          </div>
        </div>

      </div>
    </div>

    <!-- TABLE -->

    <div class="bg-white rounded-xl shadow overflow-hidden">
      <DataTable
        :value="filteredKategori"
        :loading="loading"
        paginator
        :rows="10"
        responsiveLayout="scroll"
        class="p-datatable-sm"
        rowHover
      >
        <Column header="No" style="width:60px">
          <template #body="{ index }">{{ index + 1 }}</template>
        </Column>

        <Column field="kategori_layanan" header="Nama Kategori" />

        <Column header="Status">
          <template #body="{ data }">
            <span
              class="px-3 py-1 text-xs rounded-full font-medium"
              :class="data.status_aktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
            >
              {{ data.status_aktif ? 'Aktif' : 'Non Aktif' }}
            </span>
          </template>
        </Column>

        <Column header="Aksi" style="width:140px">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Button icon="pi pi-pencil" severity="info" size="small" class="hover:-translate-y-0.5 transition" @click="openEdit(data)" />
              <Button icon="pi pi-trash" severity="danger" size="small" class="hover:-translate-y-0.5 transition" @click="openDelete(data)" />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>

        <!-- FORM DIALOG (CLEAN MODERN) -->
    <Dialog
      v-model:visible="dialog"
      :header="isEdit ? 'Edit Kategori' : 'Tambah Kategori'"
      modal
      class="w-full max-w-md"
    >
      <div class="bg-white rounded-lg">
        <div class="space-y-5">

          <!-- FIELD: NAMA KATEGORI -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
            <InputText
              v-model="form.kategori_layanan"
              placeholder="Contoh: Fisioterapi"
              class="w-full rounded-md border-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-100"
            />
          </div>

          <!-- FIELD: STATUS -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <Dropdown
              v-model="form.status_aktif"
              :options="statusOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Pilih status"
              class="w-full rounded-md"
            />
          </div>

        </div>
      </div>

      <template #footer>
        <div class="flex justify-end gap-3 pt-2">
          <Button
            label="Batal"
            class="text-gray-600"
            text
            @click="dialog = false"
          />

          <Button
            label="Simpan"
            icon="pi pi-check"
            class="bg-indigo-600 hover:bg-indigo-700 border-0 px-5 rounded-md"
            @click="save"
          />
        </div>
      </template>
    </Dialog>

    <!-- CONFIRM DELETE -->
    <Dialog v-model:visible="confirmDelete" header="Konfirmasi" modal class="w-full max-w-sm">
      <p class="text-sm text-gray-600">Yakin ingin menghapus kategori ini?</p>
      <template #footer>
        <Button label="Batal" text @click="confirmDelete = false" />
        <Button label="Hapus" icon="pi pi-trash" severity="danger" @click="remove" />
      </template>
    </Dialog>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'

import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'

const toast = useToast()
const kategoriList = ref([])
const loading = ref(false)
const dialog = ref(false)
const confirmDelete = ref(false)
const isEdit = ref(false)

const search = ref('')

const form = ref({ id: null, kategori_layanan: '', status_aktif: true })

const statusOptions = [
  { label: 'Aktif', value: true },
  { label: 'Non Aktif', value: false }
]

const filteredKategori = computed(() => {
  if (!search.value) return kategoriList.value
  return kategoriList.value.filter(item =>
    item.kategori_layanan.toLowerCase().includes(search.value.toLowerCase())
  )
})

const fetchKategori = async () => {
  loading.value = true
  try {
    const res = await api.get('/kategori-layanan')
    kategoriList.value = res.data
  } catch {
    toast.error('Gagal memuat data kategori')
  } finally {
    loading.value = false
  }
}

const openAdd = () => {
  isEdit.value = false
  form.value = { id: null, kategori_layanan: '', status_aktif: true }
  dialog.value = true
}

const openEdit = (row) => {
  isEdit.value = true
  form.value = { ...row }
  dialog.value = true
}

const save = async () => {
  try {
    if (isEdit.value) await api.put(`/kategori-layanan/${form.value.id}`, form.value)
    else await api.post('/kategori-layanan', form.value)

    toast.success('Data berhasil disimpan')
    dialog.value = false
    fetchKategori()
  } catch {
    toast.error('Gagal menyimpan data')
  }
}

const openDelete = (row) => {
  form.value = { ...row }
  confirmDelete.value = true
}

const remove = async () => {
  try {
    await api.delete(`/kategori-layanan/${form.value.id}`)
    toast.success('Data berhasil dihapus')
    confirmDelete.value = false
    fetchKategori()
  } catch {
    toast.error('Gagal menghapus data')
  }
}

onMounted(fetchKategori)
</script>
