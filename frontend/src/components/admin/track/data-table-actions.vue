<script setup lang="ts">
import { ref, inject } from 'vue';
import { Track } from '@/constants/types';

import { toast } from '@/components/ui/toast/use-toast';
import api from '@/lib/axios';
import { Button } from '@/components/ui/button';
import EditButton from '@/components/admin/track/edit-button.vue';

const props = defineProps<Track>();

const onDeleteTrack = inject<(id: number) => void>('onDelete');

const isLoadingDelete = ref<boolean>(false);
const onDelete = (id: number): void => {
  isLoadingDelete.value = true;
  api
    .postForm('/tracks/delete_track.php', { id })
    .then(() => {
      if (onDeleteTrack) {
        onDeleteTrack(id);
      }
      toast({
        title: 'Успешно',
        description: 'Трек успешно удалён',
      });
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
</script>

<template>
  <div class="w-fit flex flex-col sm:flex-row gap-x-2 gap-y-2">
    <Button variant="destructive" :loading="isLoadingDelete" @click="onDelete(id)">Удалить</Button>
    <EditButton v-bind="props" />
  </div>
</template>
