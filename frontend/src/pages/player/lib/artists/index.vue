<script lang="ts">
import { defineBasicLoader } from 'unplugin-vue-router/data-loaders/basic';

import { Artist, Track } from '@/constants/types';
import api from '@/lib/axios';
import { userStore } from '@/stores/user';

export const useArtistData = defineBasicLoader(
  '/player/lib/artists/',
  async () => {
    if (!userStore.user) {
      return [];
    }
    const likedArtistsPromise = userStore.likedArtists.map(async (id) => {
      const artist = (await api.get<Artist & { tracks: Track[] }>(`/artists/?id=${id}`)).data;
      const tracks = (await api.get<Track[]>(`/tracks/?artist_id=${artist.id}`)).data;
      artist.tracks = tracks;
      return artist;
    });

    return (await Promise.all(likedArtistsPromise)).filter((a) => !!a);
  },
  {
    lazy: true,
  },
);
</script>

<script setup lang="ts">
import { audioStore } from '@/stores/audio';
import { Skeleton } from '@/components/ui/skeleton';
import ContentCard from '@/components/player/content-card.vue';

const { data: likedArtists, isLoading } = useArtistData();
</script>

<template>
  <div class="w-full flex flex-col gap-y-6">
    <h1 class="font-bold text-3xl">Ваши любимые исполнители</h1>
    <div v-if="isLoading" class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-8 gap-4">
      <div v-for="_ in 4" class="w-full h-full flex flex-col gap-y-1">
        <Skeleton class="bg-primary/30 w-full aspect-square rounded-lg" />
        <div class="w-full flex flex-col justify-center gap-y-1">
          <Skeleton class="bg-primary/30 w-24 h-6" />
        </div>
      </div>
    </div>
    <div
      v-else-if="likedArtists && likedArtists.length"
      class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-8 gap-4">
      <ContentCard
        v-for="artist in likedArtists"
        :href="`/player/artists/${artist.id}`"
        :title="artist.name"
        description="Исполнитель"
        :cover="artist.photo_uri"
        :on-click="() => audioStore.setPlaylist(artist.tracks)"
        rounded />
    </div>
    <p v-else class="text-lg text-muted-foreground">
      Добавляйте любимых исполнителей в медиатеку и они будут отображаться здесь.
    </p>
  </div>
</template>
