<template>
  <div>
    <ConfirmDialog
      :isOpen="showDeleteConfirm"
      :title="'Artikel entfernen'"
      :message="'Möchtest du diesen Artikel wirklich aus dem Warenkorb entfernen?'"
      @confirm="confirmRemove"
      @cancel="cancelRemove"
    />
    <button
      @click="toggleSidebar"
      class="hover:text-shop-blue-light flex items-center justify-center transition-all hover:scale-110 relative"
    >
      <Icon name="mdi:shopping-cart" class="w-6 h-6 text-white" />
      <span
        v-if="cartItemCount > 0"
        class="absolute -top-2 -right-2 bg-signal-red text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
      >
        {{ cartItemCount }}
      </span>
    </button>

    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      leave-active-class="transition-opacity duration-300 ease-in"
      enter-from-class="opacity-0"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 bg-black/50 z-50"
        @click.self="!showDeleteConfirm && closeSidebar()"
      >
        <div class="absolute inset-0" @click="!showDeleteConfirm && closeSidebar()"></div>
      </div>
    </Transition>

    <Transition
      enter-active-class="transition-transform duration-300 ease-out"
      leave-active-class="transition-transform duration-300 ease-in"
      enter-from-class="translate-x-full"
      leave-to-class="translate-x-full"
    >
      <div
        v-if="isOpen"
        class="fixed top-0 right-0 h-full w-full sm:w-[480px] bg-white z-50 shadow-2xl flex flex-col"
      >
        <div class="px-6 py-4 shadow-xl bg-shop-blue-dark flex items-center justify-between text-white">
          <div>
            <h2 class="font-bold text-xl text-white">Warenkorb</h2>
            <p class="text-sm opacity-90 text-white">{{ cartItemCount }} Artikel</p>
          </div>
          <button
            @click="closeSidebar"
            class="hover:bg-white/20 rounded-full p-2 transition-all flex items-center"
          >
            <Icon name="mdi:close" class="w-6 h-6 text-white" />
          </button>
        </div>

        <div v-if="cartItems.length === 0" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
          <Icon name="mdi:cart-outline" class="w-24 h-24 mb-4 text-gray-300" />
          <h3 class="text-xl font-semibold text-gray-700 mb-2">Dein Warenkorb ist leer</h3>
          <p class="text-gray-500 mb-6">Füge Produkte hinzu, um mit dem Einkaufen zu beginnen.</p>
          <NuxtLink
            to="/products"
            class="bg-shop-blue-dark text-white px-6 py-3 rounded-lg hover:bg-shop-blue-light transition-all"
            @click="closeSidebar"
          >
            Produkte entdecken
          </NuxtLink>
        </div>

        <div v-else class="flex-1 overflow-y-auto p-6 space-y-4">
          <div
            v-for="item in cartItems"
            :key="item.id"
            class="bg-gray-50 rounded-xl p-4 hover:shadow-md transition-all"
          >
            <div class="flex gap-4">
              <img
                :src="`${config.public.uploadsUrl}/${item.image}`"
                :alt="item.name"
                class="w-24 h-24 object-cover rounded-lg border border-shop-blue-dark"
              />
              <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start mb-2">
                  <div class="flex-1 pr-2">
                    <h3 class="font-bold text-base mb-1 truncate">{{ item.name }}</h3>
                    <p class="text-sm text-gray-500">Größe: {{ item.size }}</p>
                  </div>
                  <button
                    @click="showRemoveDialog(item.id)"
                    class="text-gray-400 hover:text-signal-red transition-all flex-shrink-0"
                  >
                    <Icon name="mdi:close-circle" class="w-6 h-6" />
                  </button>
                </div>
                
                <div class="flex items-center justify-between mt-3">
                  <div class="flex items-center gap-2">
                    <button
                      @click="decreaseQuantity(item.id)"
                      class="w-8 h-8 rounded-lg bg-white border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-all"
                    >
                      <Icon name="mdi:minus" class="w-4 h-4" />
                    </button>
                    <span class="text-sm font-bold w-10 text-center">{{ item.quantity }}</span>
                    <button
                      @click="increaseQuantity(item.id)"
                      class="w-8 h-8 rounded-lg bg-white border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-all"
                    >
                      <Icon name="mdi:plus" class="w-4 h-4" />
                    </button>
                  </div>
                  <div class="text-right">
                    <p class="text-xs text-gray-500">{{ formatPrice(item.price) }} / Stk.</p>
                    <p class="font-bold text-lg text-shop-blue-dark">{{ formatPrice(item.price * item.quantity) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="cartItems.length > 0" class="border-t border-gray-200 bg-gray-50 p-6">
          <div class="mb-4">
            <div class="flex justify-between text-gray-600 mb-2">
              <span>Zwischensumme</span>
              <span>{{ formatPrice(subtotal || 0) }}</span>
            </div>
            <div class="flex justify-between text-gray-600 mb-2">
              <span>Versand</span>
              <span>{{ shippingCost > 0 ? formatPrice(shippingCost) : 'Kostenlos' }}</span>
            </div>
            <div class="flex justify-between items-center pt-3 border-t border-gray-200">
              <span class="font-bold text-lg">Gesamt:</span>
              <span class="font-bold text-2xl text-shop-blue-dark">{{ formatPrice(totalPrice || 0) }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-1 text-right">Inkl. MwSt.</p>
          </div>

          <div class="space-y-3">
            <NuxtLink
              to="/checkout"
              class="block w-full bg-signal-red text-white text-center py-3.5 rounded-lg hover:opacity-90 transition-all font-bold text-lg"
              @click="closeSidebar"
            >
              Zur Kasse
            </NuxtLink>
            <NuxtLink
              to="/cart"
              class="block w-full bg-shop-blue-dark text-white text-center py-3 rounded-lg hover:bg-shop-blue-light transition-all font-semibold"
              @click="closeSidebar"
            >
              Warenkorb ansehen
            </NuxtLink>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import ConfirmDialog from "./ConfirmDialog.vue";
import { useCart } from "~/composables/useCart";

const config = useRuntimeConfig();

const { 
  cartItems, 
  cartItemCount, 
  cartTotal,
  isLoading,
  fetchCart, 
  increaseQuantity: increaseQty,
  decreaseQuantity: decreaseQty,
  removeFromCart,
  formatPrice
} = useCart();

const isOpen = ref(false);
const showDeleteConfirm = ref(false);
const itemToDelete = ref<number | null>(null);

const subtotal = computed(() => {
  return cartTotal.value;
});

const shippingCost = computed(() => {
  return subtotal.value >= 50 ? 0 : 4.99;
});

const totalPrice = computed(() => {
  return subtotal.value + shippingCost.value;
});

function toggleSidebar() {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
}

function closeSidebar() {
  isOpen.value = false;
  document.body.style.overflow = '';
}

async function increaseQuantity(itemId: number) {
  await increaseQty(itemId);
}

async function decreaseQuantity(itemId: number) {
  await decreaseQty(itemId);
}

async function removeItem(itemId: number) {
  await removeFromCart(itemId);
}

function showRemoveDialog(itemId: number) {
  itemToDelete.value = itemId;
  showDeleteConfirm.value = true;
}

async function confirmRemove() {
  if (itemToDelete.value !== null) {
    await removeItem(itemToDelete.value);
    itemToDelete.value = null;
  }
  showDeleteConfirm.value = false;
}

function cancelRemove() {
  showDeleteConfirm.value = false;
  itemToDelete.value = null;
}

function handleKeydown(event: KeyboardEvent) {
  if (event.key === 'Escape' && isOpen.value && !showDeleteConfirm.value) {
    closeSidebar();
  }
}

onMounted(() => {
  fetchCart();
  document.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleKeydown);
  document.body.style.overflow = '';
});
</script>
