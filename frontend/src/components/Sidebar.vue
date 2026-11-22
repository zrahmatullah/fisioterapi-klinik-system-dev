<template>
  <aside class="w-[215px] h-full shrink-0 bg-[#0B3948] text-white flex flex-col justify-between overflow-hidden select-none border-r border-[#0B3948]/50 z-30">
    <!-- TOP BRAND HEADER (h-[48px]) -->
    <div class="h-[48px] px-3.5 flex items-center gap-2 border-b border-white/10 shrink-0">
      <div class="w-6 h-6 rounded-md bg-[#14B8A6] text-[#0B3948] flex items-center justify-center font-black text-xs shadow-sm">
        <i class="pi pi-plus font-bold"></i>
      </div>
      <div class="flex items-center gap-1.5 min-w-0">
        <span class="text-[14px] font-bold text-white tracking-wide">SIMRS</span>
        <span class="text-[9px] font-semibold uppercase px-1.5 py-0.2 rounded bg-teal-500/20 text-teal-300 border border-teal-400/30">KlinikKu</span>
      </div>
    </div>

    <!-- DYNAMIC NAVIGATION MENU LIST -->
    <nav class="flex-1 min-h-0 overflow-y-auto custom-scrollbar px-2 py-2 space-y-2 text-[12.5px]">
      <div
        v-for="cat in visibleCategories"
        :key="cat.category"
        class="space-y-0.5"
      >
        <!-- Category Header (Clickable Accordion) -->
        <div
          @click="toggleCategory(cat.category)"
          class="px-2 py-1 flex items-center justify-between text-[10.5px] font-bold text-teal-300/80 uppercase tracking-wider cursor-pointer hover:text-teal-200 transition select-none group"
        >
          <span class="group-hover:translate-x-0.5 transition-transform truncate">{{ cat.category }}</span>
          <i
            class="pi pi-chevron-down text-[8.5px] text-teal-400/60 group-hover:text-teal-200 transition-transform duration-200"
            :class="{ '-rotate-90': isCollapsed(cat.category) }"
          ></i>
        </div>

        <!-- Category Items -->
        <div v-show="!isCollapsed(cat.category)" class="space-y-0.5 transition-all">
          <div
            v-for="item in cat.items"
            :key="item.route"
            @click="go(item.dynamicRoute ? item.dynamicRoute(roleId) : item.route)"
            :class="isActive(item.route) ? activeClass : inactiveClass"
          >
            <i :class="item.icon" class="text-[13px] w-4 text-center shrink-0"></i>
            <span class="truncate">{{ item.title }}</span>
          </div>
        </div>
      </div>
    </nav>

    <!-- BOTTOM USER ACCOUNT FOOTER (shrink-0 p-2.5) -->
    <div class="shrink-0 p-2.5 border-t border-white/10 flex items-center justify-between hover:bg-[#135063]/40 transition relative group">
      <div class="flex items-center gap-2 min-w-0">
        <div class="w-7 h-7 rounded-full bg-[#15576B] border border-white/20 text-white flex items-center justify-center text-xs font-bold shrink-0">
          {{ userInitial }}
        </div>
        <div class="min-w-0">
          <span class="text-[10px] text-teal-300 block leading-tight font-medium">{{ userRoleLabel }}</span>
          <span class="text-[11.5px] font-bold text-white leading-tight truncate block max-w-[110px]">{{ userName }}</span>
        </div>
      </div>

      <button
        type="button"
        @click="showAccountMenu = !showAccountMenu"
        class="text-slate-400 hover:text-white p-1 rounded transition cursor-pointer shrink-0"
        title="Menu Akun"
      >
        <i class="pi pi-chevron-down text-[10px] transition-transform" :class="{ 'rotate-180': showAccountMenu }"></i>
      </button>

      <!-- DROPDOWN LOGOUT -->
      <div
        v-if="showAccountMenu"
        class="absolute bottom-12 left-1.5 right-1.5 bg-[#0B3948] border border-white/20 rounded-xl p-1.5 shadow-2xl z-50 space-y-1 text-xs"
      >
        <div class="px-2 py-1 text-slate-300 border-b border-white/10">
          <p class="font-bold text-white truncate text-[11px]">{{ userName }}</p>
          <p class="text-[9.5px] text-teal-400 font-semibold">{{ userRoleLabel }}</p>
        </div>
        <button
          type="button"
          @click="logout"
          class="w-full text-left px-2 py-1 rounded hover:bg-rose-600/30 text-rose-300 hover:text-rose-100 flex items-center gap-2 transition cursor-pointer font-medium text-[11px]"
        >
          <i class="pi pi-sign-out text-[10px]"></i>
          <span>Keluar Sistem</span>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { menuCategories, ROLE_IDS, getDefaultLandingRoute } from '../config/menuConfig'

const router = useRouter()
const route = useRoute()

const showAccountMenu = ref(false)
const collapsedCategories = ref({})

// User storage
const user = computed(() => {
  try {
    return JSON.parse(localStorage.getItem('user')) || {}
  } catch (e) {
    return {}
  }
})

const roleId = computed(() => Number(user.value?.role_id || localStorage.getItem('role_id') || 0))
const username = computed(() => (user.value?.username || localStorage.getItem('username') || '').toLowerCase())

// SUPER ADMIN: role 13 OR username === 'admin' -> Full Access to everything!
const isAdmin = computed(() => roleId.value === ROLE_IDS.SUPER_ADMIN || username.value === 'admin')

// Filtered dynamic categories & items
const visibleCategories = computed(() => {
  return menuCategories
    .map(cat => {
      // Check if user role matches category
      if (!isAdmin.value && !cat.allowedRoles.includes(roleId.value)) {
        return null
      }

      // Filter category items
      const items = cat.items.filter(item => {
        if (isAdmin.value) return true
        return item.allowedRoles.includes(roleId.value)
      })

      if (items.length === 0) return null

      return {
        ...cat,
        items
      }
    })
    .filter(Boolean)
})

const toggleCategory = (catName) => {
  collapsedCategories.value[catName] = !collapsedCategories.value[catName]
}

const isCollapsed = (catName) => {
  // If active route is in this category, keep it expanded
  const cat = visibleCategories.value.find(c => c.category === catName)
  if (cat && cat.items.some(item => isActive(item.route))) {
    return false
  }
  return !!collapsedCategories.value[catName]
}

const userName = computed(() => user.value?.nama || user.value?.name || user.value?.username || 'Administrator')
const userInitial = computed(() => (userName.value?.charAt(0) || 'A').toUpperCase())

const userRoleLabel = computed(() => {
  if (isAdmin.value) return 'Super Admin'
  if (roleId.value === ROLE_IDS.DOKTER) return 'Dokter'
  if (roleId.value === ROLE_IDS.TERAPIS) return 'Terapis'
  if (roleId.value === ROLE_IDS.ORANG_TUA) return 'Orang Tua'
  if (roleId.value === ROLE_IDS.KASIR) return 'Kasir'
  if (roleId.value === ROLE_IDS.FARMASI) return 'Farmasi'
  if (roleId.value === ROLE_IDS.GUDANG) return 'Gudang'
  if (roleId.value === ROLE_IDS.PENDAFTARAN) return 'Pendaftaran'
  return 'Tenaga Medis'
})

const activeClass = 'bg-[#15576B] text-white font-semibold rounded-lg px-2.5 py-1.5 border-l-4 border-[#2DD4BF] flex items-center gap-2.5 transition cursor-pointer shadow-sm text-[12px]'
const inactiveClass = 'text-slate-300 hover:bg-[#135063]/60 hover:text-white rounded-lg px-2.5 py-1.5 flex items-center gap-2.5 transition cursor-pointer text-[12px]'

const isActive = (path) => {
  if (path === '/dashboard') {
    return route.path === '/dashboard' || route.path === '/dashboard-terapis' || route.path === '/dashboard-orang-tua'
  }
  return route.path === path || route.path.startsWith(path + '/')
}

const go = (path) => {
  showAccountMenu.value = false
  if (route.path !== path) {
    router.push(path)
  }
}

const logout = () => {
  localStorage.clear()
  router.push('/login')
}
</script>
