<template>
  <header class="h-[48px] bg-white border-b border-slate-200 px-4 flex items-center justify-between shrink-0 select-none z-20">
    <!-- LEFT: DYNAMIC PAGE / MODULE TITLE -->
    <div class="flex items-center gap-2.5 min-w-0">
      <h2 class="text-[14.5px] font-semibold text-slate-900 tracking-tight leading-none truncate max-w-md">
        {{ pageTitle }}
      </h2>
    </div>

    <!-- RIGHT: UTILITY CLUSTER -->
    <div class="flex items-center gap-2">
      <!-- GLOBAL QUICK SEARCH TRIGGER (Ctrl + K) -->
      <button
        type="button"
        @click="showCommandPalette = true"
        class="hidden md:flex items-center gap-2 h-[32px] px-2.5 rounded-lg bg-slate-100 hover:bg-slate-200/80 text-slate-600 hover:text-slate-900 text-[12px] font-medium transition border border-slate-200"
        title="Pencarian Cepat Pasien / Menu (Ctrl+K)"
      >
        <i class="pi pi-search text-[11px] text-[#0F766E]"></i>
        <span>Cari...</span>
        <kbd class="px-1.5 py-0.2 rounded bg-white border border-slate-200 font-mono text-[9px] text-slate-400">Ctrl K</kbd>
      </button>

      <!-- HELP ICON (?) -->
      <button
        type="button"
        @click="openHelpModal"
        class="w-7 h-7 rounded-lg border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 flex items-center justify-center text-xs font-semibold transition"
        title="Bantuan & Petunjuk SIMRS"
      >
        <i class="pi pi-question text-[11px]"></i>
      </button>

      <!-- NOTIFICATION BELL WITH BADGE -->
      <div class="relative">
        <button
          type="button"
          @click="toggleNotif"
          class="w-7 h-7 rounded-lg border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 flex items-center justify-center text-sm transition relative"
          title="Notifikasi"
        >
          <i class="pi pi-bell text-[11px]"></i>
          <!-- Red dot badge -->
          <span
            v-if="unreadCount > 0"
            class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-rose-500 text-white text-[8px] font-bold flex items-center justify-center ring-2 ring-white"
          >
            {{ unreadCount }}
          </span>
        </button>

        <!-- Notification Dropdown -->
        <div
          v-if="notifOpen"
          class="absolute right-0 mt-2 w-72 bg-white border border-slate-200 rounded-xl shadow-xl z-50 overflow-hidden text-xs"
        >
          <div class="px-3 py-2 bg-slate-50 border-b border-slate-100 flex items-center justify-between font-semibold text-slate-800">
            <span>Notifikasi Klinis</span>
            <span class="text-[9px] bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded-full font-bold">1 Baru</span>
          </div>
          <div class="p-2 space-y-1">
            <div class="p-2 rounded-lg bg-teal-50/60 border border-teal-100 flex gap-2">
              <i class="pi pi-bell text-teal-600 mt-0.5 text-xs"></i>
              <div>
                <p class="font-semibold text-slate-800 text-[11px]">Pasien Antrian A-001</p>
                <p class="text-[10px] text-slate-500 leading-tight">Azhar Al-Farizi siap masuk Ruang Terapi Wicara A.</p>
              </div>
            </div>
            <div class="p-2 rounded-lg hover:bg-slate-50 flex gap-2 text-slate-500">
              <i class="pi pi-exclamation-triangle text-amber-500 mt-0.5 text-xs"></i>
              <div>
                <p class="font-medium text-slate-700 text-[11px]">Stok Kritis Sarung Tangan</p>
                <p class="text-[10px] text-slate-400 leading-tight">Tersisa 3 box di Gudang Farmasi.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- LOGGED-IN USER PROFILE PILL -->
      <div class="relative">
        <button
          type="button"
          @click="toggleProfile"
          class="flex items-center gap-2 pl-1 pr-2 h-[32px] rounded-full border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition"
        >
          <div class="w-6 h-6 rounded-full bg-[#0F766E] text-white flex items-center justify-center font-bold text-[10px] shrink-0 shadow-sm">
            {{ userInitial }}
          </div>
          <span class="text-[12px] font-semibold text-slate-800 hidden sm:inline max-w-[130px] truncate">
            {{ userName }}
          </span>
          <i class="pi pi-chevron-down text-[9px] text-slate-400"></i>
        </button>

        <!-- Profile Dropdown -->
        <div
          v-if="profileOpen"
          class="absolute right-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1.5 space-y-0.5 text-xs"
        >
          <div class="px-2.5 py-1.5 border-b border-slate-100 mb-1">
            <p class="font-bold text-slate-900 truncate text-[11.5px]">{{ userName }}</p>
            <p class="text-[10px] text-teal-600 font-medium">{{ userRoleTitle }}</p>
          </div>
          <button
            type="button"
            @click="goToSettings"
            class="w-full text-left px-2.5 py-1.5 rounded-lg hover:bg-slate-100 flex items-center gap-2 text-slate-700 text-[11.5px]"
          >
            <i class="pi pi-cog text-[11px] text-slate-500"></i>
            <span>Pengaturan Akun</span>
          </button>
          <button
            type="button"
            @click="logout"
            class="w-full text-left px-2.5 py-1.5 rounded-lg hover:bg-rose-50 text-rose-600 flex items-center gap-2 transition text-[11.5px]"
          >
            <i class="pi pi-sign-out text-[11px]"></i>
            <span>Keluar Sesi</span>
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL COMMAND PALETTE (CTRL + K) -->
    <teleport to="body">
      <div
        v-if="showCommandPalette"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-start justify-center z-[9999] p-4 pt-16"
        @click.self="showCommandPalette = false"
      >
        <div class="bg-white rounded-xl w-full max-w-xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-100">
          <div class="p-3 border-b border-slate-100 flex items-center gap-3">
            <i class="pi pi-search text-teal-600 text-sm"></i>
            <input
              v-model="paletteQuery"
              type="text"
              placeholder="Cari pasien atau lompat ke menu SIMRS..."
              class="w-full text-[13px] outline-none text-slate-800 placeholder-slate-400 bg-transparent"
              autofocus
              @keydown.esc="showCommandPalette = false"
            />
            <kbd class="px-1.5 py-0.5 rounded bg-slate-100 text-[10px] text-slate-500 font-mono">ESC</kbd>
          </div>

          <div class="p-1.5 max-h-80 overflow-y-auto custom-scrollbar space-y-0.5">
            <button
              v-for="(item, idx) in filteredPaletteItems"
              :key="idx"
              @click="navigatePalette(item.route)"
              class="w-full px-2.5 py-1.5 rounded-lg hover:bg-[#E6F2F5] text-left flex items-center justify-between text-xs transition group"
            >
              <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded bg-slate-100 group-hover:bg-[#0F766E] text-slate-600 group-hover:text-white flex items-center justify-center transition">
                  <i :class="item.icon" class="text-[11px]"></i>
                </div>
                <div>
                  <p class="font-semibold text-slate-800 group-hover:text-[#0F766E] text-[12px]">{{ item.title }}</p>
                  <p class="text-[10.5px] text-slate-400">{{ item.subtitle }}</p>
                </div>
              </div>
              <i class="pi pi-arrow-right text-[10px] text-slate-300 group-hover:text-[#0F766E] transition"></i>
            </button>
          </div>
        </div>
      </div>
    </teleport>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useToast } from 'vue-toastification'

const router = useRouter()
const route = useRoute()
const toast = useToast()

const profileOpen = ref(false)
const notifOpen = ref(false)
const showCommandPalette = ref(false)
const paletteQuery = ref('')
const unreadCount = ref(1)

// Dynamic Page Title
const pageTitle = computed(() => {
  const path = route.path
  if (path === '/data-pasien') return 'Daftar Pasien Anak Terdaftar'
  if (path === '/mapping-jadwal') return 'Mapping Jadwal Praktik'
  if (path === '/emr-timeline') return 'Rekam Medis Elektronik (RME)'
  if (path === '/dashboard') return 'Dashboard Eksekutif'
  if (path === '/dashboard-terapis') return 'Dashboard Terapis'
  if (path === '/dashboard-orang-tua') return 'Portal Orang Tua Pasien'
  if (path === '/kasir-billing') return 'Kasir & Billing (POS)'
  if (path === '/farmasi-logistik') return 'Farmasi & Logistik FEFO'
  if (path === '/surat-dokumen') return 'Dokumen & Surat Resmi Medis'
  if (path === '/registrasi') return 'Pendaftaran & Antrian Pasien'
  if (path === '/jadwal-terapi') return 'Jadwal Terapi Pasien'
  if (path === '/pengaturan-klinik') return 'Pengaturan Klinik & Hak Akses'
  if (path === '/list-pendaftaran-layanan') return 'Daftar Kunjungan Rawat Jalan'
  if (path === '/pemanggilan-antrian') return 'Pemanggilan Antrian TV'
  if (path === '/catatan-aktivitas-anak') return 'Catatan Aktivitas & Terapi'
  if (path === '/assesment') return 'Form Asesmen Awal Pasien'
  if (path === '/hasil-evaluasi') return 'Monitoring & Evaluasi Klinis'
  if (path === '/keluhan-anak') return 'Keluhan Pasien Anak'
  if (path === '/keluhan-admin') return 'Tanggapan Keluhan Admin'
  if (path === '/list-reschedule') return 'Daftar Pengajuan Reschedule'
  if (path === '/input-layanan') return 'Input Layanan Anak'
  if (path === '/pembayaran-tagihan') return 'Pembayaran Tagihan'
  if (path === '/riwayat-pembayaran-anak') return 'Riwayat Pembayaran Pasien'
  if (path === '/master-user') return 'Master Pengguna Sistem'
  if (path === '/master-role') return 'Master Role & Hak Akses'
  if (path === '/master-anak') return 'Master Profil Anak'
  if (path === '/master-terapis') return 'Master Tenaga Terapis'
  if (path === '/master-jadwal-terapis') return 'Master Jadwal Terapis'
  if (path === '/master-ruangan') return 'Master Ruangan Terapi'
  if (path === '/master-kategori-layanan') return 'Master Kategori Layanan'
  if (path === '/master-layanan') return 'Master Layanan & Tarif'
  if (path === '/master-produk') return 'Master Produk & Obat'
  if (path === '/master-promosi') return 'Master Promosi & Diskon'
  if (path === '/laporan-pembayaran') return 'Laporan Transaksi Pembayaran'
  if (path === '/laporan-keluhan') return 'Laporan Layanan & Keluhan'
  if (path === '/laporan-histori-promosi') return 'Laporan Histori Promosi'

  // Format any raw PascalCase route names into clean spaced Title Case
  if (route.name) {
    return String(route.name)
      .replace(/([A-Z])/g, ' $1')
      .replace(/_/g, ' ')
      .trim()
  }
  return 'SIMRS KlinikKu'
})

// Current user
const user = computed(() => {
  try {
    return JSON.parse(localStorage.getItem('user')) || {}
  } catch (e) {
    return {}
  }
})

const userName = computed(() => user.value?.nama || user.value?.name || user.value?.username || 'Alkar As Rahmatulloh')
const userInitial = computed(() => (userName.value?.charAt(0) || 'A').toUpperCase())
const userRoleTitle = computed(() => {
  const r = Number(user.value?.role_id || 13)
  if (r === 13) return 'Administrator SIMRS'
  if (r === 14) return 'Terapis Klinis'
  if (r === 15) return 'Orang Tua Pasien'
  if (r === 19) return 'Kasir & Keuangan'
  return 'Tenaga Medis'
})

const toggleProfile = () => {
  profileOpen.value = !profileOpen.value
  notifOpen.value = false
}

const toggleNotif = () => {
  notifOpen.value = !notifOpen.value
  profileOpen.value = false
}

const openHelpModal = () => {
  toast.info('Sistem SIMRS E-Clinic v2.0 aktif. Gunakan Ctrl+K untuk pencarian kilat pasien dan rekam medis.')
}

const goToSettings = () => {
  profileOpen.value = false
  router.push('/pengaturan-klinik')
}

const logout = () => {
  localStorage.clear()
  toast.success('Sesi berhasil diakhiri')
  router.push('/login')
}

// Command palette
const paletteItems = [
  { title: 'Daftar Pasien Anak Terdaftar', subtitle: 'Pencarian RM, cetak karcis, gelang pasien & EMR', route: '/data-pasien', icon: 'pi pi-id-card' },
  { title: 'Mapping Jadwal Praktik', subtitle: 'Penataan jadwal dokter, terapis, ruangan & kuota sesi', route: '/mapping-jadwal', icon: 'pi pi-calendar-plus' },
  { title: 'Rekam Medis Elektronik (RME)', subtitle: 'Pemeriksaan SOAP, vital signs, order terapi', route: '/emr-timeline', icon: 'pi pi-file-edit' },
  { title: 'Kasir & Billing (POS)', subtitle: 'Kwitansi, faktur, shift kasir, struk thermal', route: '/kasir-billing', icon: 'pi pi-wallet' },
  { title: 'Farmasi & Stok FEFO', subtitle: 'Stok obat, BHP, batch kedaluwarsa', route: '/farmasi-logistik', icon: 'pi pi-box' },
  { title: 'Dokumen & Surat Medis', subtitle: 'Surat keterangan terapi, rekomendasi sekolah', route: '/surat-dokumen', icon: 'pi pi-file' },
  { title: 'Pendaftaran Kunjungan', subtitle: 'Registrasi anak baru dan penomoran RM', route: '/registrasi', icon: 'pi pi-user-plus' },
  { title: 'Rawat Jalan / Antrian', subtitle: 'Monitoring status kedatangan & antrian poli', route: '/list-pendaftaran-layanan', icon: 'pi pi-users' },
  { title: 'Pengaturan SIMRS', subtitle: 'Konfigurasi klinik, hak akses RBAC & audit log', route: '/pengaturan-klinik', icon: 'pi pi-cog' }
]

const filteredPaletteItems = computed(() => {
  if (!paletteQuery.value) return paletteItems
  const q = paletteQuery.value.toLowerCase()
  return paletteItems.filter(item =>
    item.title.toLowerCase().includes(q) || item.subtitle.toLowerCase().includes(q)
  )
})

const navigatePalette = (targetRoute) => {
  showCommandPalette.value = false
  paletteQuery.value = ''
  router.push(targetRoute)
}

// Global hotkey Ctrl+K / Cmd+K
const handleKeyDown = (e) => {
  if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
    e.preventDefault()
    showCommandPalette.value = !showCommandPalette.value
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown)
})
</script>
