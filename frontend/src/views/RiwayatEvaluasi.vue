<script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  
  /* ======================
     STATE
  ====================== */
  const dataList = ref([])
  const search = ref('')
  
  const showModal = ref(false)
  const selected = ref(null)
  
  /* ======================
     LOAD DATA
  ====================== */
  const loadData = async () => {
    try {
      const res = await axios.get(
        'http://localhost:8000/api/riwayat-evaluasi',
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        }
      )
      dataList.value = res.data
    } catch (e) {
      alert('Gagal memuat data evaluasi')
    }
  }
  
  onMounted(loadData)
  
  /* ======================
     FILTER
  ====================== */
  const filtered = () => {
    if (!search.value) return dataList.value
    return dataList.value.filter(d =>
      d.nama_anak.toLowerCase().includes(search.value.toLowerCase())
    )
  }
  
  /* ======================
     MODAL
  ====================== */
  const openModal = (row) => {
    selected.value = row
    showModal.value = true
  }
  
  const closeModal = () => {
    showModal.value = false
    selected.value = null
  }
  
  /* ======================
     CETAK PDF PER ITEM
  ====================== */
  const cetakItem = (id) => {
    if (!id) {
      alert('ID evaluasi tidak ditemukan')
      return
    }
  
    const baseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'
  
    window.open(
      `${baseUrl}/api/cetak/evaluasi-anak/${id}/pdf`,
      '_blank'
    )
  }
  
  </script>
  
  <template>
    <div class="p-6 bg-white rounded-xl shadow">
  
      <!-- HEADER -->
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-bold">
          Riwayat Pembuatan Laporan Evaluasi
        </h2>
  
        <input
          v-model="search"
          placeholder="Search nama anak..."
          class="border rounded px-3 py-1 text-sm"
        />
      </div>
  
      <!-- TABLE -->
      <div class="overflow-x-auto">
        <table class="w-full border text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="th">No</th>
              <th class="th">Id Pendaftaran</th>
              <th class="th">Nama Anak</th>
              <th class="th">Tanggal Pembuatan</th>
              <th class="th">Kategori Layanan</th>
              <th class="th">Jumlah Sesi</th>
              <th class="th">Aksi</th>
            </tr>
          </thead>
  
          <tbody>
            <tr
              v-for="(d, i) in filtered()"
              :key="d.id"
              class="border-t hover:bg-gray-50"
            >
              <td class="td text-center">{{ i + 1 }}</td>
              <td class="td">{{ d.registrasi_anak_id }}</td>
              <td class="td">{{ d.nama_anak }}</td>
              <td class="td">{{ d.tanggal_pembuatan }}</td>
              <td class="td">{{ d.kategori_layanan ?? '-' }}</td>
              <td class="td text-center">{{ d.total_sesi }}</td>
              <td class="td text-center space-x-2">
                <button class="btn-view" @click="openModal(d)">
                  View
                </button>
                <button class="btn-cetak" @click="cetakItem(d.id)">
                  Cetak
                </button>
              </td>
            </tr>
  
            <tr v-if="filtered().length === 0">
              <td colspan="7" class="text-center py-4 text-gray-400">
                Data tidak tersedia
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- ======================
           MODAL DETAIL
      ======================= -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
      >
        <div class="bg-white w-full max-w-3xl rounded-lg p-6 max-h-[90vh] overflow-y-auto">
  
          <!-- HEADER -->
          <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="font-semibold text-lg">
              Detail Hasil Evaluasi
            </h3>
            <button @click="closeModal">✕</button>
          </div>
  
          <!-- INFO -->
          <div class="grid grid-cols-2 gap-4 text-sm mb-4">
            <div>Nama Anak: <strong>{{ selected?.nama_anak }}</strong></div>
            <div>Tanggal: <strong>{{ selected?.tanggal_pembuatan }}</strong></div>
            <div>Kategori Layanan: <strong>{{ selected?.kategori_layanan }}</strong></div>
            <div>Total Sesi: <strong>{{ selected?.total_sesi }}</strong></div>
          </div>
  
          <!-- DETAIL -->
          <div class="space-y-3 text-sm">
            <div v-for="(label, key) in {
              komponen_perilaku: 'Komponen Perilaku',
              kondisi_awal: 'Kondisi Awal',
              kondisi_saat_ini: 'Kondisi Saat Ini',
              kemampuan_sebelumnya: 'Kemampuan Sebelumnya',
              peningkatan_kemampuan_saat_ini: 'Peningkatan Kemampuan Saat Ini',
              program_lanjutan: 'Program Lanjutan',
              kesimpulan_hasil_followup: 'Kesimpulan Follow Up',
              saran_terapi: 'Saran Terapi'
            }" :key="key">
              <p class="font-semibold mb-1">{{ label }}</p>
              <div class="box">
                {{ selected?.[key] || '-' }}
              </div>
            </div>
          </div>
  
          <!-- FOOTER -->
          <div class="flex justify-end mt-6">
            <button
              class="border px-4 py-2 rounded hover:bg-gray-100"
              @click="closeModal"
            >
              Tutup
            </button>
          </div>
  
        </div>
      </div>
  
    </div>
  </template>
  
  <style scoped>
  .th {
    padding: 8px;
    border: 1px solid #ddd;
    font-weight: 600;
  }
  .td {
    padding: 8px;
    border: 1px solid #ddd;
  }
  .btn-view {
    border: 1px solid #ccc;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
  }
  .btn-cetak {
    background: #22c55e;
    color: white;
    padding: 3px 10px;
    border-radius: 4px;
    font-size: 12px;
  }
  .box {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px;
    background: #f9fafb;
    min-height: 40px;
  }
  </style>
  