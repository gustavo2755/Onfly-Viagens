import { defineStore } from 'pinia'
import {
  createTravelOrder,
  getDashboard,
  getTravelOrder,
  listStatusLogs,
  listTravelOrders,
  updateTravelOrderStatus,
} from '../services/travelOrderService'

export const useTravelOrderStore = defineStore('travelOrders', {
  state: () => ({
    items: [],
    meta: null,
    links: null,
    selected: null,
    dashboard: null,
    statusLogs: [],
    loading: false,
  }),
  actions: {
    async fetchList(params = {}) {
      this.loading = true
      try {
        const data = await listTravelOrders(params)
        this.items = data.data
        this.meta = data.meta || null
        this.links = data.links || null
        return data
      } finally {
        this.loading = false
      }
    },
    async fetchOne(id) {
      const data = await getTravelOrder(id)
      this.selected = data.data
      return data
    },
    async create(payload) {
      return createTravelOrder(payload)
    },
    async changeStatus(id, status) {
      return updateTravelOrderStatus(id, status)
    },
    async fetchDashboard() {
      const data = await getDashboard()
      this.dashboard = data.data
      return data
    },
    async fetchStatusLogs(params = {}) {
      const data = await listStatusLogs(params)
      this.statusLogs = data.data
      return data
    },
  },
})
