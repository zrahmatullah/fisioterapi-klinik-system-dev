<template>
  <div class="bg-[#F0F6F8] border border-[#DCE8EC] rounded-lg px-2.5 py-2 text-center transition hover:border-[#CBD5E1]">
    <div class="text-[10.5px] font-medium text-[#64748B] uppercase tracking-wide leading-none">
      {{ label }}
    </div>
    <div class="text-[14px] font-bold text-[#0F172A] tabular-nums mt-1 leading-tight">
      {{ value || '-' }}
    </div>
    <div v-if="unit" class="text-[9.5px] text-[#64748B] mt-0.5 leading-none">
      {{ unit }}
    </div>
  </div>
</template>

<script setup>
defineProps({
  label: { type: String, required: true },
  value: { type: [String, Number], default: '-' },
  unit: { type: String, default: '' },
})
</script>
