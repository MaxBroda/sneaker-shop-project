<template>
  <div class="max-w-4xl mx-auto">
    <div class="grid md:grid-cols-2 gap-6">
      <div class="bg-white p-8 rounded-2xl shadow-lg">
        <div class="flex items-center gap-3 mb-6">
          <Icon name="mdi:account-circle" class="w-8 h-8 text-shop-blue-light" />
          <h2 class="text-2xl font-bold text-shop-blue-dark">Anmelden</h2>
        </div>
        
        <AlertMessage type="error" :message="loginError" class="mb-4" />

        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label class="block text-sm font-semibold mb-2 ">
              E-Mail
            </label>
            <input
              v-model="loginForm.email"
              type="email"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
              placeholder="deine@email.de"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-2 ">
              Passwort
            </label>
            <input
              v-model="loginForm.password"
              type="password"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
              placeholder="••••••••"
            />
          </div>

          <button
            type="submit"
            :disabled="isLoggingIn"
            class="w-full bg-shop-blue-light hover:bg-shop-blue-dark text-white py-3 rounded-lg font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <Icon v-if="isLoggingIn" name="mdi:loading" class="w-5 h-5 animate-spin" />
            <span>{{ isLoggingIn ? 'Anmelden...' : 'Anmelden' }}</span>
          </button>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-200">
          <p class="text-sm text-gray-500 mb-3">Noch kein Konto?</p>
          <NuxtLink
            to="/register?redirect=/checkout"
            class="block w-full bg-gray-100 hover:bg-gray-200  py-3 rounded-lg font-semibold text-center transition-all"
          >
            Jetzt registrieren
          </NuxtLink>
        </div>
      </div>

      <div class="bg-white p-8 rounded-2xl shadow-lg">
        <div class="flex items-center gap-3 mb-6">
          <Icon name="mdi:account-outline" class="w-8 h-8 text-shop-blue-light" />
          <h2 class="text-2xl font-bold text-shop-blue-dark">Als Gast bestellen</h2>
        </div>

        <p class="text-gray-500 mb-6">
          Bestelle schnell und einfach ohne ein Konto zu erstellen. Du kannst später jederzeit ein Konto anlegen.
        </p>

        <div class="space-y-4 mb-6">
          <div class="flex items-start gap-3">
            <Icon name="mdi:check-circle" class="w-6 h-6 text-signal-green flex-shrink-0 mt-0.5" />
            <div>
              <p class="font-semibold ">Schneller Checkout</p>
              <p class="text-sm text-gray-500">Keine Registrierung erforderlich</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <Icon name="mdi:check-circle" class="w-6 h-6 text-signal-green flex-shrink-0 mt-0.5" />
            <div>
              <p class="font-semibold ">Sichere Bestellung</p>
              <p class="text-sm text-gray-500">Deine Daten sind geschützt</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <Icon name="mdi:check-circle" class="w-6 h-6 text-signal-green flex-shrink-0 mt-0.5" />
            <div>
              <p class="font-semibold ">E-Mail Bestätigung</p>
              <p class="text-sm text-gray-500">Erhalte deine Bestellbestätigung</p>
            </div>
          </div>
        </div>

        <button
          @click="$emit('guest-checkout')"
          class="w-full bg-signal-red hover:opacity-90 text-white py-3 rounded-lg font-bold transition-all flex items-center justify-center gap-2"
        >
          <Icon name="mdi:cart-check" class="w-5 h-5" />
          <span>Als Gast bestellen</span>
        </button>

        <p class="text-xs text-gray-500 mt-4 text-center">
          Du erhältst eine Bestellbestätigung per E-Mail mit deiner Bestellnummer.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import AlertMessage from '~/components/ui/AlertMessage.vue';

const { login } = useAuth();

const emit = defineEmits<{
  'login-success': [];
  'guest-checkout': [];
}>();

const loginForm = reactive({
  email: '',
  password: '',
});

const loginError = ref('');
const isLoggingIn = ref(false);

async function handleLogin() {
  loginError.value = '';
  isLoggingIn.value = true;

  try {
    const result = await login(loginForm.email, loginForm.password);
    
    if (result.success) {
      emit('login-success');
    } else {
      loginError.value = result.message || 'Anmeldung fehlgeschlagen';
    }
  } catch (error) {
    loginError.value = 'Ein Fehler ist aufgetreten. Bitte versuche es erneut.';
  } finally {
    isLoggingIn.value = false;
  }
}
</script>
