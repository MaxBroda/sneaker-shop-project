<template>
  <Transition
    enter-active-class="transition-opacity duration-300 ease-out"
    leave-active-class="transition-opacity duration-200 ease-in"
    enter-from-class="opacity-0"
    leave-to-class="opacity-0"
  >
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
      @click.self="closeModal"
    >
      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        leave-active-class="transition-all duration-200 ease-in"
        enter-from-class="opacity-0 scale-95"
        leave-to-class="opacity-0 scale-95"
      >
        <div
          v-if="isOpen && product"
          class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
          @click.stop
        >
          <div class="sticky top-0 bg-shop-blue-dark text-white px-6 py-4 flex items-center justify-between rounded-t-2xl z-10 shadow-xl">
            <h2 class="text-xl font-bold">Zum Warenkorb hinzufügen</h2>
            <button
              @click="closeModal"
              class="hover:bg-white/20 rounded-full p-2 transition-all"
            >
              <Icon name="mdi:close" class="w-5 h-5 flex justify-center items-center text-white" />
            </button>
          </div>

          <div class="p-6">
            <div class="flex gap-4 mb-6">
              <div class="w-32 h-32 flex-shrink-0 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl overflow-hidden">
                <img
                  :src="`${config.public.uploadsUrl}/${product.image}`"
                  :alt="product.name"
                  class="w-full h-full object-cover"
                />
              </div>
              <div class="flex-1">
                <h3 class="text-2xl font-bold mb-2 text-gray-900">{{ product.name }}</h3>
                <p class="text-gray-500 text-sm mb-3 line-clamp-2">
                  {{ product.description || 'Keine Beschreibung verfügbar' }}
                </p>
                <div class="flex flex-col md:flex-row items-baseline gap-2">
                  <span class="text-3xl font-bold text-shop-blue-light">{{ product.price }} €</span>
                  <span class="text-gray-500 text-sm">inkl. MwSt.</span>
                </div>
              </div>
            </div>

            <div class="mb-6">
              <label class="block text-sm font-bold mb-3 uppercase tracking-wide ">
                Größe auswählen *
              </label>
              <div class="grid grid-cols-6 gap-2">
                <button
                  v-for="size in availableSizes"
                  :key="size"
                  @click="selectedSize = size.toString()"
                  :class="[
                    'border-2 rounded-lg py-2.5 text-sm font-medium transition-all',
                    selectedSize === size.toString()
                      ? 'border-shop-blue-light bg-shop-blue-light text-white shadow-md'
                      : 'border-gray-300 hover:border-shop-blue-light hover:bg-shop-blue-light/10'
                  ]"
                >
                  {{ size }}
                </button>
              </div>
              <p v-if="sizeError" class="text-signal-red text-sm mt-2 flex items-center gap-1">
                <Icon name="mdi:alert-circle" class="w-4 h-4" />
                {{ sizeError }}
              </p>
            </div>

            <div class="mb-6">
              <label class="block text-sm font-bold mb-3 uppercase tracking-wide ">
                Anzahl
              </label>
              <div class="flex justify-center md:justify-start items-center gap-3">
                <button
                  @click="decreaseQuantity"
                  :disabled="quantity <= 1"
                  class="w-12 h-12 rounded-lg bg-gray-100 border-2 border-gray-300 flex items-center justify-center hover:bg-gray-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <Icon name="mdi:minus" class="w-5 h-5" />
                </button>
                <div class="flex-1 w-2/3 md:max-w-[120px]">
                  <input
                    v-model.number="quantity"
                    type="number"
                    min="1"
                    max="99"
                    class="w-full h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:outline-none focus:border-shop-blue-light [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                  />
                </div>
                <button
                  @click="increaseQuantity"
                  :disabled="quantity >= 99"
                  class="w-12 h-12 rounded-lg bg-gray-100 border-2 border-gray-300 flex items-center justify-center hover:bg-gray-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <Icon name="mdi:plus" class="w-5 h-5" />
                </button>
              </div>
            </div>

            <AlertMessage :type="messageType" :message="message" class="mb-4" />

            <div class="flex gap-3">
              <button
                @click="closeModal"
                class="flex-1 bg-gray-200  py-3.5 px-4 rounded-xl font-bold hover:bg-gray-300 transition-all"
              >
                Abbrechen
              </button>
              <button
                @click="handleAddItem"
                :disabled="isAdding || !selectedSize"
                :class="[
                  'flex-1 py-3.5 px-4 rounded-xl font-bold transition-all duration-300 flex items-center justify-center gap-2',
                  selectedSize && !isAdding
                    ? 'bg-shop-blue-light hover:bg-shop-blue-dark text-white shadow-md hover:shadow-xl hover:scale-105'
                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                ]"
              >
                <Icon v-if="isAdding" name="mdi:loading" class="w-5 h-5 animate-spin" />
                <Icon v-else name="mdi:cart-plus" :class="['w-5 h-5', selectedSize && !isAdding ? 'text-white' : 'text-gray-500']" />
                {{ isAdding ? 'Wird hinzugefügt...' : 'In den Warenkorb' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { useCart } from '~/composables/useCart';
import AlertMessage from '~/components/ui/AlertMessage.vue';

interface Product {
  id: number;
  name: string;
  description?: string;
  price: number;
  image: string;
  category?: string;
}

interface Props {
  isOpen: boolean;
  product: Product | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  close: [];
  added: [];
}>();

const config = useRuntimeConfig();
const { addItem } = useCart();

const selectedSize = ref<string>('');
const quantity = ref<number>(1);
const isAdding = ref(false);
const message = ref('');
const messageType = ref<'success' | 'error'>('success');
const sizeError = ref('');

const availableSizes = [36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47];

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    selectedSize.value = '';
    quantity.value = 1;
    message.value = '';
    sizeError.value = '';
  }
});

watch(() => props.product, () => {
  selectedSize.value = '';
  quantity.value = 1;
  message.value = '';
  sizeError.value = '';
});

function decreaseQuantity() {
  if (quantity.value > 1) {
    quantity.value--;
  }
}

function increaseQuantity() {
  if (quantity.value < 99) {
    quantity.value++;
  }
}

function closeModal() {
  emit('close');
}

async function handleAddItem() {
  if (!props.product) return;
  
  if (!selectedSize.value) {
    sizeError.value = 'Bitte wähle eine Größe aus';
    return;
  }
  
  sizeError.value = '';
  isAdding.value = true;
  message.value = '';
  
  try {
    const result = await addItem(props.product.id, selectedSize.value, quantity.value);
    
    if (result.success) {
      messageType.value = 'success';
      message.value = `${quantity.value}x ${props.product.name} (Größe ${selectedSize.value}) wurde zum Warenkorb hinzugefügt!`;
      
      setTimeout(() => {
        emit('added');
        closeModal();
      }, 1500);
    } else {
      messageType.value = 'error';
      message.value = result.message || 'Fehler beim Hinzufügen zum Warenkorb';
    }
  } catch (error) {
    console.error('Error adding to cart:', error);
    messageType.value = 'error';
    message.value = 'Ein Fehler ist aufgetreten. Bitte versuche es erneut.';
  } finally {
    setTimeout(() => isAdding.value = false, 1000);
  }
}
</script>
