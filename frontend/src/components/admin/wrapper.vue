<script setup lang="ts">
defineProps<{
  title: string;
  widthLimit?: boolean;
}>();
</script>

<template>
  <div :class="widthLimit ? 'max-w-6xl w-full mx-auto' : ''" class="flex items-center">
    <h1 class="text-lg font-semibold md:text-2xl">{{ title }}</h1>
  </div>
  <div
    :class="widthLimit ? 'max-w-6xl w-full mx-auto' : ''"
    class="flex-1 flex-col gap-y-4 rounded-lg border border-dashed shadow-sm p-4">
    <slot />
  </div>
</template>
