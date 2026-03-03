<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-8">

      <h2 class="text-2xl font-bold text-center text-indigo-600 mb-6">
        Registrasi Akun Orang Tua
      </h2>

      <form class="space-y-4" @submit.prevent="save">

        <!-- DATA DIRI -->
        <div>
          <label class="label">Nama Lengkap</label>
          <input
            v-model="form.nama"
            type="text"
            class="input"
            placeholder="Nama lengkap"
            required
          />
        </div>

        <div>
          <label class="label">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="input"
            placeholder="Email aktif"
            required
          />
        </div>

        <div>
          <label class="label">No. HP</label>
          <input
            v-model="form.no_telepon"
            type="text"
            class="input"
            placeholder="08xxxxxxxxxx"
            required
          />
        </div>

        <!-- OTP EMAIL -->
        <div class="border rounded-lg p-4 bg-gray-50">
          <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-semibold">Verifikasi Email</span>

            <span
              v-if="emailVerified"
              class="text-green-600 text-sm font-medium"
            >
              ✔ Terverifikasi
            </span>
            <span
              v-else
              class="text-red-500 text-sm font-medium"
            >
              Belum diverifikasi
            </span>
          </div>

          <div v-if="!emailVerified" class="space-y-2">
            <button
              type="button"
              class="btn-secondary w-full"
              @click="sendOtp"
              :disabled="!form.email"
            >
              Kirim OTP ke Email
            </button>

            <input
              v-model="otpCode"
              type="text"
              class="input"
              placeholder="Masukkan kode OTP"
            />

            <button
              type="button"
              class="btn-primary w-full"
              @click="verifyOtp"
              :disabled="!otpCode"
            >
              Verifikasi OTP
            </button>
          </div>
        </div>

        <hr class="my-4" />

        <!-- AKUN -->
        <div>
          <label class="label">Username</label>
          <input
            v-model="form.username"
            type="text"
            class="input"
            placeholder="Username"
            required
          />
        </div>

        <div>
          <label class="label">Password</label>
          <input
            v-model="form.password"
            type="password"
            class="input"
            placeholder="Password"
            required
          />
        </div>

        <button
          type="submit"
          class="w-full bg-indigo-600 text-white py-2 rounded-lg
                 font-semibold hover:bg-indigo-700 transition
                 disabled:opacity-50"
          :disabled="loading || !emailVerified"
        >
          {{ loading ? 'Menyimpan...' : 'Daftar' }}
        </button>
      </form>

      <p class="text-sm text-center mt-5 text-gray-600">
        Sudah punya akun?
        <span
          class="text-indigo-600 font-semibold cursor-pointer hover:underline"
          @click="$router.push('/login')"
        >
          Login
        </span>
      </p>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'          // 🔥 sama dengan master-user
import { useToast } from 'vue-toastification'

const router = useRouter()
const toast = useToast()

/* ================= STATE ================= */
const formDefault = {
  nama: '',
  email: '',
  no_telepon: '',
  username: '',
  password: '',
  role_id: 3 // 🔥 ROLE ORANG TUA (WAJIB samakan dengan DB)
}

const form = ref({ ...formDefault })
const otpCode = ref('')
const emailVerified = ref(false)
const loading = ref(false)

/* ================= OTP ================= */
const sendOtp = async () => {
  try {
    await api.post('/otp-email/send', { email: form.value.email })
    toast.success('OTP dikirim ke email')
  } catch {
    toast.error('Gagal mengirim OTP')
  }
}

const verifyOtp = async () => {
  try {
    await api.post('/otp-email/verify', {
      email: form.value.email,
      kode_otp: otpCode.value
    })
    emailVerified.value = true
    toast.success('Email berhasil diverifikasi')
  } catch {
    toast.error('OTP tidak valid')
  }
}

/* ================= SAVE (SAMA DENGAN ADMIN) ================= */
const save = async () => {
  if (!emailVerified.value) {
    toast.error('Email belum diverifikasi')
    return
  }

  try {
    loading.value = true

    // 🔥 API SAMA DENGAN MASTER USER
    await api.post('/master-user/public', form.value)

    toast.success('Registrasi berhasil, silakan login')
    router.push('/login')
  } catch {
    toast.error('Registrasi gagal')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 6px;
  color: #374151;
}

.input {
  width: 100%;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #d1d5db;
  outline: none;
}
.input:focus {
  border-color: #6366f1;
}

.btn-primary {
  background-color: #6366f1;
  color: white;
  padding: 10px;
  border-radius: 8px;
  font-weight: 600;
}

.btn-secondary {
  background-color: #e5e7eb;
  color: #374151;
  padding: 10px;
  border-radius: 8px;
  font-weight: 600;
}
</style>
