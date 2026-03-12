import api from './httpClient'

function cleanParams(params) {
  return Object.fromEntries(
    Object.entries(params).filter(([, v]) => v !== '' && v !== null && v !== undefined)
  )
}

export async function listNotifications(params = {}) {
  const response = await api.get('/notifications', { params: cleanParams(params) })
  return response.data
}

export async function getUnreadCount() {
  const response = await api.get('/notifications/unread-count')
  return response.data
}

export async function markAsRead(id) {
  await api.post(`/notifications/${id}/read`)
}
