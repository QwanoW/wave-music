<script setup lang="ts">
import { onMounted, ref } from 'vue';

import { audioStore } from '@/stores/audio';
import ContentCard from '@/components/player/content-card.vue';
import api from '@/lib/axios';
import { Genre } from '@/constants/types';
import { Skeleton } from '@/components/ui/skeleton';

const genres = ref<Genre[]>();

onMounted(async () => {
  genres.value = (await api.get('/genres/')).data;
});

const clickGenre = (genre_id: number) => {
  api.get(`/tracks/?genre_id=${genre_id}`).then((response) => {
    audioStore.setPlaylist(response.data);
  });
};
</script>

<template>
  <main class="flex flex-col gap-y-2">
    <div class="flex justify-between">
      <h1 class="font-medium text-2xl mb-5 text-amber-700">Жанры</h1>
      <RouterLink class="text-amber-700" to="/player/genres">Все жанры</RouterLink>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-8 gap-4">
      <ContentCard
        v-if="genres"
        v-for="genre in genres"
        :title="genre.name"
        :cover="genre.cover_uri"
        :on-click="() => clickGenre(genre.id)" />
      <div v-else v-for="_ in 6" class="w-full h-full flex flex-col gap-y-1">
        <Skeleton class="bg-secondary/30 w-full aspect-square rounded-lg" />
        <div class="w-full flex flex-col justify-center gap-y-1">
          <Skeleton class="bg-secondary/30 w-24 h-6" />
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped></style>
