<script setup lang="ts">
defineProps<{
  title: string;
  isLoading?: boolean;
  isEmpty?: boolean;
  widthLimit?: boolean;
}>();

import Loader from '@/components/loader.vue';
</script>

<template>
  <div :class="widthLimit ? 'max-w-6xl w-full mx-auto' : 'w-full'" class="flex items-center">
    <h1 class="text-lg font-semibold md:text-2xl">{{ title }}</h1>
  </div>
  <div
    :class="widthLimit ? 'max-w-6xl w-full mx-auto' : ''"
    class="flex-1 flex-col gap-y-4 rounded-lg border border-dashed shadow-sm p-4">
    <div v-if="isLoading || isEmpty" class="w-full h-full flex justify-center items-center">
      <Loader v-if="isLoading" />
      <p v-else class="my-5 h-24 text-center">Ничего не найдено.</p>
    </div>
    <slot v-else />
  </div>
</template>
