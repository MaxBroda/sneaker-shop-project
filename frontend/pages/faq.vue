<template>
  <div class="container mx-auto px-4 py-12 max-w-4xl">
    <h1 class="text-4xl font-bold mb-8 text-center text-shop-blue-dark">
      Häufig gestellte Fragen (FAQ)
    </h1>

    <div class="space-y-4">
      <!-- Bestellung & Zahlung -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <details class="group">
          <summary class="p-6 cursor-pointer font-semibold text-lg text-shop-blue-dark hover:bg-gray-50 transition-colors flex justify-between items-center">
            Welche Zahlungsmethoden akzeptiert ihr?
            <Icon name="mdi:chevron-down" class="w-6 h-6 group-open:rotate-180 transition-transform" />
          </summary>
          <div class="px-6 pb-6 ">
            Wir nutzen einen sicheren Payment-Service-Provider und akzeptieren alle gängigen Zahlungsmethoden: Kreditkarten (Visa, Mastercard, American Express), PayPal, Sofortüberweisung, SEPA-Lastschrift und weitere lokale Zahlungsarten. Alle Zahlungen werden verschlüsselt und sicher verarbeitet.
          </div>
        </details>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <details class="group">
          <summary class="p-6 cursor-pointer font-semibold text-lg text-shop-blue-dark hover:bg-gray-50 transition-colors flex justify-between items-center">
            Kann ich meine Bestellung stornieren?
            <Icon name="mdi:chevron-down" class="w-6 h-6 group-open:rotate-180 transition-transform" />
          </summary>
          <div class="px-6 pb-6 ">
            Ja, solange deine Bestellung noch nicht versandt wurde, kannst du sie kostenlos stornieren. Kontaktiere dazu einfach unseren Kundenservice unter support@ecostep.de oder über das Kontaktformular.
          </div>
        </details>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <details class="group">
          <summary class="p-6 cursor-pointer font-semibold text-lg text-shop-blue-dark hover:bg-gray-50 transition-colors flex justify-between items-center">
            Sind meine Zahlungsdaten sicher?
            <Icon name="mdi:chevron-down" class="w-6 h-6 group-open:rotate-180 transition-transform" />
          </summary>
          <div class="px-6 pb-6 ">
            Ja, deine Sicherheit hat höchste Priorität. Wir verwenden einen zertifizierten Payment-Service-Provider, der höchste Sicherheitsstandards erfüllt. Alle Zahlungsdaten werden verschlüsselt übertragen und wir speichern keine sensiblen Zahlungsinformationen auf unseren Servern.
          </div>
        </details>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <details class="group">
          <summary class="p-6 cursor-pointer font-semibold text-lg text-shop-blue-dark hover:bg-gray-50 transition-colors flex justify-between items-center">
            Erhalte ich eine Rechnung?
            <Icon name="mdi:chevron-down" class="w-6 h-6 group-open:rotate-180 transition-transform" />
          </summary>
          <div class="px-6 pb-6 ">
            Ja, mit jeder Bestellung erhältst du automatisch eine Rechnung per E-Mail. Diese findest du auch in deinem Kundenkonto unter "Bestellungen".
          </div>
        </details>
      </div>

      <!-- Versand -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <details class="group">
          <summary class="p-6 cursor-pointer font-semibold text-lg text-shop-blue-dark hover:bg-gray-50 transition-colors flex justify-between items-center">
            Wie lange dauert der Versand?
            <Icon name="mdi:chevron-down" class="w-6 h-6 group-open:rotate-180 transition-transform" />
          </summary>
          <div class="px-6 pb-6 ">
            Innerhalb Deutschlands beträgt die Lieferzeit 2-4 Werktage. In andere EU-Länder 4-7 Werktage. Du erhältst eine Versandbestätigung mit Tracking-Nummer per E-Mail.
          </div>
        </details>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <details class="group">
          <summary class="p-6 cursor-pointer font-semibold text-lg text-shop-blue-dark hover:bg-gray-50 transition-colors flex justify-between items-center">
            Ist der Versand kostenlos?
            <Icon name="mdi:chevron-down" class="w-6 h-6 group-open:rotate-180 transition-transform" />
          </summary>
          <div class="px-6 pb-6 ">
            Ja! Wir bieten kostenlosen Versand ab einem Bestellwert von 50€ innerhalb Deutschlands. Für Bestellungen unter 50€ berechnen wir 4,90€ Versandkosten.
          </div>
        </details>
      </div>

      <!-- Produkt & Größe -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <details class="group">
          <summary class="p-6 cursor-pointer font-semibold text-lg text-shop-blue-dark hover:bg-gray-50 transition-colors flex justify-between items-center">
            Wie finde ich die richtige Größe?
            <Icon name="mdi:chevron-down" class="w-6 h-6 group-open:rotate-180 transition-transform" />
          </summary>
          <div class="px-6 pb-6 ">
            Nutze unseren <NuxtLink to="/size-guide" class="text-shop-blue-light hover:underline">Größenberater</NuxtLink> mit detaillierten Größentabellen und Messanleitungen. Unsere Sneaker fallen normal aus.
          </div>
        </details>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <details class="group">
          <summary class="p-6 cursor-pointer font-semibold text-lg text-shop-blue-dark hover:bg-gray-50 transition-colors flex justify-between items-center">
            Sind eure Sneaker wirklich nachhaltig?
            <Icon name="mdi:chevron-down" class="w-6 h-6 group-open:rotate-180 transition-transform" />
          </summary>
          <div class="px-6 pb-6 ">
            Absolut! Alle unsere Sneaker werden zu 100% aus recycelten Materialien hergestellt. Die Produktion erfolgt klimaneutral in zertifizierten Fabriken. Mehr Details findest du auf unserer <NuxtLink to="/about" class="text-shop-blue-light hover:underline">Über uns</NuxtLink> Seite.
          </div>
        </details>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <details class="group">
          <summary class="p-6 cursor-pointer font-semibold text-lg text-shop-blue-dark hover:bg-gray-50 transition-colors flex justify-between items-center">
            Wie pflege ich meine EcoStep Sneaker?
            <Icon name="mdi:chevron-down" class="w-6 h-6 group-open:rotate-180 transition-transform" />
          </summary>
          <div class="px-6 pb-6 ">
            Reinige sie mit einem feuchten Tuch und milder Seife. Vermeide die Waschmaschine und direkte Sonneneinstrahlung beim Trocknen. Bei hartnäckigen Flecken empfehlen wir unsere umweltfreundlichen Reinigungsprodukte.
          </div>
        </details>
      </div>

      <!-- Rückgabe -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <details class="group">
          <summary class="p-6 cursor-pointer font-semibold text-lg text-shop-blue-dark hover:bg-gray-50 transition-colors flex justify-between items-center">
            Wie funktioniert die Rückgabe?
            <Icon name="mdi:chevron-down" class="w-6 h-6 group-open:rotate-180 transition-transform" />
          </summary>
          <div class="px-6 pb-6 ">
            Du hast 30 Tage Zeit für kostenlose Rücksendungen. Nutze das Rücksendeetikett in deinem Paket oder lade es in deinem Kundenkonto herunter. Mehr Infos unter <NuxtLink to="/returns" class="text-shop-blue-light hover:underline">Rückgabe & Umtausch</NuxtLink>.
          </div>
        </details>
      </div>

      <!-- Kundenkonto -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <details class="group">
          <summary class="p-6 cursor-pointer font-semibold text-lg text-shop-blue-dark hover:bg-gray-50 transition-colors flex justify-between items-center">
            Brauche ich ein Kundenkonto?
            <Icon name="mdi:chevron-down" class="w-6 h-6 group-open:rotate-180 transition-transform" />
          </summary>
          <div class="px-6 pb-6 ">
            Nein, du kannst auch als Gast bestellen. Ein Kundenkonto bietet aber Vorteile: Bestellübersicht, schnellerer Checkout, Adressverwaltung und exklusive Angebote.
          </div>
        </details>
      </div>
    </div>

    <!-- Contact CTA -->
    <div class="mt-12 bg-gray-50 p-8 rounded-xl border-l-4 border-shop-blue-light text-center">
      <h2 class="text-2xl font-bold mb-4 text-shop-blue-dark">Deine Frage ist nicht dabei?</h2>
      <p class="mb-6">Unser Kundenservice hilft dir gerne weiter!</p>
      <NuxtLink
        to="/contact"
        class="inline-block px-8 py-3 bg-shop-blue-light text-white rounded-lg hover:bg-shop-blue-dark transition-all font-semibold"
      >
        Kontakt aufnehmen
      </NuxtLink>
    </div>
  </div>
</template>
