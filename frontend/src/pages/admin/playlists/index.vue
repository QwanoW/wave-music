<script setup lang="ts">
import { onMounted, ref, provide, computed } from 'vue';

import { Playlist, Track, User } from '@/constants/types';
import { editPlaylistSchemaType } from '@/schemas/index';
import api from '@/lib/axios';
import Card from '@/components/admin/playlist/card.vue';
import Wrapper from '@/components/admin/wrapper.vue';
import { getURL } from '@/lib/utils';
import {
  Pagination,
  PaginationEllipsis,
  PaginationFirst,
  PaginationLast,
  PaginationList,
  PaginationListItem,
  PaginationNext,
  PaginationPrev,
} from '@/components/ui/pagination';
import { Button } from '@/components/ui/button';

const playlists = ref<Playlist[]>([]);
const tracks = ref<Track[]>([]);
const users = ref<User[]>([]);

provide('form-data', { tracks, users });

provide('onUpdate', (updatedPlaylist: editPlaylistSchemaType) => {
  playlists.value = playlists.value.map((playlist) => {
    if (playlist.id === updatedPlaylist.id) {
      const { title, description, is_private, user_id } = updatedPlaylist;
      return {
        ...playlist,
        title,
        description,
        is_private,
        user_id: Number(user_id),
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

const isLoading = ref(true);
onMounted(async () => {
  playlists.value = (await api.get('/playlists/')).data;
  tracks.value = (await api.get('/tracks/')).data;
  users.value = (await api.get('/user/')).data;
  console.log(playlists.value);
  isLoading.value = false;
});

const perPage = 8;
const paginatedAlbums = computed(() => {
  return playlists.value.slice((page.value - 1) * perPage, page.value * perPage);
});

const page = ref(1);

const setPage = (newPage: number) => {
  page.value = newPage;
};
</script>

<template>
  <Wrapper :is-loading="isLoading" :is-empty="!playlists.length" title="Плейлисты">
    <div v-if="playlists" class="grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
      <Card v-for="playlist in paginatedAlbums" v-bind="playlist" />
    </div>
    <Pagination
      class="mt-4 flex justify-center"
      :total="playlists.length"
      show-edges
      :default-page="1">
      <PaginationList v-slot="{ items }" class="flex mx-auto items-center gap-1">
        <PaginationFirst @click="setPage(1)" />
        <PaginationPrev @click="setPage(page - 1)" />

        <template v-for="(item, index) in items">
          <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
            <Button
              @click="setPage(item.value)"
              class="w-10 h-10 p-0"
              :variant="item.value === page ? 'default' : 'outline'">
              {{ item.value }}
            </Button>
          </PaginationListItem>
          <PaginationEllipsis v-else :key="item.type" :index="index" />
        </template>

        <PaginationNext @click="setPage(page + 1)" />
        <PaginationLast @click="setPage(Math.ceil(playlists.length / perPage))" />
      </PaginationList>
    </Pagination>
  </Wrapper>
</template>
