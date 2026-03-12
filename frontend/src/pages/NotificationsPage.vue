<script setup>
import {
  BellIcon,
  CheckIcon,
  DocumentTextIcon,
  EyeIcon,
} from '@heroicons/vue/24/outline'
import { onMounted, reactive, ref } from 'vue'
import { getErrorMessage } from '../utils/errorMessage'
import { useToast } from 'vue-toastification'
import AppLayout from '../layouts/AppLayout.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import PaginationControls from '../components/PaginationControls.vue'
import StatusBadge from '../components/StatusBadge.vue'
import TravelOrderDetailModal from '../components/TravelOrderDetailModal.vue'
import { useNotificationStore } from '../stores/notificationStore'
import { useTravelOrderStore } from '../stores/travelOrderStore'

const toast = useToast()
const notificationStore = useNotificationStore()
const travelOrderStore = useTravelOrderStore()
const detailModalOpen = ref(false)

const filters = reactive({
  page: 1,
  per_page: 10,
})

async function loadData() {
  try {
    await notificationStore.fetchList({
      page: filters.page,
      per_page: filters.per_page,
    })
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

async function markAsRead(notification) {
  try {
    await notificationStore.markAsRead(notification.id)
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

async function changePage(page) {
  filters.page = page
  await loadData()
}

function closeDetailModal() {
  detailModalOpen.value = false
}

onMounted(loadData)
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <h2 class="flex items-center gap-2 text-2xl font-semibold tracking-tight text-slate-800">
        <BellIcon class="size-7 text-sky-600" />
        Notificações
      </h2>

      <LoadingSpinner v-if="notificationStore.loading" />

      <div v-else class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50/80 px-5 py-4">
          <h3 class="flex items-center gap-2 text-lg font-semibold text-slate-800">
            <DocumentTextIcon class="size-5 text-slate-500" />
            Todas as notificações
          </h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-left text-sm">
            <thead>
              <tr class="border-b border-slate-200 bg-slate-50/80">
                <th class="px-5 py-4 font-semibold text-slate-600">Pedido</th>
                <th class="px-5 py-4 font-semibold text-slate-600">De</th>
                <th class="px-5 py-4 font-semibold text-slate-600">Para</th>
                <th class="px-5 py-4 font-semibold text-slate-600">Quando</th>
                <th class="px-5 py-4 font-semibold text-slate-600">Lida</th>
                <th class="px-5 py-4 font-semibold text-slate-600">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="n in notificationStore.items"
                :key="n.id"
                class="transition-colors hover:bg-slate-50/50"
              >
                <td class="px-5 py-4 font-medium text-slate-900">#{{ n.data?.travel_order_id }}</td>
                <td class="px-5 py-4"><StatusBadge :status="n.data?.from_status" /></td>
                <td class="px-5 py-4"><StatusBadge :status="n.data?.to_status" /></td>
                <td class="px-5 py-4 text-slate-600">{{ formatDate(n.created_at) }}</td>
                <td class="px-5 py-4">
                  <span v-if="n.read_at" class="text-emerald-600">Sim</span>
                  <span v-else class="text-amber-600">Não</span>
                </td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-2">
                    <button
                      v-if="!n.read_at"
                      class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 shadow-sm transition hover:bg-slate-50"
                      @click="markAsRead(n)"
                    >
                      <CheckIcon class="size-3.5" />
                      Marcar como lida
                    </button>
                    <button
                      class="inline-flex items-center gap-1.5 rounded-lg bg-sky-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-sky-700"
                      @click="openDetail(n.data?.travel_order_id)"
                    >
                      <EyeIcon class="size-3.5" />
                      Detalhes
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <PaginationControls :meta="notificationStore.meta" @change="changePage" />
    </div>

    <TravelOrderDetailModal
      :open="detailModalOpen"
      :order="travelOrderStore.selected"
      @close="closeDetailModal"
    />
  </AppLayout>
</template>
