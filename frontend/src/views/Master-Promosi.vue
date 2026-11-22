<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-percentage"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Master Promosi & Diskon</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Kelola voucher promo, potongan harga sesi terapi, dan periode masa berlaku diskon</p>
        </div>
      </div>

      <button
        type="button"
        @click="openCreate"
        class="btn-primary-clinical text-xs py-2 px-3.5 shrink-0"
      >
        <i class="pi pi-plus text-xs"></i>
        <span>Tambah Promo</span>
      </button>
    </div>

    <!-- MAIN TABLE CARD -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">
      <!-- TOOLBAR -->
      <div class="p-3 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2.5 shrink-0">
        <div class="relative w-72">
          <i class="pi pi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
          <input
            v-model="search"
            placeholder="Cari nama promo..."
            class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
          />
        </div>

        <span class="text-xs text-slate-500 font-medium">Total <strong>{{ filteredPromos.length }}</strong> promo</span>
      </div>

      <!-- TABLE BODY -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="w-full text-[12.5px] text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3 text-center w-12">No</th>
              <th class="py-2.5 px-3">Kode</th>
              <th class="py-2.5 px-3 cursor-pointer" @click="sortBy('nama_promo')">Nama Promo ⬍</th>
              <th class="py-2.5 px-3">Diskon</th>
              <th class="py-2.5 px-3">Periode</th>
              <th class="py-2.5 px-3 text-center">Status</th>
              <th class="py-2.5 px-3 text-right w-24">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr v-if="paginatedPromos.length === 0">
              <td colspan="7" class="py-8 text-center text-slate-400 text-xs">Tidak ada data promosi.</td>
            </tr>
            <tr v-for="(promo, index) in paginatedPromos" :key="promo.id" class="hover:bg-[#F0F6F8]/60 transition">
              <td class="py-2 px-3 text-center text-slate-400 text-xs font-mono">{{ (currentPage - 1) * perPage + index + 1 }}</td>
              <td class="py-2 px-3 font-mono font-semibold text-[#0F766E]">{{ promo.kode_promo }}</td>
              <td class="py-2 px-3 font-medium text-slate-900">{{ promo.nama_promo }}</td>
              <td class="py-2 px-3 font-semibold tabular-nums">
                <span v-if="promo.tipe_diskon === 'persen'" class="text-amber-700">{{ promo.nilai_diskon }}%</span>
                <span v-else class="text-emerald-700">Rp {{ formatRupiah(promo.nilai_diskon) }}</span>
              </td>
              <td class="py-2 px-3 text-xs tabular-nums text-slate-600">
                {{ formatTanggal(promo.tanggal_mulai) }}
                <div class="text-[11px] text-slate-400">s/d {{ formatTanggal(promo.tanggal_selesai) }}</div>
              </td>
              <td class="py-2 px-3 text-center">
                <StatusBadge :status="promo.status_aktif ? 'Aktif' : 'Nonaktif'" />
              </td>
              <td class="py-2 px-3 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button @click="openEdit(promo)" class="w-7 h-7 rounded flex items-center justify-center text-slate-500 hover:text-[#0F766E] hover:bg-[#E6F2F5] transition cursor-pointer" title="Edit">
                    <i class="pi pi-pencil text-xs"></i>
                  </button>
                  <button @click="remove(promo.id)" class="w-7 h-7 rounded flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                    <i class="pi pi-trash text-xs"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- FOOTER PAGINATION -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] flex justify-between items-center text-slate-500">
        <div>Halaman {{ currentPage }} dari {{ totalPages || 1 }}</div>
        <div class="flex gap-1">
          <button class="px-2.5 py-1 border border-slate-300 rounded text-xs hover:bg-slate-50 disabled:opacity-40" :disabled="currentPage === 1" @click="currentPage--">Prev</button>
          <button v-for="p in totalPages" :key="p" class="px-2.5 py-1 border rounded text-xs" :class="p === currentPage ? 'bg-[#0F766E] text-white border-[#0F766E]' : 'border-slate-300 hover:bg-slate-50'" @click="currentPage = p">
            {{ p }}
          </button>
          <button class="px-2.5 py-1 border border-slate-300 rounded text-xs hover:bg-slate-50 disabled:opacity-40" :disabled="currentPage === totalPages || totalPages === 0" @click="currentPage++">Next</button>
        </div>
      </div>
    </div>

    <!-- MODAL FORM PROMOSI -->
    <BaseModal
      v-model="showModal"
      :title="isEdit ? 'Edit Data Promosi' : 'Tambah Promosi Baru'"
      subtitle="Isi informasi promosi dan nominal diskon secara lengkap"
      size="md"
    >
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 text-xs">
        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Kode Promo</label>
          <input v-model="form.kode_promo" class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] font-mono" placeholder="mis. PROMO-TERAPI" />
        </div>

        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Nama Promo</label>
          <input v-model="form.nama_promo" class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]" placeholder="mis. Diskon Awal Tahun" />
        </div>

        <div class="md:col-span-2">
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Deskripsi Promo</label>
          <textarea v-model="form.deskripsi" rows="2" class="w-full px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]" placeholder="Keterangan diskon paket terapi..."></textarea>
        </div>

        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Tipe Diskon</label>
          <select v-model="form.tipe_diskon" class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]">
            <option value="persen">Persen (%)</option>
            <option value="nominal">Nominal (Rp)</option>
          </select>
        </div>

        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Nilai Diskon</label>
          <input type="number" v-model="form.nilai_diskon" class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] tabular-nums" />
        </div>

        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Tanggal Mulai</label>
          <input type="date" v-model="form.tanggal_mulai" class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]" />
        </div>

        <div>
          <label class="block text-[12px] font-medium text-slate-700 mb-1">Tanggal Selesai</label>
          <input type="date" v-model="form.tanggal_selesai" class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]" />
        </div>

        <div class="md:col-span-2 flex items-center gap-2 pt-1">
          <input type="checkbox" id="promo_status" v-model="form.status_aktif" class="w-4 h-4 text-[#0F766E] border-slate-300 rounded cursor-pointer" />
          <label for="promo_status" class="text-xs text-slate-700 font-medium cursor-pointer">Aktifkan promo untuk transaksi</label>
        </div>
      </div>

      <template #footer>
        <button type="button" @click="close" class="h-[34px] px-3.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-[12.5px] font-medium transition cursor-pointer">
          Batal
        </button>
        <button type="button" @click="save" class="h-[34px] px-3.5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-[12.5px] font-medium transition cursor-pointer">
          Simpan Promo
        </button>
      </template>
    </BaseModal>

    <!-- CONFIRM DELETE DIALOG -->
    <ConfirmDialog
      v-model="confirmDeleteModal"
      title="Hapus Master Promo"
      message="Apakah Anda yakin ingin menghapus data promo ini? Tindakan ini tidak dapat dibatalkan."
      confirmText="Ya, Hapus Promo"
      type="danger"
      @confirm="executeRemove"
    />

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/axios'
import BaseModal from '@/components/ui/BaseModal.vue'
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const promos = ref([])
const showModal = ref(false)
const isEdit = ref(false)
const editId = ref(null)
const search = ref('')
const sortKey = ref('')
const sortAsc = ref(true)
const currentPage = ref(1)
const perPage = 10

const confirmDeleteModal = ref(false)
const selectedPromoToDelete = ref(null)

const form = ref({
  kode_promo: '',
  nama_promo: '',
  deskripsi: '',
  tipe_diskon: 'persen',
  nilai_diskon: 0,
  tanggal_mulai: '',
  tanggal_selesai: '',
  status_aktif: true
})

const fetchPromos = async () => {
  try {
    const res = await api.get('/promosi')
    promos.value = res.data.data || res.data || []
  } catch (err) {
    console.error('Error fetching promos:', err)
  }
}

const filteredPromos = computed(() => !search.value ? promos.value :
  promos.value.filter(p => (p.nama_promo || '').toLowerCase().includes(search.value.toLowerCase()) || (p.kode_promo || '').toLowerCase().includes(search.value.toLowerCase()))
)

const sortedPromos = computed(() => {
  if (!sortKey.value) return filteredPromos.value
  return [...filteredPromos.value].sort((a, b) => {
    const A = a[sortKey.value] || ''
    const B = b[sortKey.value] || ''
    return sortAsc.value ? String(A).localeCompare(String(B)) : String(B).localeCompare(String(A))
  })
})

const totalPages = computed(() => Math.ceil(sortedPromos.value.length / perPage))
const paginatedPromos = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return sortedPromos.value.slice(start, start + perPage)
})

const sortBy = (k) => {
  if (sortKey.value === k) sortAsc.value = !sortAsc.value
  else { sortKey.value = k; sortAsc.value = true }
}

const openCreate = () => { resetForm(); isEdit.value = false; showModal.value = true }
const openEdit = (p) => { editId.value = p.id; isEdit.value = true; form.value = { ...p }; showModal.value = true }

const save = async () => {
  try {
    if (isEdit.value) {
      await api.put(`/promosi/${editId.value}`, form.value)
      toast.success('Promo berhasil diperbarui')
    } else {
      await api.post('/promosi', form.value)
      toast.success('Promo berhasil ditambahkan')
    }
    close()
    fetchPromos()
  } catch (err) {
    toast.error('Gagal menyimpan data')
  }
}

const remove = (id) => {
  selectedPromoToDelete.value = id
  confirmDeleteModal.value = true
}

const executeRemove = async () => {
  if (!selectedPromoToDelete.value) return
  try {
    await api.delete(`/promosi/${selectedPromoToDelete.value}`)
    confirmDeleteModal.value = false
    fetchPromos()
    toast.success('Promo berhasil dihapus')
  } catch {
    toast.error('Gagal menghapus data')
  }
}

const close = () => { showModal.value = false }

const resetForm = () => {
  form.value = {
    kode_promo: '',
    nama_promo: '',
    deskripsi: '',
    tipe_diskon: 'persen',
    nilai_diskon: 0,
    tanggal_mulai: '',
    tanggal_selesai: '',
    status_aktif: true
  }
}

const formatRupiah = v => new Intl.NumberFormat('id-ID').format(v || 0)
const formatTanggal = d => d ? new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(d)) : '-'

onMounted(fetchPromos)
</script>
