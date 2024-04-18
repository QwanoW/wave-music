<script setup lang="ts">
import { onMounted, ref, provide } from 'vue';

import api from '@/lib/axios';
import Wrapper from '@/components/admin/wrapper.vue';
import Card from '@/components/admin/genre/card.vue';
import { Genre } from '@/constants/types.ts';
import { getURL } from '@/lib/utils';
import { editGenreSchemaType } from '@/schemas';

const genres = ref<Genre[]>([]);

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

onMounted(async () => {
  const response = await api.get('/genres/');
  genres.value = response.data;
});
</script>

<template>
  <Wrapper title="Жанры">
    <div v-if="genres" class="grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
      <Card v-for="genre in genres" v-bind="genre" />
    </div>
  </Wrapper>
</template>
