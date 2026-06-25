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
const showPassword = ref(false)

// FULLSCREEN LOADING
const pageLoading = ref(false)

// FORGOT PASSWORD
const showForgot = ref(false)
const step = ref(1)

const fpUsername = ref('')
const newPassword = ref('')
const confirmPassword = ref('')
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

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

    pageLoading.value = true

    setTimeout(() => {
      if (user.role_id === 15) {
        router.push('/dashboard-orang-tua')
      } else if (user.role_id === 14) {
        router.push('/dashboard-terapis')
      } else {
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

// CHECK USERNAME
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

    fpUsername.value = ''
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

const closeForgot = () => {
  showForgot.value = false
  step.value = 1
  fpError.value = ''
}
</script>

<template>

  <div class="auth-screen">

    <!-- LEFT PANEL — BRAND / GROWTH MOTIF -->
    <div class="auth-side">

      <div class="auth-side-content">

        <div class="brand-mark">
          <div class="brand-logo">
            <img src="/logo-abqary.png" alt="Logo Klinik Abqary" />
          </div>
          <span class="brand-name">E-Clinic System Dev</span>
        </div>

        <div class="side-copy">
          <h1>Mendampingi setiap<br />langkah tumbuh kembang.</h1>
          <p>Sistem informasi terpadu untuk memantau perkembangan anak bersama tim terapis dan orang tua.</p>
        </div>

        <!-- GROWTH CURVE SIGNATURE -->
        <svg class="growth-curve" viewBox="0 0 480 220" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <line x1="0" y1="190" x2="480" y2="190" stroke="rgba(255,255,255,0.18)" stroke-width="1" />
          <line x1="40" y1="0" x2="40" y2="190" stroke="rgba(255,255,255,0.18)" stroke-width="1" />

          <path
            d="M 40 175 C 110 168, 140 130, 190 110 C 250 86, 280 60, 340 42 C 380 30, 410 22, 460 15"
            stroke="rgba(255,255,255,0.85)"
            stroke-width="3"
            stroke-linecap="round"
          />

          <circle cx="40" cy="175" r="5" fill="#C7D2FE" />
          <circle cx="190" cy="110" r="5" fill="#C7D2FE" />
          <circle cx="340" cy="42" r="5" fill="#C7D2FE" />
          <circle cx="460" cy="15" r="6" fill="#FFFFFF" />

          <text x="40" y="208" fill="rgba(255,255,255,0.55)" font-size="12" font-family="inherit">Lahir</text>
          <text x="170" y="208" fill="rgba(255,255,255,0.55)" font-size="12" font-family="inherit">Terapi rutin</text>
          <text x="400" y="208" fill="rgba(255,255,255,0.55)" font-size="12" font-family="inherit">Mandiri</text>
        </svg>

      </div>

    </div>

    <!-- RIGHT PANEL — FORM -->
    <div class="auth-main">

      <div class="auth-card">

        <div class="mobile-brand">
          <div class="brand-logo brand-logo--sm">
            <img src="/logo-abqary.png" alt="Logo Klinik Abqary" />
          </div>
          <span class="brand-name brand-name--dark">Klinik Abqary</span>
        </div>

        <div class="form-heading">
          <h2>Masuk ke akun Anda</h2>
          <p>Gunakan username dan password yang telah didaftarkan.</p>
        </div>

        <div v-if="error" class="alert alert--error" role="alert">
          <i class="pi pi-exclamation-circle"></i>
          <span>{{ error }}</span>
        </div>

        <form @submit.prevent="login" class="form" novalidate>

          <div class="field">
            <label for="username">Username</label>
            <div class="input-shell">
              <i class="pi pi-user input-icon"></i>
              <input
                id="username"
                v-model="username"
                type="text"
                placeholder="Masukkan username"
                autocomplete="username"
              />
            </div>
          </div>

          <div class="field">
            <label for="password">Password</label>
            <div class="input-shell">
              <i class="pi pi-lock input-icon"></i>
              <input
                id="password"
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Masukkan password"
                autocomplete="current-password"
              />
              <button
                type="button"
                class="input-action"
                @click="showPassword = !showPassword"
                :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
              >
                <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn-primary" :disabled="loading">
            <span v-if="!loading" class="btn-content">
              <i class="pi pi-sign-in"></i>
              Masuk
            </span>
            <span v-else class="btn-content">
              <i class="pi pi-spin pi-spinner"></i>
              Memproses...
            </span>
          </button>

        </form>

        <div class="forgot-link">
          <button type="button" @click="showForgot = true">
            Lupa password?
          </button>
        </div>

        <p class="footer-note">© {{ new Date().getFullYear() }} Klinik Abqary &middot; Sistem Informasi Tumbuh Kembang Anak</p>

      </div>

    </div>

    <!-- FULLSCREEN LOADING ON LOGIN SUCCESS -->
    <transition name="fade">
      <div v-if="pageLoading" class="page-loading">
        <div class="page-loading-bar-track">
          <div class="page-loading-bar"></div>
        </div>
        <p>Menyiapkan dashboard Anda...</p>
      </div>
    </transition>

    <!-- FORGOT PASSWORD MODAL -->
    <transition name="fade">

      <div
        v-if="showForgot"
        class="modal-backdrop"
        @click.self="closeForgot"
      >

        <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="reset-title">

          <button class="modal-close" @click="closeForgot" aria-label="Tutup">
            <i class="pi pi-times"></i>
          </button>

          <div class="modal-heading">
            <div class="step-dots" aria-hidden="true">
              <span :class="['dot', { 'dot--active': step >= 1 }]"></span>
              <span :class="['dot', { 'dot--active': step >= 2 }]"></span>
              <span :class="['dot', { 'dot--active': step >= 3 }]"></span>
            </div>
            <h2 id="reset-title">Reset Password</h2>
            <p v-if="step === 1">Masukkan username untuk menerima kode verifikasi.</p>
            <p v-else-if="step === 2">Masukkan kode OTP yang dikirim ke {{ fpEmail }}.</p>
            <p v-else>Buat password baru untuk akun Anda.</p>
          </div>

          <div v-if="fpError" class="alert alert--error">
            {{ fpError }}
          </div>

          <!-- STEP 1 -->
          <div v-if="step === 1" class="form">

            <div class="field">
              <label for="fp-username">Username</label>
              <div class="input-shell">
                <i class="pi pi-user input-icon"></i>
                <input
                  id="fp-username"
                  v-model="fpUsername"
                  type="text"
                  placeholder="Masukkan username"
                  autocomplete="username"
                  @keyup.enter="checkUsername"
                />
              </div>
            </div>

            <button class="btn-primary" :disabled="fpLoading" @click="checkUsername">
              <span v-if="!fpLoading" class="btn-content">
                <i class="pi pi-send"></i>
                Kirim kode OTP
              </span>
              <span v-else class="btn-content">
                <i class="pi pi-spin pi-spinner"></i>
                Memproses...
              </span>
            </button>

          </div>

          <!-- STEP 2 -->
          <div v-if="step === 2" class="form">

            <div class="field">
              <label for="fp-otp">Kode OTP</label>
              <div class="input-shell">
                <i class="pi pi-shield input-icon"></i>
                <input
                  id="fp-otp"
                  v-model="otpCode"
                  type="text"
                  inputmode="numeric"
                  placeholder="Masukkan kode OTP"
                  @keyup.enter="verifyOtp"
                />
              </div>
            </div>

            <button class="btn-primary" :disabled="fpLoading" @click="verifyOtp">
              <span v-if="!fpLoading" class="btn-content">
                <i class="pi pi-check-circle"></i>
                Verifikasi OTP
              </span>
              <span v-else class="btn-content">
                <i class="pi pi-spin pi-spinner"></i>
                Memproses...
              </span>
            </button>

          </div>

          <!-- STEP 3 -->
          <div v-if="step === 3" class="form">

            <div class="field">
              <label for="fp-new-password">Password baru</label>
              <div class="input-shell">
                <i class="pi pi-lock input-icon"></i>
                <input
                  id="fp-new-password"
                  v-model="newPassword"
                  :type="showNewPassword ? 'text' : 'password'"
                  placeholder="Masukkan password baru"
                  autocomplete="new-password"
                />
                <button
                  type="button"
                  class="input-action"
                  @click="showNewPassword = !showNewPassword"
                  :aria-label="showNewPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                >
                  <i :class="showNewPassword ? 'pi pi-eye-slash' : 'pi pi-eye'"></i>
                </button>
              </div>
            </div>

            <div class="field">
              <label for="fp-confirm-password">Konfirmasi password</label>
              <div class="input-shell">
                <i class="pi pi-lock input-icon"></i>
                <input
                  id="fp-confirm-password"
                  v-model="confirmPassword"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  placeholder="Konfirmasi password baru"
                  autocomplete="new-password"
                  @keyup.enter="resetPasswordOtp"
                />
                <button
                  type="button"
                  class="input-action"
                  @click="showConfirmPassword = !showConfirmPassword"
                  :aria-label="showConfirmPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                >
                  <i :class="showConfirmPassword ? 'pi pi-eye-slash' : 'pi pi-eye'"></i>
                </button>
              </div>
            </div>

            <button class="btn-primary" :disabled="fpLoading" @click="resetPasswordOtp">
              <span v-if="!fpLoading" class="btn-content">
                <i class="pi pi-lock"></i>
                Reset password
              </span>
              <span v-else class="btn-content">
                <i class="pi pi-spin pi-spinner"></i>
                Memproses...
              </span>
            </button>

          </div>

        </div>

      </div>

    </transition>

  </div>

</template>

<style scoped>

/* ---------- TOKENS ---------- */
.auth-screen {
  --ink: #16231F;
  --ink-soft: #5B6B66;
  --paper: #FFFFFF;
  --paper-soft: #F4F7F6;
  --line: #E1E8E6;
  --teal: #6366F1;
  --teal-dark: #4338CA;
  --teal-tint: #EEF0FF;
  --amber: #818CF8;
  --danger: #B3261E;
  --danger-tint: #FBEAE9;
  --radius: 16px;
  --shadow: 0 2px 4px rgba(22, 35, 31, 0.04), 0 12px 32px rgba(22, 35, 31, 0.08);

  min-height: 100vh;
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
  background: var(--paper-soft);
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  color: var(--ink);
}

@media (max-width: 900px) {
  .auth-screen {
    grid-template-columns: 1fr;
  }
}

/* ---------- LEFT PANEL ---------- */
.auth-side {
  background: var(--teal-dark);
  position: relative;
  display: flex;
  align-items: center;
  padding: 64px;
  overflow: hidden;
}

@media (max-width: 900px) {
  .auth-side {
    display: none;
  }
}

.auth-side::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: radial-gradient(circle at 1.5px 1.5px, rgba(255,255,255,0.10) 1.5px, transparent 0);
  background-size: 28px 28px;
  opacity: 0.5;
}

.auth-side-content {
  position: relative;
  z-index: 1;
  max-width: 460px;
}

.brand-mark {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 64px;
}

.brand-logo {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.12);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.brand-logo img {
  width: 30px;
  height: 30px;
  object-fit: contain;
}

.brand-logo--sm {
  width: 38px;
  height: 38px;
  border-radius: 10px;
}

.brand-logo--sm img {
  width: 24px;
  height: 24px;
}

.brand-name {
  font-size: 16px;
  font-weight: 600;
  letter-spacing: 0.01em;
  color: #FFFFFF;
}

.brand-name--dark {
  color: var(--ink);
}

.side-copy h1 {
  font-size: 34px;
  line-height: 1.25;
  font-weight: 600;
  color: #FFFFFF;
  margin: 0 0 16px;
  letter-spacing: -0.01em;
}

.side-copy p {
  font-size: 15px;
  line-height: 1.6;
  color: rgba(255, 255, 255, 0.72);
  margin: 0 0 56px;
  max-width: 380px;
}

.growth-curve {
  width: 100%;
  height: auto;
}

.growth-curve text {
  font-family: 'Inter', system-ui, sans-serif;
}

/* ---------- RIGHT PANEL ---------- */
.auth-main {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 32px 24px;
}

.auth-card {
  width: 100%;
  max-width: 400px;
}

.mobile-brand {
  display: none;
  align-items: center;
  gap: 12px;
  margin-bottom: 40px;
}

@media (max-width: 900px) {
  .mobile-brand {
    display: flex;
  }
}

.form-heading {
  margin-bottom: 28px;
}

.form-heading h2 {
  font-size: 24px;
  font-weight: 600;
  color: var(--ink);
  margin: 0 0 6px;
  letter-spacing: -0.01em;
}

.form-heading p {
  font-size: 14px;
  color: var(--ink-soft);
  margin: 0;
}

/* ---------- ALERT ---------- */
.alert {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  border-radius: 12px;
  padding: 12px 14px;
  font-size: 13.5px;
  line-height: 1.5;
  margin-bottom: 20px;
}

.alert--error {
  background: var(--danger-tint);
  color: var(--danger);
  border: 1px solid rgba(179, 38, 30, 0.18);
}

.alert--error i {
  margin-top: 1px;
}

/* ---------- FORM ---------- */
.form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 7px;
}

.field label {
  font-size: 13px;
  font-weight: 600;
  color: var(--ink);
}

.input-shell {
  position: relative;
  display: flex;
  align-items: center;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  background: var(--paper);
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.input-shell:focus-within {
  border-color: var(--teal);
  box-shadow: 0 0 0 3px var(--teal-tint);
}

.input-icon {
  position: absolute;
  left: 14px;
  color: var(--ink-soft);
  font-size: 14px;
  pointer-events: none;
}

.input-shell input {
  flex: 1;
  height: 48px;
  border: none;
  outline: none;
  background: transparent;
  padding: 0 14px 0 40px;
  font-size: 14.5px;
  color: var(--ink);
  font-family: inherit;
}

.input-shell input::placeholder {
  color: #9AA8A4;
}

.input-action {
  border: none;
  background: transparent;
  color: var(--ink-soft);
  padding: 0 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  font-size: 14px;
}

.input-action:hover {
  color: var(--teal);
}

/* ---------- BUTTON ---------- */
.btn-primary {
  height: 48px;
  border: none;
  border-radius: 12px;
  background: var(--teal);
  color: #FFFFFF;
  font-size: 14.5px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s ease, transform 0.1s ease;
  width: 100%;
  margin-top: 4px;
}

.btn-primary:hover:not(:disabled) {
  background: var(--teal-dark);
}

.btn-primary:active:not(:disabled) {
  transform: scale(0.99);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

/* ---------- FORGOT LINK / FOOTER ---------- */
.forgot-link {
  text-align: center;
  margin-top: 18px;
}

.forgot-link button {
  border: none;
  background: none;
  font-size: 13.5px;
  font-weight: 600;
  color: var(--teal);
  cursor: pointer;
}

.forgot-link button:hover {
  color: var(--teal-dark);
  text-decoration: underline;
}

.footer-note {
  text-align: center;
  font-size: 12px;
  color: #9AA8A4;
  margin-top: 40px;
}

/* ---------- FULLSCREEN LOADING ---------- */
.page-loading {
  position: fixed;
  inset: 0;
  z-index: 100000;
  background: var(--paper);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 18px;
}

.page-loading p {
  font-size: 14px;
  color: var(--ink-soft);
}

.page-loading-bar-track {
  width: 220px;
  height: 4px;
  border-radius: 999px;
  background: var(--line);
  overflow: hidden;
}

.page-loading-bar {
  height: 100%;
  width: 0%;
  background: var(--teal);
  border-radius: 999px;
  animation: loadingBar 1.8s ease forwards;
}

/* ---------- MODAL ---------- */
.modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 99999;
  background: rgba(22, 35, 31, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}

.modal-card {
  position: relative;
  width: 100%;
  max-width: 420px;
  background: var(--paper);
  border-radius: 20px;
  box-shadow: var(--shadow);
  padding: 32px;
}

.modal-close {
  position: absolute;
  top: 18px;
  right: 18px;
  border: none;
  background: var(--paper-soft);
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--ink-soft);
  cursor: pointer;
}

.modal-close:hover {
  background: var(--line);
  color: var(--ink);
}

.modal-heading {
  margin-bottom: 24px;
}

.modal-heading h2 {
  font-size: 20px;
  font-weight: 600;
  margin: 4px 0 6px;
}

.modal-heading p {
  font-size: 13.5px;
  color: var(--ink-soft);
  margin: 0;
}

.step-dots {
  display: flex;
  gap: 6px;
  margin-bottom: 14px;
}

.dot {
  width: 22px;
  height: 4px;
  border-radius: 999px;
  background: var(--line);
}

.dot--active {
  background: var(--teal);
}

/* ---------- TRANSITIONS ---------- */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes loadingBar {
  0% { width: 0%; }
  50% { width: 70%; }
  100% { width: 100%; }
}

</style>