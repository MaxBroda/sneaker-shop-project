<template>
  <div
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div
      class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
      @click.stop
    >
      <div class="sticky top-0 bg-shop-blue-dark text-white shadow-xl p-6 rounded-t-2xl flex justify-between items-center z-10">
        <h2 class="text-2xl font-bold">Produkt bearbeiten</h2>
        <button
          @click="$emit('close')"
          class="hover:bg-white/20 rounded-full p-1.5 transition-colors"
        >
          <Icon name="mdi:close" class="w-5 h-5 text-white flex items-center" />
        </button>
      </div>

      <div class="p-6">
        <div
          v-if="errorMessage"
          class="bg-red-50 border-l-4 border-signal-red text-signal-red px-4 py-3 rounded mb-6 flex items-start gap-2"
        >
          <Icon name="mdi:alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5" />
          <span>{{ errorMessage }}</span>
        </div>

        <div
          v-if="successMessage"
          class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-6 flex items-start gap-2"
        >
          <Icon name="mdi:check-circle" class="w-5 h-5 flex-shrink-0 mt-0.5" />
          <span>{{ successMessage }}</span>
        </div>

        <form @submit.prevent="updateProduct" class="space-y-6">
          <div>
            <label class="block text-sm font-semibold mb-2 text-shop-blue-dark">
              Produktname *
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
              placeholder="z.B. EcoStep Runner Pro"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-2 text-shop-blue-dark">
              Beschreibung
            </label>
            <textarea
              v-model="form.description"
              rows="4"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all resize-none"
              placeholder="Beschreiben Sie die Eigenschaften und Vorteile des Produkts..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-semibold mb-2 text-shop-blue-dark">
              Technische Daten
            </label>
            <textarea
              v-model="form.technicalSpecs"
              rows="4"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all resize-none"
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
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-shop-blue-light focus:border-transparent transition-all"
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
                isDragging ? 'border-shop-blue-light bg-shop-blue-light/10' : 'border-gray-300 hover:border-shop-blue-light'
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
                <p class="text-sm text-gray-600">Neues Bild hochladen (optional)</p>
              </div>
              
              <div v-else class="space-y-2">
                <Icon name="mdi:cloud-upload" class="w-12 h-12 mx-auto text-gray-500" />
                <div>
                  <p class="font-medium">Bild hochladen</p>
                  <p class="text-sm text-gray-500 mt-1">
                    Klicken oder Bild hierher ziehen
                  </p>
                  <p class="text-xs text-gray-500 mt-2">
                    JPG, PNG oder WebP • Max. 5MB
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
                  class="w-5 h-5 accent-shop-blue-light border-gray-300 rounded focus:ring-shop-blue-light cursor-pointer"
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
              <span class="text-xs text-gray-600">Vorschau:</span>
              <div class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-bold flex items-center gap-1">
                <Icon :name="form.tagIcon" class="w-3 h-3" />
                {{ form.tagText }}
              </div>
            </div>
          </div>

          <div class="flex gap-3 pt-4 border-t">
            <button
              type="button"
              @click="$emit('close')"
              class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-bold hover:bg-gray-50 transition-all"
            >
              Abbrechen
            </button>
            <button
              type="submit"
              :disabled="isLoading"
              class="flex-1 px-6 py-3 bg-shop-blue-light hover:bg-shop-blue-dark rounded-xl font-bold shadow-md hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <Icon v-if="isLoading" name="mdi:loading" class="w-5 h-5 animate-spin" />
              <span class="text-white">{{ isLoading ? 'Wird gespeichert...' : 'Speichern' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const config = useRuntimeConfig();
const API_URL = config.public.apiUrl;
const { token } = useAuth();

interface Product {
  id: number;
  name: string;
  description: string;
  price: number;
  category: string;
  image: string;
  seller_id: number;
  technical_specs?: string;
  tag_icon?: string;
  tag_text?: string;
}

const props = defineProps<{
  product: Product | null;
}>();

const emit = defineEmits<{
  close: [];
  updated: [];
}>();

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

const imagePreview = ref<string | null>(null);
const uploadedImageFilename = ref<string | null>(null);
const isDragging = ref(false);
const uploadError = ref("");
const fileInput = ref<HTMLInputElement | null>(null);
const errorMessage = ref("");
const successMessage = ref("");
const isLoading = ref(false);

watch(() => props.product, (product) => {
  if (product) {
    form.name = product.name;
    form.description = product.description || "";
    form.price = product.price.toString();
    form.categories = product.category ? product.category.split(", ").map(c => c.trim()) : [];
    form.technicalSpecs = product.technical_specs || "";
    form.tagIcon = product.tag_icon || "";
    form.tagText = product.tag_text || "";
    
    if (product.image) {
      imagePreview.value = `${config.public.uploadsUrl}/${product.image}`;
      uploadedImageFilename.value = product.image;
    }
  }
}, { immediate: true });

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

async function updateProduct() {
  errorMessage.value = "";
  successMessage.value = "";

  if (!form.name || !form.price) {
    errorMessage.value = "Name und Preis sind erforderlich";
    document.querySelector('.max-h-\\[90vh\\]')?.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }

  if (!uploadedImageFilename.value) {
    errorMessage.value = "Bitte laden Sie ein Produktbild hoch";
    document.querySelector('.max-h-\\[90vh\\]')?.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }

  const priceRegex = /^\d+(\.\d{1,2})?$/;
  if (!priceRegex.test(form.price)) {
    errorMessage.value = "Preis muss eine gültige Zahl mit maximal 2 Dezimalstellen sein";
    document.querySelector('.max-h-\\[90vh\\]')?.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }

  const priceValue = parseFloat(form.price);

  if (priceValue <= 0) {
    errorMessage.value = "Der Preis muss größer als 0 sein";
    document.querySelector('.max-h-\\[90vh\\]')?.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }

  if (priceValue > 999999.99) {
    errorMessage.value = "Der Preis ist zu hoch (Maximum: 999.999,99 €)";
    document.querySelector('.max-h-\\[90vh\\]')?.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }

  let authToken = token.value;
  if (!authToken && typeof window !== "undefined") {
    authToken = localStorage.getItem("token");
  }

  if (!authToken) {
    errorMessage.value = "Sie sind nicht angemeldet. Bitte melden Sie sich erneut an.";
    return;
  }

  isLoading.value = true;

  try {
    const response = await $fetch<any>(`${API_URL}/products.php`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${authToken}`,
      },
      body: {
        id: props.product?.id,
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
      successMessage.value = "Produkt erfolgreich aktualisiert!";
      document.querySelector('.max-h-\\[90vh\\]')?.scrollTo({ top: 0, behavior: 'smooth' });
      setTimeout(() => {
        emit('updated');
      }, 1000);
    } else {
      errorMessage.value = response.message || "Fehler beim Aktualisieren";
      document.querySelector('.max-h-\\[90vh\\]')?.scrollTo({ top: 0, behavior: 'smooth' });
    }
  } catch (err: any) {
    console.error("Fehler:", err);
    errorMessage.value = err?.data?.message || "Netzwerk- oder Serverfehler";
    document.querySelector('.max-h-\\[90vh\\]')?.scrollTo({ top: 0, behavior: 'smooth' });
  } finally {
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
