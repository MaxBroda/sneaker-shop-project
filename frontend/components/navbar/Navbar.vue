<template>
  <nav class="px-4 my-4">
    <div
      class="hidden md:grid md:grid-cols-3 bg-shop-blue-dark items-center shadow-lg rounded-full text-white pl-6 h-16"
    >
      <div class="flex items-center">
        <NuxtLink to="/" class="text-2xl font-bold text-shop-blue-light">SneakerShop</NuxtLink>
      </div>
      <div class="flex justify-center items-center space-x-4">
        <NavbarMobileNavMenuItems />
      </div>
      <div class="flex justify-end items-center space-x-4 h-full py-2 pr-2">
        <template v-if="!user">
          <CartModal />
          <GuestModal />
          <NuxtLink
            to="/login"
            class="bg-signal-red bg-signal-red-hover text-white font-bold h-full px-10 rounded-full flex items-center justify-center transition-all duration-200"
          >
            Login
          </NuxtLink>
        </template>
        <template v-else>
          <NuxtLink
            v-if="user.role === 'seller'"
            to="/add-product"
            class="hover:text-shop-blue-light flex items-center justify-center transition-all hover:scale-110"
          >
            <Icon name="mdi:plus-thick" class="w-6 h-6 text-white" />
          </NuxtLink>
          <CartModal v-if="user.role === 'customer'" />
          <AccountModal />
          <button
            class="bg-signal-red bg-signal-red-hover text-white font-bold h-full px-8 rounded-full transition-all duration-200"
            @click="handleLogout"
          >
            Logout
          </button>
        </template>
      </div>
    </div>
    <MobileNav class="md:hidden" />
  </nav>
</template>

<script setup lang="ts">
import NavbarMobileNavMenuItems from "./NavMenuItems.vue";
import MobileNav from "./MobileNav.vue";
import AccountModal from "../modals/AccountModal.vue";
import GuestModal from "../modals/GuestModal.vue";
import CartModal from "../modals/CartModal.vue";
import { useCart } from "~/composables/useCart";

const { user, logout } = useAuth();
const { clearCart, fetchCart } = useCart();

async function handleLogout() {
  await logout();
  clearCart();
  await fetchCart();
  navigateTo('/');
}
</script>
