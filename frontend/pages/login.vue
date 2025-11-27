<template>
  <div class="flex flex-col items-center justify-center">
    <div class="bg-white shadow-md p-8 rounded-xl w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center text-black">Login</h2>

      <form class="flex flex-col gap-4" @submit.prevent="loginUser">
        <input
          v-model="email"
          type="email"
          placeholder="E-Mail"
          class="border rounded-lg p-2 text-black"
        />
        <input
          v-model="password"
          type="password"
          placeholder="Password"
          class="border rounded-lg p-2 text-black"
        />
        <button
          class="bg-shop-blue-dark text-white py-2 rounded-lg hover:bg-shop-blue-light"
        >
          Login
        </button>
      </form>

      <p v-if="error" class="text-signal-red mt-2 text-center">{{ error }}</p>
      <p class="mt-4 text-center">
        Du hast noch keinen Account?
        <NuxtLink to="/register" class="text-shop-blue-light"
          >Registrieren</NuxtLink
        >
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
const email = ref("");
const password = ref("");
const error = ref("");
const { login } = useAuth();

async function loginUser() {
  const success = await login(email.value, password.value);
  if (success) {
    navigateTo("/");
  } else {
    error.value = "Ungültige Anmeldedaten";
  }
}
</script>
