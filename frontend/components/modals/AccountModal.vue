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
        class="absolute -right-5 mt-3 w-52 bg-white text-shop-dark rounded-xl shadow-xl z-50 overflow-hidden"
      >
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
          <h1 class="font-semibold">
            {{ user?.firstName }} {{ user?.lastName }}
          </h1>
          <p class="text-sm text-gray-500">
            {{ getRoleLabel(user?.role) }}
          </p>
        </div>
        <ul class="">
          <li>
            <NuxtLink
              to="/profile"
              class="flex items-center gap-1 px-4 py-2 hover:bg-gray-50 transition-all"
              @click="closeDropdown"
            >
              <Icon name="mdi:account-outline" class="w-5 h-5 text-shop-blue-light" />
              <span>Profil</span>
            </NuxtLink>
          </li>
          <li>
            <NuxtLink
              to="/profile?section=addresses"
              class="flex items-center gap-1 px-4 py-2 hover:bg-gray-50 transition-all"
              @click="closeDropdown"
            >
              <Icon name="mdi:map-marker-outline" class="w-5 h-5 text-shop-blue-light" />
              <span>Adressen</span>
            </NuxtLink>
          </li>
          <li v-if="user?.role === 'customer'">
            <NuxtLink
              to="/profile?section=orders"
              class="flex items-center gap-1 px-4 py-2 hover:bg-gray-50 transition-all"
              @click="closeDropdown"
            >
              <Icon name="mdi:package-variant" class="w-5 h-5 text-shop-blue-light" />
              <span>Bestellungen</span>
            </NuxtLink>
          </li>
          <li v-if="user?.role === 'seller'">
            <NuxtLink
              to="/profile?section=products"
              class="flex items-center gap-1 px-4 py-2 hover:bg-gray-50 transition-all"
              @click="closeDropdown"
            >
              <Icon name="mdi:store-outline" class="w-5 h-5 text-shop-blue-light" />
              <span>Produkte verwalten</span>
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
const { clearCart, fetchCart } = useCart();
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
  clearCart();
  await fetchCart();
  closeDropdown();
  navigateTo('/');
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