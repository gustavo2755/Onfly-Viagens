<script setup>
import {
  ArrowPathIcon,
  ChartBarIcon,
  CheckCircleIcon,
  ClockIcon,
  DocumentTextIcon,
  UserIcon,
  XCircleIcon,
} from '@heroicons/vue/24/outline'
import { EyeIcon } from '@heroicons/vue/20/solid'
import { onMounted, ref } from 'vue'
import { getErrorMessage } from '../utils/errorMessage'
import { useToast } from 'vue-toastification'
import AppLayout from '../layouts/AppLayout.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import StatusBadge from '../components/StatusBadge.vue'
import TravelOrderDetailModal from '../components/TravelOrderDetailModal.vue'
import { useTravelOrderStore } from '../stores/travelOrderStore'

const toast = useToast()
const travelOrderStore = useTravelOrderStore()
const detailModalOpen = ref(false)

async function loadData() {
  try {
    await Promise.all([travelOrderStore.fetchDashboard(), travelOrderStore.fetchStatusLogs({ per_page: 10 })])
  } catch (error) {
    toast.error(getErrorMessage(error))
  }
}

async function openDetail(travelOrderId) {
  try {
    await travelOrderStore.fetchOne(travelOrderId)
    detailModalOpen.value = true
  } catch (error) {
    toast.error(getErrorMessage(error))
  }
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

onMounted(loadData)
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <h2 class="flex items-center gap-2 text-2xl font-semibold tracking-tight text-slate-800">
        <ChartBarIcon class="size-7 text-sky-600" />
        Dashboard Admin
      </h2>

      <LoadingSpinner v-if="!travelOrderStore.dashboard" />

      <div v-else class="grid gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
          <div class="flex items-center gap-2 text-slate-500">
            <DocumentTextIcon class="size-4" />
            <p class="text-xs font-medium uppercase tracking-wide">Total</p>
          </div>
          <p class="mt-2 text-2xl font-semibold text-slate-900">{{ travelOrderStore.dashboard.total }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
          <div class="flex items-center gap-2 text-amber-600">
            <ClockIcon class="size-4" />
            <p class="text-xs font-medium uppercase tracking-wide">Solicitado</p>
          </div>
          <p class="mt-2 text-2xl font-semibold text-slate-900">{{ travelOrderStore.dashboard.requested }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
          <div class="flex items-center gap-2 text-emerald-600">
            <CheckCircleIcon class="size-4" />
            <p class="text-xs font-medium uppercase tracking-wide">Aprovado</p>
          </div>
          <p class="mt-2 text-2xl font-semibold text-slate-900">{{ travelOrderStore.dashboard.approved }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
          <div class="flex items-center gap-2 text-rose-600">
            <XCircleIcon class="size-4" />
            <p class="text-xs font-medium uppercase tracking-wide">Cancelado</p>
          </div>
          <p class="mt-2 text-2xl font-semibold text-slate-900">{{ travelOrderStore.dashboard.cancelled }}</p>
        </div>
      </div>

      <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50/80 px-5 py-4">
          <h3 class="flex items-center gap-2 text-lg font-semibold text-slate-800">
            <ArrowPathIcon class="size-5 text-slate-500" />
            Últimas alterações de status
          </h3>
        </div>
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
                v-for="log in travelOrderStore.statusLogs"
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
                    @click="openDetail(log.travel_order_id)"
                  >
                    <EyeIcon class="size-3.5" />
                    Detalhe
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <TravelOrderDetailModal
      :open="detailModalOpen"
      :order="travelOrderStore.selected"
      @close="detailModalOpen = false"
    />
  </AppLayout>
</template>
