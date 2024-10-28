<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import EditButton from '@/components/admin/artist/edit-button.vue';
import { API_URL } from '@/constants/index';
import { Artist } from '@/constants/types';

const props = defineProps<Artist>();
</script>

<template>
  <Card>
    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
      <CardTitle class="text-lg font-medium">{{ name }}</CardTitle>
    </CardHeader>
    <CardContent class="space-y-2">
      <img
        :src="temp_photo ? temp_photo : API_URL + photo_uri"
        class="rounded-lg w-full object-cover aspect-square" />
      <p class="text-sm text-muted-foreground truncate">{{ bio }}</p>
      <EditButton v-bind="props" />
    </CardContent>
  </Card>
</template>
