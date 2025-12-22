<template>
  <div>
    <div class="container mx-auto px-4 py-6 max-w-7xl">
      <button
        @click="navigateTo('/products')"
        class="group flex items-center gap-2 hover:text-shop-blue-light transition-colors"
      >
        <Icon name="mdi:arrow-left" class="w-5 h-5 transition-transform group-hover:-translate-x-1" />
        <span class="font-medium hover:text-shop-blue-light">Zurück zu allen Produkten</span>
      </button>
    </div>

    <div v-if="isLoading" class="container mx-auto px-4 py-12 text-center">
      <Icon name="mdi:loading" class="w-12 h-12 animate-spin mx-auto text-shop-blue-light mb-4" />
      <p class="text-gray-500">Produkt wird geladen...</p>
    </div>
    <div
      v-else-if="errorMessage"
      class="container mx-auto px-4 py-12 max-w-2xl"
    >
      <AlertMessage type="error" title="Fehler" :message="errorMessage" />
    </div>
    <div v-else-if="product" class="bg-gradient-to-b pb-12">
      <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid md:grid-cols-2 gap-6 md:gap-12 mb-12">
          <div class="md:sticky md:top-24 md:self-start">
            <div class="bg-white rounded-xl md:rounded-2xl shadow-lg md:shadow-xl overflow-hidden p-3 md:p-4">
              <div class="bg-gradient-to-br from-gray-100 to-gray-200 aspect-[5/4] rounded-lg md:rounded-xl overflow-hidden mb-3 md:mb-4">
                <img
                  :src="`${config.public.uploadsUrl}/${product.image}`"
                  :alt="product.name"
                  loading="eager"
                  class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                />
              </div>
              <div class="grid grid-cols-3 gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-md md:rounded-lg p-2 md:p-3 text-center border border-green-200">
                  <Icon name="mdi:leaf" class="w-5 h-5 md:w-6 md:h-6 mx-auto mb-1 text-signal-green" />
                  <p class="text-xs font-medium text-signal-green">Nachhaltig</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-md md:rounded-lg p-2 md:p-3 text-center border border-blue-200">
                  <Icon name="mdi:truck-fast" class="w-5 h-5 md:w-6 md:h-6 mx-auto mb-1 text-shop-blue-light" />
                  <p class="text-xs font-medium text-shop-blue-light">Gratis Versand</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-md md:rounded-lg p-2 md:p-3 text-center border border-purple-200">
                  <Icon name="mdi:shield-check" class="w-5 h-5 md:w-6 md:h-6 mx-auto mb-1 text-purple-600" />
                  <p class="text-xs font-medium text-purple-800">2J Garantie</p>
                </div>
              </div>
            </div>
          </div>
          <div class="space-y-4 md:space-y-6">
            <div class="bg-white rounded-xl md:rounded-2xl shadow-lg p-6 md:p-8">
              <h1 class="text-3xl md:text-4xl xl:text-5xl font-bold mb-3 md:mb-4">
                {{ product.name }}
              </h1>
              
              <div class="flex items-baseline gap-2 md:gap-3 mb-4 md:mb-6">
                <span class="text-4xl md:text-5xl font-bold text-shop-blue-light">
                  {{ product.price }} €
                </span>
                <span class="text-gray-500 text-sm">inkl. MwSt.</span>
              </div>
              <div v-if="product.category" class="mb-4 md:mb-6">
                <h3 class="text-sm font-semibold mb-3 uppercase tracking-wide">
                  Kategorien
                </h3>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="category in product.category.split(', ')"
                    :key="category"
                    class="inline-flex items-center gap-1 bg-shop-blue-light/10 text-shop-blue-dark text-sm px-4 py-2 rounded-full font-medium border-2 border-shop-blue-light/20"
                  >
                    <Icon name="mdi:tag" class="w-4 h-4" />
                    {{ category }}
                  </span>
                </div>
              </div>
              <div class="mb-4 md:mb-6">
                <h3 class="text-sm font-semibold mb-3 uppercase tracking-wide">
                  Größe auswählen
                </h3>
                <div class="grid grid-cols-6 gap-2">
                  <button
                    v-for="size in [36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47]"
                    :key="size"
                    @click="selectedSize = size.toString()"
                    :class="[
                      'border-2 rounded-lg py-2 text-sm font-medium transition-all',
                      selectedSize === size.toString()
                        ? 'border-shop-blue-light bg-shop-blue-light text-white'
                        : 'border-gray-300 hover:border-shop-blue-light hover:bg-shop-blue-light/10'
                    ]"
                  >
                    {{ size }}
                  </button>
                </div>
              </div>
              <button
                @click="handleAddToCart"
                :disabled="!selectedSize || isAddingToCart"
                :class="[
                  'w-full py-3 md:py-4 px-6 rounded-xl font-bold text-base md:text-lg shadow-md transition-all duration-300 flex items-center justify-center gap-2 mb-3 md:mb-4',
                  selectedSize && !isAddingToCart
                    ? 'bg-shop-blue-light hover:bg-shop-blue-dark text-white hover:shadow-xl hover:scale-105'
                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                ]"
              >
                <Icon v-if="isAddingToCart" name="mdi:loading" class="w-6 h-6 text-white animate-spin" />
                <Icon v-else name="mdi:cart-plus" class="w-6 h-6 text-white" />
                {{ isAddingToCart ? 'Wird hinzugefügt...' : 'In den Warenkorb' }}
              </button>
              <p v-if="!selectedSize" class="text-sm text-signal-red text-center mb-4">
                Bitte wähle eine Größe aus
              </p>
              <p v-if="addToCartMessage" :class="['text-sm text-center mb-4', addToCartSuccess ? 'text-signal-green' : 'text-signal-red']">
                {{ addToCartMessage }}
              </p>
              <div class="pt-6 border-t border-gray-200">
                <ul class="space-y-3">
                  <li class="flex items-center gap-3">
                    <Icon name="mdi:truck-fast" class="w-5 h-5 text-shop-blue-light" />
                    <span>Kostenloser Versand ab 50€</span>
                  </li>
                  <li class="flex items-center gap-3">
                    <Icon name="mdi:autorenew" class="w-5 h-5 text-shop-blue-light" />
                    <span>30 Tage Rückgaberecht</span>
                  </li>
                  <li class="flex items-center gap-3">
                    <Icon name="mdi:shield-check" class="w-5 h-5 text-shop-blue-light" />
                    <span>2 Jahre Garantie</span>
                  </li>
                </ul>
              </div>
            </div>
            <div v-if="product.technical_specs" class="bg-white rounded-xl md:rounded-2xl shadow-lg p-6 md:p-8">
              <h2 class="text-xl md:text-2xl font-bold mb-3 md:mb-4 text-shop-blue-dark flex items-center gap-2">
                <Icon name="mdi:clipboard-list" class="w-6 h-6" />
                Technische Daten
              </h2>
              <div class="space-y-2">
                <div
                  v-for="(spec, index) in product.technical_specs.split('\n')"
                  :key="index"
                  class="flex items-start gap-3 py-2 border-b border-gray-100 last:border-0"
                >
                  <Icon name="mdi:circle-small" class="w-5 h-5 text-shop-blue-light flex-shrink-0" />
                  <span class="">{{ spec }}</span>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-xl md:rounded-2xl shadow-lg p-6 md:p-8">
              <h2 class="text-xl md:text-2xl font-bold mb-3 md:mb-4 text-shop-blue-dark flex items-center gap-2">
                <Icon name="mdi:store" class="w-6 h-6" />
                Verkäufer
              </h2>
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-shop-blue-light rounded-full flex items-center justify-center text-white text-xl md:text-2xl font-bold shadow-lg">
                  {{ product.seller_first_name.charAt(0) }}{{ product.seller_last_name.charAt(0) }}
                </div>
                <div>
                  <p class="font-semibold text-lg">
                    {{ product.seller_first_name }} {{ product.seller_last_name }}
                  </p>
                  <p class="text-gray-500 text-sm">Verifizierter Verkäufer</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="space-y-6 md:space-y-8">
          <div class="grid md:grid-cols-2 gap-6 md:gap-8">
            <div class="bg-white rounded-xl md:rounded-2xl shadow-lg p-6 md:p-8">
              <h2 class="text-xl md:text-2xl font-bold mb-3 md:mb-4 flex items-center gap-2">
                <Icon name="mdi:information" class="w-6 h-6" />
                Produktbeschreibung
              </h2>
              <p class="leading-relaxed text-lg">
                {{ product.description || "Keine Beschreibung verfügbar" }}
              </p>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl md:rounded-2xl shadow-lg p-6 md:p-8 border-2 border-green-200">
              <h2 class="text-xl md:text-2xl font-bold mb-3 md:mb-4 text-signal-green flex items-center gap-2">
                <Icon name="mdi:leaf" class="w-7 h-7" />
                Nachhaltig & Umweltfreundlich
              </h2>
              <ul class="space-y-3 text-signal-green">
                <li class="flex items-start gap-3">
                  <Icon name="mdi:check-circle" class="w-5 h-5 flex-shrink-0 mt-0.5 text-signal-green" />
                  <span>Hergestellt aus recycelten und nachhaltigen Materialien</span>
                </li>
                <li class="flex items-start gap-3">
                  <Icon name="mdi:check-circle" class="w-5 h-5 flex-shrink-0 mt-0.5 text-signal-green" />
                  <span>Klimaneutrale Produktion und Lieferung</span>
                </li>
                <li class="flex items-start gap-3">
                  <Icon name="mdi:check-circle" class="w-5 h-5 flex-shrink-0 mt-0.5 text-signal-green" />
                  <span>Fair Trade zertifiziert</span>
                </li>
              </ul>
            </div>
          </div>
          <div class="grid md:grid-cols-2 gap-6 md:gap-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl md:rounded-2xl shadow-lg p-6 md:p-8 border-2 border-blue-200">
              <h2 class="text-xl md:text-2xl font-bold mb-3 md:mb-4 flex items-center gap-2">
                <Icon name="mdi:spray-bottle" class="w-6 h-6" />
                Pflegehinweise
              </h2>
              <ul class="space-y-3">
                <li class="flex items-start gap-3">
                  <Icon name="mdi:water" class="w-5 h-5 flex-shrink-0 mt-0.5" />
                  <span>Mit feuchtem Tuch und mildem Reinigungsmittel säubern</span>
                </li>
                <li class="flex items-start gap-3">
                  <Icon name="mdi:white-balance-sunny" class="w-5 h-5 flex-shrink-0 mt-0.5" />
                  <span>An der Luft trocknen lassen, nicht im Trockner</span>
                </li>
                <li class="flex items-start gap-3">
                  <Icon name="mdi:washing-machine-off" class="w-5 h-5 flex-shrink-0 mt-0.5" />
                  <span>Nicht in der Waschmaschine waschen</span>
                </li>
                <li class="flex items-start gap-3">
                  <Icon name="mdi:home-outline" class="w-5 h-5 flex-shrink-0 mt-0.5" />
                  <span>Trocken und bei Raumtemperatur lagern</span>
                </li>
              </ul>
            </div>

            <div class="bg-white rounded-xl md:rounded-2xl shadow-lg p-6 md:p-8 border-2 border-gray-200">
              <h2 class="text-xl md:text-2xl font-bold mb-3 md:mb-4 text-shop-blue-dark flex items-center gap-2">
                <Icon name="mdi:package-variant" class="w-6 h-6" />
                Versand & Rückgabe
              </h2>
              <div class="space-y-4">
                <div>
                  <h3 class="font-semibold mb-2 flex items-center gap-2">
                    <Icon name="mdi:truck-delivery" class="w-5 h-5 text-shop-blue-light" />
                    Versand
                  </h3>
                  <p class=" text-sm pl-7">Kostenloser Versand ab 50€ • Lieferzeit 2-4 Werktage • Klimaneutraler Versand mit DHL GoGreen</p>
                </div>
                <div>
                  <h3 class="font-semibold mb-2 flex items-center gap-2">
                    <Icon name="mdi:keyboard-return" class="w-5 h-5 text-shop-blue-light" />
                    Rückgabe
                  </h3>
                  <p class=" text-sm pl-7">30 Tage Rückgaberecht • Kostenloser Rückversand • Schnelle Rückerstattung</p>
                </div>
                <div>
                  <h3 class="font-semibold mb-2 flex items-center gap-2">
                    <Icon name="mdi:credit-card" class="w-5 h-5 text-shop-blue-light" />
                    Zahlung
                  </h3>
                  <p class=" text-sm pl-7">PayPal • Kreditkarte • Klarna • Mastercard • Maestro</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useCart } from "~/composables/useCart";
import AlertMessage from '~/components/ui/AlertMessage.vue';

const route = useRoute();
const config = useRuntimeConfig();
const API_URL = config.public.apiUrl;

const { addItem, fetchCart } = useCart();

interface Product {
  id: number;
  name: string;
  description: string;
  price: number;
  category: string;
  image: string;
  seller_id: number;
  seller_first_name: string;
  seller_last_name: string;
  seller_email: string;
  technical_specs: string;
}

const product = ref<Product | null>(null);
const isLoading = ref(true);
const errorMessage = ref("");
const selectedSize = ref<string>("");
const isAddingToCart = ref(false);
const addToCartMessage = ref("");
const addToCartSuccess = ref(false);

const slug = route.params.slug as string;
const productId = parseInt(slug.split("-").pop() || "0");

async function fetchProduct() {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    const response = await $fetch<any>(`${API_URL}/product.php`);
    if (response.success) {
      const foundProduct = response.data.find(
        (p: Product) => p.id === productId
      );
      if (foundProduct) {
        product.value = foundProduct;
      } else {
        errorMessage.value = "Produkt nicht gefunden";
      }
    } else {
      errorMessage.value = "Fehler beim Laden des Produkts";
    }
  } catch (err) {
    console.error("Fehler beim Laden des Produkts:", err);
    errorMessage.value = "Netzwerk- oder Serverfehler";
  } finally {
    isLoading.value = false;
  }
}

async function handleAddToCart() {
  if (!product.value || !selectedSize.value) {
    addToCartMessage.value = "Bitte wähle eine Größe aus";
    addToCartSuccess.value = false;
    return;
  }

  isAddingToCart.value = true;
  addToCartMessage.value = "";

  try {
    const result = await addItem(product.value.id, selectedSize.value, 1);
    
    if (result.success) {
      addToCartSuccess.value = true;
      setTimeout(() => {
        isAddingToCart.value = false;
        addToCartMessage.value = result.message || "Zum Warenkorb hinzugefügt!";
      }, 1000);

      setTimeout(() => {
        addToCartMessage.value = "";
      }, 3000);
    } else {
      addToCartMessage.value = result.message || "Fehler beim Hinzufügen";
      addToCartSuccess.value = false;
      isAddingToCart.value = false;
    }
  } catch (error) {
    console.error('Error adding to cart:', error);
    addToCartMessage.value = "Fehler beim Hinzufügen zum Warenkorb";
    addToCartSuccess.value = false;
    isAddingToCart.value = false;
  }
}

onMounted(() => {
  fetchProduct();
});
</script>
