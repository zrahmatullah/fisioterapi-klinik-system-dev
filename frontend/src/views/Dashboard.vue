<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Chart from 'primevue/chart'

/* ======================
   LOADING SCREEN
====================== */
const pageLoading = ref(true)

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
   CHART OPTIONS
====================== */
const chartOptions = {
  plugins: {
    legend: {
      labels: {
        color: '#374151'
      }
    }
  },
  scales: {
    x: {
      ticks: { color: '#6b7280' },
      grid: { display: false }
    },
    y: {
      ticks: { color: '#6b7280' },
      grid: { color: '#e5e7eb' }
    }
  }
}

/* ======================
   LOAD DASHBOARD
====================== */
const loadDashboard = async () => {
  try {

    pageLoading.value = true

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
        icon: 'pi-users',
        color: 'from-indigo-500 to-indigo-700'
      },
      {
        title: 'Terapi Aktif',
        value: d.kpi.terapi_aktif,
        icon: 'pi-heart',
        color: 'from-emerald-500 to-emerald-700'
      },
      {
        title: 'Pendapatan Bulan Ini',
        value: formatRupiah(d.kpi.pendapatan_bulan_ini),
        icon: 'pi-wallet',
        color: 'from-amber-500 to-amber-700'
      },
      {
        title: 'Terapis',
        value: d.kpi.terapis,
        icon: 'pi-user',
        color: 'from-rose-500 to-rose-700'
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
          borderColor: '#6366f1',
          backgroundColor: 'rgba(99,102,241,0.15)'
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
          backgroundColor: [
            '#6366f1',
            '#10b981',
            '#f59e0b',
            '#ef4444'
          ],
          borderRadius: 10
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
          backgroundColor: [
            '#22c55e',
            '#6366f1',
            '#f97316'
          ]
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
          backgroundColor: [
            '#6366f1',
            '#22c55e',
            '#f59e0b',
            '#ef4444',
            '#06b6d4'
          ]
        }
      ]
    }

    pieLayananDikeluhkan.value = {
      labels: t.layanan_dikeluhkan.labels,
      datasets: [
        {
          data: t.layanan_dikeluhkan.data,
          backgroundColor: [
            '#ef4444',
            '#f97316',
            '#facc15',
            '#6366f1',
            '#22c55e'
          ]
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
          borderColor: '#6366f1',
          backgroundColor: 'rgba(99,102,241,0.15)'
        }
      ]
    }

    /* ======================
       DELAY LOADING
    ====================== */
    setTimeout(() => {
      pageLoading.value = false
    }, 1400)

  } catch (err) {
    console.error(err)
    alert('Gagal memuat dashboard')
    pageLoading.value = false
  }
}

const formatRupiah = (val) =>
  'Rp ' + new Intl.NumberFormat('id-ID').format(val)

onMounted(loadDashboard)
</script>

<template>

  <!-- ======================
       LOADING SCREEN
  ====================== -->
  <transition name="fade">

    <div
      v-if="pageLoading"
      class="
        fixed inset-0 z-[9999]
        bg-gradient-to-br from-indigo-700 via-purple-700 to-fuchsia-600
        flex items-center justify-center
        overflow-hidden
      "
    >

      <!-- BLUR CIRCLE -->
      <div
        class="
          absolute -top-40 -right-40
          w-96 h-96
          bg-white/10
          rounded-full blur-3xl
        "
      ></div>

      <div
        class="
          absolute -bottom-40 -left-40
          w-96 h-96
          bg-pink-400/20
          rounded-full blur-3xl
        "
      ></div>

      <!-- CONTENT -->
      <div class="relative text-center text-white">

        <!-- LOGO -->
        <div
          class="
            relative
            w-32 h-32
            mx-auto
            mb-8
          "
        >

          <!-- OUTER SPIN -->
          <div
            class="
              absolute inset-0
              rounded-full
              border-[6px]
              border-white/20
            "
          ></div>

          <div
            class="
              absolute inset-0
              rounded-full
              border-[6px]
              border-transparent
              border-t-white
              animate-spin
            "
          ></div>

          <!-- LOGO -->
          <div
            class="
              absolute inset-4
              bg-white
              rounded-full
              flex items-center justify-center
              shadow-2xl
            "
          >
            <img
              src="/logo-abqary.png"
              alt="Logo"
              class="w-16 h-16 object-contain animate-pulse"
            />
          </div>

        </div>

        <!-- TITLE -->
        <h1 class="text-3xl font-bold tracking-wide">
          Welcome Back
        </h1>

        <p class="mt-2 text-white/80 text-sm">
          Please Wait While We Load Your Account
        </p>

        <!-- LOADING DOT -->
        <div class="flex justify-center gap-2 mt-6">

          <span class="loading-dot"></span>
          <span class="loading-dot delay-150"></span>
          <span class="loading-dot delay-300"></span>

        </div>

      </div>

    </div>

  </transition>

  <!-- ======================
       DASHBOARD
  ====================== -->
  <div
    v-if="!pageLoading"
    class="p-6 space-y-8"
  >

    <!-- HEADER -->
    <div>
      <h1 class="text-3xl font-bold text-gray-800">
        Dashboard Admin
      </h1>

      <p class="text-gray-500 text-sm">
        Monitoring performa & insight klinik terapi
      </p>
    </div>

    <!-- KPI -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

      <div
        v-for="(s,i) in stats"
        :key="i"
        class="
          rounded-3xl
          p-6
          text-white
          shadow-xl
          bg-gradient-to-r
          hover:scale-[1.02]
          transition-all duration-300
        "
        :class="s.color"
      >

        <div class="flex justify-between items-center">

          <div>
            <p class="text-sm opacity-90">
              {{ s.title }}
            </p>

            <p class="text-3xl font-bold mt-2">
              {{ s.value }}
            </p>
          </div>

          <div
            class="
              w-14 h-14
              rounded-2xl
              bg-white/20
              flex items-center justify-center
              backdrop-blur
            "
          >
            <i :class="['pi', s.icon, 'text-2xl']"></i>
          </div>

        </div>

      </div>

    </div>

    <!-- CHART -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- PIE -->
      <div class="dashboard-card">
        <h3 class="chart-title">
          Layanan Paling Sering Diambil
        </h3>

        <Chart
          v-if="pieLayananDiambil"
          type="pie"
          :data="pieLayananDiambil"
          class="h-72"
        />
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
          class="h-72"
        />
      </div>

      <!-- LINE -->
      <div class="dashboard-card lg:col-span-2">
        <h3 class="chart-title">
          📈 Perkembangan Pendaftaran
        </h3>

        <Chart
          v-if="pendaftaranBulanan"
          type="line"
          :data="pendaftaranBulanan"
          :options="chartOptions"
          class="h-80"
        />
      </div>

    </div>

    <!-- INSIGHT -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

      <div class="insight bg-indigo-50 border border-indigo-100">
        <h4>🔥 Terapi Terfavorit</h4>

        <p>
          Terapi
          <b>{{ treatmentData?.labels?.[0] ?? '-' }}</b>
          paling sering digunakan
        </p>
      </div>

      <div class="insight bg-emerald-50 border border-emerald-100">
        <h4>💰 Pendapatan</h4>

        <p>
          Bulan ini mencapai
          <b>{{ stats[2]?.value }}</b>
        </p>
      </div>

      <div class="insight bg-amber-50 border border-amber-100">
        <h4>📅 Pasien Aktif</h4>

        <p>
          {{ patientStatus?.datasets?.[0]?.data?.[0] ?? 0 }}
          pasien aktif
        </p>
      </div>

    </div>

  </div>

</template>

<style scoped>
.chart-title{
  @apply font-semibold text-gray-800 mb-4 text-lg;
}

.dashboard-card{
  @apply bg-white rounded-3xl p-6 shadow-sm border border-slate-100;
}

.insight{
  @apply p-5 rounded-2xl shadow-sm;
}

.insight h4{
  @apply font-semibold text-gray-800 mb-2;
}

.insight p{
  @apply text-sm text-gray-600;
}

/* ======================
   FADE
====================== */
.fade-enter-active,
.fade-leave-active{
  transition: opacity .5s ease;
}

.fade-enter-from,
.fade-leave-to{
  opacity: 0;
}

/* ======================
   LOADING DOT
====================== */
.loading-dot{
  width: 10px;
  height: 10px;
  border-radius: 999px;
  background: white;
  animation: bounce 1s infinite;
}

.delay-150{
  animation-delay: .15s;
}

.delay-300{
  animation-delay: .3s;
}

@keyframes bounce{
  0%, 80%, 100%{
    transform: scale(0);
    opacity: .5;
  }

  40%{
    transform: scale(1);
    opacity: 1;
  }
}
</style>