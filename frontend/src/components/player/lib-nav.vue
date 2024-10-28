<script setup lang="ts">
import { RouterLink } from 'vue-router/auto';

const libRoutes = [
  { name: 'Плейлисты', path: '/player/lib/playlists' },
  { name: 'Исполнители', path: '/player/lib/artists' },
  { name: 'Альбомы', path: '/player/lib/albums' },
];
</script>

<template>
  <nav class="w-full space-x-2">
    <RouterLink
      :class="route.path === $route.path ? 'bg-amber-600/30' : 'hover:bg-amber-600/15'"
      class="px-3 py-1 rounded-lg font-bold text-sm sm:text-xl text-orange-600 cursor-pointer transition-all"
      v-for="route in libRoutes"
      :to="route.path"
      >{{ route.name }}</RouterLink
    >
  </nav>
</template>

<style scoped></style>
