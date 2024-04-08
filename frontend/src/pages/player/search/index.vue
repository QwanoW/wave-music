<script setup lang="ts">
import { definePage } from 'vue-router/auto';

definePage({
  meta: {
    requiresAuth: true,
  },
});
</script>

<template>
  <h1>Поиск</h1>
</template>

<style scoped></style>
