<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- 1. HEADER & ACTIONS -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col md:flex-row md:items-center md:justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm">
          <i class="pi pi-calendar-plus"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">
            Mapping Jadwal Praktik Dokter & Terapis
          </h2>
          <p class="text-xs text-[#64748B] mt-0.5">
            Pengaturan jadwal kerja klinis, alokasi ruangan poli/terapi, dan pembagian kuota pasien harian
          </p>
        </div>
      </div>

      <div class="flex items-center gap-2.5">
        <button
          @click="openModalAdd"
          class="btn-primary-clinical text-xs py-2 px-3.5"
        >
          <i class="pi pi-plus text-xs"></i>
          <span>Tambah Jadwal Praktik</span>
        </button>
      </div>
    </div>

    <!-- 2. SUMMARY KPI -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 shrink-0">
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Jadwal Praktik</span>
        <div class="flex items-baseline justify-between mt-1">
          <h3 class="text-xl font-bold text-[#0F172A] tabular-nums">{{ listJadwal.length }}</h3>
          <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200">
            Terjadwal
          </span>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-xs font-semibold text-teal-800 uppercase tracking-wider flex items-center gap-1.5">
          <i class="pi pi-heart text-teal-600 text-xs"></i>
          Dokter Spesialis
        </span>
        <div class="flex items-baseline justify-between mt-1">
          <h3 class="text-xl font-bold text-[#0F766E] tabular-nums">
            {{ listJadwal.filter(j => j.jenis === 'Dokter').length }}
          </h3>
          <span class="text-xs text-slate-500">Sp.A & Psikolog</span>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-xs font-semibold text-cyan-800 uppercase tracking-wider flex items-center gap-1.5">
          <i class="pi pi-users text-cyan-600 text-xs"></i>
          Terapis Klinis
        </span>
        <div class="flex items-baseline justify-between mt-1">
          <h3 class="text-xl font-bold text-cyan-800 tabular-nums">
            {{ listJadwal.filter(j => j.jenis === 'Terapis').length }}
          </h3>
          <span class="text-xs text-slate-500">Wicara, Okupasi, SI</span>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-xs font-semibold text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
          <i class="pi pi-check-circle text-emerald-600 text-xs"></i>
          Total Kuota Pasien
        </span>
        <div class="flex items-baseline justify-between mt-1">
          <h3 class="text-xl font-bold text-emerald-800 tabular-nums">54</h3>
          <span class="text-xs text-emerald-600 font-medium">Sesi / Hari</span>
        </div>
      </div>
    </div>

    <!-- 3. DAY SELECTOR & FILTERS -->
    <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-[0_1px_3px_rgba(15,23,42,0.04)] space-y-2.5 shrink-0">
      <!-- Day Pills -->
      <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none custom-scrollbar">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider shrink-0 mr-1">Hari:</span>
        <button
          v-for="hari in days"
          :key="hari"
          @click="selectedDay = hari"
          class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition shrink-0 cursor-pointer"
          :class="selectedDay === hari ? 'bg-[#0F766E] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
        >
          {{ hari }}
        </button>
      </div>

      <!-- Secondary Filters -->
      <div class="flex flex-col md:flex-row items-center justify-between gap-3 pt-2 border-t border-slate-100">
        <!-- Search Name -->
        <div class="relative w-full md:w-72">
          <i class="pi pi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
          <input
            v-model="searchKeyword"
            type="text"
            placeholder="Cari nama dokter, terapis, ruangan..."
            class="w-full pl-9 pr-3.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 transition"
          />
        </div>

        <div class="flex items-center gap-2.5 w-full md:w-auto">
          <!-- Role Filter -->
          <select
            v-model="filterJenis"
            class="text-xs border border-slate-300 rounded-lg px-3 py-1.5 bg-white text-slate-700 outline-none focus:border-[#0F766E]"
          >
            <option value="">Semua Peran</option>
            <option value="Dokter">Dokter Spesialis</option>
            <option value="Terapis">Terapis Klinis</option>
          </select>

          <!-- Shift Filter -->
          <select
            v-model="filterShift"
            class="text-xs border border-slate-300 rounded-lg px-3 py-1.5 bg-white text-slate-700 outline-none focus:border-[#0F766E]"
          >
            <option value="">Semua Shift</option>
            <option value="Pagi">Shift Pagi (08:00 - 12:00)</option>
            <option value="Siang">Shift Siang (13:00 - 17:00)</option>
          </select>
        </div>
      </div>
    </div>

    <!-- 4. SCHEDULE TABLE -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="min-w-full text-sm">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr class="border-b border-slate-200 bg-slate-50/90">
              <th class="th-clinical text-center w-12">No</th>
              <th class="th-clinical">Tenaga Medis</th>
              <th class="th-clinical">Peran & Disiplin</th>
              <th class="th-clinical">Hari Praktik</th>
              <th class="th-clinical">Jam Kerja (WIB)</th>
              <th class="th-clinical">Ruangan / Poli</th>
              <th class="th-clinical text-center">Kuota & Keterisian</th>
              <th class="th-clinical text-center">Status</th>
              <th class="th-clinical text-center w-28">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(item, index) in filteredJadwal"
              :key="item.id"
              class="hover:bg-slate-50/80 transition-colors"
            >
              <!-- 1. Index -->
              <td class="td-clinical text-center font-mono text-xs text-slate-400">
                {{ index + 1 }}
              </td>

              <!-- 2. Tenaga Medis -->
              <td class="td-clinical">
                <div class="flex items-center gap-2.5">
                  <div
                    class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0"
                    :class="item.jenis === 'Dokter' ? 'bg-teal-100 text-teal-800 border border-teal-200' : 'bg-cyan-100 text-cyan-800 border border-cyan-200'"
                  >
                    {{ item.nama.charAt(item.nama.startsWith('dr.') ? 4 : 0) }}
                  </div>
                  <div>
                    <p class="font-bold text-slate-900 text-sm leading-tight">{{ item.nama }}</p>
                    <p class="text-[11px] text-slate-500 font-mono mt-0.5">NIP: {{ item.nip }}</p>
                  </div>
                </div>
              </td>

              <!-- 3. Peran & Disiplin -->
              <td class="td-clinical">
                <span
                  class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold"
                  :class="item.jenis === 'Dokter' ? 'bg-teal-50 text-teal-700 border border-teal-200' : 'bg-cyan-50 text-cyan-700 border border-cyan-200'"
                >
                  {{ item.spesialisasi }}
                </span>
              </td>

              <!-- 4. Hari Praktik -->
              <td class="td-clinical">
                <span class="font-bold text-slate-800 text-xs px-2 py-1 rounded bg-slate-100 border border-slate-200">
                  {{ item.hari }}
                </span>
              </td>

              <!-- 5. Jam Kerja -->
              <td class="td-clinical">
                <p class="text-xs font-semibold text-slate-800 tabular-nums">
                  {{ item.jam_mulai }} - {{ item.jam_selesai }} WIB
                </p>
                <p class="text-[11px] text-slate-500">
                  {{ item.jam_mulai < '12:00' ? 'Shift Pagi' : 'Shift Siang' }}
                </p>
              </td>

              <!-- 6. Ruangan -->
              <td class="td-clinical">
                <div class="flex items-center gap-1.5">
                  <i class="pi pi-building text-slate-400 text-xs"></i>
                  <span class="font-medium text-slate-800 text-xs">{{ item.ruangan }}</span>
                </div>
              </td>

              <!-- 7. Kuota & Keterisian -->
              <td class="td-clinical text-center">
                <div class="inline-block text-left w-28">
                  <div class="flex justify-between text-[11px] font-medium text-slate-600 mb-1">
                    <span>Terisi: {{ item.terisi }}/{{ item.kuota }}</span>
                    <span class="tabular-nums font-bold text-[#0F766E]">{{ Math.round((item.terisi / item.kuota) * 100) }}%</span>
                  </div>
                  <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                    <div
                      class="h-full rounded-full transition-all"
                      :class="item.terisi >= item.kuota ? 'bg-rose-500' : 'bg-[#0F766E]'"
                      :style="{ width: (item.terisi / item.kuota) * 100 + '%' }"
                    ></div>
                  </div>
                </div>
              </td>

              <!-- 8. Status -->
              <td class="td-clinical text-center">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                  :class="item.status_aktif ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="item.status_aktif ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                  {{ item.status_aktif ? 'Aktif' : 'Libur' }}
                </span>
              </td>

              <!-- 9. Aksi -->
              <td class="td-clinical text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    @click="editJadwal(item)"
                    class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-[#E6F2F5] text-slate-600 hover:text-[#0F766E] hover:border-teal-400 flex items-center justify-center transition cursor-pointer"
                    title="Ubah Pemetaan Jadwal"
                  >
                    <i class="pi pi-pencil text-xs"></i>
                  </button>

                  <button
                    @click="toggleStatus(item)"
                    class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 flex items-center justify-center transition cursor-pointer"
                    :title="item.status_aktif ? 'Set Libur/Nonaktif' : 'Aktifkan Jadwal'"
                  >
                    <i :class="item.status_aktif ? 'pi pi-ban text-rose-500' : 'pi pi-check text-emerald-600'" class="text-xs"></i>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="filteredJadwal.length === 0">
              <td colspan="9" class="py-14 text-center">
                <div class="flex flex-col items-center justify-center">
                  <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                    <i class="pi pi-calendar-times text-xl"></i>
                  </div>
                  <p class="font-semibold text-slate-700 text-sm">Tidak ada jadwal pada kriteria ini</p>
                  <p class="text-xs text-slate-500 mt-0.5">Pilih hari lain atau sesuaikan filter peranan</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================= MODAL TAMBAH / EDIT JADWAL ================= -->
    <teleport to="body">
    <div v-if="showModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showModal = false"></div>

      <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-fadeIn">
        <!-- MODAL HEADER -->
        <div class="bg-[#0B3948] text-white px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <i class="pi pi-calendar-plus text-teal-300 text-base"></i>
            <h3 class="text-base font-bold tracking-wide">
              {{ editingId ? 'Edit Pemetaan Jadwal Praktik' : 'Tambah Pemetaan Jadwal Praktik' }}
            </h3>
          </div>
          <button @click="showModal = false" class="text-slate-300 hover:text-white p-1">
            <i class="pi pi-times text-sm"></i>
          </button>
        </div>

        <!-- FORM BODY -->
        <form @submit.prevent="saveJadwal" class="p-6 space-y-4 text-sm">
          <!-- Tenaga Medis -->
          <div>
            <label class="label-clinical">Tenaga Medis (Dokter / Terapis)</label>
            <select v-model="form.nama" required class="input-clinical">
              <option value="">Pilih tenaga medis...</option>
              <option value="dr. Alkar As Rahmatulloh, Sp.A">dr. Alkar As Rahmatulloh, Sp.A (Dokter Spesialis Anak)</option>
              <option value="dr. Maya Kartika, Sp.KFR">dr. Maya Kartika, Sp.KFR (Spesialis Kedokteran Fisik & Rehab)</option>
              <option value="Siti Rahmawati, S.Tr.Kes">Siti Rahmawati, S.Tr.Kes (Terapis Wicara)</option>
              <option value="Dimas Prasetyo, A.Md.OT">Dimas Prasetyo, A.Md.OT (Terapis Okupasi & SI)</option>
              <option value="Nadia Fitriani, S.Psi, M.Psi">Nadia Fitriani, S.Psi, M.Psi (Psikolog Tumbuh Kembang)</option>
              <option value="Farhan Maulana, S.Ft">Farhan Maulana, S.Ft (Fisioterapis Pediatrik)</option>
            </select>
          </div>

          <!-- Hari & Shift -->
          <div class="grid grid-cols-2 gap-3.5">
            <div>
              <label class="label-clinical">Hari Praktik</label>
              <select v-model="form.hari" required class="input-clinical">
                <option v-for="h in ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']" :key="h" :value="h">
                  {{ h }}
                </option>
              </select>
            </div>

            <div>
              <label class="label-clinical">Ruangan / Poli</label>
              <select v-model="form.ruangan" required class="input-clinical">
                <option value="Poli Tumbuh Kembang 01">Poli Tumbuh Kembang 01</option>
                <option value="Poli Konsultasi Psikologi">Poli Konsultasi Psikologi</option>
                <option value="Ruang Terapi Wicara 01">Ruang Terapi Wicara 01</option>
                <option value="Ruang Sensori Integrasi 01">Ruang Sensori Integrasi 01</option>
                <option value="Ruang Fisioterapi Anak">Ruang Fisioterapi Anak</option>
              </select>
            </div>
          </div>

          <!-- Jam Praktik -->
          <div class="grid grid-cols-2 gap-3.5">
            <div>
              <label class="label-clinical">Jam Mulai</label>
              <input v-model="form.jam_mulai" type="time" required class="input-clinical" />
            </div>

            <div>
              <label class="label-clinical">Jam Selesai</label>
              <input v-model="form.jam_selesai" type="time" required class="input-clinical" />
            </div>
          </div>

          <!-- Kuota Pasien -->
          <div class="grid grid-cols-2 gap-3.5">
            <div>
              <label class="label-clinical">Batas Kuota Pasien (Sesi)</label>
              <input v-model.number="form.kuota" type="number" min="1" max="50" required class="input-clinical" />
            </div>

            <div>
              <label class="label-clinical">Status Jadwal</label>
              <select v-model="form.status_aktif" class="input-clinical">
                <option :value="true">Aktif Praktik</option>
                <option :value="false">Nonaktif / Cuti</option>
              </select>
            </div>
          </div>

          <!-- MODAL ACTIONS -->
          <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
            <button type="button" @click="showModal = false" class="btn-secondary-clinical">
              Batal
            </button>
            <button type="submit" class="btn-primary-clinical">
              <i class="pi pi-check text-sm"></i>
              <span>Simpan Pemetaan</span>
            </button>
          </div>
        </form>
      </div>
    </div>
    </teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import api from '../axios'

const toast = useToast()

const days = ['Semua Hari', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
const selectedDay = ref('Semua Hari')
const searchKeyword = ref('')
const filterJenis = ref('')
const filterShift = ref('')
const loading = ref(false)

const showModal = ref(false)
const editingId = ref(null)

const form = ref({
  nama: '',
  hari: 'Senin',
  ruangan: 'Poli Tumbuh Kembang 01',
  jam_mulai: '08:00',
  jam_selesai: '12:00',
  kuota: 8,
  status_aktif: true
})

// Connected to Backend API
const listJadwal = ref([])

const fetchJadwal = async () => {
  loading.value = true
  try {
    const res = await api.get('/mapping-jadwal')
    listJadwal.value = res.data?.data || []
  } catch (err) {
    console.error('Failed to fetch mapping-jadwal:', err)
  } finally {
    loading.value = false
  }
}

const filteredJadwal = computed(() => {
  return listJadwal.value.filter(j => {
    // Hari filter
    if (selectedDay.value !== 'Semua Hari' && j.hari !== selectedDay.value) {
      return false
    }

    // Role filter
    if (filterJenis.value && j.jenis !== filterJenis.value) {
      return false
    }

    // Shift filter
    if (filterShift.value === 'Pagi' && j.jam_mulai >= '12:00') {
      return false
    }
    if (filterShift.value === 'Siang' && j.jam_mulai < '12:00') {
      return false
    }

    // Keyword
    if (searchKeyword.value.trim()) {
      const q = searchKeyword.value.toLowerCase().trim()
      const n = j.nama.toLowerCase()
      const r = j.ruangan.toLowerCase()
      const s = j.spesialisasi.toLowerCase()
      if (!n.includes(q) && !r.includes(q) && !s.includes(q)) return false
    }

    return true
  })
})

const openModalAdd = () => {
  editingId.value = null
  form.value = {
    nama: '',
    hari: selectedDay.value !== 'Semua Hari' ? selectedDay.value : 'Senin',
    ruangan: 'Poli Tumbuh Kembang 01',
    jam_mulai: '08:00',
    jam_selesai: '12:00',
    kuota: 8,
    status_aktif: true
  }
  showModal.value = true
}

const editJadwal = (item) => {
  editingId.value = item.id
  form.value = {
    nama: item.nama,
    hari: item.hari,
    ruangan: item.ruangan,
    jam_mulai: item.jam_mulai,
    jam_selesai: item.jam_selesai,
    kuota: item.kuota,
    status_aktif: item.status_aktif
  }
  showModal.value = true
}

const saveJadwal = async () => {
  try {
    const res = await api.post('/mapping-jadwal', form.value)
    if (editingId.value) {
      const idx = listJadwal.value.findIndex(x => x.id === editingId.value)
      if (idx !== -1) {
        listJadwal.value[idx] = {
          ...listJadwal.value[idx],
          ...form.value
        }
      }
      toast.success('Jadwal praktik berhasil diperbarui')
    } else {
      listJadwal.value.unshift(res.data.data)
      toast.success('Jadwal baru berhasil ditambahkan ke database')
    }
  } catch (err) {
    toast.error('Gagal menyimpan jadwal')
  } finally {
    showModal.value = false
  }
}

const toggleStatus = (item) => {
  item.status_aktif = !item.status_aktif
  toast.info(`Status jadwal ${item.nama} (${item.hari}) diubah menjadi ${item.status_aktif ? 'Aktif' : 'Libur'}`)
}

onMounted(() => {
  fetchJadwal()
})
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.15s ease-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.97) translateY(4px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
</style>
