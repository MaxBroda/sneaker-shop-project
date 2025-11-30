<template>
  <div class="container mx-auto px-4 py-2 md:py-8 max-w-5xl">
    <h1 class="text-2xl font-bold mb-6 text-center">
      Alle Produkte
    </h1>
    <div class="bg-white p-4 shadow-md rounded-xl mb-6">
      <h2 class="text-lg font-semibold mb-3">
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
            class="w-4 h-4 accent-shop-blue-light border-gray-500 rounded focus:ring-shop-blue-dark"
          />
          <span class="text-sm">{{ category }}</span>
        </label>
      </div>
      <div
        v-if="selectedCategories.length > 0"
        class="mt-3 pt-3 border-t border-gray-200"
      >
        <p class="text-sm">
          {{ products.length }} von {{ allProducts.length }} Produkten angezeigt
        </p>
      </div>
    </div>
    <div v-if="isLoading" class="text-center py-12">
      <p class="">Produkte werden geladen...</p>
    </div>
    <div
      v-else-if="errorMessage"
      class="bg-red-100 border border-red-400 text-signal-red px-6 py-4 rounded-xl mb-4"
    >
      {{ errorMessage }}
    </div>
    <div
      v-else-if="products.length === 0"
      class="text-center py-12 bg-white rounded-xl shadow-md"
    >
      <p class="">
        {{
          selectedCategories.length > 0
            ? "Keine Produkte in den ausgewählten Kategorien gefunden."
            : "Noch keine Produkte verfügbar."
        }}
      </p>
    </div>
    <div
      v-else
      class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-2 md:gap-6"
    >
      <div
        v-for="product in products"
        :key="product.id"
        @click="goToProduct(product)"
        class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 cursor-pointer group"
      >
        <div
          class="bg-gradient-to-br from-gray-100 to-gray-200 h-48 flex items-center justify-center group-hover:from-blue-50 group-hover:to-blue-100 transition-all"
        >
          <span
            class="text-gray-500 text-4xl group-hover:scale-110 transition-transform"
            >👟</span
          >
        </div>
        <div class="p-4">
          <h3
            class="font-bold text-2xl md:text-xl mb-2 truncate group-hover:text-shop-blue-light transition-colors max-md:text-center"
            :title="product.name"
          >
            {{ product.name }}
          </h3>
          <p class="max-md:hidden text-gray-500 text-sm mb-3 line-clamp-2 min-h-[2.5rem]">
            {{ product.description || "Keine Beschreibung verfügbar" }}
          </p>
          <div class="flex flex-col md:flex-row justify-between items-center mb-3 max-md:gap-4">
            <span class="text-2xl font-bold text-shop-blue-light order-2 md:order-1">
              {{ product.price }} €
            </span>
            <span
              v-if="product.category"
              class="text-xs bg-blue-100 px-2 py-1 rounded-full font-medium order-1 md:order-2 max-md:w-full max-md:text-center"
            >
              {{ product.category }}
            </span>
          </div>
          <div class="border-t border-gray-200 pt-3 mt-3">
            <p class="text-sm text-gray-500">
              Verkäufer:
              <span class="font-medium">
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
  // Check for category query parameter
  const route = useRoute();
  if (route.query.category) {
    const category = route.query.category as string;
    if (availableCategories.includes(category)) {
      selectedCategories.value = [category];
    }
  }
  
  fetchProducts();
});

// Watch for route query changes
watch(
  () => useRoute().query.category,
  (newCategory) => {
    if (newCategory && typeof newCategory === 'string') {
      if (availableCategories.includes(newCategory)) {
        selectedCategories.value = [newCategory];
      }
    } else {
      selectedCategories.value = [];
    }
  }
);
</script>
