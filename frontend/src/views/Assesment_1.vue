<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6">
    <div class="max-w-5xl mx-auto">
      <div class="bg-white rounded-2xl shadow-xl p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">
          Assesment Anak – Tahap 1
        </h1>

        <form @submit.prevent="submitForm" class="space-y-8">
          <!-- Informasi Umum -->
          <section>
            <h2 class="section-title">Informasi Umum</h2>
            <div class="grid md:grid-cols-2 gap-4">
              <input-field label="Umur Anak" v-model="form.umur" />
            </div>
            <textarea-field
              label="Informasi Subjektif"
              v-model="form.informasi_subjektif"
            />
            <textarea-field
              label="Informasi Objektif"
              v-model="form.informasi_objektif"
            />
          </section>

          <!-- Kehamilan & Kelahiran -->
          <section>
            <h2 class="section-title">Kehamilan & Kelahiran</h2>
            <div class="grid md:grid-cols-2 gap-4">
              <input-field label="Gangguan Kehamilan" v-model="form.gangguan_kehamilan" />
              <input-field label="Proses Kelahiran" v-model="form.proses_kelahiran" />
              <input-field label="Usia Kehamilan Saat Lahir" v-model="form.usia_kehamilan_lahir" />
              <input-field label="Gangguan Saat Melahirkan" v-model="form.gangguan_melahirkan" />
            </div>
          </section>

          <!-- Riwayat & Perilaku -->
          <section>
            <h2 class="section-title">Riwayat & Perilaku Anak</h2>
            <toggle-field label="Riwayat Kejang" v-model="form.riwayat_kejang" />
            <toggle-field label="Konsumsi Obat Epilepsi" v-model="form.konsumsi_obat_epilepsi" />
            <toggle-field label="Perkembangan Sesuai Usia" v-model="form.perkembangan_sesuai_usia" />
            <toggle-field label="Disusui Oleh Ibu" v-model="form.disusui_ibu" />
            <toggle-field label="TV / Gadget Addict" v-model="form.tv_gadget_addict" />
            <toggle-field label="Sering Memutar Benda" v-model="form.sering_memutar_benda" />
            <toggle-field label="Main Mobil-mobilan Berulang" v-model="form.main_mobil_berulang" />
            <toggle-field label="Flapping" v-model="form.flapping" />
            <toggle-field label="Tantrum" v-model="form.tantrum" />
            <toggle-field label="Kontak Mata dengan Orang Lain" v-model="form.kontak_mata" />
            <toggle-field label="Gangguan Makan & Menelan" v-model="form.gangguan_makan_menelan" />
            <toggle-field label="Komunikasi Dua Arah" v-model="form.komunikasi_dua_arah" />
          </section>

          <div class="flex justify-end gap-4">
            <button
              type="reset"
              class="px-6 py-2 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100"
            >
              Reset
            </button>
            <button
              type="submit"
              class="px-6 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow"
            >
              Simpan Assesment
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
  await axios.post('/assesment-1', form)
  alert('Assesment berhasil disimpan')
}
</script>

<script>
export default {
  components: {
    InputField: {
      props: ['label', 'modelValue'],
      emits: ['update:modelValue'],
      template: `
        <div class="flex flex-col gap-1">
          <label class="text-sm text-gray-600">{{ label }}</label>
          <input
            type="text"
            class="input"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
          />
        </div>`
    },
    TextareaField: {
      props: ['label', 'modelValue'],
      emits: ['update:modelValue'],
      template: `
        <div class="flex flex-col gap-1 mt-4">
          <label class="text-sm text-gray-600">{{ label }}</label>
          <textarea
            rows="3"
            class="input"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
          ></textarea>
        </div>`
    },
    ToggleField: {
      props: ['label', 'modelValue'],
      emits: ['update:modelValue'],
      template: `
        <div class="flex items-center justify-between py-2 border-b">
          <span class="text-gray-700">{{ label }}</span>
          <button
            type="button"
            @click="$emit('update:modelValue', !modelValue)"
            :class="modelValue ? 'bg-green-500' : 'bg-gray-300'"
            class="w-12 h-6 rounded-full relative transition"
          >
            <span
              :class="modelValue ? 'translate-x-6' : 'translate-x-1'"
              class="absolute top-1 w-4 h-4 bg-white rounded-full transition"
            ></span>
          </button>
        </div>`
    }
  }
}
</script>

<style scoped>
.section-title {
  @apply text-lg font-semibold text-gray-800 mb-4;
}
.input {
  @apply px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400;
}
</style>
