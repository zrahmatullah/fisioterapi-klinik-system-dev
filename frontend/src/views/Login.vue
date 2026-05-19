<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '../axios'

const router = useRouter()
const toast = useToast()

// LOGIN
const username = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

// FULLSCREEN LOADING
const pageLoading = ref(false)

// FORGOT PASSWORD
const showForgot = ref(false)
const step = ref(1)

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

    // SHOW LOADING SCREEN
    pageLoading.value = true

    setTimeout(() => {

      if (user.role_id === 15) {
        router.push('/dashboard-orang-tua')
      }

      else if (user.role_id === 14) {
        router.push('/dashboard-terapis')
      }

      else {
        router.push('/dashboard')
      }

    }, 1800)

  } catch (err) {

    const msg = err.response?.data?.error || 'Login gagal'

    error.value = msg

    toast.error(msg)

  } finally {
    loading.value = false
  }
}

// CEK USERNAME
const checkUsername = async () => {

  fpError.value = ''
  fpLoading.value = true

  try {

    const res = await api.post('/forgot-password/check-username', {
      username: fpUsername.value
    })

    fpEmail.value = res.data.email

    await api.post('/otp-email/reset/send', {
      email: fpEmail.value
    })

    step.value = 2

    toast.success('Kode OTP dikirim ke email')

  } catch (err) {

    fpError.value =
      err.response?.data?.error || 'Email tidak ditemukan'

  } finally {

    fpLoading.value = false
  }
}

// VERIFY OTP
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

    fpError.value =
      err.response?.data?.message || 'OTP salah'

  } finally {

    fpLoading.value = false
  }
}

// RESET PASSWORD
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

    fpError.value =
      err.response?.data?.message || 'Gagal reset password'

  } finally {

    fpLoading.value = false
  }
}
</script>

<template>

  <div
    class="
      min-h-screen
      flex
      items-center
      justify-center
      bg-gradient-to-br
      from-indigo-700
      via-purple-700
      to-fuchsia-600
      px-4
      overflow-hidden
      relative
    "
  >

    <!-- BG BLUR -->
    <div
      class="
        absolute
        top-[-100px]
        right-[-100px]
        w-96 h-96
        bg-white/10
        rounded-full
        blur-3xl
      "
    ></div>

    <div
      class="
        absolute
        bottom-[-120px]
        left-[-120px]
        w-96 h-96
        bg-pink-300/20
        rounded-full
        blur-3xl
      "
    ></div>

    <!-- CARD -->
    <div
      class="
        relative
        w-full
        max-w-md
        bg-white/95
        backdrop-blur-xl
        rounded-[32px]
        shadow-2xl
        border border-white/30
        p-8
      "
    >

      <!-- LOGO -->
      <div class="flex justify-center mb-6">

        <div
          class="
            w-28 h-28
            rounded-3xl
            bg-gradient-to-br
            from-indigo-50
            to-purple-50
            shadow-inner
            flex items-center justify-center
          "
        >
          <img
            src="/logo-abqary.png"
            alt="Logo"
            class="w-20 h-20 object-contain"
          />
        </div>

      </div>

      <!-- TITLE -->
      <div class="text-center mb-8">

        <h1 class="text-3xl font-bold text-slate-800">
          KLINIK ABQARY
        </h1>

        <p class="text-slate-500 mt-2 text-sm">
          Sistem Informasi Tumbuh Kembang Anak
        </p>

      </div>

      <!-- ERROR -->
      <div
        v-if="error"
        class="
          mb-5
          rounded-2xl
          border border-red-200
          bg-red-50
          px-4 py-3
          text-sm
          text-red-600
          flex items-center gap-2
        "
      >
        <i class="pi pi-exclamation-circle"></i>
        {{ error }}
      </div>

      <!-- FORM -->
      <form
        @submit.prevent="login"
        class="space-y-5"
      >

        <!-- USERNAME -->
        <div>

          <label class="text-sm font-semibold text-slate-600">
            Username
          </label>

          <div class="relative mt-2">

            <i
              class="
                pi pi-user
                absolute
                left-4
                top-1/2
                -translate-y-1/2
                text-slate-400
              "
            ></i>

            <input
              v-model="username"
              type="text"
              placeholder="Masukkan username"
              class="
                w-full
                h-14
                rounded-2xl
                border border-slate-200
                bg-slate-50
                pl-12 pr-4
                text-slate-700
                outline-none
                transition-all
                focus:bg-white
                focus:ring-4
                focus:ring-indigo-100
                focus:border-indigo-500
              "
            />

          </div>

        </div>

        <!-- PASSWORD -->
        <div>

          <label class="text-sm font-semibold text-slate-600">
            Password
          </label>

          <div class="relative mt-2">

            <i
              class="
                pi pi-lock
                absolute
                left-4
                top-1/2
                -translate-y-1/2
                text-slate-400
              "
            ></i>

            <input
              v-model="password"
              type="password"
              placeholder="Masukkan password"
              class="
                w-full
                h-14
                rounded-2xl
                border border-slate-200
                bg-slate-50
                pl-12 pr-4
                text-slate-700
                outline-none
                transition-all
                focus:bg-white
                focus:ring-4
                focus:ring-indigo-100
                focus:border-indigo-500
              "
            />

          </div>

        </div>

        <!-- BUTTON -->
        <button
          type="submit"
          :disabled="loading"
          class="
            w-full
            h-14
            rounded-2xl
            bg-gradient-to-r
            from-indigo-600
            to-purple-600
            text-white
            font-semibold
            shadow-xl
            shadow-indigo-500/30
            hover:scale-[1.01]
            hover:shadow-2xl
            active:scale-[0.99]
            transition-all
            disabled:opacity-60
            disabled:cursor-not-allowed
          "
        >

          <span
            v-if="!loading"
            class="flex items-center justify-center gap-2"
          >
            <i class="pi pi-sign-in"></i>
            Masuk
          </span>

          <span
            v-else
            class="flex items-center justify-center gap-2"
          >
            <i class="pi pi-spin pi-spinner"></i>
            Memproses...
          </span>

        </button>

      </form>

      <!-- FORGOT -->
      <div class="mt-5 text-center">

        <button
          type="button"
          @click="showForgot = true"
          class="
            text-sm
            font-medium
            text-indigo-600
            hover:text-indigo-700
            hover:underline
          "
        >
          Lupa Password?
        </button>

      </div>

      <!-- FOOTER -->
      <div class="mt-8 text-center">

        <p class="text-xs text-slate-400">
          © {{ new Date().getFullYear() }} Klinik Abqary
        </p>

      </div>

    </div>

    <!-- FULLSCREEN LOADING -->
    <transition name="fade">

      <div
        v-if="pageLoading"
        class="
          fixed inset-0 z-[9999]
          bg-gradient-to-br
          from-indigo-700
          via-purple-700
          to-fuchsia-600
          flex items-center justify-center
        "
      >

        <div
          class="
            absolute
            w-96 h-96
            bg-white/10
            rounded-full
            blur-3xl
            animate-pulse
          "
        ></div>

        <div
          class="
            relative
            bg-white/10
            backdrop-blur-xl
            border border-white/20
            rounded-[32px]
            px-12 py-10
            shadow-2xl
            flex flex-col items-center
          "
        >

          <!-- LOGO -->
          <div
            class="
              w-28 h-28
              rounded-3xl
              bg-white
              flex items-center justify-center
              shadow-2xl
              animate-bounce
            "
          >

            <img
              src="/logo-abqary.png"
              class="w-16 h-16 object-contain"
            />

          </div>

          <h2 class="mt-6 text-2xl font-bold text-white">
            Memuat Dashboard
          </h2>

          <p class="mt-2 text-white/70 text-sm">
            Mohon tunggu sebentar...
          </p>

          <!-- BAR -->
          <div
            class="
              mt-6
              w-64
              h-2
              bg-white/20
              rounded-full
              overflow-hidden
            "
          >

            <div
              class="
                h-full
                bg-white
                rounded-full
                animate-loading-bar
              "
            ></div>

          </div>

          <i
            class="
              pi pi-spin pi-spinner
              text-3xl
              text-white
              mt-6
            "
          ></i>

        </div>

      </div>

    </transition>

  </div>

</template>

<style scoped>

.fade-enter-active,
.fade-leave-active {
  transition: opacity .4s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes loadingBar {

  0% {
    width: 0%;
  }

  50% {
    width: 70%;
  }

  100% {
    width: 100%;
  }
}

.animate-loading-bar {
  animation: loadingBar 1.8s ease forwards;
}

</style>