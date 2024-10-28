<script setup lang="ts">
definePage({
  meta: {
    requiresAuth: true,
  },
});

import api from '@/lib/axios';
import { ref } from 'vue';
import { definePage } from 'vue-router/auto';
import { Search } from 'lucide-vue-next';
import { debounce } from 'lodash';

import Loader from '@/components/loader.vue';
import { Button } from '@/components/ui/button';
import { audioStore } from '@/stores/audio';
import { userStore } from '@/stores/user';
import { Album, Artist, Track } from '@/constants/types';
import { Input } from '@/components/ui/input';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { API_URL } from '@/constants';
import { Clock, Play, Heart, PlusIcon } from 'lucide-vue-next';
import Equalizer from '@/components/player/equalizer.vue';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { toast } from '@/components/ui/toast';

const addTrackToPlaylist = (id: number, track: Track) => {
  api
    .postForm('/user/playlists/add_track.php', { playlist_id: id, track_id: track.id })
    .then((resp) => {
      userStore.myPlaylists = userStore.myPlaylists.map((p) => {
        if (p.id == id) {
          p.tracks.push(track);
        }
        return p;
      });
      toast({
        title: 'Трек добавлен в плейлист',
        description: resp.data.message,
      });
    });
};

const searchedTracks = ref<Track[]>([]);
const searchedArtists = ref<Artist[]>([]);
const searchedAlbums = ref<Album[]>([]);

const search = ref('');

const isLoading = ref(false);
const tab = ref('Треки');
const onInput = debounce(async (newTab?: string) => {
  if (newTab) {
    tab.value = newTab;
  }

  if (search.value === '') {
    return;
  }

  isLoading.value = true;
  try {
    switch (tab.value) {
      case 'Треки':
        searchedTracks.value = (await api.get(`/tracks/?search=${search.value}`)).data;
        break;
      case 'Исполнители':
        searchedArtists.value = (await api.get(`/artists/?search=${search.value}`)).data;
        break;
      case 'Альбомы':
        searchedAlbums.value = (await api.get(`/albums/?search=${search.value}`)).data;
        break;
      default:
        break;
    }
  } catch (error) {
    console.log(error);
  }
  isLoading.value = false;
}, 500);
</script>

<template>
  <div class="max-w-5xl m-auto w-full h-full flex flex-col gap-y-4 p-5">
    <h1 class="font-medium text-2xl mb-5 text-amber-700">Поиск</h1>
    <div class="relative w-full items-center">
      <Input
        v-model="search"
        @input="() => onInput()"
        id="search"
        type="text"
        placeholder="Поиск..."
        class="pl-10 py-6" />
      <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
        <Search class="size-6 text-muted-foreground" />
      </span>
    </div>
    <Tabs @update:model-value="(value) => onInput(value.toString())" default-value="Треки">
      <TabsList class="grid w-full grid-cols-3">
        <TabsTrigger value="Треки"> Треки </TabsTrigger>
        <TabsTrigger value="Исполнители"> Исполнители </TabsTrigger>
        <TabsTrigger value="Альбомы"> Альбомы </TabsTrigger>
      </TabsList>
      <Loader v-if="isLoading" />
      <TabsContent class="w-full" value="Треки">
        <Table v-if="!isLoading && searchedTracks.length" class="w-full max-w-5xl m-auto">
          <TableHeader>
            <TableRow>
              <TableHead class="text-sm text-center font-normal w-[60px]"> # </TableHead>
              <TableHead class="text-sm font-normal uppercase"> Трек </TableHead>
              <TableHead class="text-sm font-normal uppercase">Исполнитель</TableHead>
              <TableHead class="text-sm font-normal uppercase">Альбом</TableHead>
              <TableHead class="flex justify-end"> <Clock /> </TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow
              class="group"
              :class="audioStore.currentAudio?.id === track.id ? 'bg-secondary' : ''"
              @click="
                () =>
                  audioStore.currentAudio?.id === track.id && audioStore.isPlaying
                    ? audioStore.pause()
                    : audioStore.setPlaylist(searchedTracks, track.id)
              "
              v-for="(track, index) in searchedTracks"
              :key="track.id">
              <TableCell>
                <div class="flex justify-center items-center">
                  <Equalizer
                    v-if="audioStore.currentAudio?.id === track.id && audioStore.isPlaying" />
                  <div v-else>
                    <span
                      :class="audioStore.currentAudio?.id === track.id ? 'text-orange-600' : ''"
                      class="group-hover:hidden"
                      >{{ index + 1 }}</span
                    >
                    <Play
                      class="hidden group-hover:block text-orange-500 fill-orange-500 w-5 h-5" />
                  </div>
                </div>
              </TableCell>
              <TableCell class="flex items-center gap-x-3">
                <img
                  class="w-16 h-16 rounded object-cover"
                  :src="API_URL + track.cover_uri"
                  alt="" />
                <p :class="audioStore.currentAudio?.id === track.id ? 'text-orange-600' : ''">
                  {{ track.title }}
                </p>
              </TableCell>
              <TableCell>
                <RouterLink
                  v-if="track.artists.length == 1"
                  :to="`/player/artists/${track.artists[0].id}`">
                  {{ track.artists[0].name }}
                </RouterLink>
                <Popover v-else>
                  <PopoverTrigger @click.stop>{{
                    track.artists
                      .slice(0, 2)
                      .map((a) => a.name)
                      .join(', ') + '...'
                  }}</PopoverTrigger>
                  <PopoverContent class="w-fit flex flex-col gap-y-2 p-1">
                    <ul class="text-sm space-y-1">
                      <li
                        class="bg-secondary p-2 px-4 rounded-md hover:bg-secondary/50 cursor-pointer"
                        v-for="artist in track.artists">
                        <RouterLink :to="`/player/artists/${artist.id}`">
                          {{ artist.name }}
                        </RouterLink>
                      </li>
                    </ul>
                  </PopoverContent>
                </Popover>
              </TableCell>
              <TableCell class="text-sm text-muted-foreground">
                <RouterLink
                  @click.stop
                  v-if="track.album.id"
                  :to="`/player/albums/${track.album.id}`">
                  {{ track.album.title }}
                </RouterLink>
                <p v-else>Сингл</p>
              </TableCell>
              <TableCell class="text-right">
                <div class="flex items-center justify-end gap-x-2">
                  <Popover>
                    <PopoverTrigger @click.stop>
                      <Button variant="link"><PlusIcon class="w-5 h-5" /></Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-fit flex flex-col gap-y-2">
                      <h4 class="text-sm font-semibold">Добавление в плейлист</h4>
                      <ul
                        class="text-sm space-y-1"
                        v-if="
                          userStore.myPlaylists.filter(
                            (playlist) => !playlist.tracks.map((t) => t.id).includes(track.id),
                          ).length
                        ">
                        <li
                          class="bg-secondary py-1 px-3 rounded-md hover:bg-secondary/50 cursor-pointer"
                          v-for="playlist in userStore.myPlaylists.filter(
                            (playlist) => !playlist.tracks.map((t) => t.id).includes(track.id),
                          )"
                          :key="playlist.id"
                          @click="addTrackToPlaylist(playlist.id, track)">
                          {{ playlist.title }}
                        </li>
                      </ul>
                      <p class="text-sm" v-else>Создайте плейлист в медиатеке</p>
                    </PopoverContent>
                  </Popover>
                  <Button
                    v-if="userStore.user?.is_subscribed"
                    @click.stop="
                      userStore.likedTracks.includes(track.id)
                        ? userStore.unlikeTrack(track.id)
                        : userStore.likeTrack(track.id)
                    "
                    variant="link"
                    ><Heart
                      :class="userStore.likedTracks.includes(track.id) ? 'fill-red-500' : ''"
                      class="w-5 h-5"
                  /></Button>
                  <span>{{
                    new Date(1000 * track.content.duration)
                      .toISOString()
                      .substr(11, 8)
                      .replace(/^[0:]+/, '')
                  }}</span>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
        <p
          class="mt-20 text-sm text-center text-muted-foreground"
          v-if="!isLoading && searchedTracks.length === 0">
          Ничего не найдено
        </p>
      </TabsContent>
      <TabsContent value="Исполнители">
        <div v-if="!isLoading && searchedArtists.length" class="w-full flex flex-col">
          <RouterLink
            v-for="artist in searchedArtists"
            :key="artist.id"
            :to="`/player/artists/${artist.id}`">
            <div
              class="flex items-center gap-x-4 bg-secondary p-2 px-4 rounded-md hover:bg-secondary/50 cursor-pointer">
              <img
                :src="API_URL + artist.photo_uri"
                :alt="artist.name"
                class="w-16 h-16 object-cover rounded-full" />
              <p>{{ artist.name }}</p>
            </div>
          </RouterLink>
        </div>
        <p
          class="mt-20 text-sm text-center text-muted-foreground"
          v-if="!isLoading && searchedArtists.length === 0">
          Ничего не найдено
        </p>
      </TabsContent>
      <TabsContent value="Альбомы">
        <div v-if="!isLoading && searchedAlbums.length" class="w-full flex flex-col">
          <RouterLink
            v-for="artist in searchedAlbums"
            :key="artist.id as number"
            :to="`/player/artists/${artist.id}`">
            <div
              class="flex items-center gap-x-4 bg-secondary p-2 px-4 rounded-md hover:bg-secondary/50 cursor-pointer">
              <img
                :src="API_URL + artist.cover_uri"
                :alt="artist.title as string"
                class="w-16 h-16 object-cover" />
              <p>{{ artist.title }}</p>
            </div>
          </RouterLink>
        </div>
        <p
          class="mt-20 text-sm text-center text-muted-foreground"
          v-if="!isLoading && searchedAlbums.length === 0">
          Ничего не найдено
        </p>
      </TabsContent>
    </Tabs>
  </div>
</template>

<style scoped></style>
