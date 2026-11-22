/**
 * Centralized SIMRS Menu & RBAC Configuration
 * Role IDs:
 * 13: Super Admin (Admin)
 * 14: Terapis
 * 15: Orang Tua
 * 16: Pendaftaran
 * 17: Dokter / Psikolog
 * 18: Farmasi / Apoteker
 * 19: Kasir
 * 20: Gudang / Logistik
 * 21: Manajemen / Owner
 */

export const ROLE_IDS = {
  SUPER_ADMIN: 13,
  TERAPIS: 14,
  ORANG_TUA: 15,
  PENDAFTARAN: 16,
  DOKTER: 17,
  FARMASI: 18,
  KASIR: 19,
  GUDANG: 20,
  MANAJEMEN: 21,
}

export const menuCategories = [
  {
    category: 'Pelayanan & Pasien',
    allowedRoles: [13, 14, 15, 16, 17, 18, 19, 20, 21],
    items: [
      {
        title: 'Dashboard',
        route: '/dashboard',
        icon: 'pi pi-th-large',
        allowedRoles: [13, 16, 17, 18, 19, 20, 21],
        dynamicRoute: (roleId) => {
          if (roleId === 15) return '/dashboard-orang-tua'
          if (roleId === 14) return '/dashboard-terapis'
          if (roleId === 18) return '/dashboard-farmasi'
          return '/dashboard'
        }
      },
      {
        title: 'Portal Orang Tua',
        route: '/dashboard-orang-tua',
        icon: 'pi pi-home',
        allowedRoles: [13, 15],
      },
      {
        title: 'Dashboard Terapis',
        route: '/dashboard-terapis',
        icon: 'pi pi-chart-pie',
        allowedRoles: [13, 14],
      },
      {
        title: 'Dashboard Farmasi',
        route: '/dashboard-farmasi',
        icon: 'pi pi-box',
        allowedRoles: [13, 17, 18, 19, 21],
      },
      {
        title: 'Dashboard Penunjang',
        route: '/dashboard-penunjang',
        icon: 'pi pi-sliders-h',
        allowedRoles: [13, 14, 17, 18, 19, 21],
      },
      {
        title: 'Daftar Pasien (RM)',
        route: '/data-pasien',
        icon: 'pi pi-id-card',
        allowedRoles: [13, 14, 15, 16, 17, 21],
      },
      {
        title: 'Pemeriksaan RME Terpadu',
        route: '/emr-timeline',
        icon: 'pi pi-file-edit',
        allowedRoles: [13, 14, 17, 21],
      },
      {
        title: 'Pendaftaran Baru',
        route: '/registrasi',
        icon: 'pi pi-user-plus',
        allowedRoles: [13, 15, 16],
      },
      {
        title: 'Rawat Jalan / Antrian',
        route: '/list-pendaftaran-layanan',
        icon: 'pi pi-users',
        allowedRoles: [13, 14, 16, 17, 21],
      },
      {
        title: 'Keluhan Pasien',
        route: '/keluhan-anak',
        icon: 'pi pi-comment',
        allowedRoles: [13, 15],
      },
    ]
  },
  {
    category: 'Penjadwalan & Terapi',
    allowedRoles: [13, 14, 15, 16, 17, 21],
    items: [
      {
        title: 'Mapping Jadwal',
        route: '/mapping-jadwal',
        icon: 'pi pi-calendar-plus',
        allowedRoles: [13, 14, 16, 17, 21],
      },
      {
        title: 'Jadwal Terapi',
        route: '/jadwal-terapi',
        icon: 'pi pi-calendar',
        allowedRoles: [13, 14, 15, 16, 17, 21],
      },
      {
        title: 'Aktivitas & Sesi Terapi',
        route: '/catatan-aktivitas-anak',
        icon: 'pi pi-check-square',
        allowedRoles: [13, 14, 15, 17, 21],
      },
      {
        title: 'Report Catatan Aktivitas',
        route: '/report-catatan-aktivitas',
        icon: 'pi pi-file-edit',
        allowedRoles: [13, 14, 17, 21],
      },
      {
        title: 'Evaluasi & Monitoring Terapi',
        route: '/evaluasi-terapi',
        icon: 'pi pi-heart-fill',
        allowedRoles: [13, 14, 17, 21],
      },
    ]
  },
  {
    category: 'Billing & Farmasi',
    allowedRoles: [13, 14, 15, 17, 18, 19, 20, 21],
    items: [
      {
        title: 'Verifikasi Resep & Penunjang',
        route: '/verifikasi-resep-penunjang',
        icon: 'pi pi-check-circle',
        allowedRoles: [13, 14, 17, 18, 19, 21],
      },
      {
        title: 'Tagihan & Kasir POS',
        route: '/kasir-billing',
        icon: 'pi pi-wallet',
        allowedRoles: [13, 19, 21],
      },
      {
        title: 'Riwayat Pembayaran',
        route: '/riwayat-pembayaran-anak',
        icon: 'pi pi-receipt',
        allowedRoles: [13, 15, 19],
      },
      {
        title: 'Farmasi & Stok FEFO',
        route: '/farmasi-logistik',
        icon: 'pi pi-box',
        allowedRoles: [13, 18, 20, 21],
      },
    ]
  },
  {
    category: 'Dokumen & Klinis',
    allowedRoles: [13, 14, 15, 17, 21],
    items: [
      {
        title: 'Dokumen & Surat',
        route: '/surat-dokumen',
        icon: 'pi pi-file',
        allowedRoles: [13, 14, 17, 21],
      },
    ]
  },
  {
    category: 'Sistem & Operasional',
    allowedRoles: [13, 15, 16, 21],
    items: [
      {
        title: 'TV Antrian',
        route: '/pemanggilan-antrian',
        icon: 'pi pi-bell',
        allowedRoles: [13, 16, 21],
      },
      {
        title: 'List Reschedule',
        route: '/list-reschedule',
        icon: 'pi pi-clock',
        allowedRoles: [13, 15, 16, 21],
      },
      {
        title: 'Pengaturan SIMRS',
        route: '/pengaturan-klinik',
        icon: 'pi pi-cog',
        allowedRoles: [13],
      },
    ]
  },
  {
    category: 'Master Data',
    allowedRoles: [13, 21],
    items: [
      {
        title: 'Pusat Master Data',
        route: '/master-data',
        icon: 'pi pi-database',
        allowedRoles: [13, 21],
      },
      {
        title: 'Master User',
        route: '/master-user',
        icon: 'pi pi-user',
        allowedRoles: [13],
      },
      {
        title: 'Master Role',
        route: '/master-role',
        icon: 'pi pi-id-card',
        allowedRoles: [13],
      },
      {
        title: 'Master Profil Anak',
        route: '/master-anak',
        icon: 'pi pi-users',
        allowedRoles: [13, 21],
      },
      {
        title: 'Master Terapis & Dokter',
        route: '/master-terapis',
        icon: 'pi pi-user-plus',
        allowedRoles: [13, 21],
      },
      {
        title: 'Master Jadwal Terapis',
        route: '/master-jadwal-terapis',
        icon: 'pi pi-calendar',
        allowedRoles: [13, 14, 21],
      },
      {
        title: 'Master Ruangan',
        route: '/master-ruangan',
        icon: 'pi pi-building',
        allowedRoles: [13, 21],
      },
      {
        title: 'Kategori Layanan',
        route: '/master-kategori-layanan',
        icon: 'pi pi-tags',
        allowedRoles: [13, 21],
      },
      {
        title: 'Master Layanan & Tarif',
        route: '/master-layanan',
        icon: 'pi pi-briefcase',
        allowedRoles: [13, 21],
      },
      {
        title: 'Master Penjamin & Asuransi',
        route: '/master-penjamin',
        icon: 'pi pi-shield',
        allowedRoles: [13, 19, 21],
      },
      {
        title: 'Master Paket Terapi',
        route: '/master-paket-terapi',
        icon: 'pi pi-box',
        allowedRoles: [13, 16, 19, 21],
      },
      {
        title: 'Master ICD-10 & ICD-9',
        route: '/master-icd',
        icon: 'pi pi-book',
        allowedRoles: [13, 14, 17, 21],
      },
      {
        title: 'Master Supplier Rekanan',
        route: '/master-supplier',
        icon: 'pi pi-truck',
        allowedRoles: [13, 18, 20, 21],
      },
      {
        title: 'Master Produk Farmasi',
        route: '/master-produk',
        icon: 'pi pi-shopping-bag',
        allowedRoles: [13, 18, 20, 21],
      },
      {
        title: 'Master Promosi',
        route: '/master-promosi',
        icon: 'pi pi-percentage',
        allowedRoles: [13, 21],
      },
    ]
  },
  {
    category: 'Laporan & Rekap',
    allowedRoles: [13, 19, 21],
    items: [
      {
        title: 'Laporan Pembayaran',
        route: '/laporan-pembayaran',
        icon: 'pi pi-file-excel',
        allowedRoles: [13, 19, 21],
      },
      {
        title: 'Laporan Keluhan',
        route: '/laporan-keluhan',
        icon: 'pi pi-comment',
        allowedRoles: [13, 21],
      },
      {
        title: 'Laporan Promosi',
        route: '/laporan-histori-promosi',
        icon: 'pi pi-history',
        allowedRoles: [13, 21],
      },
      {
        title: 'Keluhan Admin',
        route: '/keluhan-admin',
        icon: 'pi pi-inbox',
        allowedRoles: [13, 21],
      },
    ]
  }
]

export const getDefaultLandingRoute = (roleId) => {
  switch (Number(roleId)) {
    case ROLE_IDS.ORANG_TUA:
      return '/dashboard-orang-tua'
    case ROLE_IDS.TERAPIS:
      return '/dashboard-terapis'
    case ROLE_IDS.DOKTER:
      return '/dashboard'
    case ROLE_IDS.KASIR:
      return '/kasir-billing'
    case ROLE_IDS.FARMASI:
      return '/dashboard-farmasi'
    case ROLE_IDS.GUDANG:
      return '/farmasi-logistik'
    case ROLE_IDS.PENDAFTARAN:
      return '/registrasi'
    case ROLE_IDS.SUPER_ADMIN:
    default:
      return '/dashboard'
  }
}

export const isRouteAllowed = (path, roleId, username = '') => {
  if (Number(roleId) === ROLE_IDS.SUPER_ADMIN || username.toLowerCase() === 'admin') {
    return true
  }

  // Flatten items
  for (const cat of menuCategories) {
    for (const item of cat.items) {
      if (item.route === path) {
        return item.allowedRoles.includes(Number(roleId))
      }
    }
  }

  // Allow sub-routes or general auth routes if user is logged in
  const generalAllowed = ['/dashboard', '/dashboard-terapis', '/dashboard-orang-tua']
  if (generalAllowed.includes(path)) return true

  return true
}
