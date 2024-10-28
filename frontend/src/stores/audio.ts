import { reactive } from 'vue';
import { API_URL } from '@/constants/index';
import { Track } from '@/constants/types';

interface AudioStore {
  isPlaying: boolean;
  playerInstance?: any;
  currentAudio?: Track;
  playlist: Track[];

  init: () => void;
  pause: () => void;
  onChange: () => void;
  setPlaylist: (playlist: Track[], id?: number) => void;
  setCurrentAudio: (audio: Track) => void;
}

export const audioStore = reactive<AudioStore>({
  isPlaying: false,
  playerInstance: undefined,
  currentAudio: undefined,
  playlist: [],

  init() {
    // @ts-ignore
    this.playerInstance = new Playerjs({
      id: 'player',
      title: '...',
      poster: this.playlist.length > 0 && API_URL + this.playlist[0].cover_uri,
      file: this.playlist.length > 0 && API_URL + this.playlist[0].content.uri,
    });
  },

  pause() {
    this.playerInstance.api('pause');
  },

  onChange() {
    if (this.playerInstance) {
      const id = this.playerInstance.api('playlist_id');
      if (id && this.currentAudio?.id !== id) {
        this.currentAudio = this.playlist.find((audio) => audio.id === id);
        this.playerInstance.api('poster', API_URL + this.currentAudio?.cover_uri);
      }
    }
  },

  setPlaylist(playlist: Track[], id?: number) {
    if (!id) {
      id = playlist[0].id;
    }

    this.playlist = playlist;
    this.playerInstance.api(
      'play',
      playlist.map((audio) => {
        return {
          id: audio.id,
          title: audio.title,
          file: API_URL + audio.content.uri,
        };
      }),
    );

    this.playerInstance.api('poster', API_URL + playlist[0].cover_uri);
    this.playerInstance.api('find', id);
    this.playerInstance.api('play');
  },

  setCurrentAudio(audio: Track) {
    this.currentAudio = audio;
    this.playerInstance.api('play', API_URL + audio.content.uri);
    this.playerInstance.api('poster', API_URL + audio.cover_uri);
    this.playerInstance.api('title', audio.title);
  },
});
