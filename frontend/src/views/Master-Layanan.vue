<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-briefcase"></i>
        </div>
        <div>
          <h1 class="text-[15px] font-bold text-[#0F172A] leading-tight">Master Layanan & Tarif</h1>
          <p class="text-[11.5px] text-[#64748B] mt-0.5">Kelola data tarif layanan terapi, tarif weekday/weekend, dan kuota sesi</p>
        </div>
      </div>

      <button
        type="button"
        @click="openAdd"
        class="btn-primary-clinical text-[12.5px] shrink-0"
      >
        <i class="pi pi-plus text-xs"></i>
        <span>Tambah Layanan</span>
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
            placeholder="Cari nama layanan..."
            class="w-full h-[34px] text-[12.5px] pl-8 pr-2.5 border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 transition bg-white text-slate-800 placeholder:text-slate-400"
          />
        </div>
        <span class="text-[11.5px] text-slate-500 font-medium">
          Total <strong>{{ filteredList.length }}</strong> layanan aktif
        </span>
      </div>

      <!-- TABLE BODY -->
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="min-w-full text-[12.5px] text-slate-700">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2 px-3 text-center w-12">No</th>
              <th class="py-2 px-3">Nama Layanan</th>
              <th class="py-2 px-3">Kategori</th>
              <th class="py-2 px-3 text-right">Tarif Weekday</th>
              <th class="py-2 px-3 text-right">Tarif Weekend</th>
              <th class="py-2 px-3 text-center">Qty / Sesi</th>
              <th class="py-2 px-3 text-center">Status</th>
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
              <td class="py-2 px-3 font-semibold text-slate-900">{{ item.layanan }}</td>
              <td class="py-2 px-3 text-slate-600">{{ item.kategori?.kategori_layanan || '-' }}</td>
              <td class="py-2 px-3 text-right tabular-nums font-medium text-slate-800">
                {{ formatCurrency(item.harga_weekday) }}
              </td>
              <td class="py-2 px-3 text-right tabular-nums font-medium text-slate-800">
                {{ formatCurrency(item.harga_weekend) }}
              </td>
              <td class="py-2 px-3 text-center tabular-nums">{{ item.qty || 1 }} sesi</td>
              <td class="py-2 px-3 text-center">
                <StatusBadge
                  :label="item.status_aktif ? 'Aktif' : 'Non Aktif'"
                  :type="item.status_aktif ? 'success' : 'neutral'"
                />
              </td>
              <td class="py-2 px-3 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    type="button"
                    @click="openEdit(item)"
                    class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 flex items-center justify-center transition shadow-2xs"
                    title="Edit Layanan"
                  >
                    <i class="pi pi-pencil text-[11px]"></i>
                  </button>
                  <button
                    type="button"
                    @click="openDelete(item)"
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
              <td colspan="8" class="py-12 text-center text-slate-400">
                <div class="flex flex-col items-center justify-center">
                  <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                    <i class="pi pi-folder-open text-lg"></i>
                  </div>
                  <h4 class="text-[13px] font-semibold text-slate-800">Layanan Tidak Ditemukan</h4>
                  <p class="text-[11.5px] text-slate-500 mt-0.5">Belum ada layanan yang sesuai pencarian.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- TABLE FOOTER -->
      <div class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px] text-slate-500 flex items-center justify-between">
        <span>Menampilkan {{ filteredList.length }} layanan</span>
        <span>SIMRS Tarif Medis</span>
      </div>
    </div>

    <!-- MODAL FORM (COMPACT TIER 'md': max-w-[640px]) -->
    <BaseModal
      v-model="dialog"
      :title="form.id ? 'Edit Layanan' : 'Tambah Layanan Baru'"
      :subtitle="form.id ? 'Perbarui informasi tarif dan kuota layanan' : 'Daftarkan layanan terapi baru ke klinik'"
      icon="pi pi-briefcase"
      size="md"
      :loading="saving"
      @submit="save"
    >
      <form @submit.prevent="save" class="grid grid-cols-1 sm:grid-cols-2 gap-x-3.5 gap-y-2.5">
        <div class="sm:col-span-2">
          <label class="label-clinical">Nama Layanan <span class="text-rose-500">*</span></label>
          <input
            v-model="form.layanan"
            required
            class="input-clinical"
            placeholder="mis. Fisioterapi Tumbuh Kembang 60 Menit"
          />
        </div>

        <div>
          <label class="label-clinical">Kategori Layanan <span class="text-rose-500">*</span></label>
          <select v-model="form.kategori_layanan_id" required class="input-clinical">
            <option :value="null" disabled>Pilih kategori...</option>
            <option v-for="k in kategoriOptions" :key="k.id" :value="k.id">
              {{ k.kategori_layanan }}
            </option>
          </select>
        </div>

        <div>
          <label class="label-clinical">Status</label>
          <select v-model="form.status_aktif" class="input-clinical">
            <option :value="true">Aktif</option>
            <option :value="false">Non Aktif</option>
          </select>
        </div>

        <div>
          <label class="label-clinical">Tarif Weekday (Rp) <span class="text-rose-500">*</span></label>
          <input
            type="number"
            min="0"
            v-model.number="form.harga_weekday"
            required
            class="input-clinical tabular-nums"
          />
        </div>

        <div>
          <label class="label-clinical">Tarif Weekend (Rp) <span class="text-rose-500">*</span></label>
          <input
            type="number"
            min="0"
            v-model.number="form.harga_weekend"
            required
            class="input-clinical tabular-nums"
          />
        </div>

        <div>
          <label class="label-clinical">Alokasi Qty / Sesi</label>
          <input
            type="number"
            min="1"
            v-model.number="form.qty"
            class="input-clinical tabular-nums"
          />
        </div>

        <div>
          <label class="label-clinical">Deskripsi Singkat</label>
          <input
            v-model="form.deskripsi"
            class="input-clinical"
            placeholder="mis. Sesi individu motorik"
          />
        </div>
      </form>
    </BaseModal>

    <!-- CONFIRM DELETE DIALOG -->
    <ConfirmDialog
      v-model="confirmDelete"
      title="Hapus Layanan"
      :message="`Yakin ingin menghapus layanan '${form.layanan}'?`"
      type="danger"
      confirmText="Ya, Hapus"
      :loading="deleting"
      @confirm="remove"
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
const layananList = ref([])
const kategoriOptions = ref([])
const search = ref('')
const loading = ref(false)
const saving = ref(false)
const deleting = ref(false)

const dialog = ref(false)
const confirmDelete = ref(false)

const form = ref({
  id: null,
  layanan: '',
  kategori_layanan_id: null,
  harga_weekday: 0,
  harga_weekend: 0,
  qty: 1,
  deskripsi: '',
  status_aktif: true
})

onMounted(() => {
  loadLayanan()
  loadKategori()
})

async function loadLayanan() {
  loading.value = true
  try {
    const res = await api.get('/layanan')
    layananList.value = res.data.data ?? res.data
  } catch (err) {
    toast.error('Gagal mengambil data layanan')
  } finally {
    loading.value = false
  }
}

async function loadKategori() {
  try {
    const res = await api.get('/kategori-layanan')
    kategoriOptions.value = res.data.data ?? res.data
  } catch (err) {
    console.error(err)
  }
}

const filteredList = computed(() => {
  if (!search.value) return layananList.value
  const q = search.value.toLowerCase()
  return layananList.value.filter(l =>
    (l.layanan || '').toLowerCase().includes(q) ||
    (l.kategori?.kategori_layanan || '').toLowerCase().includes(q)
  )
})

const formatCurrency = (val) => {
  return 'Rp ' + Number(val || 0).toLocaleString('id-ID')
}

const openAdd = () => {
  form.value = {
    id: null,
    layanan: '',
    kategori_layanan_id: null,
    harga_weekday: 0,
    harga_weekend: 0,
    qty: 1,
    deskripsi: '',
    status_aktif: true
  }
  dialog.value = true
}

const openEdit = (data) => {
  form.value = { ...data }
  dialog.value = true
}

const openDelete = (data) => {
  form.value = { ...data }
  confirmDelete.value = true
}

const save = async () => {
  if (!form.value.layanan || !form.value.kategori_layanan_id) {
    toast.warning('Nama layanan dan kategori wajib diisi')
    return
  }

  saving.value = true
  try {
    if (form.value.id) {
      await api.put(`/layanan/${form.value.id}`, form.value)
      toast.success('Layanan berhasil diperbarui')
    } else {
      await api.post('/layanan', form.value)
      toast.success('Layanan berhasil ditambahkan')
    }
    dialog.value = false
    loadLayanan()
  } catch (err) {
    toast.error('Gagal menyimpan layanan')
  } finally {
    saving.value = false
  }
}

const remove = async () => {
  deleting.value = true
  try {
    await api.delete(`/layanan/${form.value.id}`)
    toast.success('Layanan berhasil dihapus')
    confirmDelete.value = false
    loadLayanan()
  } catch (err) {
    toast.error('Gagal menghapus layanan')
  } finally {
    deleting.value = false
  }
}
</script>
