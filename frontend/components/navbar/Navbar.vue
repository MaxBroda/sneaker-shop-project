<template>
  <nav class="px-4 my-4">
    <div
      class="hidden md:grid md:grid-cols-3 bg-shop-blue-dark items-center shadow-lg rounded-full text-white pl-6 h-16"
    >
      <div class="flex items-center">
        <div class="text-2xl font-bold text-shop-blue-light">SneakerShop</div>
      </div>
      <div class="flex justify-center items-center space-x-4">
        <NavbarMobileNavMenuItems />
      </div>
      <div class="flex justify-end items-center space-x-4 h-full py-2 pr-2">
        <NuxtLink
          v-if="!user"
          to="/login"
          class="bg-signal-red hover:bg-red-800 text-white font-bold h-full px-10 rounded-full flex items-center justify-center transition-all duration-200"
        >
          Login
        </NuxtLink>
        <template v-else>
          <NuxtLink
            v-if="user && user.role === 'seller'"
            to="/add-product"
            class="hover:text-shop-blue-light flex items-center justify-center"
          >
            <Icon name="mdi:plus-thick" class="w-6 h-6 text-white" />
          </NuxtLink>
          <NuxtLink
            v-if="user && user.role === 'customer'"
            to="/cart"
            class="hover:text-shop-blue-light flex items-center justify-center"
          >
            <Icon name="mdi:shopping-cart" class="w-5 h-5 text-white" />
          </NuxtLink>
          <AccountModal />
          <button
            class="bg-signal-red hover:bg-red-800 text-white font-bold h-full px-8 rounded-full transition-all duration-200"
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

const { user, logout } = useAuth();

async function handleLogout() {
  await logout();
  console.log("👋 User logged out.");
  navigateTo("/");
}
</script>
