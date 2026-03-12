<script setup>
import { reactive } from 'vue'

const emit = defineEmits(['submit'])

const form = reactive({
  requester_name: '',
  destination: '',
  departure_date: '',
  return_date: '',
})

function handleSubmit() {
  emit('submit', { ...form })
}
</script>

<template>
  <form class="space-y-4 rounded bg-white p-4 shadow" @submit.prevent="handleSubmit">
    <div>
      <label class="mb-1 block text-sm font-medium">Nome do solicitante</label>
      <input v-model="form.requester_name" class="w-full rounded border px-3 py-2" required minlength="3" />
    </div>
    <div>
      <label class="mb-1 block text-sm font-medium">Destino</label>
      <input v-model="form.destination" class="w-full rounded border px-3 py-2" required minlength="2" />
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <label class="mb-1 block text-sm font-medium">Data de saida</label>
        <input v-model="form.departure_date" type="date" class="w-full rounded border px-3 py-2" required />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium">Data de retorno</label>
        <input v-model="form.return_date" type="date" class="w-full rounded border px-3 py-2" required />
      </div>
    </div>
    <button class="rounded bg-slate-900 px-4 py-2 text-white">Criar pedido</button>
  </form>
</template>
