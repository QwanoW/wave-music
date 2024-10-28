<script setup lang="ts">
import { onMounted, ref, provide, computed } from 'vue';

import api from '@/lib/axios';
import Wrapper from '@/components/admin/wrapper.vue';
import Card from '@/components/admin/artist/card.vue';
import { Artist } from '@/constants/types.ts';
import { getURL } from '@/lib/utils';
import { editArtistSchemaType } from '@/schemas/index';
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
import { Input } from '@/components/ui/input';

const artists = ref<Artist[]>([]);

const onUpdate = (updatedArtist: editArtistSchemaType): void => {
  if (artists.value) {
    const artistIndex = artists.value.findIndex((artist) => artist.id === updatedArtist.id);
    if (artistIndex !== -1) {
      artists.value[artistIndex] = {
        ...artists.value[artistIndex],
        name: updatedArtist.name,
        bio: updatedArtist.bio,
        temp_photo: updatedArtist.photo && getURL(updatedArtist.photo),
      };
    }
  }
};

provide('onUpdate', onUpdate);

const isLoading = ref(true);
onMounted(async () => {
  artists.value = (await api.get('/artists/')).data;
  isLoading.value = false;
});

const artistFilter = ref('');
const perPage = 8;
const paginatedAndFilteredArtists = computed(() => {
  return artists.value
    .filter((artist) => artist.name.toLowerCase().includes(artistFilter.value.toLowerCase()))
    .slice((page.value - 1) * perPage, page.value * perPage);
});

const page = ref(1);

const setPage = (newPage: number) => {
  page.value = newPage;
};
</script>

<template>
  <Wrapper :is-loading="isLoading" :is-empty="!artists.length" title="Исполнители">
    <div class="flex items-center pb-4">
      <Input class="max-w-sm" placeholder="Фильтрация по названию..." v-model="artistFilter" />
    </div>
    <div class="my-5 grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
      <Card v-for="artist in paginatedAndFilteredArtists" :key="artist.id" v-bind="artist" />
    </div>
    <Pagination class="flex justify-center" :total="artists.length" show-edges :default-page="1">
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
        <PaginationLast @click="setPage(Math.ceil(artists.length / perPage))" />
      </PaginationList>
    </Pagination>
  </Wrapper>
</template>
