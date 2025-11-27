<template>
  <div class="container mx-auto px-4 py-2 md:py-8 max-w-5xl">
    <h1 class="text-2xl font-bold mb-6 text-center">Produkt hinzufügen</h1>

    <div class="bg-white p-6 shadow-md rounded-xl mb-6">
      <div
        v-if="errorMessage"
        class="bg-red-100 border border-red-400 text-signal-red px-4 py-3 rounded mb-4"
      >
        {{ errorMessage }}
      </div>
      <div
        v-if="successMessage"
        class="bg-green-100 border border-green-400 text-signal-green px-4 py-3 rounded mb-4"
      >
        {{ successMessage }}
      </div>

      <form @submit.prevent="addProduct" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">
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
          <label class="block text-sm font-medium mb-1">
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
          <label class="block text-sm font-medium mb-1">
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
          <label class="block text-sm font-medium mb-2">
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


  </div>
</template>
<script setup lang="ts">
const { user, token } = useAuth();
const API_URL = "http://localhost:8080/api";

const isLoading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

const availableCategories = ["Sneaker", "Laufschuhe", "Training", "Outdoor"];

const form = reactive({
  name: "",
  description: "",
  price: "",
  categories: [] as string[],
});

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
      
      setTimeout(() => {
        successMessage.value = "";
      }, 3000);
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
</script>
