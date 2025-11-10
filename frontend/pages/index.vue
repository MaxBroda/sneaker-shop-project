<template>
  <div class="flex flex-col items-center justify-center">
    <h1 class="text-3xl font-bold mb-4 text-center">
      Willkommen zu Sneaker Shop!
    </h1>
    <p v-if="user">{{ user?.firstName }}, schön dich zu sehen!</p>
  </div>
</template>
<script setup lang="ts">
const { user } = useAuth();
</script>
