<script setup lang="ts">
import { Home, Search, LibraryBig, AudioLines } from 'lucide-vue-next';
import { ref } from 'vue';
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

defineProps<{ isCollapsed: boolean }>();

const windowSize = ref(window.innerWidth);

window.addEventListener('resize', () => {
  windowSize.value = window.innerWidth;
});
</script>

<template>
  <aside class="p-5 h-full flex flex-col justify-between bg-orange-300 rounded-sm">
    <div class="flex flex-row sm:flex-col gap-y-10">
      <div class="hidden sm:block text-center text-2xl font-bold text-white relative">
        <RouterLink
          class="w-full h-full flex items-center justify-center"
          v-if="isCollapsed"
          to="/">
          <AudioLines class="w-10 h-10" stroke-width="2" />
        </RouterLink>
        <h1 v-else class="text-center text-3xl font-bold text-white">
          <RouterLink to="/">Wave Music</RouterLink>
        </h1>
      </div>
      <ul class="w-full flex flex-row justify-between sm:justify-start sm:flex-col gap-y-2">
        <RouterLink class="w-full"  v-for="item in navItems" :to="'/player' + item.href">
          <li
            class="flex justify-center sm:justify-normal gap-x-4 p-4 hover:text-amber-600 rounded-lg transition-colors"
            :class="
              ($route.path.includes(item.href) && item.href !== '') ||
              '/player' + item.href === $route.path
                ? 'bg-orange-900/15 font-medium text-amber-600'
                : 'text-white'
            ">
            <component
              :is="item.icon"
              :class="[
                item.href === $route.path ? 'text-amber-600' : 'text-white',
                isCollapsed ? 'w-full' : '',
              ]" />
            {{ isCollapsed || windowSize < 640 ? '' : item.label }}
          </li>
        </RouterLink>
      </ul>
    </div>
    <!-- <div
      :class="isCollapsed ? 'w-full' : ''"
      class="hidden sm:block  p-4 self-end rounded-lg hover:bg-orange-900/15 text-white hover:text-amber-600 cursor-pointer transition-colors">
      <RouterLink class="w-full h-full" to="/player/settings">
        <Settings :class="isCollapsed ? 'w-full' : ''" />
      </RouterLink>
    </div> -->
  </aside>
</template>

<style scoped></style>
