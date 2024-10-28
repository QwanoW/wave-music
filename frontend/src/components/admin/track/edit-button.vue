<script setup lang="ts">
import { ref, inject } from 'vue';
import { useForm } from 'vee-validate';
import { vAutoAnimate } from '@formkit/auto-animate/vue';

import { API_URL } from '@/constants/index';
import { editTrackSchema, editTrackSchemaType } from '@/schemas/index';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { getURL } from '@/lib/utils';
import { Input } from '@/components/ui/input';
import { Track } from '@/constants/types';
import api from '@/lib/axios';
import Button from '@/components/ui/button/Button.vue';
import EditDialog from '@/components/admin/edit-dialog.vue';
import FormError from '@/components/form-error.vue';
import FormSuccess from '@/components/form-success.vue';
import { Genre, Artist, Language } from '@/constants/types.ts';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Command, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { Check, ChevronsUpDown } from 'lucide-vue-next';

const onUpdate = inject<(updatedTrack: editTrackSchemaType) => void>('onUpdate');
const { genres, languages, trackArtists } = inject<{
  genres: Genre[];
  languages: Language[];
  trackArtists: Artist[];
}>('track-data') as any;

const props = defineProps<Track>();

const form = useForm({
  validationSchema: editTrackSchema,
  initialValues: {
    id: props.id,
    title: props.title,
    artists: props.artists.map((artist) => artist.id),
    genre_id: String(props.genre.id),
    language_id: String(props.language.id),
  },
});

const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const onSubmit = form.handleSubmit((values) => {
  isLoading.value = true;
  api
    .postForm('/tracks/edit_track.php', values)
    .then((resp) => {
      successMessage.value = resp.data.message;
      if (onUpdate) {
        onUpdate(values);
      }
    })
    .catch((error) => {
      errorMessage.value = error.response.data.message;
    })
    .finally(() => {
      isLoading.value = false;
    });
});
</script>

<template>
  <EditDialog
    triggerTitle="Редактировать"
    dialogTitle="Редактировать трек"
    dialogDescription="Редактировать трек">
    <form class="flex flex-col gap-y-4" @submit="onSubmit">
      <FormField v-slot="{ componentField }" name="title">
        <FormItem v-auto-animate>
          <FormLabel>Название трека</FormLabel>
          <FormControl>
            <Input type="text" placeholder="Король и шут" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ handleChange, handleBlur, handleInput, handleReset }" name="audio">
        <FormItem v-auto-animate>
          <FormLabel>Аудиофайл</FormLabel>
          <FormControl>
            <Input
              type="file"
              accept="audio/*"
              @change="handleChange"
              @blur="handleBlur"
              @input="handleInput"
              @reset="handleReset" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="genre_id">
        <FormItem>
          <FormLabel>Жанр</FormLabel>
          <Select v-bind="componentField">
            <FormControl>
              <SelectTrigger>
                <SelectValue placeholder="Выберите жанр трека" />
              </SelectTrigger>
            </FormControl>
            <SelectContent>
              <SelectItem v-for="genre in genres" :value="String(genre.id)">
                {{ genre.name }}
              </SelectItem>
            </SelectContent>
          </Select>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="language_id">
        <FormItem>
          <FormLabel>Язык</FormLabel>
          <Select v-bind="componentField">
            <FormControl>
              <SelectTrigger>
                <SelectValue placeholder="Выберите язык трека" />
              </SelectTrigger>
            </FormControl>
            <SelectContent>
              <SelectItem v-for="language in languages" :value="String(language.id)">
                {{ language.name }}
              </SelectItem>
            </SelectContent>
          </Select>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField name="artists">
        <FormItem class="flex flex-col">
          <FormLabel>Исполнители</FormLabel>
          <Popover>
            <PopoverTrigger as-child>
              <FormControl>
                <Button
                  variant="outline"
                  role="combobox"
                  :class="
                    cn('w-fit justify-between', !form.values.artists && 'text-muted-foreground')
                  ">
                  {{
                    form.values.artists
                      ? 'Выбрано ' + form.values.artists.length + ' исполнителей'
                      : 'Выберите исполнителя...'
                  }}
                  <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
              </FormControl>
            </PopoverTrigger>
            <PopoverContent class="w-[200px] p-0">
              <Command>
                <CommandList>
                    <CommandItem
                      v-if="trackArtists"
                      v-for="artist in trackArtists"
                      :key="artist.id"
                      :value="artist.id"
                      @select="
                        () => {
                          if (form.values.artists?.includes(artist.id)) {
                            form.setFieldValue(
                              'artists',
                              form.values.artists?.filter((id) => id !== artist.id),
                            );
                          } else {
                            form.setFieldValue('artists', [
                              ...(form.values.artists || []),
                              artist.id,
                            ]);
                          }
                        }
                      ">
                      <Check
                        :class="
                          cn(
                            'mr-2 h-4 w-4',
                            (form.values.artists || []).includes(artist.id)
                              ? 'opacity-100'
                              : 'opacity-0',
                          )
                        " />
                      <p class="truncate">{{ artist.name }}</p>
                    </CommandItem>
                </CommandList>
              </Command>
            </PopoverContent>
          </Popover>
          <FormMessage />
        </FormItem>
      </FormField>
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
          <div class="flex flex-col gap-y-4" v-if="props.cover_uri || value.modelValue">
            <p class="">Предпросмотр</p>
            <img
              class="max-w-sm w-full rounded-lg"
              :src="value.modelValue ? getURL(value.modelValue) : API_URL + props.cover_uri"
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
