<template>
  <div class="flex flex-col items-center justify-center py-12 min-h-[calc(100vh-200px)]">
    <div class="bg-white shadow-md p-8 rounded-xl w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center text-black">Login</h2>

      <AlertMessage type="error" :message="error" class="mb-6" />
      <AlertMessage type="success" :message="successMessage" class="mb-6" />

      <form class="flex flex-col gap-4" @submit.prevent="loginUser" novalidate>
        <div>
          <input
            v-model="email"
            type="email"
            placeholder="E-Mail"
            class="border rounded-lg p-2 text-black w-full"
            :class="{ 'border-signal-red': emailError }"
            @blur="validateEmail"
          />
          <p v-if="emailError" class="text-signal-red text-sm mt-1">{{ emailError }}</p>
        </div>
        <div>
          <input
            v-model="password"
            type="password"
            placeholder="Password"
            class="border rounded-lg p-2 text-black w-full"
            :class="{ 'border-signal-red': passwordError }"
            @blur="validatePassword"
          />
          <p v-if="passwordError" class="text-signal-red text-sm mt-1">{{ passwordError }}</p>
        </div>
        <button
          class="bg-shop-blue-dark text-white py-2 rounded-lg hover:bg-shop-blue-light"
        >
          Login
        </button>
      </form>

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
import { useCart } from "~/composables/useCart";
import AlertMessage from '~/components/ui/AlertMessage.vue';

const email = ref("");
const password = ref("");
const error = ref("");
const successMessage = ref("");
const emailError = ref("");
const passwordError = ref("");
const { login } = useAuth();
const { fetchCart } = useCart();

function validateEmail() {
  if (!email.value) {
    emailError.value = "Bitte gib deine E-Mail-Adresse ein.";
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    emailError.value = "Bitte gib eine gültige E-Mail-Adresse ein.";
  } else {
    emailError.value = "";
  }
}

function validatePassword() {
  if (!password.value) {
    passwordError.value = "Bitte gib dein Passwort ein.";
  } else {
    passwordError.value = "";
  }
}

async function loginUser() {
  error.value = "";
  successMessage.value = "";
  
  validateEmail();
  validatePassword();
  
  if (emailError.value || passwordError.value) {
    return;
  }
  
  const res = await login(email.value, password.value);
  if (res.success) {
    successMessage.value = "Login erfolgreich!";
    await fetchCart();
    setTimeout(() => navigateTo("/"), 1000);
  } else {
    // Handle validation errors
    if (res.errors) {
      const errorMessages = Object.values(res.errors).join('. ');
      error.value = errorMessages;
    } else {
      error.value = res.message || "Login fehlgeschlagen.";
    }
  }
}
</script>
