import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';

export const loginSchema = toTypedSchema(
  z.object({
    email: z
      .string({ required_error: 'Обязательное поле' })
      .email({ message: 'Некорректная почта' }),
    password: z
      .string({ required_error: 'Обязательное поле' })
      .min(1, { message: 'Пароль обязателен' }),
  }),
);

export const registerSchema = toTypedSchema(
  z
    .object({
      username: z
        .string({ required_error: 'Обязательное поле' })
        .min(3, { message: 'Минимальная длина 3 символа' })
        .max(20, { message: 'Максимальная длина 20 символов' }),
      email: z
        .string({ required_error: 'Обязательное поле' })
        .email({ message: 'Некорректная почта' }),
      password: z
        .string({ required_error: 'Обязательное поле' })
        .min(6, { message: 'Минимальная длина 6 символов' }),
      confirmPassword: z
        .string({ required_error: 'Обязательное поле' })
        .min(6, { message: 'Минимальная длина 6 символов' }),
    })
    .partial(),
);

const MAX_FILE_SIZE = 500000;
const ACCEPTED_IMAGE_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

const genreSchemaZod = z.object({
  name: z
    .string({ required_error: 'Обязательное поле' })
    .min(2, { message: 'Минимальная длина 2 символа' })
    .max(30, { message: 'Максимальная длина 30 символов' }),
  cover: z
    .custom<File>((val) => {
      return val instanceof File;
    }, 'Обязательное поле')
    .refine((file) => file.size <= MAX_FILE_SIZE, `Максимальный размер файла - 5 MB.`)
    .refine(
      (file) => ACCEPTED_IMAGE_TYPES.includes(file.type),
      'Принимаются только изображения формата jpeg, jpg, png, webp.',
    ),
});

export const genreSchema = toTypedSchema(genreSchemaZod);

export const editGenreSchema = toTypedSchema(
  z.object({
    name: z
      .string({ required_error: 'Обязательное поле' })
      .min(2, { message: 'Минимальная длина 2 символа' })
      .max(30, { message: 'Максимальная длина 30 символов' }),
    cover: z
      .custom<File>((val) => {
        return val instanceof File;
      }, 'Обязательное поле')
      .refine((file) => file.size <= MAX_FILE_SIZE, `Максимальный размер файла - 5 MB.`)
      .refine(
        (file) => ACCEPTED_IMAGE_TYPES.includes(file.type),
        'Принимаются только изображения формата jpeg, jpg, png, webp.',
      )
      .optional(),
  }),
);
