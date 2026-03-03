<template>
  <!-- <div class="bg-white text-gray-800"> -->
    <div class="bg-white text-gray-800 text-base md:text-lg lg:text-xl leading-relaxed">


    <!-- ================= NAVBAR ================= -->
    <header class="sticky top-0 bg-white/90 backdrop-blur shadow z-50">
      <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
  <img
    src="/logo.png"
    alt="Logo Abqary"
   class="h-16 w-16 object-contain"
  />
  <span class="text-3xl font-extrabold text-Gree-600"> Abqary</span>
</div>


        <nav class="hidden md:flex gap-8 font-semibold text-lg">
          <a
            v-for="n in nav"
            :key="n.href"
            :href="n.href"
            class="hover:text-indigo-600 transition"
          >
            {{ n.label }}
          </a>
        </nav>

        <button
          @click="showModal = true"
          class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition"
        >
          Login / Daftar
        </button>
      </div>
    </header>

    <!-- ================= HERO ================= -->
    <section id="home" class="bg-indigo-50 py-24">
  <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">

    <div>
      <h1 class="text-5xl md:text-6xl font-extrabold mb-6 leading-tight">
        Layanan Terapi <br /> & Tumbuh Kembang Anak
      </h1>

      <p class="text-gray-600 mb-8 max-w-lg text-justify">
        Abqary – Pusat Terapi Tumbuh Kembang Anak Kami melayani terapi untuk anak dengan berbagai kebutuhan khusus seperti keterlambatan bicara, gangguan motorik dan sensorik, gangguan belajar, fokus dan konsentrasi, ADHD, hiperaktif, autisme, cerebral palsy, dan Down syndrome.
      </p>

      <div class="flex gap-8">
        <Stat :value="stat.layanan + '+'" label="Jenis Terapi" />
        <Stat :value="stat.terapis + '+'" label="Terapis" />
        <Stat :value="stat.sesi + '+'" label="Sesi" />
      </div>
    </div>

    <!-- SLIDER -->
    <div class="flex justify-center">
      <div class="relative w-full max-w-[500px] h-[420px] rounded-2xl overflow-hidden shadow-xl">

        <img
          :src="images[currentIndex]"
          class="w-full h-full object-cover transition-all duration-700"
        />

        <button
          @click="prevSlide"
          class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/40 text-white px-2 py-1 rounded-full"
        >‹</button>

        <button
          @click="nextSlide"
          class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/40 text-white px-2 py-1 rounded-full"
        >›</button>

        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2">
          <span
            v-for="(img, i) in images"
            :key="i"
            class="w-2.5 h-2.5 rounded-full"
            :class="i === currentIndex ? 'bg-white' : 'bg-white/50'"
          ></span>
        </div>

      </div>
    </div>

  </div>
</section>


    <!-- ================= TERAPIS ================= -->
<!-- ================= TERAPIS ================= -->
<section id="terapis" class="py-24">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-4xl font-bold text-center mb-12">
      Terapis Profesional Kami
    </h2>

    <div class="grid md:grid-cols-3 gap-8">
      <div
        v-for="t in terapis"
        :key="t.nama"
        class="group bg-white rounded-2xl shadow-md p-6 text-center
               transition-all duration-300 ease-out
               hover:-translate-y-3 hover:shadow-2xl
               hover:bg-gradient-to-br hover:from-indigo-50 hover:to-white"
      >
        <!-- ICON TERAPIS -->
        <div
          class="w-28 h-28 mx-auto mb-4 rounded-full bg-indigo-50
                 border border-indigo-100 flex items-center justify-center
                 transition-all duration-300
                 group-hover:scale-110 group-hover:rotate-6"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-14 h-14 text-indigo-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="1.8"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM4 21v-1a7 7 0 0114 0v1"
            />
          </svg>
        </div>

        <!-- NAMA -->
        <h3
          class="font-semibold text-lg text-gray-800
                 transition group-hover:text-indigo-700"
        >
          {{ t.nama }}
        </h3>

        <!-- SPESIALISASI -->
        <p
          class="text-sm font-medium text-gray-500
                 transition group-hover:text-indigo-600"
        >
          {{ t.spesialisasi }}
        </p>
      </div>
    </div>
  </div>
</section>

    <!-- ================= LAYANAN ================= -->
    <section id="layanan" class="bg-gray-50 py-24">
      <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-14">
          Layanan Terapi
        </h2>

        <div class="grid md:grid-cols-3 gap-8">
          <div
            v-for="(l, i) in layanan"
            :key="i"
            class="bg-white rounded-2xl p-6 shadow
                   hover:-translate-y-2 hover:shadow-xl transition"
          >
            <div
              class="w-12 h-12 rounded-full bg-indigo-600 text-white
                     flex items-center justify-center font-bold mb-4"
            >
              {{ i + 1 }}
            </div>
            <h3 class="font-semibold mb-2">{{ l.title }}</h3>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= PROMOSI ================= -->
    <section id="promo" class="bg-indigo-50 py-24">
      <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12">
          Promo Spesial
        </h2>

        <div v-if="promosi.length" class="grid md:grid-cols-3 gap-8">
          <div
            v-for="p in promosi"
            :key="p.id"
            class="bg-white rounded-2xl shadow p-6 hover:shadow-xl transition"
          >
            <div class="flex justify-between items-center mb-4">
              <span class="bg-indigo-600 text-white text-xs px-3 py-1 rounded-full">
                {{ p.kode_promo }}
              </span>
              <span class="text-xs text-gray-500">
                s/d {{ p.tanggal_selesai }}
              </span>
            </div>

            <h3 class="text-xl font-bold text-indigo-600 mb-2">
              {{ p.nama_promo }}
            </h3>

            <p class="text-gray-600 text-sm mb-4">
              {{ p.deskripsi }}
            </p>

            <div class="text-2xl font-extrabold text-indigo-700 mb-6">
              {{ formatDiskon(p) }}
            </div>

            <button
              class="w-full bg-indigo-600 text-white py-2 rounded-lg
                     hover:bg-indigo-700 transition"
            >
              View Promosi
            </button>
          </div>
        </div>

        <p v-else class="text-center text-gray-500">
          Saat ini belum ada promo tersedia
        </p>
      </div>
    </section>

    <!-- ================= PRICE LIST ================= -->
    <section id="harga" class="py-24">
      <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12">
          Paket & Harga Terapi
        </h2>

        <div class="grid md:grid-cols-3 gap-8">
          <div
            v-for="p in paket"
            :key="p.nama"
            class="border rounded-2xl p-6 shadow-sm hover:shadow-xl transition"
          >
            <h3 class="text-xl font-bold mb-2 text-indigo-600">
              {{ p.nama }}
            </h3>

            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span>Weekday</span>
                <strong>Rp {{ format(p.harga_weekday) }}</strong>
              </div>
              <div class="flex justify-between">
                <span>Weekend</span>
                <strong>Rp {{ format(p.harga_weekend) }}</strong>
              </div>
            </div>

            <button
              class="mt-6 w-full bg-indigo-600 text-white py-2 rounded-lg
                     hover:bg-indigo-700 transition"
            >
              Informasi Layanan
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= CONTACT INFO ================= -->
<section id="kontak" class="bg-indigo-50 py-12">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-2xl font-bold text-center text-indigo-600 mb-8">
      Informasi Kontak
    </h2>

    <div class="grid md:grid-cols-3 gap-6 text-center">

      <!-- Alamat -->
      <div class="bg-white rounded-2xl shadow p-6">
        <div class="text-3xl mb-3">📍</div>
        <h3 class="font-semibold mb-1">Alamat</h3>
        <p class="text-sm text-gray-600">
          Jl. Raden Saleh No 9 C Karang Tengah, Ciledug<br />
          Kota Tangerang, Indonesia
        </p>
      </div>

      <!-- Telepon -->
      <div class="bg-white rounded-2xl shadow p-6">
        <div class="text-3xl mb-3">📞</div>
        <h3 class="font-semibold mb-1">Telepon</h3>
        <p class="text-sm text-gray-600">
          081-9811-131
        </p>
      </div>

      <!-- Email -->
      <div class="bg-white rounded-2xl shadow p-6">
        <div class="text-3xl mb-3">✉️</div>
        <h3 class="font-semibold mb-1">Email</h3>
        <p class="text-sm text-gray-600">
          abqarcdc@gmail.com
        </p>
      </div>
    </div>
  </div>
</section>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-indigo-600 text-white py-6 text-center">
      © 2026 Klinik Abqary
    </footer>

    <!-- ================= MODAL ================= -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-2xl p-8 w-full max-w-md text-center">
        <h2 class="text-xl font-bold mb-6">
          Apakah Anda sudah memiliki akun?
        </h2>

        <div class="flex justify-center gap-4">
          <button
            @click="router.push('/login')"
            class="bg-indigo-600 text-white px-6 py-2 rounded-lg"
          >
            Sudah Punya
          </button>
          <button
            @click="router.push('/register')"
            class="bg-gray-200 px-6 py-2 rounded-lg"
          >
            Belum Punya
          </button>
        </div>

        <button
          class="mt-6 text-sm text-gray-500 hover:underline"
          @click="showModal = false"
        >
          Batal
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const images = [
  '/abqary3.jpg',
  '/abqary2.jpg',
  '/abqary.jpg',

]

const currentIndex = ref(0)

const nextSlide = () => {
  currentIndex.value = (currentIndex.value + 1) % images.length
}

const prevSlide = () => {
  currentIndex.value =
    (currentIndex.value - 1 + images.length) % images.length
}

onMounted(() => {
  setInterval(nextSlide, 4000)
})

const router = useRouter()
const showModal = ref(false)

const stat = ref({ layanan: 0, terapis: 0, sesi: 0 })
const terapis = ref([])
const layanan = ref([])
const paket = ref([])
const promosi = ref([])
const kontak = ref({})

const nav = [
  { label: 'Home', href: '#home' },
  { label: 'Terapis', href: '#terapis' },
  { label: 'Layanan', href: '#layanan' },
  { label: 'Promo', href: '#promo' },
  { label: 'Harga', href: '#harga' },
  {label : 'Kontak', href: '#kontak' }
]

const loadLanding = async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/public/landing')

    stat.value = res.data.stat
    terapis.value = res.data.terapis
    layanan.value = res.data.layanan
    paket.value = res.data.paket
    promosi.value = res.data.promosi || []

  } catch (err) {
    console.error('Gagal load landing page', err)
  }
}

const format = (val) =>
  new Intl.NumberFormat('id-ID').format(val)

const formatDiskon = (promo) => {
  if (promo.tipe_diskon === 'persen') {
    return `Diskon ${promo.nilai_diskon}%`
  }
  return `Potongan Rp ${format(promo.nilai_diskon)}`
}

onMounted(loadLanding)
</script>
