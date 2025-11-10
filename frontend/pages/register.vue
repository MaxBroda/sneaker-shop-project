<template>
  <div
    class="flex flex-col items-center justify-center w-full md:max-w-lg mb-20"
  >
    <div class="bg-white shadow-md p-8 md:rounded-xl w-full">
      <h2 class="text-2xl font-bold mb-6 text-center">Registrieren</h2>

      <form @submit.prevent="registerUser" class="flex flex-col gap-4">
        <div
          class="flex border border-gray-200 rounded-lg overflow-hidden w-full"
        >
          <button
            type="button"
            class="flex-1 text-center py-2 font-medium transition-colors duration-300 ease-in-out"
            :class="
              role === 'customer'
                ? 'bg-shop-blue-dark text-white'
                : 'bg-white text-gray-400 hover:bg-gray-100'
            "
            @click="role = 'customer'"
          >
            Käufer
          </button>
          <button
            type="button"
            class="flex-1 text-center py-2 font-medium transition-colors duration-300 ease-in-out"
            :class="
              role === 'seller'
                ? 'bg-shop-blue-dark text-white'
                : 'bg-white text-gray-400 hover:bg-gray-100'
            "
            @click="role = 'seller'"
          >
            Verkäufer
          </button>
        </div>
        <div class="flex flex-col md:flex-row gap-4 md:gap-2 w-full">
          <input
            v-model="lastName"
            type="text"
            placeholder="Nachname"
            class="border rounded-lg p-2 md:w-1/2"
            required
          />
          <input
            v-model="firstName"
            type="text"
            placeholder="Vorname"
            class="border rounded-lg p-2 md:w-1/2"
            required
          />
        </div>
        <hr class="my-2 w-full" />
        <input
          v-model="email"
          type="email"
          placeholder="E-Mail"
          class="border rounded-lg p-2 w-full"
          required
        />

        <div class="flex flex-col md:flex-row gap-4 md:gap-2 w-full">
          <input
            v-model="password"
            type="password"
            placeholder="Passwort"
            class="border rounded-lg p-2 md:w-1/2"
            required
          />
          <input
            v-model="passwordConfirmation"
            type="password"
            placeholder="Passwort wiederholen"
            class="border rounded-lg p-2 md:w-1/2"
            required
          />
        </div>
        <hr class="my-2 w-full" />

        <div class="flex flex-col md:flex-row gap-4 md:gap-2 w-full">
          <input
            v-model="address.street"
            type="text"
            placeholder="Straße"
            class="border rounded-lg p-2 md:w-1/2"
            required
          />
          <input
            v-model="address.house_number"
            type="text"
            placeholder="Hausnummer"
            @input="validateNumberInput"
            class="border rounded-lg p-2 md:w-1/2"
            required
          />
        </div>
        <div class="flex flex-col md:flex-row gap-4 md:gap-2 w-full">
          <input
            v-model="address.postal_code"
            type="text"
            placeholder="PLZ"
            maxlength="5"
            @input="validateNumberInput"
            class="border rounded-lg p-2 md:w-1/2"
            required
          />
          <input
            v-model="address.city"
            type="text"
            placeholder="Stadt"
            class="border rounded-lg p-2 md:w-1/2"
            required
          />
        </div>

        <input
          v-model="address.country"
          type="text"
          placeholder="Land"
          class="border rounded-lg p-2"
          required
        />

        <button
          type="submit"
          class="bg-shop-blue-dark text-white py-2 rounded-lg hover:bg-shop-blue-light transition-colors duration-300"
        >
          Registrieren
        </button>
      </form>

      <p v-if="error" class="text-red-500 mt-2 text-center">{{ error }}</p>
      <p v-if="success" class="text-green-600 mt-2 text-center">
        Registrierung erfolgreich! Weiterleitung...
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from "vue";

const email = ref("");
const firstName = ref("");
const lastName = ref("");
const password = ref("");
const passwordConfirmation = ref("");
const role = ref("customer");
const error = ref("");
const success = ref(false);

const address = reactive({
  street: "",
  house_number: "",
  city: "",
  postal_code: "",
  country: "",
});

const { register } = useAuth();

// Simple email regex for validation
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Number validation - only allow digits
function validateNumberInput(event: Event) {
  const input = event.target as HTMLInputElement;
  input.value = input.value.replace(/\D/g, "");
}

async function registerUser() {
  error.value = "";
  success.value = false;

  if (!emailRegex.test(email.value)) {
    error.value = "Bitte eine gültige E-Mail-Adresse eingeben.";
    return;
  }
  if (password.value !== passwordConfirmation.value) {
    error.value = "Passwörter stimmen nicht überein.";
    return;
  }

  const res = await register(
    email.value,
    firstName.value,
    lastName.value,
    password.value,
    role.value,
    address
  );

  if (res.success) {
    success.value = true;
    setTimeout(() => navigateTo("/"), 500);
  } else {
    error.value = res.message || "Registrierung fehlgeschlagen.";
  }
}
</script>
