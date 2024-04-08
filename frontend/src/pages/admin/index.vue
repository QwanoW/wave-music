<script setup lang="ts">
import { definePage } from 'vue-router/auto';
import { User, Disc3, ListMusic, Laugh } from 'lucide-vue-next';

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { ScrollArea } from '@/components/ui/scroll-area';
import Wrapper from '@/components/admin/wrapper.vue';

definePage({
  meta: {
    requiresAuth: true,
    role: 'ADMIN',
  },
});
</script>

<template>
  <Wrapper title="Дашборд">
    <div class="grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium"> Исполнители </CardTitle>
          <User class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">+342</div>
          <p class="text-xs text-muted-foreground">+20.1% c прошлого месяца</p>
        </CardContent>
      </Card>
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium"> Альбомы </CardTitle>
          <Disc3 class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">+100</div>
          <p class="text-xs text-muted-foreground">+180.1% from last month</p>
        </CardContent>
      </Card>
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium"> Плейлисты </CardTitle>
          <ListMusic class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">+12,234</div>
          <p class="text-xs text-muted-foreground">+19% from last month</p>
        </CardContent>
      </Card>
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium"> Слушатели </CardTitle>
          <Laugh class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">+573</div>
          <p class="text-xs text-muted-foreground">+201 since last hour</p>
        </CardContent>
      </Card>
    </div>
    <Separator class="my-4" />
    <div class="grid gap-4 sm:grid-cols-1 md:gap-8 lg:grid-cols-3">
      <Card>
        <CardHeader class="flex flex-row items-center space-x-2 pb-2">
          <CardTitle class="text-sm font-medium"> Исполнители </CardTitle>
          <User class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent class="flex flex-col gap-2">
          <ScrollArea class="h-96 w-full">
            <div class="flex items-center" v-for="i in 10" :key="i">
              <img class="h-16 w-16 rounded-full" src="https://i.pravatar.cc/300" alt="" />
              <p class="pl-3 text-sm font-medium">Тестовый исполнитель</p>
            </div>
          </ScrollArea>
        </CardContent>
      </Card>
      <Card>
        <CardHeader class="flex flex-row items-center space-x-2 pb-2">
          <CardTitle class="text-sm font-medium"> Альбомы </CardTitle>
          <Disc3 class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent class="flex flex-col gap-2">
          <ScrollArea class="h-96 w-full">
            <div class="flex items-center" v-for="i in 15" :key="i">
              <img class="h-16 w-16 rounded-full" src="https://i.pravatar.cc/300" alt="" />
              <p class="pl-3 text-sm font-medium">Тестовый альбом</p>
            </div>
          </ScrollArea>
        </CardContent>
      </Card>
      <Card>
        <CardHeader class="flex flex-row items-center space-x-2 pb-2">
          <CardTitle class="text-sm font-medium"> Плейлисты </CardTitle>
          <ListMusic class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent class="flex flex-col gap-2">
          <ScrollArea class="h-96 w-full">
            <div class="flex items-center" v-for="i in 20" :key="i">
              <img class="h-16 w-16 rounded-full" src="https://i.pravatar.cc/300" alt="" />
              <p class="pl-3 text-sm font-medium">Тестовый плейлист</p>
            </div>
          </ScrollArea>
        </CardContent>
      </Card>
    </div>
  </Wrapper>
</template>
