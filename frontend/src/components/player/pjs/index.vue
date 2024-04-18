<script setup lang="ts">
import pjs from '@/components/player/pjs/loader';
import { audioStore } from '@/stores/audio';

import { onMounted, ref } from 'vue';

onMounted(async () => {
  await pjs;
  audioStore.init();
});

const player = ref<HTMLElement | null>(null);

onMounted(() => {
  player.value?.addEventListener('new', () => {
    audioStore.onChange();
  })

  player.value?.addEventListener('play', () => {
    audioStore.isPlaying = true;
  })

  player.value?.addEventListener('pause', () => {
    audioStore.isPlaying = false;
  })
});
</script>

<template>
  <div ref="player" id="player"></div>
</template>

<style>
#player {
  width: 100%;
}
</style>
