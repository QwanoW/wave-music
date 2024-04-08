<script setup lang="ts">
import Header from '@/components/partials/header/index.vue';
</script>

<template>
  <div class="min-h-screen h-full flex flex-col bg-orange-100 relative">
    <Header class="sticky top-0 z-10" />
    <!-- NOT FOUND PAGE -->
    <div class="w-full h-full flex flex-col-reverse items-center justify-center px-3 absolute">
      <img class="w-36" src="../assets/404.png" alt="" />
      <h3 class="text-3xl font-bold text-orange-800">Страница не найдена</h3>
    </div>
  </div>
</template>
