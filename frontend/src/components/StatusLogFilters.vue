<script setup>
import {
  CalendarDaysIcon,
  DocumentTextIcon,
  TagIcon,
  TrashIcon,
  UserCircleIcon,
} from '@heroicons/vue/24/outline'
import { reactive, watch } from 'vue'
import FilterDateInput from './FilterDateInput.vue'
import FilterField from './FilterField.vue'
import FilterSelect from './FilterSelect.vue'
import UserSearchCombobox from './UserSearchCombobox.vue'
import { useDebounce } from '../composables/useDebounce'

const STATUS_TO_OPTIONS = [
  { value: '', label: 'Todos' },
  { value: 'approved', label: 'Aprovado' },
  { value: 'cancelled', label: 'Cancelado' },
]

const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['update:modelValue', 'search'])

const localFilters = reactive({
  travel_order_id: props.modelValue.travel_order_id ?? '',
  admin_user_id: props.modelValue.admin_user_id ?? '',
  to_status: props.modelValue.to_status ?? '',
  created_from: props.modelValue.created_from ?? '',
  created_to: props.modelValue.created_to ?? '',
})

watch(
  () => props.modelValue,
  (value) => {
    localFilters.travel_order_id = value.travel_order_id ?? ''
    localFilters.admin_user_id = value.admin_user_id ?? ''
    localFilters.to_status = value.to_status ?? ''
    localFilters.created_from = value.created_from ?? ''
    localFilters.created_to = value.created_to ?? ''
  },
  { deep: true }
)

function buildEmitPayload() {
  return {
    ...props.modelValue,
    travel_order_id: localFilters.travel_order_id,
    admin_user_id: localFilters.admin_user_id,
    to_status: localFilters.to_status,
    created_from: localFilters.created_from,
    created_to: localFilters.created_to,
    per_page: props.modelValue.per_page ?? 10,
  }
}

function apply() {
  emit('update:modelValue', buildEmitPayload())
  emit('search')
}

const debouncedApply = useDebounce(apply, 400)

function clearFilters() {
  localFilters.travel_order_id = ''
  localFilters.admin_user_id = ''
  localFilters.to_status = ''
  localFilters.created_from = ''
  localFilters.created_to = ''
  emit('update:modelValue', {
    travel_order_id: '',
    admin_user_id: '',
    to_status: '',
    created_from: '',
    created_to: '',
    per_page: props.modelValue.per_page ?? 10,
  })
  emit('search')
}
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <FilterField label="Pedido #" :icon="DocumentTextIcon">
        <input
          :value="localFilters.travel_order_id"
          type="number"
          min="1"
          placeholder="Ex: 1"
          class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
          @input="localFilters.travel_order_id = $event.target.value; debouncedApply()"
        />
      </FilterField>

      <FilterField label="Admin" :icon="UserCircleIcon">
        <UserSearchCombobox
          :model-value="localFilters.admin_user_id"
          :admin-only="true"
          @update:model-value="(val) => { localFilters.admin_user_id = val; debouncedApply() }"
        />
      </FilterField>

      <FilterField label="Para (status)" :icon="TagIcon">
        <FilterSelect
          :model-value="localFilters.to_status"
          :options="STATUS_TO_OPTIONS"
          @update:model-value="(v) => { localFilters.to_status = v; debouncedApply() }"
        />
      </FilterField>

      <FilterField label="Criado de" :icon="CalendarDaysIcon">
        <FilterDateInput
          :model-value="localFilters.created_from"
          @update:model-value="(v) => { localFilters.created_from = v; debouncedApply() }"
        />
      </FilterField>

      <FilterField label="Criado até" :icon="CalendarDaysIcon">
        <FilterDateInput
          :model-value="localFilters.created_to"
          @update:model-value="(v) => { localFilters.created_to = v; debouncedApply() }"
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
  </div>
</template>
