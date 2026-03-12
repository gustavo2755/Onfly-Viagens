<script setup>
import { BellIcon } from '@heroicons/vue/24/outline'
import { Menu, MenuButton, MenuItems } from '@headlessui/vue'
import { onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import StatusBadge from './StatusBadge.vue'
import { useNotificationStore } from '../stores/notificationStore'

const router = useRouter()
const notificationStore = useNotificationStore()

async function loadUnread() {
  await notificationStore.fetchUnreadList()
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

async function handleMarkAsRead(notification) {
  await notificationStore.markAsRead(notification.id)
}

function goToAll() {
  router.push({ name: 'notifications' })
}

onMounted(() => {
  notificationStore.fetchUnreadCount()
})

watch(
  () => router.currentRoute.value.path,
  () => {
    notificationStore.fetchUnreadCount()
  }
)
</script>

<template>
  <Menu as="div" class="relative">
    <MenuButton
      class="relative rounded p-1.5 text-white/90 transition hover:bg-sky-700 hover:text-white"
      @click.stop="loadUnread"
    >
      <BellIcon class="size-5" />
      <span
        v-if="notificationStore.unreadCount > 0"
        class="absolute -right-0.5 -top-0.5 flex size-5 items-center justify-center rounded-full bg-rose-500 text-xs font-bold text-white"
      >
        {{ notificationStore.unreadCount > 9 ? '9+' : notificationStore.unreadCount }}
      </span>
    </MenuButton>
    <MenuItems
      class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-lg border border-slate-200 bg-white py-1 shadow-lg focus:outline-none"
      @click.stop
    >
      <div class="max-h-96 overflow-y-auto">
        <template v-if="notificationStore.unreadItems.length > 0">
          <div
            v-for="n in notificationStore.unreadItems"
            :key="n.id"
            class="cursor-pointer px-4 py-3 text-left text-sm transition hover:bg-slate-50"
            @click.stop="handleMarkAsRead(n)"
          >
            <p class="font-medium text-slate-900">{{ n.data?.message }}</p>
            <div class="mt-1 flex items-center gap-2">
              <StatusBadge :status="n.data?.from_status" />
              <span class="text-slate-400">→</span>
              <StatusBadge :status="n.data?.to_status" />
            </div>
            <p class="mt-1 text-xs text-slate-500">{{ formatDate(n.created_at) }}</p>
          </div>
        </template>
        <div v-else class="px-4 py-6 text-center text-sm text-slate-500">
          Nenhuma notificação nova
        </div>
      </div>
      <div class="border-t border-slate-100 px-2 py-2">
        <button
          type="button"
          class="w-full rounded px-3 py-2 text-center text-sm font-medium text-sky-600 transition hover:bg-sky-50"
          @click.stop="goToAll"
        >
          Ver todas
        </button>
      </div>
    </MenuItems>
  </Menu>
</template>
