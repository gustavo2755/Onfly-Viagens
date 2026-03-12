import api from './httpClient'

export async function listUsers() {
  const response = await api.get('/users')
  return response.data
}
