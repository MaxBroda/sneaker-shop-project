<template>
  <div class="container mx-auto px-4 py-12 max-w-4xl">
    <h1 class="text-4xl font-bold mb-4 text-center text-shop-blue-dark">
      Größenberater
    </h1>
    <p class="text-center mb-12">
      Finde deine perfekte Passform
    </p>

    <section class="mb-12">
      <div class="bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-shop-blue-dark flex items-center gap-2">
          <Icon name="mdi:ruler" class="w-7 h-7" />
          Größentabelle
        </h2>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-shop-blue-dark text-white">
                <th class="p-3 text-left">EU</th>
                <th class="p-3 text-left">UK</th>
                <th class="p-3 text-left">US</th>
                <th class="p-3 text-left">cm</th>
              </tr>
            </thead>
            <tbody>
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">36</td>
                <td class="p-3">3.5</td>
                <td class="p-3">5.5</td>
                <td class="p-3">22.5</td>
              </tr>
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">37</td>
                <td class="p-3">4</td>
                <td class="p-3">6</td>
                <td class="p-3">23</td>
              </tr>
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">38</td>
                <td class="p-3">5</td>
                <td class="p-3">7</td>
                <td class="p-3">24</td>
              </tr>
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">39</td>
                <td class="p-3">6</td>
                <td class="p-3">8</td>
                <td class="p-3">25</td>
              </tr>
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">40</td>
                <td class="p-3">6.5</td>
                <td class="p-3">8.5</td>
                <td class="p-3">25.5</td>
              </tr>
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">41</td>
                <td class="p-3">7.5</td>
                <td class="p-3">9.5</td>
                <td class="p-3">26</td>
              </tr>
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">42</td>
                <td class="p-3">8</td>
                <td class="p-3">10</td>
                <td class="p-3">27</td>
              </tr>
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">43</td>
                <td class="p-3">9</td>
                <td class="p-3">11</td>
                <td class="p-3">28</td>
              </tr>
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">44</td>
                <td class="p-3">9.5</td>
                <td class="p-3">11.5</td>
                <td class="p-3">28.5</td>
              </tr>
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">45</td>
                <td class="p-3">10.5</td>
                <td class="p-3">12.5</td>
                <td class="p-3">29</td>
              </tr>
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">46</td>
                <td class="p-3">11</td>
                <td class="p-3">13</td>
                <td class="p-3">30</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <section class="mb-12">
      <div class="bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-shop-blue-dark flex items-center gap-2">
          <Icon name="mdi:foot-print" class="w-7 h-7" />
          So misst du deine Fußlänge
        </h2>
        <div class="space-y-6">
          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-shop-blue-light text-white rounded-full flex items-center justify-center font-bold">
              1
            </div>
            <div>
              <h3 class="font-semibold mb-1">Papier & Stift bereitlegen</h3>
              <p class="text-sm">
                Lege ein weißes Blatt Papier an eine Wand. Stelle dich barfuß oder in dünnen Socken mit der Ferse gegen die Wand.
              </p>
            </div>
          </div>
          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-shop-blue-light text-white rounded-full flex items-center justify-center font-bold">
              2
            </div>
            <div>
              <h3 class="font-semibold mb-1">Längsten Zeh markieren</h3>
              <p class="text-sm">
                Markiere die Stelle des längsten Zehs mit einem Stift. Achte darauf, dass der Stift senkrecht zum Papier gehalten wird.
              </p>
            </div>
          </div>
          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-shop-blue-light text-white rounded-full flex items-center justify-center font-bold">
              3
            </div>
            <div>
              <h3 class="font-semibold mb-1">Länge messen</h3>
              <p class="text-sm">
                Miss den Abstand von der Wand bis zur Markierung mit einem Lineal oder Maßband. Das ist deine Fußlänge in cm.
              </p>
            </div>
          </div>
          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-shop-blue-light text-white rounded-full flex items-center justify-center font-bold">
              4
            </div>
            <div>
              <h3 class="font-semibold mb-1">Größe ermitteln</h3>
              <p class="text-sm">
                Vergleiche deine Fußlänge mit unserer Größentabelle oben. Wähle die Größe, die deiner Messung am nächsten kommt.
              </p>
            </div>
          </div>
        </div>
        <div class="mt-6 bg-blue-50 p-4 rounded-lg border-l-4 border-shop-blue-light">
          <p class="text-sm text-shop-blue-dark">
            <strong>Tipp:</strong> Miss beide Füße! Oft ist ein Fuß etwas größer. Orientiere dich dann an der größeren Messung.
          </p>
        </div>
      </div>
    </section>

    <section class="mb-12">
      <div class="bg-gradient-to-br from-shop-blue-dark to-shop-blue-light text-white p-8 rounded-xl">
        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
          <Icon name="mdi:lightbulb-on" class="w-7 h-7" />
          Passform-Tipps
        </h2>
        <div class="grid md:grid-cols-2 gap-4">
          <div class="bg-white/10 backdrop-blur-sm p-5 rounded-lg">
            <h3 class="font-semibold mb-2">Normale Passform</h3>
            <p class="text-white/90 text-sm">
              Unsere EcoStep Sneaker fallen normal aus. Wenn du zwischen zwei Größen liegst, empfehlen wir die größere Größe für mehr Komfort.
            </p>
          </div>
          <div class="bg-white/10 backdrop-blur-sm p-5 rounded-lg">
            <h3 class="font-semibold mb-2">Breite Füße</h3>
            <p class="text-white/90 text-sm">
              Bei breiten Füßen empfehlen wir, eine halbe Größe größer zu wählen. Unsere Modelle sind atmungsaktiv und passen sich gut an.
            </p>
          </div>
          <div class="bg-white/10 backdrop-blur-sm p-5 rounded-lg">
            <h3 class="font-semibold mb-2">Sportliche Nutzung</h3>
            <p class="text-white/90 text-sm">
              Für Sport und intensive Bewegung empfehlen wir ca. 0,5-1 cm Platz im Zehenbereich, damit der Fuß nicht anstößt.
            </p>
          </div>
          <div class="bg-white/10 backdrop-blur-sm p-5 rounded-lg">
            <h3 class="font-semibold mb-2">Alltag & Freizeit</h3>
            <p class="text-white/90 text-sm">
              Für den täglichen Gebrauch wähle deine normale Größe. Die Materialien sind flexibel und passen sich deinem Fuß an.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section>
      <div class="bg-gray-50 p-8 rounded-xl border-l-4 border-shop-blue-light text-center">
        <h2 class="text-2xl font-bold mb-4 text-shop-blue-dark">Immer noch unsicher?</h2>
        <p class="mb-6">
          Kein Problem! Mit unserem 30-tägigen Rückgaberecht kannst du risikolos bestellen und zuhause anprobieren.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <NuxtLink
            to="/returns"
            class="px-8 py-3 bg-white border-2 border-shop-blue-light text-shop-blue-dark rounded-lg hover:bg-shop-blue-light hover:text-white transition-all font-semibold"
          >
            Rückgabebedingungen
          </NuxtLink>
          <NuxtLink
            to="/contact"
            class="px-8 py-3 bg-shop-blue-light text-white rounded-lg hover:bg-shop-blue-dark transition-all font-semibold"
          >
            Kundenservice
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>
