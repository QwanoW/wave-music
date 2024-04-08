<script setup lang="ts">
import { userStore } from '@/stores/user';
import { UserRole } from '@/constants/index';
import UserButton from '@/components/partials/header/user-button.vue';
</script>

<template>
  <div class="w-full bg-orange-100">
    <div
      class="max-w-6xl w-full px-6 mx-auto py-8 flex flex-col sm:flex-row justify-between items-center">
      <RouterLink to="/">
        <div class="flex items-center space-x-2">
          <!-- <img class="w-10" src="../assets/gopher-logo.png" alt="" /> -->
          <span class="font-bold text-2xl text-amber-800">Vue Music</span>
        </div>
      </RouterLink>

      <div>
        <ul class="flex space-x-3 items-center">
          <li class="font-medium text-amber-600 hover:text-amber-900 cursor-pointer transition-all">
            <RouterLink to="/blog">Блог</RouterLink>
          </li>
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
      </div>
    </div>
  </div>
</template>

<style scoped></style>
