<template>
  <div>
    <ConfirmDialog
      :isOpen="showDeleteConfirm"
      :title="'Artikel entfernen'"
      :message="'Möchtest du diesen Artikel wirklich aus dem Warenkorb entfernen?'"
      @confirm="confirmRemove"
      @cancel="cancelRemove"
    />
    <section class="text-white md:py-12">
      <div class="container mx-auto px-4 max-w-6xl text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Warenkorb</h1>
        <p class="text-lg opacity-90">{{ cartItemCount || 0 }} Artikel in deinem Warenkorb</p>
      </div>
    </section>

    <div class="container mx-auto px-4 max-w-6xl py-8">
      <div v-if="cartItems.length === 0" class="text-center py-16">
        <Icon name="mdi:cart-outline" class="w-24 h-24 mx-auto mb-4" />
        <h2 class="text-2xl font-semibold mb-4">Dein Warenkorb ist leer</h2>
        <p class="text-gray-500 mb-6">Füge Produkte zu deinem Warenkorb hinzu, um mit dem Einkaufen zu beginnen.</p>
        <NuxtLink
          to="/products"
          class="inline-block bg-shop-blue-dark text-white px-8 py-3 rounded-lg hover:bg-shop-blue-light transition-all"
        >
          Produkte entdecken
        </NuxtLink>
      </div>

      <div v-else class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-4">
          <div
            v-for="item in cartItems"
            :key="item.id"
            class="bg-white rounded-xl shadow-md p-4 sm:p-6 hover:shadow-lg transition-all"
          >
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
              <img
                :src="item.image || `${config.public.uploadsUrl}/placeholder.jpg`"
                :alt="item.name"
                class="w-full sm:w-32 h-32 object-cover rounded-lg border border-shop-blue-dark"
              />
              <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <h3 class="font-bold text-lg mb-1">{{ item.name }}</h3>
                    <p class="text-sm text-gray-500">Größe: {{ item.size }}</p>
                  </div>
                  <button
                    @click="showRemoveDialog(item.id)"
                    class="text-gray-300 hover:text-signal-red transition-all"
                  >
                    <Icon name="mdi:delete-outline" class="w-6 h-6" />
                  </button>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4 gap-3">
                  <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500">Menge:</span>
                    <div class="flex items-center gap-2">
                      <button
                        @click="decreaseQuantity(item.id)"
                        class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-all"
                      >
                        <Icon name="mdi:minus" class="w-5 h-5" />
                      </button>
                      <span class="text-lg font-semibold w-12 text-center">{{ item.quantity }}</span>
                      <button
                        @click="increaseQuantity(item.id)"
                        class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-all"
                      >
                        <Icon name="mdi:plus" class="w-5 h-5" />
                      </button>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm text-gray-500">Preis pro Stück: {{ formatPrice(item.price) }}</p>
                    <p class="font-bold text-xl text-shop-blue-dark">{{ formatPrice(item.price * item.quantity) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="lg:col-span-1">
          <div class="bg-white rounded-xl shadow-md p-6 sticky top-4">
            <h2 class="font-bold text-xl mb-6">Bestellübersicht</h2>
            
            <div class="space-y-3 mb-6">
              <div class="flex justify-between text-gray-500">
                <span>Zwischensumme ({{ cartItemCount || 0 }} Artikel)</span>
                <span>{{ formatPrice(subtotal || 0) }}</span>
              </div>
              <div class="flex justify-between text-gray-500">
                <span>Versand</span>
                <span>{{ shippingCost > 0 ? formatPrice(shippingCost) : 'Kostenlos' }}</span>
              </div>
              <div v-if="discount > 0" class="flex justify-between text-signal-green">
                <span>Rabatt</span>
                <span>-{{ formatPrice(discount) }}</span>
              </div>
            </div>

            <div class="border-t border-gray-200 pt-4 mb-6">
              <div class="flex justify-between items-center">
                <span class="font-bold text-lg">Gesamt:</span>
                <span class="font-bold text-2xl text-shop-blue-dark">{{ formatPrice(totalPrice || 0) }}</span>
              </div>
              <p class="text-xs text-gray-500 mt-1">Inkl. MwSt.</p>
            </div>

            <NuxtLink
              to="/checkout"
              class="block w-full bg-signal-red text-white text-center py-3 rounded-lg hover:opacity-90 transition-all font-semibold mb-3"
            >
              Zur Kasse
            </NuxtLink>
            <NuxtLink
              to="/products"
              class="block w-full bg-shop-blue-dark text-white text-center py-3 rounded-lg hover:bg-shop-blue-light transition-all"
            >
              Weiter einkaufen
            </NuxtLink>

            <div class="mt-6 pt-6 border-t border-gray-200">
              <div class="flex items-center gap-3 text-sm text-gray-500 mb-3">
                <Icon name="mdi:truck-delivery-outline" class="w-5 h-5 text-shop-blue-light" />
                <span>Kostenloser Versand ab 50€</span>
              </div>
              <div class="flex items-center gap-3 text-sm text-gray-500 mb-3">
                <Icon name="mdi:shield-check-outline" class="w-5 h-5 text-shop-blue-light" />
                <span>Sichere Bezahlung</span>
              </div>
              <div class="flex items-center gap-3 text-sm text-gray-500">
                <Icon name="mdi:arrow-u-left-top" class="w-5 h-5 text-shop-blue-light" />
                <span>30 Tage Rückgaberecht</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import ConfirmDialog from "../components/modals/ConfirmDialog.vue";
import { useCart } from "~/composables/useCart";

const config = useRuntimeConfig();

const { 
  items: cartItems, 
  itemCount: cartItemCount, 
  total: cartTotal,
  isLoading,
  fetchCart, 
  updateQuantity,
  removeItem,
} = useCart();

const showDeleteConfirm = ref(false);
const itemToDelete = ref<number | null>(null);

const subtotal = computed(() => {
  return cartTotal.value;
});

const shippingCost = computed(() => {
  return subtotal.value >= 50 ? 0 : 4.99;
});

const discount = computed(() => {
  return 0;
});

const totalPrice = computed(() => {
  return subtotal.value + shippingCost.value - discount.value;
});

function formatPrice(price: number): string {
  return `${price.toFixed(2)} €`;
}

async function increaseQuantity(itemId: number) {
  const item = cartItems.value.find(i => i.id === itemId);
  if (item) {
    await updateQuantity(itemId, item.quantity + 1);
  }
}

async function decreaseQuantity(itemId: number) {
  const item = cartItems.value.find(i => i.id === itemId);
  if (item && item.quantity > 1) {
    await updateQuantity(itemId, item.quantity - 1);
  }
}

async function removeCartItem(itemId: number) {
  await removeItem(itemId);
}

function showRemoveDialog(itemId: number) {
  itemToDelete.value = itemId;
  showDeleteConfirm.value = true;
}

async function confirmRemove() {
  if (itemToDelete.value !== null) {
    await removeCartItem(itemToDelete.value);
    itemToDelete.value = null;
  }
  showDeleteConfirm.value = false;
}

function cancelRemove() {
  showDeleteConfirm.value = false;
  itemToDelete.value = null;
}

onMounted(() => {
  fetchCart();
});
</script>
