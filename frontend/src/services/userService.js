import api from './httpClient'

export async function listUsers() {
  const response = await api.get('/users')
  return response.data
}

export async function searchUsers(search = '') {
  const response = await api.get('/users', {
    params: { search: search.trim() || undefined },
  })
  return response.data
}
