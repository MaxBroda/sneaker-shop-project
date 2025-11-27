<template>
  <div class="container mx-auto px-4 py-8 max-w-5xl">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Produkt hinzufügen</h1>

    <div class="bg-white p-6 shadow-md rounded-xl mb-6">
      <h2 class="text-xl font-semibold mb-4 text-gray-800">
        Neues Produkt erstellen
      </h2>

      <div
        v-if="errorMessage"
        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
      >
        {{ errorMessage }}
      </div>
      <div
        v-if="successMessage"
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"
      >
        {{ successMessage }}
      </div>

      <form @submit.prevent="addProduct" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Produktname *
          </label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            placeholder="z.B. Nike Air Max"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Beschreibung
          </label>
          <textarea
            v-model="form.description"
            rows="3"
            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
            placeholder="Produktbeschreibung..."
          ></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Preis (€) *
          </label>
          <input
            v-model="form.price"
            @input="formatPriceInput"
            type="text"
            inputmode="decimal"
            required
            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            placeholder="99.99"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Kategorien (mehrfach auswählbar)
          </label>
          <div class="grid grid-cols-2 gap-2">
            <label
              v-for="category in availableCategories"
              :key="category"
              class="flex items-center space-x-2 p-2 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition-all"
            >
              <input
                type="checkbox"
                :value="category"
                v-model="form.categories"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              />
              <span class="text-sm">{{ category }}</span>
            </label>
          </div>
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-all duration-200 shadow-sm hover:shadow-md"
        >
          {{ isLoading ? "Wird hinzugefügt..." : "Produkt hinzufügen" }}
        </button>
      </form>
    </div>

    <div class="bg-white p-6 shadow-md rounded-xl">
      <h2 class="text-xl font-semibold mb-4 text-gray-800">Meine Produkte</h2>

      <div
        v-if="myProducts.length === 0"
        class="text-gray-500 text-center py-8 bg-gray-50 rounded-lg"
      >
        <p>Sie haben noch keine Produkte hinzugefügt.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="product in myProducts"
          :key="product.id"
          class="border border-gray-200 rounded-xl p-4 hover:shadow-lg transition-all duration-300 bg-white"
        >
          <h3 class="font-semibold text-lg mb-2 text-gray-800">
            {{ product.name }}
          </h3>
          <p class="text-gray-600 text-sm mb-3 line-clamp-2 min-h-[2.5rem]">
            {{ product.description || "Keine Beschreibung" }}
          </p>

          <div class="flex justify-between items-center mb-3">
            <span class="text-xl font-bold text-blue-600">
              {{ parseFloat(product.price).toFixed(2) }} €
            </span>
            <span
              v-if="product.category"
              class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium"
            >
              {{ product.category }}
            </span>
          </div>

          <button
            @click="deleteProduct(product.id)"
            class="w-full mt-2 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-all duration-200 font-medium shadow-sm hover:shadow-md"
          >
            Löschen
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
const { user, token } = useAuth();
const API_URL = "http://localhost:8080/api";

const myProducts = ref<any[]>([]);
const isLoading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const isInitialized = ref(false);

const availableCategories = ["Sneaker", "Laufschuhe", "Training", "Outdoor"];

const form = reactive({
  name: "",
  description: "",
  price: "",
  categories: [] as string[],
});

async function fetchMyProducts() {
  if (!user.value) return;

  try {
    const response = await $fetch<any>(
      `${API_URL}/products.php?seller_id=${user.value.id}`
    );
    if (response.success) {
      myProducts.value = response.data;
    }
  } catch (err) {
    console.error("Fehler beim Laden der Produkte:", err);
  }
}

onMounted(() => {
  if (typeof window !== "undefined") {
    const savedToken = localStorage.getItem("token");
    const savedUser = localStorage.getItem("user");

    console.log("Checking auth - Token:", savedToken ? "exists" : "missing");
    console.log(
      "Checking auth - User:",
      savedUser ? JSON.parse(savedUser) : "missing"
    );

    if (savedUser) {
      const parsedUser = JSON.parse(savedUser);
      if (parsedUser.role !== "seller") {
        navigateTo("/");
        return;
      }
    } else {
      navigateTo("/login");
      return;
    }

    fetchMyProducts();
    isInitialized.value = true;
  }
});

async function addProduct() {
  errorMessage.value = "";
  successMessage.value = "";

  if (!form.name || !form.price) {
    errorMessage.value = "Name und Preis sind erforderlich";
    return;
  }

  const priceRegex = /^\d+(\.\d{1,2})?$/;
  if (!priceRegex.test(form.price)) {
    errorMessage.value =
      "Preis muss eine gültige Zahl mit maximal 2 Dezimalstellen sein";
    return;
  }

  const priceValue = parseFloat(form.price);

  if (priceValue <= 0) {
    errorMessage.value = "Der Preis muss größer als 0 sein";
    return;
  }

  if (priceValue > 999999.99) {
    errorMessage.value = "Der Preis ist zu hoch (Maximum: 999.999,99 €)";
    return;
  }

  let authToken = token.value;
  if (!authToken && typeof window !== "undefined") {
    authToken = localStorage.getItem("token");
  }

  if (!authToken) {
    errorMessage.value =
      "Sie sind nicht angemeldet. Bitte melden Sie sich erneut an.";
    navigateTo("/login");
    return;
  }

  console.log("Sending request with token:", authToken);

  isLoading.value = true;

  try {
    const response = await $fetch<any>(`${API_URL}/products.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${authToken}`,
      },
      body: {
        name: form.name,
        description: form.description,
        price: priceValue,
        category: form.categories.join(", "),
      },
    });

    if (response.success) {
      successMessage.value = "Produkt erfolgreich hinzugefügt!";
      form.name = "";
      form.description = "";
      form.price = "";
      form.categories = [];
      await fetchMyProducts();
    } else {
      errorMessage.value = response.message || "Fehler beim Hinzufügen";
    }
  } catch (err: any) {
    console.error("Fehler:", err);
    errorMessage.value = err?.data?.message || "Netzwerk- oder Serverfehler";
  } finally {
    isLoading.value = false;
  }
}

function formatPriceInput(event: Event) {
  const input = event.target as HTMLInputElement;
  let value = input.value;

  value = value.replace(/,/g, ".");

  value = value.replace(/[^\d.]/g, "");

  const parts = value.split(".");
  if (parts.length > 2) {
    value = parts[0] + "." + parts.slice(1).join("");
  }

  if (parts.length === 2 && parts[1] && parts[1].length > 2) {
    value = parts[0] + "." + parts[1].substring(0, 2);
  }

  form.price = value;
}

async function deleteProduct(productId: number) {
  if (!confirm("Möchten Sie dieses Produkt wirklich löschen?")) {
    return;
  }

  let authToken = token.value;
  if (!authToken && typeof window !== "undefined") {
    authToken = localStorage.getItem("token");
  }

  if (!authToken) {
    errorMessage.value =
      "Sie sind nicht angemeldet. Bitte melden Sie sich erneut an.";
    navigateTo("/login");
    return;
  }

  try {
    const response = await $fetch<any>(
      `${API_URL}/products.php?id=${productId}`,
      {
        method: "DELETE",
        headers: {
          Authorization: `Bearer ${authToken}`,
        },
      }
    );

    if (response.success) {
      successMessage.value = "Produkt erfolgreich gelöscht!";
      await fetchMyProducts();

      setTimeout(() => {
        successMessage.value = "";
      }, 3000);
    } else {
      errorMessage.value = response.message || "Fehler beim Löschen";
    }
  } catch (err: any) {
    console.error("Fehler beim Löschen:", err);
    errorMessage.value =
      err?.data?.message || "Fehler beim Löschen des Produkts";
  }
}
</script>
