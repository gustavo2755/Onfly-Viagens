<script setup>
const props = defineProps({
  meta: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['change'])

function go(page) {
  if (!page || page < 1 || page > props.meta.last_page || page === props.meta.current_page) {
    return
  }
  emit('change', page)
}
</script>

<template>
  <div v-if="props.meta" class="mt-4 flex items-center justify-between text-sm">
    <p class="text-slate-600">
      Pagina {{ props.meta.current_page }} de {{ props.meta.last_page }} ({{ props.meta.total }} itens)
    </p>
    <div class="flex gap-2">
      <button class="rounded border px-3 py-1 disabled:opacity-50" :disabled="props.meta.current_page <= 1" @click="go(props.meta.current_page - 1)">
        Anterior
      </button>
      <button class="rounded border px-3 py-1 disabled:opacity-50" :disabled="props.meta.current_page >= props.meta.last_page" @click="go(props.meta.current_page + 1)">
        Proxima
      </button>
    </div>
  </div>
</template>
