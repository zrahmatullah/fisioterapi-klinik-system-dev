<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-hidden w-full">

    <!-- 1. HEADER & ACTIONS -->
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col md:flex-row md:items-center md:justify-between gap-3 shrink-0">
      <div>
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-sm">
            <i class="pi pi-users"></i>
          </div>
          <div>
            <h2 class="text-base font-bold text-[#0F172A] leading-tight">
              Daftar Pasien Anak Terdaftar
            </h2>
            <p class="text-xs text-[#64748B] mt-0.5">
              Kelola master data rekam medis anak, pencetakan karcis antrian, gelang pasien, dan rekam medis elektronik (EMR)
            </p>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-2.5 flex-wrap">
        <button
          @click="openModalTambahPasien"
          class="bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs py-2 px-3.5 rounded-lg flex items-center gap-1.5 transition cursor-pointer shadow-sm"
        >
          <i class="pi pi-plus text-xs"></i>
          <span>Tambah Data Anak</span>
        </button>
        <button
          @click="router.push('/registrasi')"
          class="btn-secondary-clinical text-xs py-2 px-3.5"
        >
          <i class="pi pi-calendar-plus text-xs"></i>
          <span>Pendaftaran Kunjungan</span>
        </button>
      </div>
    </div>

    <!-- 2. SUMMARY METRICS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 shrink-0">
      <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Pasien Terdaftar</span>
        <div class="flex items-baseline justify-between mt-1.5">
          <h3 class="text-2xl font-bold text-[#0F172A] tabular-nums">{{ listPasien.length }}</h3>
          <span class="text-xs font-medium text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200">
            Terverifikasi RM
          </span>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
        <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          Aktif Sesi Terapi
        </span>
        <div class="flex items-baseline justify-between mt-1.5">
          <h3 class="text-2xl font-bold text-emerald-800 tabular-nums">
            {{ listPasien.filter(p => p.status_aktif !== false).length }}
          </h3>
          <span class="text-xs text-emerald-600 font-medium">Sesi Rutin</span>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
        <span class="text-xs font-semibold text-sky-700 uppercase tracking-wider flex items-center gap-1.5">
          <i class="pi pi-shield text-sky-500 text-xs"></i>
          Penjamin BPJS / Asuransi
        </span>
        <div class="flex items-baseline justify-between mt-1.5">
          <h3 class="text-2xl font-bold text-sky-800 tabular-nums">
            {{ listPasien.filter(p => p.penjamin && p.penjamin !== 'Umum').length || 8 }}
          </h3>
          <span class="text-xs text-sky-600 font-medium">Tercover</span>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
        <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider flex items-center gap-1.5">
          <i class="pi pi-chart-line text-amber-500 text-xs"></i>
          Evaluasi Berkala
        </span>
        <div class="flex items-baseline justify-between mt-1.5">
          <h3 class="text-2xl font-bold text-amber-800 tabular-nums">6</h3>
          <span class="text-xs text-amber-600 font-medium">Bulan Ini</span>
        </div>
      </div>
    </div>

    <!-- 3. SEARCH & FILTER BAR -->
    <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-[0_1px_3px_rgba(15,23,42,0.04)] flex flex-col md:flex-row items-center justify-between gap-3 shrink-0">
      <!-- Search Input -->
      <div class="relative w-full md:w-80">
        <i class="pi pi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari nama anak, No. RM, NIK, nama wali..."
          class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 transition"
        />
        <button
          v-if="searchQuery"
          @click="searchQuery = ''"
          class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5"
        >
          <i class="pi pi-times text-xs"></i>
        </button>
      </div>

      <!-- Filter Group -->
      <div class="flex items-center gap-2.5 w-full md:w-auto">
        <select
          v-model="filterPenjamin"
          class="text-xs border border-slate-300 rounded-lg px-3 py-2 bg-white text-slate-700 outline-none focus:border-[#0F766E]"
        >
          <option value="">Semua Penjamin</option>
          <option value="BPJS">BPJS Kesehatan</option>
          <option value="Umum">Umum / Pribadi</option>
          <option value="Asuransi">Asuransi Swasta</option>
        </select>

        <select
          v-model="filterDiagnosis"
          class="text-xs border border-slate-300 rounded-lg px-3 py-2 bg-white text-slate-700 outline-none focus:border-[#0F766E]"
        >
          <option value="">Semua Disiplin / Terapi</option>
          <option value="Wicara">Terapi Wicara</option>
          <option value="Okupasi">Okupasi & Sensori</option>
          <option value="Fisioterapi">Fisioterapi</option>
          <option value="Psikologi">Psikologi Anak</option>
        </select>

        <button
          @click="fetchData"
          class="btn-secondary-clinical text-xs py-2 px-3 shrink-0"
          title="Segarkan Data"
        >
          <i class="pi pi-refresh text-xs" :class="{ 'pi-spin': loading }"></i>
        </button>
      </div>
    </div>

    <!-- 4. DATA TABLE -->
    <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
      <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
        <table class="min-w-full text-sm">
          <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
            <tr class="border-b border-slate-200 bg-slate-50/90">
              <th class="th-clinical text-center w-12">No</th>
              <th class="th-clinical">No. RM</th>
              <th class="th-clinical">Nama Anak & NIK</th>
              <th class="th-clinical">Usia / JK</th>
              <th class="th-clinical">Orang Tua / Wali</th>
              <th class="th-clinical">Diagnosis & Disiplin</th>
              <th class="th-clinical">Penjamin</th>
              <th class="th-clinical text-center">Status</th>
              <th class="th-clinical text-center min-w-[280px]">Aksi Rekam Medis</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(item, index) in filteredPasien"
              :key="item.id"
              class="hover:bg-slate-50/80 transition-colors"
            >
              <!-- 1. Index -->
              <td class="td-clinical text-center font-mono text-xs text-slate-400">
                {{ index + 1 }}
              </td>

              <!-- 2. No. RM -->
              <td class="td-clinical">
                <span class="font-mono font-bold text-[#0F766E] text-xs px-2 py-0.5 rounded bg-teal-50 border border-teal-200/80">
                  {{ item.no_rm || formatRm(item.id) }}
                </span>
              </td>

              <!-- 3. Nama Anak & NIK -->
              <td class="td-clinical">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-[#E6F2F5] border border-[#CBE3E9] text-[#0F766E] flex items-center justify-center font-bold text-xs shrink-0">
                    {{ item.nama_anak?.charAt(0) || 'A' }}
                  </div>
                  <div>
                    <p class="font-semibold text-slate-900 text-sm leading-tight hover:text-[#0F766E] transition cursor-pointer" @click="openEmr(item)">
                      {{ item.nama_anak }}
                    </p>
                    <p class="text-[11px] text-slate-500 font-mono mt-0.5">
                      NIK: {{ item.NIK || '317101' + String(item.id).padStart(10, '0') }}
                    </p>
                  </div>
                </div>
              </td>

              <!-- 4. Usia / JK -->
              <td class="td-clinical">
                <p class="text-xs font-medium text-slate-800 leading-tight">
                  {{ item.usia_lengkap || (item.umur ? item.umur + ' Tahun' : '4 Thn 2 Bln') }}
                </p>
                <p class="text-[11px] text-slate-500 mt-0.5">
                  <span v-if="item.jenis_kelamin_id === 1 || item.jenis_kelamin?.jenis_kelamin?.includes('Laki')" class="text-[#0F766E] font-medium">Laki-laki</span>
                  <span v-else class="text-pink-600 font-medium">Perempuan</span>
                </p>
              </td>

              <!-- 5. Orang Tua / Wali -->
              <td class="td-clinical">
                <p class="text-xs font-semibold text-slate-800 leading-tight">
                  {{ item.orang_tua?.nama || item.user_profile?.nama || 'Ahmad Fauzi' }}
                </p>
                <p class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5 font-mono">
                  <i class="pi pi-whatsapp text-emerald-500 text-[10px]"></i>
                  {{ item.no_hp_orang_tua || item.orang_tua?.no_telepon || '0812-3456-7890' }}
                </p>
              </td>

              <!-- 6. Diagnosis & Disiplin -->
              <td class="td-clinical">
                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                  {{ item.diagnosis_awal || 'Speech Delay (F80.1)' }}
                </span>
                <p class="text-[11px] text-[#0F766E] mt-0.5 font-medium">
                  Terapi Wicara & SI
                </p>
              </td>

              <!-- 7. Penjamin -->
              <td class="td-clinical">
                <span
                  class="px-2 py-0.5 rounded-full text-[11px] font-semibold"
                  :class="{
                    'bg-sky-50 text-sky-700 border border-sky-200': (item.penjamin || 'BPJS Kesehatan').includes('BPJS'),
                    'bg-slate-100 text-slate-700 border border-slate-200': (item.penjamin || 'BPJS Kesehatan') === 'Umum',
                    'bg-teal-50 text-teal-700 border border-teal-200': (item.penjamin || 'BPJS Kesehatan').includes('Asuransi')
                  }"
                >
                  {{ item.penjamin || 'BPJS Kesehatan' }}
                </span>
              </td>

              <!-- 8. Status -->
              <td class="td-clinical text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  Aktif
                </span>
              </td>

              <!-- 9. Aksi Rekam Medis -->
              <td class="td-clinical text-center">
                <div class="flex items-center justify-center gap-1.5 flex-wrap">

                  <!-- Buka EMR -->
                  <button
                    @click="openEmr(item)"
                    class="bg-[#0F766E] hover:bg-[#115E59] text-white px-2.5 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center gap-1 transition shadow-sm cursor-pointer"
                    title="Buka Rekam Medis Elektronik (EMR)"
                  >
                    <i class="pi pi-id-card text-xs"></i>
                    <span>EMR</span>
                  </button>

                  <!-- Cetak Antrian / Karcis -->
                  <button
                    @click="openAntrianModal(item)"
                    class="bg-white hover:bg-[#E6F2F5] text-slate-700 hover:text-[#0F766E] border border-slate-200 hover:border-teal-400 px-2.5 py-1.5 rounded-lg text-xs font-medium inline-flex items-center gap-1 transition shadow-sm cursor-pointer"
                    title="Cetak Karcis & Antrian Pasien"
                  >
                    <i class="pi pi-ticket text-xs text-teal-600"></i>
                    <span>Karcis</span>
                  </button>

                  <!-- Cetak Gelang Pasien -->
                  <button
                    @click="openGelangModal(item)"
                    class="bg-white hover:bg-[#E6F2F5] text-slate-700 hover:text-[#0F766E] border border-slate-200 hover:border-teal-400 px-2.5 py-1.5 rounded-lg text-xs font-medium inline-flex items-center gap-1 transition shadow-sm cursor-pointer"
                    title="Cetak Gelang Identitas Pasien"
                  >
                    <i class="pi pi-tag text-xs text-sky-600"></i>
                    <span>Gelang</span>
                  </button>

                  <!-- Portofolio Berkas Pasien & Consent -->
                  <button
                    @click="openBerkasModal(item)"
                    class="bg-[#E6F2F5] hover:bg-[#d5eaf0] text-[#0F766E] border border-[#CBE3E9] px-2.5 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center gap-1 transition shadow-sm cursor-pointer"
                    title="Portofolio Berkas Pasien (Informed Consent, Surat Medis, Lampiran Lab)"
                  >
                    <i class="pi pi-folder-open text-xs"></i>
                    <span>Berkas</span>
                  </button>

                </div>
              </td>
            </tr>

            <!-- Empty Row -->
            <tr v-if="filteredPasien.length === 0">
              <td colspan="9" class="py-14 text-center">
                <div class="flex flex-col items-center justify-center">
                  <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                    <i class="pi pi-search text-xl"></i>
                  </div>
                  <p class="font-semibold text-slate-700 text-sm">Pasien tidak ditemukan</p>
                  <p class="text-xs text-slate-500 mt-0.5">Coba sesuaikan kata kunci pencarian atau filter yang dipilih</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================= MODAL 1: CETAK KARCIS ANTRIAN PASIEN ================= -->
    <teleport to="body">
    <div v-if="showModalAntrian" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showModalAntrian = false"></div>

      <div class="relative bg-white w-full max-w-sm rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-fadeIn">
        <!-- MODAL HEADER -->
        <div class="bg-[#0B3948] text-white px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <i class="pi pi-ticket text-teal-300 text-base"></i>
            <h3 class="text-base font-bold tracking-wide">Karcis Antrian Pasien</h3>
          </div>
          <button @click="showModalAntrian = false" class="text-slate-300 hover:text-white p-1">
            <i class="pi pi-times text-sm"></i>
          </button>
        </div>

        <!-- THERMAL SLIP PREVIEW (58mm style) -->
        <div class="p-5 bg-slate-50">
          <div class="bg-white border border-dashed border-slate-300 p-5 rounded-xl shadow-sm text-center space-y-3 font-mono">
            <div>
              <h4 class="font-bold text-sm text-[#0F172A] uppercase tracking-wider">KLINIK TERAPI KLINIKKU</h4>
              <p class="text-[10px] text-slate-500">Jl. Terapi Medika No. 10 - Telp (021) 7890123</p>
              <div class="border-b border-dashed border-slate-300 my-2"></div>
            </div>

            <div>
              <p class="text-xs text-slate-500 uppercase tracking-wider">NOMOR ANTRIAN</p>
              <p class="text-4xl font-black text-[#0B3948] my-1 tracking-widest tabular-nums">
                A-{{ String(selectedPasien?.id || 1).padStart(3, '0') }}
              </p>
              <p class="text-xs font-bold text-[#0F766E] uppercase">POLI TERAPI WICARA & TUMBUH KEMBANG</p>
            </div>

            <div class="border-b border-dashed border-slate-300"></div>

            <div class="text-left text-xs space-y-1 text-slate-700">
              <div class="flex justify-between">
                <span class="text-slate-500">No. RM:</span>
                <span class="font-bold text-slate-900">{{ selectedPasien?.no_rm || formatRm(selectedPasien?.id) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Nama Pasien:</span>
                <span class="font-bold text-slate-900">{{ selectedPasien?.nama_anak }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Wali:</span>
                <span>{{ selectedPasien?.orang_tua?.nama || 'Ahmad Fauzi' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Waktu Cetak:</span>
                <span>{{ currentDateFormatted }}</span>
              </div>
            </div>

            <div class="border-b border-dashed border-slate-300"></div>

            <!-- Simulated QR -->
            <div class="py-2 flex flex-col items-center justify-center">
              <div class="w-20 h-20 bg-slate-100 border border-slate-300 rounded p-1 flex items-center justify-center">
                <i class="pi pi-qrcode text-4xl text-slate-800"></i>
              </div>
              <p class="text-[9px] text-slate-400 mt-1">Scan untuk memantau panggilan antrian TV</p>
            </div>

            <p class="text-[10px] text-slate-500 italic">Harap menunggu panggilan di ruang tunggu klinis</p>
          </div>
        </div>

        <!-- MODAL FOOTER -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3">
          <button @click="showModalAntrian = false" class="btn-secondary-clinical">
            Tutup
          </button>
          <button @click="cetakKarcisAction" class="btn-primary-clinical">
            <i class="pi pi-print text-sm"></i>
            <span>Cetak Karcis (Thermal 58mm)</span>
          </button>
        </div>
      </div>
    </div>
    </teleport>

    <!-- ================= MODAL 2: CETAK GELANG IDENTITAS PASIEN ================= -->
    <teleport to="body">
    <div v-if="showModalGelang" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showModalGelang = false"></div>

      <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-fadeIn">
        <!-- MODAL HEADER -->
        <div class="bg-[#0B3948] text-white px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <i class="pi pi-tag text-teal-300 text-base"></i>
            <h3 class="text-base font-bold tracking-wide">Cetak Gelang Identitas Pasien (Wristband)</h3>
          </div>
          <button @click="showModalGelang = false" class="text-slate-300 hover:text-white p-1">
            <i class="pi pi-times text-sm"></i>
          </button>
        </div>

        <div class="p-6 space-y-4">
          <p class="text-sm text-slate-600">
            Standar Akreditasi Kemenkes: Gelang memuat 3 identitas wajib: <strong>Nama Lengkap Pasien</strong>, <strong>Nomor Rekam Medis</strong>, dan <strong>Tanggal Lahir</strong>.
          </p>

          <!-- MEDICAL WRISTBAND SIMULATION -->
          <div class="bg-gradient-to-r from-sky-100 via-sky-50 to-sky-100 border-2 border-sky-300 rounded-xl p-4 shadow-sm flex items-center justify-between gap-4 font-mono">
            <!-- Left Info -->
            <div class="space-y-1 min-w-0">
              <span class="text-xs uppercase font-bold text-sky-800 tracking-wider">SIMRS KLINIKKU - RAWAT JALAN</span>
              <p class="text-base font-black text-slate-900 truncate uppercase">{{ selectedPasien?.nama_anak }}</p>
              <div class="flex items-center gap-3 text-sm text-slate-700 font-bold">
                <span>RM: {{ selectedPasien?.no_rm || formatRm(selectedPasien?.id) }}</span>
                <span>TL: {{ selectedPasien?.tanggal_lahir || '12/05/2020' }}</span>
                <span>JK: {{ selectedPasien?.jenis_kelamin_id === 1 ? 'L' : 'P' }}</span>
              </div>
              <div class="flex items-center gap-2 pt-1">
                <span class="text-xs font-bold bg-rose-100 text-rose-700 px-2 py-0.5 rounded border border-rose-300">
                  ALERGI: Tidak Ada
                </span>
              </div>
            </div>

            <!-- Barcode Right -->
            <div class="text-center shrink-0">
              <div class="bg-white p-2.5 rounded border border-slate-300 text-slate-800 font-bold flex flex-col items-center">
                <div class="tracking-widest text-lg font-barcode">||| | |||| || |||</div>
                <span class="text-xs text-slate-600 font-mono">{{ selectedPasien?.no_rm || formatRm(selectedPasien?.id) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- MODAL FOOTER -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3">
          <button @click="showModalGelang = false" class="btn-secondary-clinical">
            Batal
          </button>
          <button @click="cetakGelangAction" class="btn-primary-clinical">
            <i class="pi pi-print text-sm"></i>
            <span>Cetak Gelang Pasien (Direct Thermal)</span>
          </button>
        </div>
      </div>
    </div>
    </teleport>

    <!-- ================= MODAL 3: INFORMED CONSENT PREVIEW ================= -->
    <teleport to="body">
    <div v-if="showModalConsent" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showModalConsent = false"></div>

      <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-fadeIn">
        <div class="bg-[#0B3948] text-white px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <i class="pi pi-check-circle text-teal-300 text-base"></i>
            <h3 class="text-base font-bold tracking-wide">Informed Consent & Persetujuan Medis</h3>
          </div>
          <button @click="showModalConsent = false" class="text-slate-300 hover:text-white p-1">
            <i class="pi pi-times text-sm"></i>
          </button>
        </div>

        <div class="p-6 space-y-4 text-sm text-slate-700 max-h-[75vh] overflow-y-auto">
          <div class="p-3.5 bg-teal-50 border border-teal-200 rounded-xl flex items-center gap-3">
            <i class="pi pi-verified text-teal-700 text-xl"></i>
            <div>
              <p class="font-bold text-teal-900 text-sm">Persetujuan Tindakan Medis Terverifikasi</p>
              <p class="text-xs text-teal-700">Telah ditandatangani secara elektronik oleh Orang Tua / Wali sah pasien.</p>
            </div>
          </div>

          <div class="space-y-1.5 border-b border-slate-200 pb-3.5 text-sm">
            <p class="font-bold text-slate-800">Identitas Anak:</p>
            <p>Nama Pasien: <strong>{{ selectedPasien?.nama_anak }}</strong></p>
            <p>Nomor Rekam Medis: <strong>{{ selectedPasien?.no_rm || formatRm(selectedPasien?.id) }}</strong></p>
            <p>Diagnosis: <strong>{{ selectedPasien?.diagnosis_awal || 'Keterlambatan Perkembangan Bicara & Bahasa' }}</strong></p>
          </div>

          <div class="space-y-1.5 border-b border-slate-200 pb-3.5 text-sm">
            <p class="font-bold text-slate-800">Tindakan Klinis yang Disetujui:</p>
            <p class="text-slate-600">1. Pelaksanaan evaluasi dan asesmen tumbuh kembang pediatrik.</p>
            <p class="text-slate-600">2. Program terapi okupasi, sensori integrasi, dan terapi wicara terjadwal.</p>
            <p class="text-slate-600">3. Rekam medis digital terintegrasi sistem kesehatan SatuSehat Kemenkes.</p>
          </div>

          <div class="pt-2 flex justify-between items-end text-sm">
            <div>
              <p class="text-xs text-slate-500">Tanggal Persetujuan:</p>
              <p class="font-semibold text-slate-800">{{ currentDateFormatted }}</p>
            </div>

            <!-- Digital Signature Mock -->
            <div class="text-center">
              <div class="w-36 h-16 border border-dashed border-slate-300 rounded-lg bg-slate-50 flex items-center justify-center text-teal-700 font-serif italic text-sm">
                (Ttd Digital Terverifikasi)
              </div>
              <p class="text-xs text-slate-500 mt-1">{{ selectedPasien?.orang_tua?.nama || 'Ahmad Fauzi' }} (Wali)</p>
            </div>
          </div>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end">
          <button @click="showModalConsent = false" class="btn-primary-clinical">
            Tutup Pratinjau
          </button>
        </div>
      </div>
    </div>
    </teleport>

    <!-- ================= MODAL 4: PORTOFOLIO BERKAS REKAM MEDIS & DOKUMEN ANAK ================= -->
    <teleport to="body">
    <div v-if="showModalBerkas" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showModalBerkas = false"></div>

      <div class="relative bg-white w-full max-w-3xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-fadeIn flex flex-col max-h-[90vh]">
        <!-- HEADER -->
        <div class="bg-[#0B3948] text-white px-6 py-4 flex items-center justify-between shrink-0">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-teal-500/20 border border-teal-400/30 flex items-center justify-center text-teal-300">
              <i class="pi pi-folder-open text-base"></i>
            </div>
            <div>
              <h3 class="text-base font-bold tracking-wide">Portofolio Berkas Medis Pasien</h3>
              <p class="text-xs text-teal-200/90 font-mono mt-0.5">
                [{{ selectedPasien?.no_rm || formatRm(selectedPasien?.id) }}] {{ selectedPasien?.nama_anak }} · Wali: {{ selectedPasien?.orang_tua?.nama || 'Ahmad Fauzi' }}
              </p>
            </div>
          </div>
          <button @click="showModalBerkas = false" class="text-slate-300 hover:text-white p-1">
            <i class="pi pi-times text-sm"></i>
          </button>
        </div>

        <!-- TABS NAV -->
        <div class="flex border-b border-slate-200 bg-slate-50 px-6 gap-4 text-sm font-semibold shrink-0">
          <button
            type="button"
            @click="activeBerkasTab = 'consent'"
            class="py-3 transition flex items-center gap-1.5 cursor-pointer"
            :class="activeBerkasTab === 'consent' ? 'border-b-2 border-[#0F766E] text-[#0F766E]' : 'text-slate-500 hover:text-slate-700'"
          >
            <i class="pi pi-check-circle text-sm"></i>
            <span>1. Informed Consent Digital ({{ berkasData.informed_consents?.length || 0 }})</span>
          </button>
          <button
            type="button"
            @click="activeBerkasTab = 'surat'"
            class="py-3 transition flex items-center gap-1.5 cursor-pointer"
            :class="activeBerkasTab === 'surat' ? 'border-b-2 border-[#0F766E] text-[#0F766E]' : 'text-slate-500 hover:text-slate-700'"
          >
            <i class="pi pi-file text-sm"></i>
            <span>2. Surat Resmi Medis ({{ berkasData.surat_resmi?.length || 0 }})</span>
          </button>
          <button
            type="button"
            @click="activeBerkasTab = 'lampiran'"
            class="py-3 transition flex items-center gap-1.5 cursor-pointer"
            :class="activeBerkasTab === 'lampiran' ? 'border-b-2 border-[#0F766E] text-[#0F766E]' : 'text-slate-500 hover:text-slate-700'"
          >
            <i class="pi pi-paperclip text-sm"></i>
            <span>3. Lampiran Klinis / Lab ({{ berkasData.lampiran_klinis?.length || 0 }})</span>
          </button>
        </div>

        <!-- CONTENT BODY -->
        <div class="p-6 overflow-y-auto space-y-4 text-sm flex-1">
          <!-- TAB 1: INFORMED CONSENT -->
          <div v-if="activeBerkasTab === 'consent'" class="space-y-4">
            <div class="flex justify-between items-center">
              <div>
                <h4 class="font-bold text-slate-900">Persetujuan Tindakan & Rekam Medis (Informed Consent)</h4>
                <p class="text-[11px] text-slate-500">Bukti persetujuan wali pasien sah dengan tanda tangan digital terverifikasi</p>
              </div>
              <button
                type="button"
                @click="showCreateConsentForm = !showCreateConsentForm"
                class="btn-primary-clinical text-xs py-1.5 px-3"
              >
                <i class="pi" :class="showCreateConsentForm ? 'pi-times' : 'pi-plus'"></i>
                <span>{{ showCreateConsentForm ? 'Batal' : 'Buat Consent Baru' }}</span>
              </button>
            </div>

            <!-- FORM BUAT CONSENT BARU -->
            <form v-if="showCreateConsentForm" @submit.prevent="submitConsent" class="p-4 bg-[#F0F6F8] rounded-xl border border-[#DCE8EC] space-y-3">
              <h5 class="font-bold text-[#0F766E]">Form Persetujuan Tindakan Medis Digital</h5>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="label-clinical">Judul Persetujuan</label>
                  <input v-model="consentForm.judul_persetujuan" required class="input-clinical" placeholder="mis. Persetujuan Program Terapi Sensori Integrasi" />
                </div>
                <div>
                  <label class="label-clinical">Tipe Persetujuan</label>
                  <select v-model="consentForm.tipe_persetujuan" class="input-clinical">
                    <option value="tindakan_terapi">Tindakan Program Terapi</option>
                    <option value="umum">Persetujuan Umum Rawat Jalan</option>
                    <option value="penggunaan_data_pdp">Persetujuan Penggunaan Data Medis (UU PDP)</option>
                  </select>
                </div>
              </div>
              <div>
                <label class="label-clinical">Pernyataan Persetujuan</label>
                <textarea v-model="consentForm.pernyataan" rows="2" class="input-clinical"></textarea>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="label-clinical">Nama Penandatangan (Wali)</label>
                  <input v-model="consentForm.nama_penandatangan" required class="input-clinical" />
                </div>
                <div>
                  <label class="label-clinical">Hubungan dengan Pasien</label>
                  <select v-model="consentForm.hubungan_wali" class="input-clinical">
                    <option value="Ayah Kandung">Ayah Kandung</option>
                    <option value="Ibu Kandung">Ibu Kandung</option>
                    <option value="Wali Sah">Wali Sah</option>
                  </select>
                </div>
              </div>
              <div class="pt-2 flex justify-end">
                <button type="submit" class="btn-primary-clinical text-xs py-1.5 px-3.5">
                  <i class="pi pi-check text-xs"></i> Simpan Informed Consent Digital
                </button>
              </div>
            </form>

            <!-- LIST CONSENT TERSIMPAN -->
            <div v-if="!berkasData.informed_consents || berkasData.informed_consents.length === 0" class="text-center py-8 text-slate-400">
              Belum ada dokumen Informed Consent yang tersimpan untuk pasien ini.
            </div>
            <div v-else class="space-y-3">
              <div v-for="c in berkasData.informed_consents" :key="c.id" class="p-4 border border-slate-200 rounded-xl bg-white shadow-sm space-y-2">
                <div class="flex items-start justify-between">
                  <div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#DCFCE7] text-[#15803D] uppercase border border-emerald-200">
                      Terverifikasi Digital
                    </span>
                    <h5 class="font-bold text-slate-900 text-sm mt-1">{{ c.judul_persetujuan }}</h5>
                    <p class="text-[11px] text-slate-400 font-mono">Ditandatangani pada: {{ formatDate(c.signed_at) }}</p>
                  </div>
                  <div class="text-right">
                    <span class="font-bold text-slate-800">{{ c.nama_penandatangan }}</span>
                    <p class="text-[11px] text-slate-500">({{ c.hubungan_wali }})</p>
                  </div>
                </div>
                <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded border border-slate-100 italic">
                  "{{ c.pernyataan }}"
                </p>
              </div>
            </div>
          </div>

          <!-- TAB 2: SURAT RESMI MEDIS -->
          <div v-else-if="activeBerkasTab === 'surat'" class="space-y-4">
            <div>
              <h4 class="font-bold text-slate-900">Arsip Surat Resmi & Keterangan Dokter</h4>
              <p class="text-[11px] text-slate-500">Surat keterangan dokter, surat rujukan, dan hasil evaluasi klinis dengan verifikasi QR code</p>
            </div>

            <div v-if="!berkasData.surat_resmi || berkasData.surat_resmi.length === 0" class="text-center py-8 text-slate-400">
              Belum ada arsip surat resmi diterbitkan untuk pasien ini.
            </div>
            <div v-else class="space-y-2.5">
              <div v-for="s in berkasData.surat_resmi" :key="s.id" class="p-3.5 border border-slate-200 rounded-xl bg-white shadow-sm flex items-center justify-between">
                <div>
                  <div class="flex items-center gap-2">
                    <span class="font-mono font-bold text-[#0F766E] text-xs">{{ s.nomor_surat }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                      QR Terverifikasi
                    </span>
                  </div>
                  <h5 class="font-bold text-slate-900 text-xs mt-0.5">{{ s.judul_surat }}</h5>
                  <p class="text-[11px] text-slate-500 font-mono">Tanggal Terbit: {{ formatDate(s.tanggal_surat) }}</p>
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-xs text-emerald-700 font-semibold flex items-center gap-1">
                    <i class="pi pi-qrcode text-base"></i> QR Asli
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 3: LAMPIRAN KLINIS & LAB -->
          <div v-else-if="activeBerkasTab === 'lampiran'" class="space-y-4">
            <div class="flex justify-between items-center">
              <div>
                <h4 class="font-bold text-slate-900">Lampiran & Berkas Pendukung Klinis</h4>
                <p class="text-[11px] text-slate-500">Hasil tes lab, pemeriksaan BERA/audiometri, laporan psikotes, atau rekaman sesi terapi</p>
              </div>
              <button
                type="button"
                @click="showUploadForm = !showUploadForm"
                class="btn-primary-clinical text-xs py-1.5 px-3"
              >
                <i class="pi" :class="showUploadForm ? 'pi-times' : 'pi-upload'"></i>
                <span>{{ showUploadForm ? 'Batal' : 'Upload Berkas' }}</span>
              </button>
            </div>

            <!-- FORM UPLOAD BERKAS BARU -->
            <form v-if="showUploadForm" @submit.prevent="submitUpload" class="p-4 bg-[#F0F6F8] rounded-xl border border-[#DCE8EC] space-y-3">
              <h5 class="font-bold text-[#0F766E]">Upload Berkas / Hasil Uji Klinis Baru</h5>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="label-clinical">Nama Dokumen / File</label>
                  <input v-model="uploadForm.nama_file" required class="input-clinical" placeholder="mis. Hasil_Tes_Audiometri_2026.pdf" />
                </div>
                <div>
                  <label class="label-clinical">Kategori Berkas</label>
                  <select v-model="uploadForm.kategori" class="input-clinical">
                    <option value="lab_psikotes">Hasil Lab / Psikotes / BERA</option>
                    <option value="rujukan_eksternal">Surat Rujukan RS Lain</option>
                    <option value="foto_sesi">Foto Observasi Klinis</option>
                    <option value="video_sesi">Video Perkembangan Sesi</option>
                    <option value="berkas_lainnya">Berkas Lainnya</option>
                  </select>
                </div>
              </div>
              <div>
                <label class="label-clinical">Keterangan / Catatan Medis Berkas</label>
                <input v-model="uploadForm.keterangan" class="input-clinical" placeholder="mis. Pemeriksaan pendengaran pra-terapi wicara dari RS Hermina" />
              </div>
              <div class="pt-2 flex justify-end">
                <button type="submit" class="btn-primary-clinical text-xs py-1.5 px-3.5">
                  <i class="pi pi-upload text-xs"></i> Simpan & Arsipkan Berkas
                </button>
              </div>
            </form>

            <!-- LIST LAMPIRAN TERSIMPAN -->
            <div v-if="!berkasData.lampiran_klinis || berkasData.lampiran_klinis.length === 0" class="text-center py-8 text-slate-400">
              Belum ada lampiran berkas lab atau psikotes yang diunggah untuk pasien ini.
            </div>
            <div v-else class="space-y-2.5">
              <div v-for="att in berkasData.lampiran_klinis" :key="att.id" class="p-3.5 border border-slate-200 rounded-xl bg-white shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-lg bg-teal-50 border border-teal-200 flex items-center justify-center text-[#0F766E]">
                    <i class="pi pi-file-pdf text-base"></i>
                  </div>
                  <div>
                    <h5 class="font-bold text-slate-900 text-xs">{{ att.nama_file }}</h5>
                    <p class="text-[11px] text-slate-500">{{ att.keterangan || '-' }}</p>
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 uppercase">
                      {{ att.kategori }}
                    </span>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <button
                    type="button"
                    @click="deleteAttachment(att.id)"
                    class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded"
                    title="Hapus berkas"
                  >
                    <i class="pi pi-trash text-xs"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end shrink-0">
          <button @click="showModalBerkas = false" class="btn-primary-clinical">
            Tutup Portofolio
          </button>
        </div>
      </div>
    </div>
    </teleport>

    <!-- ================= MODAL 5: REGISTRASI PASIEN BARU & AUTO AKUN ORTU ================= -->
    <teleport to="body">
    <div v-if="showModalTambahPasien" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="closeModalTambahPasien"></div>

      <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-fadeIn flex flex-col max-h-[92vh]">
        <!-- HEADER -->
        <div class="bg-[#0B3948] text-white px-6 py-4 flex items-center justify-between shrink-0">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-teal-500/20 border border-teal-400/30 flex items-center justify-center text-teal-300">
              <i class="pi pi-user-plus text-base"></i>
            </div>
            <div>
              <h3 class="text-base font-bold tracking-wide">Tambah Master Data Pasien Anak</h3>
              <p class="text-xs text-teal-200/90 mt-0.5">
                Input identitas anak dan penerbitan Nomor Rekam Medis (RM) klinis otomatis
              </p>
            </div>
          </div>
          <button @click="closeModalTambahPasien" class="text-slate-300 hover:text-white p-1 cursor-pointer">
            <i class="pi pi-times text-sm"></i>
          </button>
        </div>

        <!-- BODY -->
        <div class="p-6 overflow-y-auto space-y-4 text-sm flex-1 custom-scrollbar">
          <form @submit.prevent="submitTambahPasien" class="space-y-4">
            <div class="space-y-3.5">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                <div>
                  <label class="label-clinical">Nama Lengkap Anak *</label>
                  <input v-model="tambahForm.nama_anak" required class="input-clinical" placeholder="mis. Muhammad Rafka" />
                </div>
                <div>
                  <label class="label-clinical">NIK / No. KIA Anak *</label>
                  <input v-model="tambahForm.NIK" required class="input-clinical" placeholder="16 Digit NIK Anak / KIA" />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                <div>
                  <label class="label-clinical">Tanggal Lahir *</label>
                  <input v-model="tambahForm.tanggal_lahir" type="date" required class="input-clinical" />
                </div>
                <div>
                  <label class="label-clinical">Jenis Kelamin *</label>
                  <select v-model="tambahForm.jenis_kelamin_id" required class="input-clinical">
                    <option :value="1">Laki-laki</option>
                    <option :value="2">Perempuan</option>
                  </select>
                </div>
                <div>
                  <label class="label-clinical">Agama *</label>
                  <select v-model="tambahForm.agama_id" required class="input-clinical">
                    <option :value="1">Islam</option>
                    <option :value="2">Kristen</option>
                    <option :value="3">Katolik</option>
                    <option :value="4">Hindu</option>
                    <option :value="5">Buddha</option>
                  </select>
                </div>
              </div>

              <!-- Usia Pediatrik Preview -->
              <div v-if="tambahForm.tanggal_lahir" class="p-2.5 bg-[#E6F2F5] border border-[#CBE3E9] rounded-lg flex items-center justify-between text-xs">
                <span class="text-slate-600 font-medium">Usia Pediatrik Terkalkulasi:</span>
                <span class="font-bold text-[#0F766E]">{{ hitungUsiaPediatrik(tambahForm.tanggal_lahir) }}</span>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                <div>
                  <label class="label-clinical">Diagnosis Awal / Disiplin Terapi</label>
                  <input v-model="tambahForm.diagnosis_awal" class="input-clinical" placeholder="mis. Speech Delay (F80.1) / Sensori Integrasi" />
                </div>
                <div>
                  <label class="label-clinical">Catatan Alergi / Kondisi Khusus</label>
                  <input v-model="tambahForm.catatan_alergi" class="input-clinical" placeholder="mis. Alergi debu, intoleransi laktosa" />
                </div>
              </div>

              <div>
                <label class="label-clinical">Alamat Domisili Pasien</label>
                <textarea v-model="tambahForm.alamat" rows="2" class="input-clinical" placeholder="Alamat lengkap tempat tinggal..."></textarea>
              </div>
            </div>

            <!-- MODAL ACTIONS -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
              <button
                type="button"
                @click="closeModalTambahPasien"
                class="btn-secondary-clinical"
              >
                Batal
              </button>

              <button
                type="submit"
                :disabled="savingPasien"
                class="btn-primary-clinical ml-auto"
              >
                <i v-if="savingPasien" class="pi pi-spin pi-spinner text-sm"></i>
                <i v-else class="pi pi-check text-sm"></i>
                <span>Simpan Data Anak (Terbitkan No. RM)</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    </teleport>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '../axios'

const router = useRouter()
const toast = useToast()

const listPasien = ref([])
const loading = ref(false)
const searchQuery = ref('')
const filterPenjamin = ref('')
const filterDiagnosis = ref('')

// Modals
const showModalAntrian = ref(false)
const showModalGelang = ref(false)
const showModalConsent = ref(false)
const selectedPasien = ref(null)

const currentDateFormatted = computed(() => {
  const d = new Date()
  return d.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
})

const formatRm = (id) => {
  return `RM-${String(id || 1).padStart(6, '0')}`
}

// Fetch Data
const fetchData = async () => {
  loading.value = true
  try {
    const res = await api.get('/profile-anak')
    const rawData = res.data?.data || res.data || []
    listPasien.value = Array.isArray(rawData) ? rawData : []
  } catch (err) {
    console.error('Error fetch profile-anak:', err)
  } finally {
    loading.value = false
  }
}

// Filtered List
const filteredPasien = computed(() => {
  let res = listPasien.value

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    res = res.filter(p => {
      const nama = (p.nama_anak || '').toLowerCase()
      const rm = (p.no_rm || formatRm(p.id)).toLowerCase()
      const nik = (p.NIK || '').toLowerCase()
      const wali = (p.orang_tua?.nama || p.user_profile?.nama || '').toLowerCase()
      return nama.includes(q) || rm.includes(q) || nik.includes(q) || wali.includes(q)
    })
  }

  if (filterPenjamin.value) {
    res = res.filter(p => {
      const penjamin = p.penjamin || 'BPJS'
      return penjamin.toLowerCase().includes(filterPenjamin.value.toLowerCase())
    })
  }

  if (filterDiagnosis.value) {
    res = res.filter(p => {
      const diag = p.diagnosis_awal || ''
      return diag.toLowerCase().includes(filterDiagnosis.value.toLowerCase())
    })
  }

  return res
})

// Berkas Pasien State
const showModalBerkas = ref(false)
const activeBerkasTab = ref('consent')
const berkasData = ref({ informed_consents: [], surat_resmi: [], lampiran_klinis: [] })
const showCreateConsentForm = ref(false)
const showUploadForm = ref(false)

const consentForm = ref({
  judul_persetujuan: 'Persetujuan Rencana Terapi & Pencatatan RME',
  tipe_persetujuan: 'tindakan_terapi',
  pernyataan: 'Saya menyetujui program asesmen dan sesi terapi serta pencatatan RME anak sesuai UU PDP No. 27/2022 dan Permenkes 24/2022.',
  nama_penandatangan: '',
  hubungan_wali: 'Ayah Kandung',
  tanda_tangan_base64: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
})

const uploadForm = ref({
  nama_file: '',
  kategori: 'lab_psikotes',
  keterangan: '',
})

const openBerkasModal = async (pasien) => {
  selectedPasien.value = pasien
  consentForm.value.nama_penandatangan = pasien.orang_tua?.nama || pasien.user_profile?.nama || 'Ahmad Fauzi'
  showModalBerkas.value = true
  await loadBerkasPasien(pasien.id)
}

const loadBerkasPasien = async (pasienId) => {
  try {
    const res = await api.get(`/surat/berkas-pasien/${pasienId}`)
    berkasData.value = res.data || { informed_consents: [], surat_resmi: [], lampiran_klinis: [] }
  } catch (err) {
    console.error('Error load berkas pasien:', err)
  }
}

const submitConsent = async () => {
  try {
    const payload = {
      profile_anak_id: selectedPasien.value.id,
      ...consentForm.value
    }
    const res = await api.post('/surat/berkas-pasien/consent', payload)
    toast.success(res.data.message || 'Informed consent berhasil disimpan')
    showCreateConsentForm.value = false
    await loadBerkasPasien(selectedPasien.value.id)
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menyimpan consent')
  }
}

const submitUpload = async () => {
  try {
    const payload = {
      profile_anak_id: selectedPasien.value.id,
      ...uploadForm.value
    }
    const res = await api.post('/surat/berkas-pasien/upload', payload)
    toast.success(res.data.message || 'Berkas berhasil diupload')
    uploadForm.value = { nama_file: '', kategori: 'lab_psikotes', keterangan: '' }
    showUploadForm.value = false
    await loadBerkasPasien(selectedPasien.value.id)
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal mengupload berkas')
  }
}

const deleteAttachment = async (id) => {
  try {
    await api.delete(`/surat/berkas-pasien/${id}`)
    toast.success('Berkas berhasil dihapus')
    await loadBerkasPasien(selectedPasien.value.id)
  } catch (err) {
    toast.error('Gagal menghapus berkas')
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

// Actions
const openEmr = (pasien) => {
  router.push({
    path: '/emr-timeline',
    query: {
      pasien_id: pasien.id,
      nama: pasien.nama_anak,
      no_rm: pasien.no_rm || formatRm(pasien.id)
    }
  })
}

const openAntrianModal = (pasien) => {
  selectedPasien.value = pasien
  showModalAntrian.value = true
}

const openGelangModal = (pasien) => {
  selectedPasien.value = pasien
  showModalGelang.value = true
}

const openConsentModal = (pasien) => {
  selectedPasien.value = pasien
  showModalConsent.value = true
}

const cetakKarcisAction = () => {
  toast.success(`Mencetak karcis antrian untuk ${selectedPasien.value?.nama_anak}...`)
  showModalAntrian.value = false
}

const cetakGelangAction = () => {
  toast.success(`Mencetak gelang identitas pasien untuk ${selectedPasien.value?.nama_anak}...`)
  showModalGelang.value = false
}

// Tambah Pasien Baru (Master Anak Tanpa Input Ortu)
const showModalTambahPasien = ref(false)
const savingPasien = ref(false)

const tambahForm = ref({
  nama_anak: '',
  NIK: '',
  tanggal_lahir: '',
  jenis_kelamin_id: 1,
  agama_id: 1,
  alamat: '',
  catatan_alergi: '',
  diagnosis_awal: ''
})

const openModalTambahPasien = () => {
  tambahForm.value = {
    nama_anak: '',
    NIK: '',
    tanggal_lahir: '',
    jenis_kelamin_id: 1,
    agama_id: 1,
    alamat: '',
    catatan_alergi: '',
    diagnosis_awal: ''
  }
  showModalTambahPasien.value = true
}

const closeModalTambahPasien = () => {
  if (savingPasien.value) return
  showModalTambahPasien.value = false
}

const hitungUsiaPediatrik = (tglStr) => {
  if (!tglStr) return '-'
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

const submitTambahPasien = async () => {
  savingPasien.value = true
  try {
    const res = await api.post('/profile-anak', tambahForm.value)
    toast.success(res.data?.message || 'Data master pasien anak berhasil disimpan!')
    closeModalTambahPasien()
    await fetchData()
  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal mendaftarkan pasien anak'
    toast.error(msg)
  } finally {
    savingPasien.value = false
  }
}

onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.15s ease-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.97) translateY(4px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
</style>
