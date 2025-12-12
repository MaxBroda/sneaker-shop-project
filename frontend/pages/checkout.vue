<template>
  <div class="min-h-screen bg-gradient-to-b from-shop-bright to-white">
    <section
      class="bg-gradient-to-br from-shop-blue-dark via-shop-blue-light to-shop-blue-dark text-white py-12"
    >
      <div class="container mx-auto px-4 max-w-6xl text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Zur Kasse</h1>
        <p class="text-lg opacity-90">{{ getStepTitle() }}</p>
      </div>
    </section>

    <div class="container mx-auto px-4 max-w-6xl py-8">
      <CheckoutAuth
        v-if="checkoutStep === 'auth'"
        @login-success="handleLoginSuccess"
        @guest-checkout="handleGuestCheckout"
      />

      <CheckoutForm
        v-else-if="checkoutStep === 'checkout'"
        :user="user"
        :is-guest="isGuestCheckout"
        @order-complete="handleOrderComplete"
      />

      <CheckoutSuccess
        v-else-if="checkoutStep === 'success'"
        :order-number="orderNumber"
        :payment-status="paymentStatus"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
const { user } = useAuth();
const route = useRoute();
const config = useRuntimeConfig();

type CheckoutStep = "auth" | "checkout" | "success";

const checkoutStep = ref<CheckoutStep>("auth");
const isGuestCheckout = ref(false);
const orderNumber = ref("");
const paymentStatus = ref<string | null>(null);

onMounted(async () => {
  // Check if returning from Mollie payment
  if (route.query.payment === "return" && route.query.order) {
    orderNumber.value = route.query.order as string;
    checkoutStep.value = "success";

    // Fetch actual payment status from backend
    try {
      const response = await $fetch<any>(
        `${config.public.apiUrl}/mollie-payment.php?order_number=${orderNumber.value}`
      );
      if (response.success) {
        paymentStatus.value = response.data.is_paid
          ? "paid"
          : response.data.status;
      } else {
        paymentStatus.value = "processing";
      }
    } catch (error) {
      paymentStatus.value = "processing";
    }

    return;
  }

  if (user.value) {
    checkoutStep.value = "checkout";
  } else {
    checkoutStep.value = "auth";
  }
});

watch(user, (newUser) => {
  if (newUser && checkoutStep.value === "auth") {
    checkoutStep.value = "checkout";
  }
});

function getStepTitle(): string {
  switch (checkoutStep.value) {
    case "auth":
      return "Anmelden oder als Gast fortfahren";
    case "checkout":
      return isGuestCheckout.value
        ? "Bestellung als Gast abschließen"
        : "Bestellung abschließen";
    case "success":
      return "Bestellung erfolgreich!";
    default:
      return "";
  }
}

function handleLoginSuccess() {
  checkoutStep.value = "checkout";
  isGuestCheckout.value = false;
}

function handleGuestCheckout() {
  checkoutStep.value = "checkout";
  isGuestCheckout.value = true;
}

function handleOrderComplete(orderNum: string) {
  orderNumber.value = orderNum;
  checkoutStep.value = "success";
  window.scrollTo({ top: 0, behavior: "smooth" });
}
</script>
