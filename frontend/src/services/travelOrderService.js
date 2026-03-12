import api from './httpClient'

function cleanParams(params) {
  return Object.fromEntries(
    Object.entries(params).filter(([, v]) => v !== '' && v !== null && v !== undefined)
  )
}

export async function listTravelOrders(params = {}) {
  const response = await api.get('/travel-orders', { params: cleanParams(params) })
  return response.data
}

export async function createTravelOrder(payload) {
  const response = await api.post('/travel-orders', payload)
  return response.data
}

export async function getTravelOrder(id) {
  const response = await api.get(`/travel-orders/${id}`)
  return response.data
}

export async function updateTravelOrderStatus(id, status) {
  const response = await api.patch(`/travel-orders/${id}/status`, { status })
  return response.data
}

export async function getDashboard() {
  const response = await api.get('/travel-orders/dashboard')
  return response.data
}

export async function listStatusLogs(params = {}) {
  const response = await api.get('/travel-orders/status-logs', { params })
  return response.data
}
