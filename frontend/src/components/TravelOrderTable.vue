<script setup>
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
  <div class="overflow-x-auto rounded bg-white shadow">
    <table class="min-w-full text-left text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="px-4 py-3">ID</th>
          <th class="px-4 py-3">Solicitante</th>
          <th class="px-4 py-3">Destino</th>
          <th class="px-4 py-3">Saida</th>
          <th class="px-4 py-3">Retorno</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in items" :key="item.id" class="border-t">
          <td class="px-4 py-3">{{ item.id }}</td>
          <td class="px-4 py-3">{{ item.requester_name }}</td>
          <td class="px-4 py-3">{{ item.destination }}</td>
          <td class="px-4 py-3">{{ item.departure_date_br || item.departure_date }}</td>
          <td class="px-4 py-3">{{ item.return_date_br || item.return_date }}</td>
          <td class="px-4 py-3"><StatusBadge :status="item.status" /></td>
          <td class="px-4 py-3">
            <div class="flex gap-2">
              <button class="rounded border px-2 py-1 text-xs" @click="emit('open', item)">Detalhe</button>
              <button
                v-if="isAdmin && item.status === 'requested'"
                class="rounded bg-emerald-600 px-2 py-1 text-xs text-white"
                @click="emit('approve', item)"
              >
                Aprovar
              </button>
              <button
                v-if="isAdmin && item.status === 'requested'"
                class="rounded bg-rose-600 px-2 py-1 text-xs text-white"
                @click="emit('cancel', item)"
              >
                Cancelar
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
