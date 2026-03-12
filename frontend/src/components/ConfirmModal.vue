<script setup>
import { ExclamationTriangleIcon, XMarkIcon } from '@heroicons/vue/24/outline'

defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Confirmar ação',
  },
  description: {
    type: String,
    default: '',
  },
  variant: {
    type: String,
    default: 'default',
    validator: (v) => ['default', 'danger'].includes(v),
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['cancel', 'confirm'])
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="open"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
        @click.self="emit('cancel')"
      >
        <div
          class="relative w-full max-w-md rounded-xl border border-slate-200 bg-white p-6 shadow-xl"
          role="dialog"
          aria-modal="true"
          aria-labelledby="confirm-title"
        >
          <button
            type="button"
            class="absolute right-4 top-4 rounded p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
            aria-label="Fechar"
            @click="emit('cancel')"
          >
            <XMarkIcon class="size-5" />
          </button>

          <div class="flex flex-col items-center text-center">
            <div
              class="flex size-12 items-center justify-center rounded-full"
              :class="variant === 'danger' ? 'bg-red-100' : 'bg-emerald-100'"
            >
              <ExclamationTriangleIcon
                class="size-6"
                :class="variant === 'danger' ? 'text-red-600' : 'text-emerald-600'"
              />
            </div>
            <h3 id="confirm-title" class="mt-4 text-lg font-semibold text-slate-800">
              {{ title }}
            </h3>
            <p class="mt-2 text-sm text-slate-600">{{ description }}</p>
          </div>

          <div class="mt-6 flex justify-center gap-3">
            <button
              class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
              @click="emit('cancel')"
            >
              Cancelar
            </button>
            <button
              :disabled="loading"
              :class="[
                'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white transition disabled:opacity-60',
                variant === 'danger'
                  ? 'bg-red-600 hover:bg-red-700'
                  : 'bg-emerald-600 hover:bg-emerald-700',
              ]"
              @click="emit('confirm')"
            >
              <span
                v-if="loading"
                class="size-4 animate-spin rounded-full border-2 border-white/30 border-t-white"
              />
              {{ loading ? 'Processando...' : 'Confirmar' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
