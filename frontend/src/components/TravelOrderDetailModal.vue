<script setup>
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
          class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl"
          role="dialog"
          aria-modal="true"
          aria-labelledby="detail-title"
        >
          <div class="flex items-center justify-between">
            <h3 id="detail-title" class="text-lg font-semibold">Detalhe do pedido</h3>
            <button
              class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
              aria-label="Fechar"
              @click="emit('close')"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div v-if="order" class="mt-4 space-y-3 text-sm">
            <p><strong>ID:</strong> {{ order.id }}</p>
            <p><strong>Solicitante:</strong> {{ order.requester_name }}</p>
            <p><strong>Destino:</strong> {{ order.destination }}</p>
            <p><strong>Saida:</strong> {{ order.departure_date_br || order.departure_date }}</p>
            <p><strong>Retorno:</strong> {{ order.return_date_br || order.return_date }}</p>
            <p><strong>Status:</strong> <StatusBadge :status="order.status" /></p>
          </div>
          <div class="mt-6 flex justify-end">
            <button
              class="rounded border px-4 py-2 text-sm hover:bg-slate-50"
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
