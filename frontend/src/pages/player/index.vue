<script lang="ts">
import { defineBasicLoader } from 'unplugin-vue-router/data-loaders/basic';

import api from '@/lib/axios';
import { Genre, Playlist, Artist } from '@/constants/types';

export const useMainPageData = defineBasicLoader(
  '/player/',
  async () => {
    const genres = (await api.get<Genre[]>('/genres/?limit=6')).data;
    const mixes = (await api.get<Playlist[]>('/playlists/?owner=null&limit=4')).data;
    const artists = (await api.get<Artist[]>('/artists/?limit=6')).data;
    return {
      genres,
      mixes,
      artists,
    };
  },
  {
    lazy: true,
  },
);
</script>

<script setup lang="ts">
import { API_URL } from '@/constants/';
import { audioStore } from '@/stores/audio';
import ContentCard from '@/components/player/content-card.vue';
import { Skeleton } from '@/components/ui/skeleton';

const { data, isLoading } = useMainPageData();

const clickGenre = (genre_id: number) => {
  api.get(`/tracks/?genre_id=${genre_id}`).then((response) => {
    audioStore.setPlaylist(response.data);
  });
};

const clickMix = (mix_id: number) => {
  api.get(`/playlists/?id=${mix_id}`).then((response) => {
    audioStore.setPlaylist(response.data[0].tracks);
  });
};

const clickArtist = (artist_id: number) => {
  api.get(`/tracks/?artist_id=${artist_id}`).then((response) => {
    audioStore.setPlaylist(response.data);
  });
};
</script>

<template>
  <main>
    <img
      class="w-full h-64 object-cover shadow-2xl"
      :src="API_URL + '/content/images/default/banner.png'"
      alt="banner" />
    <div class="mt-5 w-full h-full flex flex-col gap-y-4 p-5">
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
          v-else-if="data && data.genres"
          v-for="genre in data.genres"
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
      <div class="flex justify-between mt-10">
        <h1 class="font-medium text-2xl mb-5 text-amber-700">Твои лучшие миксы</h1>
        <RouterLink class="text-amber-700" to="/player/playlists">Показать все</RouterLink>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-if="isLoading" v-for="_ in 4" class="w-full h-full flex flex-col gap-y-1">
          <Skeleton class="bg-secondary/30 w-full aspect-square rounded-lg" />
          <div class="w-full flex flex-col justify-center gap-y-1">
            <Skeleton class="bg-secondary/30 w-24 h-6" />
          </div>
        </div>
        <ContentCard
          v-else-if="data && data.mixes"
          v-for="mix in data.mixes"
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
      <div class="flex justify-between mt-10">
        <h1 class="font-medium text-2xl mb-5 text-amber-700">Лучшие исполнители</h1>
        <RouterLink class="text-amber-700" to="/player/artists">Показать все</RouterLink>
      </div>
      <div class="grid grid-cols-4 md:grid-cols-6 gap-4">
        <div v-if="isLoading" v-for="_ in 6" class="w-full h-full flex flex-col gap-y-1">
          <Skeleton class="bg-secondary/30 w-full aspect-square rounded-full" />
          <div class="w-full flex flex-col justify-center gap-y-1">
            <Skeleton class="bg-secondary/30 w-24 h-6" />
          </div>
        </div>
        <ContentCard
          v-else-if="data && data.artists"
          v-for="artist in data.artists"
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
    </div>
  </main>
</template>

<style scoped></style>
