<template> 
  <div class="min-h-screen bg-gray-50 p-6">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Master Profile Anak</h2>

    <!-- CARD -->
    <div class="bg-white shadow-lg rounded-lg p-6">

      <!-- TOOLBAR -->
      <div class="flex justify-between items-center mb-6">
        <button
          class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2"
          @click="openAdd"
        >
          <span class="pi pi-plus"></span>
          Tambah Anak
        </button>

        <div class="relative w-64">
          <input
            v-model="search"
            type="text"
            placeholder="Cari anak..."
            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-green-300"
          />
          <i class="pi pi-search absolute left-3 top-2.5 text-gray-400"></i>
        </div>
      </div>

      <!-- TABLE -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 text-center text-sm font-medium text-gray-600">No</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Nama Anak</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Nama Orang Tua</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Jenis Kelamin</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Agama</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">NIK</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Alamat</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Umur</th>
              <th class="px-4 py-2 text-center text-sm font-medium text-gray-600">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-200">
            <tr
              v-for="(item, index) in filteredData"
              :key="item.id"
              class="hover:bg-gray-50"
            >
              <td class="px-4 py-2 text-center text-gray-600 font-medium">
                {{ index + 1 }}
              </td>
              <td class="px-4 py-2">{{ item.nama_anak }}</td>
              <td class="px-4 py-2">{{ item.orangTua?.nama }}</td>
              <td class="px-4 py-2">{{ item.jenis_kelamin?.nama }}</td>
              <td class="px-4 py-2">{{ item.agama?.nama }}</td>
              <td class="px-4 py-2">{{ item.NIK }}</td>
              <td class="px-4 py-2">{{ item.alamat }}</td>
              <td class="px-4 py-2">{{ item.umur }}</td>
              <td class="px-4 py-2 flex justify-center gap-2">
                <button class="text-yellow-500 hover:text-yellow-700" @click="openEdit(item)">
                  ✏️
                </button>
                <button class="text-red-500 hover:text-red-700" @click="deleteRow(item)">
                  🗑️
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL -->
    <transition name="fade">
      <div
        v-if="dialog"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
      >
        <!-- MODAL CONTAINER (LEBAR) -->
        <div class="bg-white rounded-2xl w-full max-w-6xl shadow-xl relative">

          <!-- HEADER -->
          <div class="flex justify-between items-center px-6 py-4 border-b">
            <h3 class="text-xl font-bold">
              {{ isEdit ? 'Edit Anak' : 'Tambah Anak' }}
            </h3>
            <button @click="dialog=false" class="text-gray-500 hover:text-gray-700">✕</button>
          </div>

          <!-- BODY -->
          <div class="p-6 max-h-[75vh] overflow-y-auto">
            <!-- FORM GRID 2 KOLOM -->
            <form class="grid grid-cols-1 md:grid-cols-2 gap-5">

              <div>
                <label class="label">Nama Anak</label>
                <input v-model="form.nama_anak" class="input" />
              </div>

              <div>
                <label class="label">Orang Tua</label>
                <select v-model="form.id_orang_tua" class="input">
                  <option :value="null" disabled>Pilih orang tua</option>
                  <option v-for="ot in orangTua" :key="ot.id" :value="ot.id">
                    {{ ot.nama }}
                  </option>
                </select>
              </div>

              <div>
                <label class="label">Jenis Kelamin</label>
                <select v-model="form.jenis_kelamin_id" class="input">
                  <option :value="null" disabled>Pilih</option>
                  <option v-for="jk in jenisKelamin" :key="jk.id" :value="jk.id">
                    {{ jk.nama }}
                  </option>
                </select>
              </div>

              <div>
                <label class="label">Agama</label>
                <select v-model="form.agama_id" class="input">
                  <option :value="null" disabled>Pilih</option>
                  <option v-for="ag in agama" :key="ag.id" :value="ag.id">
                    {{ ag.nama }}
                  </option>
                </select>
              </div>

              <div>
                <label class="label">NIK</label>
                <input v-model="form.NIK" class="input" />
              </div>

              <div>
                <label class="label">Umur</label>
                <!-- <input type="number" v-model="form.umur" class="input" /> -->
                 <input
                  type="text"
                  :value="form.umur !== null ? form.umur + ' th' : ''"
                  class="input bg-gray-100 cursor-not-allowed"
                  readonly
                />
              </div>

              <!-- FULL WIDTH -->
              <div class="md:col-span-2">
                <label class="label">Alamat</label>
                <textarea v-model="form.alamat" rows="2" class="input"></textarea>
              </div>

              <div>
                <label class="label">Tanggal Lahir</label>
                <input type="date" v-model="form.tanggal_lahir" class="input" />
              </div>

              <div>
                <label class="label">Tempat Lahir</label>
                <input v-model="form.tempat_lahir" class="input" />
              </div>

              <div>
                <label class="label">No HP Orang Tua</label>
                <input v-model="form.no_hp_orang_tua" class="input" />
              </div>

              <div>
                <label class="label">Status</label>
                <select v-model="form.status_aktif" class="input">
                  <option :value="true">Aktif</option>
                  <option :value="false">Nonaktif</option>
                </select>
              </div>

            </form>
          </div>

          <!-- FOOTER -->
          <div class="flex justify-end gap-3 px-6 py-4 border-t">
            <button class="btn-secondary" @click="dialog=false">Batal</button>
            <button class="btn-primary" @click="save">Simpan</button>
          </div>

        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../axios'
import { watch } from 'vue'
import { useToast } from 'vue-toastification'

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


const toast = useToast()

const data = ref([])
const orangTua = ref([])
const jenisKelamin = ref([])
const agama = ref([])

const search = ref('')
const dialog = ref(false)
const isEdit = ref(false)

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
  status_aktif: true,
}

const form = ref({ ...formDefault })

onMounted(loadData)

async function loadData() {
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
  }
}

const deleteRow = async (row) => {
  await api.delete(`/profile-anak/${row.id}`)
  loadData()
}

watch(
  () => form.value.tanggal_lahir,
  (val) => {
    const hasil = hitungUmur(val)
    form.value.umur = hasil
  }
)
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.input {
  @apply w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-green-300;
}
.btn-primary {
  @apply bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold;
}
.btn-secondary {
  @apply bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg font-semibold;
}
.label {
  @apply text-sm font-medium text-gray-700;
}
</style>
