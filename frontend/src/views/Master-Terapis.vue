<template>
  <div class="p-6 bg-gray-50 min-h-screen">

    <!-- TAB -->
    <div class="flex gap-3 mb-6">
      <button @click="tab='terapis'" :class="tab==='terapis' ? 'tab-active' : 'tab'">
        Master Terapis
      </button>
      <button @click="tab='mapping'" :class="tab==='mapping' ? 'tab-active' : 'tab'">
        Terapis To Ruangan
      </button>
    </div>

    <div v-if="tab==='terapis'">

      <h1 class="text-3xl font-bold text-gray-800 mb-1">Master Terapis</h1>
      <p class="text-sm text-gray-500 mb-4">
        Kelola data terapis dan jadwal praktik
      </p>

      <div class="flex justify-end mb-4">
        <input v-model="search" placeholder="Cari nama terapis..."
               class="input w-72" />
      </div>

      <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="th text-center">No</th>
              <th class="th cursor-pointer" @click="sortBy('nama')">Nama Terapis</th>
              <th class="th">Jenis Kelamin</th>
              <th class="th">No. Telepon</th>
              <th class="th">Spesialisasi</th>
              <th class="th">Jadwal</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(u, index) in paginatedTerapis" :key="u.id"
                class="border-t hover:bg-gray-50">
              <td class="td text-center">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="td font-semibold text-indigo-600">{{ u.nama }}</td>
              <td class="td">{{ u.jenis_kelamin?.nama || '-' }}</td>
              <td class="td">{{ u.no_telepon || '-' }}</td>
              <td class="td">{{ u.spesialisasi || '-' }}</td>

              <td class="td">
                <div v-if="u.jadwal_user?.length" class="flex flex-wrap gap-2">
                  <span v-for="j in u.jadwal_user" :key="j.id" class="badge">
                    {{ hariLabel(j.jadwal_master.hari) }}
                    ({{ j.jadwal_master.jam_mulai }} - {{ j.jadwal_master.jam_selesai }})
                  </span>
                </div>
                <span v-else class="text-xs text-gray-400 italic">
                  Belum ada jadwal
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-between items-center mt-4">
        <div class="text-sm text-gray-500">
          Halaman {{ currentPage }} dari {{ totalPages }}
        </div>
        <div class="flex gap-1">
          <button class="page-btn" :disabled="currentPage===1" @click="currentPage--">Prev</button>
          <button v-for="p in totalPages" :key="p"
                  @click="currentPage=p"
                  class="page-btn"
                  :class="{active:p===currentPage}">
            {{ p }}
          </button>
          <button class="page-btn" :disabled="currentPage===totalPages" @click="currentPage++">Next</button>
        </div>
      </div>
    </div>

    <div v-if="tab==='mapping'" class="bg-white rounded-2xl shadow p-6">

      <h2 class="text-xl font-bold mb-4 text-indigo-600 flex items-center gap-2">
        Mapping Terapis To Ruangan
      </h2>

      <div class="grid grid-cols-3 gap-4 mb-6">
        <select v-model="map.terapis_id" class="input">
          <option value="">Pilih Terapis</option>
          <option v-for="t in terapis" :key="t.id" :value="t.id">
            {{ t.nama }}
          </option>
        </select>

        <select v-model="map.ruangan_id" class="input">
          <option value="">Pilih Ruangan</option>
          <option v-for="r in allRuangan" :key="r.id" :value="r.id">
            {{ r.ruangan }}
          </option>
        </select>

        <button class="btn-primary" @click="saveMap">+ Tambah</button>
      </div>

      <table class="w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="th text-left">Terapis</th>
            <th class="th text-left">Ruangan</th>
            <th class="th text-center">Aksi</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="m in mappings" :key="m.id" class="border-t hover:bg-gray-50">
            <td class="td">{{ m.terapis }}</td>
            <td class="td">{{ m.ruangan }}</td>
            <td class="td text-center">
              <button class="btn-delete" @click="hapusMap(m)">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const tab = ref('terapis')

const terapis = ref([])
const allRuangan = ref([])
const mappings = ref([])

const map = ref({ terapis_id:'', ruangan_id:'' })

const search = ref('')
const sortKey = ref('')
const sortAsc = ref(true)

const currentPage = ref(1)
const perPage = 5

const hariList = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']
const hariLabel = h => hariList[h]

const fetchTerapis = async () => {
  terapis.value = (await api.get('/terapis')).data.data
}
const fetchRuangan = async () => {
  allRuangan.value = (await api.get('/ruangan')).data
}
const fetchMapping = async () => {
  mappings.value = (await api.get('/terapis-ruangan')).data
}

const filteredTerapis = computed(() =>
  !search.value ? terapis.value :
  terapis.value.filter(t =>
    t.nama?.toLowerCase().includes(search.value.toLowerCase()))
)

const sortedTerapis = computed(() => {
  if (!sortKey.value) return filteredTerapis.value
  return [...filteredTerapis.value].sort((a,b)=>{
    const A=a[sortKey.value]||'', B=b[sortKey.value]||''
    if(A<B) return sortAsc.value?-1:1
    if(A>B) return sortAsc.value?1:-1
    return 0
  })
})

const totalPages = computed(()=>Math.ceil(sortedTerapis.value.length/perPage))
const paginatedTerapis = computed(()=>{
  const s=(currentPage.value-1)*perPage
  return sortedTerapis.value.slice(s,s+perPage)
})

const sortBy = (key)=>{
  if(sortKey.value===key) sortAsc.value=!sortAsc.value
  else { sortKey.value=key; sortAsc.value=true }
}

const saveMap = async () => {
  if(!map.value.terapis_id || !map.value.ruangan_id){
    toast.warning('Pilih terapis & ruangan dulu')
    return
  }
  try{
    await api.post('/terapis-ruangan', map.value)
    toast.success('Mapping berhasil disimpan')
    map.value={terapis_id:'',ruangan_id:''}
    fetchMapping()
  }catch(e){
    toast.error('Gagal menyimpan mapping')
  }
}

const hapusMap = async (m)=>{
  if(!confirm('Hapus mapping ini?')) return
  try{
    await api.delete(`/terapis/${m.terapis_id}/ruangan/${m.ruangan_id}`)
    toast.success('Mapping dihapus')
    fetchMapping()
  }catch{
    toast.error('Gagal menghapus')
  }
}

onMounted(async ()=>{
  await fetchTerapis()
  await fetchRuangan()
  await fetchMapping()
})
</script>

<style scoped>
.th { @apply px-4 py-3 text-xs font-semibold text-gray-600 uppercase; }
.td { @apply px-4 py-3 text-gray-700; }

.badge { @apply bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs shadow; }

.tab { @apply px-5 py-2 rounded-xl bg-gray-200 text-gray-700 font-semibold transition hover:bg-gray-300; }
.tab-active { @apply px-5 py-2 rounded-xl bg-indigo-600 text-white font-semibold shadow-md scale-105; }

.btn-primary { @apply bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl shadow; }
.btn-delete { @apply bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg shadow; }

.input { @apply border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-400; }

.page-btn { @apply px-3 py-1 border rounded-lg text-sm hover:bg-gray-100; }
.page-btn.active { @apply bg-indigo-600 text-white border-indigo-600 shadow; }
</style>
