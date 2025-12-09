<template>
  <div
    v-if="message"
    :class="[
      'px-4 py-3 rounded-lg flex items-start gap-3',
      type === 'success' 
        ? 'bg-green-100 border-l-4 border-shop-blue-dark text-signal-green' 
        : type === 'error'
        ? 'bg-red-100 border-l-4 border-signal-red text-signal-red'
        : 'bg-blue-50 border-l-4 border-shop-blue-light text-shop-blue-dark'
    ]"
  >
    <Icon 
      :name="type === 'success' ? 'mdi:check-circle' : type === 'error' ? 'mdi:alert-circle' : 'mdi:information'" 
      class="w-6 h-6 flex-shrink-0 mt-0.5" 
    />
    <div class="flex-1">
      <h3 v-if="title" class="font-semibold mb-1">{{ title }}</h3>
      <p class="font-medium">{{ message }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  type?: 'success' | 'error' | 'info';
  message?: string;
  title?: string;
}

withDefaults(defineProps<Props>(), {
  type: 'info',
  message: '',
  title: ''
});
</script>
