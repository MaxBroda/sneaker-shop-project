<template>
  <div class="min-h-screen bg-shop-bright flex flex-col">
    <Navbar />

    <main class="flex-1 flex flex-col items-center justify-center">
      <NuxtPage />
    </main>
  </div>
</template>

<script setup>
import Navbar from "~/components/navbar/Navbar.vue";
</script>
