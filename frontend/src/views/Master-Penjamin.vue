<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-shield"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Master Penjamin & Asuransi</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Kelola mitra penjamin BPJS Kesehatan, asuransi swasta, dan korporat penjamin biaya terapi anak</p>
        </div>
      </div>

      <button
        type="button"
        @click="openAddModal"
        class="btn-primary-clinical text-xs py-2 px-3.5 shrink-0"
      >
        <i class="pi pi-plus text-xs"></i>
        <span>Tambah Penjamin</span>
      </button>
    </div>

    <!-- METRICS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0">
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Mitra Penjamin</span>
        <h3 class="text-lg font-bold text-[#0F172A] tabular-nums mt-0.5">{{ guarantors.length }}</h3>
      </div>
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-semibold text-sky-700 uppercase tracking-wider block">BPJS Kesehatan</span>
        <h3 class="text-lg font-bold text-sky-800 tabular-nums mt-0.5">
          {{ guarantors.filter(g => g.tipe === 'bpjs').length }}
        </h3>
      </div>
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-semibold text-teal-700 uppercase tracking-wider block">Asuransi Swasta</span>
        <h3 class="text-lg font-bold text-teal-800 tabular-nums mt-0.5">
          {{ guarantors.filter(g => g.tipe === 'asuransi_swasta').length }}
        </h3>
      </div>
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider block">Status Aktif PKS</span>
        <h3 class="text-lg font-bold text-emerald-800 tabular-nums mt-0.5">
          {{ guarantors.filter(g => g.status_aktif !== false).length }}
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
              placeholder="Cari kode atau nama penjamin..."
              class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
            />
          </div>
          <select
            v-model="filterType"
            class="text-xs border border-slate-300 rounded-lg px-3 py-1.5 bg-white text-slate-700 outline-none focus:border-[#0F766E]"
          >
            <option value="">Semua Tipe</option>
            <option value="bpjs">BPJS Kesehatan</option>
            <option value="asuransi_swasta">Asuransi Swasta</option>
            <option value="corporate">Korporat / Perusahaan</option>
          </select>
        </div>

        <span class="text-xs text-slate-500 font-medium">
          Ditemukan <strong>{{ filteredGuarantors.length }}</strong> penjamin
        </span>
      </div>

      <!-- TABLE BODY -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-[12.5px] text-left text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3 w-12 text-center">No</th>
              <th class="py-2.5 px-3">Kode & Nama Penjamin</th>
              <th class="py-2.5 px-3">Tipe Penjamin</th>
              <th class="py-2.5 px-3">Kontak Klaim</th>
              <th class="py-2.5 px-3">Alamat Kantor</th>
              <th class="py-2.5 px-3 text-center">Status</th>
              <th class="py-2.5 px-3 text-right w-24">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loading" class="text-center">
              <td colspan="7" class="py-10 text-slate-400">
                <i class="pi pi-spin pi-spinner text-xl text-[#0F766E]"></i>
                <p class="mt-2 text-xs">Memuat data penjamin...</p>
              </td>
            </tr>
            <tr v-else-if="filteredGuarantors.length === 0" class="text-center">
              <td colspan="7" class="py-8 text-slate-400 text-xs">Tidak ada data penjamin ditemukan.</td>
            </tr>
            <tr v-for="(g, index) in filteredGuarantors" :key="g.id" class="hover:bg-[#F0F6F8]/60 transition">
              <td class="py-2 px-3 text-center font-mono text-slate-400 text-xs">{{ index + 1 }}</td>
              <td class="py-2 px-3">
                <p class="font-bold text-slate-900 leading-tight">{{ g.nama_penjamin }}</p>
                <p class="text-[11px] font-mono text-[#0F766E] font-semibold mt-0.5">{{ g.kode_penjamin }}</p>
              </td>
              <td class="py-2 px-3">
                <span
                  class="px-2 py-0.5 rounded-full text-[11px] font-semibold capitalize"
                  :class="{
                    'bg-sky-50 text-sky-700 border border-sky-200': g.tipe === 'bpjs',
                    'bg-teal-50 text-teal-700 border border-teal-200': g.tipe === 'asuransi_swasta',
                    'bg-indigo-50 text-indigo-700 border border-indigo-200': g.tipe === 'corporate'
                  }"
                >
                  {{ (g.tipe || '').replace('_', ' ') }}
                </span>
              </td>
              <td class="py-2 px-3 text-xs">
                <p class="font-mono text-slate-800 font-semibold">{{ g.kontak_klaim || '-' }}</p>
                <p class="text-slate-500 text-[11px]">{{ g.email_klaim || '-' }}</p>
              </td>
              <td class="py-2 px-3 text-xs text-slate-600 max-w-xs truncate">
                {{ g.alamat || '-' }}
              </td>
              <td class="py-2 px-3 text-center">
                <StatusBadge :status="g.status_aktif !== false ? 'Aktif PKS' : 'Non-Aktif'" />
              </td>
              <td class="py-2 px-3 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    type="button"
                    @click="openEditModal(g)"
                    class="w-7 h-7 rounded flex items-center justify-center text-slate-500 hover:text-[#0F766E] hover:bg-[#E6F2F5] transition cursor-pointer"
                    title="Edit Penjamin"
                  >
                    <i class="pi pi-pencil text-xs"></i>
                  </button>
                  <button
                    type="button"
                    @click="confirmDeactivate(g)"
                    class="w-7 h-7 rounded flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                    title="Nonaktifkan"
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

    <!-- MODAL FORM TAMBAH / EDIT PENJAMIN -->
    <BaseModal
      v-model="showModal"
      :title="isEdit ? 'Edit Mitra Penjamin' : 'Tambah Mitra Penjamin Baru'"
      subtitle="Kelola data rekanan asuransi kesehatan atau korporat"
      size="md"
    >
      <form @submit.prevent="saveGuarantor" id="formPenjamin" class="space-y-3 text-xs">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-[12px] font-medium text-slate-700 mb-1">Kode Penjamin</label>
            <input v-model="form.kode_penjamin" required class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] font-mono" placeholder="mis. BPJS-01" />
          </div>
          <div>
            <label class="block text-[12px] font-medium text-slate-700 mb-1">Tipe Penjamin</label>
            <select v-model="form.tipe" class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]">
              <option value="bpjs">BPJS Kesehatan</option>
              <option value="asuransi_swasta">Asuransi Swasta</option>
              <option value="corporate">Korporat / Rekanan</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Nama Resmi Penjamin</label>
          <input v-model="form.nama_penjamin" required class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]" placeholder="mis. PT Asuransi Allianz Life Indonesia" />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-[12px] font-medium text-slate-700 mb-1">No. Telepon / Call Center Klaim</label>
            <input v-model="form.kontak_klaim" class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] font-mono" placeholder="mis. 1500136" />
          </div>
          <div>
            <label class="block text-[12px] font-medium text-slate-700 mb-1">Email Layanan Klaim</label>
            <input v-model="form.email_klaim" type="email" class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]" placeholder="mis. claims@allianz.co.id" />
          </div>
        </div>

        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Alamat Kantor / Surat Menyurat</label>
          <textarea v-model="form.alamat" rows="2" class="w-full px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]" placeholder="Alamat lengkap mitra..."></textarea>
        </div>

        <div class="flex items-center gap-2 pt-1">
          <input v-model="form.status_aktif" type="checkbox" id="status_aktif" class="w-4 h-4 text-[#0F766E] border-slate-300 rounded cursor-pointer" />
          <label for="status_aktif" class="text-xs text-slate-700 font-medium cursor-pointer">Status Perjanjian Kerjasama (PKS) Aktif</label>
        </div>
      </form>

      <template #footer>
        <button type="button" @click="showModal = false" class="h-[34px] px-3.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-[12.5px] font-medium transition cursor-pointer">
          Batal
        </button>
        <button type="submit" form="formPenjamin" class="h-[34px] px-3.5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-[12.5px] font-medium transition cursor-pointer">
          Simpan Penjamin
        </button>
      </template>
    </BaseModal>

    <!-- CONFIRM DEACTIVATE DIALOG -->
    <ConfirmDialog
      v-model="confirmDeactivateModal"
      title="Nonaktifkan Mitra Penjamin"
      :message="`Apakah Anda yakin ingin menonaktifkan mitra penjamin '${selectedGuarantorToDeactivate?.nama_penjamin}'?`"
      confirmText="Ya, Nonaktifkan"
      type="danger"
      @confirm="executeDeactivate"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../axios'
import BaseModal from '@/components/ui/BaseModal.vue'
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const guarantors = ref([])
const loading = ref(false)
const searchQuery = ref('')
const filterType = ref('')

const showModal = ref(false)
const isEdit = ref(false)
const confirmDeactivateModal = ref(false)
const selectedGuarantorToDeactivate = ref(null)

const form = ref({
  id: null,
  kode_penjamin: '',
  nama_penjamin: '',
  tipe: 'asuransi_swasta',
  kontak_klaim: '',
  email_klaim: '',
  alamat: '',
  status_aktif: true,
})

const fetchGuarantors = async () => {
  loading.value = true
  try {
    const res = await api.get('/pediatric-workflow/guarantors')
    guarantors.value = res.data || []
  } catch (err) {
    console.error('Error fetching guarantors:', err)
  } finally {
    loading.value = false
  }
}

const filteredGuarantors = computed(() => {
  return guarantors.value.filter(g => {
    const q = searchQuery.value.toLowerCase().trim()
    const matchesQuery = !q || (g.nama_penjamin || '').toLowerCase().includes(q) || (g.kode_penjamin || '').toLowerCase().includes(q)
    const matchesType = !filterType.value || g.tipe === filterType.value
    return matchesQuery && matchesType
  })
})

const openAddModal = () => {
  isEdit.value = false
  form.value = {
    id: null,
    kode_penjamin: 'ASR-' + Math.floor(100 + Math.random() * 900),
    nama_penjamin: '',
    tipe: 'asuransi_swasta',
    kontak_klaim: '',
    email_klaim: '',
    alamat: '',
    status_aktif: true,
  }
  showModal.value = true
}

const openEditModal = (g) => {
  isEdit.value = true
  form.value = { ...g }
  showModal.value = true
}

const saveGuarantor = async () => {
  try {
    const res = await api.post('/pediatric-workflow/guarantors', form.value)
    toast.success(res.data.message || 'Data penjamin berhasil disimpan')
    showModal.value = false
    fetchGuarantors()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menyimpan penjamin')
  }
}

const confirmDeactivate = (g) => {
  selectedGuarantorToDeactivate.value = g
  confirmDeactivateModal.value = true
}

const executeDeactivate = async () => {
  if (!selectedGuarantorToDeactivate.value) return
  try {
    await api.delete(`/pediatric-workflow/guarantors/${selectedGuarantorToDeactivate.value.id}`)
    toast.success('Mitra penjamin berhasil dinonaktifkan')
    confirmDeactivateModal.value = false
    fetchGuarantors()
  } catch (err) {
    toast.error('Gagal menonaktifkan penjamin')
  }
}

onMounted(fetchGuarantors)
</script>
