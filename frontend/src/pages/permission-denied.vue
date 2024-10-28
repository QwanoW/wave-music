<script setup lang="ts">
import { ShieldX } from 'lucide-vue-next';

import Header from '@/components/partials/header/index.vue';
import Footer from '@/components/partials/Footer.vue';
</script>

<template>
  <div class="min-h-screen h-full flex flex-col bg-orange-100 relative">
    <Header class="sticky top-0 z-10" />
    <!-- Permission denied -->
    <div class="w-full h-full flex items-center justify-center space-x-2 px-3 absolute">
      <h3 class="text-3xl font-bold text-orange-800">Доступ запрещён</h3>
      <ShieldX stroke="orange" :size="46" />
    </div>
  </div>
  <Footer class="fixed bottom-0" />
</template>
