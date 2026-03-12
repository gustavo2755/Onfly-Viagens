import { onUnmounted } from 'vue'

export function usePolling(callback, intervalMs, options = {}) {
  const { whenVisible = true } = options
  let intervalId = null
  let isRunning = false

  function tick() {
    if (whenVisible && document.visibilityState !== 'visible') return
    if (isRunning) return
    isRunning = true
    Promise.resolve(callback())
      .finally(() => {
        isRunning = false
      })
  }

  function start() {
    if (intervalId) return
    intervalId = setInterval(tick, intervalMs)
  }

  function stop() {
    if (intervalId) {
      clearInterval(intervalId)
      intervalId = null
    }
  }

  function handleVisibilityChange() {
    if (document.visibilityState === 'visible') {
      start()
    } else {
      stop()
    }
  }

  start()
  if (whenVisible) {
    document.addEventListener('visibilitychange', handleVisibilityChange)
  }

  onUnmounted(() => {
    stop()
    if (whenVisible) {
      document.removeEventListener('visibilitychange', handleVisibilityChange)
    }
  })

  return { stop }
}
