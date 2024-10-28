<script setup lang="ts">
import { RouterLink } from 'vue-router/auto';
import { Home, Menu, AudioLines } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';

import {
  Music,
  Music2,
  LanguagesIcon,
  User,
  UserPlus,
  PlayCircleIcon,
  LucideDiscAlbum,
} from 'lucide-vue-next';

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
        href: '/admin/tracks',
      },
      {
        title: 'Добавить трек',
        icon: Music2,
        href: '/admin/tracks/add',
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
  {
    title: 'Языки',
    children: [
      {
        title: 'Языки',
        icon: LanguagesIcon,
        href: '/admin/languages',
      },
    ],
  },
  {
    title: 'Исполнители',
    children: [
      {
        title: 'Исполнители',
        icon: User,
        href: '/admin/artists',
      },
      {
        title: 'Добавить исполнителя',
        icon: UserPlus,
        href: '/admin/artists/add',
      },
    ],
  },
  {
    title: 'Плейлисты',
    children: [
      {
        title: 'Плейлисты',
        icon: PlayCircleIcon,
        href: '/admin/playlists',
      },
      {
        title: 'Добавить плейлист',
        icon: PlayCircleIcon,
        href: '/admin/playlists/add',
      },
    ],
  },
  {
    title: 'Альбомы',
    children: [
      {
        title: 'Альбомы',
        icon: LucideDiscAlbum,
        href: '/admin/albums',
      },
      {
        title: 'Добавить альбом',
        icon: LucideDiscAlbum,
        href: '/admin/albums/add',
      },
    ],
  },
];
</script>

<template>
  <header class="flex h-14 items-center gap-4 border-b bg-muted/40 px-4 lg:h-[60px] lg:px-6">
    <Sheet>
      <SheetTrigger as-child>
        <Button variant="outline" size="icon" class="shrink-0 md:hidden">
          <Menu class="h-5 w-5" />
          <span class="sr-only">Toggle navigation menu</span>
        </Button>
      </SheetTrigger>
      <SheetContent side="left" class="flex flex-col">
        <div class="flex items-center justify-center px-4">
          <RouterLink to="/" class="flex items-center gap-2 font-semibold">
            <AudioLines class="h-10 w-10" />
          </RouterLink>
        </div>
        <div class="flex-1">
          <nav class="grid items-start px-2 text-sm font-medium lg:px-4 space-y-4">
            <RouterLink
              to="/admin"
              class="flex items-center gap-3 rounded-lg px-5 py-2 text-muted-foreground transition-all hover:text-primary">
              <Home class="h-4 w-4" />
              Дашборд
            </RouterLink>
            <Accordion
              type="single"
              class="w-full px-3 py-2 space-y-6"
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
      </SheetContent>
    </Sheet>
  </header>
</template>
