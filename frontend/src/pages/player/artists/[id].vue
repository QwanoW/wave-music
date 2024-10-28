<script lang="ts">
import { defineBasicLoader } from 'unplugin-vue-router/data-loaders/basic';

import { Artist, Track } from '@/constants/types';
import api from '@/lib/axios';

export const usePlaylistData = defineBasicLoader(
  '/player/artists/[id]',
  async (route) => {
    const artist = (await api.get<Artist & { tracks: Track[] }>(`/artists/?id=${route.params.id}`))
      .data;

    if (!artist.id) {
      return undefined;
    }

    const tracks = (await api.get<Track[]>(`/tracks/?artist_id=${artist.id}`)).data;

    artist.tracks = tracks;

    return artist;
  },
  {
    lazy: true,
  },
);
</script>

<script setup lang="ts">
import MusicList from '@/components/player/music-list.vue';

const { data: artist, isLoading } = usePlaylistData();
</script>

<template>
  <div>
    <div v-if="isLoading">Loading...</div>
    <MusicList
      v-else-if="artist !== undefined"
      :key="artist.id"
      :id="artist.id"
      :title="artist.name"
      :cover="artist.photo_uri"
      :description="artist.bio"
      :tracks="artist.tracks"
      type="artist" />
    <div v-else>Artist not found</div>
  </div>
</template>

<style scoped></style>
