<script setup>
import { reactive, watch } from 'vue'
import { useDebounce } from '../composables/useDebounce'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
  },
  users: {
    type: Array,
    default: () => [],
  },
  isAdmin: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue', 'search'])

const STATUS_LABELS = {
  requested: 'Solicitado',
  approved: 'Aprovado',
  cancelled: 'Cancelado',
}

const localFilters = reactive({
  status: props.modelValue.status || '',
  destination: props.modelValue.destination || '',
  user_id: props.modelValue.user_id || '',
  departure_from: props.modelValue.departure_from || '',
  departure_to: props.modelValue.departure_to || '',
})

watch(
  () => props.modelValue,
  (value) => {
    localFilters.status = value.status || ''
    localFilters.destination = value.destination || ''
    localFilters.user_id = value.user_id || ''
    localFilters.departure_from = value.departure_from || ''
    localFilters.departure_to = value.departure_to || ''
  },
  { deep: true }
)

function buildEmitPayload() {
  return {
    ...props.modelValue,
    status: localFilters.status,
    destination: localFilters.destination,
    user_id: localFilters.user_id,
    departure_from: localFilters.departure_from,
    departure_to: localFilters.departure_to,
    page: 1,
  }
}

function apply() {
  emit('update:modelValue', buildEmitPayload())
  emit('search')
}

const debouncedApply = useDebounce(apply, 400)

function clearFilters() {
  localFilters.status = ''
  localFilters.destination = ''
  localFilters.user_id = ''
  localFilters.departure_from = ''
  localFilters.departure_to = ''
  emit('update:modelValue', {
    status: '',
    destination: '',
    user_id: '',
    departure_from: '',
    departure_to: '',
    page: 1,
    per_page: props.modelValue.per_page || 10,
  })
  emit('search')
}
</script>

<template>
  <div class="grid gap-3 rounded bg-white p-4 shadow sm:grid-cols-2 lg:grid-cols-4">
    <select v-model="localFilters.status" class="rounded border px-3 py-2 text-sm" @change="debouncedApply">
      <option value="">Todos status</option>
      <option value="requested">{{ STATUS_LABELS.requested }}</option>
      <option value="approved">{{ STATUS_LABELS.approved }}</option>
      <option value="cancelled">{{ STATUS_LABELS.cancelled }}</option>
    </select>

    <select
      v-if="isAdmin"
      v-model="localFilters.user_id"
      class="rounded border px-3 py-2 text-sm"
      @change="debouncedApply"
    >
      <option value="">Todos usuarios</option>
      <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }} ({{ u.email }})</option>
    </select>

    <input
      v-model="localFilters.destination"
      type="text"
      placeholder="Destino"
      class="rounded border px-3 py-2 text-sm"
      @input="debouncedApply"
    />

    <div class="flex gap-2">
      <button class="rounded border px-3 py-2 text-sm" @click="clearFilters">Limpar</button>
    </div>

    <div class="sm:col-span-2 lg:col-span-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
      <div>
        <label class="mb-1 block text-xs text-slate-500">Data de partida (de)</label>
        <input
          v-model="localFilters.departure_from"
          type="date"
          class="w-full rounded border px-3 py-2 text-sm"
          @change="debouncedApply"
        />
      </div>
      <div>
        <label class="mb-1 block text-xs text-slate-500">Data de partida (ate)</label>
        <input
          v-model="localFilters.departure_to"
          type="date"
          class="w-full rounded border px-3 py-2 text-sm"
          @change="debouncedApply"
        />
      </div>
    </div>
  </div>
</template>
