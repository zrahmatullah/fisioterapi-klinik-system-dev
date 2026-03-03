<template>
  <nav :class="['navbar', { 'shadow-lg': scrolled }]" class="h-16 px-6 flex items-center justify-between bg-white border-b transition-shadow">
    <div></div>
    <div class="relative">
      <button @click="toggle" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-indigo-50 transition">
        <div class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold">
          {{ userInitial }}
        </div>
        <span class="text-sm font-semibold text-gray-700">{{ userName }}</span>
        <i class="pi pi-chevron-down text-xs transition" :class="{ 'rotate-180': open }"></i>
      </button>
      <transition name="fade">
        <div v-if="open" class="absolute right-0 mt-3 w-44 bg-white rounded-xl shadow-lg overflow-hidden z-50">
          <button @click="openProfileModal" class="w-full px-4 py-2.5 text-left text-sm hover:bg-indigo-50 transition flex items-center gap-2">
            <i class="pi pi-user text-sm"></i>
            <span>Profile</span>
          </button>
          <button @click="openConfirm" class="w-full px-4 py-2.5 text-left text-sm text-red-600 hover:bg-red-50 transition flex items-center gap-2">
            <i class="pi pi-sign-out text-sm"></i>
            <span>Logout</span>
          </button>
        </div>
      </transition>
    </div>
  </nav>

  <!-- MODAL PROFILE -->
  <transition name="fade">
    <div v-if="showProfile" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl w-full max-w-sm p-6 shadow-2xl text-white relative">
        <button @click="closeProfileModal" class="absolute top-3 right-3 text-white hover:text-gray-200 transition text-lg">&times;</button>
        <div class="flex flex-col items-center space-y-4">
          <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center text-indigo-700 text-3xl font-bold">
            {{ userInitial }}
          </div>
          <div class="text-center">
            <h2 class="text-xl font-semibold">{{ user.nama }}</h2>
            <p class="text-sm text-indigo-200">{{ user.role }}</p>
          </div>
          <div class="w-full bg-white/20 rounded-lg p-4 space-y-2 text-sm">
            <div><strong>Username:</strong> {{ user.username }}</div>
            <div><strong>Jenis User:</strong> {{ user.jenis_user }}</div>
          </div>
          <button @click="closeProfileModal" class="mt-4 px-6 py-2 bg-white text-indigo-700 font-semibold rounded-xl hover:bg-white/90 transition">Tutup</button>
        </div>
      </div>
    </div>
  </transition>

  <!-- MODAL CONFIRM LOGOUT -->
  <transition name="fade">
    <div v-if="showConfirm" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-xl">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Konfirmasi Logout</h3>
        <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin keluar dari sistem?</p>
        <div class="flex justify-end gap-3">
          <button @click="closeConfirm" class="px-4 py-2 text-sm rounded-xl bg-gray-100 hover:bg-gray-200 transition">Batal</button>
          <button @click="logout" class="px-4 py-2 text-sm rounded-xl bg-red-600 text-white hover:bg-red-700 transition">Ya, Logout</button>
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
const showConfirm = ref(false)
const showProfile = ref(false)
const scrolled = ref(false)

const userName = ref('User')
const userInitial = ref('U')
const user = ref({ nama: '', username: '', role: '', jenis_user: '' })

const toggle = () => { open.value = !open.value }
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
  background-color: white;
  transition: box-shadow 0.2s ease;
}

/* fade transition untuk dropdown/modal */
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
