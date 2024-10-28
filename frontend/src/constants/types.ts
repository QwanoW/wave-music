export interface Genre {
  id: number;
  name: string;
  cover_uri: string;
  temp_cover?: string;
}

export interface Artist {
  id: number;
  name: string;
  bio: string;
  photo_uri: string;
  temp_photo?: string;
}

export interface Language {
  id: number;
  name: string;
}

interface Content {
  id: number;
  uri: string;
  duration: number;
  contentType: string;
  created_at: string;
}

interface AlbumTrack extends Track {
  order: number;
}

export interface Album {
  id: number | null;
  title: string | null;
  cover_uri: string | null;
  temp_cover?: string | null;
  tracks: AlbumTrack[];
}

export interface Track {
  id: number;
  title: string;
  cover_uri: string;
  temp_cover?: string;
  genre: Genre;
  content: Content;
  language: Language;
  album: Album;
  artists: Artist[];
}

export interface Playlist {
  id: number;
  title: string;
  description?: string;
  cover_uri: string;
  is_private: boolean;
  temp_cover?: string;
  owner: Partial<User>;
  tracks: Track[];
}

export enum UserRole {
  USER = 'USER',
  ADMIN = 'ADMIN',
  MODERATOR = 'MODERATOR',
}

export enum AuthType {
  CREDENTIALS = 'credentials',
  GITHUB = 'github',
  GOOGLE = 'google',
}

export interface User {
  id: number;
  username: string;
  email: string;
  role: UserRole;
  created_at: string;
  auth_type: AuthType;
  is_subscribed: boolean;
  trial_expires_at: string;
}
