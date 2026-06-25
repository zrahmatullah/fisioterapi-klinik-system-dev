<template>
  <div class="queue-page">
    <!-- HEADER -->
    <header class="queue-header">
      <div class="queue-header__brand">
        <div class="queue-header__mark">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 21s-7-4.35-9.5-8.8C.6 8.4 2.3 4.5 6 4.5c2 0 3.4 1.1 4 2.1.6-1 2-2.1 4-2.1 3.7 0 5.4 3.9 3.5 7.7C19 16.65 12 21 12 21z" fill="currentColor"/>
          </svg>
        </div>
        <div>
          <h1>Pemanggil Antrian</h1>
          <p>Antrian hari ini &bull; {{ todayLabel }}</p>
        </div>
      </div>

      <div class="queue-header__controls">
        <span class="live-dot" aria-hidden="true"></span>
        <label class="visually-hidden" for="ruangan-filter">Filter ruangan</label>
        <select id="ruangan-filter" v-model="ruanganId" @change="loadData" class="select-ruangan">
          <option value="">Semua ruangan</option>
          <option v-for="r in ruanganList" :key="r.id" :value="r.id">
            {{ r.ruangan }}
          </option>
        </select>
      </div>
    </header>

    <!-- DISPLAY PANGGILAN AKTIF -->
    <section class="now-serving" :class="{ 'now-serving--idle': !current }">
      <template v-if="current">
        <span class="now-serving__eyebrow">
          <span class="now-serving__pulse"></span>
          Sedang dipanggil
        </span>
        <div class="now-serving__number">{{ current.no_antrian }}</div>
        <div class="now-serving__meta">
          <span class="now-serving__name">{{ current.nama_anak }}</span>
          <span class="now-serving__sep">&middot;</span>
          <span class="now-serving__room">{{ current.ruangan }}</span>
        </div>
      </template>
      <template v-else>
        <span class="now-serving__eyebrow">Menunggu panggilan</span>
        <div class="now-serving__placeholder">&mdash;</div>
        <p class="now-serving__hint">Klik &ldquo;Panggil&rdquo; pada salah satu antrian di bawah untuk mulai</p>
      </template>
    </section>

    <!-- DAFTAR ANTRIAN -->
    <section class="queue-list">
      <div class="queue-list__head">
        <h2>Daftar antrian</h2>
        <span class="queue-count">{{ antrian.length }} menunggu</span>
      </div>

      <div v-if="antrian.length" class="queue-list__items">
        <article
          v-for="(a, i) in antrian"
          :key="a.id"
          class="queue-row"
          :class="{ 'queue-row--calling': current && current.id === a.id }"
        >
          <div class="queue-row__rank">{{ i + 1 }}</div>

          <div class="queue-row__ticket">{{ a.no_antrian }}</div>

          <div class="queue-row__info">
            <span class="queue-row__child">{{ a.profile_anak?.nama_anak }}</span>
            <span class="queue-row__detail">
              <i class="ti ti-door" aria-hidden="true"></i>{{ a.ruangan?.ruangan ?? '-' }}
              <span class="queue-row__divider" aria-hidden="true">&bull;</span>
              <i class="ti ti-stethoscope" aria-hidden="true"></i>{{ a.terapis?.nama ?? '-' }}
            </span>
          </div>

          <button
            class="call-button"
            type="button"
            :disabled="calling === a.id"
            @click="panggil(a)"
          >
            <i class="ti ti-volume" aria-hidden="true"></i>
            {{ calling === a.id ? 'Memanggil...' : 'Panggil' }}
          </button>
        </article>
      </div>

      <div v-else class="queue-empty">
        <i class="ti ti-clipboard-check" aria-hidden="true"></i>
        <p>Tidak ada antrian hari ini</p>
        <span>Antrian baru akan muncul di sini secara otomatis</span>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const antrian = ref([])
const ruanganList = ref([])
const ruanganId = ref('')
const current = ref(null)
const calling = ref(null)

const today = new Date().toISOString().slice(0, 10)
const todayLabel = new Date().toLocaleDateString('id-ID', {
  day: '2-digit',
  month: 'long',
  year: 'numeric'
})

const loadRuangan = async () => {
  const res = await api.get('/ruangan')
  ruanganList.value = res.data.data ?? res.data
}

const loadData = async () => {
  const res = await api.get('/registrasi-anak')

  antrian.value = res.data.filter(r =>
    ['belum_dilayani', 'dipanggil'].includes(r.status_pelayanan) &&
    r.tgl_regis?.slice(0, 10) === today &&
    (!ruanganId.value || r.ruangan_id == ruanganId.value)
  )
}

const panggil = async (row) => {
  calling.value = row.id
  try {
    const res = await api.put(`/registrasi-anak/${row.id}/panggil`)

    current.value = res.data.data

    toast.success(`Memanggil ${current.value.no_antrian}`)

    await loadData()
  } catch {
    toast.error('Gagal memanggil antrian')
  } finally {
    calling.value = null
  }
}

onMounted(async () => {
  await loadRuangan()
  await loadData()
})
</script>

<style scoped>
.queue-page {
  min-height: 100vh;
  padding: 1.5rem;
  background: #F5F6F7;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  font-family: 'Inter', system-ui, sans-serif;
}

.visually-hidden {
  position: absolute;
  width: 1px;
  height: 1px;
  overflow: hidden;
  clip: rect(0 0 0 0);
}

/* HEADER */
.queue-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  background: #ffffff;
  border: 1px solid #E2E4E8;
  border-radius: 20px;
  padding: 1.25rem 1.5rem;
}

.queue-header__brand {
  display: flex;
  align-items: center;
  gap: 0.9rem;
}

.queue-header__mark {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  background: #0F6E56;
  color: #E1F5EE;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.queue-header__mark svg { width: 22px; height: 22px; }

.queue-header h1 {
  font-size: 1.4rem;
  font-weight: 700;
  color: #1A1D23;
  margin: 0;
  letter-spacing: -0.01em;
}

.queue-header p {
  font-size: 0.85rem;
  color: #6B7280;
  margin: 0.15rem 0 0;
}

.queue-header__controls {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.live-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #639922;
  box-shadow: 0 0 0 3px rgba(99, 153, 34, 0.15);
}

.select-ruangan {
  appearance: none;
  border: 1px solid #E2E4E8;
  background: #F8F9FA url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%237C7A6E' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 0.85rem center;
  border-radius: 12px;
  padding: 0.6rem 2.4rem 0.6rem 1rem;
  font-size: 0.9rem;
  color: #1A1D23;
  min-width: 200px;
  cursor: pointer;
}
.select-ruangan:focus {
  outline: none;
  border-color: #0F6E56;
  box-shadow: 0 0 0 3px rgba(15, 110, 86, 0.12);
}

/* NOW SERVING DISPLAY */
.now-serving {
  position: relative;
  background: #0B4F3F;
  background-image: radial-gradient(circle at 18% 20%, rgba(255,255,255,0.07), transparent 45%);
  border-radius: 24px;
  padding: 2.75rem 2rem;
  text-align: center;
  color: #EAF6F1;
  overflow: hidden;
}

.now-serving--idle {
  background: #F8F9FA;
  border: 1px dashed #D5D8DC;
  color: #6B7280;
}

.now-serving__eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #9FE1CB;
}

.now-serving--idle .now-serving__eyebrow {
  color: #9CA3AF;
}

.now-serving__pulse {
  width: 9px;
  height: 9px;
  border-radius: 50%;
  background: #5DCAA5;
  animation: pulse 1.6s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(93, 202, 165, 0.55); }
  50% { box-shadow: 0 0 0 8px rgba(93, 202, 165, 0); }
}

.now-serving__number {
  font-size: clamp(3.5rem, 9vw, 6.5rem);
  font-weight: 800;
  letter-spacing: 0.04em;
  line-height: 1.05;
  margin: 0.6rem 0 0.4rem;
  font-family: 'JetBrains Mono', 'SF Mono', monospace;
}

.now-serving__placeholder {
  font-size: 4rem;
  font-weight: 700;
  margin: 0.6rem 0 0.4rem;
  color: #C7C4B5;
}

.now-serving__meta {
  font-size: 1.05rem;
  font-weight: 500;
}

.now-serving__sep { margin: 0 0.5rem; opacity: 0.6; }
.now-serving__room { color: #C8E9DB; font-weight: 400; }

.now-serving__hint {
  margin: 0.5rem 0 0;
  font-size: 0.9rem;
}

/* QUEUE LIST */
.queue-list {
  background: #ffffff;
  border: 1px solid #E2E4E8;
  border-radius: 20px;
  padding: 1.25rem 1.25rem 0.75rem;
}

.queue-list__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 0.25rem 1rem;
}

.queue-list__head h2 {
  font-size: 1.05rem;
  font-weight: 700;
  color: #1A1D23;
  margin: 0;
}

.queue-count {
  font-size: 0.8rem;
  font-weight: 600;
  color: #0F6E56;
  background: #E1F5EE;
  padding: 0.3rem 0.75rem;
  border-radius: 999px;
}

.queue-list__items {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  padding-bottom: 0.75rem;
}

.queue-row {
  display: grid;
  grid-template-columns: 32px auto 1fr auto;
  align-items: center;
  gap: 1rem;
  padding: 0.85rem 1rem;
  border-radius: 14px;
  border: 1px solid #ECEDEF;
  background: #FBFBFC;
  transition: border-color 0.15s ease, background 0.15s ease;
}

.queue-row:hover {
  background: #EEF0F2;
  border-color: #D0D3D8;
}

.queue-row--calling {
  border-color: #5DCAA5;
  background: #EFFAF5;
}

.queue-row__rank {
  font-size: 0.8rem;
  color: #9CA3AF;
  font-weight: 600;
  text-align: center;
}

.queue-row__ticket {
  font-family: 'JetBrains Mono', 'SF Mono', monospace;
  font-size: 1.15rem;
  font-weight: 700;
  color: #0F6E56;
  background: #E1F5EE;
  padding: 0.35rem 0.7rem;
  border-radius: 10px;
  min-width: 88px;
  text-align: center;
}

.queue-row__info {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
  min-width: 0;
}

.queue-row__child {
  font-size: 0.95rem;
  font-weight: 600;
  color: #1A1D23;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.queue-row__detail {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.82rem;
  color: #6B7280;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.queue-row__detail i { font-size: 0.95rem; }
.queue-row__divider { margin: 0 0.15rem; }

.call-button {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  background: #430f6e;
  color: #ffffff;
  border: none;
  border-radius: 12px;
  padding: 0.65rem 1.1rem;
  font-size: 0.88rem;
  font-weight: 600;
  cursor: pointer;
  white-space: nowrap;
  transition: background 0.15s ease, transform 0.1s ease;
}

.call-button i { font-size: 1rem; }

.call-button:hover:not(:disabled) { background: #085041; }
.call-button:active:not(:disabled) { transform: scale(0.97); }
.call-button:disabled {
  background: #B4B2A9;
  cursor: not-allowed;
}

/* EMPTY STATE */
.queue-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 3rem 1rem;
  color: #9CA3AF;
  text-align: center;
}

.queue-empty i { font-size: 2rem; }
.queue-empty p {
  font-size: 0.95rem;
  font-weight: 600;
  color: #6B7280;
  margin: 0;
}
.queue-empty span { font-size: 0.82rem; }

/* RESPONSIVE */
@media (max-width: 720px) {
  .queue-header { flex-direction: column; align-items: stretch; }
  .queue-header__controls { justify-content: space-between; }
  .select-ruangan { flex: 1; min-width: 0; }

  .queue-row {
    grid-template-columns: 1fr auto;
    grid-template-areas:
      "ticket action"
      "info info";
  }
  .queue-row__rank { display: none; }
  .queue-row__ticket { grid-area: ticket; justify-self: start; }
  .call-button { grid-area: action; }
  .queue-row__info { grid-area: info; }
}

@media (prefers-reduced-motion: reduce) {
  .now-serving__pulse { animation: none; }
}
</style>