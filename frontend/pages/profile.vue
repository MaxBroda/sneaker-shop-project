<template>
  <div class="container mx-auto px-4 md:py-8 max-w-6xl">
    <h1 class="text-2xl font-bold mb-6 max-md:text-center">Mein Profil</h1>

    <div
      v-if="errorMessage"
      class="bg-red-100 border border-red-400 text-signal-red px-4 py-3 rounded-xl mb-4"
    >
      {{ errorMessage }}
    </div>
    <div
      v-if="successMessage"
      class="bg-green-100 border border-green-400 text-signal-green px-4 py-3 rounded-xl mb-4"
    >
      {{ successMessage }}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <!-- Left Navigation -->
      <div class="col-span-1">
        <ProfileNav
          :activeSection="activeSection"
          @select="handleSectionChange"
        />
      </div>

      <!-- Right Content -->
      <div class="col-span-1 md:col-span-3">
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

// Check authentication
onMounted(() => {
  if (typeof window !== "undefined") {
    const savedUser = localStorage.getItem("user");

    if (!savedUser) {
      navigateTo("/login");
      return;
    }

    // Check for section in URL query
    const route = useRoute();
    if (route.query.section) {
      activeSection.value = route.query.section as string;
    }
  }
});

function handleSectionChange(sectionId: string) {
  activeSection.value = sectionId;
  
  // Update URL without reloading
  const router = useRouter();
  router.push({ query: { section: sectionId } });
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
