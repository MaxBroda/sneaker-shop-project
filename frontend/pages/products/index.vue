<template>
  <div class="bg-gradient-to-b from-shop-bright to-white pb-12">
    <div class="container mx-auto px-4 py-6 md:py-12 max-w-7xl">
      <div class="text-center mb-6 md:mb-8">
        <h1 class="text-3xl md:text-4xl font-bold mb-2 text-shop-blue-dark">
          Alle Produkte
        </h1>
        <p class="text-base text-gray-600">
          Entdecke unsere nachhaltige Sneaker-Kollektion
        </p>
      </div>

      <div class="bg-white p-4 md:p-6 shadow-lg rounded-2xl mb-6">
        <div class="flex flex-col gap-3 mb-4">
          <h2 class="text-lg font-semibold flex items-center gap-2">
            <Icon name="mdi:filter-variant" class="w-5 h-5 text-shop-blue-light" />
            Nach Kategorien filtern
          </h2>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <div class="relative flex items-center gap-2 col-span-2 md:col-span-1 md:col-start-4">
              <Icon name="mdi:sort" class="w-5 h-5 text-shop-blue-light flex-shrink-0 ml-2" />
              <select
                v-model="sortBy"
                class="flex-1 justify-end pl-2 pr-10 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-shop-blue-light bg-white transition-all hover:bg-blue-50 hover:border-shop-blue-light cursor-pointer appearance-none text-sm"
              >
                <option value="default" disabled>Filter</option>
                <option value="name-asc">Name (A-Z)</option>
                <option value="name-desc">Name (Z-A)</option>
                <option value="price-asc">Preis (niedrig → hoch)</option>
                <option value="price-desc">Preis (hoch → niedrig)</option>
                <option value="newest">Neueste zuerst</option>
              </select>
              <Icon
                name="mdi:chevron-down"
                class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
              />
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-4">
          <label
            v-for="category in availableCategories"
            :key="category"
            class="flex items-center space-x-2 p-2 border-2 border-gray-200 rounded-xl hover:bg-blue-50 hover:border-shop-blue-light cursor-pointer transition-all"
          >
            <input
              type="checkbox"
              :value="category"
              v-model="selectedCategories"
              class="w-4 h-4 accent-shop-blue-light border-gray-300 rounded focus:ring-shop-blue-dark"
            />
            <span class="text-sm font-medium">{{ category }}</span>
          </label>
        </div>

        <div
          v-if="selectedCategories.length > 0"
          class="pt-4 border-t border-gray-200 flex items-center justify-between"
        >
          <p class="text-sm font-medium text-gray-600">
            {{ filteredAndSortedProducts.length }} von {{ allProducts.length }} Produkten angezeigt
          </p>
          <button
            @click="selectedCategories = []"
            class="text-sm text-shop-blue-light hover:text-shop-blue-dark font-medium flex items-center gap-1"
          >
            <Icon name="mdi:close-circle" class="w-4 h-4" />
            Filter zurücksetzen
          </button>
        </div>
      </div>
      <div v-if="isLoading" class="text-center py-16">
        <Icon name="mdi:loading" class="w-12 h-12 animate-spin mx-auto text-shop-blue-light mb-4" />
        <p class="text-gray-600">Produkte werden geladen...</p>
      </div>
      <div
        v-else-if="errorMessage"
        class="bg-red-50 border-l-4 border-signal-red text-signal-red px-6 py-4 rounded-xl flex items-start gap-3"
      >
        <Icon name="mdi:alert-circle" class="w-6 h-6 flex-shrink-0 mt-0.5" />
        <div>
          <h3 class="font-semibold mb-1">Fehler</h3>
          <p>{{ errorMessage }}</p>
        </div>
      </div>
      <div
        v-else-if="filteredAndSortedProducts.length === 0"
        class="text-center py-16 bg-white rounded-2xl shadow-lg"
      >
        <Icon name="mdi:package-variant-closed" class="w-20 h-20 mx-auto mb-4 text-gray-300" />
        <p class="text-lg text-gray-600">
          {{
            selectedCategories.length > 0
              ? "Keine Produkte in den ausgewählten Kategorien gefunden."
              : "Noch keine Produkte verfügbar."
          }}
        </p>
      </div>
      <div
        v-else
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6"
      >
        <div
          v-for="product in filteredAndSortedProducts"
          :key="product.id"
          class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 group flex flex-col"
        >
          <div
            @click="goToProduct(product)"
            class="cursor-pointer bg-gradient-to-br from-gray-100 to-gray-200 aspect-[4/3] overflow-hidden relative"
          >
            <img
              :src="`${config.public.uploadsUrl}/${product.image}`"
              :alt="product.name"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            />
            <div v-if="product.tag_icon && product.tag_text" class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-bold flex items-center gap-1">
              <Icon :name="product.tag_icon" class="w-3 h-3" />
              {{ product.tag_text }}
            </div>
          </div>
          <div class="p-4 flex flex-col flex-1">
            <div @click="goToProduct(product)" class="cursor-pointer mb-3">
              <h3
                class="font-bold text-xl mb-1 group-hover:text-shop-blue-light transition-colors line-clamp-2"
                :title="product.name"
              >
                {{ product.name }}
              </h3>
              <p class="text-gray-500 text-sm mb-2 line-clamp-2">
                {{ product.description || "Keine Beschreibung verfügbar" }}
              </p>
            </div>
            <div class="flex items-center gap-2 mb-3">
              <span
                v-for="category in product.category?.split(', ')"
                :key="category"
                class="text-xs bg-shop-blue-light/10 text-shop-blue-dark py-1 rounded-full font-medium border border-shop-blue-light/20 flex-1 text-center flex items-center justify-center"
                style="min-width: calc(25% - 0.375rem)"
              >
                {{ category }}
              </span>
            </div>
            <div class="mt-auto">
              <div class="flex items-baseline justify-between mb-3">
                <span class="text-2xl font-bold text-shop-blue-light">
                  {{ product.price }} €
                </span>
                <span class="text-xs text-gray-500">inkl. MwSt.</span>
              </div>
              <button
                @click.stop="addToCart(product)"
                class="w-full bg-shop-blue-light hover:bg-shop-blue-dark text-white py-2.5 px-4 rounded-xl font-bold text-sm shadow-md hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2"
              >
                <Icon name="mdi:cart-plus" class="w-5 h-5 text-white" />
                In den Warenkorb
              </button>
              <div class="mt-3 pt-3 border-t border-gray-200">
                <p class="text-sm text-gray-600 flex items-center gap-2">
                  <Icon name="mdi:account-circle" class="w-4 h-4 text-shop-blue-light" />
                  <span class="font-medium">
                    {{ product.seller_first_name }} {{ product.seller_last_name }}
                  </span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
const config = useRuntimeConfig();
const API_URL = config.public.apiUrl;

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
  tag_icon?: string;
  tag_text?: string;
}

const availableCategories = ["Sneaker", "Running", "Training", "Outdoor"];

const allProducts = ref<Product[]>([]);
const isLoading = ref(true);
const errorMessage = ref("");
const selectedCategories = ref<string[]>([]);
const sortBy = ref("default");

async function fetchProducts() {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    const response = await $fetch<any>(`${API_URL}/products.php`);
    if (response.success) {
      allProducts.value = response.data;
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

function addToCart(product: Product) {
  console.log("Added to cart:", product);
  alert(`${product.name} wurde zum Warenkorb hinzugefügt!`);
}

const filteredAndSortedProducts = computed(() => {
  let filtered = allProducts.value;

  if (selectedCategories.value.length > 0) {
    filtered = filtered.filter((product) => {
      if (!product.category) return false;
      const productCategories = product.category
        .split(", ")
        .map((c) => c.trim());
      
      return selectedCategories.value.every((selectedCat) =>
        productCategories.includes(selectedCat)
      );
    });
  }

  const sorted = [...filtered];
  switch (sortBy.value) {
    case "name-asc":
      sorted.sort((a, b) => a.name.localeCompare(b.name));
      break;
    case "name-desc":
      sorted.sort((a, b) => b.name.localeCompare(a.name));
      break;
    case "price-asc":
      sorted.sort((a, b) => a.price - b.price);
      break;
    case "price-desc":
      sorted.sort((a, b) => b.price - a.price);
      break;
    case "newest":
      sorted.sort((a, b) => b.id - a.id);
      break;
    default:
      break;
  }

  return sorted;
});

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
  const route = useRoute();
  if (route.query.category) {
    const category = route.query.category as string;
    if (availableCategories.includes(category)) {
      selectedCategories.value = [category];
    }
  }
  
  fetchProducts();
});

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
