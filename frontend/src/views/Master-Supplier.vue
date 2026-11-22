<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-truck"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Master Supplier & Distributor Rekanan</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Kelola distributor rekanan farmasi, suplemen anak, dan penyedia perlengkapan terapi sensori motorik</p>
        </div>
      </div>

      <button
        type="button"
        @click="openAddModal"
        class="btn-primary-clinical text-xs py-2 px-3.5 shrink-0"
      >
        <i class="pi pi-plus text-xs"></i>
        <span>Tambah Supplier</span>
      </button>
    </div>

    <!-- METRICS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0">
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Rekanan</span>
        <h3 class="text-lg font-bold text-[#0F172A] tabular-nums mt-0.5">{{ suppliers.length }}</h3>
      </div>
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-semibold text-teal-700 uppercase tracking-wider block">Supplier Aktif</span>
        <h3 class="text-lg font-bold text-teal-800 tabular-nums mt-0.5">
          {{ suppliers.filter(s => s.status_aktif !== false).length }}
        </h3>
      </div>
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider block">Email Terdata</span>
        <h3 class="text-lg font-bold text-amber-800 tabular-nums mt-0.5">
          {{ suppliers.filter(s => s.email).length }}
        </h3>
      </div>
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider block">Non-Aktif</span>
        <h3 class="text-lg font-bold text-rose-800 tabular-nums mt-0.5">
          {{ suppliers.filter(s => s.status_aktif === false).length }}
        </h3>
      </div>
    </div>

    <!-- MAIN TABLE CARD -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">
      <!-- TOOLBAR -->
      <div class="p-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shrink-0">
        <div class="flex items-center gap-2.5 flex-wrap">
          <div class="relative w-72">
            <i class="pi pi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari nama supplier, kontak, atau telepon..."
              class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
            />
          </div>
          <select
            v-model="filterStatus"
            class="text-xs border border-slate-300 rounded-lg px-3 py-1.5 bg-white text-slate-700 outline-none focus:border-[#0F766E]"
          >
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Non-Aktif</option>
          </select>
        </div>

        <span class="text-xs text-slate-500 font-medium">
          Ditemukan <strong>{{ filteredSuppliers.length }}</strong> supplier
        </span>
      </div>

      <!-- TABLE BODY -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-[12.5px] text-left text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3 w-12 text-center">No</th>
              <th class="py-2.5 px-3">Nama Supplier & Vendor</th>
              <th class="py-2.5 px-3">Kontak Person (PIC)</th>
              <th class="py-2.5 px-3">No. Telepon / WhatsApp</th>
              <th class="py-2.5 px-3">Email Resmi</th>
              <th class="py-2.5 px-3">Alamat Gudang / Kantor</th>
              <th class="py-2.5 px-3 text-center">Status</th>
              <th class="py-2.5 px-3 text-right w-24">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loading" class="text-center">
              <td colspan="8" class="py-10 text-slate-400">
                <i class="pi pi-spin pi-spinner text-xl text-[#0F766E]"></i>
                <p class="mt-2 text-xs">Memuat data supplier...</p>
              </td>
            </tr>
            <tr v-else-if="filteredSuppliers.length === 0" class="text-center">
              <td colspan="8" class="py-8 text-slate-400 text-xs">Tidak ada data supplier ditemukan.</td>
            </tr>
            <tr v-for="(s, index) in filteredSuppliers" :key="s.id" class="hover:bg-[#F0F6F8]/60 transition">
              <td class="py-2.5 px-3 text-center font-mono text-slate-400 text-xs">{{ index + 1 }}</td>
              <td class="py-2.5 px-3">
                <div class="flex items-center gap-2">
                  <div class="w-6 h-6 rounded bg-[#E6F2F5] text-[#0F766E] flex items-center justify-center font-bold text-xs shrink-0">
                    {{ s.nama_supplier ? s.nama_supplier.charAt(0) : 'S' }}
                  </div>
                  <div>
                    <p class="font-bold text-slate-900 leading-tight">{{ s.nama_supplier }}</p>
                    <span class="text-[11px] text-slate-400">ID: SUP-{{ String(s.id).padStart(3, '0') }}</span>
                  </div>
                </div>
              </td>
              <td class="py-2.5 px-3">
                <span class="font-medium text-slate-800">{{ s.kontak_person || '-' }}</span>
              </td>
              <td class="py-2.5 px-3">
                <span class="font-mono text-xs text-slate-700">{{ s.no_telp || '-' }}</span>
              </td>
              <td class="py-2.5 px-3 text-xs text-slate-600">
                {{ s.email || '-' }}
              </td>
              <td class="py-2.5 px-3 text-xs text-slate-600 max-w-xs truncate">
                {{ s.alamat || '-' }}
              </td>
              <td class="py-2.5 px-3 text-center">
                <span
                  class="px-2 py-0.5 rounded-full text-[10.5px] font-semibold"
                  :class="s.status_aktif !== false ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                >
                  {{ s.status_aktif !== false ? 'Aktif' : 'Non-Aktif' }}
                </span>
              </td>
              <td class="py-2.5 px-3 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    type="button"
                    @click="openEditModal(s)"
                    class="p-1 rounded text-slate-500 hover:text-[#0F766E] hover:bg-slate-100 transition"
                    title="Edit Supplier"
                  >
                    <i class="pi pi-pencil text-xs"></i>
                  </button>
                  <button
                    type="button"
                    @click="deleteSupplier(s)"
                    class="p-1 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                    title="Hapus Supplier"
                  >
                    <i class="pi pi-trash text-xs"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL FORM TAMBAH / EDIT SUPPLIER -->
    <teleport to="body">
      <div
        v-if="showModal"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4"
        @click.self="showModal = false"
      >
        <div class="w-full max-w-[560px] max-h-[88dvh] flex flex-col rounded-xl bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] overflow-hidden animate-fadeIn">
          <!-- Modal Header -->
          <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-truck text-sm text-[#2DD4BF]"></i>
              <h3 class="font-semibold text-[13.5px]">
                {{ isEdit ? 'Edit Data Supplier' : 'Tambah Supplier Rekanan Baru' }}
              </h3>
            </div>
            <button
              type="button"
              @click="showModal = false"
              class="w-6 h-6 rounded flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition text-xs"
            >
              ✕
            </button>
          </div>

          <!-- Modal Form Body -->
          <form @submit.prevent="saveSupplier" class="flex-1 min-h-0 flex flex-col">
            <div class="p-4 flex-1 min-h-0 overflow-y-auto custom-scrollbar space-y-3 text-[12.5px]">
              <div>
                <label class="block text-[12px] font-medium text-slate-700 mb-1">
                  Nama Supplier / Perusahaan <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="form.nama_supplier"
                  type="text"
                  required
                  placeholder="Contoh: PT Medika Jaya Farma"
                  class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
                />
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label class="block text-[12px] font-medium text-slate-700 mb-1">Kontak Person (PIC)</label>
                  <input
                    v-model="form.kontak_person"
                    type="text"
                    placeholder="Nama PIC vendor"
                    class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
                  />
                </div>
                <div>
                  <label class="block text-[12px] font-medium text-slate-700 mb-1">No. Telp / WhatsApp</label>
                  <input
                    v-model="form.no_telp"
                    type="text"
                    placeholder="0812-xxxx-xxxx"
                    class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
                  />
                </div>
              </div>

              <div>
                <label class="block text-[12px] font-medium text-slate-700 mb-1">Email Resmi</label>
                <input
                  v-model="form.email"
                  type="email"
                  placeholder="vendor@domain.com"
                  class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
                />
              </div>

              <div>
                <label class="block text-[12px] font-medium text-slate-700 mb-1">Alamat Kantor / Gudang</label>
                <textarea
                  v-model="form.alamat"
                  rows="2"
                  placeholder="Alamat lengkap supplier..."
                  class="w-full px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
                ></textarea>
              </div>

              <div class="flex items-center gap-2 pt-1">
                <input
                  type="checkbox"
                  id="status_aktif_supplier"
                  v-model="form.status_aktif"
                  class="rounded border-slate-300 text-[#0F766E] focus:ring-[#0F766E]"
                />
                <label for="status_aktif_supplier" class="text-[12px] font-medium text-slate-700 select-none cursor-pointer">
                  Supplier Aktif (Dapat dipilih dalam Purchase Order & Penerimaan Barang)
                </label>
              </div>
            </div>

            <!-- Actions Footer -->
            <div class="px-4 py-2.5 bg-slate-50/95 border-t border-slate-200/80 flex items-center justify-end gap-2 shrink-0">
              <button
                type="button"
                @click="showModal = false"
                class="btn-secondary h-[34px] px-3.5 text-xs"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="btn-primary h-[34px] px-4 text-xs font-semibold"
              >
                <i v-if="saving" class="pi pi-spin pi-spinner text-xs mr-1"></i>
                <span>{{ isEdit ? 'Simpan Perubahan' : 'Tambah Supplier' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </teleport>

    <!-- CONFIRM DELETE DIALOG -->
    <ConfirmDialog
      v-model="showConfirmDelete"
      title="Hapus Data Supplier"
      :message="deleteMessage"
      type="danger"
      confirmText="Ya, Hapus"
      cancelText="Batal"
      :loading="deleting"
      @confirm="executeDeleteSupplier"
      @cancel="showConfirmDelete = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue'

const toast = useToast()
const suppliers = ref([])
const loading = ref(false)
const saving = ref(false)
const deleting = ref(false)
const searchQuery = ref('')
const filterStatus = ref('')
const showModal = ref(false)
const isEdit = ref(false)
const currentId = ref(null)

const showConfirmDelete = ref(false)
const supplierToDelete = ref(null)
const deleteMessage = computed(() => {
  return supplierToDelete.value
    ? `Anda yakin ingin menghapus data supplier "${supplierToDelete.value.nama_supplier}"?`
    : 'Anda yakin ingin menghapus data supplier ini?'
})

const form = ref({
  nama_supplier: '',
  kontak_person: '',
  no_telp: '',
  email: '',
  alamat: '',
  status_aktif: true,
})

const fetchSuppliers = async () => {
  loading.value = true
  try {
    const res = await api.get('/farmasi/suppliers')
    suppliers.value = Array.isArray(res.data) ? res.data : []
  } catch (err) {
    console.error('Error fetching suppliers:', err)
  } finally {
    loading.value = false
  }
}

const filteredSuppliers = computed(() => {
  let list = suppliers.value

  if (filterStatus.value === 'aktif') {
    list = list.filter(s => s.status_aktif !== false)
  } else if (filterStatus.value === 'nonaktif') {
    list = list.filter(s => s.status_aktif === false)
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    list = list.filter(s =>
      (s.nama_supplier || '').toLowerCase().includes(q) ||
      (s.kontak_person || '').toLowerCase().includes(q) ||
      (s.no_telp || '').toLowerCase().includes(q) ||
      (s.email || '').toLowerCase().includes(q)
    )
  }

  return list
})

const openAddModal = () => {
  isEdit.value = false
  currentId.value = null
  form.value = {
    nama_supplier: '',
    kontak_person: '',
    no_telp: '',
    email: '',
    alamat: '',
    status_aktif: true,
  }
  showModal.value = true
}

const openEditModal = (item) => {
  isEdit.value = true
  currentId.value = item.id
  form.value = {
    nama_supplier: item.nama_supplier,
    kontak_person: item.kontak_person || '',
    no_telp: item.no_telp || '',
    email: item.email || '',
    alamat: item.alamat || '',
    status_aktif: item.status_aktif !== false,
  }
  showModal.value = true
}

const saveSupplier = async () => {
  saving.value = true
  try {
    if (isEdit.value) {
      await api.put(`/farmasi/suppliers/${currentId.value}`, form.value)
      toast.success('Data supplier berhasil diperbarui')
    } else {
      await api.post('/farmasi/suppliers', form.value)
      toast.success('Supplier rekanan baru berhasil ditambahkan')
    }
    showModal.value = false
    fetchSuppliers()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Terjadi kesalahan sistem saat menyimpan.')
  } finally {
    saving.value = false
  }
}

const deleteSupplier = (item) => {
  supplierToDelete.value = item
  showConfirmDelete.value = true
}

const executeDeleteSupplier = async () => {
  if (!supplierToDelete.value) return
  deleting.value = true
  try {
    await api.delete(`/farmasi/suppliers/${supplierToDelete.value.id}`)
    toast.success('Data supplier telah dihapus.')
    showConfirmDelete.value = false
    supplierToDelete.value = null
    fetchSuppliers()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menghapus supplier.')
  } finally {
    deleting.value = false
  }
}

onMounted(fetchSuppliers)
</script>
