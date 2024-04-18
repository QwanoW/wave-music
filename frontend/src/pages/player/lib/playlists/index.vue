<script lang="ts">
import { defineBasicLoader } from 'unplugin-vue-router/data-loaders/basic';

import { userStore } from '@/stores/user';
import playlistService from '@/services/playlist.service';

export const usePlaylistData = defineBasicLoader(
  '/player/lib/playlists/',
  async () => {
    if (!userStore.user) {
      return {
        likedPlaylists: [],
        myPlaylists: [],
      };
    }

    const likedPlaylistsPromise = userStore.likedPlaylists.map(async (id) => {
      return await playlistService.getById(id);
    });

    const likedPlaylists = await Promise.all(likedPlaylistsPromise);

    const myPlaylists = await playlistService.getByOwner(userStore.user.id);

    return {
      likedPlaylists,
      myPlaylists,
    };
  },
  {
    lazy: true,
  },
);
</script>

<script setup lang="ts">
import { PlusIcon } from 'lucide-vue-next';

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

const { data, isLoading } = usePlaylistData();
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
          
        </DialogScrollContent>
      </Dialog>
    </div>

    <div v-if="isLoading" class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-8 gap-4">
      <div v-for="_ in 4" class="w-full h-full flex flex-col gap-y-1">
        <Skeleton class="bg-primary/30 w-full aspect-square rounded-lg" />
        <div class="w-full flex flex-col justify-center gap-y-1">
          <Skeleton class="bg-primary/30 w-24 h-6" />
          <Skeleton class="bg-primary/30 w-16 h-6" />
        </div>
      </div>
    </div>
    <div
      v-else-if="data && data.myPlaylists.length"
      class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-8 gap-4">
      <ContentCard
        v-for="playlist in data.myPlaylists"
        :href="`/player/playlists/${playlist.id}`"
        :title="playlist.title"
        :cover="playlist.cover_uri" />
    </div>
    <p v-else class="text-lg text-muted-foreground">Вы не создали ни одного плейлиста</p>

    <h1 class="font-bold text-2xl">Вам понравились эти плейлисты</h1>
    <div v-if="isLoading" class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-8 gap-4">
      <div v-for="_ in 4" class="w-full h-full flex flex-col gap-y-1">
        <Skeleton class="bg-primary/30 w-full aspect-square rounded-lg" />
        <div class="w-full flex flex-col justify-center gap-y-1">
          <Skeleton class="bg-primary/30 w-24 h-6" />
          <Skeleton class="bg-primary/30 w-16 h-6" />
        </div>
      </div>
    </div>
    <div
      v-else-if="data && data.likedPlaylists.length"
      class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-8 gap-4">
      <ContentCard
        v-for="playlist in data.likedPlaylists"
        :href="`/player/playlists/${playlist.id}`"
        :title="playlist.title"
        :cover="playlist.cover_uri" />
    </div>
    <p v-else class="text-lg text-muted-foreground">
      Добавляйте любимые плейлисты в медиатеку и они будут отображаться здесь.
    </p>
  </div>
</template>
