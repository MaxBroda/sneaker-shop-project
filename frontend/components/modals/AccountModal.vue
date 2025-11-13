<template>
  <div class="relative" ref="dropdownRef">
    <button
      @click="toggleDropdown"
      class="flex items-center justify-center hover:text-shop-blue-light focus:outline-none"
    >
      <Icon name="mdi:account" class="w-6 h-6 text-white" />
    </button>
    <transition name="fade">
      <div
        v-if="isOpen"
        class="absolute -right-5 mt-3 w-52 bg-white text-shop-dark rounded-xl shadow-xl border z-50"
      >
        <div class="px-4 py-3 border-b border-gray-200">
          <h1 class="font-semibold text-shop-blue-dark">
            {{ user?.firstName }} {{ user?.lastName }}
          </h1>
          <p class="text-sm text-shop-blue-light">
            {{ getRoleLabel(user?.role) }}
          </p>
        </div>
        <ul class="py-2">
          <li>
            <NuxtLink
              to="/profile"
              class="block px-4 py-2 hover:bg-gray-100 transition text-shop-blue-dark"
              @click="closeDropdown"
            >
              Profil
            </NuxtLink>
          </li>

          <li v-if="user?.role === 'seller'">
            <NuxtLink
              to="/seller/add-product"
              class="block px-4 py-2 hover:bg-gray-100 transition text-shop-blue-dark"
              @click="closeDropdown"
            >
              Produkte verwalten
            </NuxtLink>
          </li>
          <li v-if="user?.role === 'customer'">
            <NuxtLink
              to="/orders"
              class="block px-4 py-2 hover:bg-gray-100 transition text-shop-blue-dark"
              @click="closeDropdown"
            >
              Bestellungen
            </NuxtLink>
            <NuxtLink
              to="/address-book"
              class="block px-4 py-2 hover:bg-gray-100 transition text-shop-blue-dark"
              @click="closeDropdown"
            >
              Addressen
            </NuxtLink>
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue";

const { user, logout } = useAuth();
const isOpen = ref(false);
const dropdownRef = ref<HTMLElement | null>(null);

function toggleDropdown() {
  isOpen.value = !isOpen.value;
}

function closeDropdown() {
  isOpen.value = false;
}

async function handleLogout() {
  await logout();
  closeDropdown();
  navigateTo("/");
}

function getRoleLabel(role?: string): string {
  if (role === "customer") return "Käufer";
  if (role === "seller") return "Verkäufer";
  return "Keine Rolle";
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

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
