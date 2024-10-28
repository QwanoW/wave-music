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
  <main>
    <div v-if="isLoading">Loading...</div>
    <div class="w-full flex justify-center items-center mt-10" v-else-if="playlist?.is_private && playlist.owner.id !== userStore.user?.id">
      <h1 class="text-amber-700 text-3xl text-center">Это приватный плейлист, <br> он доступен только владельцу</h1>
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
      <div class="w-full flex justify-center items-center mt-10" v-else>
      <h1 class="text-amber-700 text-3xl text-center">Плейлист не найден</h1>
    </div>
  </main>
</template>

<style scoped></style>
