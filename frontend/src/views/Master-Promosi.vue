<template>
  <div class="p-6 bg-slate-50 min-h-screen">

    <!-- HEADER -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-slate-800">Master Promosi</h1>
      <p class="text-sm text-slate-500 mt-1">Kelola data promo & diskon</p>
    </div>

    <!-- TOOLBAR -->
    <div class="bg-white rounded-xl shadow-sm border p-4 flex justify-between items-center mb-5">
      <button
        @click="openCreate"
        class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-5 py-2 rounded-lg hover:opacity-90 shadow"
      >
        + Tambah Promo
      </button>

      <div class="relative w-72">
        <input
          v-model="search"
          placeholder="Cari nama promo..."
          class="w-full border rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
        />
        <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
      </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow border overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-100 text-slate-700">
          <tr>
            <th class="th text-center">No</th>
            <th class="th">Kode</th>
            <th class="th cursor-pointer" @click="sortBy('nama_promo')">Nama Promo ⬍</th>
            <th class="th">Diskon</th>
            <th class="th">Periode</th>
            <th class="th">Status</th>
            <th class="th text-center">Aksi</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="(promo,index) in paginatedPromos" :key="promo.id" class="border-t hover:bg-slate-50">
            <td class="td text-center text-gray-500">{{ (currentPage-1)*perPage + index + 1 }}</td>
            <td class="td font-medium">{{ promo.kode_promo }}</td>
            <td class="td">{{ promo.nama_promo }}</td>
            <td class="td">
              <span v-if="promo.tipe_diskon==='persen'">{{ promo.nilai_diskon }}%</span>
              <span v-else>Rp {{ formatRupiah(promo.nilai_diskon) }}</span>
            </td>
            <td class="td">
              {{ formatTanggal(promo.tanggal_mulai) }}
              <div class="text-xs text-slate-400">s/d {{ formatTanggal(promo.tanggal_selesai) }}</div>
            </td>
            <td class="td">
              <span class="px-3 py-1 rounded-full text-xs font-semibold"
                :class="promo.status_aktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                {{ promo.status_aktif ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td class="td text-center space-x-2">
              <button @click="openEdit(promo)" class="btn-edit">Edit</button>
              <button @click="remove(promo.id)" class="btn-delete">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    <div class="flex justify-between items-center mt-4">
      <div class="text-sm text-gray-500">Halaman {{ currentPage }} dari {{ totalPages }}</div>
      <div class="flex gap-1">
        <button class="page-btn" :disabled="currentPage===1" @click="currentPage--">Prev</button>
        <button v-for="p in totalPages" :key="p" class="page-btn" :class="{active:p===currentPage}" @click="currentPage=p">
          {{p}}
        </button>
        <button class="page-btn" :disabled="currentPage===totalPages" @click="currentPage++">Next</button>
      </div>
    </div>

    <!-- MODAL FORM (TIDAK DIUBAH) -->
    <transition name="fade-scale">
      <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-xl p-6">

          <div class="flex justify-between items-center mb-4 border-b pb-3">
            <div>
              <h2 class="text-lg font-bold text-slate-800">
                {{ isEdit ? 'Edit Promosi' : 'Tambah Promosi' }}
              </h2>
              <p class="text-sm text-slate-500">Isi informasi promo dengan benar</p>
            </div>
            <button @click="close" class="text-xl text-slate-400 hover:text-red-500">✕</button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="label">Kode Promo</label>
              <input v-model="form.kode_promo" class="input" />
            </div>

            <div>
              <label class="label">Nama Promo</label>
              <input v-model="form.nama_promo" class="input" />
            </div>

            <div class="md:col-span-2">
              <label class="label">Deskripsi</label>
              <textarea v-model="form.deskripsi" rows="2" class="input"></textarea>
            </div>

            <div>
              <label class="label">Tipe Diskon</label>
              <select v-model="form.tipe_diskon" class="input">
                <option value="persen">Persen (%)</option>
                <option value="nominal">Nominal (Rp)</option>
              </select>
            </div>

            <div>
              <label class="label">Nilai Diskon</label>
              <input type="number" v-model="form.nilai_diskon" class="input" />
            </div>

            <div>
              <label class="label">Tanggal Mulai</label>
              <input type="date" v-model="form.tanggal_mulai" class="input" />
            </div>

            <div>
              <label class="label">Tanggal Selesai</label>
              <input type="date" v-model="form.tanggal_selesai" class="input" />
            </div>

            <div class="md:col-span-2 flex items-center gap-2">
              <input type="checkbox" v-model="form.status_aktif" />
              <span class="text-sm text-slate-700">Aktifkan promo</span>
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-6 border-t pt-4">
            <button @click="close" class="btn-secondary">Batal</button>
            <button @click="save" class="btn-primary">Simpan</button>
          </div>

        </div>
      </div>
    </transition>

    <!-- TOAST -->
    <div v-if="toast.show"
      class="fixed top-5 right-5 z-50 px-4 py-3 rounded-lg shadow-lg text-white transition"
      :class="toast.type==='success'?'bg-green-600':'bg-red-600'">
      {{ toast.message }}
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/axios'

const promos = ref([])
const showModal = ref(false)
const isEdit = ref(false)
const editId = ref(null)
const search = ref('')
const sortKey = ref('')
const sortAsc = ref(true)
const currentPage = ref(1)
const perPage = 5

const toast = ref({ show:false, message:'', type:'success' })

const showToast = (msg,type='success')=>{
  toast.value={show:true,message:msg,type}
  setTimeout(()=>toast.value.show=false,3000)
}

const form = ref({
  kode_promo:'',
  nama_promo:'',
  deskripsi:'',
  tipe_diskon:'persen',
  nilai_diskon:0,
  tanggal_mulai:'',
  tanggal_selesai:'',
  status_aktif:true
})

const fetchPromos = async()=>{
  const res = await api.get('/promosi')
  promos.value = res.data.data
}

const filteredPromos = computed(()=> !search.value ? promos.value :
  promos.value.filter(p=>p.nama_promo?.toLowerCase().includes(search.value.toLowerCase()))
)

const sortedPromos = computed(()=>{
  if(!sortKey.value) return filteredPromos.value
  return [...filteredPromos.value].sort((a,b)=>{
    const A=a[sortKey.value]||''
    const B=b[sortKey.value]||''
    return sortAsc.value ? A.localeCompare(B) : B.localeCompare(A)
  })
})

const totalPages = computed(()=>Math.ceil(sortedPromos.value.length/perPage))
const paginatedPromos = computed(()=>{
  const start=(currentPage.value-1)*perPage
  return sortedPromos.value.slice(start,start+perPage)
})

const sortBy=(k)=>{
  if(sortKey.value===k) sortAsc.value=!sortAsc.value
  else{sortKey.value=k;sortAsc.value=true}
}

const openCreate=()=>{resetForm();isEdit.value=false;showModal.value=true}
const openEdit=(p)=>{editId.value=p.id;isEdit.value=true;form.value={...p};showModal.value=true}

const save = async()=>{
  try{
    if(isEdit.value){
      await api.put(`/promosi/${editId.value}`,form.value)
      showToast('Promo berhasil diperbarui')
    }else{
      await api.post('/promosi',form.value)
      showToast('Promo berhasil ditambahkan')
    }
    close()
    fetchPromos()
  }catch(err){
    showToast('Gagal menyimpan data','error')
  }
}

const remove=async(id)=>{
  if(confirm('Hapus promo ini?')){
    try{
      await api.delete(`/promosi/${id}`)
      fetchPromos()
      showToast('Promo berhasil dihapus')
    }catch{
      showToast('Gagal menghapus data','error')
    }
  }
}

const close=()=>showModal.value=false

const resetForm=()=>form.value={
  kode_promo:'',
  nama_promo:'',
  deskripsi:'',
  tipe_diskon:'persen',
  nilai_diskon:0,
  tanggal_mulai:'',
  tanggal_selesai:'',
  status_aktif:true
}

const formatRupiah=v=>new Intl.NumberFormat('id-ID').format(v)
const formatTanggal=d=>d?new Intl.DateTimeFormat('id-ID',{day:'2-digit',month:'2-digit',year:'numeric'}).format(new Date(d)):'-'

onMounted(fetchPromos)
</script>

<style scoped>
.th{@apply px-4 py-3 text-left font-semibold}
.td{@apply px-4 py-3}

.input{@apply w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none}
.label{@apply text-sm font-semibold text-slate-700 mb-1}

.btn-edit{@apply text-blue-600 hover:underline}
.btn-delete{@apply text-red-600 hover:underline}

.btn-primary{@apply bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-5 py-2 rounded-lg hover:opacity-90}
.btn-secondary{@apply border px-5 py-2 rounded-lg hover:bg-slate-100}

.page-btn{@apply px-3 py-1 border rounded text-sm hover:bg-slate-100 disabled:opacity-40}
.page-btn.active{@apply bg-blue-600 text-white border-blue-600}

.fade-scale-enter-active,.fade-scale-leave-active{transition:.25s}
.fade-scale-enter-from,.fade-scale-leave-to{opacity:0;transform:scale(.95)}
</style>
