<script setup>
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useToast } from 'vue-toastification'
import { getErrorMessage } from '../utils/errorMessage'
import AppLayout from '../layouts/AppLayout.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import StatusBadge from '../components/StatusBadge.vue'
import { useTravelOrderStore } from '../stores/travelOrderStore'

const route = useRoute()
const toast = useToast()
const travelOrderStore = useTravelOrderStore()

async function loadOrder() {
  try {
    await travelOrderStore.fetchOne(route.params.id)
  } catch (error) {
    toast.error(getErrorMessage(error))
  }
}

onMounted(loadOrder)
</script>

<template>
  <AppLayout>
    <div class="space-y-4">
      <h2 class="text-xl font-semibold">Detalhe do pedido</h2>

      <LoadingSpinner v-if="travelOrderStore.loading" />

      <div v-else-if="travelOrderStore.selected" class="space-y-2 rounded bg-white p-4 shadow">
        <p><strong>ID:</strong> {{ travelOrderStore.selected.id }}</p>
        <p><strong>Solicitante:</strong> {{ travelOrderStore.selected.requester_name }}</p>
        <p><strong>Destino:</strong> {{ travelOrderStore.selected.destination }}</p>
        <p><strong>Saida:</strong> {{ travelOrderStore.selected.departure_date_br || travelOrderStore.selected.departure_date }}</p>
        <p><strong>Retorno:</strong> {{ travelOrderStore.selected.return_date_br || travelOrderStore.selected.return_date }}</p>
        <p><strong>Status:</strong> <StatusBadge :status="travelOrderStore.selected.status" /></p>
      </div>
    </div>
  </AppLayout>
</template>
