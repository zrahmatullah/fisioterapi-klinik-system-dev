<template>
  <div class="p-6 bg-slate-100 min-h-screen space-y-6">

    <!-- JUDUL -->
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Master Layanan Klinik </h1>
      <p class="text-sm text-gray-500">Kelola data layanan terapi</p>
    </div>

    <!-- TOMBOL -->
    <div class="bg-white rounded-2xl shadow-md p-4">
      <Button
        @click="openAdd"
        class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl
               bg-gradient-to-r from-indigo-600 to-purple-600
               hover:from-indigo-700 hover:to-purple-700 border-0 text-white"
      >
        <i class="pi pi-plus"></i> Tambah Layanan
      </Button>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow-md p-4">
      <DataTable :value="layananList" :loading="loading" paginator :rows="10">
        <Column header="No">
          <template #body="{ index }">{{ index + 1 }}</template>
        </Column>
        <Column field="layanan" header="Nama Layanan" />
        <Column field="kategori.kategori_layanan" header="Kategori" />

        <Column header="Harga Weekday">
          <template #body="{ data }">{{ formatCurrency(data.harga_weekday) }}</template>
        </Column>

        <Column header="Harga Weekend">
          <template #body="{ data }">{{ formatCurrency(data.harga_weekend) }}</template>
        </Column>

        <Column field="qty" header="Qty" />

        <Column header="Status">
          <template #body="{ data }">
            <span
              class="px-3 py-1 text-xs rounded-full"
              :class="data.status_aktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
            >
              {{ data.status_aktif ? 'Aktif' : 'Non Aktif' }}
            </span>
          </template>
        </Column>

        <Column header="Aksi">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Button icon="pi pi-pencil" size="small" @click="openEdit(data)" />
              <Button icon="pi pi-trash" size="small" severity="danger" @click="openDelete(data)" />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>

    <!-- FORM DIALOG -->
    <Dialog v-model:visible="dialog" modal :header="form.id ? 'Edit Layanan' : 'Tambah Layanan'" class="w-full max-w-2xl">

      <div class="space-y-6">

        <!-- INFO -->
        <div class="bg-slate-50 rounded-xl p-4">
          <h3 class="font-semibold mb-3">Informasi Layanan</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium">Nama Layanan *</label>
              <InputText v-model="form.layanan" class="w-full mt-1" />
            </div>

            <div>
              <label class="text-sm font-medium">Kategori *</label>
              <Dropdown
                v-model="form.kategori_layanan_id"
                :options="kategoriOptions"
                optionLabel="kategori_layanan"
                optionValue="id"
                class="w-full mt-1"
              />
            </div>
          </div>
        </div>

        <!-- HARGA -->
        <div class="bg-slate-50 rounded-xl p-4">
          <h3 class="font-semibold mb-3">Harga Layanan</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
              <label class="text-sm font-medium">Harga Weekday *</label>
              <div class="flex mt-1">
                <span class="px-3 bg-gray-200 border border-r-0 rounded-l-lg flex items-center font-semibold">Rp</span>
                <input
                  type="text"
                  v-model="hargaWeekdayFormatted"
                  @input="onHargaWeekdayInput"
                  class="border rounded-r-lg px-3 py-2 w-full focus:ring-2 focus:ring-indigo-400 outline-none"
                />
              </div>
            </div>

            <div>
              <label class="text-sm font-medium">Harga Weekend *</label>
              <div class="flex mt-1">
                <span class="px-3 bg-gray-200 border border-r-0 rounded-l-lg flex items-center font-semibold">Rp</span>
                <input
                  type="text"
                  v-model="hargaWeekendFormatted"
                  @input="onHargaWeekendInput"
                  class="border rounded-r-lg px-3 py-2 w-full focus:ring-2 focus:ring-indigo-400 outline-none"
                />
              </div>
            </div>

          </div>
        </div>

        <!-- SETTING -->
        <div class="bg-slate-50 rounded-xl p-4">
          <h3 class="font-semibold mb-3">Pengaturan</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium">Qty *</label>
              <InputText type="number" v-model.number="form.qty" class="w-full mt-1" min="1" />
            </div>

            <div>
              <label class="text-sm font-medium">Status</label>
              <Dropdown
                v-model="form.status_aktif"
                :options="statusOptions"
                optionLabel="label"
                optionValue="value"
                class="w-full mt-1"
              />
            </div>
          </div>
        </div>

      </div>

      <template #footer>
        <div class="flex justify-end gap-3">
          <Button label="Batal" text @click="dialog = false" />
          <Button
            label="Simpan"
            icon="pi pi-check"
            :loading="saving"
            :disabled="!isValid"
            class="bg-indigo-600 hover:bg-indigo-700 border-0 text-white px-6"
            @click="save"
          />
        </div>
      </template>
    </Dialog>

    <!-- DELETE -->
    <Dialog v-model:visible="confirmDelete" header="Konfirmasi" modal>
      <p>Yakin ingin menghapus layanan ini?</p>
      <template #footer>
        <Button label="Batal" text @click="confirmDelete = false" />
        <Button label="Hapus" severity="danger" @click="remove" />
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

const layananList = ref([])
const kategoriOptions = ref([])
const loading = ref(false)
const dialog = ref(false)
const confirmDelete = ref(false)
const saving = ref(false)

const form = ref({
  id: null,
  layanan: '',
  kategori_layanan_id: null,
  harga_weekday: 0,
  harga_weekend: 0,
  qty: 1,
  status_aktif: true
})

const hargaWeekdayFormatted = ref('')
const hargaWeekendFormatted = ref('')

const statusOptions = [
  { label: 'Aktif', value: true },
  { label: 'Non Aktif', value: false }
]

const formatCurrency = (v) =>
  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(v)

const formatNumber = (v) =>
  v.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')

const onHargaWeekdayInput = () => {
  hargaWeekdayFormatted.value = formatNumber(hargaWeekdayFormatted.value)
  form.value.harga_weekday = parseInt(hargaWeekdayFormatted.value.replace(/\./g, '') || 0)
}

const onHargaWeekendInput = () => {
  hargaWeekendFormatted.value = formatNumber(hargaWeekendFormatted.value)
  form.value.harga_weekend = parseInt(hargaWeekendFormatted.value.replace(/\./g, '') || 0)
}

const isValid = computed(() =>
  form.value.layanan &&
  form.value.kategori_layanan_id &&
  form.value.harga_weekday > 0 &&
  form.value.harga_weekend > 0 &&
  form.value.qty > 0
)

const fetchKategori = async () => {
  kategoriOptions.value = (await api.get('/kategori-layanan')).data
}

const fetchLayanan = async () => {
  loading.value = true
  layananList.value = (await api.get('/layanan')).data
  loading.value = false
}

const openAdd = () => {
  form.value = { id: null, layanan: '', kategori_layanan_id: null, harga_weekday: 0, harga_weekend: 0, qty: 1, status_aktif: true }
  hargaWeekdayFormatted.value = ''
  hargaWeekendFormatted.value = ''
  dialog.value = true
}

const openEdit = (row) => {
  form.value = { ...row }
  hargaWeekdayFormatted.value = formatNumber(String(row.harga_weekday))
  hargaWeekendFormatted.value = formatNumber(String(row.harga_weekend))
  dialog.value = true
}

const save = async () => {
  saving.value = true
  try {
    if (form.value.id) await api.put(`/layanan/${form.value.id}`, form.value)
    else await api.post('/layanan', form.value)

    toast.success('Data berhasil disimpan')
    dialog.value = false
    fetchLayanan()
  } catch {
    toast.error('Gagal menyimpan data')
  } finally {
    saving.value = false
  }
}

const openDelete = (row) => {
  form.value = { ...row }
  confirmDelete.value = true
}

const remove = async () => {
  await api.delete(`/layanan/${form.value.id}`)
  toast.success('Data berhasil dihapus')
  confirmDelete.value = false
  fetchLayanan()
}

onMounted(() => {
  fetchKategori()
  fetchLayanan()
})
</script>
