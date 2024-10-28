<script setup lang="ts">
import { onMounted, ref, provide } from 'vue';

import { editTrackSchemaType } from '@/schemas';
import { getURL } from '@/lib/utils';
import { Track, Language, Genre, Artist } from '@/constants/types';
import api from '@/lib/axios';

import Wrapper from '@/components/admin/wrapper.vue';
import { columns } from '@/components/admin/track/columns';
import DataTable from '@/components/admin/track/data-table.vue';

const tracks = ref<Track[]>([]);
const languages = ref<Language[]>([]);
const genres = ref<Genre[]>([]);
const artists = ref<Artist[]>([]);

provide('track-data', { genres, languages, trackArtists: artists, tracks });

provide('onUpdate', (updatedTrack: editTrackSchemaType) => {
  const index = (tracks.value || []).findIndex((track) => track.id === updatedTrack.id);
  if (index !== -1 && tracks.value) {
    tracks.value[index] = {
      ...tracks.value[index],
      title: updatedTrack.title,
      genre: genres.value.find((g) => g.id === Number(updatedTrack.genre_id)) as Genre,
      language: languages.value.find((l) => l.id === Number(updatedTrack.language_id)) as Language,
      artists: artists.value.filter((a) => updatedTrack.artists.includes(a.id)),
      temp_cover: updatedTrack.cover && getURL(updatedTrack.cover),
    };
  }
});

provide('onDelete', (id: number) => {
  tracks.value = tracks.value.filter((track) => track.id !== id);
});

const isLoading = ref(true);
onMounted(async () => {
  tracks.value = (await api.get('/tracks/')).data;
  genres.value = (await api.get('/genres/')).data;
  languages.value = (await api.get('/languages/')).data;
  artists.value = (await api.get('/artists/')).data;
  isLoading.value = false;
});
</script>

<template>
  <Wrapper :is-loading="isLoading" :is-empty="!tracks.length" title="Треки">
    <DataTable :columns="columns" :data="tracks" />
  </Wrapper>
</template>
