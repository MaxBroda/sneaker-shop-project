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
        <AlertMessage type="error" :message="errorMessage" class="mb-4" />
        <AlertMessage type="success" :message="successMessage" class="mb-4" />

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
        <SellerOrdersSection v-else-if="activeSection === 'seller-orders'" />
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
import SellerOrdersSection from "~/components/profile/SellerOrdersSection.vue";
import AlertMessage from '~/components/ui/AlertMessage.vue';

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
