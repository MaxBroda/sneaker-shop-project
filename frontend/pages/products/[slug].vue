<template>
  <div class="container mx-auto px-4 py-8 max-w-4xl">
    <button
      @click="navigateTo('/products')"
      class="mb-6 flex items-center hover:text-blue-600 transition-colors"
    >
      <span class="mr-2">←</span> Zurück zu allen Produkten
    </button>
    <div v-if="isLoading" class="text-center py-12">
      <p class="">Produkt wird geladen...</p>
    </div>
    <div
      v-else-if="errorMessage"
      class="bg-red-100 border border-red-400 text-signal-red px-6 py-4 rounded-xl mb-4"
    >
      {{ errorMessage }}
    </div>
    <div
      v-else-if="product"
      class="bg-white shadow-md rounded-xl overflow-hidden"
    >
      <div class="grid md:grid-cols-2 gap-6">
        <div
          class="bg-gradient-to-br from-gray-100 to-gray-200 h-100 flex items-center justify-center"
        >
          <span class="text-gray-500 text-8xl">👟</span>
        </div>
        <div class="p-6">
          <h1 class="text-3xl font-bold mb-4">
            {{ product.name }}
          </h1>

          <div class="mb-6">
            <span class="text-4xl font-bold text-blue-600">
              {{ product.price }} €
            </span>
          </div>

          <div v-if="product.category" class="mb-6">
            <h3 class="text-sm font-medium mb-2">Kategorien:</h3>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="cat in product.category.split(', ')"
                :key="cat"
                class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full font-medium"
              >
                {{ cat }}
              </span>
            </div>
          </div>
          <div class="mb-6">
            <h3 class="text-sm font-medium mb-2">
              Beschreibung:
            </h3>
            <p class="text-gray-500">
              {{ product.description || "Keine Beschreibung verfügbar" }}
            </p>
          </div>
          <div class="border-t border-gray-200 pt-4">
            <h3 class="text-sm font-medium mb-2">Verkäufer:</h3>
            <p class="text-gray-500">
              {{ product.seller_first_name }} {{ product.seller_last_name }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
const route = useRoute();
const API_URL = "http://localhost:8080/api";

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
}

const product = ref<Product | null>(null);
const isLoading = ref(true);
const errorMessage = ref("");

// Extract product ID from slug (last part after final dash)
const slug = route.params.slug as string;
const productId = parseInt(slug.split("-").pop() || "0");

async function fetchProduct() {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    const response = await $fetch<any>(`${API_URL}/products.php`);
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

onMounted(() => {
  fetchProduct();
});
</script>
