<script lang="ts">
import { defineBasicLoader } from 'unplugin-vue-router/data-loaders/basic';

import { userStore } from '@/stores/user';
import playlistService from '@/services/playlist.service';
import { ref } from 'vue';

export const usePlaylistData = defineBasicLoader(
  '/player/lib/playlists/',
  async () => {
    if (!userStore.user) {
      return [];
    }

    const likedPlaylistsPromise = userStore.likedPlaylists.map(async (id) => {
      return await playlistService.getById(id);
    });

    return (await Promise.all(likedPlaylistsPromise)).filter((p) => !!p);
  },
  {
    lazy: true,
  },
);
</script>

<script setup lang="ts">
import { PlusIcon } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import { vAutoAnimate } from '@formkit/auto-animate';

import api from '@/lib/axios';
import {
  Dialog,
  DialogScrollContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Skeleton } from '@/components/ui/skeleton';
import ContentCard from '@/components/player/content-card.vue';
import { Button } from '@/components/ui/button';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Playlist } from '@/constants/types';
import { toast } from '@/components/ui/toast';
import { audioStore } from '@/stores/audio';

const formSchema = toTypedSchema(
  z.object({
    title: z.string().min(2).max(50),
    description: z.string().optional(),
    is_private: z.boolean().default(false),
  }),
);

const { handleSubmit } = useForm({
  validationSchema: formSchema,
});

const { data: likedPlaylists, isLoading } = usePlaylistData();

const isLoadingAdd = ref(false);
const onSubmit = handleSubmit((values) => {
  if (!userStore.user?.is_subscribed) {
    toast({
      title: 'Ошибка',
      description: 'Это действие доступно только для пользователей с премиум подпиской',
      variant: 'destructive',
    });
    return;
  }

  api
    .postForm<{ message: string; data: Playlist }>('/user/playlists/add.php', values)
    .then((resp) => {
      userStore.myPlaylists.push(resp.data.data);
      toast({
        title: 'Плейлист создан',
        description: resp.data.message,
      });
    })
    .catch((error) => {
      toast({
        title: 'Произошла ошибка',
        description: error.response.data.message,
      });
    })
    .finally(() => {
      isLoadingAdd.value = false;
    });
});
</script>

<template>
  <div class="w-full flex flex-col gap-y-6">
    <div class="flex justify-between">
      <h1 class="font-bold text-3xl">Ваши плейлисты</h1>
      <Dialog>
        <DialogTrigger>
          <PlusIcon class="w-6 h-6" />
        </DialogTrigger>
        <DialogScrollContent class="sm:max-w-[425px]">
          <DialogHeader>
            <DialogTitle>Создание плейлиста</DialogTitle>
            <DialogDescription>Составьте свой собственный плейлист</DialogDescription>
          </DialogHeader>
          <form class="space-y-2" @submit="onSubmit">
            <FormField v-slot="{ componentField }" name="title">
              <FormItem>
                <FormLabel>Название плейлиста</FormLabel>
                <FormControl>
                  <Input type="text" placeholder="Мой плейлист" v-bind="componentField" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="description">
              <FormItem>
                <FormLabel>Описание (необязательно)</FormLabel>
                <FormControl>
                  <Textarea type="text" placeholder="Мой плейлист" v-bind="componentField" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>
            <FormField v-slot="{ value, handleChange }" name="is_private">
              <FormItem class="flex flex-col justify-between rounded-lg border p-4 w-fit">
                <FormLabel class="text-base"> Приватность </FormLabel>
                <FormDescription> Просматривать плейлист сможете только вы </FormDescription>
                <FormControl>
                  <Switch :checked="value" @update:checked="handleChange" />
                </FormControl>
              </FormItem>
            </FormField>
            <Button :disabled="isLoadingAdd" type="submit"> Отправить </Button>
          </form>
        </DialogScrollContent>
      </Dialog>
    </div>

    <div
      v-if="userStore.myPlaylists && userStore.myPlaylists.length"
      v-auto-animate
      class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-8 gap-4">
      <ContentCard
        v-for="playlist in userStore.myPlaylists"
        :href="`/player/playlists/${playlist.id}`"
        :title="playlist.title"
        :tracks="playlist.tracks"
        :on-click="() => audioStore.setPlaylist(playlist.tracks)"
        :cover="playlist.cover_uri" />
    </div>
    <p v-else class="text-lg text-muted-foreground">Вы не создали ни одного плейлиста</p>

    <h1 class="font-bold text-2xl">Вам понравились эти плейлисты</h1>
    <div v-if="isLoading" class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-8 gap-4">
      <div v-for="_ in 4" class="w-full h-full flex flex-col gap-y-1">
        <Skeleton class="bg-primary/30 w-full aspect-square rounded-lg" />
        <div class="w-full flex flex-col justify-center gap-y-1">
          <Skeleton class="bg-primary/30 w-24 h-6" />
        </div>
      </div>
    </div>
    <div
      v-else-if="likedPlaylists && likedPlaylists.length"
      class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-8 gap-4">
      <ContentCard
        :href="'/player/playlists/'"
        title="Любимые треки"
        cover="/content/images/default/liked.svg" />
      <ContentCard
        v-for="playlist in likedPlaylists"
        :href="`/player/playlists/${playlist.id}`"
        :title="playlist.title"
        :on-click="() => audioStore.setPlaylist(playlist.tracks)"
        :cover="playlist.cover_uri" />
    </div>
    <div v-else class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-8 gap-4">
      <ContentCard
        :href="'/player/playlists/liked/'"
        title="Любимые треки"
        cover="/content/images/default/liked.svg" />
    </div>
  </div>
</template>
