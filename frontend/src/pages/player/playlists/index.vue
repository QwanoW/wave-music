
<script lang="ts">
import { defineBasicLoader } from 'unplugin-vue-router/data-loaders/basic';
import api from '@/lib/axios';
import { Playlist } from '@/constants/types';

export const usePlaylistsData = defineBasicLoader(
  '/player/',
  async () => {
    return (await api.get<Playlist[]>('/playlists/?owner=null')).data;
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

const { data, isLoading } = usePlaylistsData();

const clickMix = (mix_id: number) => {
  api.get(`/tracks/?playlist_id=${mix_id}`).then((response) => {
    audioStore.setPlaylist(response.data);
  });
};
</script>

<template>
  <main class="w-full h-full flex flex-col gap-y-4 p-5">
    <div class="flex justify-between">
      <h1 class="font-medium text-2xl mb-5 text-amber-700">Все плейлисты</h1>
      <RouterLink class="text-amber-700" to="/player/genres">Смотреть все</RouterLink>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-8 gap-4">
      <div v-if="isLoading" v-for="_ in 4" class="w-full h-full flex flex-col gap-y-1">
        <Skeleton class="bg-secondary/30 w-full aspect-square rounded-lg" />
        <div class="w-full flex flex-col justify-center gap-y-1">
          <Skeleton class="bg-secondary/30 w-24 h-6" />
        </div>
      </div>
      <ContentCard
        v-else-if="data"
        v-for="mix in data"
        :title="mix.title"
        :cover="mix.cover_uri"
        :href="`/player/playlists/${mix.id}`"
        :on-click="() => clickMix(mix.id)" />
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
