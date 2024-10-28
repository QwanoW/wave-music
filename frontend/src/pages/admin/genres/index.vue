<script setup lang="ts">
import { onMounted, ref, provide, computed } from 'vue';
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

import api from '@/lib/axios';
import Wrapper from '@/components/admin/wrapper.vue';
import Card from '@/components/admin/genre/card.vue';
import { Genre } from '@/constants/types.ts';
import { getURL } from '@/lib/utils';
import { editGenreSchemaType } from '@/schemas';

const genres = ref<Genre[]>([]);
const perPage = 8;

const onUpdate = (updatedGenre: editGenreSchemaType): void => {
  if (genres.value) {
    const genreIndex = genres.value.findIndex((genre) => genre.id === updatedGenre.id);
    if (genreIndex !== -1) {
      genres.value[genreIndex] = {
        ...genres.value[genreIndex],
        name: updatedGenre.name,
        temp_cover: updatedGenre.cover && getURL(updatedGenre.cover),
      };
    }
  }
};

provide('onUpdate', onUpdate);

const isLoading = ref(true);
onMounted(async () => {
  const response = await api.get('/genres/');
  genres.value = response.data;
  isLoading.value = false;
});

const paginatedGenres = computed(() => {
  return genres.value.slice((page.value - 1) * perPage, page.value * perPage);
});

const page = ref(1);

const setPage = (newPage: number) => {
  page.value = newPage;
};
</script>

<template>
  <Wrapper :is-loading="isLoading" :is-empty="!genres.length" title="Жанры">
    <div class="grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
      <Card v-for="genre in paginatedGenres" v-bind="genre" :key="genre.id" />
    </div>
    <Pagination
      class="mt-4 flex justify-center"
      :total="genres.length"
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
        <PaginationLast @click="setPage(Math.ceil(genres.length / perPage))" />
      </PaginationList>
    </Pagination>
  </Wrapper>
</template>
