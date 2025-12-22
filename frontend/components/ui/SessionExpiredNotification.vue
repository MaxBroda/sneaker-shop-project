<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0 translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-2"
    >
      <div
        v-if="showNotification"
        class="fixed top-4 right-4 z-[9999] max-w-md"
      >
        <div class="bg-orange-50 border border-orange-200 rounded-lg shadow-lg p-4">
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
              <svg
                class="w-6 h-6 text-orange-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-sm font-semibold text-orange-800">
                Sitzung abgelaufen
              </h3>
              <p class="mt-1 text-sm text-orange-700">
                {{ message }}
              </p>
            </div>
            <button
              @click="close"
              class="flex-shrink-0 text-orange-600 hover:text-orange-800"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
const showNotification = ref(false)
const message = ref('')

// Listen for session expired events
onMounted(() => {
  if (import.meta.client) {
    window.addEventListener('session-expired', handleSessionExpired)
  }
})

onUnmounted(() => {
  if (import.meta.client) {
    window.removeEventListener('session-expired', handleSessionExpired)
  }
})

function handleSessionExpired(event: Event) {
  const customEvent = event as CustomEvent<{ message: string }>
  message.value = customEvent.detail.message
  showNotification.value = true
  
  // Auto-hide after 8 seconds
  setTimeout(() => {
    showNotification.value = false
  }, 8000)
}

function close() {
  showNotification.value = false
}
</script>

