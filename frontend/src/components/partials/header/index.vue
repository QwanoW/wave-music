<script setup lang="ts">
import { AudioLines } from 'lucide-vue-next';

import { userStore } from '@/stores/user';
import { UserRole } from '@/constants/types';
import UserButton from '@/components/partials/header/user-button.vue';
</script>

<template>
  <header class="w-full bg-orange-100">
    <div
      class="max-w-6xl w-full px-6 mx-auto py-8 flex flex-col sm:flex-row justify-between items-center">
      <RouterLink to="/">
        <div class="flex items-center space-x-2">
          <AudioLines class="w-8 h-8 text-orange-500" />
          <span class="font-bold text-2xl text-amber-800">Wave Music</span>
        </div>
      </RouterLink>

      <nav>
        <ul class="flex space-x-3 items-center">
          <li class="font-medium text-amber-600 hover:text-amber-900 cursor-pointer transition-all">
            <RouterLink to="/premium">Премиум</RouterLink>
          </li>
          <li class="border-l border-orange-800 pl-2 border-1 h-5"></li>
          <div v-if="!userStore.isAuthenticated" class="flex space-x-3 items-center">
            <li
              class="font-medium text-amber-600 hover:text-amber-900 cursor-pointer transition-all">
              <RouterLink to="/auth/login">Вход</RouterLink>
            </li>
            <li
              class="font-medium text-amber-600 hover:text-amber-900 cursor-pointer transition-all">
              <RouterLink to="/auth/register">Регистрация</RouterLink>
            </li>
          </div>
          <div v-else-if="userStore.user" class="flex space-x-3 items-center">
            <li
              v-if="userStore.user.role === UserRole.ADMIN"
              class="font-medium text-amber-600 hover:text-amber-900 cursor-pointer transition-all">
              <RouterLink to="/admin">Админ-панель</RouterLink>
            </li>
            <li
              class="font-medium text-amber-600 hover:text-amber-900 cursor-pointer transition-all">
              <RouterLink to="/player">Плеер</RouterLink>
            </li>
            <li>
              <UserButton />
            </li>
          </div>
        </ul>
      </nav>
    </div>
  </header>
</template>

<style scoped></style>
