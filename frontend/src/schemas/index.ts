import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';

const loginSchemaZod = z.object({
  email: z.string({ required_error: 'Обязательное поле' }).email({ message: 'Некорректная почта' }),
  password: z
    .string({ required_error: 'Обязательное поле' })
    .min(1, { message: 'Пароль обязателен' }),
});
export const loginSchema = toTypedSchema(loginSchemaZod);
export type loginSchemaType = z.infer<typeof loginSchemaZod>;

const registerSchemaZod = z.object({
  username: z
    .string({ required_error: 'Обязательное поле' })
    .min(3, { message: 'Минимальная длина 3 символа' })
    .max(20, { message: 'Максимальная длина 20 символов' }),
  email: z.string({ required_error: 'Обязательное поле' }).email({ message: 'Некорректная почта' }),
  password: z
    .string({ required_error: 'Обязательное поле' })
    .min(6, { message: 'Минимальная длина 6 символов' }),
  confirmPassword: z
    .string({ required_error: 'Обязательное поле' })
    .min(6, { message: 'Минимальная длина 6 символов' }),
});
export const registerSchema = toTypedSchema(registerSchemaZod);
export type registerSchemaType = z.infer<typeof registerSchemaZod>;

const MAX_IMAGE_SIZE = 500000;
const ACCEPTED_IMAGE_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

const genreSchemaZod = z.object({
  name: z
    .string({ required_error: 'Обязательное поле' })
    .min(2, { message: 'Минимальная длина 2 символа' })
    .max(30, { message: 'Максимальная длина 30 символов' }),
  cover: z
    .custom<File>((val) => val instanceof File, 'Обязательное поле')
    .refine((file) => file.size <= MAX_IMAGE_SIZE, `Максимальный размер файла - 5 MB.`)
    .refine(
      (file) => ACCEPTED_IMAGE_TYPES.includes(file.type),
      'Принимаются только изображения формата jpeg, jpg, png, webp.',
    ),
});
export const genreSchema = toTypedSchema(genreSchemaZod);
export type genreSchemaType = z.infer<typeof genreSchemaZod>;

const editGenreSchemaZod = z.object({
  id: z.number(),
  name: z
    .string({ required_error: 'Обязательное поле' })
    .min(2, { message: 'Минимальная длина 2 символа' })
    .max(30, { message: 'Максимальная длина 30 символов' }),
  cover: z
    .custom<File>((val) => val instanceof File, 'Обязательное поле')
    .refine((file) => file.size <= MAX_IMAGE_SIZE, `Максимальный размер файла - 5 MB.`)
    .refine(
      (file) => ACCEPTED_IMAGE_TYPES.includes(file.type),
      'Принимаются только изображения формата jpeg, jpg, png, webp.',
    )
    .optional(),
});
export const editGenreSchema = toTypedSchema(editGenreSchemaZod);
export type editGenreSchemaType = z.infer<typeof editGenreSchemaZod>;

const artistSchemaZod = z.object({
  name: z
    .string({ required_error: 'Обязательное поле' })
    .min(2, { message: 'Минимальная длина 2 символа' })
    .max(30, { message: 'Максимальная длина 30 символов' }),
  bio: z
    .string({ required_error: 'Обязательное поле' })
    .min(10, { message: 'Минимальная длина 10 символов' })
    .max(500, { message: 'Максимальная длина 500 символов' }),
  photo: z
    .custom<File>((val) => val instanceof File, 'Обязательное поле')
    .refine((file) => file.size <= MAX_IMAGE_SIZE, `Максимальный размер файла - 5 MB.`)
    .refine(
      (file) => ACCEPTED_IMAGE_TYPES.includes(file.type),
      'Принимаются только изображения формата jpeg, jpg, png, webp.',
    ),
});
export const artistSchema = toTypedSchema(artistSchemaZod);
export type artistSchemaType = z.infer<typeof artistSchemaZod>;

const editArtistSchemaZod = z.object({
  id: z.number(),
  name: z
    .string({ required_error: 'Обязательное поле' })
    .min(2, { message: 'Минимальная длина 2 символа' })
    .max(30, { message: 'Максимальная длина 30 символов' }),
  bio: z
    .string({ required_error: 'Обязательное поле' })
    .min(10, { message: 'Минимальная длина 10 символов' })
    .max(500, { message: 'Максимальная длина 500 символов' }),
  photo: z
    .custom<File>((val) => val instanceof File, 'Обязательное поле')
    .refine((file) => file.size <= MAX_IMAGE_SIZE, `Максимальный размер файла - 5 MB.`)
    .refine(
      (file) => ACCEPTED_IMAGE_TYPES.includes(file.type),
      'Принимаются только изображения формата jpeg, jpg, png, webp.',
    )
    .optional(),
});
export const editArtistSchema = toTypedSchema(editArtistSchemaZod);
export type editArtistSchemaType = z.infer<typeof editArtistSchemaZod>;

const MAX_AUDIO_SIZE = 10 * 1024 * 1024;
const ACCEPTED_AUDIO_TYPES = ['audio/mpeg', 'audio/mp3', 'audio/wav'];

const trackSchemaZod = z.object({
  title: z
    .string({ required_error: 'Обязательное поле' })
    .min(2, { message: 'Минимальная длина 2 символа' })
    .max(30, { message: 'Максимальная длина 30 символов' }),
  cover: z
    .custom<File>((val) => val instanceof File, 'Обязательное поле')
    .refine((file) => file.size <= MAX_IMAGE_SIZE, `Максимальный размер файла - 5 MB.`)
    .refine(
      (file) => ACCEPTED_IMAGE_TYPES.includes(file.type),
      'Принимаются только изображения формата jpeg, jpg, png, webp.',
    ),
  audio: z
    .custom<File>((val) => val instanceof File, 'Обязательное поле')
    .refine((file) => file.size <= MAX_AUDIO_SIZE, `Максимальный размер файла - 5 MB.`)
    .refine(
      (file) => ACCEPTED_AUDIO_TYPES.includes(file.type),
      'Принимаются только аудио файлы формата mpeg, mp3, wav.',
    ),
  genre_id: z.string({ required_error: 'Обязательное поле' }),
  language_id: z.string({ required_error: 'Обязательное поле' }),
  artists: z.array(z.number({ required_error: 'Обязательное поле' }), {
    required_error: 'Выберите минимум 1 исполнителя',
  }),
});
export const trackSchema = toTypedSchema(trackSchemaZod);
export type trackSchemaType = z.infer<typeof trackSchemaZod>;

const editTrackSchemaZod = z.object({
  id: z.number(),
  title: z
    .string({ required_error: 'Обязательное поле' })
    .min(2, { message: 'Минимальная длина 2 символа' })
    .max(30, { message: 'Максимальная длина 30 символов' }),
  cover: z
    .custom<File>((val) => val instanceof File, 'Обязательное поле')
    .refine((file) => file.size <= MAX_IMAGE_SIZE, `Максимальный размер файла - 5 MB.`)
    .refine(
      (file) => ACCEPTED_IMAGE_TYPES.includes(file.type),
      'Принимаются только изображения формата jpeg, jpg, png, webp.',
    )
    .optional(),
  audio: z
    .custom<File>((val) => val instanceof File, 'Обязательное поле')
    .refine((file) => file.size <= MAX_AUDIO_SIZE, `Максимальный размер файла - 5 MB.`)
    .refine(
      (file) => ACCEPTED_AUDIO_TYPES.includes(file.type),
      'Принимаются только аудио файлы формата mpeg, mp3, wav.',
    )
    .optional(),
  genre_id: z.string({ required_error: 'Обязательное поле' }),
  language_id: z.string({ required_error: 'Обязательное поле' }),
  artists: z.array(z.number({ required_error: 'Обязательное поле' }), {
    required_error: 'Выберите минимум 1 исполнителя',
  }),
});
export const editTrackSchema = toTypedSchema(editTrackSchemaZod);
export type editTrackSchemaType = z.infer<typeof editTrackSchemaZod>;

const playlistSchemaZod = z.object({
  title: z
    .string({ required_error: 'Обязательное поле' })
    .min(2, { message: 'Минимальная длина 2 символа' })
    .max(30, { message: 'Максимальная длина 30 символов' }),
  description: z.string().optional(),
  is_private: z.boolean().default(false),
  user_id: z.string().optional(),
  tracks: z
    .array(z.number({ required_error: 'Обязательное поле' }), {
      required_error: 'Выберите минимум 1 трек',
    })
    .min(1, { message: 'Выберите минимум 1 трек' }),
  cover: z
    .custom<File>((val) => val instanceof File, 'Обязательное поле')
    .refine((file) => file.size <= MAX_IMAGE_SIZE, `Максимальный размер файла - 5 MB.`)
    .refine(
      (file) => ACCEPTED_IMAGE_TYPES.includes(file.type),
      'Принимаются только изображения формата jpeg, jpg, png, webp.',
    ),
});
export const playlistSchema = toTypedSchema(playlistSchemaZod);
export type playlistSchemaType = z.infer<typeof playlistSchemaZod>;

const editPlaylistSchemaZod = z.object({
  id: z.number(),
  title: z
    .string({ required_error: 'Обязательное поле' })
    .min(2, { message: 'Минимальная длина 2 символа' })
    .max(30, { message: 'Максимальная длина 30 символов' }),
  description: z.string().optional(),
  is_private: z.boolean().default(false),
  user_id: z.string().optional(),
  tracks: z
    .array(z.number({ required_error: 'Обязательное поле' }), {
      required_error: 'Выберите минимум 1 трек',
    })
    .min(1, { message: 'Выберите минимум 1 трек' }),
  cover: z
    .custom<File>((val) => val instanceof File, 'Обязательное поле')
    .refine((file) => file.size <= MAX_IMAGE_SIZE, `Максимальный размер файла - 5 MB.`)
    .refine(
      (file) => ACCEPTED_IMAGE_TYPES.includes(file.type),
      'Принимаются только изображения формата jpeg, jpg, png, webp.',
    )
    .optional(),
});
export const editPlaylistSchema = toTypedSchema(editPlaylistSchemaZod);
export type editPlaylistSchemaType = z.infer<typeof editPlaylistSchemaZod>;

const albumSchemaZod = z.object({
  title: z
    .string({ required_error: 'Обязательное поле' })
    .min(2, { message: 'Минимальная длина 2 символа' })
    .max(30, { message: 'Максимальная длина 30 символов' }),
  cover: z
    .custom<File>((val) => val instanceof File, 'Обязательное поле')
    .refine((file) => file.size <= MAX_IMAGE_SIZE, `Максимальный размер файла - 5 MB.`)
    .refine(
      (file) => ACCEPTED_IMAGE_TYPES.includes(file.type),
      'Принимаются только изображения формата jpeg, jpg, png, webp.',
    ),
  tracks: z
    .array(
      z.object({
        id: z.number({ required_error: 'Обязательное поле' }),
        order: z.number({ required_error: 'Обязательное поле' }),
      }),
      {
        required_error: 'Перенесите минимум 1 трек',
      },
    )
    .min(1, { message: 'Перенесите минимум 1 трек' }),
});
export const albumSchema = toTypedSchema(albumSchemaZod);
export type albumSchemaType = z.infer<typeof albumSchemaZod>;

const editAlbumSchemaZod = z.object({
  id: z.number(),
  title: z
    .string({ required_error: 'Обязательное поле' })
    .min(2, { message: 'Минимальная длина 2 символа' })
    .max(30, { message: 'Максимальная длина 30 символов' }),
  cover: z
    .custom<File>((val) => val instanceof File, 'Обязательное поле')
    .refine((file) => file.size <= MAX_IMAGE_SIZE, `Максимальный размер файла - 5 MB.`)
    .refine(
      (file) => ACCEPTED_IMAGE_TYPES.includes(file.type),
      'Принимаются только изображения формата jpeg, jpg, png, webp.',
    )
    .optional(),
  tracks: z
    .array(
      z.object({
        id: z.number({ required_error: 'Обязательное поле' }),
        order: z.number({ required_error: 'Обязательное поле' }),
      }),
      {
        required_error: 'Перенесите минимум 1 трек',
      },
    )
    .min(1, { message: 'Перенесите минимум 1 трек' }),
});

export const editAlbumSchema = toTypedSchema(editAlbumSchemaZod);
export type editAlbumSchemaType = z.infer<typeof editAlbumSchemaZod>;

const addUserPlaylistSchemaZod = z.object({
  title: z.string().min(2).max(50),
  description: z.string().optional(),
  is_private: z.boolean().default(false),
});

export const addUserPlaylistSchema = toTypedSchema(addUserPlaylistSchemaZod);
export type addUserPlaylistSchemaType = z.infer<typeof addUserPlaylistSchemaZod>;

const editUserSchemaZod = z.object({
  username: z
    .string({ required_error: 'Обязательное поле' })
    .min(3, { message: 'Минимальная длина 3 символа' })
    .max(20, { message: 'Максимальная длина 20 символов' }),
  email: z.string({ required_error: 'Обязательное поле' }).email({ message: 'Некорректная почта' }),
  oldPassword: z.string({ required_error: 'Обязательное поле' }).optional(),
  password: z
    .string({ required_error: 'Обязательное поле' })
    .min(6, { message: 'Минимальная длина 6 символов' })
    .optional(),
  confirmPassword: z
    .string({ required_error: 'Обязательное поле' })
    .min(6, { message: 'Минимальная длина 6 символов' })
    .optional(),
});

export const editUserSchema = toTypedSchema(editUserSchemaZod);
export type editUserSchemaType = z.infer<typeof editUserSchemaZod>;
