<template>
  <div class="relative" ref="dropdownRef">
    <button
      @click="toggleDropdown"
      class="flex items-center justify-center hover:text-shop-blue-light focus:outline-none transition-all hover:scale-110"
    >
      <Icon name="mdi:account" class="w-6 h-6 text-white" />
    </button>
    <transition name="fade">
      <div
        v-if="isOpen"
        class="absolute -right-5 mt-3 w-64 bg-white text-shop-dark rounded-xl shadow-xl z-50 overflow-hidden"
      >
        <div class="px-4 py-4">
          <h2 class="font-semibold text-center">Schon ein Konto?</h2>
        </div>
        <div class="px-4 pb-4">
          <NuxtLink
            to="/login"
            class="block w-full bg-signal-red text-white text-center py-2 rounded-lg hover:opacity-90 transition-all"
            @click="closeDropdown"
          >
            Login
          </NuxtLink>
        </div>
        <div class="px-4 pb-4 pt-2 border-t border-gray-200">
          <p class="text-sm text-center mb-3 text-gray-500">Kein Konto?</p>
          <NuxtLink
            to="/register"
            class="block w-full bg-shop-blue-dark text-white text-center py-2 rounded-lg hover:bg-shop-blue-light transition-all"
            @click="closeDropdown"
          >
            Registrieren
          </NuxtLink>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue";

const isOpen = ref(false);
const dropdownRef = ref<HTMLElement | null>(null);

function toggleDropdown() {
  isOpen.value = !isOpen.value;
}

function closeDropdown() {
  isOpen.value = false;
}

function handleClickOutside(event: MouseEvent) {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    isOpen.value = false;
  }
}

onMounted(() => document.addEventListener("click", handleClickOutside));
onBeforeUnmount(() =>
  document.removeEventListener("click", handleClickOutside)
);
</script>