<script setup>
import { CheckCircleIcon, ClockIcon, XCircleIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
  status: {
    type: String,
    required: true,
  },
})

const STATUS_CONFIG = {
  requested: {
    label: 'Solicitado',
    icon: ClockIcon,
    classes: 'bg-amber-50 text-amber-800 border-amber-200',
    iconClasses: 'text-amber-600',
  },
  approved: {
    label: 'Aprovado',
    icon: CheckCircleIcon,
    classes: 'bg-emerald-50 text-emerald-800 border-emerald-200',
    iconClasses: 'text-emerald-600',
  },
  cancelled: {
    label: 'Cancelado',
    icon: XCircleIcon,
    classes: 'bg-rose-50 text-rose-800 border-rose-200',
    iconClasses: 'text-rose-600',
  },
}

const config = STATUS_CONFIG[props.status] || {
  label: props.status,
  icon: ClockIcon,
  classes: 'bg-slate-50 text-slate-700 border-slate-200',
  iconClasses: 'text-slate-500',
}
</script>

<template>
  <span
    class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium shadow-sm"
    :class="config.classes"
  >
    <component :is="config.icon" class="size-3.5 shrink-0" :class="config.iconClasses" />
    {{ config.label }}
  </span>
</template>
