<script lang="ts">
import { defineBasicLoader } from 'unplugin-vue-router/data-loaders/basic';

import { Album, Track } from '@/constants/types';
import api from '@/lib/axios';

export const useAlbumData = defineBasicLoader(
  '/player/artists/[id]',
  async (route) => {
    const album = (await api.get<Album & { tracks: Track[] }>(`/albums/?id=${route.params.id}`))
      .data;

    if (!album.id) {
      return undefined;
    }

    const tracks = (await api.get<Track[]>(`/tracks/?album_id=${album.id}`)).data;

    album.tracks = tracks.map((t: any) => {
      return {
        ...t,
        order: t.album.order,
      };
    });

    return album;
  },
  {
    lazy: true,
  },
);
</script>

<script setup lang="ts">
import MusicList from '@/components/player/music-list.vue';

const { data: album, isLoading } = useAlbumData();
</script>

<template>
  <div>
    <div v-if="isLoading">Loading...</div>
    <MusicList
      v-else-if="album !== undefined"
      :key="album.id as number"
      :id="album.id as number"
      :title="album.title as string"
      :cover="album.cover_uri as string"
      :artists="[...new Set(album.tracks.flatMap((t) => t.artists.map((a) => a.name)))]"
      :tracks="album.tracks"
      type="album" />
    <div v-else>Artist not found</div>
  </div>
</template>

<style scoped></style>
