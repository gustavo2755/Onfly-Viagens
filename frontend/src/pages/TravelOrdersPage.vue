<script setup>
import { DocumentTextIcon } from '@heroicons/vue/24/outline'
import { onMounted, reactive, ref } from 'vue'
import { getErrorMessage } from '../utils/errorMessage'
import { useToast } from 'vue-toastification'
import AppLayout from '../layouts/AppLayout.vue'
import ConfirmModal from '../components/ConfirmModal.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import PaginationControls from '../components/PaginationControls.vue'
import TravelOrderDetailModal from '../components/TravelOrderDetailModal.vue'
import TravelOrderFilters from '../components/TravelOrderFilters.vue'
import TravelOrderTable from '../components/TravelOrderTable.vue'
import { useAuthStore } from '../stores/authStore'
import { useTravelOrderStore } from '../stores/travelOrderStore'
const authStore = useAuthStore()
const travelOrderStore = useTravelOrderStore()
const toast = useToast()
const statusChangeLoading = ref(false)

const filters = reactive({
  status: '',
  destination: '',
  requester_name: '',
  user_id: '',
  departure_from: '',
  departure_to: '',
  created_from: '',
  created_to: '',
  page: 1,
  per_page: 10,
})

const detailModalOpen = ref(false)
const selectedOrderForDetail = ref(null)
const confirmOpen = ref(false)
const confirmTitle = ref('')
const confirmDescription = ref('')
const selectedOrder = ref(null)
const nextStatus = ref('')

async function loadData() {
  try {
    await travelOrderStore.fetchList(filters)
  } catch (error) {
    toast.error(getErrorMessage(error))
  }
}

function openDetail(order) {
  selectedOrderForDetail.value = order
  detailModalOpen.value = true
}

function askStatusChange(order, status) {
  selectedOrder.value = order
  nextStatus.value = status
  confirmTitle.value = status === 'approved' ? 'Aprovar pedido' : 'Cancelar pedido'
  confirmDescription.value =
    status === 'approved'
      ? `Deseja aprovar o pedido #${order.id} de ${order.requester_name}?`
      : `Deseja cancelar o pedido #${order.id} de ${order.requester_name}?`
  confirmOpen.value = true
}

async function confirmStatusChange() {
  if (!selectedOrder.value || !nextStatus.value) {
    return
  }

  statusChangeLoading.value = true
  try {
    await travelOrderStore.changeStatus(selectedOrder.value.id, nextStatus.value)
    toast.success('Status atualizado')
    await loadData()
  } catch (error) {
    toast.error(getErrorMessage(error))
    if (error?.response?.status === 409) {
      await loadData()
    }
  } finally {
    statusChangeLoading.value = false
    confirmOpen.value = false
  }
}

async function changePage(page) {
  filters.page = page
  await loadData()
}

onMounted(() => {
  loadData()
})
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <h2 class="flex items-center gap-2 text-2xl font-semibold tracking-tight text-slate-800">
        <DocumentTextIcon class="size-7 text-sky-600" />
        Pedidos de viagem
      </h2>

      <TravelOrderFilters
        :modelValue="filters"
        :is-admin="authStore.isAdmin"
        @update:modelValue="Object.assign(filters, $event)"
        @search="loadData"
      />

      <LoadingSpinner v-if="travelOrderStore.loading" />
      <TravelOrderTable
        v-else
        :items="travelOrderStore.items"
        :is-admin="authStore.isAdmin"
        @approve="askStatusChange($event, 'approved')"
        @cancel="askStatusChange($event, 'cancelled')"
        @open="openDetail"
      />

      <PaginationControls :meta="travelOrderStore.meta" @change="changePage" />
    </div>

    <TravelOrderDetailModal
      :open="detailModalOpen"
      :order="selectedOrderForDetail"
      @close="detailModalOpen = false"
    />

    <ConfirmModal
      :open="confirmOpen"
      :title="confirmTitle"
      :description="confirmDescription"
      :variant="nextStatus === 'cancelled' ? 'danger' : 'default'"
      :loading="statusChangeLoading"
      @cancel="confirmOpen = false"
      @confirm="confirmStatusChange"
    />
  </AppLayout>
</template>
