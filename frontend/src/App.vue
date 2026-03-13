<script setup>
import { onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useAuthStore } from './stores/authStore'
import {
  setupApiInterceptors,
  resetSessionExpiredHandled,
} from './services/setupApiInterceptors'

const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()

onMounted(() => {
  setupApiInterceptors({ authStore, router, toast })
})

watch(
  () => authStore.token,
  (token) => {
    if (token) resetSessionExpiredHandled()
  }
)
</script>

<template>
  <RouterView />
</template>
