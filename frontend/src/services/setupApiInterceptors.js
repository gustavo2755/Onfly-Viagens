import api from './httpClient'

let sessionExpiredHandled = false

export function resetSessionExpiredHandled() {
  sessionExpiredHandled = false
}

export function setupApiInterceptors({ authStore, router, toast }) {
  api.interceptors.response.use(
    (response) => response,
    (error) => {
      const status = error?.response?.status
      const isLoginRequest = error?.config?.url?.includes('/auth/login')
      if (status === 401 && !isLoginRequest && !sessionExpiredHandled) {
        sessionExpiredHandled = true
        authStore.clearSessionState()
        toast.warning('Sessão expirada. Faça login novamente.')
        router.push({ name: 'login' })
      }
      return Promise.reject(error)
    }
  )
}
