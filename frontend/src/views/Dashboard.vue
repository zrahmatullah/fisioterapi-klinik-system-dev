<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Chart from 'primevue/chart'

/* ======================
   STATE LAMA
====================== */
const stats = ref([])
const revenueData = ref(null)
const treatmentData = ref(null)
const patientStatus = ref(null)

/* ======================
   STATE TAMBAHAN
====================== */
const pieLayananDiambil = ref(null)
const pieLayananDikeluhkan = ref(null)
const pendaftaranBulanan = ref(null)

/* ======================
   PALET NETRAL + 1 AKSEN (INDIGO)
   Dipakai konsisten di semua chart supaya
   dashboard tidak terasa "ramai warna".
====================== */
const ACCENT = '#4f46e5'        // indigo-600 - satu-satunya warna kuat
const ACCENT_SOFT = 'rgba(79,70,229,0.12)'
const NEUTRAL_900 = '#111827'
const NEUTRAL_500 = '#6b7280'
const NEUTRAL_300 = '#d1d5db'
const NEUTRAL_200 = '#e5e7eb'
const NEUTRAL_100 = '#f3f4f6'

// Skala abu untuk data kategori (pie/bar) -> tetap monokrom,
// hanya item paling penting yang dapat warna aksen.
const NEUTRAL_SCALE = [ACCENT, '#9ca3af', '#c4c8cd', NEUTRAL_300, NEUTRAL_200]

/* ======================
   CHART OPTIONS
====================== */
const chartOptions = {
  plugins: {
    legend: {
      labels: {
        color: NEUTRAL_500,
        font: { size: 12 }
      }
    }
  },
  scales: {
    x: {
      ticks: { color: NEUTRAL_500 },
      grid: { display: false }
    },
    y: {
      ticks: { color: NEUTRAL_500 },
      grid: { color: NEUTRAL_100 }
    }
  }
}

const pieOptions = {
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        color: NEUTRAL_500,
        font: { size: 12 },
        padding: 16,
        usePointStyle: true,
        pointStyle: 'circle'
      }
    }
  }
}

/* ======================
   LOAD DASHBOARD
====================== */
const loadDashboard = async () => {
  try {

    const res = await axios.get(
      'http://localhost:8000/api/dashboard/admin',
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      }
    )

    const d = res.data

    /* ===== KPI ===== */
    stats.value = [
      {
        title: 'Total Pasien',
        value: d.kpi.total_pasien,
        icon: 'pi-users'
      },
      {
        title: 'Terapi Aktif',
        value: d.kpi.terapi_aktif,
        icon: 'pi-heart'
      },
      {
        title: 'Pendapatan Bulan Ini',
        value: formatRupiah(d.kpi.pendapatan_bulan_ini),
        icon: 'pi-wallet'
      },
      {
        title: 'Terapis',
        value: d.kpi.terapis,
        icon: 'pi-user'
      }
    ]

    /* ===== LINE – Pendapatan ===== */
    revenueData.value = {
      labels: d.pendapatan.labels,
      datasets: [
        {
          label: 'Pendapatan',
          data: d.pendapatan.data,
          fill: true,
          tension: 0.4,
          borderColor: ACCENT,
          backgroundColor: ACCENT_SOFT,
          pointBackgroundColor: ACCENT,
          pointBorderColor: '#fff',
          pointRadius: 4
        }
      ]
    }

    /* ===== BAR – Tindakan ===== */
    treatmentData.value = {
      labels: d.tindakan.labels,
      datasets: [
        {
          label: 'Jumlah Tindakan',
          data: d.tindakan.data,
          backgroundColor: d.tindakan.labels.map((_, i) =>
            i === 0 ? ACCENT : NEUTRAL_200
          ),
          borderRadius: 8,
          maxBarThickness: 42
        }
      ]
    }

    /* ===== DOUGHNUT – Status Pasien ===== */
    patientStatus.value = {
      labels: ['Aktif', 'Selesai', 'Pending'],
      datasets: [
        {
          data: [
            d.status_pasien.aktif,
            d.status_pasien.selesai,
            d.status_pasien.pending
          ],
          backgroundColor: [ACCENT, NEUTRAL_300, NEUTRAL_100],
          borderWidth: 0
        }
      ]
    }

    /* ======================
       TAMBAHAN DASHBOARD
    ====================== */
    const t = d.tambahan_dashboard

    pieLayananDiambil.value = {
      labels: t.layanan_diambil.labels,
      datasets: [
        {
          data: t.layanan_diambil.data,
          backgroundColor: NEUTRAL_SCALE,
          borderWidth: 0
        }
      ]
    }

    pieLayananDikeluhkan.value = {
      labels: t.layanan_dikeluhkan.labels,
      datasets: [
        {
          data: t.layanan_dikeluhkan.data,
          backgroundColor: NEUTRAL_SCALE,
          borderWidth: 0
        }
      ]
    }

    pendaftaranBulanan.value = {
      labels: t.pendaftaran.labels,
      datasets: [
        {
          label: 'Jumlah Pendaftaran',
          data: t.pendaftaran.data,
          fill: true,
          tension: 0.4,
          borderColor: ACCENT,
          backgroundColor: ACCENT_SOFT,
          pointBackgroundColor: ACCENT,
          pointBorderColor: '#fff',
          pointRadius: 4
        }
      ]
    }

  } catch (err) {
    console.error(err)
    alert('Gagal memuat dashboard')
  }
}

const formatRupiah = (val) =>
  'Rp ' + new Intl.NumberFormat('id-ID').format(val)

onMounted(loadDashboard)
</script>

<template>

  <!-- ======================
       DASHBOARD
  ====================== -->
  <div class="p-6 space-y-8 bg-gray-50 min-h-screen">

    <!-- HEADER -->
    <div>
      <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">
        Dashboard Admin
      </h1>

      <p class="text-gray-500 text-sm mt-1">
        Monitoring performa &amp; insight klinik terapi
      </p>
    </div>

    <!-- KPI -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

      <div
        v-for="(s,i) in stats"
        :key="i"
        class="kpi-card"
      >

        <div class="flex justify-between items-start">

          <div>
            <p class="text-sm text-gray-500">
              {{ s.title }}
            </p>

            <p class="text-2xl font-semibold text-gray-900 mt-2">
              {{ s.value }}
            </p>
          </div>

          <div class="kpi-icon">
            <i :class="['pi', s.icon]"></i>
          </div>

        </div>

      </div>

    </div>

    <!-- CHART -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

      <!-- PIE -->
      <div class="dashboard-card">
        <h3 class="chart-title">
          Layanan Paling Sering Diambil
        </h3>

        <Chart
          v-if="pieLayananDiambil"
          type="pie"
          :data="pieLayananDiambil"
          :options="pieOptions"
          class="h-72"
        />
        <div v-else class="chart-skeleton h-72"></div>
      </div>

      <!-- PIE -->
      <div class="dashboard-card">
        <h3 class="chart-title">
          Layanan Paling Sering Dikeluhkan
        </h3>

        <Chart
          v-if="pieLayananDikeluhkan"
          type="pie"
          :data="pieLayananDikeluhkan"
          :options="pieOptions"
          class="h-72"
        />
        <div v-else class="chart-skeleton h-72"></div>
      </div>

      <!-- LINE -->
      <div class="dashboard-card lg:col-span-2">
        <h3 class="chart-title">
          Perkembangan Pendaftaran
        </h3>

        <Chart
          v-if="pendaftaranBulanan"
          type="line"
          :data="pendaftaranBulanan"
          :options="chartOptions"
          class="h-80"
        />
        <div v-else class="chart-skeleton h-80"></div>
      </div>

    </div>

    <!-- INSIGHT -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

      <div class="insight">
        <div class="insight-icon"><i class="pi pi-star-fill"></i></div>
        <div>
          <h4>Terapi Terfavorit</h4>
          <p>
            Terapi <b class="text-gray-900">{{ treatmentData?.labels?.[0] ?? '-' }}</b> paling sering digunakan
          </p>
        </div>
      </div>

      <div class="insight">
        <div class="insight-icon"><i class="pi pi-wallet"></i></div>
        <div>
          <h4>Pendapatan</h4>
          <p>
            Bulan ini mencapai <b class="text-gray-900">{{ stats[2]?.value }}</b>
          </p>
        </div>
      </div>

      <div class="insight">
        <div class="insight-icon"><i class="pi pi-calendar"></i></div>
        <div>
          <h4>Pasien Aktif</h4>
          <p>
            <b class="text-gray-900">{{ patientStatus?.datasets?.[0]?.data?.[0] ?? 0 }}</b> pasien aktif saat ini
          </p>
        </div>
      </div>

    </div>

  </div>

</template>

<style scoped>
/* ======================
   TYPOGRAPHY / TITLES
====================== */
.chart-title{
  @apply font-semibold text-gray-800 mb-4 text-base;
}

/* ======================
   CARDS - netral, soft shadow, rounded
====================== */
.dashboard-card{
  @apply bg-white rounded-2xl p-6 shadow-sm border border-gray-100;
}

.kpi-card{
  @apply bg-white rounded-2xl p-5 shadow-sm border border-gray-100
         transition-shadow duration-200 hover:shadow-md;
}

.kpi-icon{
  @apply w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600
         flex items-center justify-center text-lg shrink-0;
}

/* ======================
   INSIGHT CARDS
====================== */
.insight{
  @apply bg-white rounded-2xl p-5 shadow-sm border border-gray-100
         flex items-start gap-4;
}

.insight-icon{
  @apply w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600
         flex items-center justify-center text-base shrink-0;
}

.insight h4{
  @apply font-semibold text-gray-800 mb-1 text-sm;
}

.insight p{
  @apply text-sm text-gray-500 leading-relaxed;
}

/* ======================
   CHART SKELETON (empty state)
====================== */
.chart-skeleton{
  @apply rounded-xl bg-gray-100 animate-pulse;
}
</style>