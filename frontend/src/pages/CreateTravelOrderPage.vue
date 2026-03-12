<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { getErrorMessage } from '../utils/errorMessage'
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
    toast.error(getErrorMessage(error))
  }
}
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <h2 class="flex items-center gap-2 text-2xl font-semibold tracking-tight text-slate-800">
        <PlusIcon class="size-7 text-sky-600" />
        Novo pedido de viagem
      </h2>
      <TravelOrderForm @submit="handleSubmit" />
    </div>
  </AppLayout>
</template>
