<template>
  <div class="max-w-6xl mx-auto">
    <CheckoutAddressSelectionModal
      :isOpen="showBillingModal"
      :addresses="savedAddresses"
      :currentAddress="billingAddress"
      addressType="billing"
      @close="closeBillingModal"
      @select="handleBillingSelect"
    />

    <CheckoutAddressSelectionModal
      :isOpen="showShippingModal"
      :addresses="savedAddresses"
      :currentAddress="shippingAddress"
      :excludeAddressId="billingAddress?.id"
      addressType="shipping"
      @close="closeShippingModal"
      @select="handleShippingSelect"
    />

    <div class="grid lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-lg">
          <div class="flex items-center gap-3 mb-6">
            <Icon name="mdi:account-details" class="w-8 h-8 text-shop-blue-light" />
            <h2 class="text-2xl font-bold text-shop-blue-dark">
              {{ isGuest ? 'Deine Daten' : 'Adressdaten' }}
            </h2>
          </div>

          <AlertMessage type="error" :message="errorMessage" class="mb-6" />

          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Logged in user view -->
            <div v-if="!isGuest">
              <!-- Billing Address -->
              <div>
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <Icon name="mdi:receipt-text" class="w-5 h-5 text-gray-500" />
                    <h3 class="font-semibold text-lg">Rechnungsadresse</h3>
                  </div>
                  <button
                    type="button"
                    @click.stop.prevent="openBillingModal"
                    class="px-4 py-2 text-sm text-shop-blue-light hover:bg-shop-blue-light/10 font-medium rounded-lg transition-colors flex items-center gap-1"
                  >
                    <Icon name="mdi:pencil" class="w-4 h-4" />
                    Ändern
                  </button>
                </div>

                <div v-if="billingAddress" class="bg-gradient-to-br from-shop-blue-light/5 to-shop-blue-dark/5 border-2 border-shop-blue-light rounded-xl p-5 shadow-sm">
                  <div class="space-y-1 ">
                    <p class="font-medium text-lg">{{ form.firstName }} {{ form.lastName }}</p>
                    <p>{{ billingAddress.street }} {{ billingAddress.house_number }}</p>
                    <p>{{ billingAddress.postal_code }} {{ billingAddress.city }}</p>
                    <p>{{ billingAddress.country }}</p>
                  </div>
                </div>
              </div>

              <!-- Shipping Address Toggle -->
              <div class="mt-6 p-5 bg-gray-50 rounded-xl border border-gray-200">
                <label class="flex items-start gap-3 cursor-pointer group">
                  <input
                    type="checkbox"
                    v-model="useDifferentShipping"
                    class="w-5 h-5 mt-0.5 accent-shop-blue-light cursor-pointer"
                  />
                  <div>
                    <span class="font-semibold  group-hover:text-shop-blue-dark transition-colors">An andere Adresse liefern</span>
                    <p class="text-sm text-gray-500 mt-1">Lieferadresse weicht von der Rechnungsadresse ab</p>
                  </div>
                </label>
              </div>

              <!-- Shipping Address Section -->
              <div v-if="useDifferentShipping" class="mt-6">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <Icon name="mdi:truck-fast" class="w-5 h-5 text-gray-500" />
                    <h3 class="font-semibold text-lg">Lieferadresse</h3>
                  </div>
                  <button
                    type="button"
                    @click.stop.prevent="openShippingModal"
                    class="px-4 py-2 text-sm text-shop-blue-light hover:bg-shop-blue-light/10 font-medium rounded-lg transition-colors flex items-center gap-1"
                  >
                    <Icon name="mdi:pencil" class="w-4 h-4" />
                    {{ shippingAddress ? 'Ändern' : 'Auswählen' }}
                  </button>
                </div>

                <div v-if="shippingAddress" class="bg-gray-50 border-2 border-gray-300 rounded-xl p-5">
                  <div class="space-y-1 ">
                    <p class="font-medium">{{ shippingAddress.street }} {{ shippingAddress.house_number }}</p>
                    <p>{{ shippingAddress.postal_code }} {{ shippingAddress.city }}</p>
                    <p>{{ shippingAddress.country }}</p>
                  </div>
                </div>

                <div v-else class="bg-yellow-50 border-2 border-yellow-300 rounded-xl p-5 flex items-center gap-3">
                  <Icon name="mdi:alert" class="w-6 h-6 text-yellow-600" />
                  <p class="text-sm text-yellow-800">Bitte wählen Sie eine Lieferadresse aus</p>
                </div>
              </div>
            </div>

            <!-- Guest user view -->
            <div v-if="isGuest">
              <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div>
                  <label class="block text-sm font-semibold mb-2 ">
                    Vorname *
                  </label>
                  <input
                    v-model="form.firstName"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
                    placeholder="Max"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold mb-2 ">
                    Nachname *
                  </label>
                  <input
                    v-model="form.lastName"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
                    placeholder="Mustermann"
                  />
                </div>
              </div>

              <div class="mb-6">
                <label class="block text-sm font-semibold mb-2 ">
                  E-Mail *
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
                  placeholder="max@beispiel.de"
                />
              </div>

              <div class="grid md:grid-cols-3 gap-4 mb-6">
                <div class="md:col-span-2">
                  <label class="block text-sm font-semibold mb-2 ">
                    Straße *
                  </label>
                  <input
                    v-model="form.street"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
                    placeholder="Musterstraße"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold mb-2 ">
                    Hausnummer *
                  </label>
                  <input
                    v-model="form.houseNumber"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
                    placeholder="123"
                  />
                </div>
              </div>

              <div class="grid md:grid-cols-3 gap-4 mb-6">
                <div>
                  <label class="block text-sm font-semibold mb-2 ">
                    PLZ *
                  </label>
                  <input
                    v-model="form.postalCode"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
                    placeholder="12345"
                  />
                </div>
                <div class="md:col-span-2">
                  <label class="block text-sm font-semibold mb-2 ">
                    Stadt *
                  </label>
                  <input
                    v-model="form.city"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
                    placeholder="Musterstadt"
                  />
                </div>
              </div>

              <div class="mb-6">
                <label class="block text-sm font-semibold mb-2 ">
                  Land *
                </label>
                <select
                  v-model="form.country"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
                >
                  <option value="Deutschland">Deutschland</option>
                  <option value="Österreich">Österreich</option>
                  <option value="Schweiz">Schweiz</option>
                </select>
              </div>
            </div>

            <div class="pt-6 border-t border-gray-200">
              <h3 class="text-lg font-bold text-shop-blue-dark mb-4 flex items-center gap-2">
                <Icon name="mdi:credit-card" class="w-6 h-6 text-shop-blue-light" />
                Zahlungsart
              </h3>
              <div class="space-y-3">
                <label
                  v-for="method in paymentMethods"
                  :key="method.value"
                  class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-lg hover:bg-shop-blue-light/5 hover:border-shop-blue-light cursor-pointer transition-all"
                >
                  <input
                    type="radio"
                    v-model="form.paymentMethod"
                    :value="method.value"
                    required
                    class="w-5 h-5 accent-shop-blue-light"
                  />
                  <Icon :name="method.icon" class="w-6 h-6 text-gray-500" />
                  <span class="font-medium">{{ method.label }}</span>
                </label>
              </div>
            </div>

            <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-lg">
              <input
                type="checkbox"
                v-model="form.agreeToTerms"
                required
                class="w-5 h-5 mt-0.5 accent-shop-blue-light"
              />
              <label class="text-sm ">
                Ich akzeptiere die 
                <NuxtLink to="/terms" class="text-shop-blue-light hover:underline font-semibold">
                  AGB
                </NuxtLink>
                und die
                <NuxtLink to="/privacy" class="text-shop-blue-light hover:underline font-semibold">
                  Datenschutzerklärung
                </NuxtLink>
                *
              </label>
            </div>

            <button
              type="submit"
              :disabled="isSubmitting || !form.agreeToTerms"
              class="w-full bg-signal-red hover:opacity-90 text-white py-4 rounded-lg font-bold text-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <Icon v-if="isSubmitting" name="mdi:loading" class="w-6 h-6 animate-spin" />
              <Icon v-else name="mdi:lock-check" class="w-6 h-6" />
              <span>{{ isSubmitting ? 'Wird verarbeitet...' : 'Zahlungspflichtig bestellen' }}</span>
            </button>
          </form>
        </div>
      </div>

      <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-2xl shadow-lg sticky top-6">
          <h2 class="text-xl font-bold text-shop-blue-dark mb-4 flex items-center gap-2">
            <Icon name="mdi:shopping" class="w-6 h-6 text-shop-blue-light" />
            Bestellübersicht
          </h2>

          <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
            <div
              v-for="item in cartItems"
              :key="item.id"
              class="flex gap-3 pb-3 border-b border-gray-200"
            >
              <img
                :src="`${config.public.uploadsUrl}/${item.image}`"
                :alt="item.name"
                class="w-16 h-16 object-cover rounded-lg"
              />
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm truncate">{{ item.name }}</p>
                <p class="text-xs text-gray-500">Größe: {{ item.size }}</p>
                <p class="text-xs text-gray-500">{{ item.quantity }}x {{ formatPrice(item.price) }}</p>
              </div>
              <p class="font-bold text-shop-blue-dark">{{ formatPrice(item.price * item.quantity) }}</p>
            </div>
          </div>

          <div class="space-y-2 pt-4 border-t border-gray-200">
            <div class="flex justify-between text-gray-500">
              <span>Zwischensumme</span>
              <span>{{ formatPrice(subtotal) }}</span>
            </div>
            <div class="flex justify-between text-gray-500">
              <span>Versand</span>
              <span>{{ shippingCost > 0 ? formatPrice(shippingCost) : 'Kostenlos' }}</span>
            </div>
            <div class="flex justify-between items-center pt-3 border-t border-gray-200">
              <span class="font-bold text-lg">Gesamt:</span>
              <span class="font-bold text-2xl text-shop-blue-dark">{{ formatPrice(total) }}</span>
            </div>
            <p class="text-xs text-gray-500 text-right">Inkl. MwSt.</p>
          </div>

          <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
            <div class="flex items-center gap-2 text-sm text-gray-500">
              <Icon name="mdi:truck-fast" class="w-5 h-5 text-signal-green" />
              <span>Kostenloser Versand ab 50€</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-500">
              <Icon name="mdi:shield-check" class="w-5 h-5 text-signal-green" />
              <span>Sichere Zahlung</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-500">
              <Icon name="mdi:keyboard-return" class="w-5 h-5 text-signal-green" />
              <span>30 Tage Rückgaberecht</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import AlertMessage from '~/components/ui/AlertMessage.vue';

const config = useRuntimeConfig();
const { cartItems, cartTotal, formatPrice } = useCart();

interface Props {
  user: any;
  isGuest: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  'order-complete': [orderNumber: string];
}>();

const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  street: '',
  houseNumber: '',
  city: '',
  postalCode: '',
  country: 'Deutschland',
  paymentMethod: 'paypal',
  agreeToTerms: false,
});

const shippingForm = reactive({
  street: '',
  houseNumber: '',
  city: '',
  postalCode: '',
  country: 'Deutschland',
});

const paymentMethods = [
  { value: 'paypal', label: 'PayPal', icon: 'mdi:paypal' },
  { value: 'creditcard', label: 'Kreditkarte', icon: 'mdi:credit-card' },
  { value: 'klarna', label: 'Klarna', icon: 'mdi:alpha-k-box' },
  { value: 'sepa', label: 'SEPA-Lastschrift', icon: 'mdi:bank' },
];

const errorMessage = ref('');
const isSubmitting = ref(false);
const useDifferentShipping = ref(false);
const savedAddresses = ref<any[]>([]);
const billingAddress = ref<any>(null);
const shippingAddress = ref<any>(null);
const showBillingModal = ref(false);
const showShippingModal = ref(false);

const subtotal = computed(() => cartTotal.value);
const shippingCost = computed(() => subtotal.value >= 50 ? 0 : 4.99);
const total = computed(() => subtotal.value + shippingCost.value);

watch(() => props.user, (newUser) => {
  if (newUser && !props.isGuest) {
    form.firstName = newUser.firstName || '';
    form.lastName = newUser.lastName || '';
    form.email = newUser.email || '';
    
    if (newUser.address) {
      form.street = newUser.address.street || '';
      form.houseNumber = newUser.address.house_number || '';
      form.city = newUser.address.city || '';
      form.postalCode = newUser.address.postal_code || '';
      form.country = newUser.address.country || 'Deutschland';
    }
    
    loadSavedAddresses();
  }
}, { immediate: false });

onMounted(() => {
  if (props.user && !props.isGuest) {
    form.firstName = props.user.firstName || '';
    form.lastName = props.user.lastName || '';
    form.email = props.user.email || '';
    
    if (props.user.address) {
      form.street = props.user.address.street || '';
      form.houseNumber = props.user.address.house_number || '';
      form.city = props.user.address.city || '';
      form.postalCode = props.user.address.postal_code || '';
      form.country = props.user.address.country || 'Deutschland';
    }
    
    loadSavedAddresses();
  }
});

async function loadSavedAddresses() {
  if (!props.user) return;
  
  const config = useRuntimeConfig();
  const { token } = useAuth();
  
  try {
    const response = await $fetch<any>(`${config.public.apiUrl}/addresses.php`, {
      headers: {
        Authorization: `Bearer ${token.value || localStorage.getItem('token')}`,
      },
    });
    
    if (response.success && response.data) {
      savedAddresses.value = response.data;
      
      const defaultAddr = response.data.find((addr: any) => addr.is_default);
      if (defaultAddr) {
        billingAddress.value = defaultAddr;
        form.street = defaultAddr.street;
        form.houseNumber = defaultAddr.house_number;
        form.city = defaultAddr.city;
        form.postalCode = defaultAddr.postal_code;
        form.country = defaultAddr.country;
      }
    }
  } catch (error: any) {
    console.error('Failed to load addresses:', error);
    savedAddresses.value = [];
  }
}

function handleBillingSelect(address: any) {
  console.log('Selected billing address:', address);
  billingAddress.value = address;
  form.street = address.street;
  form.houseNumber = address.house_number;
  form.city = address.city;
  form.postalCode = address.postal_code;
  form.country = address.country;
  showBillingModal.value = false;
  
  const addressExists = savedAddresses.value.some(a => a.id === address.id);
  if (!addressExists) {
    loadSavedAddresses();
  }
}

function handleShippingSelect(address: any) {
  console.log('Selected shipping address:', address);
  shippingAddress.value = address;
  shippingForm.street = address.street;
  shippingForm.houseNumber = address.house_number;
  shippingForm.city = address.city;
  shippingForm.postalCode = address.postal_code;
  shippingForm.country = address.country;
  showShippingModal.value = false;
  
  const addressExists = savedAddresses.value.some(a => a.id === address.id);
  if (!addressExists) {
    loadSavedAddresses();
  }
}

function openBillingModal() {
  console.log('Opening billing modal');
  showBillingModal.value = true;
}

function openShippingModal() {
  console.log('Opening shipping modal');
  showShippingModal.value = true;
}

function closeBillingModal() {
  showBillingModal.value = false;
}

function closeShippingModal() {
  showShippingModal.value = false;
}

function cancelAddressEdit() {
}

watch(useDifferentShipping, (newValue) => {
  if (!newValue) {
    shippingAddress.value = null;
    shippingForm.street = '';
    shippingForm.houseNumber = '';
    shippingForm.city = '';
    shippingForm.postalCode = '';
    shippingForm.country = 'Deutschland';
  }
});

async function handleSubmit() {
  errorMessage.value = '';
  isSubmitting.value = true;

  const config = useRuntimeConfig();
  const { token } = useAuth();
  const { clearCart } = useCart();

  try {
    const billingAddressData = {
      first_name: form.firstName,
      last_name: form.lastName,
      email: form.email,
      street: form.street,
      house_number: form.houseNumber,
      city: form.city,
      postal_code: form.postalCode,
      country: form.country
    };

    const shippingAddressData = useDifferentShipping.value && shippingAddress.value ? {
      street: shippingAddress.value.street,
      house_number: shippingAddress.value.house_number,
      city: shippingAddress.value.city,
      postal_code: shippingAddress.value.postal_code,
      country: shippingAddress.value.country
    } : null;

    const orderItems = cartItems.value.map(item => ({
      product_id: item.product_id,
      quantity: item.quantity,
      size: item.size,
      price: item.price
    }));

    console.log('Submitting order:', {
      items: orderItems,
      total: total.value,
      billing_address: billingAddressData,
      shipping_address: shippingAddressData,
      payment_method: form.paymentMethod
    });

    const response = await $fetch<any>(`${config.public.apiUrl}/orders.php`, {
      method: 'POST',
      headers: token.value ? {
        Authorization: `Bearer ${token.value || localStorage.getItem('token')}`,
      } : {},
      body: {
        items: orderItems,
        total: total.value,
        billing_address: billingAddressData,
        shipping_address: shippingAddressData,
        payment_method: form.paymentMethod
      }
    });

    console.log('Order response:', response);
    console.log('Response type:', typeof response);
    console.log('Response.success:', response.success);
    console.log('Response.data:', response.data);

    if (response.success) {
      const orderNumber = response.data.order_number;
      console.log('Order number extracted:', orderNumber);
      
      console.log('Clearing cart...');
      await clearCart();
      console.log('Cart cleared');
      
      console.log('Emitting order-complete with:', orderNumber);
      emit('order-complete', orderNumber);
      console.log('Event emitted successfully');
    } else {
      throw new Error(response.message || 'Bestellung fehlgeschlagen');
    }
  } catch (error: any) {
    console.error('Order error:', error);
    console.error('Error details:', error.data);
    errorMessage.value = error.data?.message || error.message || 'Ein Fehler ist aufgetreten. Bitte versuche es erneut.';
  } finally {
    isSubmitting.value = false;
  }
}
</script>
