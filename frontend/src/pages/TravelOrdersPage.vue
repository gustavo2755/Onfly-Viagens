<script setup>
import { onMounted, reactive, ref } from 'vue'
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
import { listUsers } from '../services/userService'

const authStore = useAuthStore()
const travelOrderStore = useTravelOrderStore()
const toast = useToast()

const filters = reactive({
  status: '',
  destination: '',
  user_id: '',
  departure_from: '',
  departure_to: '',
  page: 1,
  per_page: 10,
})

const users = ref([])

async function loadUsers() {
  if (!authStore.isAdmin) return
  try {
    const data = await listUsers()
    users.value = data.data || []
  } catch {
    users.value = []
  }
}

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
    toast.error(error.response?.data?.message || 'Erro ao carregar pedidos')
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
  confirmDescription.value = `Confirmar alteracao para status ${status}?`
  confirmOpen.value = true
}

async function confirmStatusChange() {
  if (!selectedOrder.value || !nextStatus.value) {
    return
  }

  try {
    await travelOrderStore.changeStatus(selectedOrder.value.id, nextStatus.value)
    toast.success('Status atualizado')
    await loadData()
  } catch (error) {
    toast.error(error.response?.data?.message || 'Falha ao atualizar status')
  } finally {
    confirmOpen.value = false
  }
}

async function changePage(page) {
  filters.page = page
  await loadData()
}

onMounted(() => {
  loadUsers()
  loadData()
})
</script>

<template>
  <AppLayout>
    <div class="space-y-4">
      <h2 class="text-xl font-semibold">Pedidos de viagem</h2>

      <TravelOrderFilters
        :modelValue="filters"
        :users="users"
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
      @cancel="confirmOpen = false"
      @confirm="confirmStatusChange"
    />
  </AppLayout>
</template>
