<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import EditButton from '@/components/admin/genre/edit-button.vue';
import { API_URL } from '@/constants/index';
import { Genre } from '@/constants/types';

const props = defineProps<Genre>();
</script>

<template>
  <Card>
    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
      <CardTitle class="text-sm font-medium">{{ name }}</CardTitle>
    </CardHeader>
    <CardContent class="space-y-2">
      <img
        :src="temp_cover ? temp_cover : API_URL + cover_uri"
        class="rounded-lg w-full object-cover aspect-square" />
      <EditButton v-bind="props" />
    </CardContent>
  </Card>
</template>
