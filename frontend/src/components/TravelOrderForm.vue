<script setup>
import {
  CalendarDaysIcon,
  MapPinIcon,
  UserIcon,
} from '@heroicons/vue/24/outline'
import { PaperAirplaneIcon } from '@heroicons/vue/20/solid'
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
  <form
    class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
    @submit.prevent="handleSubmit"
  >
    <div class="space-y-4">
      <div>
        <label class="mb-1.5 flex items-center gap-1.5 text-xs font-medium text-slate-500">
          <UserIcon class="size-3.5" />
          Nome do solicitante
        </label>
        <input
          v-model="form.requester_name"
          type="text"
          required
          minlength="3"
          class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
          placeholder="Ex: João Silva"
        />
      </div>
      <div>
        <label class="mb-1.5 flex items-center gap-1.5 text-xs font-medium text-slate-500">
          <MapPinIcon class="size-3.5" />
          Destino
        </label>
        <input
          v-model="form.destination"
          type="text"
          required
          minlength="2"
          class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
          placeholder="Ex: Lisboa"
        />
      </div>
      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="mb-1.5 flex items-center gap-1.5 text-xs font-medium text-slate-500">
            <CalendarDaysIcon class="size-3.5" />
            Data de saída
          </label>
          <input
            v-model="form.departure_date"
            type="date"
            required
            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
          />
        </div>
        <div>
          <label class="mb-1.5 flex items-center gap-1.5 text-xs font-medium text-slate-500">
            <CalendarDaysIcon class="size-3.5" />
            Data de retorno
          </label>
          <input
            v-model="form.return_date"
            type="date"
            required
            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
          />
        </div>
      </div>
    </div>
    <div class="mt-6 flex justify-end">
      <button
        type="submit"
        class="inline-flex items-center gap-1.5 rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-sky-700"
      >
        <PaperAirplaneIcon class="size-4" />
        Criar pedido
      </button>
    </div>
  </form>
</template>
