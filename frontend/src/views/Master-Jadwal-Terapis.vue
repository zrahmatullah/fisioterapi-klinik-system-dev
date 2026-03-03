<template>
  <div class="p-6">
    <h1 class="text-2xl font-semibold mb-6">Manajemen Jadwal</h1>

    <!-- TAB -->
    <div class="flex gap-6 border-b mb-6">
      <button @click="activeTab='master'" :class="tabStyle('master')">Master Jadwal</button>
      <button @click="activeTab='user'" :class="tabStyle('user')">Jadwal Terapis</button>
    </div>

    <!-- MASTER JADWAL -->
    <div v-if="activeTab==='master'" class="space-y-4">
      <div class="flex justify-end">
        <button @click="openMasterCreate" class="btn-primary">+ Tambah Jadwal</button>
      </div>

      <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="th">Hari</th>
              <th class="th">Jam</th>
              <th class="th">Status</th>
              <th class="th text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="j in jadwalMaster" :key="j.id" class="border-t hover:bg-gray-50">
              <td class="td">{{ hariLabel(j.hari) }}</td>
              <td class="td">{{ j.jam_mulai }} - {{ j.jam_selesai }}</td>
              <td class="td">
                <span :class="j.status_aktif ? badgeActive : badgeInactive">
                  {{ j.status_aktif ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="td text-center">
                <button @click="openMasterEdit(j)" class="link">Edit</button>
                <button @click="deleteMaster(j.id)" class="link text-red-600 ml-3">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- JADWAL TERAPIS -->
    <div v-if="activeTab==='user'" class="space-y-4">

      <!-- pilih terapis -->
      <div class="bg-white p-4 rounded-xl shadow">
        <label class="label">Pilih Terapis</label>
        <select v-model="selectedUser" @change="fetchJadwalUser" class="input">
          <option value="">-- Pilih Terapis --</option>
          <option v-for="u in terapis" :key="u.id" :value="u.id">
            {{ u.nama }}
          </option>
        </select>
      </div>

      <div v-if="selectedUser" class="space-y-6">

        <!-- jadwal saat ini -->
        <div class="bg-white p-5 rounded-xl shadow">
          <h3 class="font-semibold mb-3">Jadwal Terapis Saat Ini</h3>

          <div v-if="jadwalUserDetail.length">
            <div
              v-for="j in jadwalUserDetail"
              :key="j.id"
              class="border rounded-lg p-3 mb-2"
            >
              <div class="text-sm font-medium">
                {{ hariLabel(j.jadwal_master.hari) }}
              </div>
              <div class="text-xs text-gray-500">
                {{ j.jadwal_master.jam_mulai }} - {{ j.jadwal_master.jam_selesai }}
              </div>
            </div>
          </div>

          <div v-else class="text-xs text-gray-400">
            Belum ada jadwal
          </div>
        </div>

        <!-- atur jadwal -->
        <div class="bg-white p-5 rounded-xl shadow">
          <h3 class="font-semibold mb-3">Atur Jadwal Terapis</h3>

          <div class="grid md:grid-cols-2 gap-4">
            <label
              v-for="j in jadwalMaster"
              :key="j.id"
              class="border rounded-lg p-4 flex items-start gap-3 hover:bg-gray-50"
            >
              <input type="checkbox" :value="j.id" v-model="jadwalUser" />
              <div>
                <div class="font-medium">{{ hariLabel(j.hari) }}</div>
                <div class="text-xs text-gray-500">
                  {{ j.jam_mulai }} - {{ j.jam_selesai }}
                </div>
              </div>
            </label>
          </div>

          <div class="flex justify-end mt-6">
            <button @click="saveJadwalUser" class="btn-primary">
              Simpan Jadwal Terapis
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- MODAL MASTER -->
    <transition name="fade-scale">
      <div v-if="showMasterModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl w-full max-w-md p-6">
          <h2 class="font-semibold mb-4">{{ editMasterId ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h2>

          <div class="space-y-3">
            <select v-model="masterForm.hari" class="input">
              <option v-for="(h,i) in hariList" :key="i" :value="i">{{ h }}</option>
            </select>
            <input type="time" v-model="masterForm.jam_mulai" class="input" />
            <input type="time" v-model="masterForm.jam_selesai" class="input" />
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" v-model="masterForm.status_aktif" /> Aktif
            </label>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button @click="closeMasterModal" class="btn-secondary">Batal</button>
            <button @click="saveMaster" class="btn-primary">Simpan</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- TOAST -->
    <transition name="fade">
      <div
        v-if="toast.show"
        class="fixed top-6 right-6 px-4 py-3 rounded-lg text-white shadow-lg"
        :class="toast.type==='success' ? 'bg-green-600' : 'bg-red-600'"
      >
        {{ toast.message }}
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/axios'

const activeTab = ref('master')
const jadwalMaster = ref([])
const terapis = ref([])
const selectedUser = ref('')
const jadwalUser = ref([])
const jadwalUserDetail = ref([])

const showMasterModal = ref(false)
const editMasterId = ref(null)

const masterForm = ref({ hari:1, jam_mulai:'', jam_selesai:'', status_aktif:true })
const hariList = ['Minggu','Senin','Selasa','Rabu','Jumat','Sabtu','Kamis']

const toast = ref({ show:false, message:'', type:'success' })

const tabStyle = t => activeTab.value===t
  ? 'pb-2 border-b-2 border-blue-600 font-medium'
  : 'pb-2 text-gray-500'

const hariLabel = h => hariList[h]

const showToast = (m,t='success')=>{
  toast.value={show:true,message:m,type:t}
  setTimeout(()=>toast.value.show=false,3000)
}

const fetchMaster = async()=>{
  const r=await api.get('/jadwal-master')
  jadwalMaster.value=r.data.data
}

const fetchTerapis = async()=>{
  const r=await api.get('/user-profile?jenis_user_id=9')
  terapis.value=r.data.data
}

const fetchJadwalUser = async()=>{
  const r=await api.get(`/jadwal-user?user_profile_id=${selectedUser.value}`)
  jadwalUser.value = r.data.data.map(j=>j.jadwal_master_id)
  jadwalUserDetail.value = r.data.data
}

const saveJadwalUser = async()=>{
  await api.post('/jadwal-user/sync',{
    user_profile_id:selectedUser.value,
    jadwal_master_id:jadwalUser.value
  })
  showToast('Jadwal terapis berhasil disimpan')
  fetchJadwalUser()
}

const openMasterCreate=()=>{
  editMasterId.value=null
  masterForm.value={hari:1,jam_mulai:'',jam_selesai:'',status_aktif:true}
  showMasterModal.value=true
}

const openMasterEdit=j=>{
  editMasterId.value=j.id
  masterForm.value={...j}
  showMasterModal.value=true
}

const saveMaster=async()=>{
  editMasterId.value
    ? await api.put(`/jadwal-master/${editMasterId.value}`,masterForm.value)
    : await api.post('/jadwal-master',masterForm.value)

  showMasterModal.value=false
  fetchMaster()
  showToast('Jadwal master disimpan')
}

const deleteMaster=async(id)=>{
  if(!confirm('Hapus jadwal?'))return
  await api.delete(`/jadwal-master/${id}`)
  fetchMaster()
  showToast('Jadwal dihapus')
}

const closeMasterModal=()=>showMasterModal.value=false

onMounted(async()=>{
  await fetchMaster()
  await fetchTerapis()
})
</script>

<style scoped>
.input{ @apply border rounded-lg px-3 py-2 w-full }
.th{ @apply p-3 text-left }
.td{ @apply p-3 }
.link{ @apply text-blue-600 hover:underline }
.btn-primary{ @apply bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 }
.btn-secondary{ @apply border px-4 py-2 rounded-lg }
.badgeActive{ @apply bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs }
.badgeInactive{ @apply bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs }
.fade-scale-enter-active,.fade-scale-leave-active{transition:.2s}
.fade-scale-enter-from,.fade-scale-leave-to{opacity:0;transform:scale(.95)}
.fade-enter-active,.fade-leave-active{transition:.3s}
.fade-enter-from,.fade-leave-to{opacity:0}
</style>
