<script setup lang="ts">
import { Clock, Play, Heart } from 'lucide-vue-next';
import { useRouter } from 'vue-router/auto';

import { Playlist } from '@/constants/types';
import playlistService from '@/services/playlist.service';
import Equalizer from '@/components/player/equalizer.vue';
import { audioStore } from '@/stores/audio';
import { userStore } from '@/stores/user';
import Button from '@/components/ui/button/Button.vue';
import type { Track } from '@/constants/types';
import { API_URL } from '@/constants/index';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';

const props = defineProps<{
  id: number;
  title: string;
  description?: string;
  type: 'album' | 'playlist';
  isPrivate?: boolean;
  cover: string;
  owner: Playlist['owner'];
  tracks: Track[];
}>();

const router = useRouter();

const normalizeCountForm = (number: number, words_arr: string[]) => {
  number = Math.abs(number);
  if (Number.isInteger(number)) {
    let options = [2, 0, 1, 1, 1, 2];
    return words_arr[
      number % 100 > 4 && number % 100 < 20 ? 2 : options[number % 10 < 5 ? number % 10 : 5]
    ];
  }
  return words_arr[1];
};

const getDuration = (duration: number) => {
  const hours = Math.floor(duration / 3600);
  const minutes = Math.floor(duration / 60);
  const seconds = Math.floor(duration % 60);

  if (hours === 0 && minutes < 10) {
    return `${minutes ? minutes + ' м. ' : ''}${seconds ? seconds + ' с.' : ''}`;
  }

  return `${hours ? hours + ' ч. ' : ''}${minutes ? minutes + ' м. ' : ''}`;
};

const likeMusicList = (id: number) => {
  if (props.type == 'album' && !userStore.likedAlbums.includes(id)) {
    userStore.likeAlbum(id);
  } else if (props.type == 'playlist' && !userStore.likedPlaylists.includes(id)) {
    userStore.likePlaylist(id);
  } else if (props.type == 'album' && userStore.likedAlbums.includes(id)) {
    userStore.unlikeAlbum(id);
  } else if (props.type == 'playlist' && userStore.likedPlaylists.includes(id)) {
    userStore.unlikePlaylist(id);
  }
};

const deletePlaylist = (id: number) => {
  playlistService.delete(id).then(() => {
    router.push('/player/lib/playlists');
  });
};
</script>

<template>
  <main class="w-full flex flex-col gap-y-8">
    <div
      class="w-full h-72 bg-cover bg-center"
      :style="{
        backgroundImage: `url(${API_URL + cover})`,
      }">
      <div
        class="flex items-center py-2 md:py-10 px-6 md:px-16 w-full h-full backdrop-blur-2xl backdrop-brightness-[0.65]">
        <div class="h-full w-full max-w-5xl m-auto flex gap-x-4">
          <img
            class="h-1/2 aspect-square md:h-full rounded-md shadow-md"
            :src="API_URL + cover"
            alt="" />
          <div class="flex flex-col justify-between">
            <div class="flex flex-col gap-y-1">
              <p class="text-secondary text-sm">{{ type == 'album' ? 'Альбом' : 'Плейлист' }}</p>
              <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white">{{ title }}</h1>
              <p v-if="description" class="text-secondary text-sm">{{ description }}</p>
              <p v-if="isPrivate !== undefined" class="text-secondary text-sm">
                {{ isPrivate ? 'Закрытый плейлист' : 'Открытый плейлист' }}
              </p>
              <div class="flex gap-x-1">
                <p v-if="owner.id" class="font-medium text-secondary text-sm">
                  Автор: {{ owner.username }}
                </p>
                <p class="font-medium text-secondary text-sm">.</p>
                <p class="font-medium text-secondary text-sm">
                  {{ tracks.length }}
                  {{ normalizeCountForm(tracks.length, ['трек', 'трека', 'треков']) }}
                </p>
                <p class="font-medium text-secondary text-sm">.</p>
                <p class="font-medium text-secondary text-sm">
                  {{
                    getDuration(
                      tracks.reduce((acc, track) => acc + (track.content.duration || 0), 0),
                    )
                  }}
                </p>
              </div>
            </div>
            <div class="flex gap-x-2">
              <Button
                @click="audioStore.setPlaylist(tracks, tracks[0].id)"
                class="py-5 px-8 rounded-2xl scale-">
                Слушать
              </Button>
              <Button
                :variant="
                  props.type == 'album'
                    ? userStore.likedAlbums.includes(id)
                      ? 'default'
                      : 'secondary'
                    : userStore.likedPlaylists.includes(id)
                    ? 'default'
                    : 'secondary'
                "
                @click="likeMusicList(id)"
                class="py-5 px-8 rounded-2xl scale-">
                {{
                  (type == 'album' && userStore.likedAlbums.includes(id)) ||
                  (type === 'playlist' && userStore.likedPlaylists.includes(id))
                    ? 'Удалить из медиатеки'
                    : 'Добавить в медиатеку'
                }}
              </Button>
              <Button
                v-if="type == 'playlist' && owner"
                variant="destructive"
                @click="deletePlaylist(id)"
                class="py-5 px-8 rounded-2xl scale-">
                Удалить плейлист
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Table class="w-full max-w-5xl m-auto">
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
                : audioStore.setPlaylist(tracks, track.id)
          "
          v-for="(track, index) in tracks"
          :key="track.id">
          <TableCell>
            <div class="flex justify-center items-center">
              <Equalizer v-if="audioStore.currentAudio?.id === track.id && audioStore.isPlaying" />
              <div v-else>
                <span
                  :class="audioStore.currentAudio?.id === track.id ? 'text-orange-600' : ''"
                  class="group-hover:hidden"
                  >{{ index + 1 }}</span
                >
                <Play class="hidden group-hover:block text-orange-500 fill-orange-500 w-5 h-5" />
              </div>
            </div>
          </TableCell>
          <TableCell class="flex items-center gap-x-3">
            <img class="w-16 h-16 rounded" :src="API_URL + track.cover_uri" alt="" />
            <p :class="audioStore.currentAudio?.id === track.id ? 'text-orange-600' : ''">
              {{ track.title }}
            </p>
          </TableCell>
          <TableCell>{{ track.artists.map((artist) => artist.name).join(', ') }}</TableCell>
          <TableCell class="text-sm text-muted-foreground">{{
            track.album.title ? track.album.title : 'Сингл'
          }}</TableCell>
          <TableCell class="text-right">
            <div class="flex items-center justify-end gap-x-2">
              <Button
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
  </main>
</template>
