<template>
    <ConfirmDialog
      :isOpen="showDeleteConfirm"
      title="Produkt löschen"
      message="Möchten Sie dieses Produkt wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden."
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  <div class="bg-white p-6 shadow-md rounded-xl h-full">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-shop-blue-dark">Meine Produkte</h2>
      <NuxtLink
        v-if="myProducts.length > 0"
        to="/add-product"
        class="px-6 py-2 bg-shop-blue-light text-white rounded-lg hover:bg-shop-blue-dark transition"
      >
        Neues Produkt
      </NuxtLink>
    </div>

    <div
      v-if="myProducts.length === 0"
      class="text-center py-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-500"
    >
      <Icon name="mdi:package-variant" class="w-16 h-16 mx-auto mb-4 text-gray-500" />
      <p class="mb-6 text-lg">Sie haben noch keine Produkte hinzugefügt.</p>
      <NuxtLink
        to="/add-product"
        class="inline-block px-6 py-2 bg-shop-blue-light text-white rounded-lg hover:bg-shop-blue-dark transition"
      >
        Erstes Produkt hinzufügen
      </NuxtLink>
    </div>

    <div v-else>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="product in myProducts"
          :key="product.id"
          class="border-2 border-gray-200 rounded-xl p-5 transition-all duration-300 bg-white hover:shadow-lg hover:border-shop-blue-light group"
        >
          <div class="mb-4">
            <h3 class="font-bold text-lg mb-2 text-shop-blue-dark group-hover:text-shop-blue-light transition-colors">
              {{ product.name }}
            </h3>
            <p class="text-sm mb-3 line-clamp-2 min-h-[2.5rem]">
              {{ product.description || "Keine Beschreibung" }}
            </p>
          </div>

          <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200">
            <span class="text-2xl font-bold bg-gradient-to-r from-shop-blue-light to-shop-blue-dark bg-clip-text text-transparent">
              {{ product.price }} €
            </span>
            <span
              v-if="product.category"
              class="text-xs bg-shop-blue-light/10 text-shop-blue-dark px-3 py-1 rounded-full font-semibold border border-shop-blue-light/20"
            >
              {{ product.category }}
            </span>
          </div>

          <button
            @click="deleteProduct(product.id)"
            class="w-full bg-signal-red hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow-md"
          >
            Löschen
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import ConfirmDialog from "~/components/modals/ConfirmDialog.vue";

const { user, token } = useAuth();
const API_URL = "http://localhost:8080/api";

const myProducts = ref<any[]>([]);
const showDeleteConfirm = ref(false);
const productToDelete = ref<number | null>(null);

const emit = defineEmits<{
  success: [message: string];
  error: [message: string];
}>();

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
    emit("error", "Fehler beim Laden der Produkte");
  }
}

onMounted(() => {
  fetchMyProducts();
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
    emit("error", "Sie sind nicht angemeldet. Bitte melden Sie sich erneut an.");
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
      emit("success", "Produkt erfolgreich gelöscht!");
      await fetchMyProducts();
    } else {
      emit("error", response.message || "Fehler beim Löschen");
    }
  } catch (err: any) {
    console.error("Fehler beim Löschen:", err);
    emit("error", err?.data?.message || "Fehler beim Löschen des Produkts");
  } finally {
    productToDelete.value = null;
  }
}
</script>
