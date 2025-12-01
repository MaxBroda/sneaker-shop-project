<template>
    <ConfirmDialog
      :isOpen="showDeleteConfirm"
      title="Adresse löschen"
      message="Möchten Sie diese Adresse wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden."
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  <div class="bg-white p-6 shadow-md rounded-xl">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Adressen</h2>
      <button
        v-if="!showAddressForm"
        @click="addNewAddress"
        class="bg-shop-blue-light text-white px-4 py-2 rounded-lg hover:bg-shop-blue-dark transition-all"
      >
        + Adresse hinzufügen
      </button>
    </div>
    <div
      v-if="showAddressForm"
      class="bg-gray-50 p-4 rounded-lg border border-gray-200"
    >
      <h3 class="font-semibold mb-3">
        {{ editingAddressId !== null ? "Adresse bearbeiten" : "Neue Adresse" }}
      </h3>
      <form @submit.prevent="saveAddress" class="space-y-3">
        <div class="grid md:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium mb-1"> Straße </label>
            <input
              v-model="addressForm.street"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-dark focus:border-transparent transition-all"
              placeholder="Musterstraße"
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">
              Hausnummer
            </label>
            <input
              v-model="addressForm.house_number"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-dark focus:border-transparent transition-all"
              placeholder="123"
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1"> Stadt </label>
            <input
              v-model="addressForm.city"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-dark focus:border-transparent transition-all"
              placeholder="Dresden"
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1"> PLZ </label>
            <input
              v-model="addressForm.postal_code"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-dark focus:border-transparent transition-all"
              placeholder="80331"
            />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1"> Land </label>
          <input
            v-model="addressForm.country"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-dark focus:border-transparent transition-all"
            placeholder="Deutschland"
          />
        </div>
        <div class="flex justify-center max-md:w-full gap-2 !mt-6">
          <button
            type="submit"
            :disabled="isSaving"
            class="bg-shop-blue-light max-md:w-1/2 text-white px-4 py-2 rounded-lg hover:opacity-90 disabled:bg-gray-500 disabled:cursor-not-allowed transition-all"
          >
            {{ isSaving ? "Wird gespeichert..." : "Speichern" }}
          </button>
          <button
            type="button"
            @click="cancelAddressEdit"
            class="border border-gray-500 max-md:w-1/2 px-4 py-2 rounded-lg hover:bg-gray-50 transition-all"
          >
            Abbrechen
          </button>
        </div>
      </form>
    </div>

    <div
      v-if="addresses.length === 0 && !showAddressForm"
      class="text-center py-8 text-gray-500"
    >
      <p>Keine Adressen gespeichert.</p>
    </div>

    <div v-else-if="!showAddressForm" class="space-y-3">
      <div
        v-for="(address, index) in addresses"
        :key="index"
        class="border border-gray-200 rounded-lg p-4 transition-all"
        :class="{ 'border-shop-blue-light border-2': index === defaultAddressIndex }"
      >
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <p class="font-medium">
                {{ address.street }} {{ address.house_number }}
              </p>
              <span
                v-if="index === defaultAddressIndex"
                class="text-xs bg-shop-blue-light text-white px-2 py-1 rounded-lg"
              >
                Standard
              </span>
            </div>
            <p class="">
              {{ address.postal_code }} {{ address.city }}
            </p>
            <p class="">{{ address.country }}</p>
          </div>
          <div class="flex flex-col gap-2 items-end">
            <button
              v-if="index !== defaultAddressIndex"
              @click="setDefaultAddress(index)"
              class="text-sm text-shop-blue-light hover:text-shop-blue-dark transition-colors"
            >
              Als Standard setzen
            </button>
            <button
              @click="editAddress(index)"
              class="text-sm text-shop-blue-light hover:text-shop-blue-dark transition-colors"
            >
              Bearbeiten
            </button>
            <button
              @click="deleteAddress(index)"
              class="text-sm text-signal-red hover:opacity-80 transition-opacity"
            >
              Löschen
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import ConfirmDialog from "~/components/modals/ConfirmDialog.vue";

const { user } = useAuth();

interface Address {
  street: string;
  house_number: string;
  city: string;
  postal_code: string;
  country: string;
}

const emit = defineEmits<{
  success: [message: string];
  error: [message: string];
}>();

const addresses = ref<Address[]>([]);
const defaultAddressIndex = ref(0);
const showAddressForm = ref(false);
const editingAddressId = ref<number | null>(null);
const isSaving = ref(false);
const showDeleteConfirm = ref(false);
const addressToDelete = ref<number | null>(null);

const addressForm = reactive({
  street: "",
  house_number: "",
  city: "",
  postal_code: "",
  country: "Deutschland",
});

onMounted(() => {
  if (typeof window !== "undefined" && user.value) {
    const userAddressKey = `user_addresses_${user.value.id}`;
    const userDefaultIndexKey = `default_address_index_${user.value.id}`;
    
    const savedAddresses = localStorage.getItem(userAddressKey);
    if (savedAddresses) {
      addresses.value = JSON.parse(savedAddresses);
    } else if (user.value?.address) {
      addresses.value = [user.value.address];
      localStorage.setItem(userAddressKey, JSON.stringify(addresses.value));
    }

    const savedDefaultIndex = localStorage.getItem(userDefaultIndexKey);
    if (savedDefaultIndex) {
      defaultAddressIndex.value = parseInt(savedDefaultIndex);
    }
  }
});

function addNewAddress() {
  editingAddressId.value = null;
  addressForm.street = "";
  addressForm.house_number = "";
  addressForm.city = "";
  addressForm.postal_code = "";
  addressForm.country = "Deutschland";
  showAddressForm.value = true;
}

function editAddress(index: number) {
  editingAddressId.value = index;
  const address = addresses.value[index];
  if (address) {
    addressForm.street = address.street;
    addressForm.house_number = address.house_number;
    addressForm.city = address.city;
    addressForm.postal_code = address.postal_code;
    addressForm.country = address.country;
    showAddressForm.value = true;
  }
}

function setDefaultAddress(index: number) {
  defaultAddressIndex.value = index;
  if (typeof window !== "undefined" && user.value) {
    const userDefaultIndexKey = `default_address_index_${user.value.id}`;
    localStorage.setItem(userDefaultIndexKey, index.toString());
  }
  emit("success", "Standardadresse erfolgreich geändert!");
}

function cancelAddressEdit() {
  showAddressForm.value = false;
  addressForm.street = "";
  addressForm.house_number = "";
  addressForm.city = "";
  addressForm.postal_code = "";
  addressForm.country = "Deutschland";
}

async function saveAddress() {
  isSaving.value = true;

  try {
    await new Promise((resolve) => setTimeout(resolve, 500));

    if (editingAddressId.value !== null) {
      addresses.value[editingAddressId.value] = { ...addressForm };
      emit("success", "Adresse erfolgreich aktualisiert!");
    } else {
      addresses.value.push({ ...addressForm });
      emit("success", "Adresse erfolgreich hinzugefügt!");
    }

    if (typeof window !== "undefined" && user.value) {
      const userAddressKey = `user_addresses_${user.value.id}`;
      localStorage.setItem(userAddressKey, JSON.stringify(addresses.value));

      if (addresses.value.length > 0) {
        user.value.address = addresses.value[defaultAddressIndex.value];
        localStorage.setItem("user", JSON.stringify(user.value));
      }
    }

    cancelAddressEdit();
  } catch (err: any) {
    console.error("Fehler:", err);
    emit("error", "Fehler beim Speichern der Adresse");
  } finally {
    isSaving.value = false;
  }
}

async function deleteAddress(index: number) {
  addressToDelete.value = index;
  showDeleteConfirm.value = true;
}

function cancelDelete() {
  showDeleteConfirm.value = false;
  addressToDelete.value = null;
}

async function confirmDelete() {
  if (addressToDelete.value === null) return;

  const index = addressToDelete.value;
  showDeleteConfirm.value = false;

  try {
    addresses.value.splice(index, 1);

    if (defaultAddressIndex.value >= addresses.value.length) {
      defaultAddressIndex.value = Math.max(0, addresses.value.length - 1);
    }

    if (typeof window !== "undefined" && user.value) {
      const userAddressKey = `user_addresses_${user.value.id}`;
      const userDefaultIndexKey = `default_address_index_${user.value.id}`;
      
      localStorage.setItem(userAddressKey, JSON.stringify(addresses.value));
      localStorage.setItem(userDefaultIndexKey, defaultAddressIndex.value.toString());

      user.value.address =
        addresses.value.length > 0
          ? addresses.value[defaultAddressIndex.value]
          : undefined;
      localStorage.setItem("user", JSON.stringify(user.value));
    }

    emit("success", "Adresse erfolgreich gelöscht!");
  } catch (err: any) {
    console.error("Fehler:", err);
    emit("error", "Fehler beim Löschen der Adresse");
  } finally {
    addressToDelete.value = null;
  }
}
</script>
