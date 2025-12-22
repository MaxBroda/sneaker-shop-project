<template>
  <div>
    <section class="relative bg-gradient-to-br from-shop-blue-dark via-shop-blue-light to-shop-blue-dark text-white py-20 md:py-32">
      <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center">
          <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
            Nachhaltig. Innovativ. <br class="hidden md:block" />Unverwechselbar.
          </h1>
          <p class="text-lg md:text-xl mb-8 max-w-2xl mx-auto opacity-95">
            Entdecke Sneaker, die nicht nur gut aussehen, sondern auch Gutes tun.
            Moderne Designs treffen auf umweltfreundliche Produktion.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <NuxtLink
              to="/products"
              class="px-8 py-3 bg-white text-shop-blue-dark rounded-lg font-semibold hover:bg-gray-100 transition-all shadow-lg"
            >
              Jetzt entdecken
            </NuxtLink>
            <NuxtLink
              to="/about"
              class="px-8 py-3 border-2 border-white rounded-lg font-semibold hover:bg-white hover:text-shop-blue-dark transition-all"
            >
              Mehr erfahren
            </NuxtLink>
          </div>
        </div>
      </div>
    </section>

    <section class="py-16 md:py-24 bg-gray-50">
      <div class="container mx-auto px-4 max-w-6xl">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-shop-blue-dark scroll-fade">
          Warum EcoStep?
        </h2>
        <div class="grid md:grid-cols-3 gap-8">
          <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition-all hover:scale-105 scroll-fade">
            <div class="text-4xl mb-4">🌱</div>
            <h3 class="text-xl font-bold mb-3 text-shop-blue-dark">100% Nachhaltig</h3>
            <p class="">
              Recycelte Materialien, biologisch abbaubare Sohlen und klimaneutrale Produktion.
              Jeder Schritt zählt.
            </p>
          </div>
          <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition-all hover:scale-105 scroll-fade">
            <div class="text-4xl mb-4">⚡</div>
            <h3 class="text-xl font-bold mb-3 text-shop-blue-dark">Innovation trifft Design</h3>
            <p class="">
              Modernste Technologie in jedem Sneaker. Atmungsaktiv, leicht und perfekt für deinen
              aktiven Lifestyle.
            </p>
          </div>
          <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition-all hover:scale-105 scroll-fade">
            <div class="text-4xl mb-4">✨</div>
            <h3 class="text-xl font-bold mb-3 text-shop-blue-dark">Style & Komfort</h3>
            <p class="">
              Minimalistisches Design, maximaler Komfort. Von der Straße bis ins Studio –
              immer die richtige Wahl.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="py-16 md:py-20 bg-shop-blue-dark text-white">
      <div class="container mx-auto px-4 max-w-6xl">
        <div class="grid md:grid-cols-4 gap-8 text-center scroll-fade">
          <div>
            <div class="text-4xl md:text-5xl font-bold mb-2 text-white">15.000+</div>
            <p class="text-shop-blue-light">Zufriedene Kunden</p>
          </div>
          <div>
            <div class="text-4xl md:text-5xl font-bold mb-2 text-white">95%</div>
            <p class="text-shop-blue-light">CO₂-Reduktion</p>
          </div>
          <div>
            <div class="text-4xl md:text-5xl font-bold mb-2 text-white">100%</div>
            <p class="text-shop-blue-light">Recycelte Materialien</p>
          </div>
          <div>
            <div class="text-4xl md:text-5xl font-bold mb-2 text-white">50+</div>
            <p class="text-shop-blue-light">Einzigartige Modelle</p>
          </div>
        </div>
      </div>
    </section>

    <section class="py-16 md:py-24">
      <div class="container mx-auto px-4 max-w-4xl text-center scroll-fade">
        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-shop-blue-dark">
          Bereit für den nächsten Schritt?
        </h2>
        <p class="text-lg mb-8">
          Werde Teil der Bewegung. Entdecke Sneaker, die Style und Nachhaltigkeit vereinen.
        </p>
        <NuxtLink
          to="/products"
          class="inline-block px-10 py-4 bg-shop-blue-light text-white rounded-lg font-semibold hover:bg-shop-blue-dark transition-all shadow-lg text-lg hover:scale-105"
        >
          Zur Kollektion
        </NuxtLink>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
// Homepage component - static content
</script>
