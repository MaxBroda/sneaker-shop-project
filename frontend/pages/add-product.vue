<template>
  <div>
    <section class="bg-gradient-to-br from-shop-blue-dark via-shop-blue-light to-shop-blue-dark text-white py-12">
      <div class="container mx-auto px-4 max-w-5xl text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Produkt hinzufügen</h1>
        <p class="text-lg opacity-90">Fügen Sie ein neues Produkt zu Ihrem Sortiment hinzu</p>
      </div>
    </section>

    <div class="container mx-auto px-4 max-w-6xl">
      <div class="grid md:grid-cols-3 gap-8">
        <div class="md:col-span-2">
          <div class="bg-white p-8 shadow-lg rounded-xl h-full">
            <div
              v-if="errorMessage"
              class="bg-red-50 border-l-4 border-signal-red text-signal-red px-4 py-3 rounded mb-6 flex items-start gap-2"
            >
              <Icon name="mdi:alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5" />
              <span>{{ errorMessage }}</span>
            </div>
            <div
              v-if="successMessage"
              class="bg-green-50 border-l-4 border-signal-green text-signal-green px-4 py-3 rounded mb-6 flex items-start gap-2"
            >
              <Icon name="mdi:check-circle" class="w-5 h-5 flex-shrink-0 mt-0.5" />
              <span>{{ successMessage }}</span>
            </div>

            <form @submit.prevent="addProduct" class="space-y-6">
        <div>
          <label class="block text-sm font-semibold mb-2 text-shop-blue-dark">
            Produktname *
          </label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full px-4 py-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
            placeholder="z.B. EcoStep Runner Pro"
          />
        </div>

        <div class="flex-1">
          <label class="block text-sm font-semibold mb-2 text-shop-blue-dark">
            Beschreibung
          </label>
          <textarea
            v-model="form.description"
            rows="4"
            class="w-full px-4 py-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all resize-none"
            placeholder="Beschreiben Sie die Eigenschaften und Vorteile des Produkts..."
          ></textarea>
        </div>

        <div class="flex-1">
          <label class="block text-sm font-semibold mb-2 text-shop-blue-dark">
            Technische Daten
          </label>
          <textarea
            v-model="form.technicalSpecs"
            rows="4"
            class="w-full px-4 py-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all resize-none"
            placeholder="z.B. Gewicht: 280g&#10;Obermaterial: Recyceltes Mesh&#10;Sohle: Bio-Gummi&#10;Größen: 36-48"
          ></textarea>
        </div>

        <div>
          <label class="block text-sm font-semibold mb-2 text-shop-blue-dark">
            Preis (€) *
          </label>
          <div class="relative">
            <input
              v-model="form.price"
              @input="formatPriceInput"
              type="text"
              inputmode="decimal"
              required
              class="w-full px-4 py-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
              placeholder="99.99"
            />
            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">€</span>
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold mb-2 text-shop-blue-dark">
            Produktbild *
          </label>
          <div
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            :class="[
              'border-2 border-dashed rounded-lg p-4 text-center transition-all cursor-pointer',
              isDragging ? 'border-shop-blue-light bg-shop-blue-light/10' : 'border-gray-500 hover:border-shop-blue-light'
            ]"
            @click="fileInput?.click()"
          >
            <input
              ref="fileInput"
              type="file"
              accept="image/jpeg,image/jpg,image/png,image/webp"
              @change="handleFileSelect"
              class="hidden"
            />
            
            <div v-if="imagePreview" class="space-y-2">
              <div class="relative inline-block">
                <img
                  :src="imagePreview"
                  alt="Vorschau"
                  class="w-32 h-32 object-cover rounded-lg mx-auto shadow-md"
                />
                <button
                  type="button"
                  @click.stop="removeImage"
                  class="absolute -top-2 -right-2 bg-signal-red text-white rounded-full p-1.5 hover:bg-red-700 transition-colors shadow-md flex items-center justify-center"
                >
                  <Icon name="mdi:close" class="w-5 h-5" />
                </button>
              </div>
            </div>
            
            <div v-else class="space-y-2">
              <Icon name="mdi:cloud-upload" class="w-12 h-12 mx-auto text-gray-500" />
              <div>
                <p class="font-medium">Bild hochladen</p>
                <p class="text-sm text-gray-500 mt-1">
                  Klicken oder Bild hierher ziehen
                </p>
                <p class="text-xs text-gray-500 mt-2">
                  JPG, PNG oder WebP • Max. 5MB • Annähernd quadratisch
                </p>
              </div>
            </div>
          </div>
          <p v-if="uploadError" class="text-signal-red text-sm mt-2 flex items-center gap-1">
            <Icon name="mdi:alert-circle" class="w-4 h-4" />
            {{ uploadError }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-semibold mb-3 text-shop-blue-dark">
            Kategorien (mehrfach auswählbar)
          </label>
          <div class="grid grid-cols-2 gap-3">
            <label
              v-for="category in availableCategories"
              :key="category"
              class="flex items-center space-x-3 p-3 border-2 border-gray-200 rounded-lg hover:bg-shop-blue-light/5 hover:border-shop-blue-light cursor-pointer transition-all"
            >
              <input
                type="checkbox"
                :value="category"
                v-model="form.categories"
                class="w-5 h-5 accent-shop-blue-light border-gray-500 rounded focus:ring-shop-blue-light cursor-pointer"
              />
              <span class="text-sm font-medium">{{ category }}</span>
            </label>
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold mb-3 text-shop-blue-dark">
            Produkt-Tag (Badge)
          </label>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-600 mb-2">Icon wählen</label>
              <div class="relative">
                <select
                  v-model="form.tagIcon"
                  class="w-full px-4 py-3 pr-10 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-shop-blue-light bg-white transition-all hover:bg-blue-50 hover:border-shop-blue-light cursor-pointer appearance-none"
                >
                  <option value="">Kein Icon</option>
                  <option value="mdi:leaf">🍃 Blatt (Nachhaltig)</option>
                  <option value="mdi:fire">🔥 Feuer (Bestseller)</option>
                  <option value="mdi:star">⭐ Stern (Premium)</option>
                  <option value="mdi:new-box">✨ Neu</option>
                  <option value="mdi:sale">💰 Sale</option>
                  <option value="mdi:lightning-bolt">⚡ Limitiert</option>
                  <option value="mdi:heart">❤️ Beliebt</option>
                  <option value="mdi:trending-up">📈 Trend</option>
                </select>
                <Icon name="mdi:chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" />
              </div>
            </div>
            <div>
              <label class="block text-xs text-gray-600 mb-2">Tag-Text (max. 15 Zeichen)</label>
              <input
                v-model="form.tagText"
                type="text"
                maxlength="15"
                placeholder="z.B. Nachhaltig"
                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-shop-blue-light transition-all"
              />
            </div>
          </div>
          <div v-if="form.tagIcon && form.tagText" class="mt-3 flex items-center gap-2">
            <span class="text-sm text-gray-600">Vorschau:</span>
            <div class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-bold flex items-center gap-1">
              <Icon :name="form.tagIcon" class="w-3 h-3" />
              {{ form.tagText }}
            </div>
          </div>
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full bg-shop-blue-light text-white py-2 px-4 rounded-lg font-medium hover:bg-shop-blue-dark disabled:bg-gray-500 disabled:cursor-not-allowed transition-all duration-200 shadow-sm hover:shadow-md"
        >
          {{ isLoading ? "Wird hinzugefügt..." : "Produkt hinzufügen" }}
        </button>
      </form>
    </div>
        </div>

        <div class="md:col-span-1 space-y-6">
          <div class="bg-gradient-to-br from-shop-blue-dark to-shop-blue-light text-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center gap-3 mb-4">
              <Icon name="mdi:lightbulb" class="w-8 h-8" />
              <h3 class="font-bold text-lg">Tipps</h3>
            </div>
            <ul class="space-y-3 text-sm opacity-95">
              <li class="flex gap-2">
                <Icon name="mdi:check" class="w-5 h-5 flex-shrink-0" />
                <span>Verwenden Sie aussagekräftige Produktnamen</span>
              </li>
              <li class="flex gap-2">
                <Icon name="mdi:check" class="w-5 h-5 flex-shrink-0" />
                <span>Beschreiben Sie die nachhaltigen Eigenschaften</span>
              </li>
              <li class="flex gap-2">
                <Icon name="mdi:check" class="w-5 h-5 flex-shrink-0" />
                <span>Wählen Sie passende Kategorien aus</span>
              </li>
              <li class="flex gap-2">
                <Icon name="mdi:check" class="w-5 h-5 flex-shrink-0" />
                <span>Überprüfen Sie den Preis sorgfältig</span>
              </li>
            </ul>
          </div>

          <div class="bg-white p-6 rounded-xl shadow-lg border-2 border-gray-200">
            <h3 class="font-bold text-lg mb-4 text-shop-blue-dark">Produktstatistiken</h3>
            <div v-if="totalProducts > 0" class="space-y-4">
              <div v-for="category in availableCategories" :key="category">
                <div class="flex justify-between items-center mb-1">
                  <span class="text-sm ">{{ getCategoryIcon(category) }} {{ category }}</span>
                  <span class="text-sm font-semibold text-shop-blue-dark">{{ getCategoryPercentage(category) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div 
                    class="bg-shop-blue-light h-2 rounded-full transition-all duration-500" 
                    :style="{ width: getCategoryPercentage(category) + '%' }"
                  ></div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-4 text-gray-500 text-sm">
              Noch keine Produkte vorhanden
            </div>
            <p class="text-xs text-gray-500 mt-4">{{ totalProducts }} Produkt{{ totalProducts !== 1 ? 'e' : '' }} im Shop</p>
          </div>

          <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border-2 border-green-200">
            <div class="flex items-center gap-3 mb-3">
              <Icon name="mdi:leaf" class="w-7 h-7 text-green-600" />
              <h3 class="font-bold ">Nachhaltigkeit</h3>
            </div>
            <p class="text-sm ">
              Alle Produkte bei EcoStep werden mit umweltfreundlichen Materialien und klimaneutraler Produktion hergestellt.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
const { token } = useAuth();
const API_URL = "http://localhost:8080/api";

const isLoading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const allProducts = ref<any[]>([]);
const imagePreview = ref<string | null>(null);
const uploadedImageFilename = ref<string | null>(null);
const isDragging = ref(false);
const uploadError = ref("");
const fileInput = ref<HTMLInputElement | null>(null);

const availableCategories = ["Sneaker", "Running", "Training", "Outdoor"];

const form = reactive({
  name: "",
  description: "",
  price: "",
  categories: [] as string[],
  technicalSpecs: "",
  tagIcon: "",
  tagText: "",
});

const totalProducts = computed(() => allProducts.value.length);

const categoryCounts = computed(() => {
  const counts: Record<string, number> = {};
  availableCategories.forEach(cat => counts[cat] = 0);
  
  allProducts.value.forEach(product => {
    if (product.category) {
      const categories = product.category.split(',').map((c: string) => c.trim());
      categories.forEach((cat: string) => {
        if (cat in counts) {
          counts[cat] = (counts[cat] ?? 0) + 1;
        }
      });
    }
  });
  
  return counts;
});

function getCategoryPercentage(category: string): number {
  if (totalProducts.value === 0) return 0;
  const count = categoryCounts.value[category] || 0;
  return Math.round((count / totalProducts.value) * 100);
}

function getCategoryIcon(category: string): string {
  const icons: Record<string, string> = {
    'Sneaker': '👟',
    'Running': '🏃',
    'Training': '💪',
    'Outdoor': '🏔️'
  };
  return icons[category] || '📦';
}

async function fetchAllProducts() {
  try {
    const response = await $fetch<any>(`${API_URL}/products.php`);
    if (response.success) {
      allProducts.value = response.data;
    }
  } catch (err) {
    console.error("Fehler beim Laden der Produkte:", err);
  }
}

onMounted(() => {
  fetchAllProducts();
  
  if (typeof window !== "undefined") {
    const savedToken = localStorage.getItem("token");
    const savedUser = localStorage.getItem("user");

    console.log("Checking auth - Token:", savedToken ? "exists" : "missing");
    console.log(
      "Checking auth - User:",
      savedUser ? JSON.parse(savedUser) : "missing"
    );

    if (savedUser) {
      const parsedUser = JSON.parse(savedUser);
      if (parsedUser.role !== "seller") {
        navigateTo("/");
        return;
      }
    } else {
      navigateTo("/login");
      return;
    }
  }
});

function handleFileSelect(event: Event) {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files[0]) {
    uploadImage(input.files[0]);
  }
}

function handleDrop(event: DragEvent) {
  isDragging.value = false;
  if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
    uploadImage(event.dataTransfer.files[0]);
  }
}

function removeImage() {
  imagePreview.value = null;
  uploadedImageFilename.value = null;
  uploadError.value = "";
  if (fileInput.value) {
    fileInput.value.value = "";
  }
}

async function uploadImage(file: File) {
  uploadError.value = "";
  
  const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
  if (!allowedTypes.includes(file.type)) {
    uploadError.value = "Nur JPG, PNG und WebP Dateien sind erlaubt";
    return;
  }
  
  const maxSize = 5 * 1024 * 1024;
  if (file.size > maxSize) {
    uploadError.value = "Datei ist zu groß. Maximum: 5MB";
    return;
  }
  
  const img = new Image();
  const objectUrl = URL.createObjectURL(file);
  
  img.onload = async () => {
    
    imagePreview.value = objectUrl;
    
    let authToken = token.value;
    if (!authToken && typeof window !== "undefined") {
      authToken = localStorage.getItem("token");
    }

    const formData = new FormData();
    formData.append('image', file);

    try {
      const response = await $fetch<any>(`${API_URL}/upload.php`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${authToken}`,
        },
        body: formData,
      });

      if (response.success) {
        uploadedImageFilename.value = response.data.filename;
      } else {
        uploadError.value = response.message || 'Fehler beim Hochladen';
        imagePreview.value = null;
      }
    } catch (err: any) {
      console.error('Upload error:', err);
      uploadError.value = err?.data?.message || 'Fehler beim Hochladen des Bildes';
      imagePreview.value = null;
    }
  };
  
  img.onerror = () => {
    uploadError.value = "Ungültige Bilddatei";
    URL.revokeObjectURL(objectUrl);
  };
  
  img.src = objectUrl;
}

async function addProduct() {
  errorMessage.value = "";
  successMessage.value = "";

  if (!form.name || !form.price) {
    errorMessage.value = "Name und Preis sind erforderlich";
    window.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }

  if (!uploadedImageFilename.value) {
    errorMessage.value = "Bitte laden Sie ein Produktbild hoch";
    window.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }

  const priceRegex = /^\d+(\.\d{1,2})?$/;
  if (!priceRegex.test(form.price)) {
    errorMessage.value =
      "Preis muss eine gültige Zahl mit maximal 2 Dezimalstellen sein";
    window.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }

  const priceValue = parseFloat(form.price);

  if (priceValue <= 0) {
    errorMessage.value = "Der Preis muss größer als 0 sein";
    window.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }

  if (priceValue > 999999.99) {
    errorMessage.value = "Der Preis ist zu hoch (Maximum: 999.999,99 €)";
    window.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }

  let authToken = token.value;
  if (!authToken && typeof window !== "undefined") {
    authToken = localStorage.getItem("token");
  }

  if (!authToken) {
    errorMessage.value =
      "Sie sind nicht angemeldet. Bitte melden Sie sich erneut an.";
    navigateTo("/login");
    return;
  }

  console.log("Sending request with token:", authToken);

  isLoading.value = true;

  try {
    const response = await $fetch<any>(`${API_URL}/products.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${authToken}`,
      },
      body: {
        name: form.name,
        description: form.description,
        price: priceValue,
        category: form.categories.join(", "),
        image: uploadedImageFilename.value,
        technical_specs: form.technicalSpecs,
        tag_icon: form.tagIcon,
        tag_text: form.tagText,
      },
    });

    if (response.success) {
      form.name = "";
      form.description = "";
      form.price = "";
      form.categories = [];
      form.technicalSpecs = "";
      form.tagIcon = "";
      form.tagText = "";
      imagePreview.value = null;
      uploadedImageFilename.value = null;
      
      setTimeout(() => {
        isLoading.value = false;
        successMessage.value = "Produkt erfolgreich hinzugefügt!";
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        fetchAllProducts();
        
        setTimeout(() => {
          successMessage.value = "";
        }, 3000);
      }, 1000);
    } else {
      errorMessage.value = response.message || "Fehler beim Hinzufügen";
      window.scrollTo({ top: 0, behavior: 'smooth' });
      isLoading.value = false;
    }
  } catch (err: any) {
    console.error("Fehler:", err);
    errorMessage.value = err?.data?.message || "Netzwerk- oder Serverfehler";
    window.scrollTo({ top: 0, behavior: 'smooth' });
    isLoading.value = false;
  }
}

function formatPriceInput(event: Event) {
  const input = event.target as HTMLInputElement;
  let value = input.value;

  value = value.replace(/,/g, ".");

  value = value.replace(/[^\d.]/g, "");

  const parts = value.split(".");
  if (parts.length > 2) {
    value = parts[0] + "." + parts.slice(1).join("");
  }

  if (parts.length === 2 && parts[1] && parts[1].length > 2) {
    value = parts[0] + "." + parts[1].substring(0, 2);
  }

  form.price = value;
}
</script>
