<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';

defineProps<{
  href: string;
  label: string;
}>();
</script>

<template>
  <RouterLink class="w-[80%]" :to="href">
    <Button class="w-full" variant="outline">
      {{ label }}
    </Button>
  </RouterLink>
</template>

<style scoped></style>
