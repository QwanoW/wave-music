import { type ClassValue, clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export const getURL = (file: File) => {
  if (file) {
    const url = URL.createObjectURL(file);
    return url;
  }
};
