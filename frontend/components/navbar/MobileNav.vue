<template>
  <div class="relative" ref="mobileNavRef">
    <div class="bg-shop-blue-dark rounded-full shadow-lg relative z-50">
      <div
        class="flex items-center justify-between py-4 px-6 text-shop-blue-light"
      >
        <div class="text-lg font-bold text-shop-blue-light">SneakerShop</div>
        <div class="flex items-center text-white">
          <button
            @click="toggleMenu"
            class="focus:outline-none flex items-center justify-center"
          >
            <Icon name="mdi:menu" class="w-6 h-6 text-white" />
          </button>
        </div>
      </div>
    </div>

    <div
      class="absolute top-12 left-1/2 -translate-x-1/2 bg-white rounded-xl shadow-xl border z-40 px-4 pb-4 pt-8 w-max transform transition-all duration-300 ease-out"
      :class="{
        'opacity-100 translate-y-0 pointer-events-auto': isOpen,
        'opacity-0 -translate-y-4 pointer-events-none': !isOpen,
      }"
    >
      <div class="flex flex-row gap-3 justify-center items-center">
        <NavbarMobileNavMenuItems @link-clicked="closeMenu" />
      </div>
      <hr class="my-4 border-shop-blue-light" />
      <div class="mt-4 flex flex-col justify-center items-center">
        <div
          class="flex flex-row justify-around items-center w-full gap-2 mb-2"
        >
          <NuxtLink
            v-if="user"
            @click="closeMenu"
            class="w-1/2 flex justify-center items-center p-1 bg-shop-bright rounded-full"
            >Profil</NuxtLink
          >
          <NuxtLink
            v-if="user && user.role === 'customer'"
            to="/cart"
            @click="closeMenu"
            class="w-1/2 flex justify-center items-center p-1 bg-shop-bright rounded-full"
            >Warenkorb</NuxtLink
          >
          <NuxtLink
            v-if="user && user.role === 'seller'"
            to="/add-product"
            @click="closeMenu"
            class="w-1/2 flex justify-center items-center p-1 bg-shop-bright rounded-full"
            >Hinzufügen</NuxtLink
          >
        </div>
        <NuxtLink
          v-if="!user"
          to="/login"
          @click="closeMenu"
          class="w-full flex justify-center items-center p-1 bg-signal-red text-white font-bold rounded-full transition-all duration-200"
          >Login</NuxtLink
        >
        <NuxtLink
          v-if="user"
          to="/login"
          class="w-full flex justify-center items-center p-1 bg-signal-red text-white font-bold rounded-full transition-all duration-200"
          @click="handleLogout"
          >Logout</NuxtLink
        >
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import NavbarMobileNavMenuItems from "./NavMenuItems.vue";
import { ref, onMounted, onUnmounted } from "vue";

const { user, logout } = useAuth();

const isOpen = ref(false);
const mobileNavRef = ref<HTMLElement | null>(null);

const toggleMenu = () => {
  isOpen.value = !isOpen.value;
};

const closeMenu = () => {
  isOpen.value = false;
};

const handleClickOutside = (event: MouseEvent) => {
  if (
    mobileNavRef.value &&
    !mobileNavRef.value.contains(event.target as Node)
  ) {
    closeMenu();
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});

async function handleLogout() {
  await logout();
  console.log("👋 User logged out.");
  closeMenu();
  navigateTo("/");
}
</script>
