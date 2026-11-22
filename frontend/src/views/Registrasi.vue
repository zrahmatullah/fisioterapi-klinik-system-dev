<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">
    <!-- HEADER -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 shrink-0">
      <div>
        <h2 class="text-[15px] font-semibold text-[#0F172A] leading-tight flex items-center gap-2">
          <span>Registrasi & Pendaftaran Pasien Anak</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]">
            Dual-Track Billing
          </span>
        </h2>
        <p class="text-[11px] text-[#64748B] mt-0.5">
          Kelola registrasi per kunjungan, paket berkala (4x, 8x, 12x), jaminan asuransi (GL), dan antrian poli terapi
        </p>
      </div>

      <div class="flex flex-col sm:flex-row gap-2.5">
        <div class="relative">
          <i class="pi pi-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input
            type="date"
            v-model="filterTanggal"
            class="pl-8 pr-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs
                   focus:ring-2 focus:ring-[#0F766E]/15 focus:border-[#0F766E] outline-none"
            @change="refreshAll"
          />
        </div>

        <button
          @click="openAdd"
          class="bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white px-4 py-1.5 rounded-lg
                 flex items-center justify-center gap-1.5 text-xs font-medium transition shadow-sm cursor-pointer"
        >
          <i class="pi pi-plus text-[10px]" />
          <span>Pendaftaran Baru</span>
        </button>
      </div>
    </div>

    <!-- STATISTICS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 shrink-0">
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider block">Total Registrasi</span>
        <h3 class="text-lg font-bold text-[#0F172A] tabular-nums mt-0.5 leading-tight">{{ data.length }}</h3>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-medium text-[#0F766E] uppercase tracking-wider flex items-center gap-1.5">
          <span class="w-1.5 h-1.5 rounded-full bg-[#0F766E]"></span>
          Track Paket Terapi
        </span>
        <h3 class="text-lg font-bold text-[#0F766E] tabular-nums mt-0.5 leading-tight">
          {{ data.filter(x => x.payment_track === 'umum_package').length }}
        </h3>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-medium text-[#0F766E] uppercase tracking-wider flex items-center gap-1.5">
          <span class="w-1.5 h-1.5 rounded-full bg-[#0F766E]"></span>
          Track Asuransi / GL
        </span>
        <h3 class="text-lg font-bold text-[#0F766E] tabular-nums mt-0.5 leading-tight">
          {{ data.filter(x => x.payment_track === 'asuransi').length }}
        </h3>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
        <span class="text-[11px] font-medium text-[#15803D] uppercase tracking-wider flex items-center gap-1.5">
          <span class="w-1.5 h-1.5 rounded-full bg-[#22C55E]"></span>
          Sudah Check-In
        </span>
        <h3 class="text-lg font-bold text-[#15803D] tabular-nums mt-0.5 leading-tight">
          {{ data.filter(x => x.status_kedatangan === 'sudah_datang').length }}
        </h3>
      </div>
    </div>

    <!-- TAB NAVIGATION -->
    <div class="flex items-center gap-1.5 p-1 bg-slate-200/60 rounded-xl shrink-0 w-fit">
      <button
        type="button"
        @click="activeMainTab = 'registrasi'"
        class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 cursor-pointer"
        :class="activeMainTab === 'registrasi' ? 'bg-white text-[#0F766E] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
      >
        <i class="pi pi-users text-xs"></i>
        <span>Antrian & Kunjungan Hari Ini</span>
        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-700 font-mono">{{ data.length }}</span>
      </button>

      <button
        type="button"
        @click="activeMainTab = 'appointments'"
        class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 cursor-pointer"
        :class="activeMainTab === 'appointments' ? 'bg-white text-[#0F766E] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
      >
        <i class="pi pi-calendar-plus text-xs"></i>
        <span>Check-In Janji Temu / Sesi Lanjutan</span>
        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-[#E6F2F5] text-[#0F766E] font-mono">{{ appointments.length }}</span>
      </button>
    </div>

    <!-- TABLE CARD -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">

        <!-- TAB 1: ANTRIAN REGISTRASI AKTIF -->
        <table v-if="activeMainTab === 'registrasi'" class="min-w-full text-xs">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr class="border-b border-slate-200 bg-slate-50/80">
              <th class="th text-center w-12">No</th>
              <th class="th">Antrian & No. Regis</th>
              <th class="th">Pasien Anak & Usia</th>
              <th class="th">Metode / Track</th>
              <th class="th">Sesi Terapi</th>
              <th class="th">Terapis & Ruangan</th>
              <th class="th text-center">Status Sesi</th>
              <th class="th text-center">Kedatangan</th>
              <th class="th text-center">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(item, index) in data"
              :key="item.id"
              class="border-b border-slate-100 hover:bg-slate-50 transition-colors"
            >
              <td class="td text-center text-slate-400 font-mono text-xs">{{ index + 1 }}</td>
              
              <!-- Antrian & Regis -->
              <td class="td">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md font-bold font-mono text-xs bg-[#0B3948] text-white mr-1.5 shadow-sm">
                  {{ item.antrian_nomor || item.no_antrian || 'TW-01' }}
                </span>
                <span class="font-mono text-slate-600 font-semibold">{{ item.no_regis }}</span>
              </td>

              <!-- Pasien Anak -->
              <td class="td">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-xs shrink-0">
                    {{ item.profile_anak?.nama_anak?.charAt(0) || 'A' }}
                  </div>
                  <div>
                    <p class="font-bold text-slate-800 text-xs leading-tight">{{ item.profile_anak?.nama_anak || '-' }}</p>
                    <p class="text-[10px] text-slate-500 font-mono mt-0.5">
                      {{ item.profile_anak?.no_rm || item.id_pasien }} &bull; {{ item.profile_anak?.usia_pediatrik || item.profile_anak?.usia_lengkap || 'Anak' }}
                    </p>
                  </div>
                </div>
              </td>

              <!-- Track Pembayaran -->
              <td class="td">
                <span
                  v-if="item.payment_track === 'umum_package'"
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]"
                >
                  <i class="pi pi-box text-[9px]"></i>
                  Paket Terapi
                </span>
                <span
                  v-else-if="item.payment_track === 'asuransi'"
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]"
                >
                  <i class="pi pi-shield text-[9px]"></i>
                  {{ item.guarantor?.nama_penjamin || 'Asuransi' }}
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200"
                >
                  <i class="pi pi-ticket text-[9px]"></i>
                  Umum Single
                </span>
              </td>

              <!-- Sesi Terapi -->
              <td class="td">
                <div v-if="item.payment_track === 'umum_package'" class="text-xs font-semibold text-slate-800">
                  Sesi #{{ item.sesi_ke }} <span class="text-slate-400 font-normal">dari {{ item.total_sesi }}</span>
                </div>
                <div v-else-if="item.payment_track === 'asuransi'" class="text-xs font-semibold text-[#0F766E]">
                  Sesi #{{ item.sesi_ke }} <span class="text-slate-400 font-normal">dari {{ item.total_sesi }}</span>
                </div>
                <div v-else class="text-xs text-slate-500">
                  Single Session
                </div>
              </td>

              <!-- Terapis & Ruangan -->
              <td class="td">
                <p class="font-medium text-slate-800 text-xs">{{ item.terapis?.nama || '-' }}</p>
                <p class="text-[10px] text-slate-500">{{ item.ruangan?.ruangan || '-' }}</p>
              </td>

              <!-- Status Sesi -->
              <td class="td text-center">
                <span
                  v-if="item.status_sesi === 'selesai_terapi'"
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                  Selesai Terapi
                </span>
                <span
                  v-else-if="item.status_sesi === 'sedang_terapi'"
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                  Sedang Terapi
                </span>
                <span
                  v-else-if="item.status_sesi === 'menunggu'"
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 border border-teal-200"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>
                  Menunggu
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200"
                >
                  Terjadwal
                </span>
              </td>

              <!-- Status Kedatangan & Check-In Action -->
              <td class="td text-center">
                <button
                  v-if="item.status_kedatangan !== 'sudah_datang'"
                  type="button"
                  @click="quickCheckIn(item)"
                  class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#0F766E] hover:bg-[#115E59] text-white flex items-center justify-center gap-1 mx-auto transition cursor-pointer shadow-2xs"
                  title="Klik untuk Check-In pasien"
                >
                  <i class="pi pi-check text-[10px]"></i>
                  Check-In
                </button>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  Sudah Hadir
                </span>
              </td>

              <!-- Aksi -->
              <td class="td text-center">
                <div class="flex items-center justify-center gap-1">
                  <button
                    @click="cetakAntrian(item)"
                    title="Cetak Tiket Antrian"
                    class="action-btn"
                  >
                    <i class="pi pi-print text-xs"></i>
                  </button>
                  <button
                    v-if="item.status_kedatangan !== 'tidak_hadir'"
                    @click="updateKedatangan(item, 'tidak_hadir')"
                    title="Tandai Tidak Hadir"
                    class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition border border-rose-200 cursor-pointer"
                  >
                    <i class="pi pi-times text-xs"></i>
                  </button>
                </div>
              </td>
            </tr>

            <!-- EMPTY -->
            <tr v-if="data.length === 0">
              <td colspan="9" class="py-16 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                  <i class="pi pi-inbox text-xl text-slate-400"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-700">Belum ada registrasi pada tanggal ini</h3>
                <p class="text-xs text-slate-400 mt-0.5">Klik tombol 'Pendaftaran Baru' untuk menambahkan kunjungan terapi anak.</p>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- TAB 2: CHECK-IN SESI LANJUTAN / APPOINTMENTS (TAHAP 3) -->
        <table v-else class="min-w-full text-xs">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr class="border-b border-slate-200 bg-slate-50/80">
              <th class="th text-center w-12">No</th>
              <th class="th">Tanggal & Jam</th>
              <th class="th">Pasien Anak & No. RM</th>
              <th class="th">Layanan & Sesi Ke</th>
              <th class="th">Terapis & Ruangan</th>
              <th class="th">Paket Terapi</th>
              <th class="th text-center">Status Janji</th>
              <th class="th text-center">Aksi Check-In</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(apt, index) in appointments"
              :key="apt.id"
              class="border-b border-slate-100 hover:bg-slate-50 transition-colors"
            >
              <td class="td text-center text-slate-400 font-mono text-xs">{{ index + 1 }}</td>
              <td class="td font-mono font-semibold text-slate-800">
                <span>{{ apt.tanggal_appointment?.slice(0, 10) }}</span>
                <span class="text-slate-400 block text-[10px]">{{ apt.jam_mulai || '10:00' }} WIB</span>
              </td>
              <td class="td">
                <p class="font-bold text-slate-900 leading-tight">{{ apt.profile_anak?.nama_anak || '-' }}</p>
                <p class="text-[10px] text-slate-500 font-mono">{{ apt.profile_anak?.no_rm }}</p>
              </td>
              <td class="td font-medium text-slate-800">
                <span class="block">{{ apt.layanan?.layanan || 'Poli Terapi' }}</span>
                <span class="text-[#0F766E] font-bold text-[11px]">Sesi #{{ apt.sesi_ke }} dari {{ apt.total_sesi }}</span>
              </td>
              <td class="td text-slate-700">
                <p class="font-semibold">{{ apt.terapis?.nama || '-' }}</p>
                <p class="text-[10px] text-slate-500">{{ apt.ruangan?.ruangan || '-' }}</p>
              </td>
              <td class="td">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]">
                  Lunas di Awal
                </span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Sisa: {{ apt.paket_terapi_pasien?.sisa_sesi }} sesi</span>
              </td>
              <td class="td text-center">
                <span
                  v-if="apt.status === 'checked_in'"
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                  Checked-In
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-sky-100 text-sky-800 border border-sky-200"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                  Terjadwal (Belum Check-In)
                </span>
              </td>
              <td class="td text-center">
                <button
                  v-if="apt.status !== 'checked_in'"
                  type="button"
                  @click="checkInAppointmentSession(apt)"
                  class="px-3 py-1.5 rounded-lg text-xs font-bold bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white inline-flex items-center gap-1.5 transition cursor-pointer shadow-sm mx-auto"
                  title="Check-In Sesi Lanjutan (Potong Kuota 1 & Terbitkan ID Baru)"
                >
                  <i class="pi pi-check text-[10px]"></i>
                  <span>Check-In Hari H</span>
                </button>
                <span v-else class="text-[11px] font-mono text-emerald-700 font-bold">
                  {{ apt.registrasi_anak?.no_regis || 'Hadir' }}
                </span>
              </td>
            </tr>

            <tr v-if="appointments.length === 0">
              <td colspan="8" class="py-16 text-center text-slate-400">
                <i class="pi pi-calendar-times text-2xl text-slate-300 mb-1 block"></i>
                <span>Tidak ada jadwal appointment lanjutan pada tanggal ini</span>
              </td>
            </tr>
          </tbody>
        </table>

      </div>
    </div>

    <!-- STANDARDIZED CLINICAL MODAL: REGISTRASI BARU & KUNJUNGAN -->
    <teleport to="body">
    <div v-if="dialog" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="closeDialog"></div>

      <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-200 flex flex-col max-h-[92vh] animate-fadeIn overflow-hidden">
        <!-- MODAL HEADER -->
        <div class="flex justify-between items-center px-6 py-4 bg-[#0B3948] text-white">
          <div>
            <h3 class="text-base font-bold text-white tracking-wide flex items-center gap-2.5">
              <i class="pi pi-user-plus text-teal-300 text-base"></i>
              Form Registrasi Kunjungan Anak
            </h3>
            <p class="text-xs text-teal-200/90 mt-0.5">
              {{ step === 1 ? 'Langkah 1 dari 2 — Data Identitas Anak & Orang Tua' : 'Langkah 2 dari 2 — Poli / Ruangan, Terapis & Jalur Pembayaran' }}
            </p>
          </div>

          <button @click="closeDialog" class="w-8 h-8 rounded-lg hover:bg-white/10 text-slate-300 hover:text-white flex items-center justify-center transition cursor-pointer">
            <i class="pi pi-times text-sm"></i>
          </button>
        </div>

        <!-- STEP INDICATOR -->
        <div class="flex items-center gap-3 px-6 py-3.5 bg-slate-50 border-b border-slate-200">
          <div class="flex items-center gap-2.5 flex-1">
            <div
              class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition shadow-sm"
              :class="step >= 1 ? 'bg-[#0F766E] text-white' : 'bg-slate-200 text-slate-500'"
            >
              1
            </div>
            <span class="text-sm font-semibold" :class="step >= 1 ? 'text-[#0B3948]' : 'text-slate-400'">
              Identitas Anak & Orang Tua
            </span>
          </div>

          <div class="h-0.5 flex-1 mx-2" :class="step >= 2 ? 'bg-[#0F766E]' : 'bg-slate-200'"></div>

          <div class="flex items-center gap-2.5 flex-1 justify-end">
            <span class="text-sm font-semibold" :class="step >= 2 ? 'text-[#0B3948]' : 'text-slate-400'">
              Layanan & Pembayaran
            </span>
            <div
              class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition shadow-sm"
              :class="step >= 2 ? 'bg-[#0F766E] text-white' : 'bg-slate-200 text-slate-500'"
            >
              2
            </div>
          </div>
        </div>

        <!-- BODY (scrollable) -->
        <div class="px-6 py-4 overflow-y-auto flex-1 space-y-4">

          <!-- STEP 1: IDENTITAS ANAK & ORANG TUA (LANGSUNG TANPA DROPDOWN PENGHALANG) -->
          <div v-if="step === 1" class="space-y-3.5">
            
            <!-- OPTIONAL AUTO-FILL UNTUK PASIEN LAMA -->
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
              <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5 uppercase tracking-wider">
                  <i class="pi pi-search text-[#0F766E]"></i>
                  Cari Pasien Terdaftar / Pasien Lama (Opsional Auto-Fill)
                </span>
                <button
                  v-if="form.profile_anak_id"
                  type="button"
                  @click="resetToNewChild"
                  class="text-[11px] text-rose-600 hover:text-rose-700 font-semibold underline cursor-pointer"
                >
                  Reset ke Pasien Baru
                </button>
              </div>

              <select v-model="form.profile_anak_id" @change="onSelectChild" class="input">
                <option value="">-- Ketik Langsung Pasien Baru (Walk-in) / Tanpa Pilih Pasien --</option>
                <option v-for="a in anak" :key="a.id" :value="a.id">
                  {{ a.nama_anak }} ({{ a.no_rm || 'RM-' + a.id }})
                </option>
              </select>

              <p v-if="form.profile_anak_id" class="text-[11px] text-teal-700 font-medium">
                Data pasien lama terpilih. Kolom di bawah terisi otomatis dan dapat disesuaikan.
              </p>
            </div>

            <!-- IDENTITAS PASIEN ANAK -->
            <div class="space-y-3">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                  <label class="label">Nama Lengkap Anak *</label>
                  <input
                    v-model="form.nama_anak"
                    placeholder="mis. Muhammad Rafka"
                    class="input"
                    required
                  />
                </div>

                <div>
                  <label class="label">NIK / No. KIA Anak</label>
                  <input
                    v-model="form.NIK"
                    placeholder="16 Digit NIK Anak (Opsional)"
                    class="input"
                  />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                  <label class="label">Tanggal Lahir Anak *</label>
                  <input
                    type="date"
                    v-model="form.tanggal_lahir"
                    class="input"
                    required
                  />
                </div>

                <div>
                  <label class="label">Jenis Kelamin *</label>
                  <select v-model="form.jenis_kelamin_id" class="input" required>
                    <option :value="1">Laki-laki</option>
                    <option :value="2">Perempuan</option>
                  </select>
                </div>

                <div>
                  <label class="label">Agama</label>
                  <select v-model="form.agama_id" class="input">
                    <option :value="1">Islam</option>
                    <option :value="2">Kristen</option>
                    <option :value="3">Katolik</option>
                    <option :value="4">Hindu</option>
                    <option :value="5">Buddha</option>
                  </select>
                </div>
              </div>

              <!-- Usia Pediatrik Preview -->
              <div v-if="form.tanggal_lahir" class="p-2.5 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-between text-xs">
                <span class="text-slate-600 font-medium">Usia Pediatrik Terkalkulasi:</span>
                <span class="font-bold text-[#0F766E]">{{ hitungUsiaPediatrik(form.tanggal_lahir) }}</span>
              </div>
            </div>

            <!-- IDENTITAS ORANG TUA / WALI -->
            <div class="pt-2 border-t border-slate-200 space-y-3">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                  <label class="label">Nama Orang Tua / Wali *</label>
                  <input
                    v-model="form.nama_orang_tua"
                    placeholder="mis. Hendra Setiawan (Ayah / Ibu)"
                    class="input"
                    required
                  />
                </div>

                <div>
                  <label class="label">No. Telepon / WhatsApp *</label>
                  <input
                    v-model="form.notelp"
                    placeholder="mis. 081234567890"
                    class="input"
                    required
                  />
                </div>
              </div>

              <div>
                <label class="label">Alamat Domisili Pasien</label>
                <textarea
                  v-model="form.alamat"
                  rows="2"
                  placeholder="Alamat lengkap tempat tinggal..."
                  class="input"
                ></textarea>
              </div>
            </div>

          </div>

          <!-- STEP 2: LAYANAN, TERAPIS & JALUR PEMBAYARAN -->
          <div v-else class="space-y-4">

            <!-- TANGGAL, RUANGAN & TERAPIS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <div>
                <label class="label">Tanggal Kunjungan</label>
                <input type="date" v-model="form.tgl_regis" class="input" />
              </div>

              <div>
                <label class="label">Poli / Ruangan Terapi *</label>
                <select v-model="form.ruangan_id" class="input" required>
                  <option value="">Pilih ruangan...</option>
                  <option v-for="r in ruangan" :key="r.id" :value="r.id">{{ r.ruangan }}</option>
                </select>
              </div>

              <div>
                <label class="label">Terapis yang Bertugas *</label>
                <select v-model="form.terapis_id" class="input" required>
                  <option value="">Pilih terapis...</option>
                  <option v-for="t in terapisList" :key="t.id" :value="t.id">{{ t.nama }}</option>
                </select>
              </div>
            </div>

            <!-- JADWAL SESI & KELUHAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <label class="label">Jadwal Sesi Mingguan</label>
                <input v-model="form.jadwal_hari" placeholder="Contoh: Setiap Selasa & Kamis" class="input" />
              </div>
              <div>
                <label class="label">Jam Praktik</label>
                <input v-model="form.jadwal_jam" placeholder="Contoh: 10:00 - 11:00 WIB" class="input" />
              </div>
            </div>

            <div>
              <label class="label">Keluhan Utama & Catatan Tumbuh Kembang</label>
              <textarea
                v-model="form.keluhan_saat_ini"
                rows="2"
                placeholder="Keluhan seperti speech delay, sensori, sensory overload, motorik halus..."
                class="input"
              ></textarea>
            </div>

            <!-- METODE PEMBAYARAN & PENJAMIN -->
            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 space-y-3">
              <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block">
                Metode Pembayaran & Penjamin (Dual-Track)
              </label>

              <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <!-- Track A: Single -->
                <label
                  class="flex items-center gap-2 p-2.5 rounded-lg border text-xs font-semibold cursor-pointer transition"
                  :class="form.payment_track === 'umum_single' ? 'bg-[#E6F2F5] border-[#0F766E] text-[#0F766E]' : 'bg-white border-slate-200 text-slate-700'"
                >
                  <input type="radio" value="umum_single" v-model="form.payment_track" class="text-[#0F766E]" />
                  <span>Umum (Per Kunjungan)</span>
                </label>

                <!-- Track A: Paket -->
                <label
                  class="flex items-center gap-2 p-2.5 rounded-lg border text-xs font-semibold cursor-pointer transition"
                  :class="form.payment_track === 'umum_package' ? 'bg-[#E6F2F5] border-[#0F766E] text-[#0F766E]' : 'bg-white border-slate-200 text-slate-700'"
                >
                  <input type="radio" value="umum_package" v-model="form.payment_track" class="text-[#0F766E]" />
                  <span>Umum (Paket Berkala)</span>
                </label>

                <!-- Track B: Asuransi -->
                <label
                  class="flex items-center gap-2 p-2.5 rounded-lg border text-xs font-semibold cursor-pointer transition"
                  :class="form.payment_track === 'asuransi' ? 'bg-[#E6F2F5] border-[#0F766E] text-[#0F766E]' : 'bg-white border-slate-200 text-slate-700'"
                >
                  <input type="radio" value="asuransi" v-model="form.payment_track" class="text-[#0F766E]" />
                  <span>Asuransi / Penjamin</span>
                </label>
              </div>

              <!-- Extra fields for Paket Terapi -->
              <div v-if="form.payment_track === 'umum_package'" class="mt-2 pt-2 border-t border-slate-200 space-y-2">
                <div v-if="form.profile_anak_id && childPackages.length > 0">
                  <label class="label">Pilih Paket Terapi Aktif Anak Ini</label>
                  <select v-model="form.patient_therapy_package_id" class="input">
                    <option value="">Pilih paket...</option>
                    <option v-for="pkg in childPackages" :key="pkg.id" :value="pkg.id">
                      {{ pkg.nama_paket }} (Sisa: {{ pkg.sisa_sesi }} dari {{ pkg.total_sesi }} sesi)
                    </option>
                  </select>
                </div>
                <div v-else>
                  <label class="label">Pilih Master Paket Terapi yang Dibeli</label>
                  <select v-model="form.therapy_package_id" class="input">
                    <option value="">Pilih paket terapi...</option>
                    <option v-for="mp in masterPackages" :key="mp.id" :value="mp.id">
                      {{ mp.nama_paket }} ({{ mp.jumlah_sesi }} Sesi) - Rp {{ Number(mp.harga_paket || 0).toLocaleString('id-ID') }}
                    </option>
                  </select>
                  <p class="text-[11px] text-teal-700 mt-1">
                    Paket baru akan otomatis diterbitkan dan sesi #1 dijadwalkan hari ini.
                  </p>
                </div>
              </div>

              <!-- Extra fields for Asuransi -->
              <div v-if="form.payment_track === 'asuransi'" class="mt-2 pt-2 border-t border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                  <label class="label">Pilih Perusahaan Penjamin</label>
                  <select v-model="form.guarantor_id" class="input">
                    <option value="">Pilih penjamin...</option>
                    <option v-for="g in guarantors" :key="g.id" :value="g.id">
                      {{ g.nama_penjamin }} ({{ g.kode_penjamin }})
                    </option>
                  </select>
                </div>

                <div>
                  <label class="label">No. Polis / Kartu Peserta</label>
                  <input v-model="form.no_polis" placeholder="Contoh: 00018272991" class="input" />
                </div>

                <div>
                  <label class="label">No. Surat Jaminan (GL / LoG)</label>
                  <input v-model="form.no_surat_jaminan" placeholder="Contoh: GL-2026-MND-001" class="input" />
                </div>

                <div>
                  <label class="label">Kuota Sesi Jaminan</label>
                  <input type="number" v-model="form.kuota_sesi" placeholder="Contoh: 10" class="input" />
                </div>
              </div>
            </div>

          </div>

        </div>

        <!-- FOOTER -->
        <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-t border-slate-200 shrink-0">
          <button
            v-if="step === 2"
            @click="step = 1"
            :disabled="saving"
            class="h-[40px] px-5 rounded-lg text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 transition flex items-center gap-2 cursor-pointer"
          >
            <i class="pi pi-arrow-left text-xs"></i>
            Kembali
          </button>
          <button v-else @click="closeDialog" class="h-[40px] px-5 rounded-lg text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 transition cursor-pointer">
            Batal
          </button>

          <button
            v-if="step === 1"
            @click="goToStep2"
            class="h-[40px] px-6 rounded-lg bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white text-sm font-semibold transition flex items-center gap-2 cursor-pointer shadow-sm"
          >
            <span>Lanjut ke Layanan & Pembayaran</span>
            <i class="pi pi-arrow-right text-xs"></i>
          </button>
          <button
            v-else
            @click="save"
            :disabled="saving"
            class="h-[40px] px-6 rounded-lg bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white text-sm font-semibold transition flex items-center justify-center gap-2 min-w-[160px] cursor-pointer shadow-sm"
          >
            <span v-if="saving" class="flex items-center gap-2">
              <i class="pi pi-spin pi-spinner text-sm"></i>
              Menyimpan...
            </span>
            <span v-else class="flex items-center gap-2">
              <i class="pi pi-check-circle text-sm"></i>
              Simpan Registrasi
            </span>
          </button>
        </div>
      </div>
    </div>
    </teleport>

    <!-- SUCCESS MODAL: KREDENSIAL ORTU & BUKTI REGISTRASI -->
    <teleport to="body">
    <div v-if="showSuccessModal" class="fixed inset-0 z-[9999] bg-slate-900/40 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-fadeIn">
        <div class="px-6 py-4 bg-[#0B3948] text-white flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-teal-500/20 flex items-center justify-center text-teal-300">
              <i class="pi pi-check-circle text-base"></i>
            </div>
            <div>
              <h3 class="text-sm font-bold tracking-wide">Registrasi Pasien Sukses!</h3>
              <p class="text-[11px] text-teal-200">Akun Portal Orang Tua & Antrian Berhasil Diterbitkan</p>
            </div>
          </div>
          <button @click="showSuccessModal = false" class="text-slate-300 hover:text-white p-1 cursor-pointer">
            <i class="pi pi-times text-xs"></i>
          </button>
        </div>

        <div class="p-6 space-y-4 text-xs">
          <!-- RINGKASAN PASIEN & ANTRIAN -->
          <div class="p-3.5 bg-[#E6F2F5] border border-[#CBE3E9] rounded-xl flex items-center justify-between">
            <div>
              <p class="text-slate-500 text-[11px]">Nama Pasien Anak:</p>
              <p class="font-bold text-slate-800 text-sm">{{ createdChildData?.nama_anak }}</p>
              <p class="font-mono text-teal-700 font-semibold">{{ createdChildData?.no_rm }}</p>
            </div>
            <div class="text-right">
              <p class="text-slate-500 text-[11px]">Nomor Antrian:</p>
              <p class="text-xl font-mono font-extrabold text-[#0F766E]">{{ createdRegisData?.no_antrian }}</p>
            </div>
          </div>

          <!-- KREDENSIAL LOGIN PORTAL ORANG TUA -->
          <div v-if="createdParentAccount" class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
            <div class="flex items-center gap-2 font-bold text-emerald-800">
              <i class="pi pi-key text-emerald-600"></i>
              <span>Kredensial Portal Orang Tua (Bisa Diberikan ke Ortu):</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-emerald-300 font-mono text-xs space-y-1.5">
              <div class="flex justify-between">
                <span class="text-slate-500">Username:</span>
                <strong class="text-emerald-900 select-all font-bold">{{ createdParentAccount.username }}</strong>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Password:</span>
                <strong class="text-emerald-900 select-all font-bold">{{ createdParentAccount.password }}</strong>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Nama Wali:</span>
                <strong class="text-slate-800">{{ createdParentAccount.nama_wali }}</strong>
              </div>
            </div>
            <p class="text-[11px] text-slate-500">
              * Orang tua dapat login ke portal klinik untuk melihat jadwal sesi anak, riwayat terapi, dan home program.
            </p>
          </div>

          <div class="flex items-center justify-end gap-2.5 pt-2">
            <button
              v-if="createdRegisData?.id"
              type="button"
              @click="cetakAntrian(createdRegisData)"
              class="px-4 py-2 rounded-lg text-xs font-semibold bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 flex items-center gap-1.5 cursor-pointer"
            >
              <i class="pi pi-print text-xs"></i>
              Cetak Karcis Antrian
            </button>
            <button
              type="button"
              @click="showSuccessModal = false"
              class="btn-primary-clinical text-xs py-2 px-5"
            >
              Selesai & Tutup
            </button>
          </div>
        </div>
      </div>
    </div>
    </teleport>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const data = ref([])
const appointments = ref([])
const activeMainTab = ref('registrasi')
const anak = ref([])
const terapisList = ref([])
const ruangan = ref([])
const guarantors = ref([])
const masterPackages = ref([])
const childPackages = ref([])
const selectedChild = ref(null)

const dialog = ref(false)
const step = ref(1)
const saving = ref(false)
const filterTanggal = ref(new Date().toISOString().slice(0, 10))

// Success state for created parent credentials & queue ticket
const showSuccessModal = ref(false)
const createdParentAccount = ref(null)
const createdChildData = ref(null)
const createdRegisData = ref(null)

const form = ref({
  profile_anak_id: '',
  nama_anak: '',
  NIK: '',
  tanggal_lahir: '',
  jenis_kelamin_id: 1,
  agama_id: 1,
  nama_orang_tua: '',
  nama_ayah: '',
  nama_ibu: '',
  notelp: '',
  alamat: '',
  tgl_regis: new Date().toISOString().slice(0, 10),
  terapis_id: '',
  ruangan_id: '',
  payment_track: 'umum_single',
  patient_therapy_package_id: '',
  therapy_package_id: '',
  guarantor_id: '',
  no_polis: '',
  no_surat_jaminan: '',
  kuota_sesi: 10,
  jadwal_hari: 'Selasa & Kamis',
  jadwal_jam: '10:00',
  keluhan_saat_ini: ''
})

const hitungUsiaPediatrik = (tglStr) => {
  if (!tglStr) return ''
  const birth = new Date(tglStr)
  const today = new Date()
  let y = today.getFullYear() - birth.getFullYear()
  let m = today.getMonth() - birth.getMonth()
  let d = today.getDate() - birth.getDate()
  if (d < 0) {
    m -= 1
    d += 30
  }
  if (m < 0) {
    y -= 1
    m += 12
  }
  return `${y} Tahun, ${m} Bulan, ${d} Hari`
}

onMounted(async () => {
  await refreshAll()
  await loadMasterData()
})

async function refreshAll() {
  await Promise.all([
    load(),
    loadAppointments()
  ])
}

async function loadMasterData() {
  try {
    const [gRes, tRes, rRes, pRes] = await Promise.all([
      api.get('/pediatric-workflow/guarantors'),
      api.get('/terapis'),
      api.get('/ruangan'),
      api.get('/pediatric-workflow/packages')
    ])
    guarantors.value = gRes.data ?? []
    terapisList.value = tRes.data?.data ?? tRes.data ?? []
    ruangan.value = rRes.data ?? []
    masterPackages.value = pRes.data ?? []
  } catch (err) {
    console.error('Failed to load master data:', err)
  }
}

async function load() {
  try {
    const anakRes = await api.get('/profile-anak')
    anak.value = anakRes.data?.data ?? anakRes.data ?? []

    const r1 = await api.get('/registrasi-anak')
    let allData = r1.data ?? []

    allData = allData.filter(item => {
      const tgl = item.tgl_regis?.slice(0, 10)
      return tgl === filterTanggal.value
    })

    data.value = allData
  } catch (err) {
    console.error(err)
  }
}

async function loadAppointments() {
  try {
    const res = await api.get('/pediatric-workflow/appointments', {
      params: { tanggal: filterTanggal.value }
    })
    appointments.value = res.data?.data || res.data || []
  } catch (err) {
    console.error('Failed to load appointments:', err)
  }
}

async function checkInAppointmentSession(apt) {
  try {
    const res = await api.post(`/pediatric-workflow/appointments/${apt.id}/check-in`)
    toast.success(res.data?.message || 'Check-In sesi lanjutan berhasil!')
    await Promise.all([load(), loadAppointments()])
  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal Check-In sesi appointment'
    toast.error(msg)
  }
}

function resetToNewChild() {
  form.value.profile_anak_id = ''
  form.value.nama_anak = ''
  form.value.NIK = ''
  form.value.tanggal_lahir = ''
  form.value.jenis_kelamin_id = 1
  form.value.agama_id = 1
  form.value.nama_orang_tua = ''
  form.value.notelp = ''
  form.value.alamat = ''
  form.value.keluhan_saat_ini = ''
  selectedChild.value = null
  childPackages.value = []
}

async function onSelectChild() {
  if (!form.value.profile_anak_id) {
    resetToNewChild()
    return
  }

  selectedChild.value = anak.value.find(a => Number(a.id) === Number(form.value.profile_anak_id))
  
  if (selectedChild.value) {
    form.value.nama_anak = selectedChild.value.nama_anak || ''
    form.value.NIK = selectedChild.value.NIK || ''
    form.value.tanggal_lahir = selectedChild.value.tanggal_lahir ? selectedChild.value.tanggal_lahir.slice(0, 10) : ''
    form.value.jenis_kelamin_id = selectedChild.value.jenis_kelamin_id || 1
    form.value.agama_id = selectedChild.value.agama_id || 1
    form.value.alamat = selectedChild.value.alamat || ''
    form.value.notelp = selectedChild.value.no_hp_orang_tua || selectedChild.value.orang_tua?.no_telepon || ''
    form.value.nama_orang_tua = selectedChild.value.orang_tua?.nama || ''
    form.value.keluhan_saat_ini = selectedChild.value.diagnosis_awal || ''
  }

  try {
    const res = await api.get(`/pediatric-workflow/child/${form.value.profile_anak_id}/packages`)
    childPackages.value = res.data ?? []
    if (childPackages.value.length > 0) {
      form.value.patient_therapy_package_id = childPackages.value[0].id
    }
  } catch (err) {
    childPackages.value = []
  }
}

function openAdd() {
  form.value = {
    profile_anak_id: '',
    nama_anak: '',
    NIK: '',
    tanggal_lahir: '',
    jenis_kelamin_id: 1,
    agama_id: 1,
    nama_orang_tua: '',
    nama_ayah: '',
    nama_ibu: '',
    notelp: '',
    alamat: '',
    tgl_regis: filterTanggal.value || new Date().toISOString().slice(0, 10),
    terapis_id: terapisList.value[0]?.id || '',
    ruangan_id: ruangan.value[0]?.id || '',
    payment_track: 'umum_single',
    patient_therapy_package_id: '',
    therapy_package_id: masterPackages.value[0]?.id || '',
    guarantor_id: guarantors.value[0]?.id || '',
    no_polis: '',
    no_surat_jaminan: '',
    kuota_sesi: 10,
    jadwal_hari: 'Selasa & Kamis',
    jadwal_jam: '10:00',
    keluhan_saat_ini: ''
  }
  selectedChild.value = null
  childPackages.value = []
  step.value = 1
  dialog.value = true
}

function goToStep2() {
  if (!form.value.profile_anak_id && !form.value.nama_anak?.trim()) {
    toast.error('Harap masukkan Nama Lengkap Anak.')
    return
  }
  if (!form.value.tanggal_lahir) {
    toast.error('Harap isi Tanggal Lahir Anak.')
    return
  }
  if (!form.value.nama_orang_tua?.trim()) {
    toast.error('Harap isi Nama Orang Tua / Wali.')
    return
  }
  if (!form.value.notelp?.trim()) {
    toast.error('Harap isi No. Telepon / WhatsApp.')
    return
  }
  step.value = 2
}

function closeDialog() {
  if (saving.value) return
  dialog.value = false
}

async function save() {
  if (!form.value.profile_anak_id && !form.value.nama_anak?.trim()) {
    toast.error('Harap masukkan Nama Lengkap Anak.')
    step.value = 1
    return
  }
  if (!form.value.ruangan_id) {
    toast.error('Harap pilih Poli / Ruangan Terapi.')
    return
  }
  if (!form.value.terapis_id) {
    toast.error('Harap pilih Terapis yang bertugas.')
    return
  }

  saving.value = true
  try {
    const res = await api.post('/pediatric-workflow/register-session', form.value)
    toast.success(res.data?.message || 'Registrasi kunjungan anak berhasil disimpan!')
    dialog.value = false

    if (res.data?.parent_account) {
      createdParentAccount.value = res.data.parent_account
      createdChildData.value = res.data.child
      createdRegisData.value = res.data.data
      showSuccessModal.value = true
    }

    await Promise.all([load(), loadAppointments()])
  } catch (err) {
    console.error(err)
    const msg = err.response?.data?.message || 'Gagal menyimpan registrasi'
    toast.error(msg)
  } finally {
    saving.value = false
  }
}

async function quickCheckIn(item) {
  try {
    const res = await api.post(`/pediatric-workflow/checkin/${item.id}`)
    toast.success(res.data?.message || 'Check-In berhasil!')
    await load()
  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal Check-In pasien'
    toast.error(msg)
  }
}

function cetakAntrian(item) {
  const baseUrl = import.meta.env.VITE_API_BASE_URL
  window.open(`${baseUrl}/cetak/antrian/${item.id}`, '_blank')
}

async function updateKedatangan(item, status) {
  try {
    const res = await api.put(`/registrasi-anak/${item.id}/kedatangan`, { status_kedatangan: status })
    item.status_kedatangan = res.data.data.status_kedatangan
    item.waktu_kedatangan = res.data.data.waktu_kedatangan
    toast.success('Status kedatangan diperbarui')
  } catch (err) {
    console.error(err)
    toast.error('Gagal update status')
  }
}
</script>

<style scoped>
.th {
  @apply px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-700;
}

.td {
  @apply px-4 py-3.5 text-sm text-slate-800 align-middle;
}

.label {
  @apply text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5 block;
}

.input {
  @apply w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900
         transition focus:ring-2 focus:ring-[#0F766E]/20 focus:border-[#0F766E] focus:outline-none;
}

.action-btn {
  @apply w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-[#E6F2F5] text-slate-700 hover:text-[#0F766E] hover:border-teal-400 flex items-center justify-center transition cursor-pointer;
}

.animate-fadeIn {
  animation: fadeIn 0.15s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.98) translateY(4px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
</style>
