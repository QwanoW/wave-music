<script setup lang="ts">
import Header from '@/components/partials/header/index.vue';
import Footer from '@/components/partials/Footer.vue';
</script>

<template>
  <div class="min-h-screen h-full flex flex-col">
    <Header class="sticky top-0 z-10" />
    <div class="w-full bg-orange-100 flex flex-col px-3 flex-1">
      <RouterView v-slot="{ Component }">
        <Transition name="fade" mode="out-in">
          <component :is="Component" />
        </Transition>
      </RouterView>
    </div>
    <Footer />
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition-property: opacity;
  transition-duration: 0.25s;
}

.fade-enter-active {
  transition-delay: 0.25s;
}

.fade-enter,
.fade-leave-active {
  opacity: 0;
}
</style>
