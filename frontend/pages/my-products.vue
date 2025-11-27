<template>
  <div class="container mx-auto px-4 py-2 md:py-8 max-w-5xl">
    <h1 class="text-2xl font-bold mb-6 text-center">Meine Produkte</h1>

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

    <ConfirmDialog
      :isOpen="showDeleteConfirm"
      title="Produkt löschen"
      message="Möchten Sie dieses Produkt wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden."
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />

    <div class="bg-white p-6 shadow-md rounded-xl">
      <div
        v-if="myProducts.length === 0"
        class="text-center py-8 bg-gray-50 rounded-lg"
      >
        <p>Sie haben noch keine Produkte hinzugefügt.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="product in myProducts"
          :key="product.id"
          class="border border-gray-200 rounded-xl p-4 hover:shadow-lg transition-all duration-300 bg-white"
        >
          <h3 class="font-semibold text-lg mb-2">
            {{ product.name }}
          </h3>
          <p class="text-gray-500 text-sm mb-3 line-clamp-2 min-h-[2.5rem]">
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
            class="w-full mt-2 bg-signal-red bg-signal-red-hover text-white py-2 px-4 rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow-md"
          >
            Löschen
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import ConfirmDialog from '~/components/modals/ConfirmDialog.vue';

const { user, token } = useAuth();
const API_URL = "http://localhost:8080/api";

const myProducts = ref<any[]>([]);
const errorMessage = ref("");
const successMessage = ref("");
const showDeleteConfirm = ref(false);
const productToDelete = ref<number | null>(null);

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
    errorMessage.value = "Fehler beim Laden der Produkte";
  }
}

onMounted(() => {
  if (typeof window !== "undefined") {
    const savedUser = localStorage.getItem("user");

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
  }
});

async function deleteProduct(productId: number) {
  productToDelete.value = productId;
  showDeleteConfirm.value = true;
}

function cancelDelete() {
  showDeleteConfirm.value = false;
  productToDelete.value = null;
}

async function confirmDelete() {
  if (productToDelete.value === null) return;

  showDeleteConfirm.value = false;

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
      `${API_URL}/products.php?id=${productToDelete.value}`,
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
  } finally {
    productToDelete.value = null;
  }
}
</script>