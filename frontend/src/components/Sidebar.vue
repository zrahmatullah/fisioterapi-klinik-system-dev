<template>
  <aside class="sidebar">
    <!-- HEADER TETAP -->
    <div class="sidebar-header">
  <div class="logo-wrapper">
    <img src="/logo.png" alt="Logo Klinik" />
  </div>

  <div class="brand-text">
    <div class="brand-name">KLINIK ABQARY</div>
    <div class="brand-sub">Child Therapy Center</div>
  </div>
</div>


    <!-- MENU SCROLLABLE -->
    <ul class="sidebar-menu">
      <!-- Dashboard -->
      <li v-if="isAdmin" @click="go('/dashboard')" :class="itemClass('/dashboard')">
        <i class="pi pi-home"></i>
        <span>Dashboard</span>
      </li>
      <li v-if="isOrangTua" @click="go('/dashboard-orang-tua')" :class="itemClass('/dashboard-orang-tua')">
        <i class="pi pi-home"></i>
        <span>Dashboard</span>
      </li>
      <li v-if="isTerapis" @click="go('/dashboard-terapis')" :class="itemClass('/dashboard-terapis')">
        <i class="pi pi-home"></i>
        <span>Dashboard</span>
      </li>

      <li v-if="isOrangTua" @click="go('/registrasi')" :class="itemClass('/registrasi')">
        <i class="pi pi-file"></i>
        <span>Pendaftaran</span>
      </li>

      <li v-if="isOrangTua" @click="go('/riwayat-pembayaran-anak')" :class="itemClass('/riwayat-pembayaran-anak')">
        <i class="pi pi-credit-card"></i>
        <span>Pembayaran Anak</span>
      </li>

      <li v-if="isOrangTua" @click="go('/catatan-aktivitas-anak')" :class="itemClass('/catatan-aktivitas-anak')">
        <i class="pi pi-calendar"></i>
        <span>Jadwal Terapi</span>
      </li>
      <li v-if="isOrangTua" @click="go('/hasil-evaluasi')" :class="itemClass('/hasil-evaluasi')">
        <i class="pi pi-heart-fill"></i>
        <span>Monitoring Terapi</span>
      </li>
      <li v-if="isOrangTua" @click="go('/keluhan-anak')" :class="itemClass('/keluhan-anak')">
        <i class="pi pi-comment"></i>
        <span>Keluhan </span>
      </li>

      <!-- Submenu Pasien -->
      <transition name="slide">
        <ul v-if="activeMenu === 'pasien' && isAdmin" class="submenu-list">
          <li @click="go('/pasien')" class="submenu"><span>Daftar Pasien</span></li>
          <li @click="go('/pasien/tambah')" class="submenu"><span>Tambah Pasien</span></li>
        </ul>
      </transition>

      <!-- Submenu Terapi -->
      <transition name="slide">
        <ul v-if="activeMenu === 'terapi' && (isAdmin || isTerapis)" class="submenu-list">
          <li @click="go('/terapi')" class="submenu"><span>List Terapi</span></li>
          <li @click="go('/terapi/jadwal')" class="submenu"><span>Jadwal Terapi</span></li>
        </ul>
      </transition>

      <!-- Master Data -->
      <li v-if="isAdmin" @click="toggleMenu('master')" class="menu-item">
        <div class="flex items-center gap-3">
          <i class="pi pi-database"></i>
          <span>Master Data</span>
        </div>
        <i class="pi pi-chevron-down text-xs transition" :class="{ 'rotate-180': activeMenu === 'master' }"/>
      </li>

      <transition name="slide">
        <ul v-if="activeMenu === 'master' && isAdmin" class="submenu-list">
          <li @click="go('/master-user')" class="submenu"><i class="pi pi-user mr-2"></i><span>Master User</span></li>
          <li @click="go('/master-role')" class="submenu"><i class="pi pi-id-card mr-2"></i><span>Master Role</span></li>
          <li @click="go('/master-anak')" class="submenu"><i class="pi pi-users mr-2"></i><span>Master Anak</span></li>
          <li @click="go('/master-terapis')" class="submenu"><i class="pi pi-users mr-2"></i><span>Master Terapis</span></li>
                    <li @click="go('/master-jadwal-terapis')" class="submenu"><i class="pi pi-briefcase mr-2"></i><span>Master Jadwal Terapis</span></li>
          <li @click="go('/master-ruangan')" class="submenu"><i class="pi pi-building mr-2"></i><span>Master Ruangan</span></li>
          <li @click="go('/master-kategori-layanan')" class="submenu"><i class="pi pi-tags mr-2"></i><span> Kategori Layanan</span></li>
          <li @click="go('/master-layanan')" class="submenu"><i class="pi pi-briefcase mr-2"></i><span>Master Layanan</span></li>
          <li @click="go('/master-promosi')" class="submenu"><i class="pi pi-briefcase mr-2"></i><span>Master Promosi</span></li>
        </ul>
      </transition>

      <!-- Pendaftaran & Layanan -->
      <li v-if="isAdmin" @click="go('/registrasi')" :class="itemClass('/registrasi')">
        <i class="pi pi-file"></i>
        <span>Pendaftaran</span>
      </li>
      <li v-if="isTerapis" @click="go('/jadwal-terapi')" :class="itemClass('/jadwal-terapi')">
        <i class="pi pi-file"></i>
        <span>Jadwal Terapi</span>
      </li>
      <li v-if="isTerapis" @click="go('/report-catatan-aktivitas')" :class="itemClass('/report-catatan-aktivitas')">
        <i class="pi pi-file"></i>
        <span>Report Catatan Aktivitas</span>
      </li>
      <li v-if="isTerapis" @click="go('/evaluasi-terapi')" :class="itemClass('/evaluasi-terapi')">
        <i class="pi pi-file"></i>
        <span>Evaluasi Terapi</span>
      </li>
      <li v-if="isTerapis" @click="go('/riwayat-evaluasi')" :class="itemClass('/riwayat-evaluasi')">
        <i class="pi pi-file"></i>
        <span>Riwayat Evaluasi</span>
      </li>
      <li v-if="isAdmin" @click="go('/input-layanan')" :class="itemClass('/input-layanan')">
        <i class="pi pi-file"></i>
        <span>Input Layanan Anak</span>
      </li>
      <li v-if="isAdmin" @click="go('/list-pendaftaran-layanan')" :class="itemClass('/list-pendaftaran-layanan')">
        <i class="pi pi-file"></i>
        <span>Daftar Layanan</span>
      </li>
      <li v-if="isAdmin" @click="go('/list-reschedule')" :class="itemClass('/list-reschedule')">
        <i class="pi pi-calendar-clock"></i>
        <span>List Reschedule</span>
      </li>
      <li v-if="isAdmin" @click="go('/pembayaran-tagihan')" :class="itemClass('/pembayaran-tagihan')">
        <i class="pi pi-credit-card"></i>
        <span>Pembayaran / Tagihan</span>
      </li>
      <li v-if="isAdmin" @click="go('/keluhan-admin')" :class="itemClass('/keluhan-admin')">
        <i class="pi pi-file"></i>
        <span>Keluhan Admin</span>
      </li>
      <li v-if="isAdmin" @click="go('/laporan-pembayaran')" :class="itemClass('/laporan-pembayaran')">
        <i class="pi pi-clipboard"></i>
        <span>Laporan Pembayaran</span>
      </li>
      <li v-if="isAdmin" @click="go('/laporan-keluhan')" :class="itemClass('/laporan-keluhan')">
        <i class="pi pi-clipboard"></i>
        <span>Laporan Keluhan</span>
      </li>
      <li v-if="isAdmin" @click="go('/laporan-histori-promosi')" :class="itemClass('/laporan-histori-promosi')">
        <i class="pi pi-clipboard"></i>
        <span>Laporan Promosi</span>
      </li>
      <li v-if="isAdmin" @click="go('/pemanggilan-antrian')" :class="itemClass('/pemanggilan-antrian')">
        <i class="pi pi-bell"></i>
        <span>Pemanggilan Antrian</span>
      </li>
    </ul>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const ROLE_ADMIN = 13
const ROLE_TERAPIS = 14
const ROLE_ORANG_TUA = 15

const roleId = Number(localStorage.getItem('role_id'))
const isAdmin = computed(() => roleId === ROLE_ADMIN)
const isTerapis = computed(() => roleId === ROLE_TERAPIS)
const isOrangTua = computed(() => roleId === ROLE_ORANG_TUA)

const router = useRouter()
const route = useRoute()
const activeMenu = ref(null)

const toggleMenu = (menu) => {
  activeMenu.value = activeMenu.value === menu ? null : menu
}

const go = (path) => router.push(path)
const itemClass = (path) => ['menu-item', route.path === path ? 'bg-white/25 shadow' : 'hover:bg-white/15']
</script>

<style scoped>
/* ===============================
   SIDEBAR WRAPPER
================================ */
.sidebar {
  width: 16rem;
  height: 100vh;
  display: flex;
  flex-direction: column;
  background: linear-gradient(180deg, #4f46e5, #7c3aed);
  color: #ffffff;
  box-shadow: 2px 0 12px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}
.sidebar-header {
  display: flex;
  align-items: center;
  gap: 0.9rem;

  padding: 1.25rem 1.25rem;
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(6px);

  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

/* Logo box */
.logo-wrapper {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: white;

  display: flex;
  align-items: center;
  justify-content: center;

  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
}

.logo-wrapper img {
  width: 38px;
  height: 38px;
  object-fit: contain;
}

/* Text */
.brand-text {
  display: flex;
  flex-direction: column;
  line-height: 1.1;
}

.brand-name {
  font-size: 1.05rem;
  font-weight: 800;
  letter-spacing: 0.08em;
}

.brand-sub {
  font-size: 0.7rem;
  opacity: 0.85;
  letter-spacing: 0.12em;
}


/* ===============================
   HEADER
================================ */
.sidebar-header {
  padding: 1.25rem 1.5rem;
  font-size: 1.25rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  flex-shrink: 0;
}

/* ===============================
   MENU CONTAINER (SCROLLABLE)
================================ */
.sidebar-menu {
  flex: 1;
  overflow-y: auto;
  padding: 0.75rem;
  list-style: none;
}

/* ===============================
   MENU ITEM (UTAMA)
================================ */
.menu-item {
  display: flex;
  align-items: center;
  justify-content: flex-start; /* 🔥 FIX ALIGNMENT */
  gap: 0.75rem;

  padding: 0.65rem 1rem;
  margin-bottom: 0.25rem;

  border-radius: 0.75rem;
  cursor: pointer;

  font-size: 0.9rem;
  font-weight: 500;

  transition: all 0.2s ease;
}

/* icon */
.menu-item i {
  font-size: 1rem;
  opacity: 0.95;
}

/* text */
.menu-item span {
  flex: 1;               /* 🔥 bikin teks rata kiri */
  text-align: left;
  white-space: nowrap;
}

/* chevron */
.menu-item .pi-chevron-down {
  margin-left: auto;     /* 🔥 dorong ke kanan */
  font-size: 0.7rem;
  opacity: 0.8;
}

/* hover */
.menu-item:hover {
  background: rgba(255, 255, 255, 0.15);
}

/* active */
.menu-item.bg-white\/25 {
  background: rgba(255, 255, 255, 0.25);
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.25);
}

/* ===============================
   SUBMENU LIST
================================ */
.submenu-list {
  list-style: none;
  margin: 0.25rem 0 0.5rem 1.25rem;
  padding-left: 0.25rem;
}

/* ===============================
   SUBMENU ITEM
================================ */
.submenu {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 0.5rem;

  padding: 0.5rem 0.75rem;
  margin-bottom: 0.25rem;

  border-radius: 0.5rem;
  font-size: 0.85rem;

  cursor: pointer;
  background: rgba(255, 255, 255, 0.08);
  transition: all 0.2s ease;
}

/* submenu icon */
.submenu i {
  font-size: 0.85rem;
  opacity: 0.9;
}

/* submenu text */
.submenu span {
  flex: 1;
  text-align: left;
}

/* hover */
.submenu:hover {
  background: rgba(255, 255, 255, 0.18);
}

/* ===============================
   TRANSITION (SLIDE)
================================ */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.25s ease;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

/* ===============================
   SCROLLBAR (OPTIONAL, RAPi)
================================ */
.sidebar-menu::-webkit-scrollbar {
  width: 6px;
}

.sidebar-menu::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 10px;
}

.sidebar-menu::-webkit-scrollbar-track {
  background: transparent;
}


</style>
