<template>
  <div class="bg-white p-6 shadow-md rounded-xl h-full">
    <h2 class="text-xl font-semibold mb-4">Kontoinformationen</h2>
    <div class="grid md:grid-cols-2 gap-4">
      <div>
        <p class="text-sm text-gray-500">Vorname</p>
        <p class="font-medium">{{ user?.firstName || "-" }}</p>
      </div>
      <div>
        <p class="text-sm text-gray-500">Nachname</p>
        <p class="font-medium">{{ user?.lastName || "-" }}</p>
      </div>
      <div>
        <p class="text-sm text-gray-500">E-Mail</p>
        <p class="font-medium">{{ user?.email || "-" }}</p>
      </div>
      <div>
        <p class="text-sm text-gray-500">Rolle</p>
        <p class="font-medium">
          {{ user?.role === "seller" ? "Verkäufer" : "Käufer" }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { user } = useAuth();
</script>
