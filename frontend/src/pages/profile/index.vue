<script setup lang="ts">
import { definePage } from 'vue-router/auto';

import { userStore } from '@/stores/user';
import Header from '@/components/partials/header/index.vue';
import Button from '@/components/ui/button/Button.vue';

definePage({ meta: { requiresAuth: true } });
</script>

<template>
  <div class="min-h-screen h-full flex flex-col">
    <Header />
    <div class="w-full bg-orange-100 flex flex-col p-3 flex-1">
      <div class="w-full max-w-6xl h-full rounded-xl p-6 mx-auto grow bg-white">
        <h1 class="text-3xl font-bold text-amber-700">Профиль</h1>
        <div v-if="userStore.user" class="mt-6 flex flex-col gap-y-4">
          <div class="flex items-center space-x-3">
            <img class="w-16 h-16 rounded-full" src="https://i.pravatar.cc/300" alt="" />
            <div class="flex flex-col">
              <p class="font-bold text-amber-700">{{ userStore.user?.username }}</p>
              <p class="text-gray-400">{{ userStore.user?.email }}</p>
            </div>
          </div>
          <div class="flex space-x-3">
            <p class="font-bold text-amber-700">Роль:</p>
            <p class="text-gray-400">{{ userStore.user?.role }}</p>
          </div>
          <div class="flex space-x-3">
            <p class="font-bold text-amber-700">Исполнитель:</p>
            <p class="text-gray-400">{{ userStore.user?.isArtist ? 'Да' : 'Нет' }}</p>
          </div>
          <div class="flex space-x-3">
            <p class="font-bold text-amber-700">Дата регистрации:</p>
            <p class="text-gray-400">{{ userStore.user?.created_at }}</p>
          </div>
          <Button @click="userStore.logout()" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
