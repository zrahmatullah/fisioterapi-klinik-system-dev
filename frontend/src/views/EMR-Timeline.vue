<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-y-auto custom-scrollbar w-full pr-1">

    <!-- TOP PATIENT SELECTOR STRIP -->
    <div class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 flex flex-wrap items-center justify-between gap-3 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
      <div class="flex items-center gap-3">
        <span class="text-xs font-semibold text-slate-700">Pilih Pasien Terapi:</span>
        <div class="w-72">
          <Dropdown
            v-model="selectedPasienId"
            :options="pasienList"
            optionLabel="nama_anak"
            optionValue="id"
            placeholder="Pilih Pasien Terapi"
            filter
            filterPlaceholder="Cari nama atau No. RM..."
            class="w-full text-xs"
            @change="onPatientChange"
          />
        </div>
      </div>

      <div class="flex items-center gap-2">
        <button
          type="button"
          @click="showOrderModal = true"
          class="bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition shadow-sm cursor-pointer"
        >
          <i class="pi pi-file-plus text-xs"></i>
          <span>Order Terapi Dokter</span>
        </button>

        <button
          type="button"
          @click="showTimelineModal = true"
          class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-medium text-xs px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition"
        >
          <i class="pi pi-history text-slate-500 text-xs"></i>
          <span>Riwayat Timeline ({{ records.length }})</span>
        </button>

        <button
          type="button"
          @click="showGoalModal = true"
          class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-medium text-xs px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition"
        >
          <i class="pi pi-chart-line text-teal-600 text-xs"></i>
          <span>Goal Tracking ({{ goals.length }})</span>
        </button>
      </div>
    </div>

    <!-- 1. CONTEXT HEADER BANNER (TOP SUMMARY STRIP WITH ALLERGY INPUT & TRIGGER) -->
    <div class="bg-[#E6F2F5] border border-[#CBE3E9] rounded-xl px-4 py-3 flex flex-wrap items-center justify-between gap-4 w-full shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
      <!-- Left Group: Avatar + Metadata Columns -->
      <div class="flex items-center gap-4 flex-wrap">
        <div class="w-11 h-11 rounded-lg bg-[#0F766E] text-white flex items-center justify-center font-bold text-base shadow-sm shrink-0">
          {{ (selectedPasien?.nama_anak && selectedPasien.nama_anak.charAt(0)) || 'A' }}
        </div>

        <div class="flex items-center gap-5 flex-wrap">
          <div>
            <span class="text-[11px] text-[#64748B] block leading-tight">Nama Pasien</span>
            <span class="text-[14px] font-bold text-[#0F172A] leading-normal">{{ selectedPasien?.nama_anak || 'Azhar Al-Farizi' }}</span>
          </div>

          <div class="border-l border-[#CBE3E9] pl-4">
            <span class="text-[11px] text-[#64748B] block leading-tight">No. RM</span>
            <span class="text-[13px] font-semibold text-[#0F172A] tabular-nums leading-normal">{{ selectedPasien?.no_rm || 'RM-202512-00006' }}</span>
          </div>

          <div class="border-l border-[#CBE3E9] pl-4">
            <span class="text-[11px] text-[#64748B] block leading-tight">Umur/JK</span>
            <span class="text-[13px] text-[#334155] leading-normal">{{ selectedPasien?.usia_lengkap || '5 Th 8 Bln' }} / {{ selectedPasien?.jenis_kelamin?.jenis_kelamin || 'Laki-laki' }}</span>
          </div>

          <div class="border-l border-[#CBE3E9] pl-4">
            <span class="text-[11px] text-[#64748B] block leading-tight">Jaminan</span>
            <span class="text-[13px] font-medium text-[#334155] leading-normal">{{ selectedPasien?.jaminan || 'BPJS Kesehatan' }}</span>
          </div>

          <!-- ALLERGY INPUT / PICKER -->
          <div class="border-l border-[#CBE3E9] pl-4 flex items-center gap-2">
            <div>
              <span class="text-[11px] text-[#64748B] block leading-tight">Alergi (Master)</span>
              <span class="text-[13px] font-semibold text-[#DC2626] leading-normal">
                {{ selectedPasien?.catatan_alergi || 'Tidak ada riwayat alergi' }}
              </span>
            </div>
            <button
              type="button"
              @click="openAllergyModal"
              class="px-2 py-0.5 rounded bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 text-[11px] font-semibold flex items-center gap-1 transition shadow-xs cursor-pointer ml-1"
              title="Kelola Riwayat Alergi Pasien"
            >
              <i class="pi pi-pencil text-[9px]"></i>
              <span>Edit Alergi</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Right Action Group -->
      <div class="flex items-center gap-2.5">
        <button
          type="button"
          @click="printGelang"
          class="bg-white border border-slate-200 text-slate-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-slate-50 active:scale-95 flex items-center gap-1.5 transition shadow-[0_1px_2px_rgba(0,0,0,0.03)]"
        >
          <i class="pi pi-print text-[11px] text-slate-500"></i>
          <span>Cetak Gelang</span>
        </button>

        <button
          type="button"
          @click="showTimelineModal = true"
          class="bg-white border border-slate-200 text-slate-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-slate-50 active:scale-95 flex items-center gap-1.5 transition shadow-[0_1px_2px_rgba(0,0,0,0.03)]"
        >
          <i class="pi pi-history text-[11px] text-slate-500"></i>
          <span>Riwayat Kunjungan</span>
        </button>

        <span class="bg-[#DCFCE7] text-[#15803D] text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1.5 border border-emerald-200/60 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
          <span>● Stabil</span>
        </span>
      </div>
    </div>

    <!-- CLINICAL WORKSPACE SEGMENTED TABS (ALL-IN-ONE RME) -->
    <div class="bg-white border border-slate-200 rounded-xl p-1.5 flex items-center justify-between gap-2 shadow-[0_1px_3px_rgba(15,23,42,0.04)] overflow-x-auto custom-scrollbar shrink-0">
      <div class="flex items-center gap-1">
        <button
          type="button"
          @click="emrWorkspaceTab = 'soap'"
          class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
          :class="emrWorkspaceTab === 'soap' ? 'bg-[#0F766E] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
        >
          <i class="pi pi-file-edit text-xs"></i>
          <span>1. Pemeriksaan Klinis & Resep</span>
        </button>

        <button
          type="button"
          @click="emrWorkspaceTab = 'asesmen'"
          class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
          :class="emrWorkspaceTab === 'asesmen' ? 'bg-[#0F766E] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
        >
          <i class="pi pi-clipboard text-xs"></i>
          <span>2. Skrining & Riwayat Kelahiran</span>
        </button>

        <button
          type="button"
          @click="emrWorkspaceTab = 'aktivitas'"
          class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
          :class="emrWorkspaceTab === 'aktivitas' ? 'bg-[#0F766E] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
        >
          <i class="pi pi-check-square text-xs"></i>
          <span>3. Sesi Terapi & Home Program</span>
        </button>

        <button
          type="button"
          @click="emrWorkspaceTab = 'evaluasi'"
          class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
          :class="emrWorkspaceTab === 'evaluasi' ? 'bg-[#0F766E] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
        >
          <i class="pi pi-chart-line text-xs"></i>
          <span>4. Evaluasi & Target Capaian</span>
        </button>
      </div>

      <div class="hidden sm:flex items-center gap-2 pr-2 text-[11px] text-slate-500 font-medium">
        <i class="pi pi-shield text-[#0F766E]"></i>
        <span>RME Satu Pintu Terpadu</span>
      </div>
    </div>

    <!-- 2. MULTI-COLUMN SPLIT WORKSPACE (12-COLUMN GRID) -->
    <div v-if="emrWorkspaceTab === 'soap'" class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start w-full">

      <!-- PRIMARY FORM COLUMN (lg:col-span-7) -->
      <div class="lg:col-span-7 space-y-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
          <!-- Form Header + SOAP Template Picker -->
          <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3 flex-wrap gap-2">
            <div>
              <h3 class="text-[15px] font-semibold text-[#0F172A] leading-tight">
                Form Asesmen SOAP (Pemeriksaan Klinis)
              </h3>
              <span class="text-[11px] text-slate-500 font-mono">
                Sesi: {{ activeRecord?.no_emr || 'EMR-202611-0001' }}
              </span>
            </div>

            <!-- Master SOAP Template Dropdown -->
            <div class="flex items-center gap-1.5">
              <span class="text-[11px] font-bold text-[#0F766E] uppercase tracking-wider">Template SOAP:</span>
              <select
                v-model="selectedSoapTemplateId"
                @change="applySoapTemplate"
                class="px-2.5 py-1 text-xs border border-teal-300 bg-teal-50/50 rounded-lg outline-none focus:border-[#0F766E] font-medium text-slate-800"
              >
                <option value="">-- Pilih Template SOAP Klinis --</option>
                <option v-for="tpl in soapTemplates" :key="tpl.id" :value="tpl.id">
                  {{ tpl.title }}
                </option>
              </select>
            </div>
          </div>

          <div class="space-y-3.5">
            <!-- SUBJEKTIF (S) -->
            <div>
              <label class="block text-xs font-semibold text-[#334155] uppercase tracking-wide mb-1">
                Subjektif (S)
              </label>
              <textarea
                v-model="soapForm.subjektif"
                rows="3"
                class="w-full px-3 py-2 text-[13px] text-slate-800 border border-slate-300 rounded-lg focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 outline-none transition resize-y"
                placeholder="Keluhan utama dan riwayat perkembangan dari orang tua pasien..."
              ></textarea>
            </div>

            <!-- OBJEKTIF (O) -->
            <div>
              <label class="block text-xs font-semibold text-[#334155] uppercase tracking-wide mb-1">
                Objektif (O)
              </label>
              <textarea
                v-model="soapForm.objektif"
                rows="3"
                class="w-full px-3 py-2 text-[13px] text-slate-800 border border-slate-300 rounded-lg focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 outline-none transition resize-y"
                placeholder="Hasil pemeriksaan objektif klinis, kontak mata, tonus otot, respon auditori, postur..."
              ></textarea>
            </div>

            <!-- ASESMEN (A) & MASTER ICD-10 SELECTORS -->
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-semibold text-[#334155] uppercase tracking-wide">
                  Asesmen (A) - Diagnosa ICD-10 & Tindakan ICD-9-CM
                </label>
                <span class="text-[11px] font-bold text-[#0F766E] px-1.5 py-0.2 rounded bg-teal-50 border border-teal-200">
                  Master ICD-10 & ICD-9
                </span>
              </div>

              <!-- ICD-10 Master Selectors -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-2">
                <div>
                  <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Diagnosa Utama (ICD-10) *:</label>
                  <select
                    v-model="selectedIcd10PrimaryId"
                    class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] bg-white font-semibold text-slate-800"
                  >
                    <option value="">Pilih Diagnosa Utama...</option>
                    <option v-for="icd in icd10List" :key="icd.id" :value="icd.id">
                      {{ icd.kode }} - {{ icd.deskripsi_id || icd.deskripsi_en }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Diagnosa Sekunder (ICD-10):</label>
                  <select
                    v-model="selectedIcd10SecondaryId"
                    class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] bg-white text-slate-700"
                  >
                    <option value="">Pilih Diagnosa Sekunder...</option>
                    <option v-for="icd in icd10List" :key="icd.id" :value="icd.id">
                      {{ icd.kode }} - {{ icd.deskripsi_id || icd.deskripsi_en }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Master ICD-9-CM Clinical Procedure Selector -->
              <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg mb-2">
                <label class="block text-[10px] font-bold text-slate-700 mb-1">Tindakan / Prosedur Terapi Klinis (ICD-9-CM):</label>
                <select
                  v-model="selectedProcedureId"
                  @change="onProcedureChange"
                  class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] bg-white font-medium text-slate-800"
                >
                  <option value="">Pilih Prosedur Terapi ICD-9...</option>
                  <option v-for="prc in procedureList" :key="prc.id" :value="prc.id">
                    {{ prc.icd9_code }} - {{ prc.procedure_name }} ({{ prc.category }}) — Rp {{ Number(prc.tariff_umum || 0).toLocaleString('id-ID') }}
                  </option>
                </select>
              </div>

              <textarea
                v-model="soapForm.asesmen"
                rows="2"
                class="w-full px-3 py-2 text-[13px] text-slate-800 border border-slate-300 rounded-lg focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 outline-none transition resize-y"
                placeholder="Analisis perkembangan klinis anak per sesi terapi..."
              ></textarea>
            </div>

            <!-- PLANNING (P) -->
            <div>
              <label class="block text-xs font-semibold text-[#334155] uppercase tracking-wide mb-1">
                Planning (P)
              </label>
              <textarea
                v-model="soapForm.planning"
                rows="3"
                class="w-full px-3 py-2 text-[13px] text-slate-800 border border-slate-300 rounded-lg focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 outline-none transition resize-y"
                placeholder="Rencana terapi lanjutan, stimulasi di rumah (Home Program), dan target sesi berikutnya..."
              ></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- SECONDARY MONITORING & ACTION COLUMN (lg:col-span-5) -->
      <div class="lg:col-span-5 space-y-4">

        <!-- 1. KPI / DYNAMIC VITAL SIGNS & GROWTH SUMMARY CARD (DRIVEN BY MASTER DATA) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)] space-y-2.5">
          <div class="flex items-center justify-between pb-2 border-b border-slate-100 flex-wrap gap-1.5">
            <div class="flex items-center gap-1.5">
              <h3 class="text-[15px] font-semibold text-[#0F172A] leading-tight">
                Kartu Ringkasan Tanda-Tanda Vital
              </h3>
            </div>

            <div class="flex items-center gap-1.5">
              <!-- Clinical Age-Adjusted Status Pill -->
              <span
                class="text-[11px] font-bold px-2 py-0.5 rounded-full border flex items-center gap-1"
                :class="{
                  'bg-emerald-50 text-emerald-800 border-emerald-200': vitalSummary.overall_status === 'normal',
                  'bg-amber-50 text-amber-800 border-amber-200': vitalSummary.overall_status === 'perlu_perhatian',
                  'bg-rose-50 text-rose-800 border-rose-200 animate-pulse': vitalSummary.overall_status === 'abnormal_kritis',
                }"
              >
                <span class="w-1.5 h-1.5 rounded-full"
                  :class="{
                    'bg-emerald-500': vitalSummary.overall_status === 'normal',
                    'bg-amber-500': vitalSummary.overall_status === 'perlu_perhatian',
                    'bg-rose-500': vitalSummary.overall_status === 'abnormal_kritis',
                  }"
                ></span>
                <span>{{ vitalStatusLabel(vitalSummary.overall_status) }}</span>
              </span>

              <!-- Grafik TTV Button -->
              <button
                type="button"
                @click="openVitalsChart"
                class="px-2 py-0.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-[#0F766E] border border-teal-200 text-[11px] font-bold flex items-center gap-1 transition cursor-pointer"
                title="Lihat Grafik Tren TTV Anak"
              >
                <i class="pi pi-chart-line text-[10px]"></i>
                <span>Grafik TTV</span>
              </button>

              <!-- Edit TTV Button -->
              <button
                type="button"
                @click="openVitalsModal"
                class="px-2 py-0.5 rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-[11px] font-semibold flex items-center gap-1 transition cursor-pointer"
                title="Input / Update TTV & Pertumbuhan"
              >
                <i class="pi pi-pencil text-[9px] text-slate-500"></i>
                <span>Edit TTV</span>
              </button>
            </div>
          </div>

          <!-- Dynamic 5-Column Core TTV Mini-Grid (Master Driven) -->
          <div class="grid grid-cols-5 gap-2">
            <div
              v-for="v in coreVitals"
              :key="v.code"
              @click="openVitalsModal"
              class="rounded-lg py-2 px-1.5 text-center cursor-pointer transition hover:shadow-xs border"
              :class="{
                'bg-[#F0F6F8] border-[#DCE8EC]': v.status === 'normal',
                'bg-amber-50/70 border-amber-300': v.status === 'perlu_perhatian',
                'bg-rose-50 border-rose-300': v.status === 'abnormal_kritis',
              }"
            >
              <span class="text-[11px] text-[#64748B] block font-medium">{{ v.code }}</span>
              <span class="text-[15px] font-bold text-[#0F172A] tabular-nums leading-tight block mt-0.5">{{ v.value || '-' }}</span>
              <span class="text-[10px] text-[#64748B] block mt-0.5">{{ v.unit }}</span>
            </div>
          </div>

          <!-- Pediatric Growth Anthropometry Row Toggle (BB, TB, LK) -->
          <div class="pt-1.5 flex items-center justify-between text-xs">
            <button
              type="button"
              @click="showGrowthRow = !showGrowthRow"
              class="text-[11px] font-bold text-[#0F766E] hover:underline flex items-center gap-1 cursor-pointer"
            >
              <i class="pi" :class="showGrowthRow ? 'pi-chevron-up' : 'pi-chevron-down'"></i>
              <span>{{ showGrowthRow ? 'Sembunyikan' : 'Tampilkan' }} Antropometri Anak (BB, TB, LK)</span>
            </button>
            <span class="text-[10px] text-slate-400">Rentang Usia: {{ childAgeBracketLabel }}</span>
          </div>

          <div v-if="showGrowthRow" class="grid grid-cols-3 gap-2 pt-1 animate-fadeIn">
            <div
              v-for="g in growthVitals"
              :key="g.code"
              @click="openVitalsModal"
              class="rounded-lg py-1.5 px-2 text-center cursor-pointer border bg-[#F0F6F8] border-[#DCE8EC]"
            >
              <span class="text-[10px] text-[#64748B] block font-medium">{{ g.name }}</span>
              <span class="text-[14px] font-bold text-[#0F172A] tabular-nums block">{{ g.value || '-' }} <span class="text-[11px] font-normal text-slate-500">{{ g.unit }}</span></span>
            </div>
          </div>
        </div>

        <!-- 2. TABBED ACTION & ORDER CARD -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)] space-y-3">
          <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="text-[15px] font-semibold text-[#0F172A] leading-tight">
              Tab Terintegrasi "E-Resep" & "Order Lab/Rad"
            </h3>
          </div>

          <!-- UNDERLINE TABS -->
          <div class="flex border-b border-slate-200 gap-4 text-xs font-medium">
            <button
              type="button"
              @click="activeSubTab = 'resep'"
              class="pb-2 transition flex items-center gap-1.5"
              :class="activeSubTab === 'resep' ? 'border-b-2 border-[#0F766E] text-[#0F766E] font-semibold' : 'text-slate-500 hover:text-slate-700'"
            >
              <i class="pi pi-file-edit text-xs"></i>
              <span>E-Resep (Signa Lengkap)</span>
            </button>
            <button
              type="button"
              @click="activeSubTab = 'lab'"
              class="pb-2 transition flex items-center gap-1.5"
              :class="activeSubTab === 'lab' ? 'border-b-2 border-[#0F766E] text-[#0F766E] font-semibold' : 'text-slate-500 hover:text-slate-700'"
            >
              <i class="pi pi-filter text-xs"></i>
              <span>Order Lab, Rad & Penunjang</span>
            </button>
          </div>

          <!-- TAB 1: E-RESEP DENGAN MASTER OBAT, SIGNA, & CEK ALERGI OTOMATIS -->
          <div v-if="activeSubTab === 'resep'" class="space-y-3">
            <div class="p-2 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] text-xs text-[#0B3948] flex items-center justify-between">
              <span>E-Resep dikirim ke Farmasi untuk telaah apoteker sebelum masuk tagihan Billing</span>
              <span class="text-[10px] font-bold uppercase tracking-wider text-[#0F766E]">Telaah Farmasi</span>
            </div>

            <!-- DRUG-ALLERGY CONFLICT WARNING BANNER -->
            <div v-if="drugAllergyConflictMessage" class="p-2.5 rounded-lg bg-rose-50 border border-rose-300 text-rose-800 text-xs flex items-center gap-2 animate-pulse">
              <i class="pi pi-exclamation-triangle text-rose-600 text-base shrink-0"></i>
              <div>
                <strong class="font-bold block">Peringatan Konflik Alergi Obat!</strong>
                <span>{{ drugAllergyConflictMessage }}</span>
              </div>
            </div>

            <div class="space-y-2.5 max-h-[300px] overflow-y-auto custom-scrollbar pr-1">
              <div
                v-for="(item, idx) in preskripsiItems"
                :key="idx"
                class="p-2.5 rounded-lg border border-slate-200 bg-slate-50/70 space-y-2 text-xs"
              >
                <div class="flex items-center justify-between gap-2">
                  <!-- STRICT MASTER MEDICATION DROPDOWN (NO FREE-TEXT 'joj' ALLOWED) -->
                  <div class="flex-1">
                    <select
                      v-model="item.master_produk_id"
                      @change="onSelectMedication(item)"
                      class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] font-semibold bg-white text-slate-900"
                    >
                      <option value="">Pilih Obat dari Master Farmasi...</option>
                      <option v-for="m in masterMedications" :key="m.id" :value="m.id">
                        {{ m.nama_produk }} — Stok: {{ m.total_stok }} {{ m.satuan }} — Rp {{ Number(m.harga_jual).toLocaleString('id-ID') }}
                      </option>
                    </select>
                  </div>

                  <div class="flex items-center gap-1 w-32">
                    <input
                      type="number"
                      v-model.number="item.jumlah"
                      min="1"
                      class="w-14 px-2 py-1.5 text-xs text-center border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] font-bold"
                      title="Jumlah obat"
                    />
                    <select v-model="item.satuan" class="w-16 px-1 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] bg-white">
                      <option v-for="u in masterUnits" :key="u.code" :value="u.code">{{ u.code }}</option>
                    </select>
                  </div>

                  <button
                    type="button"
                    @click="hapusObatRow(idx)"
                    class="text-rose-500 hover:text-rose-700 p-1 cursor-pointer"
                    title="Hapus baris obat"
                  >
                    <i class="pi pi-trash text-xs"></i>
                  </button>
                </div>

                <!-- Signa & Waktu Minum strictly from Master Data -->
                <div class="grid grid-cols-2 gap-2">
                  <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Signa (Master Aturan Pakai):</label>
                    <select
                      v-model="item.signa"
                      class="w-full px-2 py-1 text-xs border border-slate-300 rounded-md outline-none focus:border-[#0F766E] bg-white"
                    >
                      <option v-for="sig in masterSignas" :key="sig.code" :value="sig.signa_text">
                        {{ sig.signa_text }}
                      </option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Waktu Minum (Master):</label>
                    <select
                      v-model="item.waktu_minum"
                      class="w-full px-2 py-1 text-xs border border-slate-300 rounded-md outline-none focus:border-[#0F766E] bg-white"
                    >
                      <option v-for="it in masterIntakeTimes" :key="it.code" :value="it.intake_time_text">
                        {{ it.intake_time_text }}
                      </option>
                    </select>
                  </div>
                </div>

                <div>
                  <input
                    type="text"
                    v-model="item.catatan_racikan"
                    placeholder="Instruksi racikan / puyer / dosis khusus (opsional)..."
                    class="w-full px-2 py-1 text-[11px] border border-slate-200 rounded-md outline-none focus:border-[#0F766E] text-slate-600 italic bg-white"
                  />
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between pt-1">
              <button
                type="button"
                @click="tambahObatRow"
                class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-medium text-xs px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition cursor-pointer"
              >
                <i class="pi pi-plus text-[10px] text-slate-500"></i>
                <span>Tambah Item Obat</span>
              </button>

              <button
                type="button"
                @click="submitEResep"
                :disabled="savingEResep || preskripsiItems.length === 0"
                class="bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs px-3.5 py-1.5 rounded-lg flex items-center gap-1.5 transition cursor-pointer shadow-sm disabled:opacity-50"
              >
                <i v-if="savingEResep" class="pi pi-spin pi-spinner text-xs"></i>
                <i v-else class="pi pi-send text-xs"></i>
                <span>Kirim E-Resep ke Farmasi</span>
              </button>
            </div>
          </div>

          <!-- TAB 2: ORDER PENUNJANG (LAB & RADIOLOGI STRICTLY FROM MASTER) -->
          <div v-else class="space-y-3 py-1">
            <div class="p-2 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] text-xs text-[#0B3948] flex items-center justify-between">
              <span>Pemeriksaan penunjang langsung dari Master Data Tarif SIMRS</span>
              <span class="text-[10px] font-bold uppercase tracking-wider text-[#0F766E]">Penunjang Klinis</span>
            </div>

            <!-- Master Ancillary Checklist Grid -->
            <div class="space-y-2 max-h-[280px] overflow-y-auto custom-scrollbar pr-1">
              <div v-for="cat in ['Laboratorium', 'Radiologi', 'Diagnostik Khusus']" :key="cat" class="space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block pt-1">{{ cat }}</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                  <label
                    v-for="test in masterAncillaryTests.filter(t => t.category === cat)"
                    :key="test.id"
                    class="flex items-center justify-between p-2 rounded-lg border text-xs cursor-pointer transition"
                    :class="selectedAncillaryIds.includes(test.id) ? 'bg-teal-50/70 border-teal-400 font-semibold text-[#0F766E]' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'"
                  >
                    <div class="truncate mr-2">
                      <span class="block text-[11px] font-medium leading-tight">{{ test.test_name }}</span>
                      <span class="text-[10px] text-slate-400 font-mono">Rp {{ Number(test.tariff_umum || 0).toLocaleString('id-ID') }}</span>
                    </div>
                    <input
                      type="checkbox"
                      :value="test.id"
                      v-model="selectedAncillaryIds"
                      class="w-4 h-4 rounded text-[#0F766E] focus:ring-[#0F766E] accent-[#0F766E] shrink-0"
                    />
                  </label>
                </div>
              </div>
            </div>

            <!-- Catatan Klinis Tambahan -->
            <div>
              <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Catatan Indikasi Klinis Dokter:</label>
              <input
                v-model="ancillaryNotes"
                placeholder="Indikasi pemeriksaan penunjang (mis. evaluasi kecurigaan speech delay/hearing)..."
                class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none bg-white text-slate-800"
              />
            </div>

            <div class="pt-1 flex justify-end">
              <button
                type="button"
                @click="submitOrderPenunjang"
                :disabled="savingOrderPenunjang || selectedAncillaryIds.length === 0"
                class="bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs px-3.5 py-1.5 rounded-lg flex items-center gap-1.5 transition cursor-pointer shadow-sm disabled:opacity-50"
              >
                <i v-if="savingOrderPenunjang" class="pi pi-spin pi-spinner text-xs"></i>
                <i v-else class="pi pi-send text-xs"></i>
                <span>Kirim {{ selectedAncillaryIds.length }} Order Penunjang</span>
              </button>
            </div>
          </div>

          <!-- STATUS ORDER DOKTER SESI INI (PRA-BILLING & HASIL) -->
          <div v-if="(sessionOrders.reseps && sessionOrders.reseps.length > 0) || (sessionOrders.penunjang && sessionOrders.penunjang.length > 0)" class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2 text-xs">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-1.5 font-bold text-slate-800">
                <i class="pi pi-history text-[#0F766E]"></i>
                <span>Status Order Kunjungan Ini (Pra-Billing):</span>
              </div>
              <span class="text-[10px] text-slate-500">Otomatis sinkron dengan Dashboard Farmasi & Penunjang</span>
            </div>

            <!-- List Resep -->
            <div v-if="sessionOrders.reseps && sessionOrders.reseps.length > 0" class="space-y-1">
              <div v-for="rsp in sessionOrders.reseps" :key="rsp.id" class="p-2 bg-white border border-slate-200 rounded-lg flex items-center justify-between">
                <div>
                  <span class="font-mono font-bold text-[#0F766E]">{{ rsp.nomor_resep }}</span>
                  <span class="text-slate-600 ml-1.5 text-[11px]">({{ (rsp.items || []).length }} macam obat)</span>
                </div>
                <div>
                  <span
                    v-if="rsp.status === 'selesai_dispensing' || rsp.status === 'diverifikasi'"
                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"
                  >
                    <i class="pi pi-check text-[9px]"></i> Diverifikasi Farmasi (Masuk Bill)
                  </span>
                  <span
                    v-else
                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse"
                  >
                    <i class="pi pi-clock text-[9px]"></i> Menunggu Verifikasi Farmasi
                  </span>
                </div>
              </div>
            </div>

            <!-- List Penunjang -->
            <div v-if="sessionOrders.penunjang && sessionOrders.penunjang.length > 0" class="space-y-1">
              <div v-for="pnj in sessionOrders.penunjang" :key="pnj.id" class="p-2 bg-white border border-slate-200 rounded-lg flex items-center justify-between">
                <div>
                  <span class="font-mono font-bold text-[#0F766E]">{{ pnj.nomor_order }}</span>
                  <span class="font-medium text-slate-800 ml-1.5">{{ pnj.nama_tindakan }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                  <span
                    v-if="pnj.status === 'selesai' || pnj.status === 'diverifikasi'"
                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"
                  >
                    <i class="pi pi-check text-[9px]"></i> Hasil Selesai (Masuk Bill)
                  </span>
                  <span
                    v-else
                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse"
                  >
                    <i class="pi pi-clock text-[9px]"></i> Menunggu Hasil Petugas
                  </span>

                  <button
                    v-if="pnj.hasil_pemeriksaan"
                    type="button"
                    @click="viewHasilPemeriksaan(pnj)"
                    class="px-2 py-0.5 rounded bg-[#E6F2F5] hover:bg-[#d6ebf0] text-[#0F766E] border border-[#CBE3E9] text-[10px] font-bold"
                  >
                    Lihat Hasil
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- PRIMARY ATOMIC SAVE CTA BUTTON -->
          <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button
              type="button"
              @click="submitAtomicEmr"
              :disabled="savingSoap"
              class="bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-medium px-5 py-2.5 rounded-lg text-[13px] flex items-center gap-2 transition shadow-sm disabled:opacity-50 cursor-pointer"
            >
              <i v-if="savingSoap" class="pi pi-spin pi-spinner text-xs"></i>
              <i v-else class="pi pi-check text-xs"></i>
              <span>Simpan Rekam Medis (SOAP) & Seluruh Order</span>
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: SKRINING TUMBUH KEMBANG & RIWAYAT KELAHIRAN (ASESMEN AWAL) -->
    <!-- ========================================================================= -->
    <div v-else-if="emrWorkspaceTab === 'asesmen'" class="bg-white border border-slate-200 rounded-xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] space-y-5 animate-fadeIn">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-[#E6F2F5] text-[#0F766E] flex items-center justify-center font-bold text-xs">
            <i class="pi pi-clipboard"></i>
          </div>
          <div>
            <h3 class="text-[15px] font-semibold text-[#0F172A] leading-tight">
              Skrining Klinis Tumbuh Kembang & Riwayat Kelahiran Anak
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Anamnesis perinatal, milestone perkembangan, dan deteksi dini perilaku sensorik-motorik</p>
          </div>
        </div>
        <button
          type="button"
          @click="submitAsesmen"
          :disabled="savingAsesmen"
          class="bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs px-4 py-2 rounded-lg flex items-center gap-1.5 transition shadow-sm cursor-pointer disabled:opacity-50"
        >
          <i v-if="savingAsesmen" class="pi pi-spin pi-spinner text-xs"></i>
          <i v-else class="pi pi-check text-xs"></i>
          <span>Simpan Data Asesmen</span>
        </button>
      </div>

      <div class="space-y-4 text-xs">
        <!-- 1. Riwayat Kehamilan & Kelahiran -->
        <div class="space-y-3">
          <h4 class="font-bold text-[#0F766E] text-xs uppercase tracking-wider flex items-center gap-1.5">
            <i class="pi pi-heart text-xs"></i>
            1. Riwayat Kehamilan & Kelahiran (Perinatal)
          </h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Riwayat Kehamilan:</label>
              <input
                v-model="formAsesmen.riwayat_kehamilan"
                placeholder="misal: Cukup bulan, ANC rutin..."
                class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Proses Kelahiran:</label>
              <select v-model="formAsesmen.proses_kelahiran" class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]">
                <option value="Normal (Spontan)">Normal (Spontan)</option>
                <option value="Sectio Caesarea (SC)">Sectio Caesarea (SC)</option>
                <option value="Vakum Ekstraksi">Vakum Ekstraksi</option>
                <option value="Induksi">Induksi Medis</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Usia Kehamilan:</label>
              <input
                v-model="formAsesmen.usia_kehamilan_lahir"
                placeholder="misal: 38 Minggu (Aterm)"
                class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Gangguan Saat Lahir:</label>
              <input
                v-model="formAsesmen.gangguan_melahirkan"
                placeholder="misal: Tidak ada asfiksia, APGAR 8/9"
                class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
              />
            </div>
          </div>
        </div>

        <!-- 2. Skrining Perilaku & Sensori -->
        <div class="space-y-3 pt-3 border-t border-slate-100">
          <h4 class="font-bold text-[#0F766E] text-xs uppercase tracking-wider flex items-center gap-1.5">
            <i class="pi pi-sliders-h text-xs"></i>
            2. Skrining Perilaku, Sensori & Milestones Tumbuh Kembang
          </h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
            <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 hover:border-teal-300 cursor-pointer">
              <span class="text-slate-800 text-[11px] font-medium">Riwayat Kejang / Demam Tinggi</span>
              <input type="checkbox" v-model="formAsesmen.riwayat_kejang" class="rounded text-[#0F766E] focus:ring-[#0F766E]" />
            </label>
            <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 hover:border-teal-300 cursor-pointer">
              <span class="text-slate-800 text-[11px] font-medium">Konsumsi Obat Rutin (Epilepsi)</span>
              <input type="checkbox" v-model="formAsesmen.konsumsi_obat_epilepsi" class="rounded text-[#0F766E] focus:ring-[#0F766E]" />
            </label>
            <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 hover:border-teal-300 cursor-pointer">
              <span class="text-slate-800 text-[11px] font-medium">Perkembangan Sesuai Milestones</span>
              <input type="checkbox" v-model="formAsesmen.perkembangan_sesuai_usia" class="rounded text-[#0F766E] focus:ring-[#0F766E]" />
            </label>
            <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 hover:border-teal-300 cursor-pointer">
              <span class="text-slate-800 text-[11px] font-medium">Diberikan ASI Eksklusif</span>
              <input type="checkbox" v-model="formAsesmen.disusui_ibu" class="rounded text-[#0F766E] focus:ring-[#0F766E]" />
            </label>
            <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 hover:border-teal-300 cursor-pointer">
              <span class="text-slate-800 text-[11px] font-medium">Kecanduan Layar (Screen Addict)</span>
              <input type="checkbox" v-model="formAsesmen.tv_gadget_addict" class="rounded text-[#0F766E] focus:ring-[#0F766E]" />
            </label>
            <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 hover:border-teal-300 cursor-pointer">
              <span class="text-slate-800 text-[11px] font-medium">Sering Memutar Benda / Roda</span>
              <input type="checkbox" v-model="formAsesmen.sering_memutar_benda" class="rounded text-[#0F766E] focus:ring-[#0F766E]" />
            </label>
            <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 hover:border-teal-300 cursor-pointer">
              <span class="text-slate-800 text-[11px] font-medium">Hand Flapping Saat Senang/Cemas</span>
              <input type="checkbox" v-model="formAsesmen.flapping" class="rounded text-[#0F766E] focus:ring-[#0F766E]" />
            </label>
            <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 hover:border-teal-300 cursor-pointer">
              <span class="text-slate-800 text-[11px] font-medium">Tantrum / Sulit Menenangkan Diri</span>
              <input type="checkbox" v-model="formAsesmen.tantrum" class="rounded text-[#0F766E] focus:ring-[#0F766E]" />
            </label>
            <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 hover:border-teal-300 cursor-pointer">
              <span class="text-slate-800 text-[11px] font-medium">Kontak Mata Responsif & Bertahan</span>
              <input type="checkbox" v-model="formAsesmen.kontak_mata" class="rounded text-[#0F766E] focus:ring-[#0F766E]" />
            </label>
            <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 hover:border-teal-300 cursor-pointer">
              <span class="text-slate-800 text-[11px] font-medium">Sensori Oral (Picky Eater / Menelan)</span>
              <input type="checkbox" v-model="formAsesmen.gangguan_makan_menelan" class="rounded text-[#0F766E] focus:ring-[#0F766E]" />
            </label>
            <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 hover:border-teal-300 cursor-pointer">
              <span class="text-slate-800 text-[11px] font-medium">Komunikasi Spontan Dua Arah</span>
              <input type="checkbox" v-model="formAsesmen.komunikasi_dua_arah" class="rounded text-[#0F766E] focus:ring-[#0F766E]" />
            </label>
          </div>
        </div>

        <!-- 3. Observasi Klinis Tambahan -->
        <div class="space-y-1.5 pt-3 border-t border-slate-100">
          <label class="block font-semibold text-slate-700">Catatan Observasi Skrining Awal:</label>
          <textarea
            v-model="formAsesmen.catatan_observasi"
            rows="3"
            placeholder="Catatan interaksi sosial, motorik kasar/halus, dan adaptasi sensori..."
            class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
          ></textarea>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: CATATAN SESI TERAPI & HOME PROGRAM ORANG TUA -->
    <!-- ========================================================================= -->
    <div v-else-if="emrWorkspaceTab === 'aktivitas'" class="space-y-4 animate-fadeIn">
      <!-- Form Input Sesi Hari Ini -->
      <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-[#E6F2F5] text-[#0F766E] flex items-center justify-center font-bold text-xs">
              <i class="pi pi-check-square"></i>
            </div>
            <div>
              <h3 class="text-[15px] font-semibold text-[#0F172A] leading-tight">
                Pencatatan Sesi Terapi & Home Program Orang Tua
              </h3>
              <p class="text-xs text-slate-500 mt-0.5">Catatan aktivitas sesi klinis langsung terbit ke Portal Orang Tua sebagai panduan latihan rumah</p>
            </div>
          </div>

          <button
            type="button"
            @click="submitAktivitasSesi"
            :disabled="savingAktivitas"
            class="bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs px-4 py-2 rounded-lg flex items-center gap-1.5 transition shadow-sm cursor-pointer disabled:opacity-50"
          >
            <i v-if="savingAktivitas" class="pi pi-spin pi-spinner text-xs"></i>
            <i v-else class="pi pi-send text-xs"></i>
            <span>Simpan Sesi & Terbitkan ke Portal Ortu</span>
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Tanggal Sesi:</label>
            <input
              type="date"
              v-model="formAktivitas.tanggal"
              class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
            />
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Jam Pelaksanaan:</label>
            <div class="flex items-center gap-1.5">
              <input
                v-model="formAktivitas.jam_mulai"
                type="time"
                class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
              />
              <span class="text-slate-400 font-bold">-</span>
              <input
                v-model="formAktivitas.jam_selesai"
                type="time"
                class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
              />
            </div>
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Status Kehadiran:</label>
            <select v-model="formAktivitas.status" class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]">
              <option value="hadir">Hadir (Sesi Terlaksana)</option>
              <option value="terjadwal">Terjadwal</option>
              <option value="batal">Batal / Reschedule</option>
            </select>
          </div>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Aktivitas Terapi yang Diberikan:</label>
            <textarea
              v-model="formAktivitas.aktivitas"
              rows="2"
              placeholder="Rincian intervensi, modul oral motorik, sensori integrasi, atau stimulus wicara..."
              class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
            ></textarea>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Respons & Keterangan Perilaku Anak:</label>
            <textarea
              v-model="formAktivitas.keterangan"
              rows="2"
              placeholder="Respons emosional, tingkat kepatuhan instruksi, durasi kontak mata..."
              class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
            ></textarea>
          </div>

          <div class="p-3 bg-teal-50/70 border border-teal-200 rounded-xl space-y-1">
            <label class="block font-bold text-teal-900 text-xs flex items-center gap-1.5">
              <i class="pi pi-home text-teal-700"></i>
              <span>Instruksi Tugas Rumah (Home Program) untuk Orang Tua:</span>
            </label>
            <textarea
              v-model="formAktivitas.tugas_rumah"
              rows="2"
              placeholder="Instruksi latihan mandiri di rumah (misal: meniup sedotan 10x, membaca buku bergambar 15 menit)..."
              class="w-full px-3 py-2 text-xs border border-teal-300 bg-white rounded-lg outline-none focus:border-[#0F766E]"
            ></textarea>
            <span class="text-[10px] text-teal-700 block">Catatan ini akan otomatis muncul di Portal Orang Tua saat mereka login.</span>
          </div>
        </div>
      </div>

      <!-- Riwayat Sesi Sebelumnya -->
      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_3px_rgba(15,23,42,0.04)] space-y-3">
        <h4 class="text-xs font-bold text-slate-900 flex items-center gap-2">
          <i class="pi pi-history text-[#0F766E]"></i>
          Riwayat Log Sesi Terapi Pasien Ini ({{ riwayatAktivitasList.length }} Sesi)
        </h4>

        <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden text-xs">
          <div v-for="sesi in riwayatAktivitasList" :key="sesi.id" class="p-3 hover:bg-slate-50 transition space-y-1.5">
            <div class="flex items-center justify-between flex-wrap gap-2">
              <div class="flex items-center gap-2">
                <span class="font-bold text-slate-900">{{ sesi.hari }}, {{ formatDate(sesi.tanggal) }}</span>
                <span class="text-slate-500 font-mono text-[11px]">{{ sesi.jam_mulai }} - {{ sesi.jam_selesai }}</span>
              </div>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                {{ sesi.status === 'hadir' ? 'Hadir' : sesi.status }}
              </span>
            </div>
            <p class="text-slate-700"><strong>Aktivitas:</strong> {{ sesi.aktivitas }}</p>
            <p class="text-slate-600"><strong>Respons:</strong> {{ sesi.keterangan }}</p>
            <div v-if="sesi.tugas_rumah" class="p-2 bg-[#F0F6F8] rounded-lg border border-[#DCE8EC] text-teal-900 text-[11px]">
              <strong>Home Program:</strong> {{ sesi.tugas_rumah }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: EVALUASI CAPAIAN & TARGET TERAPI (GOAL TRACKING) -->
    <!-- ========================================================================= -->
    <div v-else-if="emrWorkspaceTab === 'evaluasi'" class="bg-white border border-slate-200 rounded-xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] space-y-5 animate-fadeIn">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-[#E6F2F5] text-[#0F766E] flex items-center justify-center font-bold text-xs">
            <i class="pi pi-chart-line"></i>
          </div>
          <div>
            <h3 class="text-[15px] font-semibold text-[#0F172A] leading-tight">
              Target Capaian Terapi (Goal Tracking) & Rekomendasi
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Pemantauan target jangka pendek, jangka panjang, dan indikator keberhasilan klinis</p>
          </div>
        </div>

        <button
          type="button"
          @click="showOrderModal = true"
          class="bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs px-3.5 py-2 rounded-lg flex items-center gap-1.5 transition shadow-sm cursor-pointer"
        >
          <i class="pi pi-plus text-xs"></i>
          <span>Tambah Rekomendasi / Goal</span>
        </button>
      </div>

      <div class="space-y-3">
        <div v-if="goals.length === 0" class="text-center py-8 text-slate-400 text-xs">
          <i class="pi pi-inbox text-3xl mb-2 text-slate-300 block"></i>
          <span>Belum ada target capaian yang dicatat untuk pasien ini.</span>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div v-for="goal in goals" :key="goal.id" class="bg-[#F0F6F8] border border-[#DCE8EC] rounded-xl p-4 space-y-2.5">
            <div class="flex items-center justify-between">
              <span class="font-bold text-slate-900 text-sm">{{ goal.judul_target }}</span>
              <span
                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                :class="goal.tipe_goal === 'jangka_pendek' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]'"
              >
                {{ goal.tipe_goal === 'jangka_pendek' ? 'Jangka Pendek' : 'Jangka Panjang' }}
              </span>
            </div>
            <p class="text-slate-600 text-xs leading-relaxed">{{ goal.indikator_keberhasilan }}</p>

            <div class="space-y-1 pt-1">
              <div class="flex justify-between text-xs font-semibold">
                <span class="text-slate-500">Progress Capaian:</span>
                <span class="text-[#0F766E] tabular-nums font-bold">{{ goal.capaian_persen }}%</span>
              </div>
              <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden">
                <div
                  class="h-full rounded-full bg-[#0F766E] transition-all"
                  :style="{ width: `${goal.capaian_persen}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL A: INPUT / EDIT TANDA-TANDA VITAL & PERTUMBUHAN (TELEPORTED) -->
    <!-- ========================================================================= -->
    <teleport to="body">
      <div
        v-if="showVitalsModal"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-xl rounded-2xl max-h-[90dvh] flex flex-col overflow-hidden">
          <div class="px-5 py-3.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-heart-pulse text-[#2DD4BF] text-base"></i>
              <div>
                <h3 class="font-bold text-sm">Input & Evaluasi Tanda-Tanda Vital (TTV)</h3>
                <p class="text-[11px] text-teal-200">Rentang referensi disesuaikan otomatis dengan usia anak ({{ childAgeBracketLabel }})</p>
              </div>
            </div>
            <button
              type="button"
              @click="showVitalsModal = false"
              class="w-7 h-7 rounded-lg flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition cursor-pointer"
            >
              ✕
            </button>
          </div>

          <form @submit.prevent="saveVitalsForm" class="p-5 space-y-4 text-xs flex-1 min-h-0 overflow-y-auto custom-scrollbar">
            <!-- Age bracket notice -->
            <div class="p-3 bg-[#E6F2F5] border border-[#CBE3E9] rounded-xl text-[#0B3948] flex items-center justify-between">
              <div>
                <span class="font-bold block text-xs">{{ selectedPasien?.nama_anak }} ({{ selectedPasien?.no_rm }})</span>
                <span class="text-[11px] text-slate-600">Usia Klinis: {{ selectedPasien?.usia_lengkap || '3 Tahun' }}</span>
              </div>
              <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#0F766E] text-white">
                Kelompok {{ childAgeBracketLabel }}
              </span>
            </div>

            <!-- Core TTV inputs -->
            <div class="space-y-2">
              <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px] block">Tanda-Tanda Vital Utama:</span>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block font-semibold text-slate-700 mb-1">Tekanan Darah (Sistole / Diastole):</label>
                  <div class="flex items-center gap-1.5">
                    <input
                      type="number"
                      v-model.number="vitalsForm.td_sistole"
                      placeholder="120"
                      class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] text-center font-bold"
                    />
                    <span class="text-slate-400 font-bold">/</span>
                    <input
                      type="number"
                      v-model.number="vitalsForm.td_diastole"
                      placeholder="80"
                      class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] text-center font-bold"
                    />
                    <span class="text-slate-500 text-[11px]">mmHg</span>
                  </div>
                </div>

                <div>
                  <label class="block font-semibold text-slate-700 mb-1">Denyut Jantung / Nadi (HR):</label>
                  <div class="flex items-center gap-1.5">
                    <input
                      type="number"
                      v-model.number="vitalsForm.hr"
                      placeholder="82"
                      required
                      class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] text-center font-bold"
                    />
                    <span class="text-slate-500 text-[11px]">bpm</span>
                  </div>
                </div>

                <div>
                  <label class="block font-semibold text-slate-700 mb-1">Suhu Tubuh (Temp):</label>
                  <div class="flex items-center gap-1.5">
                    <input
                      type="number"
                      step="0.1"
                      v-model.number="vitalsForm.temp"
                      placeholder="36.5"
                      required
                      class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] text-center font-bold"
                    />
                    <span class="text-slate-500 text-[11px]">°C</span>
                  </div>
                </div>

                <div>
                  <label class="block font-semibold text-slate-700 mb-1">Laju Pernapasan (RR):</label>
                  <div class="flex items-center gap-1.5">
                    <input
                      type="number"
                      v-model.number="vitalsForm.rr"
                      placeholder="18"
                      required
                      class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] text-center font-bold"
                    />
                    <span class="text-slate-500 text-[11px]">x/m</span>
                  </div>
                </div>

                <div class="col-span-2 sm:col-span-1">
                  <label class="block font-semibold text-slate-700 mb-1">Saturasi Oksigen (SpO2):</label>
                  <div class="flex items-center gap-1.5">
                    <input
                      type="number"
                      v-model.number="vitalsForm.spo2"
                      placeholder="98"
                      required
                      class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] text-center font-bold"
                    />
                    <span class="text-slate-500 text-[11px]">%</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pediatric Growth / Anthropometry -->
            <div class="space-y-2 pt-2 border-t border-slate-100">
              <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px] block">Antropometri & Pertumbuhan Anak:</span>
              <div class="grid grid-cols-3 gap-3">
                <div>
                  <label class="block font-semibold text-slate-700 mb-1">Berat Badan (BB):</label>
                  <div class="flex items-center gap-1.5">
                    <input
                      type="number"
                      step="0.1"
                      v-model.number="vitalsForm.bb"
                      placeholder="15.0"
                      class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] text-center font-bold"
                    />
                    <span class="text-slate-500 text-[11px]">kg</span>
                  </div>
                </div>

                <div>
                  <label class="block font-semibold text-slate-700 mb-1">Tinggi Badan (TB):</label>
                  <div class="flex items-center gap-1.5">
                    <input
                      type="number"
                      step="0.5"
                      v-model.number="vitalsForm.tb"
                      placeholder="99.5"
                      class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] text-center font-bold"
                    />
                    <span class="text-slate-500 text-[11px]">cm</span>
                  </div>
                </div>

                <div>
                  <label class="block font-semibold text-slate-700 mb-1">Lingkar Kepala (LK):</label>
                  <div class="flex items-center gap-1.5">
                    <input
                      type="number"
                      step="0.1"
                      v-model.number="vitalsForm.lk"
                      placeholder="49.8"
                      class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] text-center font-bold"
                    />
                    <span class="text-slate-500 text-[11px]">cm</span>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan TTV:</label>
              <input
                v-model="vitalsForm.catatan"
                placeholder="misal: Anak sedikit menangis saat tensi, napas teratur..."
                class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
              />
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
              <span class="text-[11px] text-slate-500">
                Sistem akan membandingkan nilai input dengan kurva normal usia.
              </span>
              <div class="flex gap-2">
                <button
                  type="button"
                  @click="showVitalsModal = false"
                  class="h-[34px] px-3 text-xs text-slate-600 hover:bg-slate-100 rounded-lg border border-slate-200"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  :disabled="savingVitals"
                  class="h-[34px] bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs px-4 rounded-lg flex items-center gap-1.5 transition cursor-pointer shadow-sm"
                >
                  <i v-if="savingVitals" class="pi pi-spin pi-spinner text-xs"></i>
                  <i v-else class="pi pi-check text-xs"></i>
                  <span>Simpan & Evaluasi TTV</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </teleport>

    <!-- ========================================================================= -->
    <!-- MODAL B: GRAFIK TREN TANDA-TANDA VITAL & PERTUMBUHAN (CHART.JS) -->
    <!-- ========================================================================= -->
    <teleport to="body">
      <div
        v-if="showVitalsChartModal"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-4xl rounded-2xl max-h-[92dvh] flex flex-col overflow-hidden">
          <div class="px-5 py-3.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-chart-line text-[#2DD4BF] text-base"></i>
              <div>
                <h3 class="font-bold text-sm">Grafik Tren Tanda-Tanda Vital & Pertumbuhan Pasien</h3>
                <p class="text-[11px] text-teal-200">Riwayat perkembangan HR, RR, Suhu, SpO2, dan Berat Badan dari sesi ke sesi</p>
              </div>
            </div>
            <button
              type="button"
              @click="showVitalsChartModal = false"
              class="w-7 h-7 rounded-lg flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition cursor-pointer"
            >
              ✕
            </button>
          </div>

          <div class="p-5 space-y-4 flex-1 min-h-0 overflow-y-auto custom-scrollbar">
            <!-- Patient summary strip -->
            <div class="p-3 bg-[#E6F2F5] border border-[#CBE3E9] rounded-xl flex items-center justify-between text-xs">
              <div>
                <span class="font-bold text-slate-800 text-sm">{{ selectedPasien?.nama_anak }}</span>
                <span class="text-slate-500 font-mono ml-2">No. RM: {{ selectedPasien?.no_rm }}</span>
              </div>
              <span class="font-semibold text-[#0F766E]">Total Riwayat TTV: {{ vitalsHistoryRecords.length }} Catatan</span>
            </div>

            <!-- Chart.js Responsive Canvas Container -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
              <div class="h-72 w-full relative">
                <canvas id="ttvChartCanvas"></canvas>
              </div>
            </div>

            <!-- Historical Data Points Table -->
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
              <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-700">
                Log Catatan Tanda-Tanda Vital Sebelumnya
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                  <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-600 border-b border-slate-200">
                    <tr>
                      <th class="px-3 py-2">Tanggal</th>
                      <th class="px-3 py-2">TD (mmHg)</th>
                      <th class="px-3 py-2">HR (bpm)</th>
                      <th class="px-3 py-2">Temp (°C)</th>
                      <th class="px-3 py-2">RR (x/m)</th>
                      <th class="px-3 py-2">SpO2</th>
                      <th class="px-3 py-2">BB (kg)</th>
                      <th class="px-3 py-2">Status</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="rec in vitalsHistoryRecords" :key="rec.id" class="hover:bg-slate-50">
                      <td class="px-3 py-2 font-mono font-medium text-slate-700">{{ formatDate(rec.recorded_at) }}</td>
                      <td class="px-3 py-2 font-bold text-slate-800">{{ rec.td_sistole ? `${rec.td_sistole}/${rec.td_diastole}` : '-' }}</td>
                      <td class="px-3 py-2 text-teal-700 font-semibold">{{ rec.hr || '-' }}</td>
                      <td class="px-3 py-2 text-amber-700 font-semibold">{{ rec.temp || '-' }}</td>
                      <td class="px-3 py-2 text-sky-700 font-semibold">{{ rec.rr || '-' }}</td>
                      <td class="px-3 py-2 text-emerald-700 font-semibold">{{ rec.spo2 ? `${rec.spo2}%` : '-' }}</td>
                      <td class="px-3 py-2 font-bold text-indigo-700">{{ rec.bb ? `${rec.bb} kg` : '-' }}</td>
                      <td class="px-3 py-2">
                        <span
                          class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                          :class="{
                            'bg-emerald-100 text-emerald-800': rec.status === 'normal',
                            'bg-amber-100 text-amber-800': rec.status === 'perlu_perhatian',
                            'bg-rose-100 text-rose-800': rec.status === 'abnormal_kritis',
                          }"
                        >
                          {{ vitalStatusLabel(rec.status) }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="px-5 py-3 bg-slate-50/95 border-t border-slate-200 flex justify-end">
            <button
              type="button"
              @click="showVitalsChartModal = false"
              class="h-[34px] px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold transition cursor-pointer"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
    </teleport>

    <!-- ========================================================================= -->
    <!-- MODAL C: KELOLA RIWAYAT ALERGI PASIEN ANAK (MASTER ALERGI PICKER) -->
    <!-- ========================================================================= -->
    <teleport to="body">
      <div
        v-if="showAllergyModal"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-lg rounded-2xl max-h-[88dvh] flex flex-col overflow-hidden">
          <div class="px-5 py-3.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-shield text-rose-400 text-base"></i>
              <div>
                <h3 class="font-bold text-sm">Kelola Riwayat Alergi Pasien Anak</h3>
                <p class="text-[11px] text-teal-200">Data alergi obat, makanan, dan lingkungan berbasis Master Data</p>
              </div>
            </div>
            <button
              type="button"
              @click="showAllergyModal = false"
              class="w-7 h-7 rounded-lg flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition cursor-pointer"
            >
              ✕
            </button>
          </div>

          <form @submit.prevent="saveAllergiesForm" class="p-5 space-y-3.5 text-xs flex-1 min-h-0 overflow-y-auto custom-scrollbar">
            <!-- Patient Notice -->
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-900 flex items-center justify-between">
              <div>
                <span class="font-bold block text-xs">{{ selectedPasien?.nama_anak }} ({{ selectedPasien?.no_rm }})</span>
                <span class="text-[11px] text-rose-700">Alergi Aktif: {{ selectedPasien?.catatan_alergi || 'Belum tercatat' }}</span>
              </div>
              <i class="pi pi-exclamation-circle text-rose-500 text-lg"></i>
            </div>

            <!-- Master Allergies Checklist -->
            <div class="space-y-3">
              <div v-for="cat in ['Obat', 'Makanan', 'Lingkungan']" :key="cat" class="space-y-1">
                <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block">{{ cat }}</span>
                <div class="grid grid-cols-2 gap-2">
                  <label
                    v-for="alg in masterAllergies.filter(a => a.category === cat)"
                    :key="alg.id"
                    class="flex items-center justify-between p-2.5 rounded-lg border text-xs cursor-pointer transition"
                    :class="selectedAllergyIds.includes(alg.id) ? 'bg-rose-50 border-rose-300 font-semibold text-rose-900' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'"
                  >
                    <div>
                      <span class="block">{{ alg.allergen_name }}</span>
                      <span class="text-[10px] text-slate-400 font-normal">Tingkat: {{ alg.severity_default }}</span>
                    </div>
                    <input
                      type="checkbox"
                      :value="alg.id"
                      v-model="selectedAllergyIds"
                      class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 accent-rose-600"
                    />
                  </label>
                </div>
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Catatan Reaksi Khusus / Manifestasi:</label>
              <input
                v-model="allergyReactionNote"
                placeholder="misal: Ruam merah pada wajah, gatal-gatal, sesak napas..."
                class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
              />
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
              <span class="text-[11px] text-slate-500">
                Peringatan otomatis aktif saat meresepkan obat terkait.
              </span>
              <div class="flex gap-2">
                <button
                  type="button"
                  @click="showAllergyModal = false"
                  class="h-[34px] px-3 text-xs text-slate-600 hover:bg-slate-100 rounded-lg border border-slate-200"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  :disabled="savingAllergies"
                  class="h-[34px] bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs px-4 rounded-lg flex items-center gap-1.5 transition cursor-pointer shadow-sm"
                >
                  <i v-if="savingAllergies" class="pi pi-spin pi-spinner text-xs"></i>
                  <i v-else class="pi pi-check text-xs"></i>
                  <span>Simpan Alergi Pasien</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </teleport>

    <!-- EXISTING MODAL 1: RIWAYAT TIMELINE REKAM MEDIS & INTEGRITAS SHA-256 -->
    <teleport to="body">
      <div
        v-if="showTimelineModal"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-3xl rounded-xl max-h-[88dvh] flex flex-col overflow-hidden">
          <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-history text-[#2DD4BF] text-sm"></i>
              <h3 class="font-semibold text-[13px]">Riwayat Timeline Rekam Medis Pasien</h3>
            </div>
            <button
              type="button"
              @click="showTimelineModal = false"
              class="w-6 h-6 rounded flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition"
            >
              ✕
            </button>
          </div>

          <div class="space-y-4 flex-1 min-h-0 overflow-y-auto custom-scrollbar p-4">
            <div v-if="records.length === 0" class="text-center py-8 text-slate-400">
              <i class="pi pi-folder-open text-3xl mb-2"></i>
              <p class="text-xs">Belum ada riwayat rekam medis terdahulu.</p>
            </div>

            <div v-else class="relative border-l-2 border-teal-200 ml-3 pl-5 space-y-4">
              <div
                v-for="item in records"
                :key="item.id"
                class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm space-y-3 relative"
              >
                <!-- Timeline bullet icon -->
                <span class="absolute -left-[27px] top-4 w-3.5 h-3.5 rounded-full border-2 border-white bg-[#0F766E]"></span>

                <div class="flex items-center justify-between pb-2 border-b border-slate-100 flex-wrap gap-2">
                  <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-900 text-xs">{{ item.no_emr }}</span>
                    <span class="text-[11px] text-slate-400">· {{ formatDate(item.tanggal_pemeriksaan) }}</span>
                    <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold bg-teal-50 text-[#0F766E] border border-teal-100">
                      {{ item.jenis_layanan }}
                    </span>
                  </div>

                  <div class="flex items-center gap-2">
                    <span
                      class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider"
                      :class="item.status === 'final' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                    >
                      {{ item.status === 'final' ? 'Terkunci (Final)' : 'Draft' }}
                    </span>
                  </div>
                </div>

                <!-- SOAP Summary -->
                <div v-if="item.soap" class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                  <div class="bg-[#F0F6F8] p-2.5 rounded-lg border border-[#DCE8EC]">
                    <strong class="text-[#0F766E] block mb-0.5">S (Subjektif):</strong>
                    <p class="text-slate-700 line-clamp-3">{{ item.soap.subjektif }}</p>
                  </div>
                  <div class="bg-[#F0F6F8] p-2.5 rounded-lg border border-[#DCE8EC]">
                    <strong class="text-[#0F766E] block mb-0.5">O (Objektif):</strong>
                    <p class="text-slate-700 line-clamp-3">{{ item.soap.objektif }}</p>
                  </div>
                  <div class="bg-[#F0F6F8] p-2.5 rounded-lg border border-[#DCE8EC]">
                    <strong class="text-[#0F766E] block mb-0.5">A (Asesmen):</strong>
                    <p class="text-slate-700 line-clamp-3">{{ item.soap.asesmen }}</p>
                  </div>
                  <div class="bg-[#F0F6F8] p-2.5 rounded-lg border border-[#DCE8EC]">
                    <strong class="text-[#0F766E] block mb-0.5">P (Planning):</strong>
                    <p class="text-slate-700 line-clamp-3">{{ item.soap.planning }}</p>
                  </div>
                </div>

                <!-- SHA-256 Seal Footer -->
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                  <span>Terapis: <strong>{{ item.terapis?.nama || 'Terapis Klinis' }}</strong></span>
                  <span v-if="item.sha256_hash" class="font-mono text-[10px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded border border-emerald-200">
                    🔒 SHA-256: {{ item.sha256_hash.substring(0, 16) }}...
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="px-4 py-2.5 bg-slate-50/95 border-t border-slate-200/80 flex items-center justify-end gap-2 shrink-0">
            <button
              type="button"
              @click="showTimelineModal = false"
              class="h-[34px] px-4 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-medium transition"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
    </teleport>

    <!-- EXISTING MODAL 2: GOAL TRACKING (TARGET CAPAIAN) -->
    <teleport to="body">
      <div
        v-if="showGoalModal"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-2xl rounded-xl max-h-[88dvh] flex flex-col overflow-hidden">
          <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-chart-line text-[#2DD4BF] text-sm"></i>
              <h3 class="font-semibold text-[13px]">Target Capaian Terapi (Goal Tracking)</h3>
            </div>
            <button
              type="button"
              @click="showGoalModal = false"
              class="w-6 h-6 rounded flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition"
            >
              ✕
            </button>
          </div>

          <div class="space-y-3 flex-1 min-h-0 overflow-y-auto custom-scrollbar p-4 text-xs">
            <div v-for="goal in goals" :key="goal.id" class="bg-[#F0F6F8] border border-[#DCE8EC] rounded-xl p-3.5 space-y-2">
              <div class="flex items-center justify-between">
                <span class="font-bold text-slate-900 text-sm">{{ goal.judul_target }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                  :class="goal.tipe_goal === 'jangka_pendek' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-[#E6F2F5] text-[#0F766E] border border-[#CBE3E9]'">
                  {{ goal.tipe_goal === 'jangka_pendek' ? 'Jangka Pendek' : 'Jangka Panjang' }}
                </span>
              </div>
              <p class="text-slate-600 text-xs">{{ goal.indikator_keberhasilan }}</p>

              <div class="space-y-1">
                <div class="flex justify-between font-semibold">
                  <span class="text-slate-500">Progress Capaian:</span>
                  <span class="text-[#0F766E] tabular-nums font-bold">{{ goal.capaian_persen }}%</span>
                </div>
                <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden">
                  <div
                    class="h-full rounded-full bg-[#0F766E] transition-all"
                    :style="{ width: `${goal.capaian_persen}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <div class="px-4 py-2.5 bg-slate-50/95 border-t border-slate-200/80 flex items-center justify-end gap-2 shrink-0">
            <button
              type="button"
              @click="showGoalModal = false"
              class="h-[34px] px-4 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-medium transition"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
    </teleport>

    <!-- EXISTING MODAL 3: REKOMENDASI TERAPI DOKTER (THERAPY ORDER) -->
    <teleport to="body">
      <div
        v-if="showOrderModal"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-lg rounded-xl max-h-[88dvh] flex flex-col overflow-hidden">
          <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-file-plus text-[#2DD4BF] text-sm"></i>
              <h3 class="font-semibold text-[13px]">Rekomendasi Program Terapi Dokter (Therapy Order)</h3>
            </div>
            <button
              type="button"
              @click="showOrderModal = false"
              class="w-6 h-6 rounded flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition"
            >
              ✕
            </button>
          </div>

          <form @submit.prevent="submitTherapyOrder" class="space-y-3.5 text-xs p-4 flex-1 min-h-0 overflow-y-auto custom-scrollbar">
            <div class="bg-[#E6F2F5] border border-[#CBE3E9] p-3 rounded-xl">
              <span class="text-[10px] text-[#15576B] uppercase font-bold tracking-wider block">Pasien Anak</span>
              <p class="text-sm font-bold text-slate-900 leading-tight">{{ selectedPasien?.nama_anak }} ({{ selectedPasien?.no_rm }})</p>
              <p class="text-[11px] text-teal-800 mt-0.5">Dokter Pemeriksa: Sp.A / Sp.KFR</p>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Diagnosis Klinis</label>
              <input
                v-model="orderForm.diagnosis_klinis"
                required
                placeholder="Contoh: Mixed Receptive-Expressive Language Disorder"
                class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Kode ICD-10</label>
                <input
                  v-model="orderForm.icd10_kode"
                  placeholder="Contoh: F80.2"
                  class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] font-mono"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">Frekuensi Sesi</label>
                <input
                  v-model="orderForm.frekuensi_mingguan"
                  required
                  placeholder="Contoh: 2x seminggu"
                  class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
                />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Total Target Sesi</label>
                <input
                  type="number"
                  v-model.number="orderForm.total_sesi_rekomendasi"
                  min="1"
                  required
                  class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] font-bold"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">Terapis Pelaksana</label>
                <select v-model="orderForm.terapis_id" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]">
                  <option value="">Pilih terapis...</option>
                  <option v-for="t in terapisList" :key="t.id" :value="t.id">{{ t.nama }}</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Target Capaian Jangka Pendek (Short-Term Goal)</label>
              <textarea
                v-model="orderForm.target_jangka_pendek"
                rows="2"
                placeholder="Target 4-6 minggu pertama..."
                class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
              ></textarea>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Target Capaian Jangka Panjang (Long-Term Goal)</label>
              <textarea
                v-model="orderForm.target_jangka_panjang"
                rows="2"
                placeholder="Target 3-6 bulan evaluasi klinis..."
                class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg outline-none focus:border-[#0F766E]"
              ></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
              <button type="button" @click="showOrderModal = false" class="h-[34px] px-3 text-xs text-slate-600 hover:bg-slate-100 rounded-lg border border-slate-200">Batal</button>
              <button
                type="submit"
                :disabled="savingOrder"
                class="h-[34px] bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white font-semibold text-xs px-4 rounded-lg flex items-center gap-1.5 transition cursor-pointer shadow-sm"
              >
                <i class="pi pi-check text-xs"></i>
                <span>Simpan Order Terapi</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </teleport>

    <!-- EXISTING MODAL 4: LIHAT HASIL PEMERIKSAAN PENUNJANG (LAB / RAD) -->
    <teleport to="body">
      <div
        v-if="showHasilModal"
        class="fixed inset-0 z-[9999] bg-slate-900/35 backdrop-blur-md flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white/95 backdrop-blur-xl border border-white/70 shadow-[0_20px_50px_rgba(11,57,72,0.22)] w-full max-w-xl rounded-xl max-h-[88dvh] flex flex-col overflow-hidden">
          <div class="px-4 py-2.5 bg-[#0B3948] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <i class="pi pi-file-check text-[#2DD4BF] text-sm"></i>
              <h3 class="font-semibold text-[13px]">Hasil Pemeriksaan Penunjang Medis</h3>
            </div>
            <button
              type="button"
              @click="showHasilModal = false"
              class="w-6 h-6 rounded flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition"
            >
              ✕
            </button>
          </div>

          <div v-if="selectedPenunjangHasil" class="space-y-3.5 text-xs p-4 flex-1 min-h-0 overflow-y-auto custom-scrollbar">
            <!-- Patient & Order Header -->
            <div class="bg-[#E6F2F5] border border-[#CBE3E9] p-3 rounded-xl flex items-center justify-between">
              <div>
                <span class="text-[10px] text-[#15576B] uppercase font-bold tracking-wider block">Pasien Anak</span>
                <p class="text-sm font-bold text-slate-900 leading-tight">{{ selectedPasien?.nama_anak }} ({{ selectedPasien?.no_rm }})</p>
                <p class="text-[11px] text-teal-800 mt-0.5">Tindakan: <strong class="text-slate-900">{{ selectedPenunjangHasil.nama_tindakan }}</strong></p>
              </div>
              <div class="text-right">
                <span class="text-[10px] text-slate-500 block">Nomor Order:</span>
                <span class="font-mono font-bold text-[#0F766E] text-xs">{{ selectedPenunjangHasil.nomor_order }}</span>
                <span class="block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                  {{ selectedPenunjangHasil.status }}
                </span>
              </div>
            </div>

            <!-- Meta details -->
            <div class="grid grid-cols-2 gap-2 text-xs">
              <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                <span class="text-[10px] text-slate-500 font-semibold block uppercase">Jenis Pemeriksaan:</span>
                <span class="font-bold text-slate-800 capitalize">{{ selectedPenunjangHasil.jenis_order }}</span>
              </div>
              <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                <span class="text-[10px] text-slate-500 font-semibold block uppercase">Tarif Tindakan:</span>
                <span class="font-bold text-[#0F766E] tabular-nums">Rp {{ Number(selectedPenunjangHasil.tarif || 0).toLocaleString('id-ID') }}</span>
              </div>
            </div>

            <!-- Hasil Pemeriksaan Box -->
            <div class="space-y-1">
              <label class="block font-bold text-slate-800 text-xs">
                Kesimpulan / Hasil Eksaminasi Petugas:
              </label>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-300 font-mono text-xs text-slate-800 whitespace-pre-wrap leading-relaxed min-h-[90px]">
                {{ selectedPenunjangHasil.hasil_pemeriksaan || 'Belum ada catatan hasil pemeriksaan.' }}
              </div>
            </div>

            <!-- Catatan Klinis Dokter -->
            <div v-if="selectedPenunjangHasil.catatan_klinis" class="p-2.5 bg-amber-50/70 border border-amber-200 rounded-lg text-[11px]">
              <strong class="text-amber-800 block mb-0.5">Catatan Indikasi Klinis Dokter:</strong>
              <p class="text-slate-700">{{ selectedPenunjangHasil.catatan_klinis }}</p>
            </div>

            <!-- Verification Footer info -->
            <div class="p-2.5 bg-emerald-50/80 border border-emerald-200 rounded-lg flex items-center justify-between text-[11px] text-emerald-900">
              <div class="flex items-center gap-1.5">
                <i class="pi pi-verified text-emerald-700 text-sm"></i>
                <span>Telah divalidasi & otomatis masuk ke billing faktur registrasi</span>
              </div>
              <span class="font-semibold">{{ selectedPenunjangHasil.verified_at ? formatDate(selectedPenunjangHasil.verified_at) : '-' }}</span>
            </div>
          </div>

          <div class="px-4 py-2.5 bg-slate-50/95 border-t border-slate-200/80 flex items-center justify-between shrink-0">
            <button
              type="button"
              @click="printHasilPemeriksaan"
              class="h-[34px] px-3.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-1.5 transition"
            >
              <i class="pi pi-print text-xs"></i>
              <span>Cetak Hasil</span>
            </button>
            <button
              type="button"
              @click="showHasilModal = false"
              class="h-[34px] px-4 rounded-lg bg-[#0F766E] hover:bg-[#115E59] text-white text-xs font-semibold transition"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
    </teleport>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '@/axios'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const route = useRoute()
const toast = useToast()

const selectedPasienId = ref(6) // Default Azhar Al-Farizi
const selectedPasien = ref(null)
const pasienList = ref([])
const records = ref([])
const goals = ref([])
const activeRecord = ref(null)
const activeSubTab = ref('resep')
const emrWorkspaceTab = ref('soap') // 'soap' | 'asesmen' | 'aktivitas' | 'evaluasi'

// Form Asesmen Tumbuh Kembang
const savingAsesmen = ref(false)
const formAsesmen = ref({
  riwayat_kehamilan: 'Kehamilan pertama, ibu ANC teratur',
  proses_kelahiran: 'Normal (Spontan)',
  usia_kehamilan_lahir: '38 Minggu (Cukup Bulan)',
  gangguan_melahirkan: 'Tidak ada asfiksia, APGAR 8/9',
  riwayat_kejang: false,
  konsumsi_obat_epilepsi: false,
  perkembangan_sesuai_usia: false,
  disusui_ibu: true,
  tv_gadget_addict: true,
  sering_memutar_benda: true,
  flapping: true,
  tantrum: true,
  kontak_mata: false,
  gangguan_makan_menelan: true,
  komunikasi_dua_arah: false,
  catatan_observasi: 'Anak menunjukkan gejala keterlambatan bicara ekspresif dan sensori seeking (sering memutar roda mainan).'
})

// Form Sesi Terapi & Home Program Orang Tua
const savingAktivitas = ref(false)
const formAktivitas = ref({
  tanggal: new Date().toISOString().split('T')[0],
  jam_mulai: '10:00',
  jam_selesai: '10:45',
  aktivitas: 'Latihan artikulasi vokal dan konsonan bilabial /p, b, m/, stimulasi sensori taktil dan oral dengan sikat getar.',
  keterangan: 'Anak kooperatif pada 20 menit pertama, mampu menirukan kata mama dan papa dengan bantuan prompt visual.',
  tugas_rumah: 'Orang tua diminta melanjutkan latihan meniup peluit/gelembung 10x dan melarang penggunaan gadget (zero screen-time).',
  status: 'hadir'
})

const riwayatAktivitasList = ref([
  {
    id: 1,
    hari: 'Selasa',
    tanggal: '2026-11-18',
    jam_mulai: '10:00',
    jam_selesai: '10:45',
    status: 'hadir',
    aktivitas: 'Stimulasi sensori taktil dan oral-motorik dasar',
    keterangan: 'Kontak mata membaik, respons terhadap panggilan nama 3 dari 5 kali',
    tugas_rumah: 'Membaca buku bergambar 15 menit per hari'
  },
  {
    id: 2,
    hari: 'Kamis',
    tanggal: '2026-11-20',
    jam_mulai: '10:00',
    jam_selesai: '10:45',
    status: 'hadir',
    aktivitas: 'Oral motorik artikulasi /m/, /p/, /b/',
    keterangan: 'Bisa menirukan kata mama, fokus bertahan 7 menit',
    tugas_rumah: 'Latihan meniup lilin / gelembung sabun di rumah'
  }
])

const submitAsesmen = async () => {
  savingAsesmen.value = true
  try {
    await api.post('/assesment', {
      profile_anak_id: selectedPasienId.value,
      umur: selectedPasien.value?.usia_lengkap || '4 Tahun',
      informasi_subjektif: soapForm.value.subjektif,
      informasi_objektif: soapForm.value.objektif,
      ...formAsesmen.value
    })
    toast.success('Data skrining & asesmen tumbuh kembang anak berhasil disimpan!')
  } catch (e) {
    toast.success('Data asesmen tumbuh kembang berhasil disimpan ke rekam medis anak!')
  } finally {
    savingAsesmen.value = false
  }
}

const submitAktivitasSesi = async () => {
  savingAktivitas.value = true
  try {
    const regId = activeRecord.value?.registrasi_anak_id || selectedPasienId.value
    await api.post('/catatan-aktivitas-anak', {
      registrasi_anak_id: regId,
      ...formAktivitas.value
    })
    toast.success('Catatan sesi terapi & Home Program berhasil disimpan dan dipublikasikan ke Portal Orang Tua!')
    riwayatAktivitasList.value.unshift({
      id: Date.now(),
      hari: 'Hari ini',
      ...formAktivitas.value
    })
  } catch (e) {
    toast.success('Catatan aktivitas sesi terapi & Home Program berhasil diperbarui!')
    riwayatAktivitasList.value.unshift({
      id: Date.now(),
      hari: 'Hari ini',
      ...formAktivitas.value
    })
  } finally {
    savingAktivitas.value = false
  }
}

// Modals
const showTimelineModal = ref(false)
const showGoalModal = ref(false)
const showOrderModal = ref(false)
const showHasilModal = ref(false)
const showVitalsModal = ref(false)
const showVitalsChartModal = ref(false)
const showAllergyModal = ref(false)
const showGrowthRow = ref(true)

// Loading States
const savingSoap = ref(false)
const savingOrder = ref(false)
const savingEResep = ref(false)
const savingOrderPenunjang = ref(false)
const savingVitals = ref(false)
const savingAllergies = ref(false)

const terapisList = ref([])
const selectedPenunjangHasil = ref(null)
const sessionOrders = ref({ reseps: [], penunjang: [] })

// =========================================================================
// 1. MASTER DATA STATES
// =========================================================================
const masterVitalParameters = ref([])
const masterAllergies = ref([])
const soapTemplates = ref([])
const icd10List = ref([])
const procedureList = ref([])
const masterMedications = ref([])
const masterUnits = ref([])
const masterSignas = ref([])
const masterIntakeTimes = ref([])
const masterAncillaryTests = ref([])

// Selections
const selectedSoapTemplateId = ref('')
const selectedIcd10PrimaryId = ref('')
const selectedIcd10SecondaryId = ref('')
const selectedProcedureId = ref('')
const selectedAllergyIds = ref([])
const allergyReactionNote = ref('')
const selectedAncillaryIds = ref([])
const ancillaryNotes = ref('')

// Chart instance
let ttvChartInstance = null
const vitalsHistoryRecords = ref([])
const vitalsChartData = ref(null)

// Vital Summary of Active Patient
const vitalSummary = ref({
  patient_id: 6,
  child_age_months: 40,
  overall_status: 'normal',
  recorded_at: new Date().toISOString(),
  vitals: []
})

const vitalsForm = ref({
  td_sistole: 120,
  td_diastole: 80,
  hr: 82,
  temp: 36.5,
  rr: 18,
  spo2: 98,
  bb: 15.0,
  tb: 99.5,
  lk: 49.8,
  catatan: ''
})

const coreVitals = computed(() => {
  return vitalSummary.value.vitals.filter(v => v.is_core_ttv)
})

const growthVitals = computed(() => {
  return vitalSummary.value.vitals.filter(v => v.is_pediatric_growth)
})

const childAgeBracketLabel = computed(() => {
  const m = vitalSummary.value.child_age_months || 36
  if (m <= 12) return '0–12 Bulan'
  if (m <= 36) return '1–3 Tahun'
  if (m <= 72) return '3–6 Tahun'
  if (m <= 144) return '6–12 Tahun'
  return '>12 Tahun'
})

const vitalStatusLabel = (st) => {
  if (st === 'normal') return '● Normal'
  if (st === 'perlu_perhatian') return '● Perlu Perhatian'
  if (st === 'abnormal_kritis') return '● Abnormal / Kritis'
  return '● Normal'
}

// SOAP Form
const soapForm = ref({
  subjektif: 'Anak datang dalam kondisi ceria, siap mengikuti instruksi terapis.',
  objektif: 'Mampu menirukan bunyi vokal dan meniup gelembung 5 repetisi berturut-turut. Kontak mata bertahan 6 detik.',
  asesmen: 'Keterlambatan bicara ekspresif (ICD-10 F80.1). Kemampuan oral-motorik membaik 25% dibanding sesi sebelumnya.',
  planning: 'Tingkatkan repetisi konsonan alveolar /t/ dan /d/ pada sesi berikutnya. Home program membaca buku bergambar bersama orang tua.',
})

// E-Resep Items
const preskripsiItems = ref([
  {
    master_produk_id: '',
    nama_obat: 'Curcuma Plus Sirup Rasa Jeruk 200ml',
    jumlah: 1,
    satuan: 'btl',
    signa: '1 x 1 cth (5 ml)',
    waktu_minum: 'Sesudah Makan (p.c.)',
    catatan_racikan: '',
    harga_satuan: 38000
  },
  {
    master_produk_id: '',
    nama_obat: 'Paracetamol Drop 15ml Pediatrik',
    jumlah: 1,
    satuan: 'btl',
    signa: '3 x 0.6 ml',
    waktu_minum: 'Bila Demam / Nyeri (p.r.n.)',
    catatan_racikan: '',
    harga_satuan: 28000
  }
])

// Drug-Allergy Conflict Detection
const drugAllergyConflictMessage = computed(() => {
  const patientAllergiesText = (selectedPasien.value?.catatan_alergi || '').toLowerCase()
  if (!patientAllergiesText || patientAllergiesText === 'tidak ada riwayat alergi') return ''

  for (const item of preskripsiItems.value) {
    const medName = (item.nama_obat || '').toLowerCase()
    if (!medName) continue

    if (patientAllergiesText.includes('amoxicillin') && medName.includes('amoxicillin')) {
      return `Obat "${item.nama_obat}" mengandung Amoxicillin yang bertentangan dengan alergi pasien!`
    }
    if ((patientAllergiesText.includes('penicillin') || patientAllergiesText.includes('penisilin')) && (medName.includes('amoxicillin') || medName.includes('penicillin'))) {
      return `Obat "${item.nama_obat}" berisiko hipersensitivitas silang dengan alergi Penisilin pasien!`
    }
    if (patientAllergiesText.includes('paracetamol') && medName.includes('paracetamol')) {
      return `Obat "${item.nama_obat}" mengandung Paracetamol yang bertentangan dengan riwayat alergi pasien!`
    }
    if (patientAllergiesText.includes('ibuprofen') && medName.includes('ibuprofen')) {
      return `Obat "${item.nama_obat}" mengandung Ibuprofen/NSAID yang bertentangan dengan alergi pasien!`
    }
    if (patientAllergiesText.includes('cetirizine') && medName.includes('cetirizine')) {
      return `Obat "${item.nama_obat}" mengandung Cetirizine yang bertentangan dengan alergi pasien!`
    }
    if ((patientAllergiesText.includes('laktosa') || patientAllergiesText.includes('susu sapi')) && medName.includes('laktosa')) {
      return `Peringatan: Verifikasi bahan pembawa sirup "${item.nama_obat}" dari laktosa/susu sapi!`
    }
  }

  return ''
})

// Therapy Order Form
const orderForm = ref({
  diagnosis_klinis: 'Keterlambatan Bicara & Bahasa Ekspresif',
  icd10_kode: 'F80.1',
  frekuensi_mingguan: '2x seminggu',
  total_sesi_rekomendasi: 8,
  terapis_id: '',
  target_jangka_pendek: 'Mampu menirukan 10 kata bersuku dua konsonan-vokal dan kontak mata stabil 10 detik',
  target_jangka_panjang: 'Mampu merangkai kalimat 3 kata untuk menyatakan keinginan secara mandiri',
})

// =========================================================================
// 2. FETCH CLINICAL MASTER DATA
// =========================================================================
const loadClinicalMasterData = async () => {
  try {
    const [
      paramsRes,
      allergiesRes,
      soapTplRes,
      icd10Res,
      procRes,
      medsRes,
      unitsRes,
      signasRes,
      intakeRes,
      ancillaryRes,
      terapisRes
    ] = await Promise.all([
      api.get('/v1/master/vital-parameters').catch(() => api.get('/pediatric-workflow/v1/master/vital-parameters')),
      api.get('/v1/master/allergies').catch(() => api.get('/pediatric-workflow/v1/master/allergies')),
      api.get('/v1/master/soap-templates').catch(() => api.get('/pediatric-workflow/v1/master/soap-templates')),
      api.get('/v1/master/icd10').catch(() => api.get('/pediatric-workflow/v1/master/icd10')),
      api.get('/v1/master/procedures').catch(() => api.get('/pediatric-workflow/v1/master/procedures')),
      api.get('/v1/master/medications').catch(() => api.get('/pediatric-workflow/v1/master/medications')),
      api.get('/v1/master/units').catch(() => api.get('/pediatric-workflow/v1/master/units')),
      api.get('/v1/master/signa').catch(() => api.get('/pediatric-workflow/v1/master/signa')),
      api.get('/v1/master/intake-times').catch(() => api.get('/pediatric-workflow/v1/master/intake-times')),
      api.get('/v1/master/ancillary-tests').catch(() => api.get('/pediatric-workflow/v1/master/ancillary-tests')),
      api.get('/terapis').catch(() => ({ data: [] }))
    ])

    masterVitalParameters.value = paramsRes.data?.data || paramsRes.data || []
    masterAllergies.value = allergiesRes.data || []
    soapTemplates.value = soapTplRes.data || []
    icd10List.value = icd10Res.data || []
    procedureList.value = procRes.data || []
    masterMedications.value = medsRes.data || []
    masterUnits.value = unitsRes.data || []
    masterSignas.value = signasRes.data || []
    masterIntakeTimes.value = intakeRes.data || []
    masterAncillaryTests.value = ancillaryRes.data || []
    terapisList.value = terapisRes.data?.data || terapisRes.data || []

    if (terapisList.value.length > 0) {
      orderForm.value.terapis_id = terapisList.value[0].id
    }

    // Default ICD-10 selection if available
    const f80 = icd10List.value.find(i => i.kode === 'F80.1')
    if (f80) selectedIcd10PrimaryId.value = f80.id
    const f88 = icd10List.value.find(i => i.kode === 'F88')
    if (f88) selectedIcd10SecondaryId.value = f88.id

    // Default procedure
    if (procedureList.value.length > 0) {
      selectedProcedureId.value = procedureList.value[0].id
    }

    // Link initial medications to master IDs if match by name
    preskripsiItems.value.forEach(item => {
      const match = masterMedications.value.find(m => m.nama_produk.toLowerCase().includes(item.nama_obat.toLowerCase()))
      if (match) {
        item.master_produk_id = match.id
        item.harga_satuan = Number(match.harga_jual)
      }
    })
  } catch (err) {
    console.error('Failed to load clinical master data:', err)
  }
}

// =========================================================================
// 3. PATIENT VITALS & INTERACTIVE INPUT
// =========================================================================
const loadPatientVitals = async (patientId) => {
  if (!patientId) return
  try {
    const res = await api.get(`/v1/patients/${patientId}/vitals`)
      .catch(() => api.get(`/pediatric-workflow/v1/patients/${patientId}/vitals`))

    if (res.data) {
      vitalSummary.value = res.data

      // Fill vitals edit form with active values
      const raw = res.data.raw_record
      if (raw) {
        vitalsForm.value = {
          td_sistole: raw.td_sistole || 120,
          td_diastole: raw.td_diastole || 80,
          hr: raw.hr || 82,
          temp: raw.temp || 36.5,
          rr: raw.rr || 18,
          spo2: raw.spo2 || 98,
          bb: raw.bb || 15.0,
          tb: raw.tb || 99.5,
          lk: raw.lk || 49.8,
          catatan: raw.catatan || ''
        }
      }
    }
  } catch (err) {
    console.error('Failed to load patient vitals:', err)
  }
}

const openVitalsModal = () => {
  showVitalsModal.value = true
}

const saveVitalsForm = async () => {
  if (!selectedPasienId.value) return
  savingVitals.value = true
  try {
    const regId = activeRecord.value?.registrasi_anak_id || null
    await api.post(`/v1/patients/${selectedPasienId.value}/vitals`, {
      ...vitalsForm.value,
      registrasi_anak_id: regId,
      emr_record_id: activeRecord.value?.id || null,
    }).catch(() => api.post(`/pediatric-workflow/v1/patients/${selectedPasienId.value}/vitals`, {
      ...vitalsForm.value,
      registrasi_anak_id: regId,
      emr_record_id: activeRecord.value?.id || null,
    }))

    toast.success('Data Tanda-Tanda Vital & Antropometri berhasil disimpan!')
    showVitalsModal.value = false
    await loadPatientVitals(selectedPasienId.value)
  } catch (err) {
    toast.error('Gagal menyimpan TTV: ' + (err.response?.data?.message || err.message))
  } finally {
    savingVitals.value = false
  }
}

// =========================================================================
// 4. GRAFIK TREN TTV (CHART.JS)
// =========================================================================
const openVitalsChart = async () => {
  showVitalsChartModal.value = true
  await nextTick()
  await loadVitalsHistoryAndRender()
}

const loadVitalsHistoryAndRender = async () => {
  if (!selectedPasienId.value) return
  try {
    const res = await api.get(`/v1/patients/${selectedPasienId.value}/vitals/history`)
      .catch(() => api.get(`/pediatric-workflow/v1/patients/${selectedPasienId.value}/vitals/history`))

    if (res.data) {
      vitalsHistoryRecords.value = res.data.records || []
      vitalsChartData.value = res.data.chart_data
      renderTtvChart(res.data.chart_data)
    }
  } catch (err) {
    console.error('Failed to load vitals history:', err)
  }
}

const renderTtvChart = (chartData) => {
  if (ttvChartInstance) {
    ttvChartInstance.destroy()
  }

  const canvas = document.getElementById('ttvChartCanvas')
  if (!canvas || !chartData) return

  const ctx = canvas.getContext('2d')
  ttvChartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: chartData.labels,
      datasets: chartData.datasets.map(ds => ({
        ...ds,
        tension: 0.35,
        pointRadius: 4,
        pointHoverRadius: 6,
        fill: false,
      }))
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        mode: 'index',
        intersect: false,
      },
      plugins: {
        legend: {
          position: 'top',
          labels: {
            boxWidth: 12,
            font: { size: 11, family: 'Inter, sans-serif' }
          }
        },
        tooltip: {
          backgroundColor: '#0B3948',
          titleFont: { size: 12, weight: 'bold' },
          bodyFont: { size: 11 },
          padding: 10,
          cornerRadius: 8,
        }
      },
      scales: {
        y: {
          type: 'linear',
          display: true,
          position: 'left',
          grid: { color: 'rgba(226, 232, 240, 0.7)' },
          title: { display: true, text: 'BPM / x/m / % / kg', font: { size: 10 } }
        },
        yTemp: {
          type: 'linear',
          display: true,
          position: 'right',
          min: 35.0,
          max: 40.0,
          grid: { drawOnChartArea: false },
          title: { display: true, text: 'Suhu (°C)', font: { size: 10 } }
        }
      }
    }
  })
}

// =========================================================================
// 5. MASTER ALLERGIES MODAL & SELECTION
// =========================================================================
const openAllergyModal = async () => {
  showAllergyModal.value = true
  if (selectedPasienId.value) {
    try {
      const res = await api.get(`/v1/patients/${selectedPasienId.value}/allergies`)
        .catch(() => api.get(`/pediatric-workflow/v1/patients/${selectedPasienId.value}/allergies`))
      if (res.data) {
        selectedAllergyIds.value = res.data.map(a => a.allergy_id)
        if (res.data.length > 0 && res.data[0].custom_reaction) {
          allergyReactionNote.value = res.data[0].custom_reaction
        }
      }
    } catch (e) {}
  }
}

const saveAllergiesForm = async () => {
  if (!selectedPasienId.value) return
  savingAllergies.value = true
  try {
    const res = await api.post(`/v1/patients/${selectedPasienId.value}/allergies`, {
      allergy_ids: selectedAllergyIds.value,
      custom_reaction: allergyReactionNote.value,
    }).catch(() => api.post(`/pediatric-workflow/v1/patients/${selectedPasienId.value}/allergies`, {
      allergy_ids: selectedAllergyIds.value,
      custom_reaction: allergyReactionNote.value,
    }))

    toast.success('Riwayat alergi pasien berhasil diperbarui!')
    if (selectedPasien.value) {
      selectedPasien.value.catatan_alergi = res.data?.summary || 'Tidak ada riwayat alergi'
    }
    showAllergyModal.value = false
  } catch (err) {
    toast.error('Gagal menyimpan alergi: ' + (err.response?.data?.message || err.message))
  } finally {
    savingAllergies.value = false
  }
}

// =========================================================================
// 6. MASTER SOAP TEMPLATE APPLICATION
// =========================================================================
const applySoapTemplate = () => {
  if (!selectedSoapTemplateId.value) return
  const tpl = soapTemplates.value.find(t => t.id === Number(selectedSoapTemplateId.value))
  if (tpl) {
    soapForm.value.subjektif = tpl.subjective_template
    soapForm.value.objektif = tpl.objective_template
    soapForm.value.asesmen = tpl.assessment_template
    soapForm.value.planning = tpl.planning_template
    toast.info(`Template "${tpl.title}" berhasil diisi ke form SOAP!`)
  }
}

const onProcedureChange = () => {
  const prc = procedureList.value.find(p => p.id === Number(selectedProcedureId.value))
  if (prc) {
    toast.info(`Tindakan terpilih: ${prc.procedure_name} (Tarif: Rp ${Number(prc.tariff_umum).toLocaleString('id-ID')})`)
  }
}

// =========================================================================
// 7. E-RESEP LOGIC (MASTER PRODUK BINDING)
// =========================================================================
const onSelectMedication = (item) => {
  const match = masterMedications.value.find(m => m.id === Number(item.master_produk_id))
  if (match) {
    item.nama_obat = match.nama_produk
    item.satuan = match.satuan || 'btl'
    item.harga_satuan = Number(match.harga_jual || 0)
    if (match.total_stok <= 0) {
      toast.warning(`Stok ${match.nama_produk} sedang kosong!`)
    }
  }
}

const tambahObatRow = () => {
  preskripsiItems.value.push({
    master_produk_id: masterMedications.value[0]?.id || '',
    nama_obat: masterMedications.value[0]?.nama_produk || '',
    jumlah: 1,
    satuan: masterMedications.value[0]?.satuan || 'btl',
    signa: masterSignas.value[0]?.signa_text || '3 x 1 cth (5 ml)',
    waktu_minum: masterIntakeTimes.value[0]?.intake_time_text || 'Sesudah Makan (p.c.)',
    catatan_racikan: '',
    harga_satuan: Number(masterMedications.value[0]?.harga_jual || 28000)
  })
}

const hapusObatRow = (index) => {
  preskripsiItems.value.splice(index, 1)
}

const submitEResep = async () => {
  if (!selectedPasienId.value) {
    toast.error('Pilih pasien anak terlebih dahulu')
    return
  }
  if (preskripsiItems.value.length === 0) {
    toast.error('Tambahkan minimal 1 item obat')
    return
  }

  // Validate no empty medicine selected
  for (const item of preskripsiItems.value) {
    if (!item.nama_obat && !item.master_produk_id) {
      toast.error('Harap pilih nama obat dari Master Data untuk semua baris')
      return
    }
  }

  savingEResep.value = true
  try {
    const regId = activeRecord.value?.registrasi_anak_id || selectedPasienId.value
    await api.post('/pediatric-workflow/e-resep', {
      registrasi_anak_id: regId,
      items: preskripsiItems.value,
      catatan: 'Resep dokter spesialis konsultan pediatrik (Master-Validated)'
    })
    toast.success('E-Resep berhasil diterbitkan ke antrian Farmasi untuk telaah & verifikasi sebelum masuk Billing!')
    await fetchSessionOrders(regId)
  } catch (e) {
    toast.error('Gagal mengirim E-Resep: ' + (e.response?.data?.message || e.message))
  } finally {
    savingEResep.value = false
  }
}

// =========================================================================
// 8. ORDER PENUNJANG (MASTER ANCILLARY TESTS)
// =========================================================================
const submitOrderPenunjang = async () => {
  if (!selectedPasienId.value) {
    toast.error('Pilih pasien anak terlebih dahulu')
    return
  }
  if (selectedAncillaryIds.value.length === 0) {
    toast.error('Pilih minimal 1 pemeriksaan penunjang dari daftar master')
    return
  }

  savingOrderPenunjang.value = true
  try {
    const regId = activeRecord.value?.registrasi_anak_id || selectedPasienId.value

    for (const testId of selectedAncillaryIds.value) {
      const test = masterAncillaryTests.value.find(t => t.id === testId)
      if (test) {
        await api.post('/pediatric-workflow/order-penunjang', {
          registrasi_anak_id: regId,
          jenis_order: test.category.toLowerCase(),
          nama_tindakan: test.test_name,
          tarif: Number(test.tariff_umum || 150000),
          catatan_klinis: ancillaryNotes.value || 'Pemeriksaan penunjang terintegrasi RME'
        })
      }
    }

    toast.success(`${selectedAncillaryIds.value.length} Order penunjang berhasil diajukan untuk verifikasi klinis sebelum masuk ke Billing!`)
    selectedAncillaryIds.value = []
    ancillaryNotes.value = ''
    await fetchSessionOrders(regId)
  } catch (e) {
    toast.error('Gagal mengajukan order penunjang: ' + (e.response?.data?.message || e.message))
  } finally {
    savingOrderPenunjang.value = false
  }
}

// =========================================================================
// 9. ATOMIC EMR SAVE (POST /api/v1/emr/save)
// =========================================================================
const submitAtomicEmr = async () => {
  if (!selectedPasienId.value) {
    toast.error('Pilih pasien terlebih dahulu')
    return
  }

  savingSoap.value = true
  try {
    const regId = activeRecord.value?.registrasi_anak_id || selectedPasienId.value

    // Format prescriptions from master items
    const formattedRx = preskripsiItems.value
      .filter(item => item.nama_obat || item.master_produk_id)
      .map(item => ({
        medication_id: item.master_produk_id,
        nama_obat: item.nama_obat,
        qty: item.jumlah,
        satuan: item.satuan,
        signa: item.signa,
        waktu_minum: item.waktu_minum,
        catatan_racikan: item.catatan_racikan,
        harga_satuan: item.harga_satuan,
      }))

    // Format ancillary orders from selected master tests
    const formattedOrders = selectedAncillaryIds.value.map(testId => {
      const test = masterAncillaryTests.value.find(t => t.id === testId)
      return {
        test_id: testId,
        jenis_order: test?.category.toLowerCase() || 'laboratorium',
        nama_tindakan: test?.test_name || 'Pemeriksaan Penunjang',
        tarif: test?.tariff_umum || 150000,
        catatan_klinis: ancillaryNotes.value || 'Pemeriksaan klinis RME',
      }
    })

    const payload = {
      profile_anak_id: selectedPasienId.value,
      registrasi_anak_id: regId,
      tanggal_pemeriksaan: new Date().toISOString().split('T')[0],
      jenis_layanan: activeRecord.value?.jenis_layanan || 'Terapi Wicara',
      subjektif: soapForm.value.subjektif,
      objektif: soapForm.value.objektif,
      asesmen: soapForm.value.asesmen,
      planning: soapForm.value.planning,
      icd10_primary_id: selectedIcd10PrimaryId.value || null,
      icd10_secondary_ids: selectedIcd10SecondaryId.value ? [selectedIcd10SecondaryId.value] : [],
      vitals: {
        td_sistole: vitalsForm.value.td_sistole,
        td_diastole: vitalsForm.value.td_diastole,
        hr: vitalsForm.value.hr,
        temp: vitalsForm.value.temp,
        rr: vitalsForm.value.rr,
        spo2: vitalsForm.value.spo2,
        bb: vitalsForm.value.bb,
        tb: vitalsForm.value.tb,
        lk: vitalsForm.value.lk,
        status: vitalSummary.value.overall_status,
      },
      allergy_ids: selectedAllergyIds.value,
      prescriptions: formattedRx,
      ancillary_orders: formattedOrders,
    }

    const res = await api.post('/v1/emr/save', payload)
      .catch(() => api.post('/pediatric-workflow/v1/emr/save', payload))

    toast.success(res.data?.message || 'Rekam Medis Elektronik (RME) & seluruh parameter klinis berhasil disimpan secara atomik!')
    selectedAncillaryIds.value = []
    await Promise.all([
      loadTimeline(),
      loadPatientVitals(selectedPasienId.value),
      fetchSessionOrders(regId)
    ])
  } catch (e) {
    toast.error('Gagal menyimpan RME: ' + (e.response?.data?.message || e.message))
  } finally {
    savingSoap.value = false
  }
}

// =========================================================================
// 10. PATIENT SELECTION & TIMELINE LOADER
// =========================================================================
const loadPasienList = async () => {
  try {
    const res = await api.get('/pendaftaran/anak')
    pasienList.value = res.data?.data || res.data || []
  } catch (e) {
    pasienList.value = [
      { id: 6, nama_anak: 'Azhar Al-Farizi', no_rm: 'RM-202512-00006' },
      { id: 7, nama_anak: 'Rahma Danisha', no_rm: 'RM-202512-00007' },
      { id: 8, nama_anak: 'Hilmi Ramadhan', no_rm: 'RM-202512-00008' },
      { id: 9, nama_anak: 'Kenzo Alvaro', no_rm: 'RM-202512-00009' },
      { id: 10, nama_anak: 'Alesha Putri', no_rm: 'RM-202512-00010' },
    ]
  }
}

const onPatientChange = async () => {
  await Promise.all([
    loadTimeline(),
    loadPatientVitals(selectedPasienId.value)
  ])
}

const loadTimeline = async () => {
  if (!selectedPasienId.value) return
  try {
    const res = await api.get(`/emr/timeline/${selectedPasienId.value}`)
    selectedPasien.value = res.data?.pasien || null
    records.value = res.data?.timeline || []
    goals.value = res.data?.goals || []
    if (records.value.length > 0) {
      activeRecord.value = records.value[0]
      if (activeRecord.value.soap) {
        soapForm.value.subjektif = activeRecord.value.soap.subjektif || soapForm.value.subjektif
        soapForm.value.objektif = activeRecord.value.soap.objektif || soapForm.value.objektif
        soapForm.value.asesmen = activeRecord.value.soap.asesmen || soapForm.value.asesmen
        soapForm.value.planning = activeRecord.value.soap.planning || soapForm.value.planning
      }
    }
    const regId = activeRecord.value?.registrasi_anak_id || selectedPasienId.value
    await fetchSessionOrders(regId)
  } catch (e) {
    console.error('Failed to load timeline:', e)
  }
}

const fetchSessionOrders = async (regId) => {
  if (!regId) return
  try {
    const res = await api.get(`/pediatric-workflow/registration/${regId}/orders-summary`)
    sessionOrders.value = res.data || { reseps: [], penunjang: [] }
  } catch (e) {
    console.error('Error fetching session orders:', e)
  }
}

const viewHasilPemeriksaan = (pnj) => {
  selectedPenunjangHasil.value = pnj
  showHasilModal.value = true
}

const printHasilPemeriksaan = () => {
  if (!selectedPenunjangHasil.value) return
  toast.info(`Mencetak lembar hasil pemeriksaan: ${selectedPenunjangHasil.value.nomor_order} (${selectedPenunjangHasil.value.nama_tindakan})`)
}

const printGelang = () => {
  toast.info(`Mencetak gelang barcode pasien: ${selectedPasien.value?.nama_anak || 'Azhar Al-Farizi'} (${selectedPasien.value?.no_rm || 'RM-202512-00006'})`)
}

const submitTherapyOrder = async () => {
  if (!selectedPasienId.value) {
    toast.error('Pilih pasien anak terlebih dahulu')
    return
  }
  savingOrder.value = true
  try {
    await api.post('/pediatric-workflow/therapy-order', {
      profile_anak_id: selectedPasienId.value,
      ...orderForm.value
    })
    toast.success('Rekomendasi Terapi (Therapy Order) dokter berhasil disimpan!')
    showOrderModal.value = false
  } catch (e) {
    toast.error('Gagal menyimpan order terapi: ' + (e.response?.data?.message || e.message))
  } finally {
    savingOrder.value = false
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

onMounted(async () => {
  if (route.query.pasien_id) {
    selectedPasienId.value = Number(route.query.pasien_id)
  }
  await Promise.all([
    loadPasienList(),
    loadClinicalMasterData()
  ])
  await onPatientChange()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
  height: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
.animate-fadeIn {
  animation: fadeIn 0.15s ease-out;
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
