<template>
  <footer class="bg-shop-blue-dark text-white mt-20 relative">
    <div class="absolute left-1/2 -translate-x-1/2 -top-10">
      <div class="w-20 h-20 bg-shop-blue-light rounded-full flex items-center justify-center shadow-xl border-4 border-white">
        <span class="text-4xl">👟</span>
      </div>
    </div>
    
    <div class="container mx-auto px-4 py-12 max-w-6xl pt-16">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 text-center">
        <div>
          <NuxtLink to="/products">
            <h4 class="font-semibold mb-4 text-shop-blue-light hover:text-white transition-colors cursor-pointer">Shop</h4>
          </NuxtLink>
          <ul class="space-y-2 text-sm">
            <li>
              <NuxtLink to="/products?category=Sneaker" class="text-gray-300 hover:text-white transition-colors">
                Sneaker
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/products?category=Running" class="text-gray-300 hover:text-white transition-colors">
                Running
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/products?category=Training" class="text-gray-300 hover:text-white transition-colors">
                Training
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/products?category=Outdoor" class="text-gray-300 hover:text-white transition-colors">
                Outdoor
              </NuxtLink>
            </li>
          </ul>
        </div>

        <div>
          <h4 class="font-semibold mb-4 text-shop-blue-light">Unternehmen</h4>
          <ul class="space-y-2 text-sm">
            <li>
              <NuxtLink to="/about" class="text-gray-300 hover:text-white transition-colors">
                Über uns
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/contact" class="text-gray-300 hover:text-white transition-colors">
                Kontakt
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/careers" class="text-gray-300 hover:text-white transition-colors">
                Karriere
              </NuxtLink>
            </li>
            <li>
              <a href="#" class="text-gray-300 hover:text-white transition-colors">
                Nachhaltigkeit
              </a>
            </li>
          </ul>
        </div>

        <div>
          <h4 class="font-semibold mb-4 text-shop-blue-light">Hilfe & Support</h4>
          <ul class="space-y-2 text-sm">
            <li>
              <NuxtLink to="/faq" class="text-gray-300 hover:text-white transition-colors">
                FAQ
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/shipping" class="text-gray-300 hover:text-white transition-colors">
                Versand & Lieferung
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/returns" class="text-gray-300 hover:text-white transition-colors">
                Rückgabe & Umtausch
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/size-guide" class="text-gray-300 hover:text-white transition-colors">
                Größenberater
              </NuxtLink>
            </li>
          </ul>
        </div>
      </div>

      <div class="border-t border-gray-700 pt-8">
        <div class="flex justify-center gap-4 mb-6">
          <a href="#" class="hover:opacity-80 transition-opacity" title="Instagram">
            <Icon name="mdi:instagram" class="w-6 h-6 !text-white" />
          </a>
          <a href="#" class="hover:opacity-80 transition-opacity" title="Facebook">
            <Icon name="mdi:facebook" class="w-6 h-6 !text-white" />
          </a>
          <a href="#" class="hover:opacity-80 transition-opacity" title="Twitter">
            <Icon name="mdi:twitter" class="w-6 h-6 !text-white" />
          </a>
          <a href="#" class="hover:opacity-80 transition-opacity" title="LinkedIn">
            <Icon name="mdi:linkedin" class="w-6 h-6 !text-white" />
          </a>
        </div>

        <div class="flex flex-wrap justify-center gap-4 text-sm mb-4">
          <NuxtLink to="/legal" class="text-gray-500 hover:text-white transition-colors">
            Impressum
          </NuxtLink>
          <span class="">•</span>
          <NuxtLink to="/privacy" class="text-gray-500 hover:text-white transition-colors">
            Datenschutz
          </NuxtLink>
          <span class="">•</span>
          <NuxtLink to="/terms" class="text-gray-500 hover:text-white transition-colors">
            AGB
          </NuxtLink>
        </div>
        
        <p class="text-sm text-gray-500 text-center">
          © {{ currentYear }} EcoStep GmbH. Alle Rechte vorbehalten.
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
const currentYear = new Date().getFullYear();
</script>
