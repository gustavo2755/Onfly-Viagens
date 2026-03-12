<script setup>
import { computed, ref, watch } from 'vue'
import { MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/20/solid'
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption } from '@headlessui/vue'
import { searchUsers } from '../services/userService'
import { useDebounce } from '../composables/useDebounce'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
})

const emit = defineEmits(['update:modelValue'])

const query = ref('')
const options = ref([])
const loading = ref(false)
const selectedUser = ref(null)

watch(
  () => props.modelValue,
  (val) => {
    if (!val) selectedUser.value = null
  },
  { immediate: true }
)

const displayValue = computed(() => {
  if (query.value) return query.value
  return selectedUser.value ? `${selectedUser.value.name} (${selectedUser.value.email})` : ''
})

async function fetchUsers(term) {
  loading.value = true
  try {
    const data = await searchUsers(term)
    options.value = data.data || []
  } catch {
    options.value = []
  } finally {
    loading.value = false
  }
}

const debouncedFetch = useDebounce((term) => fetchUsers(term), 300)

watch(query, (val) => {
  debouncedFetch(val)
})

function onSelect(user) {
  if (user) {
    selectedUser.value = user
    emit('update:modelValue', String(user.id))
    query.value = ''
  }
}

function onInput(e) {
  query.value = e.target.value
  if (!query.value) selectedUser.value = null
}

function clearSelection() {
  selectedUser.value = null
  query.value = ''
  emit('update:modelValue', '')
}

function onFocus() {
  if (options.value.length === 0 && !loading.value) {
    fetchUsers('')
  }
}
</script>

<template>
  <Combobox :model-value="selectedUser" @update:model-value="onSelect">
    <div class="relative">
      <div class="relative">
        <MagnifyingGlassIcon
          class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-slate-400"
          aria-hidden="true"
        />
        <ComboboxInput
          :display-value="() => displayValue"
          class="w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-10 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
          placeholder="Buscar usuário..."
          @change="onInput"
          @focus="onFocus"
        />
        <button
          v-if="modelValue"
          type="button"
          class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
          @click.stop="clearSelection"
        >
          <XMarkIcon class="size-5" />
        </button>
      </div>

      <ComboboxOptions
        class="absolute z-20 mt-1 max-h-60 w-full overflow-auto rounded-xl border border-slate-200 bg-white py-1 text-sm shadow-lg ring-1 ring-black/5 focus:outline-none"
      >
          <ComboboxOption
            v-for="user in options"
            :key="user.id"
            :value="user"
            as="template"
            v-slot="{ active }"
          >
            <li
              :class="[
                'cursor-default select-none px-4 py-2.5',
                active ? 'bg-sky-50 text-sky-900' : 'text-slate-700',
              ]"
            >
              <span class="block font-medium">{{ user.name }}</span>
              <span class="block text-xs text-slate-500">{{ user.email }}</span>
            </li>
          </ComboboxOption>
          <li v-if="loading" class="px-4 py-3 text-slate-500">Buscando...</li>
          <li
            v-else-if="query && options.length === 0"
            class="px-4 py-3 text-slate-500"
          >
            Nenhum usuário encontrado.
          </li>
          <li
            v-else-if="!query && options.length === 0"
            class="px-4 py-3 text-slate-500"
          >
            Digite para buscar...
          </li>
        </ComboboxOptions>
    </div>
  </Combobox>
</template>
