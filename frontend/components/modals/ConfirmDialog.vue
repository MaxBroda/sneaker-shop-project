<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-200 ease-out"
      leave-active-class="transition-opacity duration-200 ease-in"
      enter-from-class="opacity-0"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4"
        @click.self="cancel"
      >
        <div class="absolute inset-0 bg-black bg-opacity-70" @click="cancel"></div>

        <Transition
          enter-active-class="transition-all duration-200 ease-out"
          leave-active-class="transition-all duration-200 ease-in"
          enter-from-class="opacity-0 scale-95"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-if="isOpen"
            class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6 transform"
          >
            <div
              class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4"
            >
              <span class="text-2xl">🗑️</span>
            </div>

            <h3 class="text-lg font-semibold text-center mb-2">
              {{ title }}
            </h3>

            <p class="text-sm text-center mb-6">
              {{ message }}
            </p>

            <div class="flex gap-3">
              <button
                @click="cancel"
                class="flex-1 px-4 py-2 border border-gray-500 rounded-lg text-gray-500 font-medium hover:bg-gray-50 transition-colors"
              >
                Abbrechen
              </button>
              <button
                @click="confirm"
                class="flex-1 px-4 py-2 rounded-lg text-white font-medium bg-signal-red bg-signal-red-hover transition-colors"
              >
                Löschen
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
interface Props {
  isOpen: boolean;
  title: string;
  message: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  confirm: [];
  cancel: [];
}>();

function confirm() {
  emit("confirm");
}

function cancel() {
  emit("cancel");
}

function handleEscape(event: KeyboardEvent) {
  if (event.key === "Escape" && props.isOpen) {
    cancel();
  }
}

onMounted(() => {
  document.addEventListener("keydown", handleEscape);
});

onBeforeUnmount(() => {
  document.removeEventListener("keydown", handleEscape);
});
</script>
