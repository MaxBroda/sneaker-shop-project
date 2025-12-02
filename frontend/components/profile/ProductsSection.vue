<template>
    <ConfirmDialog
      :isOpen="showDeleteConfirm"
      title="Produkt löschen"
      message="Möchten Sie dieses Produkt wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden."
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
    
    <EditProductModal
      v-if="showEditModal"
      :product="selectedProduct"
      @close="closeEditModal"
      @updated="handleProductUpdated"
    />
    
  <div class="bg-white p-4 md:p-6 shadow-md rounded-xl">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg md:text-xl font-bold text-shop-blue-dark">Meine Produkte</h2>
      <NuxtLink
        v-if="myProducts.length > 0"
        to="/add-product"
        class="px-3 py-1.5 md:px-6 md:py-2 text-sm md:text-base bg-shop-blue-light text-white rounded-lg hover:bg-shop-blue-dark transition"
      >
        <span class="hidden md:inline text-white">Neues Produkt</span>
        <Icon name="mdi:plus" class="md:hidden w-5 h-5 flex justify-center items-center text-white" />
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
        class="inline-block px-4 py-2 md:px-6 md:py-2 text-sm md:text-base bg-shop-blue-light text-white rounded-lg hover:bg-shop-blue-dark transition"
      >
        Erstes Produkt hinzufügen
      </NuxtLink>
    </div>

    <div v-else>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="product in myProducts"
          :key="product.id"
          class="border-2 border-gray-200 rounded-xl p-5 transition-all duration-300 bg-white hover:shadow-lg hover:border-shop-blue-light group flex flex-col"
        >
          <div class="mb-4 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg overflow-hidden aspect-[5/4]">
            <img
              :src="`${config.public.uploadsUrl}/${product.image}`"
              :alt="product.name"
              loading="lazy"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
          </div>

          <div class="mb-2 flex-grow">
            <h3 class="font-bold text-lg mb-1 text-shop-blue-dark group-hover:text-shop-blue-light transition-colors">
              {{ product.name }}
            </h3>
            <p class="text-sm mb-2 line-clamp-2">
              {{ product.description || "Keine Beschreibung" }}
            </p>
          </div>

          <div class="mt-auto">
            <div class="mb-3 pb-3 border-b border-gray-200">
              <div class="flex justify-between items-center">
                <span class="text-2xl font-bold text-shop-blue-light">
                  {{ product.price }} €
                </span>
              </div>
            </div>

            <div v-if="product.category" class="flex flex-wrap gap-1 mb-3 min-h-[3rem]">
              <span
                v-for="cat in product.category.split(', ')"
                :key="cat"
                class="text-xs bg-shop-blue-light/10 text-shop-blue-dark px-2 py-1 rounded-full font-semibold border border-shop-blue-light/20 flex-1 min-w-[calc(50%-0.25rem)] text-center h-fit"
              >
                {{ cat }}
              </span>
            </div>
          </div>

          <button
            @click="openEditModal(product)"
            class="w-full bg-shop-blue-light hover:bg-shop-blue-dark text-white py-2 px-4 rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow-md flex items-center justify-center gap-2"
          >
            <Icon name="mdi:pencil" class="w-4 h-4 text-white" />
            Bearbeiten
          </button>
          
          <button
            @click="deleteProduct(product.id)"
            class="w-full mt-2 bg-signal-red hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow-md"
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
import EditProductModal from "~/components/modals/EditProductModal.vue";

const { user, token } = useAuth();
const config = useRuntimeConfig();
const API_URL = config.public.apiUrl;

const myProducts = ref<any[]>([]);
const showDeleteConfirm = ref(false);
const productToDelete = ref<number | null>(null);
const showEditModal = ref(false);
const selectedProduct = ref<any>(null);

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

function openEditModal(product: any) {
  selectedProduct.value = product;
  showEditModal.value = true;
}

function closeEditModal() {
  showEditModal.value = false;
  selectedProduct.value = null;
}

function handleProductUpdated() {
  closeEditModal();
  emit("success", "Produkt erfolgreich aktualisiert!");
  fetchMyProducts();
}
</script>
