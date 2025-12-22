<template>
  <div class="bg-white p-6 shadow-md rounded-xl">
    <h2 class="text-xl font-semibold mb-4">Bestellungen verwalten</h2>

    <div v-if="isLoading" class="text-center py-8">
      <p class="text-gray-500">Lade Bestellungen...</p>
    </div>

    <AlertMessage v-if="error" type="error" :message="error" class="mb-4" />

    <div v-else-if="orders.length === 0" class="text-center py-8">
      <p class="text-lg font-semibold">Noch keine Bestellungen vorhanden.</p>
      <p class="text-sm mt-2 font-light text-gray-500">
        Bestellungen für Ihre Produkte werden hier angezeigt.
      </p>
    </div>

    <div v-else class="space-y-6">
      <div
        v-for="order in orders"
        :key="order.id"
        class="border rounded-lg p-4 hover:shadow-md transition-shadow"
      >
        <div class="flex justify-between items-start mb-4 pb-4 border-b">
          <div>
            <h3 class="font-semibold text-lg">
              Bestellung {{ order.order_number }}
            </h3>
            <p class="text-sm text-gray-500">
              {{ formatDate(order.created_at) }}
            </p>
            <p v-if="order.customer" class="text-sm text-gray-500 mt-1">
              Kunde: {{ order.customer.first_name }}
              {{ order.customer.last_name }} ({{ order.customer.email }})
            </p>
            <p v-else class="text-sm text-gray-500 mt-1">Gast-Bestellung</p>
            <p
              v-if="order.payment_status === 'open'"
              class="inline-block text-sm text-orange-600 font-medium mt-1 bg-orange-100 px-2 py-1 rounded-full"
            >
              ⚠️ Zahlung offen
            </p>
          </div>
          <div class="text-right">
            <div class="relative inline-block">
              <button
                @click="toggleDropdown(order.id)"
                :class="getStatusClass(order.status)"
                class="px-4 py-1.5 rounded-full text-sm font-medium cursor-pointer border-0 flex items-center gap-2"
              >
                <span>{{ getStatusText(order.status) }}</span>
                <Icon
                  name="mdi:chevron-down"
                  class="w-4 h-4 transition-transform duration-200"
                  :class="{ 'rotate-180': openDropdown === order.id }"
                />
              </button>

              <div
                v-if="openDropdown === order.id"
                class="absolute right-0 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 z-10 min-w-[180px]"
              >
                <button
                  v-for="status in statusOptions"
                  :key="status.value"
                  @click="selectStatus(order.id, status.value)"
                  :class="[
                    'w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition-colors',
                    order.status === status.value
                      ? 'bg-gray-100 font-medium'
                      : '',
                  ]"
                  class="first:rounded-t-lg last:rounded-b-lg"
                >
                  <div class="flex items-center gap-2">
                    <div
                      :class="getStatusDotClass(status.value)"
                      class="w-2 h-2 rounded-full"
                    ></div>
                    <span>{{ status.label }}</span>
                  </div>
                </button>
              </div>
            </div>
            <p class="mt-2 font-semibold text-lg">
              {{ formatPrice(order.total) }}
            </p>
          </div>
        </div>

        <div class="mb-4">
          <h4 class="font-medium mb-3">Ihre Artikel in dieser Bestellung</h4>
          <div class="space-y-3">
            <div
              v-for="item in order.items"
              :key="item.id"
              class="flex items-center gap-4"
            >
              <img
                v-if="item.product_image"
                :src="item.product_image"
                :alt="item.product_name"
                class="w-16 h-16 object-cover rounded"
              />
              <div class="flex-1">
                <p class="font-medium">{{ item.product_name }}</p>
                <p class="text-sm text-gray-500">Größe: {{ item.size }}</p>
                <p class="text-sm text-gray-500">Menge: {{ item.quantity }}</p>
              </div>
              <div class="text-right">
                <p class="font-medium">
                  {{ formatPrice(item.price * item.quantity) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4 pt-4 border-t">
          <div>
            <h4 class="font-medium mb-2">Rechnungsadresse</h4>
            <div class="text-sm text-gray-500">
              <p>
                {{ order.billing_address.first_name }}
                {{ order.billing_address.last_name }}
              </p>
              <p>
                {{ order.billing_address.street }}
                {{ order.billing_address.house_number }}
              </p>
              <p>
                {{ order.billing_address.postal_code }}
                {{ order.billing_address.city }}
              </p>
              <p>{{ order.billing_address.country }}</p>
            </div>
          </div>

          <div v-if="order.shipping_address">
            <h4 class="font-medium mb-2">Lieferadresse</h4>
            <div class="text-sm text-gray-500">
              <p>
                {{ order.shipping_address.street }}
                {{ order.shipping_address.house_number }}
              </p>
              <p>
                {{ order.shipping_address.postal_code }}
                {{ order.shipping_address.city }}
              </p>
              <p>{{ order.shipping_address.country }}</p>
            </div>
          </div>
          <div v-else class="text-sm text-gray-500">
            <h4 class="font-medium mb-2">Lieferadresse</h4>
            <p>Gleich wie Rechnungsadresse</p>
          </div>
        </div>

        <div class="mt-4 pt-4 border-t">
          <p class="text-sm text-gray-500">
            <span class="font-medium">Zahlungsmethode:</span>
            {{ getPaymentMethodText(order.payment_method) }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import AlertMessage from "~/components/ui/AlertMessage.vue";

const api = useApi();

interface Order {
  id: number;
  order_number: string;
  total: number;
  status: string;
  payment_status: string;
  billing_address: Record<string, string>;
  shipping_address: Record<string, string> | null;
  payment_method: string;
  created_at: string;
  customer: {
    email: string;
    first_name: string;
    last_name: string;
  } | null;
  items: Array<{
    id: number;
    product_id: number;
    product_name: string;
    product_image: string | null;
    quantity: number;
    size: string;
    price: number;
  }>;
}

const orders = ref<Order[]>([]);
const isLoading = ref(false);
const error = ref("");
const openDropdown = ref<number | null>(null);

const statusOptions = [
  { value: "pending", label: "Ausstehend" },
  { value: "processing", label: "In Bearbeitung" },
  { value: "shipped", label: "Versandt" },
  { value: "delivered", label: "Zugestellt" },
  { value: "cancelled", label: "Storniert" },
];

async function fetchOrders() {
  try {
    isLoading.value = true;
    error.value = "";

    const response = await api.get<Order[]>("/seller-orders.php");

    if (response.success && response.data) {
      orders.value = response.data;
    } else {
      throw new Error(response.message || "Fehler beim Laden der Bestellungen");
    }
  } catch (err: unknown) {
    const errorMessage = err instanceof Error ? err.message : "Fehler beim Laden der Bestellungen";
    error.value = errorMessage;
  } finally {
    isLoading.value = false;
  }
}

async function updateOrderStatus(orderId: number, newStatus: string) {
  try {
    const response = await api.put("/seller-orders.php", {
      order_id: orderId,
      status: newStatus,
    });

    if (response.success) {
      const order = orders.value.find((o) => o.id === orderId);
      if (order) {
        order.status = newStatus;
      }
    } else {
      throw new Error(
        response.message || "Fehler beim Aktualisieren des Status"
      );
    }
  } catch (err: unknown) {
    const errorMessage = err instanceof Error ? err.message : "Fehler beim Aktualisieren des Status";
    error.value = errorMessage;
  }
}

function toggleDropdown(orderId: number) {
  openDropdown.value = openDropdown.value === orderId ? null : orderId;
}

function selectStatus(orderId: number, newStatus: string) {
  updateOrderStatus(orderId, newStatus);
  openDropdown.value = null;
}

function handleClickOutside(e: MouseEvent) {
  const target = e.target as HTMLElement;
  if (!target.closest(".relative")) {
    openDropdown.value = null;
  }
}

onMounted(() => {
  fetchOrders();
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});

function formatDate(dateString: string): string {
  const date = new Date(dateString);
  return date.toLocaleDateString("de-DE", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

function formatPrice(price: number): string {
  return `${price.toFixed(2)} €`;
}

function getStatusClass(status: string): string {
  const classMap: Record<string, string> = {
    pending: "bg-yellow-100 text-yellow-800",
    processing: "bg-blue-100 text-blue-800",
    shipped: "bg-purple-100 text-purple-800",
    delivered: "bg-green-100 text-green-800",
    cancelled: "bg-red-100 text-red-800",
  };
  return classMap[status] || "bg-gray-100 ";
}

function getStatusDotClass(status: string): string {
  const classMap: Record<string, string> = {
    pending: "bg-yellow-500",
    processing: "bg-blue-500",
    shipped: "bg-purple-500",
    delivered: "bg-green-500",
    cancelled: "bg-red-500",
  };
  return classMap[status] || "bg-gray-500";
}

function getStatusText(status: string): string {
  const textMap: Record<string, string> = {
    pending: "Ausstehend",
    processing: "In Bearbeitung",
    shipped: "Versandt",
    delivered: "Zugestellt",
    cancelled: "Storniert",
  };
  return textMap[status] || status;
}

function getPaymentMethodText(method: string): string {
  const methodMap: Record<string, string> = {
    paypal: "PayPal",
    klarna: "Klarna",
    creditcard: "Kreditkarte",
    mastercard: "Mastercard",
    maestro: "Maestro",
  };
  return methodMap[method] || method;
}
</script>
