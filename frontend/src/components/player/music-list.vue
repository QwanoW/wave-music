<script setup lang="ts">
import { Clock, Play, Heart, PlusIcon } from 'lucide-vue-next';
import { useRouter, RouterLink } from 'vue-router/auto';

import api from '@/lib/axios';
import { Playlist, Track } from '@/constants/types';
import playlistService from '@/services/playlist.service';
import Equalizer from '@/components/player/equalizer.vue';
import { audioStore } from '@/stores/audio';
import { userStore } from '@/stores/user';
import Button from '@/components/ui/button/Button.vue';
import { API_URL } from '@/constants/index';
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
import { normalizeCountForm } from '@/lib/utils';

const props = defineProps<{
  id: number;
  title: string;
  type: 'album' | 'playlist' | 'artist' | 'likedTracks';
  cover: string;
  tracks: Track[];

  description?: string;
  isPrivate?: boolean;
  owner?: Playlist['owner'];
  artists?: string[];
}>();

const router = useRouter();

const getDuration = (duration: number) => {
  const hours = Math.floor(duration / 3600);
  const minutes = Math.floor(duration / 60);
  const seconds = Math.floor(duration % 60);

  if (hours === 0 && minutes < 10) {
    return `${minutes ? minutes + ' мин. ' : ''}${seconds ? seconds + ' с.' : ''}`;
  }

  return `${hours ? hours + ' ч. ' : ''}${minutes ? minutes + ' мин. ' : ''}`;
};

const likeMusicList = (id: number) => {
  if (!userStore.user?.is_subscribed) {
    toast({
      title: 'Ошибка',
      description: 'Это действие доступно только для пользователей с премиум подпиской',
      variant: 'destructive',
    });
    return;
  }

  if (props.type == 'album' && !userStore.likedAlbums.includes(id)) {
    userStore.likeAlbum(id);
  } else if (props.type == 'playlist' && !userStore.likedPlaylists.includes(id)) {
    userStore.likePlaylist(id);
  } else if (props.type == 'artist' && !userStore.likedArtists.includes(id)) {
    userStore.likeArtist(id);
  } else if (props.type == 'album' && userStore.likedAlbums.includes(id)) {
    userStore.unlikeAlbum(id);
  } else if (props.type == 'playlist' && userStore.likedPlaylists.includes(id)) {
    userStore.unlikePlaylist(id);
  } else if (props.type == 'artist' && userStore.likedArtists.includes(id)) {
    userStore.unlikeArtist(id);
  }
};

const deletePlaylist = (id: number) => {
  playlistService.delete(id).then(() => {
    userStore.myPlaylists = userStore.myPlaylists.filter((p) => p.id != id);
    router.push('/player/lib/playlists');
  });
};

const addTrackToPlaylist = (id: number, track: Track) => {
  if (!userStore.user?.is_subscribed) {
    toast({
      title: 'Ошибка',
      description: 'Это действие доступно только для пользователей с премиум подпиской',
      variant: 'destructive',
    });
    return;
  }

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
</script>

<template>
  <main class="w-full flex flex-col gap-y-8">
    <div
      class="w-full  h-full sm:h-72 bg-cover bg-center"
      :style="{
        backgroundImage: `url(${API_URL + cover})`,
      }">
      <div
        class="flex items-center py-2 md:py-10 px-6 md:px-16 w-full h-full backdrop-blur-2xl backdrop-brightness-[0.65]">
        <div class="h-full w-full max-w-5xl m-auto flex flex-col sm:flex-row gap-x-4 gap-y-2">
          <img
            :class="type == 'artist' ? 'rounded-full' : 'rounded-md'"
            class="h-1/2 object-cover aspect-square md:h-full shadow-md"
            :src="API_URL + cover"
            alt="" />
          <div class="flex flex-col gap-y-2 justify-between">
            <div class="flex flex-col gap-y-1">
              <p class="text-secondary text-sm">
                {{ type == 'album' ? 'Альбом' : type == 'artist' ? 'Исполнитель' : 'Плейлист' }}
              </p>
              <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white">{{ title }}</h1>
              <p v-if="description" class="text-secondary text-sm">{{ description }}</p>
              <p
                v-if="type == 'playlist' && isPrivate !== undefined"
                class="text-secondary text-sm">
                {{ isPrivate ? 'Закрытый плейлист' : 'Открытый плейлист' }}
              </p>
              <p v-if="artists && artists.length" class="font-medium text-secondary text-sm">
                Исполнители:
                {{
                  artists.length > 2
                    ? artists.slice(0, 2).join(', ') + ' и др.'
                    : artists.join(', ')
                }}
              </p>
              <div class="flex gap-x-1">
                <p v-if="owner && owner.username" class="font-medium text-secondary text-sm">
                  Автор: {{ owner.username }}
                </p>
                <p v-if="owner && owner.username" class="font-medium text-secondary text-sm">.</p>
                <p class="font-medium text-secondary text-sm">
                  {{ tracks.length }}
                  {{ normalizeCountForm(tracks.length, ['трек', 'трека', 'треков']) }}
                </p>
                <p v-if="tracks.length" class="font-medium text-secondary text-sm">.</p>
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
                v-if="type !== 'likedTracks'"
                :variant="
                  props.type == 'album'
                    ? userStore.likedAlbums.includes(id)
                      ? 'default'
                      : 'secondary'
                    : props.type == 'artist'
                    ? userStore.likedArtists.includes(id)
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
                  (type === 'playlist' && userStore.likedPlaylists.includes(id)) ||
                  (type === 'artist' && userStore.likedArtists.includes(id))
                    ? 'Удалить из медиатеки'
                    : 'Добавить в медиатеку'
                }}
              </Button>
              <Button
                v-if="type == 'playlist' && userStore.user && userStore.user.id == owner?.id"
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
          <TableHead class="hidden sm:table-cell text-sm font-normal uppercase">Исполнитель</TableHead>
          <TableHead class="hidden sm:table-cell text-sm font-normal uppercase">Альбом</TableHead>
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
          v-for="(track, index) in type === 'likedTracks'
            ? tracks.filter((t) => userStore.likedTracks.includes(t.id))
            : tracks"
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
            <img class="w-16 h-16 rounded object-cover" :src="API_URL + track.cover_uri" alt="" />
            <p :class="audioStore.currentAudio?.id === track.id ? 'text-orange-600' : ''">
              {{ track.title }}
            </p>
          </TableCell>
          <TableCell class="hidden sm:table-cell">
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
          <TableCell class="hidden sm:table-cell text-sm text-muted-foreground">
            <RouterLink @click.stop v-if="track.album.id" :to="`/player/albums/${track.album.id}`">
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
                    class="bg-secondary p-2 px-4 rounded-md hover:bg-secondary/50 cursor-pointer"
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
  </main>
</template>
