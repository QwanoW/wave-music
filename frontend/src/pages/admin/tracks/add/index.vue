<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useForm } from 'vee-validate';
import { vAutoAnimate } from '@formkit/auto-animate/vue';

import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import FormError from '@/components/form-error.vue';
import FormSuccess from '@/components/form-success.vue';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Command, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Input } from '@/components/ui/input';
import Button from '@/components/ui/button/Button.vue';
import Wrapper from '@/components/admin/wrapper.vue';
import { trackSchema } from '@/schemas/index';
import api from '@/lib/axios';
import { Genre, Artist, Language } from '@/constants/types.ts';
import { getURL } from '@/lib/utils';
import { Progress } from '@/components/ui/progress';

const artists = ref<Artist[] | null>(null);
const genres = ref<Genre[] | null>(null);
const languages = ref<Language[] | null>(null);

const form = useForm({
  validationSchema: trackSchema,
});

const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const loadingProgress = ref(0);

const onSubmit = form.handleSubmit((values) => {
  isLoading.value = true;
  api
    .postForm('/tracks/add_track.php', values, {
      onUploadProgress: (progressEvent) => {
        if (progressEvent.progress) {
          loadingProgress.value = Math.round(progressEvent.progress * 100);
        }
      },
    })
    .then(() => {
      successMessage.value = 'Трек добавлен';
      form.resetForm();
    })
    .catch((error) => {
      errorMessage.value = error.response.data.message;
    })
    .finally(() => {
      isLoading.value = false;
    });
});

onMounted(async () => {
  genres.value = (await api.get('/genres/')).data;
  artists.value = (await api.get('/artists/')).data;
  languages.value = (await api.get('/languages/')).data;
});
</script>

<!-- TODO: IF NO DATA SHOW LINK TO ADD DATA -->
<template>
  <Wrapper title="Добавить трек" width-limit>
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
        <FormItem v-auto-animate>
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
        <FormItem v-auto-animate>
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
        <FormItem v-auto-animate class="flex flex-col">
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
                <!-- <CommandInput placeholder="Поиск исполнителя..." />
                <CommandEmpty>Ничего не найдено.</CommandEmpty> -->
                <CommandList>
                  <CommandItem
                    v-for="artist in artists"
                    :key="artist.id"
                    :value="artist.id"
                    @select="
                        () => {
                          if (form.values.artists?.includes(artist.id)) {
                            form.setFieldValue(
                              'artists',
                              form.values.artists?.filter((id: any) => id !== artist.id),
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
          <div class="flex flex-col gap-y-4" v-if="value.modelValue">
            <p class="">Предпросмотр</p>
            <img class="max-w-sm w-full rounded-lg" :src="getURL(value.modelValue)" alt="" />
          </div>
        </FormItem>
      </FormField>
      <FormError :message="errorMessage" />
      <FormSuccess :message="successMessage" />
      <p v-if="loadingProgress > 0" class="text-center text-xs mt-2">
        {{ loadingProgress }} % загружено
      </p>
      <Progress v-if="loadingProgress > 0" v-model="loadingProgress" class="w-full" />
      <Button :loading="isLoading" type="submit"> Сохранить </Button>
    </form>
  </Wrapper>
</template>

<style scoped></style>
