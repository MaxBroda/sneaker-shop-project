<template>
  <div>
    <NuxtLayout />
    <ClientOnly>
      <UiSessionExpiredNotification />
    </ClientOnly>
  </div>
</template>
