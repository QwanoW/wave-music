<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useForm } from 'vee-validate';
import { vAutoAnimate } from '@formkit/auto-animate/vue';

import { ScrollArea } from '@/components/ui/scroll-area';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import FormError from '@/components/form-error.vue';
import FormSuccess from '@/components/form-success.vue';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Textarea } from '@/components/ui/textarea';
import { Command, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Input } from '@/components/ui/input';
import Button from '@/components/ui/button/Button.vue';
import Wrapper from '@/components/admin/wrapper.vue';
import { playlistSchema } from '@/schemas/index';
import api from '@/lib/axios';
import { Track, User } from '@/constants/types.ts';
import { getURL } from '@/lib/utils';

const tracks = ref<Track[] | null>(null);
const users = ref<User[] | null>(null);

const form = useForm({
  validationSchema: playlistSchema,
});

const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const onSubmit = form.handleSubmit((values) => {
  isLoading.value = true;
  api
    .postForm('/playlists/add_playlist.php', values)
    .then(() => {
      successMessage.value = 'Плейлист добавлен';
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
  tracks.value = (await api.get('/tracks/')).data;
  users.value = (await api.get('/user/')).data;
});
</script>

<!-- TODO: IF NO DATA SHOW LINK TO ADD DATA -->
<template>
  <Wrapper title="Добавить плейлист" width-limit>
    <form class="flex flex-col gap-y-4" @submit="onSubmit">
      <FormField v-slot="{ componentField }" name="title">
        <FormItem v-auto-animate>
          <FormLabel>Название плейлиста</FormLabel>
          <FormControl>
            <Input type="text" placeholder="Плейлист весёлой музыки" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="description">
        <FormItem v-auto-animate>
          <FormLabel>Описание</FormLabel>
          <FormControl>
            <Textarea type="text" placeholder="Поднимет настроение" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField name="artists">
        <FormItem v-auto-animate class="flex flex-col">
          <FormLabel>Треки</FormLabel>
          <Popover>
            <PopoverTrigger as-child>
              <FormControl>
                <Button
                  variant="outline"
                  role="combobox"
                  :class="
                    cn('w-fit justify-between', !form.values.tracks && 'text-muted-foreground')
                  ">
                  {{
                    form.values.tracks
                      ? 'Выбрано ' + form.values.tracks.length + ' треков'
                      : 'Выберите треки...'
                  }}
                  <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
              </FormControl>
            </PopoverTrigger>
            <PopoverContent class="w-[200px] p-0">
              <ScrollArea class="max-h-96">
                <Command>
                  <CommandList>
                      <CommandItem
                        v-for="track in tracks"
                        :key="track.id"
                        :value="track.id"
                        @select="
                          () => {
                            if (form.values.tracks?.includes(track.id)) {
                              form.setFieldValue(
                                'tracks',
                                form.values.tracks?.filter((id) => id !== track.id),
                              );
                            } else {
                              form.setFieldValue('tracks', [
                                ...(form.values.tracks || []),
                                track.id,
                              ]);
                            }
                          }
                        ">
                        <Check
                          :class="
                            cn(
                              'mr-2 h-4 w-4',
                              (form.values.tracks || []).includes(track.id)
                                ? 'opacity-100'
                                : 'opacity-0',
                            )
                          " />
                        <p class="truncate">{{ track.title }}</p>
                      </CommandItem>
                  </CommandList>
                </Command>
              </ScrollArea>
            </PopoverContent>
          </Popover>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="user_id">
        <FormItem>
          <FormLabel>Владелец плейлиста (необязательно)</FormLabel>
          <Select v-bind="componentField">
            <FormControl>
              <SelectTrigger>
                <SelectValue placeholder="Выберите владельца плейлиста" />
              </SelectTrigger>
            </FormControl>
            <SelectContent>
              <SelectItem v-for="user in users" :value="String(user.id)"
                ><div>
                  <p>{{ user.email }}</p>
                  <p class="text-xs text-muted-foreground">
                    {{ user.username }}
                  </p>
                </div></SelectItem
              >
            </SelectContent>
          </Select>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ value, handleChange }" name="is_private">
        <FormItem class="flex flex-col justify-between rounded-lg border p-4 w-fit">
          <FormLabel class="text-base"> Приватность </FormLabel>
          <FormDescription> Просматривать плейлист сможет только владелец </FormDescription>
          <FormControl>
            <Switch :disabled="form.values.user_id ? false : true" :checked="value" @update:checked="handleChange" />
          </FormControl>
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
      <Button :loading="isLoading" type="submit"> Сохранить </Button>
    </form>
  </Wrapper>
</template>

<style scoped></style>
