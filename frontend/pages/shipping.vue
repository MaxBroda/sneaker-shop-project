<template>
  <div class="container mx-auto px-4 py-12 max-w-4xl">
    <h1 class="text-4xl font-bold mb-4 text-center text-shop-blue-dark">
      Versand & Lieferung
    </h1>
    <p class="text-center mb-12">
      Schnell, sicher und klimaneutral zu dir nach Hause
    </p>

    <section class="mb-12">
      <div class="bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-shop-blue-dark flex items-center gap-2">
          <Icon name="mdi:truck-delivery" class="w-7 h-7" />
          Versandkosten
        </h2>
        <div class="space-y-4">
          <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
            <div>
              <p class="font-semibold">Deutschland</p>
              <p class="text-sm ">Ab 50€ kostenlos</p>
            </div>
            <span class="font-bold text-shop-blue-light">4,90€</span>
          </div>
          <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
            <div>
              <p class="font-semibold">EU-Länder</p>
              <p class="text-sm ">Ab 75€ kostenlos</p>
            </div>
            <span class="font-bold text-shop-blue-light">9,90€</span>
          </div>
          <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
            <div>
              <p class="font-semibold">Schweiz</p>
              <p class="text-sm ">Ab 100€ kostenlos</p>
            </div>
            <span class="font-bold text-shop-blue-light">14,90€</span>
          </div>
        </div>
      </div>
    </section>

    <section class="mb-12">
      <div class="bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-shop-blue-dark flex items-center gap-2">
          <Icon name="mdi:clock-outline" class="w-7 h-7" />
          Lieferzeiten
        </h2>
        <div class="grid md:grid-cols-3 gap-4">
          <div class="p-4 border-l-4 border-shop-blue-light bg-gray-50 rounded">
            <p class="font-semibold mb-1">Deutschland</p>
            <p class="text-2xl font-bold text-shop-blue-light">2-4</p>
            <p class="text-sm ">Werktage</p>
          </div>
          <div class="p-4 border-l-4 border-shop-blue-light bg-gray-50 rounded">
            <p class="font-semibold mb-1">EU-Länder</p>
            <p class="text-2xl font-bold text-shop-blue-light">4-7</p>
            <p class="text-sm ">Werktage</p>
          </div>
          <div class="p-4 border-l-4 border-shop-blue-light bg-gray-50 rounded">
            <p class="font-semibold mb-1">Schweiz</p>
            <p class="text-2xl font-bold text-shop-blue-light">5-8</p>
            <p class="text-sm ">Werktage</p>
          </div>
        </div>
        <p class="text-sm mt-4">
          * Bestellungen, die vor 14:00 Uhr eingehen, werden noch am selben Werktag versandt.
        </p>
      </div>
    </section>

    <section class="mb-12">
      <div class="bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-shop-blue-dark flex items-center gap-2">
          <Icon name="mdi:package-variant" class="w-7 h-7" />
          Unsere Versandpartner
        </h2>
        <p class="mb-4">
          Wir arbeiten mit DHL GoGreen zusammen - klimaneutraler Versand ist für uns Standard. 
          Du erhältst automatisch eine Versandbestätigung mit Tracking-Nummer per E-Mail.
        </p>
        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
          <p class="text-sm font-semibold text-signal-green flex items-center gap-2">
            <Icon name="mdi:leaf" class="w-5 h-5" />
            100% klimaneutraler Versand durch CO₂-Kompensation
          </p>
        </div>
      </div>
    </section>

    <section class="mb-12">
      <div class="bg-gradient-to-br from-shop-blue-dark to-shop-blue-light text-white p-8 rounded-xl">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <Icon name="mdi:map-marker-path" class="w-7 h-7" />
          Sendungsverfolgung
        </h2>
        <p class="mb-4">
          Verfolge deine Bestellung in Echtzeit! Nach dem Versand erhältst du:
        </p>
        <ul class="space-y-2 ml-4">
          <li class="flex items-start gap-2">
            <Icon name="mdi:check-circle" class="w-5 h-5 mt-0.5 flex-shrink-0" />
            <span>Eine E-Mail mit der Tracking-Nummer</span>
          </li>
          <li class="flex items-start gap-2">
            <Icon name="mdi:check-circle" class="w-5 h-5 mt-0.5 flex-shrink-0" />
            <span>Zugriff auf die Sendungsverfolgung in deinem Kundenkonto</span>
          </li>
          <li class="flex items-start gap-2">
            <Icon name="mdi:check-circle" class="w-5 h-5 mt-0.5 flex-shrink-0" />
            <span>SMS-Benachrichtigung am Liefertag (optional)</span>
          </li>
        </ul>
      </div>
    </section>

    <section>
      <div class="bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-shop-blue-dark flex items-center gap-2">
          <Icon name="mdi:recycle" class="w-7 h-7" />
          Nachhaltige Verpackung
        </h2>
        <p class="mb-4">
          Auch beim Versand denken wir grün! Alle Verpackungsmaterialien sind:
        </p>
        <div class="grid md:grid-cols-2 gap-4">
          <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
            <Icon name="mdi:check-bold" class="w-6 h-6 text-signal-green flex-shrink-0" />
            <div>
              <p class="font-semibold">100% recycelt</p>
              <p class="text-sm ">Aus bereits verwendeten Materialien</p>
            </div>
          </div>
          <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
            <Icon name="mdi:check-bold" class="w-6 h-6 text-signal-green flex-shrink-0" />
            <div>
              <p class="font-semibold">100% recycelbar</p>
              <p class="text-sm ">Komplett wiederverwendbar</p>
            </div>
          </div>
          <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
            <Icon name="mdi:check-bold" class="w-6 h-6 text-signal-green flex-shrink-0" />
            <div>
              <p class="font-semibold">Plastikfrei</p>
              <p class="text-sm ">Kein Plastik in der gesamten Verpackung</p>
            </div>
          </div>
          <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
            <Icon name="mdi:check-bold" class="w-6 h-6 text-signal-green flex-shrink-0" />
            <div>
              <p class="font-semibold">FSC-zertifiziert</p>
              <p class="text-sm ">Aus nachhaltiger Forstwirtschaft</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
