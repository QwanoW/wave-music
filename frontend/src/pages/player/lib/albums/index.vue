<script lang="ts">
import { defineBasicLoader } from 'unplugin-vue-router/data-loaders/basic';

import { Album, Track } from '@/constants/types';
import api from '@/lib/axios';
import { userStore } from '@/stores/user';

export const useAlbumData = defineBasicLoader(
  '/player/lib/albums/',
  async () => {
    if (!userStore.user) {
      return [];
    }
    const likedAlbumsPromise = userStore.likedAlbums.map(async (id) => {
      const album = (await api.get<Album & { tracks: Track[] }>(`/albums/?id=${id}`)).data;
      const tracks = (await api.get<Track[]>(`/tracks/?album_id=${album.id}`)).data;
      album.tracks = tracks.map((t: any) => {
        return {
          ...t,
          order: t.album.order,
        };
      });
      return album;
    });

    return (await Promise.all(likedAlbumsPromise)).filter((a) => !!a);
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

const { data: likedAlbums, isLoading } = useAlbumData();
</script>

<template>
  <div class="w-full flex flex-col gap-y-6">
    <h1 class="font-bold text-3xl">Ваши любимые альбомы</h1>
    <div v-if="isLoading" class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-8 gap-4">
      <div v-for="_ in 4" class="w-full h-full flex flex-col gap-y-1">
        <Skeleton class="bg-primary/30 w-full aspect-square rounded-lg" />
        <div class="w-full flex flex-col justify-center gap-y-1">
          <Skeleton class="bg-primary/30 w-24 h-6" />
        </div>
      </div>
    </div>
    <div
      v-else-if="likedAlbums && likedAlbums.length"
      class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-8 gap-4">
      <ContentCard
        v-for="album in likedAlbums"
        :href="`/player/albums/${album.id}`"
        :title="album.title as string"
        :cover="album.cover_uri as string"
        :number="album.tracks.length"
        :on-click="() => audioStore.setPlaylist(album.tracks)" />
    </div>
    <p v-else class="text-lg text-muted-foreground">
      Добавляйте любимые плейлисты в медиатеку и они будут отображаться здесь.
    </p>
  </div>
</template>
