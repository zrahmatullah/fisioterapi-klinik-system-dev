<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-6">

    <!-- HEADER -->
    <div class="mb-6">
      <h2 class="text-3xl font-bold text-slate-800">Data User</h2>
      <p class="text-slate-600 text-sm mt-1">Kelola data user, role, dan akses login</p>
    </div>

    <!-- CARD -->
    <div class="bg-white shadow-xl ring-1 ring-slate-200 rounded-2xl p-6">

      <!-- TOOLBAR -->
      <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">

        <Button
          label="Tambah User"
          icon="pi pi-user-plus"
          severity="success"
          class="font-semibold px-5 py-2 rounded-xl"
          @click="openAdd"
        />

        <div class="relative w-full md:w-72">
          <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <InputText
            v-model="search"
            placeholder="Cari user..."
            class="w-full pl-10 rounded-xl border-slate-300 bg-white focus:ring-2 focus:ring-indigo-400"
          />
        </div>

      </div>

      <!-- TABLE -->
      <DataTable
        :value="filteredData"
        paginator
        :rows="10"
        rowHover
        class="rounded-xl overflow-hidden text-sm"
      >
        <Column field="nama" header="Nama" />

        <Column header="Email">
          <template #body="{ data }">
            <span
              v-if="data.email_verified_at"
              class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold"
            >
              <i class="pi pi-check-circle"></i> {{ data.email }}
            </span>
            <span v-else class="text-slate-400">{{ data.email || '-' }}</span>
          </template>
        </Column>

        <Column header="Telepon">
          <template #body="{ data }">
            {{ data.no_telepon || '-' }}
          </template>
        </Column>

        <Column header="Spesialisasi">
          <template #body="{ data }">
            {{ data.spesialisasi || '-' }}
          </template>
        </Column>

        <Column header="Username">
          <template #body="{ data }">
            {{ data.userLogin?.username || '-' }}
          </template>
        </Column>

        <Column header="Aksi">
          <template #body="{ data }">
            <div class="flex gap-1">
              <Button icon="pi pi-pencil" rounded text severity="warning" @click="openEdit(data)" />
              <Button icon="pi pi-trash" rounded text severity="danger" @click="deleteRow(data)" />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>

    <!-- MODAL -->
    <Dialog
      v-model:visible="dialog"
      modal
      :header="isEdit ? 'Edit User' : 'Tambah User'"
      :style="{ width: '70vw' }"
      :breakpoints="{ '960px': '95vw', '640px': '100vw' }"
      appendTo="body"
    >

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- KIRI -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 space-y-4 shadow-sm">

          <h3 class="font-semibold text-slate-800 flex items-center gap-2">
            <i class="pi pi-id-card text-indigo-500"></i> Data Diri & Email
          </h3>

          <div>
            <label class="label">Nama</label>
            <InputText v-model="form.nama" class="input" />
          </div>

          <div>
            <label class="label">Alamat</label>
            <textarea v-model="form.alamat" class="textarea"></textarea>
          </div>

          <div>
            <label class="label">NIP</label>
            <InputText v-model="form.nip" class="input" />
          </div>

          <div>
            <label class="label">Jenis User</label>
            <Dropdown
              v-model="form.jenis_user_id"
              :options="jenisUser"
              optionLabel="jenis_user"
              optionValue="id"
              placeholder="Pilih jenis user"
              class="input"
            />
          </div>

          <div>
            <label class="label">Email</label>
            <InputText v-model="form.email" class="input" />
          </div>

          <!-- OTP -->
          <div v-if="form.email" class="bg-slate-50 border rounded-xl p-4 space-y-3">

            <div class="flex justify-between items-center">
              <span class="font-medium text-slate-700">Verifikasi Email</span>
              <span v-if="emailVerified" class="text-emerald-600 font-semibold text-sm">✔ Terverifikasi</span>
              <span v-else class="text-red-500 text-sm">Belum diverifikasi</span>
            </div>

            <div v-if="!emailVerified" class="space-y-2">
              <Button label="Kirim OTP" icon="pi pi-envelope" size="small" severity="info" class="w-full" @click="sendOtp" />
              <InputText v-model="otpCode" placeholder="Masukkan kode OTP" class="input" />
              <Button label="Verifikasi OTP" icon="pi pi-check" size="small" severity="success" class="w-full" @click="verifyOtp" />
            </div>
          </div>

          <div>
            <label class="label">Nomor Telepon</label>
            <InputText v-model="form.no_telepon" class="input" />
          </div>

          <div>
            <label class="label">Jenis Kelamin</label>
            <Dropdown
              v-model="form.jenis_kelamin_id"
              :options="jenisKelamin"
              optionLabel="nama"
              optionValue="id"
              placeholder="Pilih jenis kelamin"
              class="input"
            />
          </div>

          <div>
            <label class="label">Spesialisasi</label>
            <InputText v-model="form.spesialisasi" class="input" />
          </div>

        </div>

        <!-- KANAN -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 space-y-4 shadow-sm">

          <h3 class="font-semibold text-slate-800 flex items-center gap-2">
            <i class="pi pi-lock text-amber-500"></i> Data Login
          </h3>

          <div>
            <label class="label">Username</label>
            <InputText v-model="form.username" class="input" />
          </div>

          <div>
            <label class="label">Password</label>
            <Password v-model="form.password" toggleMask :feedback="false" class="input" />
            <small v-if="isEdit" class="text-xs text-slate-400">Kosongkan jika tidak ingin mengubah password</small>
          </div>

          <div>
            <label class="label">Role</label>
            <Dropdown
              v-model="form.role_id"
              :options="roles"
              optionLabel="role"
              optionValue="id"
              placeholder="Pilih role"
              class="input"
            />
          </div>

        </div>

      </div>

      <template #footer>
        <div class="flex justify-end gap-3">
          <Button label="Batal" severity="secondary" @click="dialog=false" />
          <Button
            label="Simpan"
            class="bg-gradient-to-r from-indigo-600 to-blue-600 border-0 text-white font-semibold px-5 py-2 rounded-xl hover:from-indigo-700 hover:to-blue-700"
            :disabled="form.email && !emailVerified"
            @click="save"
          />
        </div>
      </template>

    </Dialog>

  </div>
</template>

<script setup>
/* SCRIPT ASLI — TIDAK DIUBAH */
import { ref, computed, onMounted } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'

import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Dropdown from 'primevue/dropdown'
import Password from 'primevue/password'

const toast = useToast()

const data = ref([])
const jenisUser = ref([])
const roles = ref([])
const jenisKelamin = ref([])

const search = ref('')
const dialog = ref(false)
const isEdit = ref(false)

const otpCode = ref('')
const emailVerified = ref(false)

const formDefault = {
  nama: '',
  alamat: '',
  nip: '',
  jenis_user_id: null,
  email: '',
  no_telepon: '',
  jenis_kelamin_id: null,
  spesialisasi: '',
  username: '',
  password: '',
  role_id: null
}

const form = ref({ ...formDefault })

onMounted(loadData)

async function loadData() {
  data.value = (await api.get('/master-user')).data
  jenisUser.value = (await api.get('/jenis-user')).data
  roles.value = (await api.get('/role')).data
  jenisKelamin.value = (await api.get('/jenis-kelamin')).data
}

const filteredData = computed(() =>
  search.value
    ? data.value.filter(d => d.nama.toLowerCase().includes(search.value.toLowerCase()))
    : data.value
)

const openAdd = () => {
  form.value = { ...formDefault }
  emailVerified.value = false
  otpCode.value = ''
  isEdit.value = false
  dialog.value = true
}

const openEdit = (row) => {
  form.value = {
    ...row,
    username: row.userLogin?.username,
    role_id: row.userLogin?.role?.id
  }
  emailVerified.value = !!row.email_verified_at
  isEdit.value = true
  dialog.value = true
}

const save = async () => {
  try {
    isEdit.value
      ? await api.put(`/master-user/${form.value.id}`, form.value)
      : await api.post('/master-user', form.value)

    toast.success('Data berhasil disimpan')
    dialog.value = false
    loadData()
  } catch {
    toast.error('Gagal menyimpan data')
  }
}

const sendOtp = async () => {
  try {
    await api.post('/otp-email/send', { email: form.value.email })
    toast.success('OTP dikirim ke email')
  } catch {
    toast.error('Gagal mengirim OTP')
  }
}

const verifyOtp = async () => {
  try {
    await api.post('/otp-email/verify', {
      email: form.value.email,
      kode_otp: otpCode.value
    })
    emailVerified.value = true
    toast.success('Email berhasil diverifikasi')
  } catch {
    toast.error('OTP tidak valid')
  }
}

const deleteRow = async (row) => {
  if (!confirm(`Hapus user "${row.nama}" ?`)) return
  await api.delete(`/master-user/${row.id}`)
  toast.success('User dihapus')
  loadData()
}
</script>

<style scoped>
.label {
  @apply block text-sm font-semibold text-slate-700 mb-1;
}

.input {
  @apply w-full border border-slate-300 rounded-lg px-3 py-2
         bg-white text-slate-800
         focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
         transition;
}

.textarea {
  @apply w-full border border-slate-300 rounded-lg p-2
         bg-white text-slate-800
         focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
         transition;
}
</style>
