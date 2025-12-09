<template>
  <div class="max-w-3xl mx-auto">
    <div class="bg-white p-8 md:p-12 rounded-2xl shadow-lg text-center">
      <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <Icon name="mdi:check-circle" class="w-12 h-12 text-signal-green" />
      </div>

      <h2 class="text-3xl font-bold text-shop-blue-dark mb-4">
        Vielen Dank für deine Bestellung!
      </h2>

      <p class="text-gray-500 mb-6">
        Deine Bestellung wurde erfolgreich aufgegeben und wird bearbeitet.
      </p>

      <div class="bg-blue-50 rounded-xl p-6 mb-8">
        <p class="text-sm text-gray-500 mb-2">Bestellnummer:</p>
        <p class="text-2xl font-bold text-shop-blue-dark font-mono">{{ orderNumber }}</p>
      </div>

      <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="p-4 bg-gray-50 rounded-xl">
          <Icon name="mdi:email" class="w-8 h-8 text-shop-blue-light mx-auto mb-2" />
          <p class="font-semibold text-sm mb-1">Bestätigung per E-Mail</p>
          <p class="text-xs text-gray-500">Du erhältst eine E-Mail mit allen Details</p>
        </div>
        <div class="p-4 bg-gray-50 rounded-xl">
          <Icon name="mdi:truck-delivery" class="w-8 h-8 text-shop-blue-light mx-auto mb-2" />
          <p class="font-semibold text-sm mb-1">Versand in 2-4 Tagen</p>
          <p class="text-xs text-gray-500">Klimaneutraler Versand mit DHL</p>
        </div>
        <div class="p-4 bg-gray-50 rounded-xl">
          <Icon name="mdi:package-variant" class="w-8 h-8 text-shop-blue-light mx-auto mb-2" />
          <p class="font-semibold text-sm mb-1">Sendungsverfolgung</p>
          <p class="text-xs text-gray-500">Tracking-Link in der E-Mail</p>
        </div>
      </div>

      <div class="space-y-3">
        <NuxtLink
          to="/products"
          class="block w-full bg-shop-blue-dark hover:bg-shop-blue-light text-white py-3 rounded-lg font-bold transition-all"
        >
          Weiter einkaufen
        </NuxtLink>
        <NuxtLink
          to="/"
          class="block w-full bg-gray-100 hover:bg-gray-200  py-3 rounded-lg font-semibold transition-all"
        >
          Zur Startseite
        </NuxtLink>
      </div>

      <div class="mt-8 pt-8 border-t border-gray-200">
        <p class="text-sm text-gray-500 mb-4">
          Hast du Fragen zu deiner Bestellung?
        </p>
        <NuxtLink
          to="/contact"
          class="text-shop-blue-light hover:underline font-semibold"
        >
          Kontaktiere unseren Kundenservice
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  orderNumber: string;
}

defineProps<Props>();
</script>
