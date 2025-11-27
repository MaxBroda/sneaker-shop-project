<template>
  <div class="container mx-auto px-4 py-8 max-w-5xl">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 text-center">
      Alle Produkte
    </h1>
    <!-- Category Filter -->
    <div class="bg-white p-4 shadow-md rounded-xl mb-6">
      <h2 class="text-lg font-semibold mb-3 text-gray-800">
        Nach Kategorien filtern
      </h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
        <label
          v-for="category in availableCategories"
          :key="category"
          class="flex items-center space-x-2 p-2 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition-all"
        >
          <input
            type="checkbox"
            :value="category"
            v-model="selectedCategories"
            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
          />
          <span class="text-sm">{{ category }}</span>
        </label>
      </div>
      <div
        v-if="selectedCategories.length > 0"
        class="mt-3 pt-3 border-t border-gray-200"
      >
        <p class="text-sm text-gray-600">
          {{ products.length }} von {{ allProducts.length }} Produkten angezeigt
        </p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-12">
      <p class="text-gray-600">Produkte werden geladen...</p>
    </div>

    <!-- Error Message -->
    <div
      v-else-if="errorMessage"
      class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-4"
    >
      {{ errorMessage }}
    </div>

    <!-- No Products -->
    <div
      v-else-if="products.length === 0"
      class="text-center py-12 bg-white rounded-xl shadow-md"
    >
      <p class="text-gray-600">
        {{
          selectedCategories.length > 0
            ? "Keine Produkte in den ausgewählten Kategorien gefunden."
            : "Noch keine Produkte verfügbar."
        }}
      </p>
    </div>

    <!-- Products Grid -->
    <div
      v-else
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
    >
      <div
        v-for="product in products"
        :key="product.id"
        @click="goToProduct(product)"
        class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 cursor-pointer group"
      >
        <!-- Product Image Placeholder -->
        <div
          class="bg-gradient-to-br from-gray-100 to-gray-200 h-48 flex items-center justify-center group-hover:from-blue-50 group-hover:to-blue-100 transition-all"
        >
          <span
            class="text-gray-400 text-4xl group-hover:scale-110 transition-transform"
            >👟</span
          >
        </div>

        <!-- Product Info -->
        <div class="p-4">
          <h3
            class="font-bold text-lg mb-2 text-gray-800 truncate group-hover:text-blue-600 transition-colors"
            :title="product.name"
          >
            {{ product.name }}
          </h3>

          <p class="text-gray-600 text-sm mb-3 line-clamp-2 min-h-[2.5rem]">
            {{ product.description || "Keine Beschreibung verfügbar" }}
          </p>

          <!-- Price and Category -->
          <div class="flex justify-between items-center mb-3">
            <span class="text-2xl font-bold text-blue-600">
              {{ parseFloat(product.price.toString()).toFixed(2) }} €
            </span>
            <span
              v-if="product.category"
              class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium"
            >
              {{ product.category }}
            </span>
          </div>

          <!-- Seller Info -->
          <div class="border-t border-gray-200 pt-3 mt-3">
            <p class="text-sm text-gray-500">
              Verkäufer:
              <span class="font-medium text-gray-700">
                {{ product.seller_first_name }} {{ product.seller_last_name }}
              </span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
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

const availableCategories = ["Sneaker", "Laufschuhe", "Training", "Outdoor"];

const products = ref<Product[]>([]);
const allProducts = ref<Product[]>([]);
const isLoading = ref(true);
const errorMessage = ref("");
const selectedCategories = ref<string[]>([]);

// Fetch all products
async function fetchProducts() {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    const response = await $fetch<any>(`${API_URL}/products.php`);
    if (response.success) {
      allProducts.value = response.data;
      filterProducts();
    } else {
      errorMessage.value = "Fehler beim Laden der Produkte";
    }
  } catch (err) {
    console.error("Fehler beim Laden der Produkte:", err);
    errorMessage.value = "Netzwerk- oder Serverfehler";
  } finally {
    isLoading.value = false;
  }
}

// Filter products based on selected categories
function filterProducts() {
  if (selectedCategories.value.length === 0) {
    products.value = allProducts.value;
  } else {
    products.value = allProducts.value.filter((product) => {
      if (!product.category) return false;

      // Check if product has any of the selected categories
      const productCategories = product.category
        .split(", ")
        .map((c) => c.trim());
      return selectedCategories.value.some((selectedCat) =>
        productCategories.includes(selectedCat)
      );
    });
  }
}

// Watch for category filter changes
watch(
  selectedCategories,
  () => {
    filterProducts();
  },
  { deep: true }
);

// Create URL-friendly slug from product name
function createSlug(name: string, id: number): string {
  const slug = name
    .toLowerCase()
    .replace(/ä/g, "ae")
    .replace(/ö/g, "oe")
    .replace(/ü/g, "ue")
    .replace(/ß/g, "ss")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "");
  return `${slug}-${id}`;
}

function goToProduct(product: Product) {
  const slug = createSlug(product.name, product.id);
  navigateTo(`/products/${slug}`);
}

onMounted(() => {
  fetchProducts();
});
</script>
