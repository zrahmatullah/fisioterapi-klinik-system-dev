<template>
  <div class="w-full space-y-0.5">
    <label v-if="label" class="block text-[12px] font-medium text-slate-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-rose-600 font-bold ml-0.5">*</span>
    </label>

    <div class="relative flex items-center">
      <i
        v-if="icon"
        :class="icon"
        class="absolute left-2.5 text-slate-400 text-xs pointer-events-none"
      ></i>

      <select
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        :disabled="disabled"
        :required="required"
        class="w-full h-[34px] px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 disabled:bg-slate-100 disabled:text-slate-500 transition cursor-pointer appearance-none pr-8"
        :class="[
          icon ? 'pl-8' : '',
          error ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-200' : ''
        ]"
      >
        <option v-if="placeholder" value="" disabled selected>{{ placeholder }}</option>
        <slot>
          <option
            v-for="opt in options"
            :key="opt.value ?? opt"
            :value="opt.value ?? opt"
          >
            {{ opt.label ?? opt }}
          </option>
        </slot>
      </select>

      <i class="pi pi-chevron-down absolute right-2.5 text-slate-400 text-[9px] pointer-events-none"></i>
    </div>

    <p v-if="error" class="text-[11px] text-rose-600 mt-0.5">
      {{ error }}
    </p>
    <p v-else-if="hint" class="text-[11px] text-slate-500 mt-0.5">
      {{ hint }}
    </p>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: [String, Number, Boolean], default: '' },
  label: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  options: { type: Array, default: () => [] },
  icon: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' },
  hint: { type: String, default: '' }
})

defineEmits(['update:modelValue'])
</script>
