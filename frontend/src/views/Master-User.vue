<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-user"></i>
        </div>
        <div>
          <h1 class="text-[15px] font-bold text-[#0F172A] leading-tight">Master Pengguna & Staf</h1>
          <p class="text-[11.5px] text-[#64748B] mt-0.5">Kelola akun staf medis, kredensial login, dan verifikasi email</p>
        </div>
      </div>

      <button
        type="button"
        @click="openAdd"
        class="btn-primary-clinical text-[12.5px] shrink-0"
      >
        <i class="pi pi-user-plus text-xs"></i>
        <span>Tambah User</span>
      </button>
    </div>

    <!-- STATS ROW (shrink-0) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 shrink-0">
      <div class="bg-white rounded-xl p-3 shadow-[0_1px_3px_rgba(15,23,42,0.04)] border border-slate-200 flex items-center justify-between">
        <div>
          <div class="text-slate-500 text-[11px] font-medium uppercase tracking-wider">Total User Akun</div>
          <div class="text-lg font-bold text-[#0F172A] mt-0.5 tabular-nums">{{ data.length }}</div>
        </div>
        <div class="w-8 h-8 rounded-lg bg-teal-50 text-[#0F766E] flex items-center justify-center text-sm">
          <i class="pi pi-users"></i>
        </div>
      </div>

      <div class="bg-white rounded-xl p-3 shadow-[0_1px_3px_rgba(15,23,42,0.04)] border border-slate-200 flex items-center justify-between">
        <div>
          <div class="text-emerald-700 text-[11px] font-medium uppercase tracking-wider">Email Terverifikasi</div>
          <div class="text-lg font-bold text-emerald-800 mt-0.5 tabular-nums">
            {{ data.filter(u => u.email_verified_at).length }}
          </div>
        </div>
        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
          <i class="pi pi-check-circle"></i>
        </div>
      </div>

      <div class="bg-white rounded-xl p-3 shadow-[0_1px_3px_rgba(15,23,42,0.04)] border border-slate-200 flex items-center justify-between">
        <div>
          <div class="text-slate-500 text-[11px] font-medium uppercase tracking-wider">Tipe Pengguna</div>
          <div class="text-lg font-bold text-slate-800 mt-0.5 tabular-nums">{{ jenisUser.length }} Kategori</div>
        </div>
        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-sm">
          <i class="pi pi-shield"></i>
        </div>
      </div>
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
            placeholder="Cari nama staf / NIP..."
            class="w-full h-[34px] text-[12.5px] pl-8 pr-2.5 border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 transition bg-white text-slate-800 placeholder:text-slate-400"
          />
        </div>
        <span class="text-[11.5px] text-slate-500 font-medium">
          Total <strong>{{ filteredData.length }}</strong> staf ditemukan
        </span>
      </div>

      <!-- TABLE BODY -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="min-w-full text-[12.5px] text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2 px-3 text-center w-12">No</th>
              <th class="py-2 px-3">Nama & NIP</th>
              <th class="py-2 px-3">Kategori & Spesialisasi</th>
              <th class="py-2 px-3">Email & Kontak</th>
              <th class="py-2 px-3">Username & Role</th>
              <th class="py-2 px-3 text-center">Status Email</th>
              <th class="py-2 px-3 text-center w-24">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(item, index) in filteredData"
              :key="item.id || index"
              class="hover:bg-[#F0F6F8]/60 transition-colors"
            >
              <td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]">{{ index + 1 }}</td>
              <td class="py-2 px-3">
                <span class="font-semibold text-slate-900 block leading-tight">{{ item.nama }}</span>
                <span class="text-[10.5px] text-slate-400 font-mono">NIP: {{ item.nip || '-' }}</span>
              </td>
              <td class="py-2 px-3">
                <span class="text-slate-800 font-medium block">{{ item.jenisUser?.jenis_user || '-' }}</span>
                <span class="text-[11px] text-teal-700">{{ item.spesialisasi || '-' }}</span>
              </td>
              <td class="py-2 px-3">
                <span class="text-slate-800 block text-[11.5px]">{{ item.email || '-' }}</span>
                <span class="text-[10.5px] text-slate-400">{{ item.no_telepon || '-' }}</span>
              </td>
              <td class="py-2 px-3">
                <span class="font-mono text-[11px] font-bold text-slate-800 block">{{ item.userLogin?.username || '-' }}</span>
                <span class="text-[10.5px] text-teal-700 font-semibold">{{ item.userLogin?.role?.role || 'Tenaga Medis' }}</span>
              </td>
              <td class="py-2 px-3 text-center">
                <StatusBadge
                  :label="item.email_verified_at ? 'Terverifikasi' : 'Belum Verif'"
                  :type="item.email_verified_at ? 'success' : 'warning'"
                />
              </td>
              <td class="py-2 px-3 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    type="button"
                    @click="openEdit(item)"
                    class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 flex items-center justify-center transition shadow-2xs"
                    title="Edit User"
                  >
                    <i class="pi pi-pencil text-[11px]"></i>
                  </button>
                  <button
                    type="button"
                    @click="deleteRow(item)"
                    class="w-7 h-7 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition shadow-2xs"
                    title="Hapus"
                  >
                    <i class="pi pi-trash text-[11px]"></i>
                  </button>
                </div>
              </td>
            </tr>

            <!-- EMPTY -->
            <tr v-if="filteredData.length === 0">
              <td colspan="7" class="py-12 text-center text-slate-400">
                <div class="flex flex-col items-center justify-center">
                  <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                    <i class="pi pi-folder-open text-lg"></i>
                  </div>
                  <h4 class="text-[13px] font-semibold text-slate-800">User Tidak Ditemukan</h4>
                  <p class="text-[11.5px] text-slate-500 mt-0.5">Tidak ada user yang sesuai kriteria pencarian.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- TABLE FOOTER -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] text-slate-500 flex items-center justify-between">
        <span>Menampilkan {{ filteredData.length }} user</span>
        <span>SIMRS Kredensial Pengguna</span>
      </div>
    </div>

    <!-- MODAL FORM (COMPACT TIER 'lg': max-w-[820px]) -->
    <BaseModal
      v-model="dialog"
      :title="isEdit ? 'Edit User & Profil' : 'Tambah User & Profil Baru'"
      :subtitle="isEdit ? 'Perbarui informasi data diri dan hak login' : 'Daftarkan staf baru dan buatkan akun login SIMRS'"
      icon="pi pi-user"
      size="lg"
      :loading="saving"
      @submit="save"
    >
      <form @submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- LEFT: DATA DIRI -->
        <div class="space-y-2.5 bg-slate-50/70 p-3.5 rounded-xl border border-slate-200/60">
          <h4 class="font-bold text-slate-800 flex items-center gap-1.5 text-[11.5px] uppercase tracking-wider">
            <i class="pi pi-id-card text-[#0F766E]"></i>
            Informasi Data Diri
          </h4>

          <div>
            <label class="label-clinical">Nama Lengkap <span class="text-rose-500">*</span></label>
            <input v-model="form.nama" required class="input-clinical" placeholder="Nama beserta gelar" />
          </div>

          <div>
            <label class="label-clinical">NIP / Nomor Induk Pegawai</label>
            <input v-model="form.nip" class="input-clinical" placeholder="Nomor induk resmi" />
          </div>

          <div>
            <label class="label-clinical">Jenis User <span class="text-rose-500">*</span></label>
            <select v-model="form.jenis_user_id" required class="input-clinical">
              <option :value="null" disabled>Pilih jenis user...</option>
              <option v-for="j in jenisUser" :key="j.id" :value="j.id">
                {{ j.jenis_user }}
              </option>
            </select>
          </div>

          <div>
            <label class="label-clinical">Jenis Kelamin</label>
            <select v-model="form.jenis_kelamin_id" class="input-clinical">
              <option :value="null" disabled>Pilih jenis kelamin...</option>
              <option v-for="jk in jenisKelamin" :key="jk.id" :value="jk.id">
                {{ jk.nama }}
              </option>
            </select>
          </div>

          <div>
            <label class="label-clinical">Spesialisasi / Keahlian</label>
            <input v-model="form.spesialisasi" class="input-clinical" placeholder="mis. Terapi Wicara, Sensori Integrasi" />
          </div>

          <div>
            <label class="label-clinical">Nomor Telepon</label>
            <input v-model="form.no_telepon" class="input-clinical" placeholder="08xxxxxxxxxx" />
          </div>

          <div>
            <label class="label-clinical">Alamat</label>
            <textarea v-model="form.alamat" rows="2" class="input-clinical h-auto py-1.5 resize-none" placeholder="Alamat domisili..."></textarea>
          </div>
        </div>

        <!-- RIGHT: LOGIN & EMAIL OTP -->
        <div class="space-y-2.5 bg-slate-50/70 p-3.5 rounded-xl border border-slate-200/60 flex flex-col justify-between">
          <div class="space-y-2.5">
            <h4 class="font-bold text-slate-800 flex items-center gap-1.5 text-[11.5px] uppercase tracking-wider">
              <i class="pi pi-lock text-amber-600"></i>
              Kredensial Login & Akses
            </h4>

            <div>
              <label class="label-clinical">Username <span class="text-rose-500">*</span></label>
              <input v-model="form.username" required class="input-clinical font-mono" placeholder="Username login" />
            </div>

            <div>
              <label class="label-clinical">
                Password
                <span v-if="!isEdit" class="text-rose-500">*</span>
                <span v-else class="text-slate-400 font-normal text-[11px]">(Kosongkan jika tidak ubah)</span>
              </label>
              <input
                v-model="form.password"
                type="password"
                :required="!isEdit"
                class="input-clinical"
                placeholder="Minimal 6 karakter"
              />
            </div>

            <div>
              <label class="label-clinical">Role Akses <span class="text-rose-500">*</span></label>
              <select v-model="form.role_id" required class="input-clinical font-medium">
                <option :value="null" disabled>Pilih role akses...</option>
                <option v-for="r in roles" :key="r.id" :value="r.id">
                  {{ r.role }}
                </option>
              </select>
            </div>

            <div class="pt-2 border-t border-slate-200">
              <label class="label-clinical">Email Resmi Staf</label>
              <input v-model="form.email" type="email" class="input-clinical" placeholder="staf@klinik.com" />
            </div>

            <!-- OTP VERIFICATION BLOCK -->
            <div v-if="form.email" class="bg-white border border-slate-200 rounded-lg p-2.5 space-y-2 text-xs">
              <div class="flex items-center justify-between">
                <span class="font-semibold text-slate-700 text-[11.5px]">Status Verifikasi:</span>
                <span v-if="emailVerified" class="text-emerald-700 font-bold text-[11px]">✔ Terverifikasi</span>
                <span v-else class="text-rose-600 font-semibold text-[11px]">Belum diverifikasi</span>
              </div>

              <div v-if="!emailVerified" class="space-y-1.5">
                <div class="flex gap-1.5">
                  <input
                    v-model="otpCode"
                    placeholder="Kode OTP"
                    class="input-clinical text-center tracking-widest font-mono"
                  />
                  <button
                    type="button"
                    @click="sendOtp"
                    class="h-[34px] px-2.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-[#0F766E] border border-teal-200 text-[11px] font-semibold shrink-0 cursor-pointer"
                  >
                    Kirim OTP
                  </button>
                  <button
                    type="button"
                    @click="verifyOtp"
                    class="h-[34px] px-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold shrink-0 cursor-pointer"
                  >
                    Verifikasi
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </BaseModal>

    <!-- CONFIRM DELETE DIALOG -->
    <ConfirmDialog
      v-model="confirmDeleteModal"
      title="Hapus Akun Pengguna"
      :message="deleteMessage"
      confirmText="Ya, Hapus Pengguna"
      type="danger"
      :loading="deleting"
      @confirm="executeDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'
import BaseModal from '../components/ui/BaseModal.vue'
import ConfirmDialog from '../components/ui/ConfirmDialog.vue'
import StatusBadge from '../components/ui/StatusBadge.vue'

const toast = useToast()

const data = ref([])
const jenisUser = ref([])
const roles = ref([])
const jenisKelamin = ref([])

const search = ref('')
const dialog = ref(false)
const isEdit = ref(false)
const saving = ref(false)
const deleting = ref(false)

const confirmDeleteModal = ref(false)
const selectedUserToDelete = ref(null)
const deleteMessage = ref('')

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
  try {
    const [uRes, jRes, rRes, kRes] = await Promise.all([
      api.get('/master-user'),
      api.get('/jenis-user'),
      api.get('/role'),
      api.get('/jenis-kelamin')
    ])
    data.value = uRes.data.data ?? uRes.data
    jenisUser.value = jRes.data.data ?? jRes.data
    roles.value = rRes.data.data ?? rRes.data
    jenisKelamin.value = kRes.data.data ?? kRes.data
  } catch (e) {
    console.error(e)
  }
}

const filteredData = computed(() =>
  search.value
    ? data.value.filter(d =>
        (d.nama || '').toLowerCase().includes(search.value.toLowerCase()) ||
        (d.nip || '').toLowerCase().includes(search.value.toLowerCase()) ||
        (d.userLogin?.username || '').toLowerCase().includes(search.value.toLowerCase())
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
    username: row.userLogin?.username || '',
    role_id: row.userLogin?.role?.id || null,
    password: ''
  }
  emailVerified.value = !!row.email_verified_at
  isEdit.value = true
  dialog.value = true
}

const save = async () => {
  if (!form.value.nama || !form.value.username || !form.value.role_id) {
    toast.warning('Nama, username, dan role wajib diisi')
    return
  }

  saving.value = true
  try {
    if (isEdit.value) {
      await api.put(`/master-user/${form.value.id}`, form.value)
    } else {
      await api.post('/master-user', form.value)
    }
    toast.success('User berhasil disimpan')
    dialog.value = false
    loadData()
  } catch {
    toast.error('Gagal menyimpan user')
  } finally {
    saving.value = false
  }
}

const sendOtp = async () => {
  if (!form.value.email) return
  try {
    await api.post('/otp-email/send', { email: form.value.email })
    toast.success('Kode OTP dikirim ke email')
  } catch {
    toast.error('Gagal mengirim OTP')
  }
}

const verifyOtp = async () => {
  if (!otpCode.value) return
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

const deleteRow = (row) => {
  selectedUserToDelete.value = row
  deleteMessage.value = `Apakah Anda yakin ingin menghapus user akun "${row.nama}"? Tindakan ini tidak dapat dibatalkan.`
  confirmDeleteModal.value = true
}

const executeDelete = async () => {
  if (!selectedUserToDelete.value) return
  deleting.value = true
  try {
    await api.delete(`/master-user/${selectedUserToDelete.value.id}`)
    toast.success('User berhasil dihapus')
    confirmDeleteModal.value = false
    loadData()
  } catch (err) {
    toast.error('Gagal menghapus user')
  } finally {
    deleting.value = false
  }
}
</script>
