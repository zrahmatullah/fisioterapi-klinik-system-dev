<template>
  <div class="bg-[#E6F2F5] border border-[#CBE3E9] rounded-xl px-3.5 py-2.5 flex flex-wrap items-center justify-between gap-3 w-full shadow-[0_1px_3px_rgba(15,23,42,0.04)] shrink-0">
    <!-- LEFT GROUP: AVATAR & METADATA -->
    <div class="flex items-center gap-3 flex-wrap min-w-0">
      <!-- Square-rounded avatar -->
      <div v-if="fotoUrl" class="w-9 h-9 rounded-lg overflow-hidden shrink-0 border border-teal-200">
        <img :src="fotoUrl" :alt="nama" class="w-full h-full object-cover" />
      </div>
      <div v-else class="w-9 h-9 rounded-lg bg-[#0F766E] text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
        {{ (nama && nama.charAt(0)) || 'P' }}
      </div>

      <!-- METADATA COLUMNS -->
      <div class="flex items-center gap-4 flex-wrap">
        <div>
          <span class="text-[10px] text-[#64748B] block leading-tight">Nama Pasien</span>
          <span class="text-[13px] font-bold text-[#0F172A] leading-normal">{{ nama || '-' }}</span>
        </div>

        <div class="border-l border-[#CBE3E9] pl-3">
          <span class="text-[10px] text-[#64748B] block leading-tight">No. RM</span>
          <span class="text-[12px] font-semibold text-[#0F172A] tabular-nums leading-normal">{{ noRm || '-' }}</span>
        </div>

        <div class="border-l border-[#CBE3E9] pl-3">
          <span class="text-[10px] text-[#64748B] block leading-tight">Umur/JK</span>
          <span class="text-[12px] text-[#334155] leading-normal">{{ umur || '-' }} / {{ jk || '-' }}</span>
        </div>

        <div class="border-l border-[#CBE3E9] pl-3">
          <span class="text-[10px] text-[#64748B] block leading-tight">Jaminan</span>
          <span class="text-[12px] font-medium text-[#334155] leading-normal">{{ jaminan || 'Umum' }}</span>
        </div>

        <div class="border-l border-[#CBE3E9] pl-3">
          <span class="text-[10px] text-[#64748B] block leading-tight">Alergi</span>
          <span class="text-[12px] font-semibold text-rose-600 leading-normal">{{ alergi || 'Tidak ada' }}</span>
        </div>
      </div>
    </div>

    <!-- RIGHT ACTION GROUP -->
    <div class="flex items-center gap-2 shrink-0">
      <slot name="actions">
        <button
          type="button"
          @click="$emit('print-wristband')"
          class="h-[30px] bg-white border border-slate-200 text-slate-700 text-[11.5px] font-medium px-2.5 rounded-lg hover:bg-slate-50 active:scale-95 flex items-center gap-1.5 transition shadow-2xs"
        >
          <i class="pi pi-print text-[10px] text-slate-500"></i>
          <span>Cetak Gelang</span>
        </button>

        <button
          type="button"
          @click="$emit('view-history')"
          class="h-[30px] bg-white border border-slate-200 text-slate-700 text-[11.5px] font-medium px-2.5 rounded-lg hover:bg-slate-50 active:scale-95 flex items-center gap-1.5 transition shadow-2xs"
        >
          <i class="pi pi-history text-[10px] text-slate-500"></i>
          <span>Riwayat Kunjungan</span>
        </button>
      </slot>
    </div>
  </div>
</template>

<script setup>
defineProps({
  nama: { type: String, default: '' },
  noRm: { type: String, default: '' },
  umur: { type: String, default: '' },
  jk: { type: String, default: '' },
  jaminan: { type: String, default: 'Umum' },
  alergi: { type: String, default: 'Tidak ada' },
  fotoUrl: { type: String, default: '' }
})

defineEmits(['print-wristband', 'view-history'])
</script>
