<script setup>
import { CheckIcon, EyeIcon, XMarkIcon } from '@heroicons/vue/20/solid'
import StatusBadge from './StatusBadge.vue'

defineProps({
  items: {
    type: Array,
    default: () => [],
  },
  isAdmin: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['approve', 'cancel', 'open'])
</script>

<template>
  <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-sm">
        <thead>
          <tr class="border-b border-slate-200 bg-slate-50/80">
            <th class="px-5 py-4 font-semibold text-slate-600">ID</th>
            <th class="px-5 py-4 font-semibold text-slate-600">Solicitante</th>
            <th class="px-5 py-4 font-semibold text-slate-600">Destino</th>
            <th class="px-5 py-4 font-semibold text-slate-600">Saída</th>
            <th class="px-5 py-4 font-semibold text-slate-600">Retorno</th>
            <th class="px-5 py-4 font-semibold text-slate-600">Status</th>
            <th class="px-5 py-4 font-semibold text-slate-600">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr
            v-for="item in items"
            :key="item.id"
            class="transition-colors hover:bg-slate-50/50"
          >
            <td class="px-5 py-4 font-medium text-slate-900">{{ item.id }}</td>
            <td class="px-5 py-4 text-slate-700">{{ item.requester_name }}</td>
            <td class="px-5 py-4 text-slate-700">{{ item.destination }}</td>
            <td class="px-5 py-4 text-slate-600">{{ item.departure_date_br || item.departure_date }}</td>
            <td class="px-5 py-4 text-slate-600">{{ item.return_date_br || item.return_date }}</td>
            <td class="px-5 py-4"><StatusBadge :status="item.status" /></td>
            <td class="px-5 py-4">
              <div class="flex flex-wrap gap-2">
                <button
                  class="inline-flex items-center gap-1.5 rounded-lg bg-sky-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-sky-700"
                  @click="emit('open', item)"
                >
                  <EyeIcon class="size-3.5" />
                  Detalhe
                </button>
                <button
                  v-if="isAdmin && item.status === 'requested'"
                  class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-emerald-700"
                  @click="emit('approve', item)"
                >
                  <CheckIcon class="size-3.5" />
                  Aprovar
                </button>
                <button
                  v-if="isAdmin && item.status === 'requested'"
                  class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-rose-700"
                  @click="emit('cancel', item)"
                >
                  <XMarkIcon class="size-3.5" />
                  Cancelar
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
