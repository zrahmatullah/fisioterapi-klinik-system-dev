<template>
  <teleport to="body">
    <transition name="modal-fade">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
        @click.self="handleBackdropClick"
      >
        <div
          class="w-full max-h-[90dvh] flex flex-col rounded-2xl bg-white border border-slate-200 shadow-2xl overflow-hidden animate-fadeIn"
          :class="sizeClass"
        >
          <!-- MODAL HEADER -->
          <div class="px-6 py-4 bg-[#0B3948] text-white flex items-center justify-between shrink-0 select-none">
            <div class="flex items-center gap-3 min-w-0">
              <div v-if="icon" class="w-8 h-8 rounded-lg bg-teal-500/20 border border-teal-400/30 text-teal-300 flex items-center justify-center text-sm font-bold shrink-0">
                <i :class="icon"></i>
              </div>
              <div class="min-w-0">
                <h3 class="text-base font-bold text-white tracking-wide truncate leading-tight">
                  {{ title }}
                </h3>
                <p v-if="subtitle" class="text-xs text-teal-200/90 truncate mt-0.5">
                  {{ subtitle }}
                </p>
              </div>
            </div>

            <button
              type="button"
              @click="close"
              class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-white hover:bg-white/10 transition cursor-pointer"
              title="Tutup"
            >
              <i class="pi pi-times text-sm"></i>
            </button>
          </div>

          <!-- MODAL BODY -->
          <div class="px-6 py-5 flex-1 min-h-0 overflow-y-auto custom-scrollbar text-sm text-slate-700 space-y-4">
            <slot />
          </div>

          <!-- MODAL FOOTER -->
          <div v-if="showFooter" class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-3 shrink-0">
            <slot name="footer">
              <button
                v-if="showCancel"
                type="button"
                :disabled="loading"
                @click="cancel"
                class="h-[38px] px-4 bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 rounded-lg text-sm font-medium transition cursor-pointer disabled:opacity-50"
              >
                {{ cancelText }}
              </button>

              <button
                v-if="showSubmit"
                type="button"
                :disabled="loading"
                @click="$emit('submit')"
                class="h-[38px] px-5 bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white rounded-lg text-sm font-medium transition shadow-sm inline-flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i v-if="loading" class="pi pi-spin pi-spinner text-sm"></i>
                <i v-else-if="submitIcon" :class="submitIcon" class="text-sm"></i>
                <span>{{ submitText }}</span>
              </button>
            </slot>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Form Modal'
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'md' // sm | md | lg | xl
  },
  showFooter: {
    type: Boolean,
    default: true
  },
  showCancel: {
    type: Boolean,
    default: true
  },
  cancelText: {
    type: String,
    default: 'Batal'
  },
  showSubmit: {
    type: Boolean,
    default: true
  },
  submitText: {
    type: String,
    default: 'Simpan'
  },
  submitIcon: {
    type: String,
    default: 'pi pi-check'
  },
  loading: {
    type: Boolean,
    default: false
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:modelValue', 'cancel', 'submit', 'close'])

const sizeClass = computed(() => {
  switch (props.size) {
    case 'sm': return 'max-w-[420px]'
    case 'lg': return 'max-w-[820px]'
    case 'xl': return 'max-w-[1024px]'
    case 'md':
    default: return 'max-w-[640px]' // Default 2-column form
  }
})

const close = () => {
  emit('update:modelValue', false)
  emit('close')
}

const cancel = () => {
  emit('cancel')
  close()
}

const handleBackdropClick = () => {
  if (props.closeOnBackdrop && !props.loading) {
    close()
  }
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
