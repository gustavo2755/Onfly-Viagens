<script setup>
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import AppLayout from '../layouts/AppLayout.vue'
import TravelOrderForm from '../components/TravelOrderForm.vue'
import { useTravelOrderStore } from '../stores/travelOrderStore'

const router = useRouter()
const toast = useToast()
const travelOrderStore = useTravelOrderStore()

async function handleSubmit(payload) {
  try {
    await travelOrderStore.create(payload)
    toast.success('Pedido criado com sucesso')
    router.push({ name: 'travel-orders' })
  } catch (error) {
    toast.error(error.response?.data?.message || 'Erro ao criar pedido')
  }
}
</script>

<template>
  <AppLayout>
    <div class="space-y-4">
      <h2 class="text-xl font-semibold">Novo pedido de viagem</h2>
      <TravelOrderForm @submit="handleSubmit" />
    </div>
  </AppLayout>
</template>
