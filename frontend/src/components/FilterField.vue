<script setup>
defineProps({
  label: {
    type: String,
    required: true,
  },
  icon: {
    type: Object,
    default: null,
  },
})
</script>

<template>
  <div>
    <label class="mb-1.5 flex items-center gap-1.5 text-xs font-medium text-slate-500">
      <component v-if="icon" :is="icon" class="size-3.5 shrink-0" />
      {{ label }}
    </label>
    <slot />
  </div>
</template>
