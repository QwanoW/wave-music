<script setup lang="ts">
import { onMounted, ref, provide } from 'vue';

import api from '@/lib/axios';
import Wrapper from '@/components/admin/wrapper.vue';
import Card from '@/components/admin/artist/card.vue';
import { Artist } from '@/constants/types.ts';
import { getURL } from '@/lib/utils';
import { editArtistSchemaType } from '@/schemas/index';

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

onMounted(async () => {
  artists.value = (await api.get('/artists/')).data;
});
</script>

<template>
  <Wrapper title="Исполнители">
    <div v-if="artists" class="grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
      <Card v-for="artist in artists" :key="artist.id" v-bind="artist" />
    </div>
  </Wrapper>
</template>
