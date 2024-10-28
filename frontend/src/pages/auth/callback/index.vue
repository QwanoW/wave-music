<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router/auto';

import CardWrapper from '@/components/auth/card-wrapper.vue';
import { userStore } from '@/stores/user';

const route = useRoute();
const router = useRouter();

const queryParams = route.hash
  .replace('#', '')
  .split('&')
  .reduce((acc: { [key: string]: string }, item) => {
    const [key, value] = item.split('=');
    acc[key] = value;
    return acc;
  }, {});

if (queryParams.jwt) {
  userStore.authorize(queryParams.jwt);
  router.push('/');
}
</script>

<template>
  <CardWrapper
    :header-label="queryParams.error ? 'Что-то пошло не так' : 'Аутентификация'"
    label="Вернуться к странице входа"
    back-button-href="/auth/login"
    back-button-label="Вернуться">
    <div
      class="h-fit p-5 rounded-lg w-[80%] sm:w-1/2 md:w-2/3 space-y-6 mt-6 flex flex-col items-center bg-white">
      <div
        class="w-full max-w-md flex flex-col items-center justify-center rounded-lg bg-destructive/15 px-4 py-5"
        v-if="queryParams.error">
        <p class="text-destructive text-lg font-semibold">
          {{ queryParams.error }}
        </p>
      </div>
    </div>
  </CardWrapper>
</template>

<style scoped></style>
