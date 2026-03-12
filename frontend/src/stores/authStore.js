import { defineStore } from 'pinia'
import { login, logout, me } from '../services/authService'

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
        this.token = ''
        this.user = null
        this.persist()
      }
    },
  },
})
