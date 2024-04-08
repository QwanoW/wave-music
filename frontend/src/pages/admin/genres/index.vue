<script setup lang="ts">
import { onMounted, ref, provide } from 'vue';

import api from '@/lib/axios';
import Wrapper from '@/components/admin/wrapper.vue';
import Card from '@/components/admin/genre/card.vue';
import { Genre } from '@/constants/types.ts';
import { getURL } from '@/lib/utils';

const genres = ref<Genre[] | null>(null);

const updateGenre = (id: number, name: string, cover?: File): void => {
  if (genres.value) {
    const genreIndex = genres.value.findIndex((genre) => genre.id === id);
    if (genreIndex !== -1) {
      genres.value[genreIndex] = {
        ...genres.value[genreIndex],
        name,
        temp_cover: cover ? getURL(cover) : undefined,
      };
    }
  }
};

provide('updateGenre', updateGenre);

onMounted(async () => {
  const response = await api.get('/genres/');
  genres.value = response.data;
});
</script>

<template>
  <Wrapper title="Жанры">
    <div v-if="genres" class="grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
      <Card
        v-for="genre in genres"
        :key="genre.id"
        :id="genre.id"
        :name="genre.name"
        :cover_uri="genre.cover_uri"
        :temp_cover="genre.temp_cover" />
    </div>
  </Wrapper>
</template>
