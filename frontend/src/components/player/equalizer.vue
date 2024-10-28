<script setup lang="ts">
import { cn } from '@/lib/utils';

const props = defineProps<{
  class?: string;
}>();
</script>

<template>
  <div :class="cn('flex space-x-[2px] h-6 w-6', props.class)">
    <div class="box h-full w-2 bg-orange-500 rounded-lg animate-quiet"></div>
    <div class="box h-full w-2 bg-orange-500 rounded-lg animate-norm"></div>
    <div class="box h-full w-2 bg-orange-500 rounded-lg animate-quiet"></div>
    <div class="box h-full w-2 bg-orange-500 rounded-lg animate-loud"></div>
    <div class="box h-full w-2 bg-orange-500 rounded-lg animate-quiet"></div>
  </div>
</template>
