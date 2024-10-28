<script setup lang="ts">
import { ref, inject } from 'vue';
import { useForm } from 'vee-validate';
import { vAutoAnimate } from '@formkit/auto-animate/vue';
import Draggable from 'vuedraggable';

import { API_URL } from '@/constants/index';
import { editAlbumSchema, editAlbumSchemaType } from '@/schemas/index';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { getURL } from '@/lib/utils';
import { Input } from '@/components/ui/input';
import { Album, Track } from '@/constants/types';
import api from '@/lib/axios';
import Button from '@/components/ui/button/Button.vue';
import EditDialog from '@/components/admin/edit-dialog.vue';
import FormError from '@/components/form-error.vue';
import FormSuccess from '@/components/form-success.vue';

interface AlbumTrack extends Track {
  order: number;
}

const props = defineProps<Album & { tracks: AlbumTrack[] }>();

const onUpdate = inject<(updatedAlbum: editAlbumSchemaType) => void>('onUpdate');

const form = useForm({
  validationSchema: editAlbumSchema,
  initialValues: {
    id: props.id as number,
    title: props.title as string,
    tracks: props.tracks.map((track) => {
      return {
        id: track.id,
        order: track.order,
      };
    }),
  },
});

const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const onSubmit = form.handleSubmit((values) => {
  isLoading.value = true;
  api
    .postForm('/albums/edit_album.php', values)
    .then((resp) => {
      successMessage.value = resp.data.message;
      if (onUpdate) {
        onUpdate(values);
      }
    })
    .catch((error) => {
      console.log(error);
      errorMessage.value = error.response.data.message;
    })
    .finally(() => {
      isLoading.value = false;
    });
});

const tracks = inject<Track[]>('allTracks');

const newTracks = ref<Album['tracks']>(props.tracks.sort((a, b) => a.order - b.order));
</script>

<template>
  <EditDialog
    triggerTitle="Редактировать"
    dialogTitle="Редактировать плейлист"
    dialogDescription="Редактировать плейлист">
    <form class="flex flex-col gap-y-4" @submit="onSubmit">
      <FormField v-slot="{ componentField }" name="title">
        <FormItem v-auto-animate>
          <FormLabel>Название альбома</FormLabel>
          <FormControl>
            <Input type="text" placeholder="Thriller" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField name="tracks">
        <FormItem v-auto-animate>
          <FormControl>
            <div class="flex w-full h-fit gap-2">
              <div class="w-1/2 space-y-2">
                <FormLabel>Альбом</FormLabel>
                <Draggable
                  class="border border-gray-300 rounded-md h-72 overflow-y-auto"
                  group="allTracks"
                  :animation="200"
                  tag="ul"
                  @change="
                    () => {
                      form.setFieldValue(
                        'tracks',
                        newTracks.map((track, index) => {
                          return { id: track.id, order: index + 1 };
                        }),
                      );
                    }
                  "
                  :list="newTracks">
                  <template #item="{ element, index }">
                    <li class="cursor-grab">
                      <div class="flex items-center gap-x-2 p-2">
                        <span class="text-gray-500">#{{ index + 1 }}</span>
                        {{ element.title }}
                      </div>
                    </li>
                  </template>
                </Draggable>
              </div>
              <div class="w-1/2">
                <span class="block mb-2 font-medium">Все треки</span>
                <Draggable
                  v-if="tracks"
                  class="border border-gray-300 rounded-md h-72 overflow-y-auto"
                  group="allTracks"
                  :animation="200"
                  tag="ul"
                  :list="tracks.filter((t) => !props.tracks.map((t) => t.id).includes(t.id)) || []">
                  <template #item="{ element, index }">
                    <li class="cursor-grab">
                      <div class="flex items-center gap-x-2 p-2">
                        <span class="text-gray-500">#{{ index + 1 }}</span>
                        {{ element.title }}
                      </div>
                    </li>
                  </template>
                </Draggable>
              </div>
            </div>
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>

      <p class="text-gray-500">Перенесите нужные треки в левый столбец и сформируйте альбом</p>
      <FormField
        v-slot="{ componentField: value, handleChange, handleBlur, handleInput, handleReset }"
        name="cover">
        <FormItem v-auto-animate>
          <FormLabel>Обложка</FormLabel>
          <FormControl>
            <Input
              type="file"
              accept="image/*"
              @change="handleChange"
              @blur="handleBlur"
              @input="handleInput"
              @reset="handleReset" />
          </FormControl>
          <FormMessage />
          <div class="flex flex-col gap-y-4" v-if="cover_uri || value.modelValue">
            <p class="">Предпросмотр</p>
            <img
              class="max-w-sm w-full rounded-lg"
              :src="value.modelValue ? getURL(value.modelValue) : API_URL + cover_uri"
              alt="" />
          </div>
        </FormItem>
      </FormField>
      <FormError :message="errorMessage" />
      <FormSuccess :message="successMessage" />
      <Button :loading="isLoading" type="submit"> Сохранить </Button>
    </form>
  </EditDialog>
</template>
