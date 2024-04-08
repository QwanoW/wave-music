<script setup lang="ts">
import GlobeIcon from '@radix-icons/vue/GlobeIcon';
import { LoaderCircle } from 'lucide-vue-next';

import Button from '@/components/ui/button/Button.vue';

defineProps<{
  isLoading?: boolean;
}>();
</script>

<template>
  <Button variant="outline" type="button" :disabled="isLoading">
    <LoaderCircle v-if="isLoading" class="mr-2 h-4 w-4 animate-spin" />
    <GlobeIcon v-else class="mr-2 h-4 w-4" />
    Google
  </Button>
</template>

<style scoped></style>
