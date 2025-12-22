<template>
    <ConfirmDialog
      :isOpen="showDeleteConfirm"
      title="Adresse löschen"
      message="Möchten Sie diese Adresse wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden."
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  <div class="bg-white p-6 shadow-md rounded-xl">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-2xl font-bold mb-1">Adressverwaltung</h2>
        <p class="text-gray-500">Verwalten Sie Ihre Liefer- und Rechnungsadressen</p>
      </div>
      <button
        v-if="!showAddressForm"
        @click="addNewAddress"
        class="px-5 py-2.5 bg-shop-blue-light hover:bg-shop-blue-dark text-white font-medium rounded-lg transition-colors shadow-sm"
      >
        + Neue Adresse
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
              @input="filterNumbers($event, 'house_number')"
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
              maxlength="5"
              @input="filterNumbers($event, 'postal_code')"
              class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-dark focus:border-transparent transition-all"
              placeholder="80331"
            />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1"> Land </label>
          <BaseDropdown
            v-model="addressForm.country"
            :options="countryOptions"
            required
            customClass="px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-dark focus:border-transparent transition-all"
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

    <p v-if="addresses.length === 0 && !showAddressForm" class="text-center py-12 text-gray-500">
      Noch keine Adressen gespeichert. Fügen Sie Ihre erste Adresse hinzu.
    </p>

    <div v-else-if="!showAddressForm" class="space-y-4">
      <div v-if="defaultAddress" class="space-y-3">
        <div class="flex items-center gap-2 mb-2">
          <Icon name="mdi:check-circle" class="w-5 h-5 text-shop-blue-light" />
          <h3 class="font-semibold text-lg">Standard Lieferadresse</h3>
        </div>
        
        <div class="bg-gradient-to-br from-shop-blue-light/5 to-shop-blue-dark/5 border-2 border-shop-blue-light rounded-xl p-5 shadow-sm">
          <div v-if="!editingDefaultAddress" class="flex justify-between items-start">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-shop-blue-light text-white">
                  Standard
                </span>
              </div>
              <p class="font-medium text-lg mb-1">
                {{ defaultAddress.street }} {{ defaultAddress.house_number }}
              </p>
              <p class="">
                {{ defaultAddress.postal_code }} {{ defaultAddress.city }}
              </p>
              <p class="text-gray-500">{{ defaultAddress.country }}</p>
            </div>
            <div class="flex gap-2">
              <button
                @click="editDefaultAddress"
                class="px-4 py-2 text-sm text-shop-blue-light hover:bg-shop-blue-light/10 font-medium rounded-lg transition-colors"
              >
                Bearbeiten
              </button>
            </div>
          </div>

          <div v-else class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="md:col-span-2">
                <label class="block text-sm font-medium  mb-1">Straße</label>
                <input
                  v-model="addressForm.street"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-shop-blue-light focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium  mb-1">Hausnummer</label>
                <input
                  v-model="addressForm.house_number"
                  type="text"
                  required
                  @input="(e) => filterNumbers(e, 'house_number')"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-shop-blue-light focus:border-transparent"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium  mb-1">Postleitzahl</label>
                <input
                  v-model="addressForm.postal_code"
                  type="text"
                  required
                  maxlength="5"
                  @input="(e) => filterNumbers(e, 'postal_code')"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-shop-blue-light focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium  mb-1">Stadt</label>
                <input
                  v-model="addressForm.city"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-shop-blue-light focus:border-transparent"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium  mb-1">Land</label>
              <BaseDropdown
                v-model="addressForm.country"
                :options="countryOptions"
                required
                customClass="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-shop-blue-light focus:border-transparent"
              />
            </div>

            <div class="flex gap-3 pt-2">
              <button
                @click="saveAddress"
                :disabled="isSaving"
                class="px-6 py-2.5 bg-shop-blue-light hover:bg-shop-blue-dark text-white font-medium rounded-lg transition-colors disabled:opacity-50 shadow-sm"
              >
                {{ isSaving ? "Speichern..." : "Speichern" }}
              </button>
              <button
                @click="cancelAddressEdit"
                type="button"
                class="px-6 py-2.5 border border-gray-300 hover:bg-gray-50  font-medium rounded-lg transition-colors"
              >
                Abbrechen
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="otherAddresses.length > 0" class="space-y-3">
        <div class="flex items-center gap-2 mt-6 mb-2">
          <Icon name="mdi:map-marker" class="w-5 h-5 text-gray-500" />
          <h3 class="font-semibold text-lg">Weitere Adressen</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="address in otherAddresses"
            :key="address.id"
            class="border border-gray-200 hover:border-shop-blue-light/50 rounded-xl p-4 transition-all bg-white shadow-sm hover:shadow-md"
          >
            <div class="flex justify-between items-start mb-3">
              <div class="flex-1">
                <p class="font-medium text-base mb-1">
                  {{ address.street }} {{ address.house_number }}
                </p>
                <p class=" text-sm">
                  {{ address.postal_code }} {{ address.city }}
                </p>
                <p class="text-gray-500 text-sm">{{ address.country }}</p>
              </div>
            </div>
            <div class="flex gap-2 pt-2 border-t border-gray-100">
              <button
                @click="setDefaultAddress(address.id!)"
                class="flex-1 px-3 py-1.5 text-sm text-shop-blue-light hover:bg-shop-blue-light/10 font-medium rounded-lg transition-colors"
              >
                Als Standard setzen
              </button>
              <button
                @click="editAddress(address.id!)"
                class="px-3 py-1.5 text-sm text-gray-500 hover:bg-gray-100 font-medium rounded-lg transition-colors"
              >
                Bearbeiten
              </button>
              <button
                @click="deleteAddress(address.id!)"
                class="px-3 py-1.5 text-sm text-signal-red hover:bg-red-50 font-medium rounded-lg transition-colors"
              >
                Löschen
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import ConfirmDialog from "~/components/modals/ConfirmDialog.vue";
import BaseDropdown from "~/components/ui/BaseDropdown.vue";

const api = useApi();

interface Address {
  id?: number;
  street: string;
  house_number: string;
  city: string;
  postal_code: string;
  country: string;
  is_default?: number;
}

const emit = defineEmits<{
  success: [message: string];
  error: [message: string];
}>();

const countryOptions = [
  { value: "Deutschland", label: "Deutschland" },
  { value: "Österreich", label: "Österreich" },
  { value: "Schweiz", label: "Schweiz" },
];

const addresses = ref<Address[]>([]);
const showAddressForm = ref(false);
const editingAddressId = ref<number | null>(null);
const editingDefaultAddress = ref(false);
const isSaving = ref(false);
const showDeleteConfirm = ref(false);
const addressToDelete = ref<number | null>(null);

const addressForm = reactive({
  street: "",
  house_number: "",
  city: "",
  postal_code: "",
  country: "",
});

const defaultAddress = computed(() => addresses.value.find(a => a.is_default));
const otherAddresses = computed(() => addresses.value.filter(a => !a.is_default));

onMounted(() => {
  loadAddresses();
});

async function loadAddresses() {
  try {
    const response = await api.get<Address[]>("/addresses.php");

    if (response.success && response.data) {
      addresses.value = response.data;
    }
  } catch {
    emit("error", "Fehler beim Laden der Adressen");
  }
}

function addNewAddress() {
  editingAddressId.value = null;
  addressForm.street = "";
  addressForm.house_number = "";
  addressForm.city = "";
  addressForm.postal_code = "";
  addressForm.country = "Deutschland";
  showAddressForm.value = true;
}

function editDefaultAddress() {
  const address = defaultAddress.value;
  if (address) {
    editingAddressId.value = address.id!;
    addressForm.street = address.street;
    addressForm.house_number = address.house_number;
    addressForm.city = address.city;
    addressForm.postal_code = address.postal_code;
    addressForm.country = address.country;
    editingDefaultAddress.value = true;
  }
}

function editAddress(id: number) {
  const address = addresses.value.find(a => a.id === id);
  if (address) {
    editingAddressId.value = id;
    addressForm.street = address.street;
    addressForm.house_number = address.house_number;
    addressForm.city = address.city;
    addressForm.postal_code = address.postal_code;
    addressForm.country = address.country;
    showAddressForm.value = true;
  }
}

async function setDefaultAddress(id: number) {
  try {
    const response = await api.put("/addresses.php", {
      id,
      is_default: 1
    });

    if (response.success) {
      await loadAddresses();
      emit("success", "Standardadresse erfolgreich geändert!");
    }
  } catch {
    emit("error", "Fehler beim Setzen der Standardadresse");
  }
}

function cancelAddressEdit() {
  showAddressForm.value = false;
  editingDefaultAddress.value = false;
  editingAddressId.value = null;
  addressForm.street = "";
  addressForm.house_number = "";
  addressForm.city = "";
  addressForm.postal_code = "";
  addressForm.country = "Deutschland";
}

function filterNumbers(event: Event, field: 'house_number' | 'postal_code') {
  const input = event.target as HTMLInputElement;
  const filtered = input.value.replace(/\D/g, "");
  if (field === 'postal_code' && filtered.length > 5) {
    addressForm[field] = filtered.slice(0, 5);
  } else {
    addressForm[field] = filtered;
  }
}

async function saveAddress() {
  isSaving.value = true;

  try {
    if (editingAddressId.value !== null) {
      const response = await api.put("/addresses.php", {
        id: editingAddressId.value,
        ...addressForm
      });

      if (response.success) {
        emit("success", "Adresse erfolgreich aktualisiert!");
      }
    } else {
      const response = await api.post("/addresses.php", {
        ...addressForm,
        is_default: addresses.value.length === 0 ? 1 : 0
      });

      if (response.success) {
        emit("success", "Adresse erfolgreich hinzugefügt!");
      }
    }

    await loadAddresses();
    cancelAddressEdit();
  } catch (err: unknown) {
    const errorMessage = err instanceof Error ? err.message : "Fehler beim Speichern der Adresse";
    emit("error", errorMessage);
  } finally {
    isSaving.value = false;
  }
}

function deleteAddress(id: number) {
  addressToDelete.value = id;
  showDeleteConfirm.value = true;
}

function cancelDelete() {
  showDeleteConfirm.value = false;
  addressToDelete.value = null;
}

async function confirmDelete() {
  if (addressToDelete.value === null) return;

  const id = addressToDelete.value;
  showDeleteConfirm.value = false;

  try {
    const response = await api.del(`/addresses.php?id=${id}`);

    if (response.success) {
      await loadAddresses();
      emit("success", "Adresse erfolgreich gelöscht!");
    }
  } catch (err: unknown) {
    const errorMessage = err instanceof Error ? err.message : "Fehler beim Löschen der Adresse";
    emit("error", errorMessage);
  } finally {
    addressToDelete.value = null;
  }
}
</script>
