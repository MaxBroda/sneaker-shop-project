<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    @click.self="closeModal"
  >
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
      <div class="p-6 border-b border-gray-200 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Icon :name="addressType === 'billing' ? 'mdi:receipt-text' : 'mdi:truck-fast'" class="w-7 h-7 text-shop-blue-light" />
          <h2 class="text-2xl font-bold text-shop-blue-dark">
            {{ addressType === 'billing' ? 'Rechnungsadresse' : 'Lieferadresse' }} wählen
          </h2>
        </div>
        <button
          @click="closeModal"
          class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
        >
          <Icon name="mdi:close" class="w-6 h-6 text-gray-500" />
        </button>
      </div>

      <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
        <div v-if="addresses.length > 0" class="grid md:grid-cols-2 gap-4 mb-4">
          <button
            v-for="(address, index) in filteredAddresses"
            :key="address.id"
            @click="selectAddress(address)"
            type="button"
            class="relative p-5 border-2 rounded-xl text-left transition-all group"
            :class="selectedAddress?.id === address.id 
              ? 'border-shop-blue-light bg-shop-blue-light/5 shadow-md' 
              : 'border-gray-200 hover:border-shop-blue-light/50 hover:bg-gray-50'"
          >
            <div class="absolute top-4 right-4">
              <div
                v-if="selectedAddress?.id === address.id"
                class="w-6 h-6 bg-shop-blue-light rounded-full flex items-center justify-center"
              >
                <Icon name="mdi:check" class="w-4 h-4 text-white" />
              </div>
              <div
                v-else
                class="w-6 h-6 border-2 border-gray-300 rounded-full group-hover:border-shop-blue-light"
              ></div>
            </div>

            <div class="pr-10">
              <div class="flex items-center gap-2 mb-2">
                <span
                  v-if="address.is_default"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-shop-blue-light text-white"
                >
                  Standard
                </span>
              </div>
              <p class="font-semibold mb-1">
                {{ address.street }} {{ address.house_number }}
              </p>
              <p class="text-sm text-gray-500">{{ address.postal_code }} {{ address.city }}</p>
              <p class="text-sm text-gray-500">{{ address.country }}</p>
            </div>
          </button>
        </div>

        <button
          @click="showNewAddressForm = !showNewAddressForm"
          type="button"
          class="w-full p-4 border-2 border-dashed border-gray-300 rounded-xl hover:border-shop-blue-light hover:bg-shop-blue-light/5 transition-all flex items-center justify-center gap-2 text-shop-blue-dark font-semibold group"
        >
          <Icon name="mdi:plus-circle" class="w-5 h-5 group-hover:scale-110 transition-transform" />
          Neue Adresse erstellen
        </button>

        <div v-if="showNewAddressForm" class="mt-4 p-5 bg-gray-50 rounded-xl border-2 border-shop-blue-light">
          <h4 class="font-bold text-shop-blue-dark mb-4">Neue Adresse</h4>

          <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold mb-2">Straße *</label>
              <input
                v-model="newAddress.street"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
                placeholder="Musterstraße"
              />
            </div>
            <div>
              <label class="block text-sm font-semibold mb-2">Hausnummer *</label>
              <input
                v-model="newAddress.house_number"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
                placeholder="123"
              />
            </div>
          </div>

          <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div>
              <label class="block text-sm font-semibold mb-2">PLZ *</label>
              <input
                v-model="newAddress.postal_code"
                type="text"
                maxlength="5"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
                placeholder="12345"
              />
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold mb-2">Stadt *</label>
              <input
                v-model="newAddress.city"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
                placeholder="Berlin"
              />
            </div>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-semibold mb-2">Land *</label>
            <input
              v-model="newAddress.country"
              type="text"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
              placeholder="Deutschland"
            />
          </div>

          <button
            @click="createNewAddress"
            type="button"
            class="w-full px-6 py-3 bg-shop-blue-light hover:bg-shop-blue-dark text-white font-semibold rounded-lg transition-colors"
          >
            Adresse erstellen und auswählen
          </button>
        </div>
      </div>

      <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
        <button
          @click="closeModal"
          type="button"
          class="px-6 py-3 border border-gray-300 hover:bg-gray-50 font-semibold rounded-lg transition-colors"
        >
          Abbrechen
        </button>
        <button
          @click="confirmSelection"
          type="button"
          :disabled="!selectedAddress"
          class="px-6 py-3 bg-shop-blue-light hover:bg-shop-blue-dark text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Adresse übernehmen
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Address {
  id?: number;
  street: string;
  house_number: string;
  city: string;
  postal_code: string;
  country: string;
  is_default?: number;
}

interface Props {
  isOpen: boolean;
  addresses: Address[];
  currentAddress: Address | null;
  addressType: 'billing' | 'shipping';
  excludeAddressId?: number | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  close: [];
  select: [address: Address];
}>();

const selectedAddress = ref<Address | null>(props.currentAddress);
const showNewAddressForm = ref(false);
const newAddress = reactive({
  street: '',
  house_number: '',
  city: '',
  postal_code: '',
  country: 'Deutschland',
});

const filteredAddresses = computed(() => {
  if (props.excludeAddressId) {
    return props.addresses.filter(addr => addr.id !== props.excludeAddressId);
  }
  return props.addresses;
});

watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    selectedAddress.value = props.currentAddress;
    showNewAddressForm.value = false;
  }
});

function selectAddress(address: Address) {
  selectedAddress.value = address;
}

function closeModal() {
  emit('close');
}

function confirmSelection() {
  if (selectedAddress.value) {
    emit('select', selectedAddress.value);
  }
}

async function createNewAddress() {
  if (!newAddress.street || !newAddress.house_number || !newAddress.city || !newAddress.postal_code) {
    return;
  }

  const config = useRuntimeConfig();
  const { token } = useAuth();

  try {
    const response = await $fetch<any>(`${config.public.apiUrl}/addresses.php`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token.value || localStorage.getItem('token')}`,
      },
      body: {
        ...newAddress,
        is_default: 0
      }
    });

    if (response.success && response.data) {
      const createdAddress = response.data;
      selectedAddress.value = createdAddress;
      showNewAddressForm.value = false;
      
      emit('select', createdAddress);
    }
  } catch (error) {
    console.error('Failed to create address:', error);
  }
}
</script>
