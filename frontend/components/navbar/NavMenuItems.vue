<template>
  <NuxtLink
    to="/"
    class="hover:text-shop-blue-light text-shop-blue-dark md:text-white"
    @click="emit('linkClicked')"
    >Home</NuxtLink
  >
  <span class="border-l border-shop-blue-light h-4"></span>

  <NuxtLink
    to="/products"
    class="hover:text-shop-blue-light text-shop-blue-dark md:text-white"
    @click="emit('linkClicked')"
    >Produkte</NuxtLink
  >
  <span class="border-l border-shop-blue-light h-4"></span>

  <NuxtLink
    to="/about"
    class="hover:text-shop-blue-light text-shop-blue-dark md:text-white"
    @click="emit('linkClicked')"
    >Info</NuxtLink
  >
  <span class="border-l border-shop-blue-light h-4"></span>

  <NuxtLink
    to="/contact"
    class="hover:text-shop-blue-light text-shop-blue-dark md:text-white"
    @click="emit('linkClicked')"
    >Kontakt</NuxtLink
  >
</template>

<script setup lang="ts">
const emit = defineEmits<{
  linkClicked: [];
}>();
</script>
