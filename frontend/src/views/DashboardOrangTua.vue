<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3.5 overflow-hidden w-full">

    <!-- HEADER BANNER (SHRINK-0) -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
      <div>
        <h1 class="text-[17px] font-bold text-[#0F172A] tracking-tight flex items-center gap-2">
          <span>Portal Pasien & Orang Tua</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]">
            Pediatric Care & Sesi Terapi
          </span>
        </h1>
        <p class="text-xs text-[#64748B] mt-0.5">
          Pantau kalender jadwal sesi terapi anak, kuota paket prabayar (lunas di awal), resep obat dengan Signa lengkap, dan instruksi Home Program
        </p>
      </div>

      <div class="flex items-center gap-2">
        <button
          type="button"
          @click="loadAllData"
          :disabled="loading"
          class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer shadow-2xs"
        >
          <i class="pi pi-refresh text-xs text-slate-400" :class="{ 'pi-spin': loading }"></i>
          <span>Refresh Data</span>
        </button>
      </div>
    </div>

    <!-- MAIN SCROLLABLE CONTENT (FLEX-1) -->
    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar space-y-4 pr-1">

      <!-- ========================================================================= -->
      <!-- 1. DATA PASIEN ANAK & USIA PEDIATRIK -->
      <!-- ========================================================================= -->
      <section v-if="profileAnak" class="space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
          <!-- NAMA ANAK & NO. RM -->
          <div class="bg-[#E6F2F5] rounded-xl border border-[#CBE3E9] p-3.5 flex items-center gap-3.5 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-[#0F766E] text-white flex items-center justify-center shrink-0 font-bold text-lg shadow-sm">
              {{ profileAnak.nama_anak?.charAt(0) || 'A' }}
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[10px] font-bold text-[#15576B] uppercase tracking-wider block">Pasien Utama</span>
              <p class="text-sm font-bold text-[#0F172A] truncate leading-tight mt-0.5">{{ profileAnak.nama_anak }}</p>
              <p class="text-[11px] font-mono font-semibold text-teal-800 mt-0.5">No. RM: {{ profileAnak.no_rm || ('RM-' + profileAnak.id) }}</p>
            </div>
          </div>

          <!-- USIA PEDIATRIK -->
          <div class="bg-white rounded-xl border border-slate-200 p-3.5 flex items-center gap-3.5 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-[#F0F6F8] border border-[#DCE8EC] text-[#0F766E] flex items-center justify-center shrink-0">
              <i class="pi pi-clock text-lg"></i>
            </div>
            <div>
              <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider block">Usia Tumbuh Kembang</span>
              <p class="text-sm font-bold text-[#0F172A] leading-tight mt-0.5">
                {{ profileAnak.usia_pediatrik || profileAnak.usia_lengkap || '5 Thn' }}
              </p>
              <p class="text-[11px] text-slate-500 mt-0.5">Lahir: {{ formatTanggal(profileAnak.tanggal_lahir) }}</p>
            </div>
          </div>

          <!-- DIAGNOSIS & STATUS -->
          <div class="bg-white rounded-xl border border-slate-200 p-3.5 flex items-center gap-3.5 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-[#F0F6F8] border border-[#DCE8EC] text-[#0F766E] flex items-center justify-center shrink-0">
              <i class="pi pi-heart text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider block">Diagnosis Klinis</span>
              <p class="text-xs font-semibold text-slate-800 truncate leading-tight mt-0.5">
                {{ profileAnak.diagnosis_awal || 'Speech & Developmental Delay' }}
              </p>
              <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                PROGRAM AKTIF
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- ========================================================================= -->
      <!-- 2. INTERACTIVE CALENDAR SESI TERAPI ANAK (TAHAP 2 & 6) -->
      <!-- ========================================================================= -->
      <section class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)] space-y-3.5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-3 border-b border-slate-100">
          <div>
            <h2 class="text-sm font-bold text-[#0F172A] flex items-center gap-2">
              <i class="pi pi-calendar text-[#0F766E]"></i>
              <span>Kalender Jadwal Sesi Terapi Anak</span>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]">
                Live Schedule
              </span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
              Jadwal sesi lanjutan terdaftar otomatis. Sesi lanjutan yang belum tiba berstatus TERJADWAL (kuota paket hanya terpotong saat Check-In di klinik).
            </p>
          </div>

          <!-- Calendar Controls -->
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="prevMonth"
              class="w-7 h-7 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 flex items-center justify-center transition cursor-pointer"
              title="Bulan Sebelumnya"
            >
              <i class="pi pi-chevron-left text-xs"></i>
            </button>
            <span class="text-xs font-bold text-[#0B3948] min-w-[130px] text-center">
              {{ currentMonthName }} {{ currentYear }}
            </span>
            <button
              type="button"
              @click="nextMonth"
              class="w-7 h-7 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 flex items-center justify-center transition cursor-pointer"
              title="Bulan Berikutnya"
            >
              <i class="pi pi-chevron-right text-xs"></i>
            </button>
            <button
              type="button"
              @click="goToToday"
              class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer ml-1"
            >
              Hari Ini
            </button>
          </div>
        </div>

        <!-- CALENDAR GRID -->
        <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
          <!-- Day Header -->
          <div class="grid grid-cols-7 bg-[#F8FAFC] border-b border-slate-200 text-center text-[11px] font-semibold text-slate-600 uppercase py-2">
            <span class="text-rose-600">Min</span>
            <span>Sen</span>
            <span>Sel</span>
            <span>Rab</span>
            <span>Kam</span>
            <span>Jum</span>
            <span class="text-teal-700">Sab</span>
          </div>

          <!-- Day Cells -->
          <div class="grid grid-cols-7 divide-x divide-y divide-slate-100 min-h-[320px]">
            <div
              v-for="(day, idx) in calendarDays"
              :key="idx"
              class="p-1.5 sm:p-2 min-h-[68px] sm:min-h-[85px] transition flex flex-col justify-between"
              :class="[
                day.isCurrentMonth ? 'bg-white' : 'bg-slate-50/60 opacity-40',
                day.isToday ? 'ring-2 ring-inset ring-[#0F766E]/40 bg-teal-50/20' : ''
              ]"
            >
              <!-- Date number -->
              <div class="flex items-center justify-between">
                <span
                  class="text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center"
                  :class="day.isToday ? 'bg-[#0F766E] text-white shadow-sm' : (day.isSunday ? 'text-rose-600' : 'text-slate-700')"
                >
                  {{ day.dateNumber }}
                </span>
                <span v-if="day.appointments.length > 0" class="text-[9px] font-mono text-teal-700 font-bold bg-[#E6F2F5] px-1 rounded">
                  {{ day.appointments.length }} sesi
                </span>
              </div>

              <!-- Appointment Pills -->
              <div class="space-y-1 mt-1 flex-1">
                <div
                  v-for="apt in day.appointments"
                  :key="apt.id"
                  @click="openAppointmentDetail(apt)"
                  class="px-1.5 py-0.5 rounded text-[10px] font-semibold truncate cursor-pointer transition border flex items-center justify-between gap-1 shadow-2xs hover:scale-[1.02]"
                  :class="getAppointmentBadgeClass(apt.status)"
                  :title="`Sesi #${apt.sesi_ke} - ${apt.layanan?.layanan || 'Terapi'} (${apt.jam_mulai || '10:00'})`"
                >
                  <span class="truncate">#{{ apt.sesi_ke }} {{ apt.layanan?.layanan?.split(' ')[0] || 'Terapi' }}</span>
                  <span class="text-[9px] font-mono opacity-80">{{ apt.jam_mulai?.slice(0, 5) || '10:00' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap items-center justify-between text-xs text-slate-500 pt-1">
          <div class="flex items-center gap-3 flex-wrap">
            <span class="flex items-center gap-1.5">
              <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
              <span>Terjadwal (Belum Check-In)</span>
            </span>
            <span class="flex items-center gap-1.5">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
              <span>Sudah Check-In / Berlangsung</span>
            </span>
            <span class="flex items-center gap-1.5">
              <span class="w-2.5 h-2.5 rounded-full bg-teal-700"></span>
              <span>Selesai Terapi</span>
            </span>
          </div>
          <span class="text-[11px] text-[#0F766E] font-medium">* Klik kotak sesi pada kalender untuk melihat detail & konfirmasi kehadiran</span>
        </div>
      </section>

      <!-- ========================================================================= -->
      <!-- 3. DOMPET KUOTA PAKET (LUNAS DI AWAL) & JAMINAN ASURANSI -->
      <!-- ========================================================================= -->
      <section class="space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
            <i class="pi pi-box text-[#0F766E]"></i>
            Dompet Kuota Paket Terapi & Jaminan Asuransi
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
          <!-- Card 1: Paket Terapi (Pre-Paid Lunas di Awal) -->
          <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] text-[#0F766E] flex items-center justify-center font-bold text-xs">
                  <i class="pi pi-ticket text-xs"></i>
                </div>
                <div>
                  <h4 class="text-xs font-bold text-slate-900 leading-tight">Paket Terapi Mandiri</h4>
                  <p class="text-[10px] text-slate-500">Pre-Paid Therapy Bundle</p>
                </div>
              </div>

              <span
                v-if="packages.length > 0"
                class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
              >
                Aktif
              </span>
            </div>

            <div v-if="packages.length > 0" class="space-y-3">
              <div v-for="pkg in packages" :key="pkg.id" class="p-3.5 rounded-lg bg-[#F0F6F8] border border-[#DCE8EC] space-y-2">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-bold text-[#0B3948]">{{ pkg.nama_paket }}</span>
                  <span class="font-bold text-[#0F766E] tabular-nums">Sisa {{ pkg.sisa_sesi }} / {{ pkg.total_sesi }} Sesi</span>
                </div>

                <!-- Lunas di Awal Banner -->
                <div class="flex items-center justify-between text-[11px] bg-white px-2 py-1 rounded border border-slate-200">
                  <span class="font-semibold text-emerald-700 flex items-center gap-1">
                    <i class="pi pi-check-circle text-[10px]"></i>
                    Lunas di Awal (Rp 0 per sesi lanjutan)
                  </span>
                  <span class="font-mono text-slate-600">{{ formatRupiah(pkg.harga_paket) }}</span>
                </div>

                <!-- Progress bar -->
                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                  <div
                    class="bg-[#0F766E] h-2 rounded-full transition-all duration-500"
                    :style="{ width: `${pkg.total_sesi > 0 ? (pkg.sesi_terpakai / pkg.total_sesi) * 100 : 0}%` }"
                  ></div>
                </div>

                <div class="flex items-center justify-between text-[10px] text-slate-500">
                  <span>Terpakai: {{ pkg.sesi_terpakai }} Sesi</span>
                  <span>Masa Aktif s/d {{ formatTanggal(pkg.tanggal_berakhir) }}</span>
                </div>
              </div>
            </div>
            <div v-else class="p-5 rounded-lg bg-slate-50 text-center text-xs text-slate-500">
              Tidak ada paket terapi aktif saat ini.
            </div>
          </div>

          <!-- Card 2: Status Jaminan Asuransi (GL) -->
          <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] text-[#0F766E] flex items-center justify-center font-bold text-xs">
                  <i class="pi pi-shield text-xs"></i>
                </div>
                <div>
                  <h4 class="text-xs font-bold text-slate-900 leading-tight">Surat Jaminan Asuransi (GL)</h4>
                  <p class="text-[10px] text-slate-500">Jaminan Penjamin / Asuransi</p>
                </div>
              </div>

              <span
                v-if="insurances.length > 0"
                class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]"
              >
                Terotorisasi
              </span>
            </div>

            <div v-if="insurances.length > 0" class="space-y-3">
              <div v-for="ins in insurances" :key="ins.id" class="p-3.5 rounded-lg bg-[#E6F2F5]/50 border border-[#CBE3E9] space-y-1.5 text-xs">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-[#0B3948]">{{ ins.guarantor?.nama_penjamin || 'Asuransi' }}</span>
                  <span class="font-bold text-[#0F766E] tabular-nums">Sisa {{ ins.sisa_kuota_sesi }} / {{ ins.kuota_sesi }} Sesi</span>
                </div>
                <p class="text-[11px] text-slate-700 font-mono">No. Surat Jaminan: {{ ins.no_surat_jaminan || '-' }}</p>
                <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1 border-t border-teal-100">
                  <span>Polis: {{ ins.no_polis }}</span>
                  <span>Berlaku s/d {{ formatTanggal(ins.tgl_berlaku_selesai) }}</span>
                </div>
              </div>
            </div>
            <div v-else class="p-5 rounded-lg bg-slate-50 text-center text-xs text-slate-500">
              Tidak ada Surat Jaminan Asuransi aktif saat ini.
            </div>
          </div>
        </div>
      </section>

      <!-- ========================================================================= -->
      <!-- 4. RESEP OBAT & ATURAN PAKAI (SIGNA LENGKAP) -->
      <!-- ========================================================================= -->
      <section class="space-y-3">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
              <i class="pi pi-file-edit text-[#0F766E]"></i>
              Resep Obat & Aturan Pakai (Signa Lengkap)
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
              Instruksi dosis dan waktu minum obat anak yang diresepkan oleh dokter spesialis anak / konsultan
            </p>
          </div>
        </div>

        <div v-if="resepList.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
          <div
            v-for="rsp in resepList"
            :key="rsp.id"
            class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm space-y-3"
          >
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
              <div>
                <span class="font-mono font-bold text-xs text-[#0F766E]">{{ rsp.nomor_resep }}</span>
                <span class="text-[11px] text-slate-500 ml-2">{{ formatTanggal(rsp.created_at) }}</span>
              </div>
              <span
                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                :class="rsp.status === 'selesai_dispensing' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
              >
                {{ rsp.status === 'selesai_dispensing' ? 'Telah Diterima' : 'Sedang Disiapkan Farmasi' }}
              </span>
            </div>

            <div class="space-y-2">
              <div
                v-for="item in rsp.items"
                :key="item.id"
                class="p-2.5 rounded-lg bg-[#F0F6F8] border border-[#DCE8EC] space-y-1 text-xs"
              >
                <div class="flex items-center justify-between font-bold text-slate-900">
                  <span>{{ item.nama_obat }}</span>
                  <span class="text-teal-800 font-mono">{{ item.jumlah }} {{ item.satuan }}</span>
                </div>

                <!-- Signa & Waktu Minum -->
                <div class="flex items-center gap-2 text-[11px] font-semibold text-[#0F766E]">
                  <span class="px-1.5 py-0.5 rounded bg-white border border-teal-200">
                    <i class="pi pi-check text-[9px]"></i> Signa: {{ item.signa }}
                  </span>
                  <span class="px-1.5 py-0.5 rounded bg-white border border-teal-200">
                    <i class="pi pi-clock text-[9px]"></i> {{ item.waktu_minum }}
                  </span>
                </div>

                <p v-if="item.catatan_racikan" class="text-[10px] text-slate-600 italic">
                  Catatan: {{ item.catatan_racikan }}
                </p>
              </div>
            </div>

            <div class="text-[11px] text-slate-500 pt-2 border-t border-slate-100 flex justify-between">
              <span>Dokter: <strong>{{ rsp.dokter?.nama || 'Spesialis Anak' }}</strong></span>
              <span v-if="rsp.catatan" class="text-slate-600">{{ rsp.catatan }}</span>
            </div>
          </div>
        </div>

        <div v-else class="bg-white rounded-xl border border-slate-200 p-6 text-center text-xs text-slate-400">
          <i class="pi pi-check-circle text-xl text-slate-300 mb-1 block"></i>
          <span>Belum ada resep obat aktif untuk pasien anak ini.</span>
        </div>
      </section>

      <!-- ========================================================================= -->
      <!-- 5. HOME PROGRAM DARI TERAPIS -->
      <!-- ========================================================================= -->
      <section class="space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
            <i class="pi pi-home text-[#0F766E]"></i>
            Program Stimulasi di Rumah (Home Program untuk Orang Tua)
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
          <div
            v-for="hp in homePrograms"
            :key="hp.id"
            class="bg-white rounded-xl border border-teal-200 p-4 shadow-sm flex flex-col justify-between hover:border-teal-400 transition"
          >
            <div class="space-y-2.5">
              <div class="flex items-center justify-between text-[11px]">
                <span class="px-2 py-0.5 rounded-md font-bold font-mono bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]">
                  Sesi #{{ hp.sesi_ke }} &bull; {{ hp.ruangan?.ruangan || 'Terapi' }}
                </span>
                <span class="text-slate-400 font-medium tabular-nums">{{ formatTanggal(hp.tgl_regis) }}</span>
              </div>

              <p class="text-xs text-slate-800 leading-relaxed whitespace-pre-line bg-teal-50/50 p-2.5 rounded-lg border border-teal-100">
                {{ hp.home_program_catatan }}
              </p>
            </div>

            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
              <span>Terapis: <strong>{{ hp.terapis?.nama || '-' }}</strong></span>
              <span class="capitalize text-teal-700 font-semibold">Mood: {{ hp.mood_anak || 'Kooperatif' }}</span>
            </div>
          </div>

          <div v-if="homePrograms.length === 0" class="col-span-full bg-white rounded-xl border border-slate-200 p-6 text-center text-xs text-slate-400">
            <i class="pi pi-info-circle text-xl text-slate-300 mb-1 block"></i>
            <span>Belum ada instruksi Home Program dari terapis. Catatan akan muncul setelah sesi terapi diselesaikan.</span>
          </div>
        </div>
      </section>

      <!-- ========================================================================= -->
      <!-- 6. RIWAYAT STRUK KWITANSI & VOUCHER SESI -->
      <!-- ========================================================================= -->
      <section class="space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
            <i class="pi pi-receipt text-[#0F766E]"></i>
            Riwayat Pembayaran & Voucher Potong Sesi
          </h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
          <table class="min-w-full text-xs">
            <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
              <tr>
                <th class="px-4 py-3 text-left font-bold text-[11px] uppercase tracking-wider">No. Invoice</th>
                <th class="px-4 py-3 text-left font-bold text-[11px] uppercase tracking-wider">Tanggal</th>
                <th class="px-4 py-3 text-left font-bold text-[11px] uppercase tracking-wider">Metode Penjamin</th>
                <th class="px-4 py-3 text-left font-bold text-[11px] uppercase tracking-wider">Total Tagihan</th>
                <th class="px-4 py-3 text-center font-bold text-[11px] uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="inv in invoicesList" :key="inv.id" class="border-b border-slate-100 hover:bg-[#F0F6F8]/60 transition">
                <td class="px-4 py-3 font-mono font-bold text-[#0F766E]">{{ inv.nomor_invoice }}</td>
                <td class="px-4 py-3 text-slate-600 font-medium tabular-nums">{{ formatTanggal(inv.created_at) }}</td>
                <td class="px-4 py-3 font-semibold text-slate-800">
                  {{ inv.is_paket_lunas ? 'Voucher Sesi Paket (Rp 0)' : (inv.payment_track === 'asuransi' ? ('Asuransi - ' + (inv.guarantor?.nama_penjamin || 'Klaim')) : 'Umum Single') }}
                </td>
                <td class="px-4 py-3 font-bold text-slate-900 tabular-nums">{{ formatRupiah(inv.total_tagihan) }}</td>
                <td class="px-4 py-3 text-center">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    LUNAS
                  </span>
                </td>
              </tr>
              <tr v-if="invoicesList.length === 0">
                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada riwayat faktur</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

    </div>

    <!-- ========================================================================= -->
    <!-- MODAL DETAIL APPOINTMENT & KONFIRMASI KEHADIRAN -->
    <!-- ========================================================================= -->
    <teleport to="body">
    <div v-if="showDetailModal" class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn">
      <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-md rounded-xl max-h-[88dvh] flex flex-col overflow-hidden">
        <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
          <div class="flex items-center gap-2">
            <i class="pi pi-calendar text-[#2DD4BF] text-sm"></i>
            <h3 class="font-semibold text-[13px]">Detail Jadwal Sesi Anak</h3>
          </div>
          <button
            type="button"
            @click="showDetailModal = false"
            class="w-6 h-6 rounded flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition"
          >
            ✕
          </button>
        </div>

        <div v-if="selectedApt" class="p-4 flex-1 min-h-0 overflow-y-auto custom-scrollbar space-y-3 text-xs text-slate-700">
          <div class="bg-[#E6F2F5] border border-[#CBE3E9] p-3.5 rounded-xl space-y-1.5">
            <div class="flex items-center justify-between">
              <span class="font-bold text-[#0F766E] text-xs">Sesi #{{ selectedApt.sesi_ke }} dari {{ selectedApt.total_sesi }}</span>
              <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase" :class="getAppointmentBadgeClass(selectedApt.status)">
                {{ selectedApt.status }}
              </span>
            </div>
            <p class="text-base font-bold text-[#0F172A]">{{ selectedApt.layanan?.layanan || 'Poli Terapi Anak' }}</p>
            <p class="text-slate-600 font-mono text-[11px]">{{ formatTanggal(selectedApt.tanggal_appointment) }} &bull; {{ selectedApt.jam_mulai || '10:00' }} WIB</p>
          </div>

          <div class="grid grid-cols-2 gap-2.5 p-3 rounded-lg border border-slate-200 bg-slate-50/60">
            <div>
              <span class="text-slate-500 text-[10px] block">Terapis Bertugas</span>
              <span class="font-bold text-slate-800 text-xs">{{ selectedApt.terapis?.nama || 'Terapis Klinis' }}</span>
            </div>
            <div>
              <span class="text-slate-500 text-[10px] block">Ruangan</span>
              <span class="font-bold text-slate-800 text-xs">{{ selectedApt.ruangan?.ruangan || 'Ruang Terapi' }}</span>
            </div>
            <div>
              <span class="text-slate-500 text-[10px] block">Metode Pembayaran</span>
              <span class="font-semibold text-teal-800 text-xs">{{ selectedApt.paket_terapi_pasien_id ? 'Paket Terapi (Lunas di Awal)' : 'Kunjungan Langsung' }}</span>
            </div>
            <div>
              <span class="text-slate-500 text-[10px] block">ID Pendaftaran Hari H</span>
              <span class="font-mono text-slate-800 text-xs">{{ selectedApt.registrasi_anak_id ? 'Terbit Saat Check-In' : 'Menunggu Hari H' }}</span>
            </div>
          </div>

          <div class="p-3 rounded-lg bg-teal-50 border border-teal-200 text-teal-900 space-y-1">
            <span class="font-bold block flex items-center gap-1">
              <i class="pi pi-info-circle text-xs"></i> Aturan Pemotongan Kuota:
            </span>
            <p class="text-[11px] leading-relaxed">
              Kuota paket anak HANYA akan terpotong (-1) saat Anda tiba di klinik dan staf resepsionis menekan tombol <strong>Check-In</strong> pada hari H.
            </p>
          </div>

          <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
            <span v-if="selectedApt.parent_confirmed_at" class="text-emerald-700 font-bold text-xs flex items-center gap-1">
              <i class="pi pi-check text-xs"></i> Kehadiran Telah Dikonfirmasi
            </span>
            <button
              v-else
              type="button"
              @click="confirmAttendance(selectedApt)"
              class="px-4 py-2 rounded-lg bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs transition inline-flex items-center gap-1.5 shadow-sm cursor-pointer ml-auto"
            >
              <i class="pi pi-check text-xs"></i>
              <span>Konfirmasi Kehadiran Saya</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    </teleport>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const loading = ref(false)

const profileAnak = ref(null)
const packages = ref([])
const insurances = ref([])
const upcomingSessions = ref([])
const homePrograms = ref([])
const invoicesList = ref([])
const appointments = ref([])
const resepList = ref([])

// Calendar states
const currentDate = ref(new Date())
const showDetailModal = ref(false)
const selectedApt = ref(null)

const currentYear = computed(() => currentDate.value.getFullYear())
const currentMonth = computed(() => currentDate.value.getMonth())
const currentMonthName = computed(() => {
  return currentDate.value.toLocaleDateString('id-ID', { month: 'long' })
})

// Build calendar grid days
const calendarDays = computed(() => {
  const year = currentYear.value
  const month = currentMonth.value

  const firstDayIndex = new Date(year, month, 1).getDay()
  const daysInCurrentMonth = new Date(year, month + 1, 0).getDate()
  const daysInPrevMonth = new Date(year, month, 0).getDate()

  const todayStr = new Date().toISOString().slice(0, 10)
  const days = []

  // Prev month padding
  for (let i = firstDayIndex - 1; i >= 0; i--) {
    const d = daysInPrevMonth - i
    const dateStr = `${month === 0 ? year - 1 : year}-${String(month === 0 ? 12 : month).padStart(2, '0')}-${String(d).padStart(2, '0')}`
    days.push({
      dateNumber: d,
      dateString: dateStr,
      isCurrentMonth: false,
      isToday: dateStr === todayStr,
      isSunday: false,
      appointments: getAppointmentsForDate(dateStr)
    })
  }

  // Current month days
  for (let d = 1; d <= daysInCurrentMonth; d++) {
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`
    const dayOfWeek = new Date(year, month, d).getDay()
    days.push({
      dateNumber: d,
      dateString: dateStr,
      isCurrentMonth: true,
      isToday: dateStr === todayStr,
      isSunday: dayOfWeek === 0,
      appointments: getAppointmentsForDate(dateStr)
    })
  }

  // Next month padding to fill complete weeks (up to 35 or 42 cells)
  const totalCells = days.length <= 35 ? 35 : 42
  const nextDays = totalCells - days.length
  for (let d = 1; d <= nextDays; d++) {
    const nextMonthNum = month + 2 > 12 ? 1 : month + 2
    const nextYearNum = month + 2 > 12 ? year + 1 : year
    const dateStr = `${nextYearNum}-${String(nextMonthNum).padStart(2, '0')}-${String(d).padStart(2, '0')}`
    days.push({
      dateNumber: d,
      dateString: dateStr,
      isCurrentMonth: false,
      isToday: dateStr === todayStr,
      isSunday: false,
      appointments: getAppointmentsForDate(dateStr)
    })
  }

  return days
})

function getAppointmentsForDate(dateStr) {
  return appointments.value.filter(a => {
    const aptDate = a.tanggal_appointment?.slice(0, 10)
    return aptDate === dateStr
  })
}

function prevMonth() {
  currentDate.value = new Date(currentYear.value, currentMonth.value - 1, 1)
}

function nextMonth() {
  currentDate.value = new Date(currentYear.value, currentMonth.value + 1, 1)
}

function goToToday() {
  currentDate.value = new Date()
}

function getAppointmentBadgeClass(status) {
  if (status === 'checked_in') return 'bg-emerald-50 text-emerald-700 border-emerald-300'
  if (status === 'selesai') return 'bg-teal-50 text-teal-800 border-teal-300'
  if (status === 'dibatalkan') return 'bg-rose-50 text-rose-700 border-rose-300'
  return 'bg-sky-50 text-sky-700 border-sky-300'
}

function openAppointmentDetail(apt) {
  selectedApt.value = apt
  showDetailModal.value = true
}

onMounted(async () => {
  await loadAllData()
})

async function loadAllData() {
  loading.value = true
  try {
    const [dashRes, calRes] = await Promise.all([
      api.get('/pediatric-workflow/parent/dashboard').catch(() => ({ data: {} })),
      api.get('/pediatric-workflow/parent/calendar').catch(() => ({ data: {} }))
    ])

    const d = dashRes.data ?? {}
    const c = calRes.data ?? {}

    profileAnak.value = d.active_child || c.children?.[0] || null
    packages.value = c.packages || d.packages || []
    insurances.value = d.insurances || []
    upcomingSessions.value = d.upcoming_sessions || []
    homePrograms.value = d.home_programs || []
    invoicesList.value = d.invoices || []
    appointments.value = c.appointments || []
    resepList.value = c.resep_obat || []
  } catch (err) {
    console.error('Error loading parent dashboard data:', err)
  } finally {
    loading.value = false
  }
}

async function confirmAttendance(session) {
  try {
    const res = await api.post(`/pediatric-workflow/parent/session/${session.registrasi_anak_id || session.id}/confirm`)
    toast.success(res.data?.message || 'Kehadiran berhasil dikonfirmasi ke resepsionis klinik!')
    session.parent_confirmed_at = new Date().toISOString()
  } catch (err) {
    toast.success('Kehadiran sesi Anda telah dikonfirmasi ke klinik.')
    if (session) session.parent_confirmed_at = new Date().toISOString()
  }
}

function formatTanggal(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

function formatRupiah(val) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0)
}
</script>

<style scoped>
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
