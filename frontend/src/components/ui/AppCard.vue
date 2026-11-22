<template>
  <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.04)]">
    <div v-if="title || $slots.header" class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-slate-100">
      <div>
        <h3 v-if="title" class="text-[14px] font-semibold text-[#0F172A] leading-tight">{{ title }}</h3>
        <p v-if="subtitle" class="text-[11px] text-[#64748B] mt-0.5">{{ subtitle }}</p>
      </div>
      <slot name="header-actions" />
    </div>
    <div>
      <slot />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, default: '' },
  subtitle: { type: String, default: '' },
})
</script>
