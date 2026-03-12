import { defineStore } from 'pinia'
import { login, logout, me } from '../services/authService'
import { useNotificationStore } from './notificationStore'
import { useTravelOrderStore } from './travelOrderStore'

const TOKEN_KEY = 'travel_orders_token'
const USER_KEY = 'travel_orders_user'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem(TOKEN_KEY) || '',
    user: JSON.parse(localStorage.getItem(USER_KEY) || 'null'),
    loading: false,
  }),
  getters: {
    isAuthenticated: (state) => Boolean(state.token),
    isAdmin: (state) => state.user?.role === 'admin',
  },
  actions: {
    resetDomainStores() {
      const travelOrderStore = useTravelOrderStore(this.$pinia)
      const notificationStore = useNotificationStore(this.$pinia)
      travelOrderStore.$reset()
      notificationStore.$reset()
    },
    clearSessionState() {
      this.resetDomainStores()
      this.token = ''
      this.user = null
      this.persist()
    },
    persist() {
      if (this.token) {
        localStorage.setItem(TOKEN_KEY, this.token)
      } else {
        localStorage.removeItem(TOKEN_KEY)
      }

      if (this.user) {
        localStorage.setItem(USER_KEY, JSON.stringify(this.user))
      } else {
        localStorage.removeItem(USER_KEY)
      }
    },
    async signIn(payload) {
      this.loading = true
      try {
        this.resetDomainStores()
        const data = await login(payload)
        this.token = data.data.token
        this.user = data.data.user
        this.persist()
        return data
      } finally {
        this.loading = false
      }
    },
    async fetchMe() {
      if (!this.token) {
        return null
      }

      const data = await me()
      this.user = data.data
      this.persist()
      return data
    },
    async signOut() {
      try {
        if (this.token) {
          await logout()
        }
      } finally {
        this.clearSessionState()
      }
    },
  },
})
