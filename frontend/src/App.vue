<template>

  <!-- =========================
       GLOBAL LOADING — TOP PROGRESS BAR
  ========================== -->
  <transition name="bar-fade">
    <div v-if="loading" class="route-progress fixed inset-x-0 top-0 z-[9999] h-[3px] overflow-hidden">
      <div class="route-progress-track"></div>
      <div class="route-progress-glow"></div>
    </div>
  </transition>

  <!-- PAGE -->
  <router-view />

  <!-- =========================
       FLOATING VOICE AI BUTTON
       Persisten di semua halaman (Pojok Kanan Bawah, Non-Overlapping)
  ========================== -->
  <button
    @click="toggleListening"
    class="
      fixed bottom-4 right-4 z-[9000]
      w-10 h-10
      rounded-full
      flex items-center justify-center
      text-white
      transition-all duration-200
      hover:scale-105
      active:scale-95
      shadow-md
    "
    :class="isListening
      ? 'bg-red-500 shadow-[0_0_0_6px_rgba(239,68,68,0.25)] animate-pulse'
      : 'bg-[#0F766E] hover:bg-[#115E59] shadow-[0_4px_12px_rgba(15,118,110,0.3)]'"
    :aria-label="isListening ? 'Berhenti mendengarkan' : 'Mulai voice command'"
  >
    <!-- mic icon -->
    <svg
      v-if="!aiLoading"
      xmlns="http://www.w3.org/2000/svg"
      class="w-4 h-4"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      stroke-width="2"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"
      />
    </svg>

    <!-- spinner saat AI memproses hasil ucapan -->
    <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
    </svg>
  </button>

  <!-- =========================
       OVERLAY STATUS MENDENGARKAN
  ========================== -->
  <transition name="loader">
    <div
      v-if="isListening || transcriptPreview"
      class="fixed bottom-16 right-4 z-[9000] max-w-xs"
    >
      <div class="rounded-xl border border-white/20 bg-white/95 backdrop-blur-xl shadow-lg px-3 py-2 text-xs">
        <div class="flex items-center gap-2 mb-0.5">
          <span v-if="isListening" class="flex h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
          <p class="text-[11px] font-medium text-slate-500">
            {{ isListening ? 'Mendengarkan...' : 'Perintah dikenali' }}
          </p>
        </div>
        <p class="text-[12px] text-slate-800">
          {{ transcriptPreview || 'Silakan bicara...' }}
        </p>
      </div>
    </div>
  </transition>

</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import axios from 'axios'

const router = useRouter()
const toast = useToast()

const loading = ref(false)
const isListening = ref(false)
const aiLoading = ref(false)
const transcriptPreview = ref('')

let recognition = null
let audioCtx = null // Web Audio context, dibuat sekali saat dibutuhkan

onMounted(() => {
  router.beforeEach((to, from, next) => {
    loading.value = true
    next()
  })

  router.afterEach(() => {
    setTimeout(() => {
      loading.value = false
    }, 500)
  })

  setupSpeechRecognition()
})

/* =========================
   AUDIO FEEDBACK (beep)
   Web Audio API — tanpa file eksternal
========================== */
function getAudioContext() {
  // browser butuh AudioContext dibuat/di-resume setelah user gesture (klik tombol)
  if (!audioCtx) {
    audioCtx = new (window.AudioContext || window.webkitAudioContext)()
  }
  if (audioCtx.state === 'suspended') {
    audioCtx.resume()
  }
  return audioCtx
}

function playTone(frequency, duration = 120, type = 'sine', volume = 0.15) {
  const ctx = getAudioContext()
  const oscillator = ctx.createOscillator()
  const gainNode = ctx.createGain()

  oscillator.type = type
  oscillator.frequency.value = frequency

  // fade in-out singkat supaya tidak ada "klik" kasar di awal/akhir bunyi
  gainNode.gain.setValueAtTime(0, ctx.currentTime)
  gainNode.gain.linearRampToValueAtTime(volume, ctx.currentTime + 0.01)
  gainNode.gain.linearRampToValueAtTime(0, ctx.currentTime + duration / 1000)

  oscillator.connect(gainNode)
  gainNode.connect(ctx.destination)

  oscillator.start()
  oscillator.stop(ctx.currentTime + duration / 1000)
}

// bunyi "mulai mendengarkan" — nada naik, ceria, mengundang bicara
function playStartChime() {
  playTone(660, 90)
  setTimeout(() => playTone(880, 110), 90)
}

// bunyi "selesai mendengarkan / berhenti" — nada turun
function playStopChime() {
  playTone(880, 90)
  setTimeout(() => playTone(560, 110), 90)
}

// bunyi error / tidak dikenali — nada datar pendek
function playErrorChime() {
  playTone(220, 180, 'triangle', 0.12)
}

/* =========================
   SPEECH RECOGNITION
========================== */
function setupSpeechRecognition() {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition

  if (!SpeechRecognition) {
    console.warn('Browser tidak mendukung Web Speech API')
    return
  }

  recognition = new SpeechRecognition()
  recognition.lang = 'id-ID'
  recognition.continuous = false
  recognition.interimResults = true

  recognition.onstart = () => {
    isListening.value = true
    transcriptPreview.value = ''
  }

  recognition.onresult = (event) => {
    let text = ''
    for (let i = 0; i < event.results.length; i++) {
      text += event.results[i][0].transcript
    }
    transcriptPreview.value = text

    if (event.results[event.results.length - 1].isFinal) {
      submitVoiceCommand(text)
    }
  }

  recognition.onerror = (event) => {
    console.error('Speech recognition error:', event.error)
    isListening.value = false

    if (event.error === 'not-allowed') {
      toast.warning('Izin mikrofon ditolak. Aktifkan di setting browser.')
    } else if (event.error === 'no-speech') {
      transcriptPreview.value = ''
      playErrorChime()
    }
  }

  recognition.onend = () => {
    isListening.value = false
  }
}

function toggleListening() {
  if (!recognition) {
    toast.info('Browser Anda tidak mendukung voice command. Disarankan menggunakan Google Chrome.')
    return
  }

  if (isListening.value) {
    recognition.stop()
    playStopChime()
  } else {
    transcriptPreview.value = ''
    playStartChime()
    setTimeout(() => {
      recognition.start()
    }, 220)
  }
}

async function submitVoiceCommand(text) {
  if (!text.trim()) return

  aiLoading.value = true

  try {
    const { data } = await axios.post('/api/ai-command', {
      message: text,
    })

    executeCommand(data)

  } catch (e) {
    console.error('AI command failed:', e)
    playErrorChime()
    toast.error('Terjadi kesalahan pemrosesan perintah suara.')
  } finally {
    aiLoading.value = false
    setTimeout(() => {
      transcriptPreview.value = ''
    }, 2000)
  }
}

/* =========================
   EKSEKUSI HASIL AI
   Sudah disesuaikan dengan struktur sidebar nyata
========================== */
function executeCommand(result) {
  // backend sudah mengembalikan path langsung dan tervalidasi
  // berdasarkan role user yang login, jadi tinggal push
  if (result.menu && result.path) {
    router.push(result.path)
    return
  }

  if (result.action === 'search') {
    // mapping target pencarian ke halaman yang relevan
    // sesuaikan key di sini dengan target yang dikenali backend per role
    const searchRouteMap = {
      'anak': '/master-anak',
      'user': '/master-user',
      'terapis': '/master-terapis',
      'pembayaran': '/pembayaran-tagihan',
      'layanan': '/list-pendaftaran-layanan',
      'jadwal': '/jadwal-terapi',
      'evaluasi': '/evaluasi-terapi',
    }

    const base = searchRouteMap[result.target]
    if (base) {
      router.push({ path: base, query: { q: result.query } })
      return
    }
  }

  playErrorChime()
  toast.info('Maaf, perintah belum dikenali. Silakan coba kembali.')
}

onBeforeUnmount(() => {
  if (recognition) recognition.stop()
  if (audioCtx) audioCtx.close()
})
</script>

<style scoped>

/* =========================
   TOP PROGRESS BAR — route loading
========================== */
.route-progress {
  background: rgba(15, 118, 110, 0.12);
}

.route-progress-track {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    90deg,
    #0F766E 0%,
    #2DD4BF 50%,
    #0F766E 100%
  );
  transform-origin: left;
  animation: progressGrow 0.9s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

.route-progress-glow {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 80px;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.85),
    transparent
  );
  animation: progressSweep 1.1s ease-in-out infinite;
}

@keyframes progressGrow {
  0% {
    transform: scaleX(0);
  }
  60% {
    transform: scaleX(0.85);
  }
  100% {
    transform: scaleX(1);
    opacity: 0.4;
  }
}

@keyframes progressSweep {
  0% {
    left: -80px;
  }
  100% {
    left: 100%;
  }
}

.bar-fade-enter-active {
  transition: opacity 0.15s ease;
}
.bar-fade-leave-active {
  transition: opacity 0.3s ease;
}
.bar-fade-enter-from,
.bar-fade-leave-to {
  opacity: 0;
}

/* =========================
   Overlay status mendengarkan (sudah ada sebelumnya)
========================== */
.loader-enter-active,
.loader-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.loader-enter-from,
.loader-leave-to {
  opacity: 0;
  transform: translateY(6px);
}

</style>