<script lang="ts">
import api from '@/lib/axios';
import { defineBasicLoader } from 'unplugin-vue-router/data-loaders/basic';
import { userStore } from '@/stores/user';
import { Track } from '@/constants/types';

export const usePlaylistData = defineBasicLoader(
  '/player/playlists/',
  async () => {
    if (!userStore.likedTracks.length) {
      return [];
    }

    return (
      await api.get<Track[]>(`/tracks/?${userStore.likedTracks.map((t) => `id[]=${t}`).join('&')}`)
    ).data;
  },
  {
    lazy: true,
  },
);
</script>

<script setup lang="ts">
import MusicList from '@/components/player/music-list.vue';

const { data: likedTracks, isLoading } = usePlaylistData();
</script>

<template>
  <main>
    <div v-if="isLoading">Loading...</div>
    <MusicList
      v-else-if="likedTracks"
      :id="0"
      title="Любимые треки"
      cover="/content/images/default/liked.svg"
      :owner="userStore.user"
      :tracks="likedTracks"
      type="likedTracks" />
    <div v-else>Playlist not found</div>
  </main>
</template>

<style scoped></style>
