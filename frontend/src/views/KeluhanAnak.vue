<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <h2 class="text-2xl font-bold mb-6">Keluhan</h2>

    <div class="grid grid-cols-3 gap-6">
      <!-- ================= TABEL KELUHAN ================= -->
      <div class="col-span-2 bg-white rounded-xl shadow p-4">
        <div class="flex justify-between items-center mb-4">
          <input
            v-model="search"
            placeholder="Search..."
            class="border rounded-lg px-3 py-2 text-sm"
          />
        </div>

        <table class="w-full text-sm border">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-2 py-2 border">No</th>
              <th class="px-2 py-2 border">No Keluhan</th>
              <th class="px-2 py-2 border">Kategori</th>
              <th class="px-2 py-2 border">Tanggal</th>
              <th class="px-2 py-2 border">Detail</th>
              <th class="px-2 py-2 border">Status</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(k, i) in filteredKeluhan"
              :key="k.id"
              class="hover:bg-gray-50"
            >
              <td class="border px-2 py-2 text-center">{{ i + 1 }}</td>
              <td class="border px-2 py-2">{{ k.no_keluhan }}</td>
              <td class="border px-2 py-2">{{ k.kategori_keluhan }}</td>
              <td class="border px-2 py-2">
                {{ formatDate(k.tanggal_keluhan) }}
              </td>
              <td class="border px-2 py-2 text-center">
                <button
                  class="bg-indigo-600 text-white px-3 py-1 rounded"
                  @click="activeKeluhan = k"
                >
                  Detail
                </button>
              </td>
              <td class="border px-2 py-2 text-center">
                <span
                  class="text-xs px-2 py-1 rounded-full"
                  :class="
                    k.tanggapan_keluhan
                      ? 'bg-green-100 text-green-700'
                      : 'bg-orange-100 text-orange-700'
                  "
                >
                  {{ k.tanggapan_keluhan ? 'Sudah Ditanggapi' : 'Belum Ditanggapi' }}
                </span>
              </td>
            </tr>

            <tr v-if="filteredKeluhan.length === 0">
              <td colspan="6" class="text-center py-4 text-gray-400">
                Belum ada keluhan
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- ================= FORM INPUT ================= -->
      <div class="bg-white rounded-xl shadow p-5">
        <h3 class="font-semibold mb-5"> Keluhan</h3>

        <div class="mb-3">
          <label class="text-sm">Nama Anak</label>
          <select
            v-model="form.profile_anak_id"
            class="input"
            @change="getDataByAnak"
            disabled="true"
          >
            <option value=""></option>
            <option v-for="a in anak" :key="a.id" :value="a.id" disabled>
              {{ a.nama_anak }}
            </option>
          </select>
        </div>

        <div class="mb-3">
          <label class="text-sm">Kategori Keluhan</label>
          <select v-model="form.kategori_keluhan" class="input">
            <option value="">Pilih Kategori</option>
            <option>Pelayanan Administrasi</option>
            <option>Kualitas Terapi</option>
            <option>Sarana & Prasarana</option>
            <option>Jadwal / Waktu Pelayanan</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="text-sm">Isi Keluhan</label>
          <textarea v-model="form.isi" rows="5" class="input"></textarea>
        </div>

        <div class="flex justify-end gap-2 mt-4">
          <button class="px-4 py-2 border rounded" @click="resetForm">
            Batal
          </button>
          <button
            class="px-4 py-2 bg-blue-600 text-white rounded"
            @click="kirimKeluhan"
          >
            Kirim Keluhan
          </button>
        </div>
      </div>
    </div>

    <!-- ================= MODAL DETAIL ================= -->
    <div
      v-if="activeKeluhan"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl p-6 w-full max-w-lg">
        <div class="space-y-4">

          <!-- INFO KELUHAN (DITAMBAHKAN) -->
          <div class="border rounded-xl p-4 bg-gray-50 text-sm">
  <div class="flex justify-between">
    <div>
      <b>Kategori:</b>
      <span class="ml-1">{{ activeKeluhan.kategori_keluhan }}</span>
    </div>

    <div>
      <b>Tanggal:</b>
      <span class="ml-1">{{ formatDate(activeKeluhan.tanggal_keluhan) }}</span>
    </div>
  </div>
</div>

          <!-- ISI KELUHAN -->
          <div class="border rounded-xl p-4 bg-blue-50">
            <h3 class="font-semibold text-blue-700 mb-2">Isi Keluhan</h3>
            <p class="text-sm whitespace-pre-line text-gray-800">
              {{ activeKeluhan.isi }}
            </p>
          </div>

          <!-- TANGGAPAN -->
          <div class="border rounded-xl p-4 bg-gray-50">
            <h3 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
              Tanggapan Admin
              <span
                v-if="!activeKeluhan.tanggapan_keluhan"
                class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full"
              >
                Belum Ditanggapi
              </span>
            </h3>

            <p
              class="text-sm whitespace-pre-line"
              :class="activeKeluhan.tanggapan_keluhan
                ? 'text-gray-800'
                : 'text-gray-400 italic'"
            >
              {{ activeKeluhan.tanggapan_keluhan || 'Belum ada tanggapan dari admin.' }}
            </p>
          </div>
        </div>

        <div class="text-right mt-4">
          <button
            class="px-4 py-2 border rounded"
            @click="activeKeluhan = null"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const keluhan = ref([])
const anak = ref([])
const search = ref('')
const activeKeluhan = ref(null)

const form = ref({
  profile_anak_id: '',
  kategori_keluhan: '',
  isi: ''
})

const getDataByAnak = async () => {
  if (!form.value.profile_anak_id) return

  try {
    const res = await api.get(`/profile-anak-saya/${form.value.profile_anak_id}`)
    console.log('Anak terpilih (auto):', res.data)
  } catch (err) {
    console.error('Gagal get data anak:', err)
  }
}

onMounted(async () => {
  const resAnak = await api.get('/profile-anak-saya')
  anak.value = resAnak.data

  if (anak.value.length > 0) {
    form.value.profile_anak_id = anak.value[0].id
    await getDataByAnak()
  }

  keluhan.value = (await api.get('/keluhan-anak')).data
})

const filteredKeluhan = computed(() =>
  keluhan.value.filter(k =>
    k.no_keluhan?.toLowerCase().includes(search.value.toLowerCase())
  )
)

const kirimKeluhan = async () => {
  try {
    await api.post('/keluhan-anak', form.value)
    toast.success('Keluhan berhasil dikirim')
    resetForm()
    keluhan.value = (await api.get('/keluhan-anak')).data
  } catch {
    toast.error('Gagal mengirim keluhan')
  }
}

const resetForm = () => {
  form.value = {
    profile_anak_id: form.value.profile_anak_id,
    kategori_keluhan: '',
    isi: ''
  }
}

const formatDate = (d) => new Date(d).toLocaleDateString('id-ID')
</script>

<style scoped>
.input {
  @apply w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400;
}
</style>
