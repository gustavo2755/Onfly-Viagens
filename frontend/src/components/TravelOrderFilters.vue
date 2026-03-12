<script setup>
import {
  CalendarDaysIcon,
  MapPinIcon,
  TagIcon,
  TrashIcon,
  UserCircleIcon,
  UserIcon,
} from '@heroicons/vue/24/outline'
import { reactive, watch } from 'vue'
import CollapsibleFilters from './CollapsibleFilters.vue'
import FilterDateInput from './FilterDateInput.vue'
import FilterField from './FilterField.vue'
import FilterSelect from './FilterSelect.vue'
import SearchInput from './SearchInput.vue'
import UserSearchCombobox from './UserSearchCombobox.vue'
import { useDebounce } from '../composables/useDebounce'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
  },
  isAdmin: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue', 'search'])

const STATUS_OPTIONS = [
  { value: '', label: 'Todos status' },
  { value: 'requested', label: 'Solicitado' },
  { value: 'approved', label: 'Aprovado' },
  { value: 'cancelled', label: 'Cancelado' },
]

const localFilters = reactive({
  status: props.modelValue.status || '',
  destination: props.modelValue.destination || '',
  requester_name: props.modelValue.requester_name || '',
  user_id: props.modelValue.user_id || '',
  departure_from: props.modelValue.departure_from || '',
  departure_to: props.modelValue.departure_to || '',
  created_from: props.modelValue.created_from || '',
  created_to: props.modelValue.created_to || '',
})

watch(
  () => props.modelValue,
  (value) => {
    localFilters.status = value.status || ''
    localFilters.destination = value.destination || ''
    localFilters.requester_name = value.requester_name || ''
    localFilters.user_id = value.user_id || ''
    localFilters.departure_from = value.departure_from || ''
    localFilters.departure_to = value.departure_to || ''
    localFilters.created_from = value.created_from || ''
    localFilters.created_to = value.created_to || ''
  },
  { deep: true }
)

function buildEmitPayload() {
  return {
    ...props.modelValue,
    status: localFilters.status,
    destination: localFilters.destination,
    requester_name: localFilters.requester_name,
    user_id: localFilters.user_id,
    departure_from: localFilters.departure_from,
    departure_to: localFilters.departure_to,
    created_from: localFilters.created_from,
    created_to: localFilters.created_to,
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
  localFilters.requester_name = ''
  localFilters.user_id = ''
  localFilters.departure_from = ''
  localFilters.departure_to = ''
  localFilters.created_from = ''
  localFilters.created_to = ''
  emit('update:modelValue', {
    status: '',
    destination: '',
    requester_name: '',
    user_id: '',
    departure_from: '',
    departure_to: '',
    created_from: '',
    created_to: '',
    page: 1,
    per_page: props.modelValue.per_page || 10,
  })
  emit('search')
}
</script>

<template>
  <CollapsibleFilters>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
      <FilterField label="Status" :icon="TagIcon">
        <FilterSelect
          :model-value="localFilters.status"
          :options="STATUS_OPTIONS"
          @update:model-value="(v) => { localFilters.status = v; debouncedApply() }"
        />
      </FilterField>

      <FilterField v-if="isAdmin" label="Usuário" :icon="UserIcon">
        <UserSearchCombobox
          :model-value="localFilters.user_id"
          @update:model-value="(val) => { localFilters.user_id = val; debouncedApply() }"
        />
      </FilterField>

      <FilterField label="Solicitante" :icon="UserCircleIcon">
        <SearchInput
          :model-value="localFilters.requester_name"
          placeholder="Ex: João Silva"
          @update:model-value="(v) => { localFilters.requester_name = v; debouncedApply() }"
        />
      </FilterField>

      <FilterField label="Destino" :icon="MapPinIcon">
        <SearchInput
          :model-value="localFilters.destination"
          placeholder="Ex: Lisboa"
          @update:model-value="(v) => { localFilters.destination = v; debouncedApply() }"
        />
      </FilterField>

      <div class="flex items-end gap-2">
        <button
          class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 shadow-sm transition hover:border-slate-300 hover:bg-slate-50"
          @click="clearFilters"
        >
          <TrashIcon class="size-4" />
          Limpar
        </button>
      </div>
    </div>

    <div class="mt-4 grid gap-4 border-t border-slate-100 pt-4 sm:grid-cols-2 lg:grid-cols-4">
      <FilterField label="Data de partida (de)" :icon="CalendarDaysIcon">
        <FilterDateInput
          :model-value="localFilters.departure_from"
          @update:model-value="(v) => { localFilters.departure_from = v; debouncedApply() }"
        />
      </FilterField>
      <FilterField label="Data de partida (até)" :icon="CalendarDaysIcon">
        <FilterDateInput
          :model-value="localFilters.departure_to"
          @update:model-value="(v) => { localFilters.departure_to = v; debouncedApply() }"
        />
      </FilterField>
      <FilterField label="Pedido criado de" :icon="CalendarDaysIcon">
        <FilterDateInput
          :model-value="localFilters.created_from"
          @update:model-value="(v) => { localFilters.created_from = v; debouncedApply() }"
        />
      </FilterField>
      <FilterField label="Pedido criado até" :icon="CalendarDaysIcon">
        <FilterDateInput
          :model-value="localFilters.created_to"
          @update:model-value="(v) => { localFilters.created_to = v; debouncedApply() }"
        />
      </FilterField>
    </div>
  </CollapsibleFilters>
</template>
