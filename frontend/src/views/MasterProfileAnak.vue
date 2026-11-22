<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-users"></i>
        </div>
        <div>
          <h1 class="text-[15px] font-bold text-[#0F172A] leading-tight">Master Profile Anak</h1>
          <p class="text-[11.5px] text-[#64748B] mt-0.5">Kelola data rekam identitas anak dan penautan orang tua/wali</p>
        </div>
      </div>

      <button
        type="button"
        @click="openAdd"
        class="btn-primary-clinical text-[12.5px] shrink-0"
      >
        <i class="pi pi-plus text-xs"></i>
        <span>Tambah Anak</span>
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
            placeholder="Cari nama anak..."
            class="w-full h-[34px] text-[12.5px] pl-8 pr-2.5 border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 transition bg-white text-slate-800 placeholder:text-slate-400"
          />
        </div>
        <span class="text-[11.5px] text-slate-500 font-medium">
          Total <strong>{{ filteredData.length }}</strong> data terdaftar
        </span>
      </div>

      <!-- TABLE BODY -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="min-w-full text-[12.5px] text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2 px-3 text-center w-12">No</th>
              <th class="py-2 px-3">Nama Anak</th>
              <th class="py-2 px-3">Orang Tua</th>
              <th class="py-2 px-3">Jenis Kelamin</th>
              <th class="py-2 px-3">Agama</th>
              <th class="py-2 px-3">Umur</th>
              <th class="py-2 px-3 text-center">Status</th>
              <th class="py-2 px-3 text-center w-24">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(item, index) in filteredData"
              :key="item.id"
              class="hover:bg-[#F0F6F8]/60 transition-colors"
            >
              <td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]">{{ index + 1 }}</td>
              <td class="py-2 px-3 font-semibold text-slate-900">
                {{ item.nama_anak }}
                <span v-if="item.NIK" class="block text-[10.5px] text-slate-400 font-mono">NIK: {{ item.NIK }}</span>
              </td>
              <td class="py-2 px-3 text-slate-700">{{ item.orangTua?.nama || '-' }}</td>
              <td class="py-2 px-3 text-slate-700">{{ item.jenisKelamin?.nama || (item.jenis_kelamin_id === 1 ? 'Laki-laki' : 'Perempuan') }}</td>
              <td class="py-2 px-3 text-slate-700">{{ item.agama?.nama || '-' }}</td>
              <td class="py-2 px-3 tabular-nums font-medium text-slate-800">{{ item.umur ? item.umur + ' Tahun' : '-' }}</td>
              <td class="py-2 px-3 text-center">
                <StatusBadge
                  :label="item.status_aktif ? 'Aktif' : 'Nonaktif'"
                  :type="item.status_aktif ? 'success' : 'neutral'"
                />
              </td>
              <td class="py-2 px-3 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    type="button"
                    @click="openEdit(item)"
                    class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 flex items-center justify-center transition shadow-2xs"
                    title="Edit Profil"
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
            <tr v-if="filteredData.length === 0">
              <td colspan="8" class="py-12 text-center text-slate-400">
                <div class="flex flex-col items-center justify-center">
                  <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                    <i class="pi pi-folder-open text-lg"></i>
                  </div>
                  <h4 class="text-[13px] font-semibold text-slate-800">Data Tidak Ditemukan</h4>
                  <p class="text-[11.5px] text-slate-500 mt-0.5">Tidak ada profil anak yang sesuai pencarian.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- TABLE FOOTER -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] text-slate-500 flex items-center justify-between">
        <span>Menampilkan {{ filteredData.length }} anak</span>
        <span>SIMRS Rekam Medis Anak</span>
      </div>
    </div>

    <!-- MODAL FORM (COMPACT TIER 'md': max-w-[640px]) -->
    <BaseModal
      v-model="dialog"
      :title="isEdit ? 'Edit Data Anak' : 'Tambah Data Anak'"
      :subtitle="isEdit ? 'Perbarui informasi rekam profil anak' : 'Lengkapi informasi profil anak baru'"
      icon="pi pi-user-plus"
      size="md"
      :loading="saving"
      @submit="save"
    >
      <form @submit.prevent="save" class="grid grid-cols-1 sm:grid-cols-2 gap-x-3.5 gap-y-2.5">
        <div>
          <label class="label-clinical">Nama Anak <span class="text-rose-500">*</span></label>
          <input v-model="form.nama_anak" required class="input-clinical" placeholder="Nama lengkap anak" />
        </div>

        <div>
          <label class="label-clinical">Orang Tua / Wali <span class="text-rose-500">*</span></label>
          <select v-model="form.id_orang_tua" required class="input-clinical">
            <option :value="null" disabled>Pilih orang tua...</option>
            <option v-for="ot in orangTua" :key="ot.id" :value="ot.id">
              {{ ot.nama }}
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
          <label class="label-clinical">Agama</label>
          <select v-model="form.agama_id" class="input-clinical">
            <option :value="null" disabled>Pilih agama...</option>
            <option v-for="ag in agama" :key="ag.id" :value="ag.id">
              {{ ag.nama }}
            </option>
          </select>
        </div>

        <div>
          <label class="label-clinical">NIK Pasien Anak</label>
          <input v-model="form.NIK" class="input-clinical" placeholder="16 digit NIK" />
        </div>

        <div>
          <label class="label-clinical">Tanggal Lahir</label>
          <input type="date" v-model="form.tanggal_lahir" class="input-clinical" />
        </div>

        <div>
          <label class="label-clinical">Tempat Lahir</label>
          <input v-model="form.tempat_lahir" class="input-clinical" placeholder="Kota kelahiran" />
        </div>

        <div>
          <label class="label-clinical">Umur (Otomatis)</label>
          <input
            type="text"
            :value="form.umur !== null ? form.umur + ' Tahun' : '-'"
            readonly
            class="input-clinical bg-slate-100 cursor-not-allowed text-slate-600 font-medium"
          />
        </div>

        <div>
          <label class="label-clinical">No. HP Orang Tua</label>
          <input v-model="form.no_hp_orang_tua" class="input-clinical" placeholder="08xxxxxxxxxx" />
        </div>

        <div>
          <label class="label-clinical">Status</label>
          <select v-model="form.status_aktif" class="input-clinical">
            <option :value="true">Aktif</option>
            <option :value="false">Nonaktif</option>
          </select>
        </div>

        <div class="sm:col-span-2">
          <label class="label-clinical">Alamat Domisili</label>
          <textarea
            v-model="form.alamat"
            rows="2"
            class="input-clinical h-auto py-2 resize-none"
            placeholder="Alamat tempat tinggal lengkap..."
          ></textarea>
        </div>
      </form>
    </BaseModal>

    <!-- CONFIRM DELETE DIALOG -->
    <ConfirmDialog
      v-model="confirmDeleteModal"
      title="Hapus Data Anak"
      :message="`Apakah Anda yakin ingin menghapus data anak '${itemToDelete?.nama_anak}'? Data rekam medis terkait tidak dapat dikembalikan.`"
      type="danger"
      confirmText="Ya, Hapus"
      :loading="deleting"
      @confirm="executeDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'
import BaseModal from '../components/ui/BaseModal.vue'
import ConfirmDialog from '../components/ui/ConfirmDialog.vue'
import StatusBadge from '../components/ui/StatusBadge.vue'

const toast = useToast()

const data = ref([])
const orangTua = ref([])
const jenisKelamin = ref([])
const agama = ref([])

const search = ref('')
const dialog = ref(false)
const isEdit = ref(false)
const saving = ref(false)

const confirmDeleteModal = ref(false)
const itemToDelete = ref(null)
const deleting = ref(false)

function hitungUmur(tanggal) {
  if (!tanggal) return null
  const today = new Date()
  const birth = new Date(tanggal)
  let umur = today.getFullYear() - birth.getFullYear()
  const m = today.getMonth() - birth.getMonth()
  if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
    umur--
  }
  return umur
}

const formDefault = {
  nama_anak: '',
  id_orang_tua: null,
  jenis_kelamin_id: null,
  agama_id: null,
  NIK: '',
  alamat: '',
  umur: null,
  tanggal_lahir: '',
  tempat_lahir: '',
  no_hp_orang_tua: '',
  status_aktif: true
}

const form = ref({ ...formDefault })

onMounted(loadData)

async function loadData() {
  try {
    const anakRes = await api.get('/profile-anak')
    const userRes = await api.get('/user-profile')

    const allData = anakRes.data.data ?? anakRes.data
    const profiles = userRes.data.data ?? userRes.data

    jenisKelamin.value = (await api.get('/jenis-kelamin')).data
    agama.value = (await api.get('/agama')).data

    data.value = allData.filter(item => {
      const parent = profiles.find(p => p.id === item.id_orang_tua)
      if (parent && Number(parent.jenis_user_id) === 8) {
        item.orangTua = parent
        return true
      }
      return false
    })

    orangTua.value = profiles.filter(p => Number(p.jenis_user_id) === 8)
  } catch (e) {
    console.error(e)
  }
}

const filteredData = computed(() =>
  !search.value
    ? data.value
    : data.value.filter(d =>
        d.nama_anak.toLowerCase().includes(search.value.toLowerCase())
      )
)

const openAdd = () => {
  form.value = { ...formDefault }
  isEdit.value = false
  dialog.value = true
}

const openEdit = (row) => {
  form.value = { ...row }
  isEdit.value = true
  dialog.value = true
}

const save = async () => {
  if (!form.value.nama_anak || !form.value.id_orang_tua) {
    toast.warning('Nama anak dan orang tua wajib diisi.')
    return
  }

  saving.value = true
  try {
    if (isEdit.value) {
      await api.put(`/profile-anak/${form.value.id}`, form.value)
    } else {
      await api.post('/profile-anak', form.value)
    }
    toast.success('Data berhasil disimpan')
    dialog.value = false
    loadData()
  } catch (err) {
    toast.error('Gagal menyimpan data')
    console.error(err)
  } finally {
    saving.value = false
  }
}

const confirmDeleteAction = (row) => {
  itemToDelete.value = row
  confirmDeleteModal.value = true
}

const executeDelete = async () => {
  if (!itemToDelete.value) return
  deleting.value = true
  try {
    await api.delete(`/profile-anak/${itemToDelete.value.id}`)
    toast.success('Data berhasil dihapus')
    confirmDeleteModal.value = false
    loadData()
  } catch (e) {
    toast.error('Gagal menghapus data')
  } finally {
    deleting.value = false
  }
}

watch(
  () => form.value.tanggal_lahir,
  val => {
    form.value.umur = hitungUmur(val)
  }
)
</script>
