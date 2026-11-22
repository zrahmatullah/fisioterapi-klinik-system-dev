<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- TOP HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm shrink-0">
          <i class="pi pi-database"></i>
        </div>
        <div>
          <h2 class="text-base font-bold text-[#0F172A] leading-tight">Pusat Manajemen Master Data SIMRS</h2>
          <p class="text-xs text-[#64748B] mt-0.5">Katalog terpadu seluruh entitas master data klinik: pasien, tenaga medis, layanan, ruangan, asuransi, dan tarif</p>
        </div>
      </div>

      <div class="text-xs text-slate-500 font-mono shrink-0">
        14 Modul Master Terintegrasi
      </div>
    </div>

    <!-- SEARCH MASTER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm flex items-center gap-2.5 shrink-0">
      <div class="relative flex-1">
        <i class="pi pi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
        <input
          v-model="searchMaster"
          type="text"
          placeholder="Cari menu master (mis. ruangan, asuransi, tarif, terapis, user, paket)..."
          class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]"
        />
      </div>
      <span class="text-xs text-slate-400 shrink-0">Pencarian Cepat</span>
    </div>

    <!-- MASTER CARDS GRID -->
    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar pr-1">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5">
        <div
          v-for="card in filteredMasterCards"
          :key="card.route"
          @click="router.push(card.route)"
          class="bg-white border border-slate-200 hover:border-teal-400 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] hover:shadow-md transition-all cursor-pointer group flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between mb-2">
              <div
                class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                :class="card.colorClass"
              >
                <i :class="card.icon" class="text-sm"></i>
              </div>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold tabular-nums" :class="card.badgeClass">
                {{ card.count ?? '...' }} Data
              </span>
            </div>

            <h3 class="font-bold text-slate-900 text-[13px] group-hover:text-[#0F766E] transition-colors leading-tight">
              {{ card.title }}
            </h3>
            <p class="text-[11.5px] text-slate-500 mt-1 leading-normal line-clamp-2">
              {{ card.description }}
            </p>
          </div>

          <div class="pt-2.5 mt-2.5 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-[#0F766E]">
            <span>Buka Master</span>
            <i class="pi pi-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../axios'

const router = useRouter()
const searchMaster = ref('')
const stats = ref({})

const fetchStats = async () => {
  try {
    const res = await api.get('/pediatric-workflow/master-stats')
    stats.value = res.data || {}
  } catch (err) {
    console.error('Error fetching master stats:', err)
  }
}

const masterCards = computed(() => [
  {
    title: 'Master Profil Anak (Pasien)',
    route: '/master-anak',
    icon: 'pi pi-users',
    colorClass: 'bg-teal-50 text-teal-700 border border-teal-200',
    badgeClass: 'bg-teal-50 text-teal-700',
    count: stats.value.total_pasien_anak,
    description: 'Master data rekam medis anak, data orang tua/wali, NIK, dan diagnosis awal.'
  },
  {
    title: 'Master Jadwal & Terapis',
    route: '/master-terapis',
    icon: 'pi pi-user-plus',
    colorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    badgeClass: 'bg-emerald-50 text-emerald-700',
    count: stats.value.total_terapis,
    description: 'Data dokter spesialis, fisioterapis, terapis wicara, dan okupasi terapi.'
  },
  {
    title: 'Master Jadwal Shift',
    route: '/master-jadwal-terapis',
    icon: 'pi pi-calendar',
    colorClass: 'bg-indigo-50 text-indigo-700 border border-indigo-200',
    badgeClass: 'bg-indigo-50 text-indigo-700',
    count: stats.value.total_jadwal,
    description: 'Konfigurasi hari kerja, shift pagi/sore, dan kuota pasien per sesi.'
  },
  {
    title: 'Master Layanan Klinis',
    route: '/master-layanan',
    icon: 'pi pi-heart',
    colorClass: 'bg-rose-50 text-rose-700 border border-rose-200',
    badgeClass: 'bg-rose-50 text-rose-700',
    count: stats.value.total_layanan,
    description: 'Katalog tarif tindakan terapi sensori integrasi, fisioterapi, dan wicara.'
  },
  {
    title: 'Master Kategori Layanan',
    route: '/master-kategori-layanan',
    icon: 'pi pi-tags',
    colorClass: 'bg-amber-50 text-amber-700 border border-amber-200',
    badgeClass: 'bg-amber-50 text-amber-700',
    count: stats.value.total_kategori_layanan,
    description: 'Pengelompokan jenis layanan medis, terapi umum, dan paket bundling.'
  },
  {
    title: 'Master Paket & Bundling',
    route: '/master-paket-terapi',
    icon: 'pi pi-box',
    colorClass: 'bg-purple-50 text-purple-700 border border-purple-200',
    badgeClass: 'bg-purple-50 text-purple-700',
    count: stats.value.total_paket_terapi,
    description: 'Manajemen paket multi-sesi (4x, 8x, 12x), kuota kunjungan, dan masa aktif.'
  },
  {
    title: 'Master Penjamin & Asuransi',
    route: '/master-penjamin',
    icon: 'pi pi-shield',
    colorClass: 'bg-sky-50 text-sky-700 border border-sky-200',
    badgeClass: 'bg-sky-50 text-sky-700',
    count: stats.value.total_guarantor,
    description: 'Mitra penjamin BPJS Kesehatan, asuransi swasta rekanan, dan korporat.'
  },
  {
    title: 'Master Ruangan Terapi',
    route: '/master-ruangan',
    icon: 'pi pi-home',
    colorClass: 'bg-cyan-50 text-cyan-700 border border-cyan-200',
    badgeClass: 'bg-cyan-50 text-cyan-700',
    count: stats.value.total_ruangan,
    description: 'Alokasi ruang sensori integrasi, gym motorik, wicara, dan poli dokter.'
  },
  {
    title: 'Master ICD-10 & ICD-9',
    route: '/master-icd',
    icon: 'pi pi-book',
    colorClass: 'bg-blue-50 text-blue-700 border border-blue-200',
    badgeClass: 'bg-blue-50 text-blue-700',
    count: (stats.value.total_icd10 || 0) + (stats.value.total_icd9 || 0),
    description: 'Klasifikasi diagnosis medis ICD-10 dan prosedur terapi ICD-9-CM standar Kemenkes.'
  },
  {
    title: 'Master Produk & Obat',
    route: '/master-produk',
    icon: 'pi pi-shopping-bag',
    colorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    badgeClass: 'bg-emerald-50 text-emerald-700',
    count: stats.value.total_produk_farmasi,
    description: 'Katalog obat farmasi, multivitamin anak, dan alat sensori integrasi.'
  },
  {
    title: 'Master Supplier Rekanan',
    route: '/master-supplier',
    icon: 'pi pi-truck',
    colorClass: 'bg-teal-50 text-teal-700 border border-teal-200',
    badgeClass: 'bg-teal-50 text-teal-700',
    count: stats.value.total_supplier,
    description: 'Katalog vendor distributor obat, suplemen anak, dan sarana terapi medis.'
  },
  {
    title: 'Master Promo & Diskon',
    route: '/master-promosi',
    icon: 'pi pi-percentage',
    colorClass: 'bg-orange-50 text-orange-700 border border-orange-200',
    badgeClass: 'bg-orange-50 text-orange-700',
    count: stats.value.total_promosi,
    description: 'Voucher diskon sesi terapi, potongan harga pembukaan, dan promo musiman.'
  },
  {
    title: 'Master Hak Akses (Role)',
    route: '/master-role',
    icon: 'pi pi-key',
    colorClass: 'bg-slate-100 text-slate-700 border border-slate-300',
    badgeClass: 'bg-slate-100 text-slate-700',
    count: stats.value.total_role,
    description: 'Konfigurasi peranan sistem (Admin, Dokter, Terapis, Kasir, Farmasi, Orang Tua).'
  },
  {
    title: 'Master Pengguna (User)',
    route: '/master-user',
    icon: 'pi pi-user',
    colorClass: 'bg-teal-50 text-teal-800 border border-teal-200',
    badgeClass: 'bg-teal-50 text-teal-800',
    count: stats.value.total_user,
    description: 'Manajemen akun login pegawai klinik, dokter, staf administrasi, dan hak akses.'
  }
])

const filteredMasterCards = computed(() => {
  const q = searchMaster.value.toLowerCase().trim()
  if (!q) return masterCards.value
  return masterCards.value.filter(c =>
    c.title.toLowerCase().includes(q) ||
    c.description.toLowerCase().includes(q)
  )
})

onMounted(fetchStats)
</script>
