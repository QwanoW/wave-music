<script setup lang="ts">
import { AlertTriangle } from 'lucide-vue-next';

defineProps<{
  message?: string;
}>();
</script>

<template>
  <div
    v-if="message"
    class="bg-destructive/15 px-6 py-4 rounded-md flex items-center gap-x-2 text-sm text-destructive">
    <AlertTriangle class="w-5 h-5" />
    <p>{{ message }}</p>
  </div>
</template>

<style scoped></style>
