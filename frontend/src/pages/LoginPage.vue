<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { getErrorMessage } from '../utils/errorMessage'
import { useAuthStore } from '../stores/authStore'

const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: '',
})

async function submit() {
  try {
    await authStore.signIn(form)
    toast.success('Login realizado')
    router.push({ name: 'travel-orders' })
  } catch (error) {
    toast.error(getErrorMessage(error))
  }
}
</script>

<template>
  <div class="grid min-h-screen grid-cols-1 bg-white md:grid-cols-2">
    <aside class="hidden h-full md:block">
      <img
        src="/branding/login-cover.png"
        alt="Login cover"
        class="h-screen w-full object-cover"
      />
    </aside>

    <section class="flex min-h-screen items-center justify-center px-6 py-10">
      <form class="w-full max-w-md space-y-5" @submit.prevent="submit">
        <div class="mb-10 flex justify-center">
          <img
            src="/branding/logo-transparent.png"
            alt="Onfly logo"
            class="h-20 w-auto"
          />
        </div>

        <div>
          <label class="mb-1 block text-sm text-slate-600">Qual o seu e-mail?</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full rounded border border-slate-300 px-3 py-2.5 outline-none focus:border-sky-500"
          />
        </div>

        <div>
          <label class="mb-1 block text-sm text-slate-600">Agora sua senha</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full rounded border border-slate-300 px-3 py-2.5 outline-none focus:border-sky-500"
          />
        </div>

        <button
          :disabled="authStore.loading"
          class="w-full rounded px-4 py-2.5 text-white transition brand-bg hover:bg-sky-600 disabled:opacity-60"
        >
          Vamos!
        </button>
      </form>
    </section>
  </div>
</template>
