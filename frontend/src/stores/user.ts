import { reactive } from 'vue';
import { AxiosError } from 'axios';

import { hasToken, removeToken, setToken } from '@/lib/token';
import api from '@/lib/axios';
import { User, Playlist } from '@/constants/types';
import { editUserSchemaType } from '@/schemas';

interface UserStore {
  isAuthenticated: boolean;
  user?: User;

  myPlaylists: Playlist[];

  likedTracks: number[];
  likedPlaylists: number[];
  likedAlbums: number[];
  likedArtists: number[];

  likeTrack(id: number): Promise<void>;
  unlikeTrack(id: number): Promise<void>;

  likePlaylist(id: number): Promise<void>;
  unlikePlaylist(id: number): Promise<void>;

  likeAlbum(id: number): Promise<void>;
  unlikeAlbum(id: number): Promise<void>;

  likeArtist(id: number): Promise<void>;
  unlikeArtist(id: number): Promise<void>;

  authorize(token: string): void;
  refresh(): Promise<void>;

  subscribe(duration: number): Promise<void>;
  unsubscribe(): Promise<void>;

  register(
    username: string,
    email: string,
    password: string,
  ): Promise<{ success?: string; error?: string }>;

  login(email: string, password: string): Promise<{ success?: string; error?: string }>;

  editUser(values: editUserSchemaType): Promise<{ success?: string; error?: string }>;

  reset(): void;

  logout(): void;
}

export const userStore = reactive<UserStore>({
  isAuthenticated: hasToken(),
  user: undefined,

  likedTracks: [],
  likedPlaylists: [],
  likedAlbums: [],
  likedArtists: [],

  myPlaylists: [],

  async likeTrack(id: number) {
    this.likedTracks.push(id);
    try {
      await api.get(`/user/likes/track.php?id=${id}&action=like`);
    } catch (error) {
      this.likedTracks = this.likedTracks.filter((likedId) => likedId !== id);
      console.log(error);
    }
  },

  async unlikeTrack(id: number) {
    this.likedTracks = this.likedTracks.filter((likedId) => likedId !== id);
    try {
      await api.get(`/user/likes/track.php?id=${id}&action=unlike`);
    } catch (error) {
      console.log(error);
      this.likedTracks.push(id);
    }
  },

  async likePlaylist(id: number) {
    this.likedPlaylists = this.likedPlaylists.filter((likedId) => likedId !== id);
    try {
      this.likedPlaylists.push(id);
      await api.get(`/user/likes/playlist.php?id=${id}&action=like`);
    } catch (error) {
      this.likedPlaylists = this.likedPlaylists.filter((likedId) => likedId !== id);
      console.log(error);
    }
  },

  async unlikePlaylist(id: number) {
    this.likedPlaylists = this.likedPlaylists.filter((likedId) => likedId !== id);
    try {
      await api.get(`/user/likes/playlist.php?id=${id}&action=unlike`);
    } catch (error) {
      console.log(error);
      this.likedPlaylists.push(id);
    }
  },

  async likeAlbum(id: number) {
    this.likedAlbums = this.likedAlbums.filter((likedId) => likedId !== id);
    try {
      this.likedAlbums.push(id);
      await api.get(`/user/likes/album.php?id=${id}&action=like`);
    } catch (error) {
      this.likedAlbums = this.likedAlbums.filter((likedId) => likedId !== id);
      console.log(error);
    }
  },

  async unlikeAlbum(id: number) {
    this.likedAlbums = this.likedAlbums.filter((likedId) => likedId !== id);
    try {
      await api.get(`/user/likes/album.php?id=${id}&action=unlike`);
    } catch (error) {
      console.log(error);
      this.likedAlbums.push(id);
    }
  },

  async likeArtist(id: number) {
    this.likedArtists = this.likedArtists.filter((likedId) => likedId !== id);
    try {
      this.likedArtists.push(id);
      await api.get(`/user/likes/artist.php?id=${id}&action=like`);
    } catch (error) {
      this.likedArtists = this.likedArtists.filter((likedId) => likedId !== id);
      console.log(error);
    }
  },

  async unlikeArtist(id: number) {
    this.likedArtists = this.likedArtists.filter((likedId) => likedId !== id);
    try {
      await api.get(`/user/likes/artist.php?id=${id}&action=unlike`);
    } catch (error) {
      console.log(error);
      this.likedArtists.push(id);
    }
  },

  reset() {
    this.isAuthenticated = false;
    this.user = undefined;
  },

  authorize(token: string) {
    setToken(token);
    this.isAuthenticated = true;
  },

  async refresh() {
    try {
      const response = await api.get('/user/refresh_token.php');
      this.authorize(response.data.jwt);
    } catch (error) {
      console.log(error);
    }
  },

  async subscribe(duration: number) {
    try {
      await api.postForm(`/user/premium/purchase_premium.php`, {
        duration,
      });

      await userStore.refresh();

      await fetchUserData();
    } catch (error) {
      console.log(error);
      throw error;
    }
  },

  async unsubscribe() {
    try {
      await api.get('/user/premium/cancel_premium.php');

      await userStore.refresh();

      await fetchUserData();
    } catch (error) {
      console.log(error);
      throw error;
    }
  },

  async login(email: string, password: string) {
    try {
      const response = await api.postForm('/auth/login.php', {
        email,
        password,
      });

      const { message, jwt } = response.data;

      this.authorize(jwt);

      return { success: message };
    } catch (error) {
      console.log(error);

      if (error instanceof AxiosError && error.response?.data.message) {
        return { error: error.response.data.message };
      }

      return { error: 'Произошла ошибка, попробуйте позже' };
    }
  },

  async register(username: string, email: string, password: string) {
    try {
      const response = await api.postForm('/auth/register.php', {
        username,
        email,
        password,
      });

      return { success: response.data.message };
    } catch (error) {
      console.log(error);

      if (error instanceof AxiosError && error.response?.data.message) {
        return { error: error.response.data.message };
      }

      return { error: 'Произошла ошибка, попробуйте позже' };
    }
  },

  async editUser(values: editUserSchemaType) {
    const { confirmPassword, ...rest } = values;

    try {
      const response = await api.postForm('/user/edit_user.php', rest);

      await this.refresh();

      await fetchUserData();

      return { success: response.data.message };
    } catch (error) {
      console.log(error);
      if (error instanceof AxiosError && error.response?.data.message) {
        return { error: error.response.data.message };
      }

      return { error: 'Не удалось обновить профиль' };
    }
  },

  logout() {
    this.reset();
    removeToken();

    window.location.href = '/auth/login';
  },
});

export const fetchUserData = async () => {
  try {
    let response = await api.post('/user/me.php');

    if (response.status !== 200) {
      userStore.reset();
      return;
    }

    const { user, liked_tracks, liked_playlists, liked_albums, liked_artists } = response.data;

    userStore.isAuthenticated = true;
    userStore.user = user;

    userStore.likedTracks = liked_tracks.map((track: any) => track.track_id);
    userStore.likedPlaylists = liked_playlists.map((playlist: any) => playlist.playlist_id);
    userStore.likedAlbums = liked_albums.map((album: any) => album.album_id);
    userStore.likedArtists = liked_artists.map((artist: any) => artist.artist_id);

    // get my playlists
    if (userStore.user?.id) {
      response = await api.get<Playlist[]>(`/playlists/?owner=${userStore.user.id}`);
      userStore.myPlaylists = response.data;
    }
  } catch (error) {
    console.log(error);
  }
};
