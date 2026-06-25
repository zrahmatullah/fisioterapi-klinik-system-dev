import { createRouter, createWebHistory } from 'vue-router'

import LandingPage from '../views/LandingPage.vue'
import Login from '../views/Login.vue'
import RegisterLanding from '../views/RegisterLanding.vue'

import MainLayout from '../layouts/MainLayout.vue'
import Dashboard from '../views/Dashboard.vue'
import MasterUser from '../views/Master-User.vue'
import MasterRole from '../views/Master-Role.vue'
import MasterProfileAnak from '../views/MasterProfileAnak.vue'
import DashboardOrangTua from '../views/DashboardOrangTua.vue'
import MasterKategoriLayanan from '../views/Master-Kategori-Layanan.vue'
import MasterLayanan from '../views/Master-Layanan.vue'
import MasterRuangan from '../views/Master-Ruangan.vue'
import Registrasi from '../views/Registrasi.vue'
import DashboardTerapis from '../views/DashboardTerapis.vue'
import InputLayanan from '../views/Input-Layanan.vue'
import ListPendaftaranLayanan from '../views/List-Pendaftaran-Layanan.vue'
import PembayaranTagihan from '../views/PembayaranTagihan.vue'
import Assesment_1 from '../views/Assesment_1.vue'
import RiwayatPembayaranAnak from '../views/Riwayat-Pembayaran-Anak.vue'
import MasterPromosi from '../views/Master-Promosi.vue'
import MasterJadwalTerapis from '../views/Master-Jadwal-Terapis.vue'
import MasterTerapis from '../views/Master-Terapis.vue'
import JadwalTerapi from '../views/Jadwal-Terapi.vue'
import ReportCatatanTerapi from '../views/ReportCatatanTerapi.vue'
import EvaluasiTerapi from '../views/Evaluasi-Terapi.vue'
import HasilEvaluasi from '../views/Hasil-Evaluasi.vue'
import CatatanAktivitasAnak from '../views/CatatanAktivitasAnak.vue'
import KeluhanAnak from '../views/KeluhanAnak.vue'
import KeluhanAdmin from '../views/KeluhanAdmin.vue'
import LaporanPembayaran from '../views/LaporanPembayaran.vue'
import LaporanKeluhan from '../views/LaporanKeluhan.vue'
import LaporanHistoryPromosi from '../views/LaporanHistoryPromosi.vue'
import RiwayatEvaluasi from '../views/RiwayatEvaluasi.vue'
import ListReschedule from '../views/List-Reschedule.vue'
import PemanggilAntrian from '../views/PemanggilAntrian.vue'
import MasterProduk from '../views/Master-Produk.vue'

const routes = [
  // ================= GUEST =================
  {
    path: '/',
    name: 'Landing',
    component: LandingPage,
    meta: { guestOnly: true }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guestOnly: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: RegisterLanding,
    meta: { guestOnly: true }
  },

  // ================= AUTH =================
  {
    path: '/',
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: Dashboard
      },
      {
        path: 'dashboard-orang-tua',
        name: 'DashboardOrangTua',
        component: DashboardOrangTua
      },
      {
        path: 'dashboard-terapis',
        name: 'DashboardTerapis',
        component: DashboardTerapis
      },
      {
        path: 'master-user',
        name: 'MasterUser',
        component: MasterUser
      },
      {
        path: 'master-role',
        name: 'MasterRole',
        component: MasterRole
      },
      {
        path: 'master-anak',
        name: 'MasterProfileAnak',
        component: MasterProfileAnak
      },
      {
        path: 'master-ruangan',
        name: 'MasterRuangan',
        component: MasterRuangan
      },
      {
        path: 'master-kategori-layanan',
        name: 'MasterKategoriLayanan',
        component: MasterKategoriLayanan
      },
      {
        path: 'master-layanan',
        name: 'MasterLayanan',
        component: MasterLayanan
      },
      {
        path: 'registrasi',
        name: 'Registasi',
        component: Registrasi
      },
      {
        path: 'input-layanan',
        name: 'InputLayanan',
        component: InputLayanan
      },
      {
        path: 'list-pendaftaran-layanan',
        name: 'ListPendaftaranLayanan',
        component: ListPendaftaranLayanan
      },
      {
        path: 'pembayaran-tagihan',
        name: 'PembayaranTagihan',
        component: PembayaranTagihan
      },
      {
        path: 'assesment',
        name: 'Assesment',
        component: Assesment_1
      },
      {
        path: 'riwayat-pembayaran-anak',
        name: 'RiwayatPembayaranAnak',
        component: RiwayatPembayaranAnak
      },
      {
        path: 'master-promosi',
        name: 'MasterPromosi',
        component: MasterPromosi
      },
      {
        path: 'master-jadwal-terapis',
        name: 'MasterJadwalTerapis',
        component: MasterJadwalTerapis
      },
      {
        path: 'master-terapis',
        name: 'MasterTerapis',
        component: MasterTerapis
      },
      {
        path: 'jadwal-terapi',
        name: 'JadwalTerapi',
        component: JadwalTerapi
      },
      {
        path: 'report-catatan-aktivitas',
        name: 'ReportCatatanAktivitas',
        component: ReportCatatanTerapi
      },
      {
        path: 'evaluasi-terapi',
        name: 'EvaluasiTerapi',
        component: EvaluasiTerapi
      },
      {
        path: 'hasil-evaluasi',
        name: 'HasilEvaluasi',
        component: HasilEvaluasi
      },
      {
        path: 'catatan-aktivitas-anak',
        name: 'CatatanAktivitasAnak',
        component: CatatanAktivitasAnak
      },
      {
        path: 'keluhan-anak',
        name: 'KeluhanAnak',
        component: KeluhanAnak
      },
      {
        path: 'keluhan-admin',
        name: 'KeluhanAdmin',
        component: KeluhanAdmin
      },
      {
        path: 'laporan-pembayaran',
        name: 'LaporanPembayaran',
        component: LaporanPembayaran
      },
      {
        path: 'laporan-keluhan',
        name: 'LaporanKeluhan',
        component: LaporanKeluhan
      },
      {
        path: 'laporan-histori-promosi',
        name: 'LaporanHistoriPromosi',
        component: LaporanHistoryPromosi
      },
      {
        path: 'riwayat-evaluasi',
        name: 'RiwayatEvaluasi',
        component: RiwayatEvaluasi
      },
      {
        path: 'list-reschedule',
        name: 'ListReschedule',
        component: ListReschedule
      },
      {
        path: 'pemanggilan-antrian',
        name: 'PemanggilanAntrian',
        component: PemanggilAntrian
      },
      {
        path: 'master-produk',
        name: 'MasterProduk',
        component: MasterProduk
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// ================= AUTH GUARD =================
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  // 🔒 Belum login tapi mau ke halaman auth
  if (to.meta.requiresAuth && !token) {
    next('/login')
    return
  }

  // 🚫 Sudah login tapi mau ke guest page
  if (to.meta.guestOnly && token) {
    next('/dashboard')
    return
  }

  next()
})

export default router
