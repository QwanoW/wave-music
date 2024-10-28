<script lang="ts">
import { defineBasicLoader } from 'unplugin-vue-router/data-loaders/basic';
import api from '@/lib/axios';
import { Artist } from '@/constants/types';

export const useGenresData = defineBasicLoader(
  '/player/',
  async () => {
    return (await api.get<Artist[]>('/artists/')).data;
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

const clickArtist = (artist_id: number) => {
  api.get(`/tracks/?artist_id=${artist_id}`).then((response) => {
    audioStore.setPlaylist(response.data);
  });
};
</script>

<template>
  <main class="w-full h-full flex flex-col gap-y-4 p-5">
    <div class="flex justify-between mt-10">
      <h1 class="font-medium text-2xl mb-5 text-amber-700">Лучшие исполнители</h1>
      <RouterLink class="text-amber-700" to="/player/genres">Показать все</RouterLink>
    </div>
    <div class="grid grid-cols-4 md:grid-cols-6 gap-4">
      <div v-if="isLoading" v-for="_ in 6" class="w-full h-full flex flex-col gap-y-1">
        <Skeleton class="bg-secondary/30 w-full aspect-square rounded-full" />
        <div class="w-full flex flex-col justify-center gap-y-1">
          <Skeleton class="bg-secondary/30 w-24 h-6" />
        </div>
      </div>
      <ContentCard
        v-else-if="data && data"
        v-for="artist in data"
        rounded
        :title="artist.name"
        :cover="artist.photo_uri"
        :href="`/player/artists/${artist.id}`"
        :on-click="() => clickArtist(artist.id)"
        description="Исполнитель" />
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
