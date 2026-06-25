<template>
  <nav :class="['navbar', { 'is-scrolled': scrolled }]" class="h-16 px-6 flex items-center justify-between bg-white/80 backdrop-blur-xl border-b border-gray-100">

    <div></div>

    <div class="flex items-center gap-2">

      <!-- NOTIFICATION -->
      <div class="relative">
        <button
          @click="toggleNotif"
          class="notif-trigger relative w-10 h-10 rounded-full flex items-center justify-center text-gray-500 transition-all duration-200"
          :class="notifOpen ? 'bg-gray-100 text-gray-700' : 'hover:bg-gray-100 hover:text-gray-700'"
          aria-label="Notifikasi"
        >
          <i class="pi pi-bell text-[17px]"></i>
          <span
            v-if="unreadCount > 0"
            class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center ring-2 ring-white"
          >
            {{ unreadCount > 9 ? '9+' : unreadCount }}
          </span>
        </button>

        <transition name="pop">
          <div
            v-if="notifOpen"
            class="dropdown-panel absolute left-0 mt-3 w-[340px] overflow-hidden z-50"
          >
            <div class="px-4 py-3.5 flex items-center justify-between border-b border-gray-100/80">
              <span class="text-[13px] font-semibold text-gray-900 tracking-tight">Notifikasi</span>
              <button v-if="unreadCount > 0" class="text-[11px] font-medium text-indigo-600 hover:text-indigo-700">
                Tandai dibaca
              </button>
            </div>

            <div v-if="notifications.length === 0" class="px-6 py-10 text-center">
              <div class="w-11 h-11 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                <i class="pi pi-bell text-gray-300 text-lg"></i>
              </div>
              <p class="text-[13px] text-gray-400">Belum ada notifikasi baru</p>
            </div>

            <div v-else class="max-h-80 overflow-y-auto">
              <button
                v-for="n in notifications"
                :key="n.id"
                class="notif-item w-full px-4 py-3 text-left transition flex gap-3 relative"
              >
                <span v-if="n.unread" class="absolute left-0 top-2 bottom-2 w-[3px] rounded-full bg-indigo-500"></span>
                <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                  <i class="pi pi-info-circle text-[13px]"></i>
                </div>
                <div class="min-w-0 flex-1">
                  <p class="text-[13px] text-gray-800 leading-snug truncate">{{ n.title }}</p>
                  <p class="text-[11px] text-gray-400 mt-0.5">{{ n.time }}</p>
                </div>
              </button>
            </div>
          </div>
        </transition>
      </div>

      <!-- PROFILE -->
      <div class="relative ml-1">
        <button
          @click="toggle"
          class="profile-trigger flex items-center gap-2.5 pl-1.5 pr-3 py-1.5 rounded-full transition-all duration-200"
          :class="open ? 'bg-gray-100' : 'hover:bg-gray-100'"
        >
          <div class="relative shrink-0">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold text-[13px]">
              {{ userInitial }}
            </div>
            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
          </div>
          <span class="text-[13px] font-semibold text-gray-700 hidden sm:block">{{ userName }}</span>
          <i class="pi pi-angle-down text-[10px] text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
        </button>

        <transition name="pop">
          <div v-if="open" class="dropdown-panel absolute right-0 mt-3 w-60 overflow-hidden z-50">
            <div class="px-4 py-4 flex items-center gap-3 border-b border-gray-100/80">
              <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold text-sm shrink-0">
                {{ userInitial }}
              </div>
              <div class="min-w-0">
                <p class="text-[13px] font-semibold text-gray-900 truncate">{{ user.nama }}</p>
                <p class="text-[11px] text-gray-400 truncate">{{ user.username }}</p>
              </div>
            </div>
            <div class="py-1.5">
              <button @click="openProfileModal" class="menu-item w-full px-4 py-2.5 text-left text-[13px] text-gray-600 flex items-center gap-2.5 transition">
                <i class="pi pi-user text-[13px] text-gray-400"></i>
                <span>Profil saya</span>
              </button>
              <button @click="openConfirm" class="menu-item menu-item--danger w-full px-4 py-2.5 text-left text-[13px] text-rose-600 flex items-center gap-2.5 transition">
                <i class="pi pi-sign-out text-[13px]"></i>
                <span>Keluar</span>
              </button>
            </div>
          </div>
        </transition>
      </div>

    </div>
  </nav>

  <!-- backdrop untuk menutup dropdown saat klik di luar -->
  <div v-if="open || notifOpen" class="fixed inset-0 z-40" @click="closeDropdowns"></div>

  <!-- MODAL PROFILE -->
  <transition name="modal">
    <div v-if="showProfile" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-[28px] w-full max-w-md shadow-2xl relative p-7">

        <button @click="closeProfileModal" class="absolute top-5 right-5 w-8 h-8 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition flex items-center justify-center">
          <i class="pi pi-times text-sm"></i>
        </button>

        <div class="flex items-start gap-4">

          <!-- avatar dengan status ring -->
          <div class="relative shrink-0">
            <svg class="w-[72px] h-[72px] -rotate-90" viewBox="0 0 72 72">
              <circle cx="36" cy="36" r="33" fill="none" stroke="#EEF0FF" stroke-width="3" />
              <circle cx="36" cy="36" r="33" fill="none" stroke="#6366F1" stroke-width="3" stroke-linecap="round" stroke-dasharray="207.3" stroke-dashoffset="20" />
            </svg>
            <div class="absolute inset-[6px] rounded-full bg-indigo-600 text-white flex items-center justify-center text-2xl font-bold">
              {{ userInitial }}
            </div>
            <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 ring-[3px] ring-white"></span>
          </div>

          <div class="pt-1 min-w-0">
            <h2 class="text-[17px] font-bold text-gray-900 truncate">{{ user.nama }}</h2>
            <p class="text-[13px] text-gray-400 mb-2">{{ user.role }}</p>
            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              Aktif sekarang
            </span>
          </div>

        </div>

        <div class="grid grid-cols-2 gap-3 mt-6">
          <div class="rounded-2xl bg-gray-50 px-4 py-3.5">
            <p class="text-[11px] text-gray-400 mb-1">Username</p>
            <p class="text-[13px] font-semibold text-gray-800 truncate">{{ user.username }}</p>
          </div>
          <div class="rounded-2xl bg-gray-50 px-4 py-3.5">
            <p class="text-[11px] text-gray-400 mb-1">Jenis user</p>
            <p class="text-[13px] font-semibold text-gray-800 truncate">{{ user.jenis_user }}</p>
          </div>
        </div>

        <button @click="closeProfileModal" class="mt-6 w-full py-3 bg-gray-900 text-white font-semibold text-[13px] rounded-2xl hover:bg-gray-800 transition">
          Tutup
        </button>

      </div>
    </div>
  </transition>

  <!-- MODAL CONFIRM LOGOUT -->
  <transition name="modal">
    <div v-if="showConfirm" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-[28px] w-full max-w-sm p-7 shadow-2xl">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mb-5">
          <i class="pi pi-sign-out text-lg"></i>
        </div>
        <h3 class="text-[17px] font-bold text-gray-900 mb-1.5">Konfirmasi keluar</h3>
        <p class="text-[13px] text-gray-500 mb-6 leading-relaxed">Anda akan keluar dari sesi ini dan perlu masuk kembali untuk mengakses sistem.</p>
        <div class="flex gap-2.5">
          <button @click="closeConfirm" class="flex-1 py-3 text-[13px] font-semibold rounded-2xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Batal</button>
          <button @click="logout" class="flex-1 py-3 text-[13px] font-semibold rounded-2xl bg-rose-500 text-white hover:bg-rose-600 transition">Ya, keluar</button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '@/axios'

const router = useRouter()
const toast = useToast()

const open = ref(false)
const notifOpen = ref(false)
const showConfirm = ref(false)
const showProfile = ref(false)
const scrolled = ref(false)

const userName = ref('User')
const userInitial = ref('U')
const user = ref({ nama: '', username: '', role: '', jenis_user: '' })

// Placeholder notifikasi — gampang disambungkan ke API nanti,
// tinggal isi notifications.value dari response backend.
// Struktur per item: { id, title, time, unread }
const notifications = ref([])
const unreadCount = ref(0)

const toggle = () => { open.value = !open.value; notifOpen.value = false }
const toggleNotif = () => { notifOpen.value = !notifOpen.value; open.value = false }
const closeDropdowns = () => { open.value = false; notifOpen.value = false }

const openConfirm = () => { open.value = false; showConfirm.value = true }
const closeConfirm = () => { showConfirm.value = false }
const openProfileModal = () => { open.value = false; showProfile.value = true }
const closeProfileModal = () => { showProfile.value = false }

onMounted(() => {
  const storedUser = JSON.parse(localStorage.getItem('user'))
  if (storedUser) {
    user.value = storedUser
    userName.value = storedUser.nama || 'User'
    userInitial.value = storedUser.nama ? storedUser.nama.charAt(0).toUpperCase() : 'U'
  }

  // shadow dinamis saat scroll
  const handleScroll = () => {
    scrolled.value = window.scrollY > 10
  }
  window.addEventListener('scroll', handleScroll)
  handleScroll() // cek posisi awal

  onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
  })
})

const logout = async () => {
  try { await api.post('/logout') } catch (e) {}
  finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    localStorage.removeItem('role_id')
    delete api.defaults.headers.common['Authorization']
    toast.success('Logout berhasil')
    router.replace('/login')
    showConfirm.value = false
  }
}
</script>

<style scoped>
.navbar {
  position: sticky;
  top: 0;
  z-index: 50;
  transition: box-shadow 0.25s ease, border-color 0.25s ease;
}

.navbar.is-scrolled {
  box-shadow: 0 1px 0 rgba(17, 24, 39, 0.04), 0 8px 24px -8px rgba(17, 24, 39, 0.08);
}

/* ---------- DROPDOWN PANEL ---------- */
.dropdown-panel {
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(20px);
  border-radius: 18px;
  border: 1px solid rgba(17, 24, 39, 0.06);
  box-shadow:
    0 1px 2px rgba(17, 24, 39, 0.04),
    0 12px 32px -8px rgba(17, 24, 39, 0.12),
    0 0 0 1px rgba(17, 24, 39, 0.02);
}

.menu-item:hover {
  background: rgba(99, 102, 241, 0.06);
  color: #4338CA;
}

.menu-item:hover i {
  color: #4338CA;
}

.menu-item--danger:hover {
  background: rgba(244, 63, 94, 0.06);
  color: #e11d48;
}

.notif-item:hover {
  background: rgba(17, 24, 39, 0.03);
}

/* ---------- TRANSITIONS ---------- */
.pop-enter-active {
  transition: opacity 0.18s ease, transform 0.18s cubic-bezier(0.16, 1, 0.3, 1);
}
.pop-leave-active {
  transition: opacity 0.12s ease, transform 0.12s ease;
}
.pop-enter-from {
  opacity: 0;
  transform: translateY(-6px) scale(0.97);
}
.pop-leave-to {
  opacity: 0;
  transform: translateY(-4px) scale(0.98);
}

.modal-enter-active {
  transition: opacity 0.2s ease;
}
.modal-leave-active {
  transition: opacity 0.15s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-active > div,
.modal-leave-active > div {
  transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease;
}
.modal-enter-from > div {
  transform: scale(0.95) translateY(8px);
  opacity: 0;
}
.modal-leave-to > div {
  transform: scale(0.97);
  opacity: 0;
}
</style>