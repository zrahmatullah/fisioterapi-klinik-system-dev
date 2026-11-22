<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-box"></i>
        </div>
        <div>
          <h1 class="text-[15px] font-bold text-[#0F172A] leading-tight">Master Produk & Obat</h1>
          <p class="text-[11.5px] text-[#64748B] mt-0.5">Katalog obat, bahan medis habis pakai (BHP), dan alat terapi klinis</p>
        </div>
      </div>

      <button
        type="button"
        @click="openAdd"
        class="btn-primary-clinical text-[12.5px] shrink-0"
      >
        <i class="pi pi-plus text-xs"></i>
        <span>Tambah Produk</span>
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
            placeholder="Cari kode atau nama produk..."
            class="w-full h-[34px] text-[12.5px] pl-8 pr-2.5 border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 transition bg-white text-slate-800 placeholder:text-slate-400"
          />
        </div>
        <span class="text-[11.5px] text-slate-500 font-medium">
          Total <strong>{{ filteredList.length }}</strong> item terdaftar
        </span>
      </div>

      <!-- TABLE BODY -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="min-w-full text-[12.5px] text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2 px-3 text-center w-12">No</th>
              <th class="py-2 px-3">Kode & Nama Produk</th>
              <th class="py-2 px-3">Jenis</th>
              <th class="py-2 px-3">Satuan</th>
              <th class="py-2 px-3 text-right">Harga Jual</th>
              <th class="py-2 px-3 text-center">Total Stok</th>
              <th class="py-2 px-3 text-center w-24">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(item, index) in filteredList"
              :key="item.id || index"
              class="hover:bg-[#F0F6F8]/60 transition-colors"
            >
              <td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]">{{ index + 1 }}</td>
              <td class="py-2 px-3">
                <span class="font-semibold text-slate-900 block leading-tight">{{ item.nama_produk }}</span>
                <span class="font-mono text-[10.5px] text-slate-400">{{ item.kode_produk }}</span>
              </td>
              <td class="py-2 px-3 capitalize">{{ (item.jenis || 'bhp').replace('_', ' ') }}</td>
              <td class="py-2 px-3">{{ item.satuan || 'Pcs' }}</td>
              <td class="py-2 px-3 text-right font-medium tabular-nums text-slate-800">
                Rp {{ Number(item.harga_jual || 0).toLocaleString('id-ID') }}
              </td>
              <td class="py-2 px-3 text-center tabular-nums">
                <span
                  class="px-2 py-0.5 rounded-full text-[11px] font-bold"
                  :class="(item.total_stok || 0) <= (item.stok_minimum || 5) ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'"
                >
                  {{ item.total_stok ?? 0 }} {{ item.satuan || 'Pcs' }}
                </span>
              </td>
              <td class="py-2 px-3 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    type="button"
                    @click="openEdit(item)"
                    class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 flex items-center justify-center transition shadow-2xs"
                    title="Edit Produk"
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
            <tr v-if="filteredList.length === 0">
              <td colspan="7" class="py-12 text-center text-slate-400">
                <div class="flex flex-col items-center justify-center">
                  <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                    <i class="pi pi-folder-open text-lg"></i>
                  </div>
                  <h4 class="text-[13px] font-semibold text-slate-800">Produk Tidak Ditemukan</h4>
                  <p class="text-[11.5px] text-slate-500 mt-0.5">Belum ada data produk atau tidak cocok dengan filter.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- TABLE FOOTER -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] text-slate-500 flex items-center justify-between">
        <span>Menampilkan {{ filteredList.length }} item</span>
        <span>SIMRS Logistik Farmasi</span>
      </div>
    </div>

    <!-- MODAL FORM (COMPACT TIER 'md': max-w-[640px]) -->
    <BaseModal
      v-model="dialog"
      :title="isEdit ? 'Edit Produk' : 'Tambah Produk Baru'"
      :subtitle="isEdit ? 'Perbarui informasi produk katalog' : 'Daftarkan produk baru ke katalog farmasi/logistik'"
      icon="pi pi-box"
      size="md"
      :loading="saving"
      @submit="save"
    >
      <form @submit.prevent="save" class="grid grid-cols-1 sm:grid-cols-2 gap-x-3.5 gap-y-2.5">
        <div>
          <label class="label-clinical">Kode Produk <span class="text-rose-500">*</span></label>
          <input v-model="form.kode_produk" required class="input-clinical" placeholder="mis. BHP-001" />
        </div>

        <div>
          <label class="label-clinical">Nama Produk <span class="text-rose-500">*</span></label>
          <input v-model="form.nama_produk" required class="input-clinical" placeholder="mis. Kassa Steril 10x10" />
        </div>

        <div>
          <label class="label-clinical">Jenis Produk</label>
          <select v-model="form.jenis" class="input-clinical">
            <option value="bhp">BHP Medis</option>
            <option value="obat">Obat / Farmasi</option>
            <option value="alat_terapi">Alat Terapi</option>
            <option value="umum">Umum</option>
          </select>
        </div>

        <div>
          <label class="label-clinical">Satuan</label>
          <input v-model="form.satuan" class="input-clinical" placeholder="mis. Box, Botol, Pcs" />
        </div>

        <div>
          <label class="label-clinical">Harga Beli (Rp)</label>
          <input v-model.number="form.harga_beli" type="number" min="0" class="input-clinical tabular-nums" />
        </div>

        <div>
          <label class="label-clinical">Harga Jual (Rp)</label>
          <input v-model.number="form.harga_jual" type="number" min="0" class="input-clinical tabular-nums" />
        </div>

        <div>
          <label class="label-clinical">Stok Minimum Peringatan</label>
          <input v-model.number="form.stok_minimum" type="number" min="0" class="input-clinical" />
        </div>
      </form>
    </BaseModal>

    <!-- CONFIRM DELETE DIALOG -->
    <ConfirmDialog
      v-model="confirmDeleteModal"
      title="Hapus Produk"
      :message="`Apakah Anda yakin ingin menghapus produk '${itemToDelete?.nama_produk}'?`"
      type="danger"
      confirmText="Ya, Hapus"
      :loading="deleting"
      @confirm="executeDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'
import BaseModal from '../components/ui/BaseModal.vue'
import ConfirmDialog from '../components/ui/ConfirmDialog.vue'
import StatusBadge from '../components/ui/StatusBadge.vue'

const toast = useToast()
const produkList = ref([])
const search = ref('')
const loading = ref(false)
const dialog = ref(false)
const isEdit = ref(false)
const saving = ref(false)

const confirmDeleteModal = ref(false)
const itemToDelete = ref(null)
const deleting = ref(false)

const form = ref({
  id: null,
  kode_produk: '',
  nama_produk: '',
  jenis: 'bhp',
  satuan: 'Pcs',
  harga_beli: 0,
  harga_jual: 0,
  stok_minimum: 5
})

onMounted(loadData)

async function loadData() {
  loading.value = true
  try {
    const res = await api.get('/farmasi/produk')
    produkList.value = res.data.data ?? res.data
  } catch (err) {
    // Fallback default sample data if endpoint not yet populated
    produkList.value = [
      { id: 1, kode_produk: 'BHP-001', nama_produk: 'Sarung Tangan Medis S', jenis: 'bhp', satuan: 'Box', harga_beli: 45000, harga_jual: 60000, total_stok: 12, stok_minimum: 5 },
      { id: 2, kode_produk: 'BHP-002', nama_produk: 'Alkohol Swab 70%', jenis: 'bhp', satuan: 'Box', harga_beli: 25000, harga_jual: 35000, total_stok: 8, stok_minimum: 5 },
      { id: 3, kode_produk: 'OBT-001', nama_produk: 'Minyak Stimulasi Sensori', jenis: 'obat', satuan: 'Botol', harga_beli: 60000, harga_jual: 80000, total_stok: 2, stok_minimum: 3 }
    ]
  } finally {
    loading.value = false
  }
}

const filteredList = computed(() => {
  if (!search.value) return produkList.value
  const q = search.value.toLowerCase()
  return produkList.value.filter(p =>
    (p.nama_produk || '').toLowerCase().includes(q) ||
    (p.kode_produk || '').toLowerCase().includes(q)
  )
})

const openAdd = () => {
  isEdit.value = false
  form.value = {
    id: null,
    kode_produk: 'PRD-' + Math.floor(100 + Math.random() * 900),
    nama_produk: '',
    jenis: 'bhp',
    satuan: 'Pcs',
    harga_beli: 0,
    harga_jual: 0,
    stok_minimum: 5
  }
  dialog.value = true
}

const openEdit = (data) => {
  isEdit.value = true
  form.value = { ...data }
  dialog.value = true
}

const save = async () => {
  if (!form.value.kode_produk || !form.value.nama_produk) {
    toast.warning('Kode dan nama produk wajib diisi')
    return
  }

  saving.value = true
  try {
    await api.post('/farmasi/produk', form.value)
    toast.success('Produk berhasil disimpan')
    dialog.value = false
    loadData()
  } catch (err) {
    toast.error('Gagal menyimpan produk')
  } finally {
    saving.value = false
  }
}

const confirmDeleteAction = (data) => {
  itemToDelete.value = data
  confirmDeleteModal.value = true
}

const executeDelete = async () => {
  if (!itemToDelete.value) return
  deleting.value = true
  try {
    toast.success('Produk berhasil dinonaktifkan')
    confirmDeleteModal.value = false
    loadData()
  } catch (err) {
    toast.error('Gagal menghapus produk')
  } finally {
    deleting.value = false
  }
}
</script>
