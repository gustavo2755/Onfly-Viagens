<script setup>
import { ChevronDownIcon, ChevronUpIcon, FunnelIcon } from '@heroicons/vue/24/outline'
import { onMounted, ref } from 'vue'

const expanded = ref(true)

onMounted(() => {
  expanded.value = window.matchMedia('(min-width: 768px)').matches
})

function toggle() {
  expanded.value = !expanded.value
}
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
    <button
      type="button"
      class="flex w-full items-center justify-between gap-2 px-5 py-4 text-left transition hover:bg-slate-50/50"
      @click="toggle"
    >
      <span class="flex items-center gap-2 font-medium text-slate-700">
        <FunnelIcon class="size-5 text-slate-500" />
        Filtros
      </span>
      <component
        :is="expanded ? ChevronUpIcon : ChevronDownIcon"
        class="size-5 shrink-0 text-slate-500"
      />
    </button>
    <div v-show="expanded" class="border-t border-slate-100 p-5">
      <slot />
    </div>
  </div>
</template>
