<script setup lang="ts">
import { onMounted, ref, provide, computed } from 'vue';

import { Album, Track } from '@/constants/types';
import { editAlbumSchemaType } from '@/schemas/index';
import api from '@/lib/axios';
import Card from '@/components/admin/album/card.vue';
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

const albums = ref<Album[]>([]);
const tracks = ref<Track[]>([]);

provide('allTracks', tracks);

provide('onUpdate', (updatedAlbum: editAlbumSchemaType) => {
  albums.value = albums.value.map((album) => {
    if (album.id === updatedAlbum.id) {
      const tracksIds = updatedAlbum.tracks.map((t) => t.id);

      return {
        ...album,
        title: updatedAlbum.title,
        tracks: tracks.value
          .filter((t) => tracksIds.includes(t.id))
          .map((t) => ({
            ...t,
            order: updatedAlbum.tracks.find((t2) => t2.id === t.id)?.order as number,
          })),
        temp_cover: updatedAlbum.cover && getURL(updatedAlbum.cover),
      };
    } else {
      return album;
    }
  });
});

provide('onDelete', (id: number) => {
  albums.value = albums.value.filter((a) => a.id !== id);
});

const isLoading = ref(true);

onMounted(async () => {
  albums.value = (await api.get('/albums/')).data;
  albums.value = await Promise.all(
    albums.value.map(async (album) => ({
      ...album,
      tracks: (
        await api.get(`/tracks/?album_id=${album.id}`)
      ).data.map((t: any) => {
        return {
          ...t,
          order: t.album.order,
        };
      }),
    })),
  );

  tracks.value = (await api.get('/tracks/')).data;
  isLoading.value = false;
});

const perPage = 8;
const paginatedAlbums = computed(() => {
  return albums.value.slice((page.value - 1) * perPage, page.value * perPage);
});

const page = ref(1);

const setPage = (newPage: number) => {
  page.value = newPage;
};
</script>

<template>
  <Wrapper :is-loading="isLoading" :is-empty="!albums.length" title="Альбомы">
    <div class="grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
      <Card v-for="album in paginatedAlbums" v-bind="album" />
    </div>
    <Pagination
      class="mt-4 flex justify-center"
      :total="albums.length"
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
        <PaginationLast @click="setPage(Math.ceil(albums.length / perPage))" />
      </PaginationList>
    </Pagination>
  </Wrapper>
</template>
