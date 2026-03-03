<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '../axios'

const router = useRouter()
const toast = useToast()

// LOGIN STATE
const username = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

// FORGOT PASSWORD STATE
const showForgot = ref(false)
const step = ref(1) // 1: cek username, 2: update password
const fpUsername = ref('')
const newPassword = ref('')
const confirmPassword = ref('')
const fpLoading = ref(false)
const fpError = ref('')

const fpEmail = ref('')
const otpCode = ref('')
const resetToken = ref(null)

// LOGIN
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

    toast.success(`Selamat datang ${username.value}`)

    if (user.role_id === 15) router.push('/dashboard-orang-tua')
    else if (user.role_id === 14) router.push('/dashboard-terapis')
    else router.push('/dashboard')

  } catch (err) {
    const msg = err.response?.data?.error || 'Login gagal'
    error.value = msg
    toast.error(msg)
  } finally {
    loading.value = false
  }
}

// STEP 1 – CEK USERNAME
const checkUsername = async () => {
  fpError.value = ''
  fpLoading.value = true

  try {
    const res = await api.post('/forgot-password/check-username', {
      username: fpUsername.value
    })

    // backend harus kirim email user
    fpEmail.value = res.data.email

    // kirim OTP reset
    await api.post('/otp-email/reset/send', { email: fpEmail.value })

    step.value = 2
    toast.success('Kode OTP dikirim ke email')
  } catch (err) {
    fpError.value = err.response?.data?.error || 'Email tidak ditemukan'
  } finally {
    fpLoading.value = false
  }
}


// STEP 2 – UPDATE PASSWORD
const updatePassword = async () => {
  fpError.value = ''

  if (newPassword.value !== confirmPassword.value) {
    fpError.value = 'Konfirmasi password tidak sama'
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

    toast.success('Password berhasil diperbarui, silakan login')
    showForgot.value = false
    step.value = 1
    fpUsername.value = ''
    fpEmail.value = ''
    newPassword.value = ''
    confirmPassword.value = ''

  } catch (err) {
    fpError.value = err.response?.data?.message || 'Gagal update password'
  } finally {
    fpLoading.value = false
  }
}


// === OTP RESET FLOW ===

// STEP 1 - KIRIM OTP KE EMAIL
const sendOtp = async () => {
  fpError.value = ''
  fpLoading.value = true

  try {
    await api.post('/otp/send', { email: fpEmail.value })
    step.value = 2
    toast.success('Kode reset dikirim ke email')
  } catch (err) {
    fpError.value = err.response?.data?.message || 'Gagal kirim kode'
  } finally {
    fpLoading.value = false
  }
}

// STEP 2 - VERIFIKASI OTP
const verifyOtp = async () => {
  fpError.value = ''
  fpLoading.value = true

  try {
    const res = await api.post('/otp-email/reset/verify', {
      email: fpEmail.value,
      kode_otp: otpCode.value
    })

    resetToken.value = res.data.reset_token
    step.value = 3
    toast.success('Kode valid')
  } catch (err) {
    fpError.value = err.response?.data?.message || 'OTP salah'
  } finally {
    fpLoading.value = false
  }
}


// STEP 3 - RESET PASSWORD
const resetPasswordOtp = async () => {
  fpError.value = ''

  if (newPassword.value !== confirmPassword.value) {
    fpError.value = 'Konfirmasi password tidak sama'
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

    toast.success('Password berhasil diubah')
    showForgot.value = false
    step.value = 1
    fpEmail.value = ''
    otpCode.value = ''
    newPassword.value = ''
    confirmPassword.value = ''

  } catch (err) {
    fpError.value = err.response?.data?.message || 'Gagal reset password'
  } finally {
    fpLoading.value = false
  }
}

</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-700 via-purple-700 to-fuchsia-600 px-4">

    <!-- CARD -->
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 relative overflow-hidden">

      <!-- DECORATION -->
      <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-100 rounded-full"></div>
      <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-purple-100 rounded-full"></div>

      <!-- CONTENT -->
      <div class="relative">

        <!-- LOGO -->
        <div class="flex justify-center mb-6">
          <img
            src="/logo-abqary.png"
            alt="Logo Klinik Abqary"
            class="w-28 h-28 object-contain"
            />
        </div>

        <!-- TITLE -->
        <h1 class="text-2xl font-bold text-center text-gray-800">
          KLINIK ABQARY
        </h1>
        <p class="text-center text-sm text-gray-500 mb-8">
          Sistem Informasi Tumbuh Kembang Anak
        </p>

        <!-- ERROR -->
        <div
          v-if="error"
          class="mb-5 flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl"
        >
          <i class="pi pi-exclamation-circle"></i>
          <span>{{ error }}</span>
        </div>

        <!-- FORM -->
        <form @submit.prevent="login" class="space-y-5">

          <!-- USERNAME -->
          <div>
            <label class="text-sm font-medium text-gray-600">Username</label>
            <div class="mt-1 relative">
              <i class="pi pi-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
              <input
                v-model="username"
                type="text"
                placeholder="Masukkan username"
                class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
              />
            </div>
          </div>

          <!-- PASSWORD -->
          <div>
            <label class="text-sm font-medium text-gray-600">Password</label>
            <div class="mt-1 relative">
              <i class="pi pi-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
              <input
                v-model="password"
                type="password"
                placeholder="Masukkan password"
                class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
              />
            </div>
          </div>

          <!-- BUTTON -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full h-12 rounded-xl font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-lg transition disabled:opacity-60 disabled:cursor-not-allowed"
          >
            <span v-if="!loading">Masuk</span>
            <span v-else class="flex items-center justify-center gap-2">
              <i class="pi pi-spin pi-spinner"></i>
              Memproses...
            </span>
          </button>

          <div
            v-if="showForgot"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
          >
            <div
              class="w-full max-w-sm bg-white rounded-2xl shadow-2xl p-6 relative animate-fade-in"
            >

              <button
                @click="showForgot = false"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
              >
                <i class="pi pi-times"></i>
              </button>

              <div class="text-center mb-6">
                <div
                  class="mx-auto mb-3 w-14 h-14 flex items-center justify-center rounded-xl bg-indigo-100 text-indigo-600"
                >
                  <i class="pi pi-key text-2xl"></i>
                </div>

                <h2 class="text-lg font-semibold text-gray-800">
                  <!-- {{ step === 1 ? 'Lupa Password' : 'Buat Password Baru' }} -->
                  {{ step === 1 ? 'Lupa Password' : step === 2 ? 'Verifikasi Kode' : 'Buat Password Baru' }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                  {{ step === 1
                    ? 'Masukkan username untuk kirim OTP'
                    : step === 2
                      ? 'Masukkan kode dari email'
                      : 'Masukkan password baru Anda' }}
                </p>
              </div>

              <div
                v-if="fpError"
                class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl"
              >
                <i class="pi pi-exclamation-circle"></i>
                <span>{{ fpError }}</span>
              </div>

              <!-- STEP 1 -->
              <div v-if="step === 1" class="space-y-4">
                <div class="relative">
                  <i class="pi pi-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                  <input
                    v-model="fpUsername"
                    type="text"
                    placeholder="Username"
                    class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>

                <button
                  @click="checkUsername"
                  :disabled="fpLoading"
                  class="w-full h-12 rounded-xl font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow disabled:opacity-60"
                >
                  {{ fpLoading ? 'Mengirim OTP...' : 'Kirim Kode' }}
                </button>
              </div>

              <!-- STEP 2 -->
              <div v-else-if="step === 2" class="space-y-4">
                <div class="relative">
                  <i class="pi pi-shield absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                  <input
                    v-model="otpCode"
                    placeholder="Kode OTP"
                    class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>

                <button
                  @click="verifyOtp"
                  :disabled="fpLoading"
                  class="w-full h-12 rounded-xl font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow disabled:opacity-60"
                >
                  {{ fpLoading ? 'Memverifikasi...' : 'Verifikasi' }}
                </button>
              </div>

              <!-- STEP 3 -->
              <div v-else class="space-y-4">
                <div class="relative">
                  <i class="pi pi-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                  <input
                    v-model="newPassword"
                    type="password"
                    placeholder="Password baru"
                    class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>

                <div class="relative">
                  <i class="pi pi-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                  <input
                    v-model="confirmPassword"
                    type="password"
                    placeholder="Konfirmasi password"
                    class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>

                <button
                  @click="resetPasswordOtp"
                  :disabled="fpLoading"
                  class="w-full h-12 rounded-xl font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow disabled:opacity-60"
                >
                  {{ fpLoading ? 'Menyimpan...' : 'Simpan Password' }}
                </button>
              </div>

              <button
                class="mt-5 text-sm text-gray-500 w-full hover:underline"
                @click="showForgot = false"
              >
                Batal</button>


            </div>
          </div>


        </form>

        <div class="mt-4 text-center">
          <button
            type="button"
            @click="showForgot = true"
            class="text-sm text-indigo-600 hover:underline"
          >
            Lupa Password?
          </button>
        </div>


        <!-- FOOTER -->
        <p class="text-center text-xs text-gray-400 mt-8">
          © {{ new Date().getFullYear() }} Klinik Abqary 
        </p>

      </div>
    </div>
  </div>
</template>

<style scoped>
/* Full Tailwind – no custom CSS needed */
</style>
