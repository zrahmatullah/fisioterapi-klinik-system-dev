<template>
  <span
    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium border tabular-nums select-none"
    :class="variantClasses"
  >
    <span v-if="showDot" class="w-1.5 h-1.5 rounded-full" :class="dotClasses"></span>
    <slot>{{ label }}</slot>
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: { type: String, default: '' },
  type: {
    type: String,
    default: 'success', // success | danger | warning | info | neutral
  },
  showDot: { type: Boolean, default: true },
})

const variantClasses = computed(() => {
  switch (props.type) {
    case 'success':
      return 'bg-[#DCFCE7] text-[#15803D] border-emerald-200/60'
    case 'danger':
      return 'bg-red-50 text-red-700 border-red-200'
    case 'warning':
      return 'bg-amber-50 text-amber-700 border-amber-200'
    case 'info':
      return 'bg-[#E6F2F5] text-[#0F766E] border-[#CBE3E9]'
    default:
      return 'bg-slate-100 text-slate-700 border-slate-200'
  }
})

const dotClasses = computed(() => {
  switch (props.type) {
    case 'success':
      return 'bg-[#22C55E]'
    case 'danger':
      return 'bg-red-500'
    case 'warning':
      return 'bg-amber-500'
    case 'info':
      return 'bg-[#0F766E]'
    default:
      return 'bg-slate-400'
  }
})
</script>
