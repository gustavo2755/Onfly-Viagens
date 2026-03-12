<script setup>
import { onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import AppLayout from '../layouts/AppLayout.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import { useTravelOrderStore } from '../stores/travelOrderStore'

const toast = useToast()
const travelOrderStore = useTravelOrderStore()

async function loadData() {
  try {
    await Promise.all([travelOrderStore.fetchDashboard(), travelOrderStore.fetchStatusLogs({ per_page: 10 })])
  } catch (error) {
    toast.error(error.response?.data?.message || 'Falha ao carregar dashboard')
  }
}

onMounted(loadData)
</script>

<template>
  <AppLayout>
    <div class="space-y-4">
      <h2 class="text-xl font-semibold">Dashboard Admin</h2>

      <LoadingSpinner v-if="!travelOrderStore.dashboard" />

      <div v-else class="grid gap-3 sm:grid-cols-4">
        <div class="rounded bg-white p-4 shadow">
          <p class="text-sm text-slate-500">Total</p>
          <p class="text-2xl font-semibold">{{ travelOrderStore.dashboard.total }}</p>
        </div>
        <div class="rounded bg-white p-4 shadow">
          <p class="text-sm text-slate-500">Requested</p>
          <p class="text-2xl font-semibold">{{ travelOrderStore.dashboard.requested }}</p>
        </div>
        <div class="rounded bg-white p-4 shadow">
          <p class="text-sm text-slate-500">Approved</p>
          <p class="text-2xl font-semibold">{{ travelOrderStore.dashboard.approved }}</p>
        </div>
        <div class="rounded bg-white p-4 shadow">
          <p class="text-sm text-slate-500">Cancelled</p>
          <p class="text-2xl font-semibold">{{ travelOrderStore.dashboard.cancelled }}</p>
        </div>
      </div>

      <div class="rounded bg-white p-4 shadow">
        <h3 class="mb-3 text-lg font-semibold">Ultimas alteracoes de status</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-3 py-2">Pedido</th>
                <th class="px-3 py-2">Admin</th>
                <th class="px-3 py-2">De</th>
                <th class="px-3 py-2">Para</th>
                <th class="px-3 py-2">Quando</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="log in travelOrderStore.statusLogs" :key="log.id" class="border-t">
                <td class="px-3 py-2">#{{ log.travel_order_id }}</td>
                <td class="px-3 py-2">{{ log.admin_user?.name }}</td>
                <td class="px-3 py-2">{{ log.from_status }}</td>
                <td class="px-3 py-2">{{ log.to_status }}</td>
                <td class="px-3 py-2">{{ new Date(log.created_at).toLocaleString() }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
