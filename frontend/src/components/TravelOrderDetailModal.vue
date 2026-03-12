<script setup>
import {
  ClipboardDocumentListIcon,
  MapPinIcon,
  UserIcon,
} from '@heroicons/vue/24/outline'
import StatusBadge from './StatusBadge.vue'

defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  order: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close'])
</script>

<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
        @click.self="emit('close')"
      >
        <div
          class="w-full max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-xl"
          role="dialog"
          aria-modal="true"
          aria-labelledby="detail-title"
        >
          <div class="relative flex items-center justify-center">
            <h3 id="detail-title" class="text-lg font-semibold text-slate-800">Detalhes do pedido</h3>
            <button
              class="absolute right-0 top-0 rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
              aria-label="Fechar"
              @click="emit('close')"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div v-if="order" class="mt-5 space-y-4">
            <div class="rounded-lg border border-slate-200 p-4 shadow-sm">
              <h4 class="flex items-center gap-2 text-sm font-semibold text-slate-800">
                <ClipboardDocumentListIcon class="size-4 text-slate-500" />
                Informações do pedido
              </h4>
              <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <p class="text-xs font-medium text-slate-500">ID</p>
                  <p class="mt-0.5 text-sm text-slate-900">{{ order.id }}</p>
                </div>
                <div>
                  <p class="text-xs font-medium text-slate-500">Status</p>
                  <p class="mt-0.5"><StatusBadge :status="order.status" /></p>
                </div>
              </div>
            </div>
            <div class="rounded-lg border border-slate-200 p-4 shadow-sm">
              <h4 class="flex items-center gap-2 text-sm font-semibold text-slate-800">
                <UserIcon class="size-4 text-slate-500" />
                Solicitante
              </h4>
              <div class="mt-3">
                <p class="text-xs font-medium text-slate-500">Nome</p>
                <p class="mt-0.5 text-sm text-slate-900">{{ order.requester_name }}</p>
              </div>
            </div>
            <div class="rounded-lg border border-slate-200 p-4 shadow-sm">
              <h4 class="flex items-center gap-2 text-sm font-semibold text-slate-800">
                <MapPinIcon class="size-4 text-slate-500" />
                Informações da viagem
              </h4>
              <div class="mt-3 space-y-4">
                <div>
                  <p class="text-xs font-medium text-slate-500">Destino</p>
                  <p class="mt-0.5 text-sm text-slate-900">{{ order.destination }}</p>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <p class="text-xs font-medium text-slate-500">Saída</p>
                    <p class="mt-0.5 text-sm text-slate-900">{{ order.departure_date_br || order.departure_date }}</p>
                  </div>
                  <div>
                    <p class="text-xs font-medium text-slate-500">Retorno</p>
                    <p class="mt-0.5 text-sm text-slate-900">{{ order.return_date_br || order.return_date }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-6 flex justify-end">
            <button
              class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700"
              @click="emit('close')"
            >
              Fechar
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
