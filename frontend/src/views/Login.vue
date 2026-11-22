<template>
  <div class="min-h-screen flex flex-col lg:flex-row font-sans">

    <!-- ================= 1. LEFT HERO PANEL (BRAND & CLINICAL COLLAGE) ================= -->
    <div class="w-full lg:w-1/2 bg-[#0B3948] text-white flex flex-col justify-between p-6 sm:p-10 lg:p-12 relative overflow-hidden select-none">

      <!-- Background Glow Details -->
      <div class="absolute -top-32 -left-32 w-80 h-80 rounded-full bg-teal-500/10 blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-teal-400/10 blur-3xl pointer-events-none"></div>

      <!-- TOP BRAND HEADER -->
      <div class="text-center relative z-10">
        <!-- Turquoise Medical Cross Icon -->
        <div class="flex items-center justify-center mb-3">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#2DD4BF] to-[#0F766E] text-[#0B3948] flex items-center justify-center font-bold text-2xl shadow-lg shadow-teal-950/40">
            <i class="pi pi-plus font-black"></i>
          </div>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-white tracking-wide">SIMRS KlinikKu</h1>
      </div>

      <!-- CENTER CLINICAL COLLAGE & HEADLINE -->
      <div class="my-6 relative z-10 max-w-lg mx-auto w-full">
        <!-- 3-PANEL CLINICAL COLLAGE -->
        <div class="bg-[#082934]/90 border border-teal-500/20 rounded-2xl p-3 shadow-2xl backdrop-blur-sm">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 h-64 sm:h-72">

            <!-- LEFT PANEL: RAWAT INAP & TERAPIS CARE -->
            <div class="bg-gradient-to-b from-[#104859] to-[#0B3948] rounded-xl p-3.5 flex flex-col justify-between border border-teal-400/20 relative overflow-hidden group">
              <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-teal-400/20 text-teal-300 border border-teal-400/30">
                  Pediatrik Care
                </span>
                <i class="pi pi-heart-fill text-rose-400 text-xs"></i>
              </div>

              <!-- Graphic Vector / Silhouette -->
              <div class="my-auto flex flex-col items-center text-center space-y-2 py-2">
                <div class="w-16 h-16 rounded-2xl bg-teal-400/10 border border-teal-400/30 flex items-center justify-center text-teal-300 text-2xl shadow-inner">
                  <i class="pi pi-user-plus"></i>
                </div>
                <div>
                  <p class="font-bold text-xs text-white">Layanan Rawat & Sesi Terapi</p>
                  <p class="text-[10px] text-teal-200/70 mt-0.5">Dokter Spesialis & Tim Terapis</p>
                </div>
              </div>

              <div class="bg-white/10 rounded-lg p-2 text-[10px] text-slate-200 flex items-center justify-between">
                <span>Status Klinis:</span>
                <span class="text-emerald-300 font-bold flex items-center gap-1">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                  Terpantau RME
                </span>
              </div>
            </div>

            <!-- RIGHT COLUMN: 2 TILES STACKED -->
            <div class="flex flex-col gap-2.5 h-full">

              <!-- TOP TILE: KONSULTASI & ASESMEN -->
              <div class="flex-1 bg-gradient-to-r from-[#104859] to-[#0B3948] rounded-xl p-3 flex flex-col justify-between border border-teal-400/20">
                <div class="flex items-center justify-between">
                  <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-teal-400/20 text-teal-300">
                    Asesmen & Observasi
                  </span>
                  <i class="pi pi-id-card text-teal-300 text-xs"></i>
                </div>
                <div class="flex items-center gap-2.5 my-1">
                  <div class="w-8 h-8 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center text-sm shrink-0">
                    <i class="pi pi-clipboard"></i>
                  </div>
                  <div>
                    <p class="font-semibold text-xs text-white leading-tight">Rekam Medis Terpadu</p>
                    <p class="text-[10px] text-teal-200/70">SOAP, ICD-10 & Milestone</p>
                  </div>
                </div>
              </div>

              <!-- BOTTOM TILE: PEMERIKSAAN & DUKUNGAN -->
              <div class="flex-1 bg-gradient-to-r from-[#104859] to-[#0B3948] rounded-xl p-3 flex flex-col justify-between border border-teal-400/20">
                <div class="flex items-center justify-between">
                  <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-teal-400/20 text-teal-300">
                    Resep & Farmasi FEFO
                  </span>
                  <i class="pi pi-shield text-emerald-400 text-xs"></i>
                </div>
                <div class="flex items-center gap-2.5 my-1">
                  <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-sm shrink-0">
                    <i class="pi pi-box"></i>
                  </div>
                  <div>
                    <p class="font-semibold text-xs text-white leading-tight">Integrasi Kasir & Apotek</p>
                    <p class="text-[10px] text-teal-200/70">Otomatis & Real-time</p>
                  </div>
                </div>
              </div>

            </div>

          </div>
        </div>

        <!-- HEADLINE & DESCRIPTION -->
        <div class="text-center mt-5 space-y-1.5">
          <h2 class="text-lg sm:text-xl font-bold text-white leading-snug">
            Sistem Informasi Manajemen<br />Rumah Sakit Terintegrasi
          </h2>
          <p class="text-xs text-slate-300 max-w-sm mx-auto leading-relaxed">
            Akses cepat, aman, dan efisien untuk seluruh tenaga medis dan staf rumah sakit.
          </p>
        </div>
      </div>

      <!-- BOTTOM FOOTER -->
      <div class="text-center relative z-10">
        <span class="text-xs text-teal-300/60 font-mono tracking-wider">v3.4.0</span>
      </div>

    </div>

    <!-- ================= 2. RIGHT LOGIN CARD PANEL ================= -->
    <div class="w-full lg:w-1/2 bg-[#F3F6F8] flex items-center justify-center p-6 sm:p-10 lg:p-12 min-h-screen">

      <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xl shadow-slate-900/5 p-7 sm:p-9 max-w-md w-full space-y-6">

        <!-- CARD HEADING -->
        <div class="text-center space-y-1">
          <h2 class="text-2xl font-bold text-[#0F172A] tracking-tight">Selamat Datang</h2>
          <p class="text-xs text-[#64748B]">Silakan masuk ke akun SIMRS Anda</p>
        </div>

        <!-- ERROR ALERT -->
        <div v-if="error" class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl flex items-center gap-2">
          <i class="pi pi-exclamation-circle text-sm shrink-0"></i>
          <span>{{ error }}</span>
        </div>

        <!-- FORM -->
        <form @submit.prevent="login" class="space-y-4" novalidate>

          <!-- FIELD 1: USERNAME -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
              Username / Email / No. Pegawai
            </label>
            <div class="relative flex items-center">
              <i class="pi pi-user absolute left-3.5 text-slate-400 text-xs pointer-events-none"></i>
              <input
                v-model="username"
                type="text"
                placeholder="Masukkan username atau nomor pegawai"
                class="w-full pl-9 pr-3.5 py-2.5 text-xs text-slate-800 border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 transition"
                autocomplete="username"
                required
              />
            </div>
          </div>

          <!-- FIELD 2: KATA SANDI -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
              Kata Sandi
            </label>
            <div class="relative flex items-center">
              <i class="pi pi-lock absolute left-3.5 text-slate-400 text-xs pointer-events-none"></i>
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                class="w-full pl-9 pr-28 py-2.5 text-xs text-slate-800 border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 transition font-mono"
                autocomplete="current-password"
                required
              />

              <!-- TOGGLE PASSWORD VISIBILITY BUTTON -->
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-1.5 top-1.5 bottom-1.5 px-2.5 text-[11px] font-medium text-slate-600 hover:text-slate-900 rounded bg-slate-100 hover:bg-slate-200 border border-slate-200 flex items-center gap-1.5 transition select-none"
              >
                <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'" class="text-[10px]"></i>
                <span>{{ showPassword ? 'Sembunyikan' : 'Tampilkan/Mata' }}</span>
              </button>
            </div>
          </div>

          <!-- ROW: INGAT SAYA & LUPA KATA SANDI -->
          <div class="flex items-center justify-between text-xs pt-1">
            <label class="flex items-center gap-2 cursor-pointer text-slate-700 select-none">
              <input
                type="checkbox"
                v-model="rememberMe"
                class="w-4 h-4 rounded text-[#0F766E] accent-[#0F766E] focus:ring-[#0F766E]"
              />
              <span class="font-medium">Ingat Saya</span>
            </label>

            <button
              type="button"
              @click="showForgot = true"
              class="text-[#0F766E] hover:text-[#115E59] font-medium hover:underline transition"
            >
              Lupa Kata Sandi?
            </button>
          </div>

          <!-- SUBMIT CTA BUTTON -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-[#0F766E] hover:bg-[#115E59] active:scale-98 text-white font-medium py-2.5 px-4 rounded-lg text-xs transition shadow-sm flex items-center justify-center gap-2 disabled:opacity-60 cursor-pointer"
          >
            <i v-if="loading" class="pi pi-spin pi-spinner text-xs"></i>
            <span>Masuk ke Sistem</span>
          </button>

        </form>

        <!-- QUICK ROLE LOGIN SECTION -->
        <div class="pt-4 border-t border-slate-200/80 space-y-2.5">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
              <i class="pi pi-bolt text-teal-600 text-xs"></i>
              Akses Cepat Login Otomatis Peran
            </span>
            <span class="text-[10px] text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200 font-medium">1-Klik Masuk</span>
          </div>

          <div class="grid grid-cols-2 gap-2">
            <!-- 1. Super Admin -->
            <button
              type="button"
              @click="quickLogin('admin', 'password')"
              class="flex items-center gap-2 p-2 rounded-lg border border-teal-600/30 bg-teal-50/70 hover:bg-teal-100 text-left transition group"
            >
              <div class="w-7 h-7 rounded-md bg-[#0B3948] text-teal-300 flex items-center justify-center text-xs font-bold shrink-0">
                <i class="pi pi-shield"></i>
              </div>
              <div class="min-w-0">
                <p class="text-xs font-bold text-teal-950 truncate">Super Admin</p>
                <p class="text-[10px] text-teal-700 truncate font-mono">admin</p>
              </div>
            </button>

            <!-- 2. Dokter -->
            <button
              type="button"
              @click="quickLogin('dokter', 'password')"
              class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-left transition group hover:border-teal-500"
            >
              <div class="w-7 h-7 rounded-md bg-teal-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                <i class="pi pi-heart"></i>
              </div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-800 truncate">Dokter Spesialis</p>
                <p class="text-[10px] text-slate-500 truncate font-mono">dokter</p>
              </div>
            </button>

            <!-- 3. Terapis -->
            <button
              type="button"
              @click="quickLogin('terapis', 'password')"
              class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-left transition group hover:border-teal-500"
            >
              <div class="w-7 h-7 rounded-md bg-cyan-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                <i class="pi pi-user"></i>
              </div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-800 truncate">Terapis Klinis</p>
                <p class="text-[10px] text-slate-500 truncate font-mono">terapis</p>
              </div>
            </button>

            <!-- 4. Kasir -->
            <button
              type="button"
              @click="quickLogin('kasir', 'password')"
              class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-left transition group hover:border-teal-500"
            >
              <div class="w-7 h-7 rounded-md bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                <i class="pi pi-wallet"></i>
              </div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-800 truncate">Kasir & Billing</p>
                <p class="text-[10px] text-slate-500 truncate font-mono">kasir</p>
              </div>
            </button>

            <!-- 5. Farmasi -->
            <button
              type="button"
              @click="quickLogin('apoteker', 'password')"
              class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-left transition group hover:border-teal-500"
            >
              <div class="w-7 h-7 rounded-md bg-amber-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                <i class="pi pi-box"></i>
              </div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-800 truncate">Farmasi / Obat</p>
                <p class="text-[10px] text-slate-500 truncate font-mono">apoteker</p>
              </div>
            </button>

            <!-- 6. Pendaftaran -->
            <button
              type="button"
              @click="quickLogin('pendaftaran', 'password')"
              class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-left transition group hover:border-teal-500"
            >
              <div class="w-7 h-7 rounded-md bg-[#15576B] text-white flex items-center justify-center text-xs font-bold shrink-0">
                <i class="pi pi-ticket"></i>
              </div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-800 truncate">Pendaftaran</p>
                <p class="text-[10px] text-slate-500 truncate font-mono">pendaftaran</p>
              </div>
            </button>

            <!-- 7. Orang Tua -->
            <button
              type="button"
              @click="quickLogin('orangtua', 'password')"
              class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-left transition group hover:border-teal-500"
            >
              <div class="w-7 h-7 rounded-md bg-sky-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                <i class="pi pi-users"></i>
              </div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-800 truncate">Orang Tua Pasien</p>
                <p class="text-[10px] text-slate-500 truncate font-mono">orangtua</p>
              </div>
            </button>

            <!-- 8. Rekam Medis -->
            <button
              type="button"
              @click="quickLogin('rekammedis', 'password')"
              class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-left transition group hover:border-teal-500"
            >
              <div class="w-7 h-7 rounded-md bg-slate-700 text-white flex items-center justify-center text-xs font-bold shrink-0">
                <i class="pi pi-id-card"></i>
              </div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-800 truncate">Rekam Medis (RME)</p>
                <p class="text-[10px] text-slate-500 truncate font-mono">rekammedis</p>
              </div>
            </button>
          </div>
        </div>

        <!-- FOOTER HELPDESK -->
        <div class="pt-4 border-t border-slate-100 text-center">
          <p class="text-[11px] text-[#64748B] flex items-center justify-center gap-1.5 flex-wrap">
            <i class="pi pi-phone text-xs text-[#0F766E]"></i>
            <span>Butuh bantuan akses? Hubungi <strong class="text-slate-800">Tim IT / Helpdesk Rumah Sakit</strong></span>
          </p>
        </div>

      </div>

    </div>

    <!-- FULLSCREEN LOADING ANIMATION ON LOGIN SUCCESS -->
    <teleport to="body">
      <transition name="fade">
        <div v-if="pageLoading" class="fixed inset-0 bg-[#0B3948]/90 backdrop-blur-md flex flex-col items-center justify-center z-[9999] text-white space-y-4">
          <div class="w-12 h-12 rounded-xl bg-[#0F766E] flex items-center justify-center text-xl shadow-lg animate-bounce">
            <i class="pi pi-plus font-bold"></i>
          </div>
          <div class="w-48 h-1.5 bg-white/20 rounded-full overflow-hidden">
            <div class="h-full bg-[#2DD4BF] rounded-full animate-pulse w-3/4"></div>
          </div>
          <p class="text-xs text-teal-200">Menyiapkan workspace SIMRS...</p>
        </div>
      </transition>
    </teleport>

    <!-- FORGOT PASSWORD MODAL -->
    <teleport to="body">
      <transition name="fade">
        <div
          v-if="showForgot"
          class="fixed inset-0 bg-slate-900/35 backdrop-blur-md flex items-center justify-center z-[9999] p-4"
          @click.self="closeForgot"
        >
          <div class="bg-white/95 backdrop-blur-xl rounded-xl w-full max-w-[440px] shadow-[0_20px_50px_rgba(11,57,72,0.22)] border border-white/70 overflow-hidden flex flex-col animate-fadeIn">
            <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
              <h3 class="text-[13.5px] font-semibold">Reset Kata Sandi Akun</h3>
              <button @click="closeForgot" class="w-6 h-6 rounded flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition text-xs">
                ✕
              </button>
            </div>

            <div class="p-5 space-y-3.5 text-xs text-slate-700">
              <div v-if="fpError" class="p-2.5 bg-red-50 text-red-700 text-xs rounded-lg border border-red-200">
                {{ fpError }}
              </div>

              <!-- Step 1: Username -->
              <div v-if="step === 1" class="space-y-3">
                <p class="text-xs text-slate-500">Masukkan username terdaftar untuk menerima kode verifikasi OTP.</p>
                <div>
                  <label class="block text-xs font-semibold text-slate-700 mb-1">Username Akun</label>
                  <input
                    v-model="fpUsername"
                    type="text"
                    placeholder="Contoh: admin atau terapis"
                    class="w-full h-[34px] px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
                  />
                </div>
                <button
                  type="button"
                  @click="checkUsername"
                  :disabled="fpLoading"
                  class="w-full h-[34px] bg-[#0F766E] hover:bg-[#115E59] text-white font-medium rounded-lg text-xs transition cursor-pointer"
                >
                  {{ fpLoading ? 'Memeriksa...' : 'Kirim Kode OTP' }}
                </button>
              </div>

              <!-- Step 2: OTP -->
              <div v-else-if="step === 2" class="space-y-3">
                <p class="text-xs text-slate-500">Masukkan 6 digit kode OTP yang telah dikirim ke {{ fpEmail }}.</p>
                <div>
                  <label class="block text-xs font-semibold text-slate-700 mb-1">Kode OTP</label>
                  <input
                    v-model="otpCode"
                    type="text"
                    placeholder="123456"
                    class="w-full h-[34px] px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 text-center font-mono font-bold tracking-widest"
                  />
                </div>
                <button
                  type="button"
                  @click="verifyOtp"
                  :disabled="fpLoading"
                  class="w-full h-[34px] bg-[#0F766E] hover:bg-[#115E59] text-white font-medium rounded-lg text-xs transition cursor-pointer"
                >
                  {{ fpLoading ? 'Memverifikasi...' : 'Verifikasi OTP' }}
                </button>
              </div>

              <!-- Step 3: Password Baru -->
              <div v-else class="space-y-3">
                <div>
                  <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru</label>
                  <input
                    v-model="newPassword"
                    type="password"
                    class="w-full h-[34px] px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi</label>
                  <input
                    v-model="confirmPassword"
                    type="password"
                    class="w-full h-[34px] px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
                  />
                </div>
                <button
                  type="button"
                  @click="resetPasswordOtp"
                  :disabled="fpLoading"
                  class="w-full h-[34px] bg-[#0F766E] hover:bg-[#115E59] text-white font-medium rounded-lg text-xs transition cursor-pointer"
                >
                  {{ fpLoading ? 'Menyimpan...' : 'Perbarui Kata Sandi' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </teleport>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '../axios'

const router = useRouter()
const toast = useToast()

// Form states
const username = ref('admin')
const password = ref('')
const rememberMe = ref(true)
const loading = ref(false)
const error = ref('')
const showPassword = ref(false)
const pageLoading = ref(false)

// Forgot Password states
const showForgot = ref(false)
const step = ref(1)
const fpUsername = ref('')
const fpEmail = ref('')
const otpCode = ref('')
const newPassword = ref('')
const confirmPassword = ref('')
const fpLoading = ref(false)
const fpError = ref('')
const resetToken = ref(null)

// Quick Role Login
const quickLogin = async (u, p) => {
  username.value = u
  password.value = p
  await login()
}

// Login Action
const login = async () => {
  error.value = ''
  loading.value = true

  try {
    const res = await api.post('/login', {
      username: username.value,
      password: password.value
    })

    const { user, access_token } = res.data

    localStorage.setItem('token', access_token)
    localStorage.setItem('role_id', user.role_id)
    localStorage.setItem('user', JSON.stringify(user))

    toast.success(`Selamat datang, ${user.nama || user.name || username.value}`)

    pageLoading.value = true

    setTimeout(() => {
      if (user.role_id === 15) {
        router.push('/dashboard-orang-tua')
      } else if (user.role_id === 14) {
        router.push('/dashboard-terapis')
      } else {
        router.push('/dashboard')
      }
    }, 1200)

  } catch (err) {
    const msg = err.response?.data?.error || err.response?.data?.message || 'Login gagal, periksa username dan kata sandi Anda.'
    error.value = msg
    toast.error(msg)
  } finally {
    loading.value = false
  }
}

// Check Username for OTP
const checkUsername = async () => {
  fpError.value = ''
  fpLoading.value = true
  try {
    const res = await api.post('/forgot-password/check-username', { username: fpUsername.value })
    fpEmail.value = res.data.email
    await api.post('/otp-email/reset/send', { email: fpEmail.value })
    step.value = 2
    toast.success('Kode OTP telah dikirimkan ke email Anda')
  } catch (err) {
    fpError.value = err.response?.data?.error || 'Username atau email tidak ditemukan di database.'
  } finally {
    fpLoading.value = false
  }
}

// Verify OTP
const verifyOtp = async () => {
  fpError.value = ''
  fpLoading.value = true
  try {
    const res = await api.post('/otp-email/reset/verify', { email: fpEmail.value, kode_otp: otpCode.value })
    resetToken.value = res.data.reset_token
    step.value = 3
    toast.success('Kode OTP terverifikasi')
  } catch (err) {
    fpError.value = err.response?.data?.message || 'Kode OTP tidak cocok atau telah kedaluwarsa.'
  } finally {
    fpLoading.value = false
  }
}

// Reset Password
const resetPasswordOtp = async () => {
  fpError.value = ''
  if (newPassword.value !== confirmPassword.value) {
    fpError.value = 'Konfirmasi kata sandi tidak cocok.'
    return
  }

  fpLoading.value = true
  try {
    await api.post('/reset-password', {
      email: fpEmail.value,
      reset_token: resetToken.value,
      password: newPassword.value,
      password_confirmation: confirmPassword.value
    })
    toast.success('Kata sandi berhasil diperbarui, silakan masuk.')
    closeForgot()
  } catch (err) {
    fpError.value = err.response?.data?.message || 'Gagal mereset kata sandi.'
  } finally {
    fpLoading.value = false
  }
}

const closeForgot = () => {
  showForgot.value = false
  step.value = 1
  fpError.value = ''
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
