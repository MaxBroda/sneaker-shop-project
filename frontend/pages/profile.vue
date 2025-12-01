<template>
  <div class="container mx-auto px-4 md:py-8 max-w-6xl min-h-55vh">
    <h1 class="text-2xl font-bold mb-6 max-md:text-center">Mein Profil</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="col-span-1">
        <ProfileNav
          :activeSection="activeSection"
          @select="handleSectionChange"
        />
      </div>

      <div class="col-span-1 md:col-span-3">
        <div
          v-if="errorMessage"
          class="bg-red-50 border-l-4 border-signal-red text-signal-red px-4 py-3 rounded-xl mb-4 flex items-start gap-3"
        >
          <Icon name="mdi:alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5" />
          <span>{{ errorMessage }}</span>
        </div>
        <div
          v-if="successMessage"
          class="bg-green-50 border-l-4 border-signal-green text-signal-green px-4 py-3 rounded-xl mb-4 flex items-start gap-3"
        >
          <Icon name="mdi:check-circle" class="w-5 h-5 flex-shrink-0 mt-0.5" />
          <span>{{ successMessage }}</span>
        </div>

        <AccountInfo v-if="activeSection === 'account'" />
        <AddressesSection
          v-else-if="activeSection === 'addresses'"
          @success="handleSuccess"
          @error="handleError"
        />
        <OrdersSection v-else-if="activeSection === 'orders'" />
        <ProductsSection
          v-else-if="activeSection === 'products'"
          @success="handleSuccess"
          @error="handleError"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import ProfileNav from "~/components/profile/ProfileNav.vue";
import AccountInfo from "~/components/profile/AccountInfo.vue";
import AddressesSection from "~/components/profile/AddressesSection.vue";
import OrdersSection from "~/components/profile/OrdersSection.vue";
import ProductsSection from "~/components/profile/ProductsSection.vue";

const activeSection = ref("account");
const errorMessage = ref("");
const successMessage = ref("");
const route = useRoute();

onMounted(() => {
  if (typeof window !== "undefined") {
    const savedUser = localStorage.getItem("user");

    if (!savedUser) {
      navigateTo("/login");
      return;
    }
  }
});

watchEffect(() => {
  activeSection.value = (route.query.section as string) || "account";
});

function handleSectionChange(sectionId: string) {
  navigateTo({ query: { section: sectionId } });
}

function handleSuccess(message: string) {
  successMessage.value = message;
  setTimeout(() => {
    successMessage.value = "";
  }, 3000);
}

function handleError(message: string) {
  errorMessage.value = message;
  setTimeout(() => {
    errorMessage.value = "";
  }, 3000);
}
</script>
