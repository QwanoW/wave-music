import { defineConfig } from 'vite';
import VueRouter from 'unplugin-vue-router/vite';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'url';

import tailwind from 'tailwindcss';
import autoprefixer from 'autoprefixer';

export default defineConfig({
  css: {
    postcss: {
      plugins: [tailwind(), autoprefixer()],
    },
  },
  plugins: [VueRouter(), vue()],
  resolve: {
    alias: [{ find: '@', replacement: fileURLToPath(new URL('./src', import.meta.url)) }],
  },
});
