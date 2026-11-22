<template>
  <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-y-auto custom-scrollbar w-full pr-1">
    <div class="max-w-4xl mx-auto w-full">
      <div class="bg-white rounded-xl shadow-[0_1px_3px_rgba(15,23,42,0.04)] border border-slate-200 p-5">
        <!-- HEADER -->
        <div class="border-b border-slate-200 pb-3 mb-5">
          <h1 class="text-base font-bold text-[#0F172A] tracking-tight flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-[#E6F2F5] border border-[#CBE3E9] flex items-center justify-center text-[#0F766E] font-bold text-xs shrink-0">
              <i class="pi pi-file-edit"></i>
            </div>
            <span>Assesment Anak – Skrining Klinis Tahap 1</span>
          </h1>
          <p class="text-xs text-slate-500 mt-1">Pemeriksaan klinis awal, anamnesis kehamilan/kelahiran, dan skrining perkembangan anak</p>
        </div>

        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- 1. Informasi Umum -->
          <section class="space-y-3">
            <h2 class="section-title">1. Informasi Umum Pasien</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <input-field label="Umur Anak (Tahun / Bulan)" v-model="form.umur" placeholder="Contoh: 4 Tahun 2 Bulan" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <textarea-field
                label="Informasi Subjektif (Keluhan Utama Orang Tua)"
                v-model="form.informasi_subjektif"
                placeholder="Deskripsi keluhan dari orang tua saat datang..."
              />
              <textarea-field
                label="Informasi Objektif (Hasil Pengamatan Awal)"
                v-model="form.informasi_objektif"
                placeholder="Observasi fisik, kontak mata, dan respon spontan anak..."
              />
            </div>
          </section>

          <!-- 2. Kehamilan & Kelahiran -->
          <section class="space-y-3 pt-3 border-t border-slate-100">
            <h2 class="section-title">2. Riwayat Kehamilan & Kelahiran</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <input-field label="Gangguan Selama Kehamilan" v-model="form.gangguan_kehamilan" placeholder="Contoh: Hipertensi, pendarahan, dll" />
              <input-field label="Proses Kelahiran" v-model="form.proses_kelahiran" placeholder="Contoh: Normal, Caesar, Vakum" />
              <input-field label="Usia Kehamilan Saat Lahir" v-model="form.usia_kehamilan_lahir" placeholder="Contoh: 38 Minggu (Cukup Bulan)" />
              <input-field label="Gangguan Saat Melahirkan" v-model="form.gangguan_melahirkan" placeholder="Contoh: Asfiksia, kuning, dll" />
            </div>
          </section>

          <!-- 3. Riwayat & Perilaku -->
          <section class="space-y-3 pt-3 border-t border-slate-100">
            <h2 class="section-title">3. Skrining Perilaku & Tumbuh Kembang</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1 bg-[#F8FAFC] p-3.5 rounded-xl border border-slate-200">
              <toggle-field label="Riwayat Kejang / Demam Tinggi" v-model="form.riwayat_kejang" />
              <toggle-field label="Konsumsi Obat Epilepsi Rutin" v-model="form.konsumsi_obat_epilepsi" />
              <toggle-field label="Perkembangan Sesuai Usia Milestones" v-model="form.perkembangan_sesuai_usia" />
              <toggle-field label="Diberikan ASI Eksklusif Oleh Ibu" v-model="form.disusui_ibu" />
              <toggle-field label="Kecanduan Gadget / TV (Screen Addict)" v-model="form.tv_gadget_addict" />
              <toggle-field label="Sering Memutar Benda / Roda" v-model="form.sering_memutar_benda" />
              <toggle-field label="Main Mobil-mobilan Berulang / Sejajar" v-model="form.main_mobil_berulang" />
              <toggle-field label="Flapping Tangan Saat Gembira / Cemas" v-model="form.flapping" />
              <toggle-field label="Tantrum / Ledakan Emosi Sulit Ditenangkan" v-model="form.tantrum" />
              <toggle-field label="Kontak Mata Baik dengan Orang Lain" v-model="form.kontak_mata" />
              <toggle-field label="Gangguan Pola Makan & Menelan (Sensori Oral)" v-model="form.gangguan_makan_menelan" />
              <toggle-field label="Mampu Komunikasi Dua Arah" v-model="form.komunikasi_dua_arah" />
            </div>
          </section>

          <!-- ACTIONS -->
          <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200">
            <button
              type="reset"
              class="h-[34px] px-4 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-medium transition"
            >
              Reset Form
            </button>
            <button
              type="submit"
              class="h-[34px] px-4 rounded-lg bg-[#0F766E] text-white hover:bg-[#115E59] active:scale-95 text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5"
            >
              <i class="pi pi-check text-xs"></i>
              <span>Simpan Data Assesment</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import axios from '@/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const form = reactive({
  registrasi_anak_id: null,
  umur: '',
  informasi_subjektif: '',
  informasi_objektif: '',
  gangguan_kehamilan: '',
  proses_kelahiran: '',
  usia_kehamilan_lahir: '',
  gangguan_melahirkan: '',
  riwayat_kejang: false,
  konsumsi_obat_epilepsi: false,
  perkembangan_sesuai_usia: false,
  disusui_ibu: false,
  tv_gadget_addict: false,
  sering_memutar_benda: false,
  main_mobil_berulang: false,
  flapping: false,
  tantrum: false,
  kontak_mata: false,
  gangguan_makan_menelan: false,
  komunikasi_dua_arah: false,
})

const submitForm = async () => {
  try {
    await axios.post('/assesment-1', form)
    toast.success('Assesment berhasil disimpan')
  } catch (err) {
    toast.error('Gagal menyimpan assesment')
  }
}
</script>

<script>
export default {
  components: {
    InputField: {
      props: ['label', 'modelValue', 'placeholder'],
      emits: ['update:modelValue'],
      template: `
        <div class="flex flex-col gap-1">
          <label class="text-[12px] font-medium text-slate-700">{{ label }}</label>
          <input
            type="text"
            class="h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15"
            :placeholder="placeholder"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
          />
        </div>`
    },
    TextareaField: {
      props: ['label', 'modelValue', 'placeholder'],
      emits: ['update:modelValue'],
      template: `
        <div class="flex flex-col gap-1">
          <label class="text-[12px] font-medium text-slate-700">{{ label }}</label>
          <textarea
            rows="3"
            class="p-2.5 text-[12.5px] bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 custom-scrollbar"
            :placeholder="placeholder"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
          ></textarea>
        </div>`
    },
    ToggleField: {
      props: ['label', 'modelValue'],
      emits: ['update:modelValue'],
      template: `
        <div class="flex items-center justify-between py-1.5 border-b border-slate-200/70">
          <span class="text-[12px] text-slate-700">{{ label }}</span>
          <button
            type="button"
            @click="$emit('update:modelValue', !modelValue)"
            :class="modelValue ? 'bg-[#0F766E]' : 'bg-slate-300'"
            class="w-9 h-5 rounded-full relative transition shrink-0 cursor-pointer"
          >
            <span
              :class="modelValue ? 'translate-x-4' : 'translate-x-0.5'"
              class="inline-block top-0.5 w-4 h-4 bg-white rounded-full transition shadow-sm"
            ></span>
          </button>
        </div>`
    }
  }
}
</script>

<style scoped>
.section-title {
  @apply text-xs font-bold text-[#0F766E] uppercase tracking-wider;
}
</style>
