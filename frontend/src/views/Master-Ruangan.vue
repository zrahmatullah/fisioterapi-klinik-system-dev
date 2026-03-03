<template>
  <div class="p-6">
    <!-- HEADER -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-xl font-bold text-gray-800">Master Ruangan</h1>
      <Button
        @click="openAdd"
        class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 active:scale-95 transition border-0 shadow-lg text-white"
      >
        <i class="pi pi-plus text-sm"></i>
        <span>Tambah Ruangan</span>
      </Button>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow">
      <DataTable :value="ruanganList" :loading="loading" paginator :rows="10" responsiveLayout="scroll">
        <Column header="No" style="width:60px">
          <template #body="{ index }">{{ index + 1 }}</template>
        </Column>
        <Column field="ruangan" header="Nama Ruangan" />
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
              <Button icon="pi pi-pencil" severity="info" size="small" @click="openEdit(data)" />
              <Button icon="pi pi-trash" severity="danger" size="small" @click="openDelete(data)" />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>

    <!-- FORM DIALOG -->
    <Dialog v-model:visible="dialog" :header="isEdit ? 'Edit Ruangan' : 'Tambah Ruangan'" modal class="w-full max-w-md">
      <div class="space-y-4">
        <div>
          <label class="block text-sm mb-1 font-medium">Nama Ruangan</label>
          <InputText v-model="form.ruangan" class="w-full" />
        </div>
        <div>
          <label class="block text-sm mb-1 font-medium">Status</label>
          <Dropdown
            v-model="form.status_aktif"
            :options="statusOptions"
            optionLabel="label"
            optionValue="value"
            class="w-full"
          />
        </div>
      </div>
      <template #footer>
        <Button label="Batal" text @click="dialog = false" />
        <Button label="Simpan" icon="pi pi-check" class="!bg-indigo-600 border-0" @click="save" />
      </template>
    </Dialog>

    <!-- CONFIRM DELETE -->
    <Dialog v-model:visible="confirmDelete" header="Konfirmasi" modal class="w-full max-w-sm">
      <p class="text-sm text-gray-600">Yakin ingin menghapus ruangan ini?</p>
      <template #footer>
        <Button label="Batal" text @click="confirmDelete = false" />
        <Button label="Hapus" icon="pi pi-trash" severity="danger" @click="remove" />
      </template>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'

const toast = useToast()
const ruanganList = ref([])
const loading = ref(false)
const dialog = ref(false)
const confirmDelete = ref(false)
const isEdit = ref(false)
const form = ref({ id: null, ruangan: '', status_aktif: true })

const statusOptions = [
  { label: 'Aktif', value: true },
  { label: 'Non Aktif', value: false }
]

const fetchRuangan = async () => {
  loading.value = true
  try {
    const res = await api.get('/ruangan')
    ruanganList.value = res.data
  } catch {
    toast.error('Gagal memuat data ruangan')
  } finally {
    loading.value = false
  }
}

const openAdd = () => {
  isEdit.value = false
  form.value = { id: null, ruangan: '', status_aktif: true }
  dialog.value = true
}
const openEdit = (row) => {
  isEdit.value = true
  form.value = { ...row }
  dialog.value = true
}

const save = async () => {
  try {
    if (isEdit.value) await api.put(`/ruangan/${form.value.id}`, form.value)
    else await api.post('/ruangan', form.value)
    toast.success('Data berhasil disimpan')
    dialog.value = false
    fetchRuangan()
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
    await api.delete(`/ruangan/${form.value.id}`)
    toast.success('Data berhasil dihapus')
    confirmDelete.value = false
    fetchRuangan()
  } catch {
    toast.error('Gagal menghapus data')
  }
}

onMounted(fetchRuangan)
</script>
