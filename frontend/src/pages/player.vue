<script setup lang="ts">
import { RouterView, definePage } from 'vue-router/auto';

import SideBar from '@/components/player/side-bar.vue';
import Player from '@/components/player/pjs/index.vue';

definePage({
  meta: {
    requiresAuth: true,
  },
});
</script>

<template>
  <div class="min-h-screen h-full flex flex-col space-y-1">
    <div class="w-full h-full flex grow space-x-4">
      <div class="w-full max-w-xs">
        <SideBar />
      </div>
      <div class="bg-orange-700/30 w-full rounded-sm p-5">
        <RouterView />
      </div>
    </div>
    <div class="w-full bg-amber-300">
      <Player />
    </div>
  </div>
</template>

<style scoped></style>
