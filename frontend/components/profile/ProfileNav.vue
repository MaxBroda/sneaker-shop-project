<template>
  <div class="bg-white p-4 shadow-md rounded-xl flex flex-col h-full max-h-[400px]">
    <h2 class="text-lg font-semibold mb-4 text-shop-blue-dark">Mein Konto</h2>
    <nav class="space-y-2 flex-1">
      <button
        v-for="item in menuItems"
        :key="item.id"
        @click="$emit('select', item.id)"
        class="w-full text-left px-4 py-2 rounded-lg transition-all"
        :class="
          activeSection === item.id
            ? 'bg-shop-blue-light text-white'
            : 'hover:bg-gray-100 '
        "
      >
        {{ item.label }}
      </button>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";

interface MenuItem {
  id: string;
  label: string;
}

interface Props {
  activeSection: string;
}

defineProps<Props>();
defineEmits<{
  select: [sectionId: string];
}>();

const { user } = useAuth();

const menuItems = computed<MenuItem[]>(() => {
  const baseItems: MenuItem[] = [
    { id: "account", label: "Kontoinformationen" },
    { id: "addresses", label: "Adressen" },
  ];

  if (user.value?.role === "customer") {
    baseItems.push({ id: "orders", label: "Bestellungen" });
  } else if (user.value?.role === "seller") {
    baseItems.push({ id: "products", label: "Produkte verwalten" });
  }

  return baseItems;
});
</script>
