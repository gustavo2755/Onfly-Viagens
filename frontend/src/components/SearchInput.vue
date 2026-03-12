<script setup>
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'

defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div class="relative">
    <MagnifyingGlassIcon
      class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-slate-400"
      aria-hidden="true"
    />
    <input
      :value="modelValue"
      type="text"
      :placeholder="placeholder"
      class="w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
      @input="emit('update:modelValue', $event.target.value)"
    />
  </div>
</template>
