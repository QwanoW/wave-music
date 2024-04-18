<script setup lang="ts">
import { onMounted, ref, provide } from 'vue';

import { Playlist, Track } from '@/constants/types';
import { editPlaylistSchemaType } from '@/schemas/index';
import api from '@/lib/axios';
import Card from '@/components/admin/playlist/card.vue';
import Wrapper from '@/components/admin/wrapper.vue';
import { getURL } from '@/lib/utils';

const playlists = ref<Playlist[]>([]);
const tracks = ref<Track[]>([]);

provide('onUpdate', (updatedPlaylist: editPlaylistSchemaType) => {
  playlists.value = playlists.value.map((playlist) => {
    if (playlist.id === updatedPlaylist.id) {
      const { title, description } = updatedPlaylist;
      return {
        ...playlist,
        title,
        description,
        tracks: tracks.value.filter((t) => updatedPlaylist.tracks.includes(t.id)),
        temp_cover: updatedPlaylist.cover && getURL(updatedPlaylist.cover),
      };
    } else {
      return playlist;
    }
  });
});

provide('onDelete', (id: number) => {
  playlists.value = playlists.value.filter((playlist) => playlist.id !== id);
});

onMounted(async () => {
  playlists.value = (await api.get('/playlists/')).data;
  tracks.value = (await api.get('/tracks/')).data;
});
</script>

<template>
  <Wrapper title="Треки">
    <div v-if="playlists" class="grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
      <Card v-for="playlist in playlists" v-bind="playlist" />
    </div>
  </Wrapper>
</template>
