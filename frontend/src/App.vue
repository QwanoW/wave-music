<script setup lang="ts">
import { RouterView } from 'vue-router/auto';

import Toaster from '@/components/ui/toast/Toaster.vue';
</script>

<template>
  <RouterView />
  <Toaster :duration="3000" />
</template>

<style scoped></style>
