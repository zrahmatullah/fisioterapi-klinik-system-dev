<template>
  <div class="min-h-screen bg-[#f6f8fc] p-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">

      <div>
        <h1 class="text-4xl font-bold tracking-tight text-slate-900">
          Data User
        </h1>

        <p class="text-slate-500 mt-1">
          Kelola user, role, akses login, dan verifikasi email
        </p>
      </div>

      <div class="mt-4 md:mt-0 flex gap-3">

        <Button
          icon="pi pi-download"
          severity="secondary"
          outlined
          class="rounded-xl"
        />

        <Button
          label="Tambah User"
          icon="pi pi-plus"
          class="
            rounded-2xl
            px-5
            py-3
            bg-indigo-600
            border-indigo-600
            hover:bg-indigo-700
            hover:border-indigo-700
            shadow-md
            shadow-indigo-500/20
            hover:shadow-lg
            transition-all
            duration-300
            hover:-translate-y-0.5
            active:scale-[0.98]
            font-semibold
          "
          @click="openAdd"
        />

      </div>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">

      <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100">
        <div class="text-slate-500 text-sm">Total User</div>
        <div class="text-3xl font-bold mt-2">
          {{ data.length }}
        </div>
      </div>

      <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100">
        <div class="text-slate-500 text-sm">Email Verified</div>
        <div class="text-3xl font-bold mt-2 text-emerald-600">
          {{ data.filter(d => d.email_verified_at).length }}
        </div>
      </div>

      <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100">
        <div class="text-slate-500 text-sm">Belum Verifikasi</div>
        <div class="text-3xl font-bold mt-2 text-red-500">
          {{ data.filter(d => !d.email_verified_at).length }}
        </div>
      </div>

    </div>

    <!-- CARD -->
    <div
      class="
        bg-white/80
        backdrop-blur-xl
        border border-white/20
        shadow-[0_8px_30px_rgb(0,0,0,0.06)]
        rounded-3xl
        overflow-hidden
      "
    >

      <!-- TOOLBAR -->
      <div
        class="
          flex flex-col md:flex-row
          md:items-center
          md:justify-between
          gap-4
          p-5
          border-b
          border-slate-100
          bg-white/70
          backdrop-blur
        "
      >

        <div class="relative w-full md:w-80">

          <i
            class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
          ></i>

          <InputText
            v-model="search"
            placeholder="Cari nama user..."
            class="
              w-full
              pl-11
              py-3
              rounded-2xl
              border-slate-200
              bg-slate-50
              focus:bg-white
              focus:ring-4
              focus:ring-indigo-100
              transition-all
            "
          />

        </div>

      </div>

      <!-- TABLE -->
      <DataTable
        :value="filteredData"
        paginator
        :rows="10"
        rowHover
        stripedRows
        responsiveLayout="scroll"
        class="modern-table"
      >

        <!-- USER -->
        <Column header="User">

          <template #body="{ data }">

            <div class="flex items-center gap-3">

              <div
                class="
                  h-11 w-11 rounded-2xl
                  bg-gradient-to-br from-indigo-500 to-sky-500
                  text-white
                  flex items-center justify-center
                  font-bold shadow-lg
                "
              >
                {{ data.nama?.charAt(0) }}
              </div>

              <div>

                <div class="font-semibold text-slate-800">
                  {{ data.nama }}
                </div>

                <div class="text-xs text-slate-500">
                  {{ data.userLogin?.username || '-' }}
                </div>

              </div>

            </div>

          </template>

        </Column>

        <!-- EMAIL -->
        <Column header="Email">

          <template #body="{ data }">

            <span
              v-if="data.email_verified_at"
              class="
                inline-flex items-center gap-2
                px-3 py-1.5
                rounded-full
                bg-emerald-50
                text-emerald-700
                text-xs
                font-semibold
                border border-emerald-100
              "
            >
              <i class="pi pi-check-circle"></i>
              {{ data.email }}
            </span>

            <span
              v-else
              class="text-slate-400"
            >
              {{ data.email || '-' }}
            </span>

          </template>

        </Column>

        <!-- TELEPON -->
        <Column header="Telepon">

          <template #body="{ data }">
            {{ data.no_telepon || '-' }}
          </template>

        </Column>

        <!-- SPESIALISASI -->
        <Column header="Spesialisasi">

          <template #body="{ data }">
            {{ data.spesialisasi || '-' }}
          </template>

        </Column>

        <!-- ROLE -->
        <Column header="Role">

          <template #body="{ data }">

            <span
              class="
                px-3 py-1 rounded-full
                bg-indigo-50 text-indigo-700
                text-xs font-semibold
              "
            >
              {{ data.userLogin?.role?.role || '-' }}
            </span>

          </template>

        </Column>

        <!-- AKSI -->
        <Column header="Aksi">

          <template #body="{ data }">

            <div class="flex gap-2">

              <Button
                icon="pi pi-pencil"
                rounded
                text
                severity="contrast"
                class="hover:bg-amber-50"
                @click="openEdit(data)"
              />

              <Button
                icon="pi pi-trash"
                rounded
                text
                severity="danger"
                class="hover:bg-red-50"
                @click="deleteRow(data)"
              />

            </div>

          </template>

        </Column>

      </DataTable>

    </div>

    <!-- DIALOG -->
    <Dialog
      v-model:visible="dialog"
      modal
      :header="isEdit ? 'Edit User' : 'Tambah User'"
      :style="{ width: '75vw' }"
      :breakpoints="{ '960px': '95vw', '640px': '100vw' }"
      class="modern-dialog"
    >

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- LEFT -->
        <div
          class="
            bg-slate-50/80
            border border-slate-100
            rounded-3xl
            p-6
            space-y-5
          "
        >

          <h3 class="font-semibold text-slate-800 flex items-center gap-2">
            <i class="pi pi-id-card text-indigo-500"></i>
            Data Diri
          </h3>

          <div>
            <label class="label">Nama</label>
            <InputText v-model="form.nama" class="input" />
          </div>

          <div>
            <label class="label">Alamat</label>
            <textarea
              v-model="form.alamat"
              class="textarea"
            ></textarea>
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
              class="w-full"
            />
          </div>

          <div>
            <label class="label">Email</label>
            <InputText v-model="form.email" class="input" />
          </div>

          <!-- OTP -->
          <div
            v-if="form.email"
            class="
              bg-white
              border border-slate-200
              rounded-2xl
              p-4
              space-y-3
            "
          >

            <div class="flex justify-between items-center">

              <span class="font-medium text-slate-700">
                Verifikasi Email
              </span>

              <span
                v-if="emailVerified"
                class="text-emerald-600 font-semibold text-sm"
              >
                ✔ Terverifikasi
              </span>

              <span
                v-else
                class="text-red-500 text-sm"
              >
                Belum diverifikasi
              </span>

            </div>

            <div v-if="!emailVerified" class="space-y-3">

              <Button
                label="Kirim OTP"
                icon="pi pi-envelope"
                severity="info"
                class="w-full rounded-xl"
                @click="sendOtp"
              />

              <InputText
                v-model="otpCode"
                placeholder="Masukkan kode OTP"
                class="input"
              />

              <Button
                label="Verifikasi OTP"
                icon="pi pi-check"
                severity="success"
                class="w-full rounded-xl"
                @click="verifyOtp"
              />

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
              class="w-full"
            />
          </div>

          <div>
            <label class="label">Spesialisasi</label>
            <InputText v-model="form.spesialisasi" class="input" />
          </div>

        </div>

        <!-- RIGHT -->
        <div
          class="
            bg-slate-50/80
            border border-slate-100
            rounded-3xl
            p-6
            space-y-5
          "
        >

          <h3 class="font-semibold text-slate-800 flex items-center gap-2">
            <i class="pi pi-lock text-amber-500"></i>
            Data Login
          </h3>

          <div>
            <label class="label">Username</label>
            <InputText v-model="form.username" class="input" />
          </div>

          <div>
            <label class="label">Password</label>

            <Password
              v-model="form.password"
              toggleMask
              :feedback="false"
              class="w-full"
            />

            <small
              v-if="isEdit"
              class="text-xs text-slate-400"
            >
              Kosongkan jika tidak ingin mengubah password
            </small>

          </div>

          <div>
            <label class="label">Role</label>

            <Dropdown
              v-model="form.role_id"
              :options="roles"
              optionLabel="role"
              optionValue="id"
              placeholder="Pilih role"
              class="w-full"
            />
          </div>

        </div>

      </div>

      <template #footer>

        <div class="flex justify-end gap-3">

          <Button
            label="Batal"
            severity="secondary"
            outlined
            class="rounded-xl"
            @click="dialog=false"
          />

          <Button
            label="Simpan"
            class="
              rounded-xl
              px-5
              bg-slate-900
              border-slate-900
              hover:bg-slate-800
            "
            :disabled="form.email && !emailVerified"
            @click="save"
          />

        </div>

      </template>

    </Dialog>

  </div>
</template>

<script setup>
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
    ? data.value.filter(d =>
        d.nama.toLowerCase().includes(search.value.toLowerCase())
      )
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

    await api.post('/otp-email/send', {
      email: form.value.email
    })

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
  @apply block text-sm font-semibold text-slate-700 mb-2;
}

.input {
  @apply w-full border border-slate-200 rounded-2xl px-4 py-3
         bg-white text-slate-800
         shadow-sm
         focus:ring-4 focus:ring-indigo-100
         focus:border-indigo-400
         transition-all duration-200;
}

.textarea {
  @apply w-full border border-slate-200 rounded-2xl p-4
         bg-white text-slate-800
         shadow-sm
         focus:ring-4 focus:ring-indigo-100
         focus:border-indigo-400
         transition-all duration-200;
}

.modern-table .p-datatable-header {
  @apply bg-transparent border-0;
}

.modern-table .p-datatable-thead > tr > th {
  @apply bg-slate-50 text-slate-500 font-semibold border-0 py-4;
}

.modern-table .p-datatable-tbody > tr {
  @apply transition-all duration-200;
}

.modern-table .p-datatable-tbody > tr:hover {
  @apply bg-indigo-50/40;
}

.modern-table .p-datatable-tbody > tr > td {
  @apply border-0 py-4;
}

.modern-dialog .p-dialog-header {
  @apply border-b border-slate-100 pb-4;
}

.modern-dialog .p-dialog-content {
  @apply pt-6;
}
</style>