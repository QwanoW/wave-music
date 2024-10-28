<script setup lang="ts">
import { inject, ref } from 'vue';

import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import EditButton from '@/components/admin/album/edit-button.vue';
import { API_URL } from '@/constants/index';
import { Album, Track } from '@/constants/types';
import api from '@/lib/axios';
import { useToast } from '@/components/ui/toast/use-toast';

interface AlbumTrack extends Track {
  order: number;
}

const props = defineProps<Album & { tracks: AlbumTrack[] }>();

const onDelete = inject<(id: number) => void>('onDelete');

const { toast } = useToast();
const isLoading = ref(false);
const deleteAlbum = (id: number) => {
  isLoading.value = true;
  api
    .delete(`/albums/delete_album.php?id=${id}`)
    .then(() => {
      if (onDelete) {
        onDelete(id);
      }
      toast({
        title: 'Успешно',
        description: 'Альбом успешно удалён',
      });
    })
    .catch((error) => {
      toast({
        title: 'Ошибка',
        description: error.response.data.message,
      });
    })
    .finally(() => {
      isLoading.value = false;
    });
};
</script>

<template>
  <Card>
    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
      <CardTitle class="text-lg font-medium">{{ title }}</CardTitle>
    </CardHeader>
    <CardContent class="space-y-2">
      <img
        :src="temp_cover ? temp_cover : API_URL + cover_uri"
        class="rounded-lg w-full object-cover aspect-square" />
      <EditButton v-bind="props" />
      <Button
        :disabled="isLoading"
        @click="deleteAlbum(id as number)"
        class="w-full bg-destructive hover:bg-transparent hover:text-destructive"
        >Удалить</Button
      >
    </CardContent>
  </Card>
</template>
