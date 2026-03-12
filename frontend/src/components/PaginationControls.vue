<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'
import { computed } from 'vue'

const props = defineProps({
  meta: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['change'])

const rangeText = computed(() => {
  if (!props.meta) return ''
  const { from, to, total, current_page, per_page } = props.meta
  if (total === 0) return 'Nenhum item'
  const fromVal = from ?? (current_page - 1) * per_page + 1
  const toVal = to ?? Math.min(current_page * per_page, total)
  if (total <= per_page) return `${total} ${total === 1 ? 'item' : 'itens'}`
  return `Mostrando ${fromVal} a ${toVal} de ${total}`
})

const pageNumbers = computed(() => {
  if (!props.meta || props.meta.last_page <= 1) return []
  const current = props.meta.current_page
  const last = props.meta.last_page

  if (last <= 5) {
    return Array.from({ length: last }, (_, i) => i + 1)
  }

  const set = new Set([1, current, last])
  const start = Math.max(2, current - 1)
  const end = Math.min(last - 1, current + 1)
  for (let i = start; i <= end; i++) set.add(i)

  const sorted = [...set].sort((a, b) => a - b)
  const result = []
  for (let i = 0; i < sorted.length; i++) {
    if (i > 0 && sorted[i] - sorted[i - 1] > 1) result.push('...')
    result.push(sorted[i])
  }
  return result
})

function go(page) {
  if (!page || page < 1 || page > props.meta.last_page || page === props.meta.current_page) {
    return
  }
  emit('change', page)
}
</script>

<template>
  <div
    v-if="props.meta"
    class="mt-6 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-sm"
  >
    <p class="text-sm text-slate-600">
      <span class="font-medium text-slate-900">{{ rangeText }}</span>
      <span v-if="props.meta.last_page > 1" class="ml-2 text-slate-500">
        · Página {{ props.meta.current_page }} de {{ props.meta.last_page }}
      </span>
    </p>

    <div class="flex items-center gap-1">
      <button
        class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 text-sm font-medium transition disabled:cursor-not-allowed disabled:opacity-40"
        :class="
          props.meta.current_page <= 1
            ? 'border-slate-200 bg-slate-50 text-slate-400'
            : 'border-slate-200 bg-white text-slate-700 hover:border-sky-300 hover:bg-sky-50 hover:text-sky-700'
        "
        :disabled="props.meta.current_page <= 1"
        :aria-label="'Página anterior'"
        @click="go(props.meta.current_page - 1)"
      >
        <ChevronLeftIcon class="size-4" />
        <span class="hidden sm:inline">Anterior</span>
      </button>

      <template v-for="(page, idx) in pageNumbers" :key="idx">
        <button
          v-if="page !== '...'"
          class="min-w-[2.25rem] rounded-lg px-3 py-2 text-sm font-medium transition"
          :class="
            page === props.meta.current_page
              ? 'bg-sky-600 text-white shadow-sm'
              : 'border border-slate-200 bg-white text-slate-700 hover:border-sky-300 hover:bg-sky-50 hover:text-sky-700'
          "
          @click="go(page)"
        >
          {{ page }}
        </button>
        <span v-else class="px-2 py-2 text-slate-400">…</span>
      </template>

      <button
        class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 text-sm font-medium transition disabled:cursor-not-allowed disabled:opacity-40"
        :class="
          props.meta.current_page >= props.meta.last_page
            ? 'border-slate-200 bg-slate-50 text-slate-400'
            : 'border-slate-200 bg-white text-slate-700 hover:border-sky-300 hover:bg-sky-50 hover:text-sky-700'
        "
        :disabled="props.meta.current_page >= props.meta.last_page"
        :aria-label="'Próxima página'"
        @click="go(props.meta.current_page + 1)"
      >
        <span class="hidden sm:inline">Próxima</span>
        <ChevronRightIcon class="size-4" />
      </button>
    </div>
  </div>
</template>
