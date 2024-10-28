import type { Playlist } from '@/constants/types';
import type { playlistSchemaType } from '@/schemas';
import api from '@/lib/axios';

const playlistService = {
  async getById(id: number) {
    return (await api.get<Playlist[]>(`/playlists/?id=${id}`)).data[0];
  },

  async getByOwner(userId: number) {
    return (await api.get<Playlist[]>(`/playlists/?owner=${userId}`)).data;
  },

  async getAll() {
    return (await api.get<Playlist[]>('/playlists/')).data;
  },

  async create(data: playlistSchemaType) {
    return await api.postForm('/playlists/', data);
  },

  async delete(id: number) {
    return await api.delete(`/playlists/delete_playlist.php?id=${id}`);
  },

  async update(data: playlistSchemaType) {
    return await api.putForm(`/playlists/add_playlist.php`, data);
  },
};

export default playlistService;
