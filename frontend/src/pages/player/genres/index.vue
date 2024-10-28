<script lang="ts">
import { defineBasicLoader } from 'unplugin-vue-router/data-loaders/basic';
import api from '@/lib/axios';
import { Genre } from '@/constants/types';

export const useGenresData = defineBasicLoader(
  '/player/',
  async () => {
    return (await api.get<Genre[]>('/genres/')).data;
  },
  {
    lazy: true,
  },
);
</script>

<script setup lang="ts">
import { audioStore } from '@/stores/audio';
import ContentCard from '@/components/player/content-card.vue';
import { Skeleton } from '@/components/ui/skeleton';

const { data, isLoading } = useGenresData();

const clickGenre = (genre_id: number) => {
  api.get(`/tracks/?genre_id=${genre_id}`).then((response) => {
    audioStore.setPlaylist(response.data);
  });
};
</script>

<template>
  <main class="w-full h-full flex flex-col gap-y-4 p-5">
    <div class="flex justify-between">
      <h1 class="font-medium text-2xl mb-5 text-amber-700">Жанры</h1>
      <RouterLink class="text-amber-700" to="/player/genres">Показать все</RouterLink>
    </div>
    <div class="grid grid-cols-4 md:grid-cols-6 gap-4">
      <div v-if="isLoading" v-for="_ in 6" class="w-full h-full flex flex-col gap-y-1">
        <Skeleton class="bg-secondary/30 w-full aspect-square rounded-lg" />
        <div class="w-full flex flex-col justify-center gap-y-1">
          <Skeleton class="bg-secondary/30 w-24 h-6" />
        </div>
      </div>
      <ContentCard
        v-else-if="data"
        v-for="genre in data"
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
