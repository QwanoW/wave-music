<script lang="ts">
import { defineBasicLoader } from 'unplugin-vue-router/data-loaders/basic';
import api from '@/lib/axios';
import { Artist, Album, Playlist } from '@/constants/types';

export const useDashboardData = defineBasicLoader(
  '/player/',
  async () => {
    const statistics = (
      await api.get<{
        tracks: number;
        playlists: number;
        users: number;
        albums: number;
        artists: number;
        genres: number;
        user_liked_tracks: number;
      }>('/')
    ).data;

    const lastArtists = (await api.get<Artist[]>('/artists/?limit=10')).data;
    const lastAlbums = (await api.get<Album[]>('/albums/?limit=10')).data;
    const lastPlaylists = (await api.get<Playlist[]>('/playlists/?limit=10')).data;

    return {
      statistics,
      lastArtists,
      lastAlbums,
      lastPlaylists,
    };
  },
  {
    lazy: true,
  },
);
</script>

<script setup lang="ts">
import { definePage } from 'vue-router/auto';
import { User, Disc3, ListMusic, Laugh } from 'lucide-vue-next';

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { ScrollArea } from '@/components/ui/scroll-area';
import Wrapper from '@/components/admin/wrapper.vue';
import { API_URL } from '@/constants/index';

definePage({
  meta: {
    requiresAuth: true,
    role: 'ADMIN',
  },
});

const { data, isLoading } = useDashboardData();
</script>

<template>
  <Wrapper :is-loading="isLoading" :is-empty="!data" title="Дашборд">
    <div class="grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium"> Исполнители </CardTitle>
          <User class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ data?.statistics.artists }}</div>
        </CardContent>
      </Card>
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium"> Альбомы </CardTitle>
          <Disc3 class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ data?.statistics.albums }}</div>
        </CardContent>
      </Card>
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium"> Плейлисты </CardTitle>
          <ListMusic class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ data?.statistics.playlists }}</div>
        </CardContent>
      </Card>
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium"> Слушатели </CardTitle>
          <Laugh class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ data?.statistics.users }}</div>
        </CardContent>
      </Card>
    </div>
    <Separator class="my-4" />
    <div v-if="data" class="grid gap-4 sm:grid-cols-1 md:gap-8 lg:grid-cols-3">
      <Card>
        <CardHeader class="flex flex-row items-center space-x-2 pb-2">
          <CardTitle class="text-sm font-medium"> Новые исполнители </CardTitle>
          <User class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <ScrollArea class="h-96 w-full">
            <div class="flex flex-col gap-2">
              <div class="flex items-center" v-for="artist in data.lastArtists" :key="artist.id">
                <img
                  class="h-16 w-16 rounded-full"
                  :src="API_URL + artist.photo_uri"
                  :alt="artist.name" />
                <p class="pl-3 text-sm font-medium">{{ artist.name }}</p>
              </div>
            </div>
          </ScrollArea>
        </CardContent>
      </Card>
      <Card>
        <CardHeader class="flex flex-row items-center space-x-2 pb-2">
          <CardTitle class="text-sm font-medium"> Новые альбомы </CardTitle>
          <Disc3 class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <ScrollArea class="h-96 w-full">
            <div class="flex flex-col gap-2">
              <div
                class="flex items-center"
                v-for="album in data.lastAlbums"
                :key="album.id as number">
                <img
                  class="h-16 w-16 rounded-md"
                  :src="API_URL + album.cover_uri"
                  :alt="album.title as string" />
                <p class="pl-3 text-sm font-medium">{{ album.title }}</p>
              </div>
            </div>
          </ScrollArea>
        </CardContent>
      </Card>
      <Card>
        <CardHeader class="flex flex-row items-center space-x-2 pb-2">
          <CardTitle class="text-sm font-medium"> Новые плейлисты </CardTitle>
          <ListMusic class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <ScrollArea class="h-96 w-full">
            <div class="flex flex-col gap-2">
              <div
                class="flex items-center"
                v-for="playlist in data.lastPlaylists"
                :key="playlist.id">
                <img
                  class="h-16 w-16 rounded-md"
                  :src="API_URL + playlist.cover_uri"
                  :alt="playlist.title" />
                <p class="pl-3 text-sm font-medium">{{ playlist.title }}</p>
              </div>
            </div>
          </ScrollArea>
        </CardContent>
      </Card>
    </div>
  </Wrapper>
</template>
