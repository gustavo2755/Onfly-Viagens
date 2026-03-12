import { defineStore } from 'pinia'
import { getUnreadCount, listNotifications, markAsRead } from '../services/notificationService'

export const useNotificationStore = defineStore('notifications', {
  state: () => ({
    unreadItems: [],
    items: [],
    meta: null,
    links: null,
    unreadCount: 0,
    loading: false,
  }),
  actions: {
    async fetchUnreadList() {
      const data = await listNotifications({ unread_only: 1, per_page: 10 })
      this.unreadItems = data.data
      return data
    },
    async fetchList(params = {}, options = {}) {
      const silent = options.silent === true
      if (!silent) this.loading = true
      try {
        const data = await listNotifications(params)
        this.items = data.data
        this.meta = data.meta || null
        this.links = data.links || null
        return data
      } finally {
        if (!silent) this.loading = false
      }
    },
    async fetchUnreadCount() {
      const data = await getUnreadCount()
      this.unreadCount = data.data.count
      return data
    },
    async markAsRead(id) {
      await markAsRead(id)
      this.unreadCount = Math.max(0, this.unreadCount - 1)
      this.unreadItems = this.unreadItems.filter((n) => n.id !== id)
      const idx = this.items.findIndex((n) => n.id === id)
      if (idx >= 0) {
        this.items = [
          ...this.items.slice(0, idx),
          { ...this.items[idx], read_at: new Date().toISOString() },
          ...this.items.slice(idx + 1),
        ]
      }
    },
  },
})
