<template>
  <aside class="sidebar">
    <!-- HEADER -->
    <div class="sidebar-header">
      <div class="logo-wrapper">
        <!-- <img src="/logo.png" alt="Logo Klinik" /> -->
      </div>

      <div class="brand-text">
        <div class="brand-name">E-Clinic Dev</div>
        <div class="brand-sub">By Zharpiil</div>
      </div>
    </div>

    <!-- MENU SCROLLABLE -->
    <nav class="sidebar-menu">

      <!-- Dashboard -->
      <div class="menu-section">
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
      </div>

      <!-- ORANG TUA -->
      <template v-if="isOrangTua">
        <p class="section-label">Layanan Saya</p>

        <li @click="go('/registrasi')" :class="itemClass('/registrasi')">
          <i class="pi pi-file"></i>
          <span>Pendaftaran</span>
        </li>

        <li @click="go('/riwayat-pembayaran-anak')" :class="itemClass('/riwayat-pembayaran-anak')">
          <i class="pi pi-credit-card"></i>
          <span>Pembayaran Anak</span>
        </li>

        <li @click="go('/catatan-aktivitas-anak')" :class="itemClass('/catatan-aktivitas-anak')">
          <i class="pi pi-calendar"></i>
          <span>Jadwal Terapi</span>
        </li>

        <li @click="go('/hasil-evaluasi')" :class="itemClass('/hasil-evaluasi')">
          <i class="pi pi-heart-fill"></i>
          <span>Monitoring Terapi</span>
        </li>

        <li @click="go('/keluhan-anak')" :class="itemClass('/keluhan-anak')">
          <i class="pi pi-comment"></i>
          <span>Keluhan</span>
        </li>
      </template>

      <!-- TERAPIS -->
      <template v-if="isTerapis">
        <p class="section-label">Aktivitas Terapi</p>

        <li @click="go('/jadwal-terapi')" :class="itemClass('/jadwal-terapi')">
          <i class="pi pi-calendar"></i>
          <span>Jadwal Terapi</span>
        </li>

        <li @click="go('/report-catatan-aktivitas')" :class="itemClass('/report-catatan-aktivitas')">
          <i class="pi pi-file-edit"></i>
          <span>Report Catatan Aktivitas</span>
        </li>

        <li @click="go('/evaluasi-terapi')" :class="itemClass('/evaluasi-terapi')">
          <i class="pi pi-heart-fill"></i>
          <span>Evaluasi Terapi</span>
        </li>

        <li @click="go('/riwayat-evaluasi')" :class="itemClass('/riwayat-evaluasi')">
          <i class="pi pi-history"></i>
          <span>Riwayat Evaluasi</span>
        </li>
      </template>

      <!-- ADMIN -->
      <template v-if="isAdmin">

        <p class="section-label">Operasional</p>

        <li @click="go('/registrasi')" :class="itemClass('/registrasi')">
          <i class="pi pi-file"></i>
          <span>Pendaftaran</span>
        </li>

        <li @click="go('/input-layanan')" :class="itemClass('/input-layanan')">
          <i class="pi pi-plus-circle"></i>
          <span>Input Layanan Anak</span>
        </li>

        <li @click="go('/list-pendaftaran-layanan')" :class="itemClass('/list-pendaftaran-layanan')">
          <i class="pi pi-list"></i>
          <span>Daftar Layanan</span>
        </li>

        <li @click="go('/list-reschedule')" :class="itemClass('/list-reschedule')">
          <i class="pi pi-calendar-clock"></i>
          <span>List Reschedule</span>
        </li>

        <li @click="go('/pembayaran-tagihan')" :class="itemClass('/pembayaran-tagihan')">
          <i class="pi pi-credit-card"></i>
          <span>Pembayaran / Tagihan</span>
        </li>

        <li @click="go('/keluhan-admin')" :class="itemClass('/keluhan-admin')">
          <i class="pi pi-comment"></i>
          <span>Keluhan Admin</span>
        </li>

        <li @click="go('/pemanggilan-antrian')" :class="itemClass('/pemanggilan-antrian')">
          <i class="pi pi-bell"></i>
          <span>Pemanggilan Antrian</span>
        </li>

        <!-- Master Data (collapsible) -->
        <p class="section-label">Master Data</p>

        <li @click="toggleMenu('master')" class="menu-item menu-item--toggle">
          <i class="pi pi-database"></i>
          <span>Master Data</span>
          <i
            class="pi pi-chevron-down toggle-caret"
            :class="{ 'is-open': activeMenu === 'master' }"
          />
        </li>

        <transition name="slide">
          <ul v-if="activeMenu === 'master'" class="submenu-list">
            <li @click="go('/master-user')" class="submenu" :class="{ 'submenu--active': route.path === '/master-user' }">
              <i class="pi pi-user"></i><span>Master User</span>
            </li>
            <li @click="go('/master-role')" class="submenu" :class="{ 'submenu--active': route.path === '/master-role' }">
              <i class="pi pi-id-card"></i><span>Master Role</span>
            </li>
            <li @click="go('/master-anak')" class="submenu" :class="{ 'submenu--active': route.path === '/master-anak' }">
              <i class="pi pi-users"></i><span>Master Anak</span>
            </li>
            <li @click="go('/master-terapis')" class="submenu" :class="{ 'submenu--active': route.path === '/master-terapis' }">
              <i class="pi pi-users"></i><span>Master Terapis</span>
            </li>
            <li @click="go('/master-jadwal-terapis')" class="submenu" :class="{ 'submenu--active': route.path === '/master-jadwal-terapis' }">
              <i class="pi pi-briefcase"></i><span>Master Jadwal Terapis</span>
            </li>
            <li @click="go('/master-ruangan')" class="submenu" :class="{ 'submenu--active': route.path === '/master-ruangan' }">
              <i class="pi pi-building"></i><span>Master Ruangan</span>
            </li>
            <li @click="go('/master-kategori-layanan')" class="submenu" :class="{ 'submenu--active': route.path === '/master-kategori-layanan' }">
              <i class="pi pi-tags"></i><span>Kategori Layanan</span>
            </li>
            <li @click="go('/master-layanan')" class="submenu" :class="{ 'submenu--active': route.path === '/master-layanan' }">
              <i class="pi pi-briefcase"></i><span>Master Layanan</span>
            </li>
            <li @click="go('/master-promosi')" class="submenu" :class="{ 'submenu--active': route.path === '/master-promosi' }">
              <i class="pi pi-briefcase"></i><span>Master Promosi</span>
            </li>
            <li @click="go('/master-produk')" class="submenu" :class="{ 'submenu--active': route.path === '/master-produk' }">
              <i class="pi pi-briefcase"></i><span>Master Produk Farmasi</span>
            </li>
          </ul>
        </transition>

        <!-- Laporan (collapsible) -->
        <p class="section-label">Laporan</p>

        <li @click="toggleMenu('laporan')" class="menu-item menu-item--toggle">
          <i class="pi pi-chart-bar"></i>
          <span>Laporan</span>
          <i
            class="pi pi-chevron-down toggle-caret"
            :class="{ 'is-open': activeMenu === 'laporan' }"
          />
        </li>

        <transition name="slide">
          <ul v-if="activeMenu === 'laporan'" class="submenu-list">
            <li @click="go('/laporan-pembayaran')" class="submenu" :class="{ 'submenu--active': route.path === '/laporan-pembayaran' }">
              <i class="pi pi-clipboard"></i><span>Laporan Pembayaran</span>
            </li>
            <li @click="go('/laporan-keluhan')" class="submenu" :class="{ 'submenu--active': route.path === '/laporan-keluhan' }">
              <i class="pi pi-clipboard"></i><span>Laporan Keluhan</span>
            </li>
            <li @click="go('/laporan-histori-promosi')" class="submenu" :class="{ 'submenu--active': route.path === '/laporan-histori-promosi' }">
              <i class="pi pi-clipboard"></i><span>Laporan Promosi</span>
            </li>
          </ul>
        </transition>

      </template>

    </nav>
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
const itemClass = (path) => ['menu-item', route.path === path ? 'menu-item--active' : '']
</script>

<style scoped>
/* ===============================
   TOKENS
================================ */
.sidebar {
  --bg: #FAFAFA;
  --surface: #FFFFFF;
  --border: #ECEDF1;
  --ink: #1F2128;
  --muted: #98A0AE;
  --accent: #6D5CE0;
  --accent-soft: #F1EEFC;

  width: 17rem;
  height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--bg);
  color: var(--ink);
  border-right: 1px solid var(--border);
  overflow: hidden;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* ===============================
   HEADER
================================ */
.sidebar-header {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding: 1.5rem 1.5rem 1.25rem;
  flex-shrink: 0;
}

.logo-wrapper {
  width: 42px;
  height: 42px;
  border-radius: 11px;
  background: var(--surface);
  border: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.logo-wrapper img {
  width: 30px;
  height: 30px;
  object-fit: contain;
}

.brand-text {
  display: flex;
  flex-direction: column;
  line-height: 1.25;
  min-width: 0;
}

.brand-name {
  font-size: 0.92rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  color: var(--ink);
}

.brand-sub {
  font-size: 0.7rem;
  font-weight: 500;
  color: var(--muted);
  letter-spacing: 0.03em;
}

/* ===============================
   MENU CONTAINER
================================ */
.sidebar-menu {
  flex: 1;
  overflow-y: auto;
  padding: 0.25rem 1rem 1.5rem;
  list-style: none;
}

.menu-section {
  list-style: none;
  margin-bottom: 0.25rem;
}

.section-label {
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--muted);
  margin: 1.35rem 0.75rem 0.5rem;
}

/* ===============================
   MENU ITEM
================================ */
.menu-item {
  position: relative;
  display: flex;
  align-items: center;
  gap: 0.7rem;

  padding: 0.6rem 0.75rem;
  margin-bottom: 0.15rem;

  border-radius: 0.6rem;
  cursor: pointer;

  font-size: 0.84rem;
  font-weight: 500;
  color: var(--ink);

  list-style: none;
  transition: background 0.15s ease, color 0.15s ease;
}

.menu-item i:not(.toggle-caret) {
  font-size: 0.95rem;
  width: 1.1rem;
  text-align: center;
  color: var(--muted);
  transition: color 0.15s ease;
}

.menu-item span {
  flex: 1;
  text-align: left;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.menu-item:hover {
  background: var(--surface);
}

.menu-item:hover i:not(.toggle-caret) {
  color: var(--accent);
}

/* active state — signature: thin left indicator, not a filled block */
.menu-item--active {
  background: var(--accent-soft);
  color: var(--accent);
  font-weight: 600;
}

.menu-item--active i:not(.toggle-caret) {
  color: var(--accent);
}

.menu-item--active::before {
  content: '';
  position: absolute;
  left: -1rem;
  top: 0.3rem;
  bottom: 0.3rem;
  width: 3px;
  border-radius: 0 3px 3px 0;
  background: var(--accent);
}

/* toggle row (Master Data / Laporan) */
.menu-item--toggle {
  margin-bottom: 0;
}

.toggle-caret {
  margin-left: auto;
  font-size: 0.65rem;
  color: var(--muted);
  transition: transform 0.2s ease;
}

.toggle-caret.is-open {
  transform: rotate(180deg);
}

/* ===============================
   SUBMENU
================================ */
.submenu-list {
  list-style: none;
  margin: 0.1rem 0 0.4rem 0;
  padding-left: 1.85rem;
  border-left: 1px solid var(--border);
  margin-left: 1.05rem;
}

.submenu {
  display: flex;
  align-items: center;
  gap: 0.6rem;

  padding: 0.5rem 0.6rem;
  margin: 0.05rem 0;

  border-radius: 0.5rem;
  font-size: 0.8rem;
  font-weight: 500;
  color: var(--muted);

  cursor: pointer;
  transition: background 0.15s ease, color 0.15s ease;
}

.submenu i {
  font-size: 0.8rem;
  width: 1rem;
  text-align: center;
}

.submenu span {
  flex: 1;
  text-align: left;
}

.submenu:hover {
  background: var(--surface);
  color: var(--ink);
}

.submenu--active {
  background: var(--accent-soft);
  color: var(--accent);
  font-weight: 600;
}

/* ===============================
   TRANSITION
================================ */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.2s ease;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

/* ===============================
   SCROLLBAR
================================ */
.sidebar-menu::-webkit-scrollbar {
  width: 5px;
}

.sidebar-menu::-webkit-scrollbar-thumb {
  background: var(--border);
  border-radius: 10px;
}

.sidebar-menu::-webkit-scrollbar-track {
  background: transparent;
}
</style>