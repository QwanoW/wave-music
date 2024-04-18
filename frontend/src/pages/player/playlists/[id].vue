<script lang="ts">
import { defineBasicLoader } from 'unplugin-vue-router/data-loaders/basic';

import playlistService from '@/services/playlist.service';

export const usePlaylistData = defineBasicLoader(
  '/player/playlists/[id]',
  async (route) => {
    return playlistService.getById(Number(route.params.id));
  },
  {
    lazy: true,
  },
);
</script>

<script setup lang="ts">
import { userStore } from '@/stores/user';
import MusicList from '@/components/player/music-list.vue';

const { data: playlist, isLoading } = usePlaylistData();
</script>

<template>
  <div>
    <div v-if="isLoading">Loading...</div>
    <div v-else-if="playlist?.is_private && playlist.owner.id !== userStore.user?.id">
      Этот плейлист приватный и доступен только владельцу
    </div>
    <MusicList
      v-else-if="playlist"
      :key="playlist.id"
      :id="playlist.id"
      :title="playlist.title"
      :description="playlist.description"
      :cover="playlist.cover_uri"
      :owner="playlist.owner"
      :is-private="!!playlist.is_private"
      :tracks="playlist.tracks"
      type="playlist" />
    <div v-else>Playlist not found</div>
  </div>
</template>

<style scoped></style>
