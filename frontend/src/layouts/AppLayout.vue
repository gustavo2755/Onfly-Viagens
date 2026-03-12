<script setup>
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '../stores/authStore'

const authStore = useAuthStore()
const router = useRouter()
const toast = useToast()

async function handleLogout() {
  await authStore.signOut()
  toast.success('Sessao encerrada')
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="min-h-screen">
    <header class="text-white brand-bg">
      <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
        <img src="/branding/logo-onfly-header.png" alt="Onfly logo" class="h-9 w-auto" />
        <div class="flex items-center gap-4">
          <span class="text-sm">{{ authStore.user?.name }} ({{ authStore.user?.role }})</span>
          <button class="rounded bg-sky-700 px-3 py-1 text-sm hover:bg-sky-800" @click="handleLogout">
            Sair
          </button>
        </div>
      </div>
    </header>

    <nav class="border-b bg-white">
      <div class="mx-auto flex max-w-6xl gap-4 px-4 py-3 text-sm">
        <RouterLink class="text-slate-700 hover:text-sky-600" to="/travel-orders">Pedidos</RouterLink>
        <RouterLink class="text-slate-700 hover:text-sky-600" to="/travel-orders/create">Novo Pedido</RouterLink>
        <RouterLink v-if="authStore.isAdmin" class="text-slate-700 hover:text-sky-600" to="/dashboard">
          Dashboard
        </RouterLink>
      </div>
    </nav>

    <main class="mx-auto max-w-6xl px-4 py-6">
      <slot />
    </main>
  </div>
</template>
