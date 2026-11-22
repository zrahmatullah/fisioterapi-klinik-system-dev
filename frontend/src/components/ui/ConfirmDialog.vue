<template>
  <teleport to="body">
    <transition name="modal-fade">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
        @click.self="cancel"
      >
        <div class="w-full max-w-[460px] flex flex-col rounded-2xl bg-white border border-slate-200 shadow-2xl overflow-hidden animate-fadeIn">
          <div class="p-6 flex items-start gap-4">
            <div
              class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0"
              :class="iconBgClass"
            >
              <i :class="iconClass" class="text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
              <h4 class="text-base font-bold text-slate-900 leading-snug">{{ title }}</h4>
              <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">{{ message }}</p>
            </div>
          </div>

          <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-3 shrink-0">
            <button
              type="button"
              :disabled="loading"
              @click="cancel"
              class="h-[38px] px-4 rounded-lg text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 transition cursor-pointer"
            >
              {{ cancelText }}
            </button>
            <button
              type="button"
              :disabled="loading"
              @click="confirm"
              class="h-[38px] px-5 rounded-lg text-sm font-medium text-white transition shadow-sm inline-flex items-center gap-2 cursor-pointer disabled:opacity-50"
              :class="confirmBtnClass"
            >
              <i v-if="loading" class="pi pi-spin pi-spinner text-sm"></i>
              <span>{{ confirmText }}</span>
            </button>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: 'Konfirmasi Tindakan' },
  message: { type: String, default: 'Apakah Anda yakin ingin melanjutkan tindakan ini?' },
  type: { type: String, default: 'danger' }, // danger | warning | info | success
  confirmText: { type: String, default: 'Ya, Lanjutkan' },
  cancelText: { type: String, default: 'Batal' },
  loading: { type: Boolean, default: false }
})

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel'])

const iconBgClass = computed(() => {
  switch (props.type) {
    case 'danger': return 'bg-rose-100 text-rose-600'
    case 'warning': return 'bg-amber-100 text-amber-600'
    case 'success': return 'bg-emerald-100 text-emerald-600'
    case 'info':
    default: return 'bg-teal-100 text-[#0F766E]'
  }
})

const iconClass = computed(() => {
  switch (props.type) {
    case 'danger': return 'pi pi-exclamation-triangle'
    case 'warning': return 'pi pi-exclamation-circle'
    case 'success': return 'pi pi-check-circle'
    case 'info':
    default: return 'pi pi-info-circle'
  }
})

const confirmBtnClass = computed(() => {
  switch (props.type) {
    case 'danger': return 'bg-rose-600 hover:bg-rose-700'
    case 'warning': return 'bg-amber-600 hover:bg-amber-700'
    case 'success': return 'bg-emerald-600 hover:bg-emerald-700'
    case 'info':
    default: return 'bg-[#0F766E] hover:bg-[#115E59]'
  }
})

const cancel = () => {
  emit('update:modelValue', false)
  emit('cancel')
}

const confirm = () => {
  emit('confirm')
}
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.15s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.animate-fadeIn {
  animation: fadeIn 0.12s ease-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.98) translateY(3px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
</style>
