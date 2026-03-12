<script setup>
import { BellIcon, ChartBarIcon, CodeBracketIcon, DocumentTextIcon, PlusIcon } from '@heroicons/vue/24/outline'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import NotificationBell from '../components/NotificationBell.vue'
import { useAuthStore } from '../stores/authStore'

const authStore = useAuthStore()
const router = useRouter()
const toast = useToast()

const apiDocsUrl = (import.meta.env.VITE_BACKEND_URL || 'http://localhost:8080') + '/api/documentation'

async function handleLogout() {
  await authStore.signOut()
  toast.success('Sessão encerrada')
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="min-h-screen overflow-x-hidden">
    <header class="text-white brand-bg">
      <div class="layout-container flex items-center justify-between py-3">
        <RouterLink to="/travel-orders" class="block cursor-pointer">
          <img src="/branding/logo-onfly-header.png" alt="Onfly logo" class="h-9 w-auto" />
        </RouterLink>
        <div class="flex items-center gap-4">
          <NotificationBell />
          <span class="text-sm">{{ authStore.user?.name }} ({{ authStore.user?.role }})</span>
          <button class="rounded bg-sky-700 px-3 py-1 text-sm hover:bg-sky-800" @click="handleLogout">
            Sair
          </button>
        </div>
      </div>
    </header>

    <nav class="border-b border-slate-200 bg-white shadow-sm">
      <div class="layout-container flex gap-1 py-3 text-sm">
        <RouterLink
          class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium text-slate-600 transition hover:bg-slate-50 hover:text-sky-600"
          :class="{ 'bg-slate-50 text-sky-600': $route.path === '/travel-orders' }"
          to="/travel-orders"
        >
          <DocumentTextIcon class="size-4" />
          Pedidos
        </RouterLink>
        <RouterLink
          class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium text-slate-600 transition hover:bg-slate-50 hover:text-sky-600"
          :class="{ 'bg-slate-50 text-sky-600': $route.path === '/travel-orders/create' }"
          to="/travel-orders/create"
        >
          <PlusIcon class="size-4" />
          Novo Pedido
        </RouterLink>
        <RouterLink
          class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium text-slate-600 transition hover:bg-slate-50 hover:text-sky-600"
          :class="{ 'bg-slate-50 text-sky-600': $route.path === '/notifications' }"
          to="/notifications"
        >
          <BellIcon class="size-4" />
          Notificações
        </RouterLink>
        <RouterLink
          v-if="authStore.isAdmin"
          class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium text-slate-600 transition hover:bg-slate-50 hover:text-sky-600"
          :class="{ 'bg-slate-50 text-sky-600': $route.path === '/dashboard' }"
          to="/dashboard"
        >
          <ChartBarIcon class="size-4" />
          Dashboard
        </RouterLink>
        <a
          v-if="authStore.isAdmin"
          class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium text-slate-600 transition hover:bg-slate-50 hover:text-sky-600"
          :href="apiDocsUrl"
          target="_blank"
        >
          <CodeBracketIcon class="size-4" />
          API Docs
        </a>
      </div>
    </nav>

    <main class="layout-container py-8">
      <div class="layout-slot">
        <slot />
      </div>
    </main>
  </div>
</template>
<script setup>
import { BellIcon, ChartBarIcon, CodeBracketIcon, DocumentTextIcon, PlusIcon } from '@heroicons/vue/24/outline'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import NotificationBell from '../components/NotificationBell.vue'
import { useAuthStore } from '../stores/authStore'

const authStore = useAuthStore()
const router = useRouter()
const toast = useToast()

const apiDocsUrl = (import.meta.env.VITE_BACKEND_URL || 'http://localhost:8080') + '/api/documentation'

async function handleLogout() {
  await authStore.signOut()
  toast.success('Sessão encerrada')
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="min-h-screen">
    <header class="text-white brand-bg">
      <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
        <RouterLink to="/travel-orders" class="block cursor-pointer">
          <img src="/branding/logo-onfly-header.png" alt="Onfly logo" class="h-9 w-auto" />
        </RouterLink>
        <div class="flex items-center gap-4">
          <NotificationBell />
          <span class="text-sm">{{ authStore.user?.name }} ({{ authStore.user?.role }})</span>
          <button class="rounded bg-sky-700 px-3 py-1 text-sm hover:bg-sky-800" @click="handleLogout">
            Sair
          </button>
        </div>
      </div>
    </header>

    <nav class="border-b border-slate-200 bg-white shadow-sm">
      <div class="mx-auto flex max-w-6xl gap-1 px-4 py-3 text-sm">
        <RouterLink
          class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium text-slate-600 transition hover:bg-slate-50 hover:text-sky-600"
          :class="{ 'bg-slate-50 text-sky-600': $route.path === '/travel-orders' }"
          to="/travel-orders"
        >
          <DocumentTextIcon class="size-4" />
          Pedidos
        </RouterLink>
        <RouterLink
          class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium text-slate-600 transition hover:bg-slate-50 hover:text-sky-600"
          :class="{ 'bg-slate-50 text-sky-600': $route.path === '/travel-orders/create' }"
          to="/travel-orders/create"
        >
          <PlusIcon class="size-4" />
          Novo Pedido
        </RouterLink>
        <RouterLink
          class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium text-slate-600 transition hover:bg-slate-50 hover:text-sky-600"
          :class="{ 'bg-slate-50 text-sky-600': $route.path === '/notifications' }"
          to="/notifications"
        >
          <BellIcon class="size-4" />
          Notificações
        </RouterLink>
        <RouterLink
          v-if="authStore.isAdmin"
          class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium text-slate-600 transition hover:bg-slate-50 hover:text-sky-600"
          :class="{ 'bg-slate-50 text-sky-600': $route.path === '/dashboard' }"
          to="/dashboard"
        >
          <ChartBarIcon class="size-4" />
          Dashboard
        </RouterLink>
        <a
          v-if="authStore.isAdmin"
          class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium text-slate-600 transition hover:bg-slate-50 hover:text-sky-600"
          :href="apiDocsUrl"
          target="_blank"
        >
          <CodeBracketIcon class="size-4" />
          API Docs
        </a>
      </div>
    </nav>

    <main class="mx-auto max-w-6xl px-4 py-8">
      <slot />
    </main>
  </div>
</template>
