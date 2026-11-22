<template>
  <div class="w-full space-y-0.5">
    <label v-if="label" class="block text-[12px] font-medium text-slate-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-rose-600 font-bold ml-0.5">*</span>
    </label>

    <textarea
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :placeholder="placeholder"
      :rows="rows"
      :disabled="disabled"
      :readonly="readonly"
      :required="required"
      class="w-full px-2.5 py-1.5 text-[12.5px] bg-white border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 disabled:bg-slate-100 disabled:text-slate-500 transition custom-scrollbar"
      :class="error ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-200' : ''"
    ></textarea>

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
  modelValue: { type: String, default: '' },
  label: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  rows: { type: Number, default: 3 },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  readonly: { type: Boolean, default: false },
  error: { type: String, default: '' },
  hint: { type: String, default: '' }
})

defineEmits(['update:modelValue'])
</script>
