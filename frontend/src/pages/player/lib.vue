<script setup lang="ts">
import { RouterView } from 'vue-router/auto';

import libNav from '@/components/player/lib-nav.vue';
</script>

<template>
  <div class="w-full h-full flex flex-col gap-y-4 p-5">
    <libNav />
    <RouterView />
  </div>
</template>

<style scoped></style>
