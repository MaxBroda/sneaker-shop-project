<template>
  <div class="relative">
    <select
      :value="modelValue"
      @change="handleChange"
      :required="required"
      :disabled="disabled"
      class="w-full pr-10 cursor-pointer appearance-none"
      :class="customClass || 'px-4 py-3 border-2 border-gray-200 rounded-xl text-gray-300 focus:outline-none focus:border-shop-blue-light bg-white transition-all hover:bg-blue-50 hover:border-shop-blue-light'"
    >
      <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>
    <Icon
      name="mdi:chevron-down"
      class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-300 pointer-events-none"
    />
  </div>
</template>

<script setup lang="ts">
interface Option {
  value: string;
  label: string;
}

interface Props {
  modelValue: string;
  options: Option[];
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
  customClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: '',
  required: false,
  disabled: false,
  customClass: ''
});

const emit = defineEmits<{
  'update:modelValue': [value: string];
}>();

function handleChange(event: Event) {
  const target = event.target as HTMLSelectElement;
  emit('update:modelValue', target.value);
}
</script>

