<script setup>
const props = defineProps({
  status: {
    type: String,
    required: true,
  },
})

const STATUS_LABELS = {
  requested: 'Solicitado',
  approved: 'Aprovado',
  cancelled: 'Cancelado',
}

const styles = {
  requested: 'bg-amber-100 text-amber-800',
  approved: 'bg-emerald-100 text-emerald-800',
  cancelled: 'bg-rose-100 text-rose-800',
}

const label = STATUS_LABELS[props.status] || props.status
</script>

<template>
  <span class="rounded-full px-2 py-1 text-xs font-semibold" :class="styles[props.status] || 'bg-slate-100 text-slate-700'">
    {{ label }}
  </span>
</template>
