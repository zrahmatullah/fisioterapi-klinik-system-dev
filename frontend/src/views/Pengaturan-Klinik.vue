<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-y-auto custom-scrollbar w-full pr-1">

    <!-- HEADER STRIP -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
      <div>
        <h2 class="text-[15px] font-semibold text-[#0F172A] leading-tight">Pengaturan Klinik & Hak Akses</h2>
        <p class="text-[11px] text-[#64748B] mt-0.5">Konfigurasi profil klinik, format nomor dokumen medis, peranan RBAC, dan audit trail medis UU PDP.</p>
      </div>
      <button
        type="button"
        @click="saveSettings"
        :disabled="saving"
        class="bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-medium text-xs px-4 py-2 rounded-lg flex items-center gap-1.5 transition shadow-sm shrink-0 disabled:opacity-50"
      >
        <i v-if="saving" class="pi pi-spin pi-spinner text-xs"></i>
        <i v-else class="pi pi-check text-xs"></i>
        <span>Simpan Pengaturan</span>
      </button>
    </div>

    <!-- UNDERLINE TABS -->
    <div class="flex border-b border-slate-200 gap-4 text-xs font-medium">
      <button
        type="button"
        @click="activeTab = 'general'"
        class="pb-2.5 transition flex items-center gap-1.5"
        :class="activeTab === 'general' ? 'border-b-2 border-[#0F766E] text-[#0F766E] font-semibold' : 'text-slate-500 hover:text-slate-700'"
      >
        <i class="pi pi-building text-xs"></i>
        <span>Profil & Kop Klinik</span>
      </button>

      <button
        type="button"
        @click="activeTab = 'numbering'"
        class="pb-2.5 transition flex items-center gap-1.5"
        :class="activeTab === 'numbering' ? 'border-b-2 border-[#0F766E] text-[#0F766E] font-semibold' : 'text-slate-500 hover:text-slate-700'"
      >
        <i class="pi pi-hashtag text-xs"></i>
        <span>Format Penomoran</span>
      </button>

      <button
        type="button"
        @click="activeTab = 'rbac'"
        class="pb-2.5 transition flex items-center gap-1.5"
        :class="activeTab === 'rbac' ? 'border-b-2 border-[#0F766E] text-[#0F766E] font-semibold' : 'text-slate-500 hover:text-slate-700'"
      >
        <i class="pi pi-shield text-xs"></i>
        <span>Matriks Hak Akses (RBAC)</span>
      </button>

      <button
        type="button"
        @click="activeTab = 'audit'"
        class="pb-2.5 transition flex items-center gap-1.5"
        :class="activeTab === 'audit' ? 'border-b-2 border-[#0F766E] text-[#0F766E] font-semibold' : 'text-slate-500 hover:text-slate-700'"
      >
        <i class="pi pi-history text-xs"></i>
        <span>Audit Trail Medis (UU PDP)</span>
      </button>
    </div>

    <!-- TAB 1: PROFIL & KOP KLINIK -->
    <div v-if="activeTab === 'general'" class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)] space-y-4">
      <h3 class="text-[15px] font-semibold text-[#0F172A] border-b border-slate-100 pb-2">Identitas Resmi & Kop Surat</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Nama Resmi Klinik</label>
          <input v-model="settings.clinic_name" type="text" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]" />
        </div>
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Nomor Izin Operasional / SIP</label>
          <input v-model="settings.clinic_sip_izin" type="text" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]" />
        </div>
        <div class="md:col-span-2">
          <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap Klinik</label>
          <input v-model="settings.clinic_address" type="text" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]" />
        </div>
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Telepon & WhatsApp Layanan</label>
          <input v-model="settings.clinic_phone" type="text" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]" />
        </div>
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Email Resmi</label>
          <input v-model="settings.clinic_email" type="text" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-[#0F766E]" />
        </div>
      </div>
    </div>

    <!-- TAB 2: FORMAT PENOMORAN DOKUMEN -->
    <div v-if="activeTab === 'numbering'" class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)] space-y-4">
      <h3 class="text-[15px] font-semibold text-[#0F172A] border-b border-slate-100 pb-2">Standarisasi Format Penomoran Otomatis</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="p-3 bg-[#F0F6F8] rounded-xl border border-[#DCE8EC] space-y-1.5">
          <label class="block text-xs font-bold text-[#0F766E] uppercase">Format No. RM Pasien</label>
          <input v-model="settings.format_no_rm" type="text" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs font-mono font-bold bg-white outline-none" />
          <p class="text-[10px] text-slate-500">Pola: <code class="text-[#0F766E]">RM-{YEAR}{MONTH}-{SEQ}</code><br />Hasil: <strong>RM-202611-0001</strong></p>
        </div>

        <div class="p-3 bg-[#F0F6F8] rounded-xl border border-[#DCE8EC] space-y-1.5">
          <label class="block text-xs font-bold text-[#0F766E] uppercase">Format No. Registrasi</label>
          <input v-model="settings.format_no_regis" type="text" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs font-mono font-bold bg-white outline-none" />
          <p class="text-[10px] text-slate-500">Pola: <code class="text-[#0F766E]">REG-{YEAR}{MONTH}{DAY}-{SEQ}</code><br />Hasil: <strong>REG-20261121-0001</strong></p>
        </div>

        <div class="p-3 bg-[#F0F6F8] rounded-xl border border-[#DCE8EC] space-y-1.5">
          <label class="block text-xs font-bold text-[#0F766E] uppercase">Format Antrian TV</label>
          <input v-model="settings.format_no_antrian" type="text" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs font-mono font-bold bg-white outline-none" />
          <p class="text-[10px] text-slate-500">Pola: <code class="text-[#0F766E]">A-{SEQ}</code><br />Hasil: <strong>A-001</strong></p>
        </div>
      </div>
    </div>

    <!-- TAB 3: MATRIKS HAK AKSES GRANULAR (V, C, U, D) -->
    <div v-if="activeTab === 'rbac'" class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)] space-y-4">
      <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h3 class="text-[15px] font-semibold text-[#0F172A] flex items-center gap-2">
            <i class="pi pi-shield text-[#0F766E]"></i>
            <span>Matriks Hak Akses Granular (V, C, U, D)</span>
          </h3>
          <p class="text-[11px] text-[#64748B] mt-0.5">
            Konfigurasi hak akses Lihat (View), Tambah (Create), Ubah (Update), dan Hapus (Delete) per modul klinik untuk setiap peran.
          </p>
        </div>

        <button
          type="button"
          @click="saveRoleMatrix"
          :disabled="savingMatrix"
          class="btn-primary-clinical text-xs py-2 px-4 shadow-sm"
        >
          <i v-if="savingMatrix" class="pi pi-spin pi-spinner text-xs"></i>
          <i v-else class="pi pi-check text-xs"></i>
          <span>Simpan Matriks Peran</span>
        </button>
      </div>

      <!-- ROLE SELECTOR PILLS -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Pilih Peran Organisasi:</label>
        <div class="flex items-center gap-2 flex-wrap">
          <button
            v-for="r in rbacFull.roles"
            :key="r.id"
            type="button"
            @click="selectRole(r)"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
            :class="selectedRoleId === r.id ? 'bg-[#0F766E] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
          >
            <i class="pi pi-user text-[11px]"></i>
            <span>{{ r.role }}</span>
          </button>
        </div>
      </div>

      <!-- ROLE QUICK ACTIONS -->
      <div class="bg-[#F0F6F8] p-3 rounded-xl border border-[#DCE8EC] flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <div class="text-xs">
          <span class="text-slate-500">Mengatur Hak Akses Untuk:</span>
          <strong class="ml-1 text-[#0F766E] uppercase font-mono">{{ activeRoleName }}</strong>
        </div>

        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="bulkSetPermissions(true)"
            class="px-2.5 py-1 rounded bg-white hover:bg-emerald-50 text-emerald-700 border border-emerald-300 text-xs font-semibold inline-flex items-center gap-1 transition"
          >
            <i class="pi pi-check-circle text-[10px]"></i> Beri Semua (V, C, U, D)
          </button>
          <button
            type="button"
            @click="bulkSetReadOnly"
            class="px-2.5 py-1 rounded bg-white hover:bg-sky-50 text-sky-700 border border-sky-300 text-xs font-semibold inline-flex items-center gap-1 transition"
          >
            <i class="pi pi-eye text-[10px]"></i> Hanya Lihat (View Only)
          </button>
          <button
            type="button"
            @click="bulkSetPermissions(false)"
            class="px-2.5 py-1 rounded bg-white hover:bg-red-50 text-red-600 border border-red-300 text-xs font-semibold inline-flex items-center gap-1 transition"
          >
            <i class="pi pi-times-circle text-[10px]"></i> Cabut Semua
          </button>
        </div>
      </div>

      <!-- V-C-U-D MATRIX TABLE -->
      <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-[13px] text-left">
          <thead class="bg-[#F8FAFC] text-xs font-semibold text-slate-600 border-b border-slate-200">
            <tr>
              <th class="py-2.5 px-4 w-2/5">Modul Sistem SIMRS</th>
              <th class="py-2.5 px-3 text-center">
                <span class="text-emerald-700 font-bold">V</span>
                <span class="text-[11px] text-slate-500 font-normal block">Lihat (View)</span>
              </th>
              <th class="py-2.5 px-3 text-center">
                <span class="text-sky-700 font-bold">C</span>
                <span class="text-[11px] text-slate-500 font-normal block">Tambah (Create)</span>
              </th>
              <th class="py-2.5 px-3 text-center">
                <span class="text-amber-700 font-bold">U</span>
                <span class="text-[11px] text-slate-500 font-normal block">Ubah (Update)</span>
              </th>
              <th class="py-2.5 px-3 text-center">
                <span class="text-red-700 font-bold">D</span>
                <span class="text-[11px] text-slate-500 font-normal block">Hapus (Delete)</span>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in currentRolePermissions" :key="item.module_code" class="hover:bg-slate-50/80 transition">
              <td class="py-2.5 px-4">
                <p class="font-bold text-slate-900 text-xs">{{ item.module_name }}</p>
                <p class="text-[10px] text-slate-400 font-mono">Kode: {{ item.module_code }}</p>
              </td>

              <!-- VIEW (V) -->
              <td class="py-2.5 px-3 text-center">
                <input
                  v-model="item.can_view"
                  type="checkbox"
                  class="w-4 h-4 text-[#0F766E] border-slate-300 rounded focus:ring-[#0F766E] cursor-pointer"
                />
              </td>

              <!-- CREATE (C) -->
              <td class="py-2.5 px-3 text-center">
                <input
                  v-model="item.can_create"
                  type="checkbox"
                  class="w-4 h-4 text-[#0F766E] border-slate-300 rounded focus:ring-[#0F766E] cursor-pointer"
                />
              </td>

              <!-- UPDATE (U) -->
              <td class="py-2.5 px-3 text-center">
                <input
                  v-model="item.can_update"
                  type="checkbox"
                  class="w-4 h-4 text-[#0F766E] border-slate-300 rounded focus:ring-[#0F766E] cursor-pointer"
                />
              </td>

              <!-- DELETE (D) -->
              <td class="py-2.5 px-3 text-center">
                <input
                  v-model="item.can_delete"
                  type="checkbox"
                  class="w-4 h-4 text-red-600 border-slate-300 rounded focus:ring-red-500 cursor-pointer"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 4: AUDIT TRAIL MEDIS -->
    <div v-if="activeTab === 'audit'" class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-hidden">
      <div class="p-4 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h3 class="text-[15px] font-semibold text-[#0F172A]">Log Audit Akses Rekam Medis (Tamper-Proof)</h3>
          <p class="text-[11px] text-[#64748B]">Jejak audit tidak dapat dihapus sesuai amanat UU PDP No. 27/2022 & Permenkes 24/2022.</p>
        </div>
        <button
          type="button"
          @click="loadAuditLogs"
          class="text-xs text-[#0F766E] hover:text-[#115E59] font-semibold flex items-center gap-1"
        >
          <i class="pi pi-refresh text-xs"></i> Refresh Log
        </button>
      </div>

      <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-[13px] text-left">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr>
              <th class="py-2.5 px-3">Waktu Akses</th>
              <th class="py-2.5 px-3">Tenaga Medis / Pengguna</th>
              <th class="py-2.5 px-3">Pasien EMR</th>
              <th class="py-2.5 px-3">Aksi Medis</th>
              <th class="py-2.5 px-3">Alasan Akses</th>
              <th class="py-2.5 px-3">IP Address</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="auditLogs.length === 0" class="text-center">
              <td colspan="6" class="py-10 text-slate-400 text-xs">Belum ada catatan log audit medis.</td>
            </tr>
            <tr v-for="log in auditLogs" :key="log.id" class="hover:bg-slate-50/80 transition text-xs">
              <td class="py-2.5 px-3 font-mono text-slate-500 tabular-nums">{{ formatDate(log.created_at) }}</td>
              <td class="py-2.5 px-3 font-semibold text-slate-900">{{ log.user?.user_profile?.nama || log.user?.username || 'Administrator' }}</td>
              <td class="py-2.5 px-3 font-bold text-[#0F766E]">{{ log.profile_anak?.nama_anak || '-' }}</td>
              <td class="py-2.5 px-3">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                  :class="log.action.includes('VIEW') ? 'bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'">
                  {{ log.action }}
                </span>
              </td>
              <td class="py-2.5 px-3 text-slate-600 text-[11px]">{{ log.alasan_akses || 'Pemeriksaan rutin' }}</td>
              <td class="py-2.5 px-3 font-mono text-slate-400 text-[11px]">{{ log.ip_address || '127.0.0.1' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'
import Button from 'primevue/button'

const toast = useToast()

const activeTab = ref('general')
const saving = ref(false)
const settings = ref({
  clinic_name: 'Klinik Tumbuh Kembang & Terapi Anak E-Clinic',
  clinic_sip_izin: '445/098/SIP-KLINIK/2026',
  clinic_address: 'Jl. Kesehatan Anak No. 45, Jakarta Selatan',
  clinic_phone: '(021) 555-7890 / 081234567890',
  clinic_email: 'info@klinikanak.id',
  format_no_rm: 'RM-{YEAR}{MONTH}-{SEQ}',
  format_no_regis: 'REG-{YEAR}{MONTH}{DAY}-{SEQ}',
  format_no_antrian: 'A-{SEQ}',
})

const rbacData = ref({ roles: [], permissions_by_group: {} })
const auditLogs = ref([])

// RBAC Full Matrix State (V, C, U, D)
const rbacFull = ref({ roles: [], modules: [], matrix: [] })
const selectedRoleId = ref(null)
const currentRolePermissions = ref([])
const savingMatrix = ref(false)

const activeRoleName = computed(() => {
  const r = rbacFull.value.roles.find(x => x.id === selectedRoleId.value)
  return r ? r.role : 'Pilih Peran'
})

const loadRbacFull = async () => {
  try {
    const res = await api.get('/settings/rbac-matrix-full')
    rbacFull.value = res.data || { roles: [], modules: [], matrix: [] }
    if (rbacFull.value.roles.length > 0 && !selectedRoleId.value) {
      selectRole(rbacFull.value.roles[0])
    }
  } catch (err) {
    console.error('Error load RBAC full matrix:', err)
  }
}

const selectRole = (role) => {
  selectedRoleId.value = role.id
  // Filter matrix for selected role
  const perms = rbacFull.value.matrix.filter(m => m.role_id === role.id)
  
  // Format items
  currentRolePermissions.value = rbacFull.value.modules.map(mod => {
    const match = perms.find(p => p.module_code === mod.code)
    return {
      module_code: mod.code,
      module_name: mod.name,
      can_view: match ? Boolean(match.can_view) : false,
      can_create: match ? Boolean(match.can_create) : false,
      can_update: match ? Boolean(match.can_update) : false,
      can_delete: match ? Boolean(match.can_delete) : false,
    }
  })
}

const bulkSetPermissions = (val) => {
  currentRolePermissions.value.forEach(p => {
    p.can_view = val
    p.can_create = val
    p.can_update = val
    p.can_delete = val
  })
}

const bulkSetReadOnly = () => {
  currentRolePermissions.value.forEach(p => {
    p.can_view = true
    p.can_create = false
    p.can_update = false
    p.can_delete = false
  })
}

const saveRoleMatrix = async () => {
  if (!selectedRoleId.value) return
  savingMatrix.value = true
  try {
    const payload = {
      role_id: selectedRoleId.value,
      permissions: currentRolePermissions.value
    }
    const res = await api.post('/settings/rbac-matrix-full', payload)
    toast.success(res.data.message || 'Matriks hak akses peran berhasil disimpan')
    await loadRbacFull()
  } catch (err) {
    toast.error('Gagal menyimpan matriks hak akses')
  } finally {
    savingMatrix.value = false
  }
}

const loadSettings = async () => {
  try {
    const res = await api.get('/settings')
    if (res.data) {
      settings.value = { ...settings.value, ...res.data }
    }
  } catch (err) {
    console.error(err)
  }
}

const saveSettings = async () => {
  saving.value = true
  try {
    await api.post('/settings', settings.value)
    toast.success('Pengaturan klinik berhasil disimpan')
  } catch (err) {
    toast.error('Gagal menyimpan pengaturan')
  } finally {
    saving.value = false
  }
}

const loadRbac = async () => {
  try {
    const res = await api.get('/settings/rbac-matrix')
    rbacData.value = res.data
  } catch (err) {
    console.error(err)
  }
}

const loadAuditLogs = async () => {
  try {
    const res = await api.get('/settings/audit-logs')
    auditLogs.value = res.data?.data || res.data || []
  } catch (err) {
    console.error(err)
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleString('id-ID')
}

onMounted(() => {
  loadSettings()
  loadRbac()
  loadRbacFull()
  loadAuditLogs()
})
</script>
