<script setup lang="ts">
import { onMounted, ref, provide } from 'vue';

import { API_URL } from '@/constants/index';
import { editTrackSchemaType } from '@/schemas';
import { getURL } from '@/lib/utils';
import { Track, Language, Genre, Artist } from '@/constants/types';
import { useToast } from '@/components/ui/toast/use-toast';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import api from '@/lib/axios';
import Button from '@/components/ui/button/Button.vue';
import EditButton from '@/components/admin/track/edit-button.vue';
import Wrapper from '@/components/admin/wrapper.vue';

const { toast } = useToast();

const tracks = ref<Track[]>([]);
const languages = ref<Language[]>([]);
const genres = ref<Genre[]>([]);
const artists = ref<Artist[]>([]);

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

const isLoadingDelete = ref<boolean>(false);
const onDelete = (id: number): void => {
  isLoadingDelete.value = true;
  api
    .postForm('/tracks/delete_track.php', { id })
    .then(() => {
      const index = (tracks.value || []).findIndex((track) => track.id === id);
      if (index !== -1) {
        (tracks.value || []).splice(index, 1);
        toast({
          title: 'Успешно',
          description: 'Язык был успешно удален',
        });
      }
    })
    .catch((error) => {
      toast({
        title: 'Ошибка',
        description: error.response.data.message,
      });
    })
    .finally(() => {
      isLoadingDelete.value = false;
    });
};

onMounted(async () => {
  tracks.value = (await api.get('/tracks/')).data;
  genres.value = (await api.get('/genres/')).data;
  languages.value = (await api.get('/languages/')).data;
  artists.value = (await api.get('/artists/')).data;
});
</script>

<template>
  <Wrapper title="Треки">
    <Table>
      <TableCaption>Список треков.</TableCaption>
      <TableHeader>
        <TableRow>
          <TableHead class="w-[100px]"> # </TableHead>
          <TableHead> Трек </TableHead>
          <TableHead class="text-right"> Действия </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-if="tracks" v-for="track in tracks" :key="track.id">
          <TableCell class="font-medium">
            {{ track.id }}
          </TableCell>
          <TableCell>
            <div class="flex items-center gap-x-2">
              <img class="w-16 h-16" :src="track.temp_cover || API_URL + track.cover_uri" alt="" />
              <span class="font-medium">{{ track.title }}</span>
            </div>
          </TableCell>
          <TableCell class="h-full">
            <div class="h-full flex flex-col sm:flex-row justify-end items-end sm:gap-x-2 gap-y-2">
              <div>
                <EditButton :key="track.id" v-bind="track" />
              </div>
              <Button :disabled="isLoadingDelete" @click="onDelete(track.id)" variant="outline"
                >Удалить</Button
              >
            </div>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </Wrapper>
</template>
