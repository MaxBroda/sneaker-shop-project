<template>
  <div class="min-h-screen bg-shop-bright flex flex-col">
    <Navbar />

    <main class="flex-1">
      <NuxtPage />
    </main>

    <Footer />
  </div>
</template>

<script setup>
import Navbar from "~/components/navbar/Navbar.vue";
import Footer from "~/components/Footer.vue";

const route = useRoute();

onMounted(() => {
  observeElements();
});

watch(() => route.path, () => {
  nextTick(() => {
    observeElements();
  });
});

function observeElements() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('show');
      }
    });
  }, { threshold: 0.1 });
  
  document.querySelectorAll('.scroll-fade').forEach(el => {
    el.classList.remove('show');
    observer.observe(el);
  });
}
</script>
