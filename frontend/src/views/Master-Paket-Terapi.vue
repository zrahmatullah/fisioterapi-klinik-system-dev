<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-box"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Master Paket Terapi & Bundling Sesi</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Kelola paket terapi multi-sesi (4x, 8x, 12x sesi), kuota kunjungan, dan masa aktif untuk pasien umum</p>
        </div>
      </div>

      <button
        type="button"
        @click="openAddModal"
        class="btn-primary-clinical text-xs py-2 px-3.5 shrink-0"
      >
        <i class="pi pi-plus text-xs"></i>
        <span>Tambah Paket Baru</span>
      </button>
    </div>

    <!-- METRICS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0">
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Paket Tersedia</span>
        <h3 class="text-lg font-bold text-[#0F172A] tabular-nums mt-0.5">{{ packages.length }} Paket</h3>
      </div>
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-semibold text-teal-700 uppercase tracking-wider block">Paket Paling Populer</span>
        <h3 class="text-sm font-bold text-teal-800 mt-0.5 truncate">Paket 8x & 12x Sesi</h3>
      </div>
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider block">Masa Berlaku Kuota</span>
        <h3 class="text-sm font-bold text-amber-800 mt-0.5">60 - 120 Hari</h3>
      </div>
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider block">Status Aktif Katalog</span>
        <h3 class="text-lg font-bold text-emerald-800 tabular-nums mt-0.5">
          {{ packages.filter(p => p.status_aktif !== false).length }} Aktif
        </h3>
      </div>
    </div>

    <!-- MAIN TABLE CARD -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">
      <!-- TOOLBAR -->
      <div class="p-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shrink-0">
        <div class="relative w-full sm:w-80">
          <i class="pi pi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari nama paket atau layanan..."
            class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
          />
        </div>

        <span class="text-xs text-slate-500 font-medium">
          Menampilkan <strong>{{ filteredPackages.length }}</strong> paket terapi
        </span>
      </div>

      <!-- TABLE BODY -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-[12.5px] text-left text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3 w-12 text-center">No</th>
              <th class="py-2.5 px-3">Nama Paket Terapi</th>
              <th class="py-2.5 px-3">Layanan Disiplin</th>
              <th class="py-2.5 px-3 text-center">Jumlah Sesi</th>
              <th class="py-2.5 px-3">Harga Bundling</th>
              <th class="py-2.5 px-3">Biaya / Sesi</th>
              <th class="py-2.5 px-3 text-center">Masa Aktif</th>
              <th class="py-2.5 px-3 text-center">Status</th>
              <th class="py-2.5 px-3 text-right w-24">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loading" class="text-center">
              <td colspan="9" class="py-10 text-slate-400">
                <i class="pi pi-spin pi-spinner text-xl text-[#0F766E]"></i>
                <p class="mt-2 text-xs">Memuat katalog paket terapi...</p>
              </td>
            </tr>
            <tr v-else-if="filteredPackages.length === 0" class="text-center">
              <td colspan="9" class="py-8 text-slate-400 text-xs">Belum ada paket terapi yang tersimpan.</td>
            </tr>
            <tr v-for="(p, index) in filteredPackages" :key="p.id" class="hover:bg-[#F0F6F8]/60 transition">
              <td class="py-2 px-3 text-center font-mono text-slate-400 text-xs">{{ index + 1 }}</td>
              <td class="py-2 px-3">
                <p class="font-bold text-slate-900 leading-tight">{{ p.nama_paket }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5 truncate max-w-xs">{{ p.deskripsi || '-' }}</p>
              </td>
              <td class="py-2 px-3">
                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-teal-50 text-[#0F766E] border border-teal-200">
                  {{ p.layanan?.layanan || 'Terapi Anak' }}
                </span>
              </td>
              <td class="py-2 px-3 text-center">
                <span class="font-bold text-[#0F766E] text-xs tabular-nums">{{ p.jumlah_sesi }}x</span>
                <span class="text-[10px] text-slate-500 block">Sesi</span>
              </td>
              <td class="py-2 px-3 font-bold text-slate-900 tabular-nums">
                {{ formatRupiah(p.harga_paket) }}
              </td>
              <td class="py-2 px-3 text-xs font-mono text-slate-600 tabular-nums">
                {{ formatRupiah(p.harga_paket / (p.jumlah_sesi || 1)) }}
              </td>
              <td class="py-2 px-3 text-center font-mono text-xs text-slate-700">
                {{ p.masa_berlaku_hari }} Hari
              </td>
              <td class="py-2 px-3 text-center">
                <StatusBadge :status="p.status_aktif !== false ? 'Aktif' : 'Non-Aktif'" />
              </td>
              <td class="py-2 px-3 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    type="button"
                    @click="openEditModal(p)"
                    class="w-7 h-7 rounded flex items-center justify-center text-slate-500 hover:text-[#0F766E] hover:bg-[#E6F2F5] transition cursor-pointer"
                    title="Edit Paket"
                  >
                    <i class="pi pi-pencil text-xs"></i>
                  </button>
                  <button
                    type="button"
                    @click="confirmDeactivate(p)"
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

    <!-- MODAL TAMBAH / EDIT PAKET TERAPI -->
    <BaseModal
      v-model="showModal"
      :title="isEdit ? 'Edit Paket Terapi' : 'Buat Paket Terapi Baru'"
      subtitle="Atur kuota sesi, masa aktif kuota, dan harga bundling paket terapi"
      size="md"
    >
      <form @submit.prevent="savePackage" id="formPaket" class="space-y-3.5 text-xs">
        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Nama Paket Terapi</label>
          <input v-model="form.nama_paket" required class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]" placeholder="mis. Paket Terapi Wicara 8x Sesi Rutin" />
        </div>

        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Layanan Disiplin Terapi Terkait</label>
          <select v-model="form.layanan_id" required class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]">
            <option v-for="l in services" :key="l.id" :value="l.id">{{ l.layanan }}</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-[12px] font-medium text-slate-700 mb-1">Jumlah Sesi (Kuota)</label>
            <input v-model.number="form.jumlah_sesi" type="number" min="1" required class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] font-bold" placeholder="mis. 8" />
          </div>
          <div>
            <label class="block text-[12px] font-medium text-slate-700 mb-1">Masa Berlaku Kuota (Hari)</label>
            <input v-model.number="form.masa_berlaku_hari" type="number" min="1" required class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] font-mono" placeholder="mis. 60" />
          </div>
        </div>

        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Total Harga Paket (Rp)</label>
          <input v-model.number="form.harga_paket" type="number" min="0" required class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] tabular-nums font-bold" placeholder="mis. 1200000" />
          <p v-if="form.harga_paket && form.jumlah_sesi" class="text-[11px] text-[#0F766E] font-medium mt-1">
            Setara dengan {{ formatRupiah(form.harga_paket / form.jumlah_sesi) }} per sesi kunjungan.
          </p>
        </div>

        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Deskripsi / Ketentuan Paket</label>
          <textarea v-model="form.deskripsi" rows="2" class="w-full px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]" placeholder="Syarat reschedule, batas waktu pemakaian..."></textarea>
        </div>

        <div class="flex items-center gap-2 pt-1">
          <input v-model="form.status_aktif" type="checkbox" id="status_aktif_paket" class="w-4 h-4 text-[#0F766E] border-slate-300 rounded cursor-pointer" />
          <label for="status_aktif_paket" class="text-xs text-slate-700 font-medium cursor-pointer">Paket Aktif & Dapat Dipilih Saat Pendaftaran</label>
        </div>
      </form>

      <template #footer>
        <button type="button" @click="showModal = false" class="h-[34px] px-3.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-[12.5px] font-medium transition cursor-pointer">
          Batal
        </button>
        <button type="submit" form="formPaket" class="h-[34px] px-3.5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-[12.5px] font-medium transition cursor-pointer">
          Simpan Paket
        </button>
      </template>
    </BaseModal>

    <!-- CONFIRM DEACTIVATE DIALOG -->
    <ConfirmDialog
      v-model="confirmDeactivateModal"
      title="Nonaktifkan Paket Terapi"
      :message="`Apakah Anda yakin ingin menonaktifkan paket terapi '${selectedPackageToDeactivate?.nama_paket}'?`"
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

const packages = ref([])
const services = ref([])
const loading = ref(false)
const searchQuery = ref('')

const showModal = ref(false)
const isEdit = ref(false)
const confirmDeactivateModal = ref(false)
const selectedPackageToDeactivate = ref(null)

const form = ref({
  id: null,
  nama_paket: '',
  layanan_id: null,
  jumlah_sesi: 8,
  harga_paket: 1200000,
  masa_berlaku_hari: 60,
  deskripsi: '',
  status_aktif: true,
})

const fetchPackages = async () => {
  loading.value = true
  try {
    const res = await api.get('/pediatric-workflow/packages')
    packages.value = res.data || []
  } catch (err) {
    console.error('Error fetching packages:', err)
  } finally {
    loading.value = false
  }
}

const fetchServices = async () => {
  try {
    const res = await api.get('/layanan')
    services.value = res.data?.data || res.data || []
    if (services.value.length > 0 && !form.value.layanan_id) {
      form.value.layanan_id = services.value[0].id
    }
  } catch (err) {
    console.error('Error fetching services:', err)
  }
}

const filteredPackages = computed(() => {
  return packages.value.filter(p => {
    const q = searchQuery.value.toLowerCase().trim()
    return !q || (p.nama_paket || '').toLowerCase().includes(q) || (p.layanan?.layanan || '').toLowerCase().includes(q)
  })
})

const openAddModal = () => {
  isEdit.value = false
  form.value = {
    id: null,
    nama_paket: '',
    layanan_id: services.value[0]?.id || 1,
    jumlah_sesi: 8,
    harga_paket: 1200000,
    masa_berlaku_hari: 60,
    deskripsi: 'Paket terapi terpadu tumbuh kembang anak',
    status_aktif: true,
  }
  showModal.value = true
}

const openEditModal = (p) => {
  isEdit.value = true
  form.value = {
    id: p.id,
    nama_paket: p.nama_paket,
    layanan_id: p.layanan_id,
    jumlah_sesi: p.jumlah_sesi,
    harga_paket: Number(p.harga_paket),
    masa_berlaku_hari: p.masa_berlaku_hari,
    deskripsi: p.deskripsi,
    status_aktif: p.status_aktif,
  }
  showModal.value = true
}

const savePackage = async () => {
  try {
    const res = await api.post('/pediatric-workflow/packages', form.value)
    toast.success(res.data.message || 'Paket terapi berhasil disimpan')
    showModal.value = false
    fetchPackages()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menyimpan paket')
  }
}

const confirmDeactivate = (p) => {
  selectedPackageToDeactivate.value = p
  confirmDeactivateModal.value = true
}

const executeDeactivate = async () => {
  if (!selectedPackageToDeactivate.value) return
  try {
    await api.delete(`/pediatric-workflow/packages/${selectedPackageToDeactivate.value.id}`)
    toast.success('Paket terapi berhasil dinonaktifkan')
    confirmDeactivateModal.value = false
    fetchPackages()
  } catch (err) {
    toast.error('Gagal menonaktifkan paket')
  }
}

const formatRupiah = (val) => 'Rp ' + Number(val || 0).toLocaleString('id-ID')

onMounted(() => {
  fetchPackages()
  fetchServices()
})
</script>
