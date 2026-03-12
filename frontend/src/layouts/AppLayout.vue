<script setup>
import {
  Bars3Icon,
  BellIcon,
  ChartBarIcon,
  CodeBracketIcon,
  DocumentTextIcon,
  PlusIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import NotificationBell from '../components/NotificationBell.vue'
import { useAuthStore } from '../stores/authStore'

const authStore = useAuthStore()
const router = useRouter()
const toast = useToast()
const menuOpen = ref(false)

const apiDocsUrl = (import.meta.env.VITE_BACKEND_URL || 'http://localhost:8080') + '/api/documentation'

const navLinkClass =
  'flex items-center gap-2 rounded-lg px-3 py-2 font-medium transition'
const navLinkBase = 'text-slate-600 hover:bg-sky-100 hover:text-sky-700'
const navLinkActive = 'bg-sky-100 text-sky-700 ring-1 ring-sky-200'

function isPedidosActive(path) {
  return path === '/travel-orders' || (path.startsWith('/travel-orders/') && path !== '/travel-orders/create')
}

async function handleLogout() {
  await authStore.signOut()
  toast.success('Sessão encerrada')
  router.push({ name: 'login' })
}

function closeMenu() {
  menuOpen.value = false
}

watch(
  () => router.currentRoute.value.path,
  () => closeMenu()
)
</script>

<template>
  <div class="min-h-screen overflow-x-hidden">
    <header class="text-white brand-bg">
      <div class="layout-container flex items-center justify-between py-3">
        <RouterLink to="/travel-orders" class="block cursor-pointer" @click="closeMenu">
          <img src="/branding/logo-onfly-header.png" alt="Onfly logo" class="h-9 w-auto" />
        </RouterLink>

        <div class="hidden items-center gap-4 md:flex">
          <NotificationBell />
          <span class="text-sm">{{ authStore.user?.name }} ({{ authStore.user?.role }})</span>
          <button class="rounded bg-sky-700 px-3 py-1 text-sm hover:bg-sky-800" @click="handleLogout">
            Sair
          </button>
        </div>

        <button
          type="button"
          class="rounded p-2 text-white/90 transition hover:bg-sky-700 hover:text-white md:hidden"
          aria-label="Abrir menu"
          @click="menuOpen = true"
        >
          <Bars3Icon class="size-6" />
        </button>
      </div>
    </header>

    <nav class="hidden border-b border-slate-200 bg-white shadow-sm md:block">
      <div class="layout-container flex gap-1 py-3 text-sm">
        <RouterLink
          :class="[navLinkClass, navLinkBase, isPedidosActive($route.path) && navLinkActive]"
          to="/travel-orders"
        >
          <DocumentTextIcon class="size-4" />
          Pedidos
        </RouterLink>
        <RouterLink
          :class="[navLinkClass, navLinkBase, $route.path === '/travel-orders/create' && navLinkActive]"
          to="/travel-orders/create"
        >
          <PlusIcon class="size-4" />
          Novo Pedido
        </RouterLink>
        <RouterLink
          :class="[navLinkClass, navLinkBase, $route.path === '/notifications' && navLinkActive]"
          to="/notifications"
        >
          <BellIcon class="size-4" />
          Notificações
        </RouterLink>
        <RouterLink
          v-if="authStore.isAdmin"
          :class="[navLinkClass, navLinkBase, $route.path === '/dashboard' && navLinkActive]"
          to="/dashboard"
        >
          <ChartBarIcon class="size-4" />
          Dashboard
        </RouterLink>
        <a
          v-if="authStore.isAdmin"
          :class="[navLinkClass, navLinkBase]"
          :href="apiDocsUrl"
          target="_blank"
        >
          <CodeBracketIcon class="size-4" />
          API Docs
        </a>
      </div>
    </nav>

    <Teleport to="body">
      <Transition name="menu">
        <div
          v-if="menuOpen"
          class="fixed inset-0 z-[200] md:hidden"
          aria-modal="true"
          role="dialog"
        >
          <div
            class="absolute inset-0 bg-black/40"
            aria-hidden="true"
            @click="closeMenu"
          />
          <div
            class="menu-panel absolute right-0 top-0 flex h-full w-72 max-w-[85vw] flex-col bg-white shadow-xl"
            @click.stop
          >
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
              <span class="font-medium text-slate-800">Menu</span>
              <button
                type="button"
                class="rounded p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                aria-label="Fechar menu"
                @click="closeMenu"
              >
                <XMarkIcon class="size-5" />
              </button>
            </div>
            <div class="flex flex-1 flex-col gap-1 overflow-y-auto p-3">
              <div class="mb-2 flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                  <p class="text-sm font-medium text-slate-800">{{ authStore.user?.name }}</p>
                  <p class="text-xs text-slate-500">{{ authStore.user?.role }}</p>
                </div>
                <NotificationBell />
              </div>
              <RouterLink
                :class="[navLinkClass, navLinkBase, isPedidosActive($route.path) && navLinkActive]"
                to="/travel-orders"
                @click="closeMenu"
              >
                <DocumentTextIcon class="size-4" />
                Pedidos
              </RouterLink>
              <RouterLink
                :class="[navLinkClass, navLinkBase, $route.path === '/travel-orders/create' && navLinkActive]"
                to="/travel-orders/create"
                @click="closeMenu"
              >
                <PlusIcon class="size-4" />
                Novo Pedido
              </RouterLink>
              <RouterLink
                :class="[navLinkClass, navLinkBase, $route.path === '/notifications' && navLinkActive]"
                to="/notifications"
                @click="closeMenu"
              >
                <BellIcon class="size-4" />
                Notificações
              </RouterLink>
              <RouterLink
                v-if="authStore.isAdmin"
                :class="[navLinkClass, navLinkBase, $route.path === '/dashboard' && navLinkActive]"
                to="/dashboard"
                @click="closeMenu"
              >
                <ChartBarIcon class="size-4" />
                Dashboard
              </RouterLink>
              <a
                v-if="authStore.isAdmin"
                :class="[navLinkClass, navLinkBase]"
                :href="apiDocsUrl"
                target="_blank"
                @click="closeMenu"
              >
                <CodeBracketIcon class="size-4" />
                API Docs
              </a>
            </div>
            <div class="border-t border-slate-200 p-3">
              <button
                type="button"
                class="w-full rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700"
                @click="handleLogout"
              >
                Sair
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <main class="layout-container py-8">
      <div class="layout-slot">
        <slot />
      </div>
    </main>
  </div>
</template>

<style scoped>
.menu-enter-active,
.menu-leave-active {
  transition: opacity 0.2s ease;
}
.menu-enter-active .menu-panel,
.menu-leave-active .menu-panel {
  transition: transform 0.25s ease;
}
.menu-enter-from,
.menu-leave-to {
  opacity: 0;
}
.menu-enter-from .menu-panel,
.menu-leave-to .menu-panel {
  transform: translateX(100%);
}
</style>
