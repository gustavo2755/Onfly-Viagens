<script setup>
import { EyeIcon, UserIcon } from '@heroicons/vue/24/outline'
import StatusBadge from './StatusBadge.vue'

defineProps({
  items: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['open'])

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>

<template>
  <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-sm">
        <thead>
          <tr class="border-b border-slate-200 bg-slate-50/80">
            <th class="px-5 py-4 font-semibold text-slate-600">Pedido</th>
            <th class="px-5 py-4 font-semibold text-slate-600">Admin</th>
            <th class="px-5 py-4 font-semibold text-slate-600">De</th>
            <th class="px-5 py-4 font-semibold text-slate-600">Para</th>
            <th class="px-5 py-4 font-semibold text-slate-600">Quando</th>
            <th class="px-5 py-4 font-semibold text-slate-600">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr
            v-for="log in items"
            :key="log.id"
            class="transition-colors hover:bg-slate-50/50"
          >
            <td class="px-5 py-4 font-medium text-slate-900">#{{ log.travel_order_id }}</td>
            <td class="px-5 py-4">
              <span class="inline-flex items-center gap-1.5 text-slate-700">
                <UserIcon class="size-4 text-slate-400" />
                {{ log.admin_user?.name }}
              </span>
            </td>
            <td class="px-5 py-4"><StatusBadge :status="log.from_status" /></td>
            <td class="px-5 py-4"><StatusBadge :status="log.to_status" /></td>
            <td class="px-5 py-4 text-slate-600">{{ formatDate(log.created_at) }}</td>
            <td class="px-5 py-4">
              <button
                class="inline-flex items-center gap-1.5 rounded-lg bg-sky-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-sky-700"
                @click="emit('open', log.travel_order_id)"
              >
                <EyeIcon class="size-3.5" />
                Detalhes
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
