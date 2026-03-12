<script setup>
import {
  ArrowPathIcon,
  ChartBarIcon,
  CheckCircleIcon,
  ClockIcon,
  DocumentTextIcon,
  XCircleIcon,
} from '@heroicons/vue/24/outline'
import { onMounted, reactive, ref } from 'vue'
import { getErrorMessage } from '../utils/errorMessage'
import { useToast } from 'vue-toastification'
import AppLayout from '../layouts/AppLayout.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import StatusLogFilters from '../components/StatusLogFilters.vue'
import StatusLogTable from '../components/StatusLogTable.vue'
import TravelOrderDetailModal from '../components/TravelOrderDetailModal.vue'
import { useTravelOrderStore } from '../stores/travelOrderStore'

const toast = useToast()
const travelOrderStore = useTravelOrderStore()
const detailModalOpen = ref(false)

const logFilters = reactive({
  travel_order_id: '',
  admin_user_id: '',
  to_status: '',
  created_from: '',
  created_to: '',
  per_page: 10,
})

async function loadData() {
  try {
    await Promise.all([
      travelOrderStore.fetchDashboard(),
      travelOrderStore.fetchStatusLogs(logFilters),
    ])
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

      <div v-else class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-4">
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

        <div class="space-y-4">
          <h3 class="flex items-center gap-2 text-lg font-semibold text-slate-800">
            <ArrowPathIcon class="size-5 text-slate-500" />
            Últimas alterações de status
          </h3>

          <StatusLogFilters
            :model-value="logFilters"
            @update:model-value="Object.assign(logFilters, $event)"
            @search="loadData"
          />

          <StatusLogTable
            :items="travelOrderStore.statusLogs"
            @open="openDetail"
          />
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
