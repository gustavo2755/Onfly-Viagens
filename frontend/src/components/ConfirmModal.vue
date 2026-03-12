<script setup>
defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Confirmar acao',
  },
  description: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['cancel', 'confirm'])
</script>

<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
    <div class="w-full max-w-md rounded bg-white p-6">
      <h3 class="text-lg font-semibold">{{ title }}</h3>
      <p class="mt-2 text-sm text-slate-600">{{ description }}</p>
      <div class="mt-5 flex justify-end gap-2">
        <button class="rounded border px-4 py-2" @click="emit('cancel')">Cancelar</button>
        <button class="rounded bg-slate-900 px-4 py-2 text-white" @click="emit('confirm')">Confirmar</button>
      </div>
    </div>
  </div>
</template>
