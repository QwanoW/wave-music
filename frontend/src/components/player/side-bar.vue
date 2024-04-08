<script setup lang="ts">
import { Home, Search, LibraryBig, Settings, ArrowLeft } from 'lucide-vue-next';
import { RouterLink } from 'vue-router/auto';

const navItems = [
  {
    label: 'Обзор',
    href: '',
    icon: Home,
  },
  {
    label: 'Поиск',
    href: '/search',
    icon: Search,
  },
  {
    label: 'Библиотека',
    href: '/lib',
    icon: LibraryBig,
  },
];
</script>

<template>
  <div class="p-5 h-full flex flex-col justify-between bg-orange-300 rounded-sm">
    <div class="flex flex-col gap-y-10">
      <h1 class="text-center text-2xl font-bold text-white relative">
        <RouterLink
          to="/"
          class="bg-amber-600/30 hover:bg-amber-600 text-amber-600 hover:text-white transition-colors cursor-pointer p-2 rounded-lg absolute left-0 top-[50%] translate-y-[-50%]">
          <ArrowLeft :size="32" :stroke-width="2" />
        </RouterLink>
        Vue Music
      </h1>
      <ul class="flex flex-col gap-y-2">
        <RouterLink v-for="item in navItems" :to="'/player' + item.href">
          <li
            class="flex gap-x-4 p-4 hover:text-amber-600 rounded-lg transition-colors"
            :class="
              ($route.path.includes(item.href) && item.href !== '') ||
              '/player' + item.href === $route.path
                ? 'bg-orange-900/15 font-medium text-amber-600'
                : 'text-white'
            ">
            <component
              :is="item.icon"
              :class="item.href === $route.path ? 'text-amber-600' : 'text-white'" />
            {{ item.label }}
          </li>
        </RouterLink>
      </ul>
    </div>
    <div
      class="self-end p-2 rounded-lg hover:bg-orange-900/15 text-white hover:text-amber-600 cursor-pointer transition-colors">
      <RouterLink to="/player/settings">
        <Settings />
      </RouterLink>
    </div>
  </div>
</template>

<style scoped></style>
