<script setup lang="ts">
import { RouterLink } from 'vue-router/auto';
import { Home, AudioLines, Music, Music2 } from 'lucide-vue-next';

import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion';

const accordionNavigation = [
  {
    title: 'Треки',
    children: [
      {
        title: 'Треки',
        icon: Music,
        href: '/admin/songs',
      },
      {
        title: 'Добавить трек',
        icon: Music2,
        href: '/admin/songs/add',
      },
    ],
  },
  {
    title: 'Жанры',
    children: [
      {
        title: 'Жанры',
        icon: Music,
        href: '/admin/genres',
      },
      {
        title: 'Добавить жанр',
        icon: Music2,
        href: '/admin/genres/add',
      },
    ],
  },
];
</script>

<template>
  <div class="hidden border-r bg-muted/40 md:block">
    <div class="flex h-full max-h-screen flex-col gap-2">
      <div class="flex h-14 items-center border-b px-4 lg:h-[60px] lg:px-8">
        <RouterLink to="/" class="flex items-center gap-2 font-semibold">
          <AudioLines class="h-6 w-6" />
          <span class="">Wave Music</span>
        </RouterLink>
      </div>
      <div class="flex-1">
        <nav class="grid items-start px-2 text-sm font-medium lg:px-4">
          <RouterLink
            to="/admin"
            class="flex items-center gap-3 rounded-lg px-5 py-2 text-muted-foreground transition-all hover:text-primary">
            <Home class="h-4 w-4" />
            Дашборд
          </RouterLink>
          <Accordion
            type="single"
            class="w-full px-3 py-2 space-y-4"
            collapsible
            default-value="item-1">
            <AccordionItem
              class="border-b-0"
              v-for="item in accordionNavigation"
              :key="item.title"
              :value="item.title">
              <AccordionTrigger
                class="outline-none bg-secondary rounded-lg px-2 hover:no-underline border-t-0">
                <div
                  class="px-3 w-full flex items-center gap-3 text-muted-foreground transition-all">
                  {{ item.title }}
                </div>
              </AccordionTrigger>
              <AccordionContent>
                <RouterLink
                  v-for="child in item.children"
                  :to="child.href"
                  class="flex items-center gap-3 rounded-lg px-5 py-2 text-muted-foreground transition-all hover:text-primary">
                  <component :is="child.icon" class="h-4 w-4" />
                  {{ child.title }}
                </RouterLink>
              </AccordionContent>
            </AccordionItem>
          </Accordion>
        </nav>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
