<script setup lang="ts">
import api from '@/lib/axios';
import { ref } from 'vue';
import { definePage } from 'vue-router/auto';

definePage({
  meta: {
    requiresAuth: true,
  },
});

const fileInput = ref<HTMLInputElement | null>(null);
const fileName = ref('');

const progress = ref(0);

const handleSubmit = () => {
  if (!fileInput.value?.files || fileInput.value.files.length === 0) {
    alert('Не выбран аудиофайл');
    return;
  }

  api
    .postForm(
      '/upload/song.php',
      {
        name: fileName.value,
        file: fileInput.value.files[0],
      },
      {
        onUploadProgress: (event) => {
          if (event.progress) {
            progress.value = Math.round(event.progress * 100);
          }
        },
      },
    )
    .then((resp) => {
      console.log(resp.data);

      fileInput.value = null;
      fileName.value = '';
    });
};
</script>

<template>
  <h1 class="font-medium text-2xl mb-5 text-center">Тест загрузка</h1>
  <form class="flex flex-col space-y-5 bg-white rounded-md p-5" @submit.prevent="handleSubmit">
    <label for="name">
      <span class="font-semibold">Название</span>
      <input
        v-model="fileName"
        id="name"
        type="text"
        class="block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
    </label>
    <label class="font-semibold"
      >Аудиофайл
      <input
        v-on:change="(e) => (fileInput = e.target as HTMLInputElement)"
        type="file"
        accept=".mp3,.wav,.ogg"
        class="block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
    </label>
    <div v-if="progress > 0" class="w-full bg-gray-200 rounded-full h-10 dark:bg-gray-700">
      <div
        class="bg-amber-500 h-10 rounded-full transition-all"
        :style="{ width: progress + '%' }"></div>
    </div>
    <button
      type="submit"
      class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
      Загрузить
    </button>
  </form>
</template>

<style scoped></style>
