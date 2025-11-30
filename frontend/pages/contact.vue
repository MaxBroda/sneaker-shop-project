<template>
  <div class="container mx-auto px-4 py-12 max-w-5xl">
    <!-- Header -->
    <div class="text-center mb-12">
      <h1 class="text-4xl md:text-5xl font-bold mb-4 text-shop-blue-dark">
        Kontaktiere uns
      </h1>
      <p class="text-lg ">
        Wir freuen uns auf deine Nachricht! Unser Team antwortet in der Regel innerhalb von 24 Stunden.
      </p>
    </div>

    <div class="grid md:grid-cols-2 gap-12">
      <!-- Contact Form -->
      <div class="bg-white p-8 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-shop-blue-dark">Schreib uns</h2>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2 ">Name</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-4 py-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-dark transition-all"
              placeholder="Max Mustermann"
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2 ">E-Mail</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-dark transition-all"
              placeholder="max@beispiel.de"
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2 ">Betreff</label>
            <select
              v-model="form.subject"
              required
              class="w-full px-4 py-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-dark transition-all"
            >
              <option value="" disabled>Bitte wählen...</option>
              <option value="product">Produktanfrage</option>
              <option value="order">Bestellstatus</option>
              <option value="return">Rückgabe & Umtausch</option>
              <option value="partnership">Partnerschaft</option>
              <option value="application">Bewerbung</option>
              <option value="other">Sonstiges</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2 ">Nachricht</label>
            <textarea
              v-model="form.message"
              required
              rows="5"
              class="w-full px-4 py-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-dark transition-all resize-none"
              placeholder="Wie können wir dir helfen?"
            ></textarea>
          </div>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="w-full bg-shop-blue-light text-white py-3 rounded-lg font-semibold hover:bg-shop-blue-dark transition-all disabled:bg-gray-500 disabled:cursor-not-allowed"
          >
            {{ isSubmitting ? 'Wird gesendet...' : 'Nachricht senden' }}
          </button>
        </form>
        <div
          v-if="successMessage"
          class="mt-4 bg-green-100 border border-green-400 text-signal-green px-4 py-3 rounded-lg"
        >
          {{ successMessage }}
        </div>
      </div>

      <!-- Contact Info -->
      <div class="space-y-8">
        <div class="bg-white p-8 rounded-xl shadow-lg">
          <h2 class="text-2xl font-bold mb-6 text-shop-blue-dark">Kontaktinformationen</h2>
          <div class="space-y-4">
            <div class="flex items-start gap-4">
              <div class="text-2xl">📍</div>
              <div>
                <h3 class="font-semibold ">Adresse</h3>
                <p class="">
                  EcoStep GmbH<br />
                  Maximilianstraße 42<br />
                  01239 Dresden, Deutschland
                </p>
              </div>
            </div>
            <div class="flex items-start gap-4">
              <div class="text-2xl">📧</div>
              <div>
                <h3 class="font-semibold ">E-Mail</h3>
                <p class="">info@ecostep.de</p>
                <p class="">support@ecostep.de</p>
              </div>
            </div>
            <div class="flex items-start gap-4">
              <div class="text-2xl">📞</div>
              <div>
                <h3 class="font-semibold ">Telefon</h3>
                <p class="">+49 89 123 456 78</p>
                <p class="text-sm text-gray-500">Mo-Fr: 9:00 - 18:00 Uhr</p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-shop-blue-dark to-shop-blue-light text-white p-8 rounded-xl">
          <h3 class="text-xl font-bold mb-4">Öffnungszeiten</h3>
          <div class="space-y-2">
            <div class="flex justify-between">
              <span>Montag - Freitag</span>
              <span class="font-semibold">9:00 - 18:00</span>
            </div>
            <div class="flex justify-between">
              <span>Samstag</span>
              <span class="font-semibold">10:00 - 16:00</span>
            </div>
            <div class="flex justify-between">
              <span>Sonntag</span>
              <span class="font-semibold">Geschlossen</span>
            </div>
          </div>
        </div>

        <div class="bg-gray-50 p-8 rounded-xl border-l-4 border-shop-blue-light">
          <h3 class="font-bold mb-2 text-shop-blue-dark flex items-center gap-2">
            <Icon name="mdi:share-variant" class="w-5 h-5" />
            Social Media
          </h3>
          <p class="text-sm mb-4">
            Folge uns für Updates, Inspiration und exklusive Angebote!
          </p>
          <div class="flex gap-4">
            <a href="#" class="text-shop-blue-light hover:text-shop-blue-dark hover:scale-110 transition-all" title="Instagram">
              <Icon name="mdi:instagram" class="w-7 h-7" />
            </a>
            <a href="#" class="text-shop-blue-light hover:text-shop-blue-dark hover:scale-110 transition-all" title="Facebook">
              <Icon name="mdi:facebook" class="w-7 h-7" />
            </a>
            <a href="#" class="text-shop-blue-light hover:text-shop-blue-dark hover:scale-110 transition-all" title="Twitter">
              <Icon name="mdi:twitter" class="w-7 h-7" />
            </a>
            <a href="#" class="text-shop-blue-light hover:text-shop-blue-dark hover:scale-110 transition-all" title="LinkedIn">
              <Icon name="mdi:linkedin" class="w-7 h-7" />
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const form = reactive({
  name: '',
  email: '',
  subject: '',
  message: ''
});

const isSubmitting = ref(false);
const successMessage = ref('');

async function handleSubmit() {
  isSubmitting.value = true;
  
  // Simulate API call
  await new Promise(resolve => setTimeout(resolve, 1000));
  
  // TODO: Implement actual API call to backend
  console.log('Form submitted:', form);
  
  successMessage.value = 'Vielen Dank für deine Nachricht! Wir melden uns bald bei dir.';
  
  // Reset form
  form.name = '';
  form.email = '';
  form.subject = '';
  form.message = '';
  
  isSubmitting.value = false;
  
  // Clear success message after 5 seconds
  setTimeout(() => {
    successMessage.value = '';
  }, 5000);
}
</script>
