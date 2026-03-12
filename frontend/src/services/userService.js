import api from './httpClient'

export async function listUsers() {
  const response = await api.get('/users')
  return response.data
}

export async function searchUsers(search = '', adminOnly = false) {
  const params = { search: search.trim() || undefined }
  if (adminOnly) params.admin_only = true
  const response = await api.get('/users', { params })
  return response.data
}
