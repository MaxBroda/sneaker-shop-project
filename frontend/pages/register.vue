<template>
  <div
    class="flex flex-col items-center justify-center w-full py-12 min-h-[calc(100vh-200px)] px-4"
  >
    <div class="bg-white shadow-md p-8 md:rounded-xl w-full md:max-w-lg">
      <h2 class="text-2xl font-bold mb-6 text-center">Registrieren</h2>

      <AlertMessage type="error" :message="error" class="mb-6" />
      <AlertMessage type="success" :message="successMessage" class="mb-6" />

      <form @submit.prevent="registerUser" class="flex flex-col gap-4" novalidate>
        <div
          class="flex border border-gray-200 rounded-lg overflow-hidden w-full"
        >
          <button
            type="button"
            class="flex-1 text-center py-2 font-medium transition-colors duration-300 ease-in-out"
            :class="
              role === 'customer'
                ? 'bg-shop-blue-dark text-white'
                : 'bg-white text-gray-500 hover:bg-gray-100'
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
                : 'bg-white text-gray-500 hover:bg-gray-100'
            "
            @click="role = 'seller'"
          >
            Verkäufer
          </button>
        </div>
        <div class="flex flex-col md:flex-row gap-4 md:gap-2 w-full">
          <div class="md:w-1/2">
            <input
              v-model="firstName"
              type="text"
              placeholder="Vorname"
              class="border rounded-lg p-2 w-full"
              :class="{ 'border-signal-red': validationErrors.firstName }"
              @blur="validateField('firstName')"
            />
            <p v-if="validationErrors.firstName" class="text-signal-red text-sm mt-1">{{ validationErrors.firstName }}</p>
          </div>
          <div class="md:w-1/2">
            <input
              v-model="lastName"
              type="text"
              placeholder="Nachname"
              class="border rounded-lg p-2 w-full"
              :class="{ 'border-signal-red': validationErrors.lastName }"
              @blur="validateField('lastName')"
            />
            <p v-if="validationErrors.lastName" class="text-signal-red text-sm mt-1">{{ validationErrors.lastName }}</p>
          </div>
        </div>
        <hr class="my-2 w-full" />
        <div>
          <input
            v-model="email"
            type="email"
            placeholder="E-Mail"
            class="border rounded-lg p-2 w-full"
            :class="{ 'border-signal-red': validationErrors.email }"
            @blur="validateField('email')"
          />
          <p v-if="validationErrors.email" class="text-signal-red text-sm mt-1">{{ validationErrors.email }}</p>
        </div>

        <div class="flex flex-col md:flex-row gap-4 md:gap-2 w-full">
          <div class="md:w-1/2">
            <input
              v-model="password"
              type="password"
              placeholder="Passwort"
              class="border rounded-lg p-2 w-full"
              :class="{ 'border-signal-red': validationErrors.password }"
              @blur="validateField('password')"
            />
            <p v-if="validationErrors.password" class="text-signal-red text-sm mt-1">{{ validationErrors.password }}</p>
          </div>
          <div class="md:w-1/2">
            <input
              v-model="passwordConfirmation"
              type="password"
              placeholder="Passwort wiederholen"
              class="border rounded-lg p-2 w-full"
              :class="{ 'border-signal-red': validationErrors.passwordConfirmation }"
              @blur="validateField('passwordConfirmation')"
            />
            <p v-if="validationErrors.passwordConfirmation" class="text-signal-red text-sm mt-1">{{ validationErrors.passwordConfirmation }}</p>
          </div>
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
            class="border rounded-lg p-2 md:w-1/2"
            @input="filterNumbers($event, 'house_number')"
            required
          />
        </div>
        <div class="flex flex-col md:flex-row gap-4 md:gap-2 w-full">
          <input
            v-model="address.postal_code"
            type="text"
            placeholder="PLZ"
            maxlength="5"
            class="border rounded-lg p-2 md:w-1/2"
            @input="filterNumbers($event, 'postal_code')"
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
    </div>
  </div>
</template>

<script setup lang="ts">
import { useCart } from "~/composables/useCart";
import AlertMessage from '~/components/ui/AlertMessage.vue';

const { register } = useAuth();
const { mergeCart } = useCart();

const email = ref("");
const firstName = ref("");
const lastName = ref("");
const password = ref("");
const passwordConfirmation = ref("");
const role = ref("customer");
const error = ref("");
const successMessage = ref("");

const address = reactive({
  street: "",
  house_number: "",
  city: "",
  postal_code: "",
  country: "",
});

const validationErrors = reactive({
  firstName: "",
  lastName: "",
  email: "",
  password: "",
  passwordConfirmation: "",
});

function validateField(field: string) {
  switch (field) {
    case "firstName":
      validationErrors.firstName = !firstName.value ? "Bitte gib deinen Vornamen ein." : "";
      break;
    case "lastName":
      validationErrors.lastName = !lastName.value ? "Bitte gib deinen Nachnamen ein." : "";
      break;
    case "email":
      if (!email.value) {
        validationErrors.email = "Bitte gib deine E-Mail-Adresse ein.";
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        validationErrors.email = "Bitte gib eine gültige E-Mail-Adresse ein.";
      } else {
        validationErrors.email = "";
      }
      break;
    case "password":
      validationErrors.password = !password.value ? "Bitte gib ein Passwort ein." : "";
      break;
    case "passwordConfirmation":
      if (!passwordConfirmation.value) {
        validationErrors.passwordConfirmation = "Bitte wiederhole dein Passwort.";
      } else if (passwordConfirmation.value !== password.value) {
        validationErrors.passwordConfirmation = "Passwörter stimmen nicht überein.";
      } else {
        validationErrors.passwordConfirmation = "";
      }
      break;
  }
}

function validateAllFields() {
  validateField("firstName");
  validateField("lastName");
  validateField("email");
  validateField("password");
  validateField("passwordConfirmation");
  return !Object.values(validationErrors).some(error => error !== "");
}

function filterNumbers(event: Event, field: 'house_number' | 'postal_code') {
  const input = event.target as HTMLInputElement;
  const filtered = input.value.replace(/\D/g, "");
  if (field === 'postal_code' && filtered.length > 5) {
    address[field] = filtered.slice(0, 5);
  } else {
    address[field] = filtered;
  }
}

async function registerUser() {
  error.value = "";
  successMessage.value = "";
  
  if (!validateAllFields()) {
    return;
  }

  const res = await register(
    email.value,
    firstName.value,
    lastName.value,
    password.value,
    passwordConfirmation.value,
    role.value,
    address
  );

  if (res.success) {
    successMessage.value = res.message || "Registrierung erfolgreich!";
    await mergeCart();
    
    const route = useRoute();
    const redirectPath = route.query.redirect as string || "/";
    
    setTimeout(() => navigateTo(redirectPath), 1500);
  } else {
    error.value = res.message || "Registrierung fehlgeschlagen.";
  }
}
</script>
