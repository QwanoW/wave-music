<script setup lang="ts">
import { Play } from 'lucide-vue-next';
import { useRouter } from 'vue-router/auto';

import { API_URL } from '@/constants';
import { normalizeCountForm } from '@/lib/utils';

const router = useRouter();

defineProps<{
  title: string;
  cover: string;
  description?: string;
  rounded?: boolean;
  number?: number;
  href?: string;
  onClick?: () => void;
}>();
</script>

<template>
  <section class="w-full h-full flex flex-col gap-y-1 group transition-all">
    <div @click="href && router.push(href)" class="w-full h-full relative">
      <img
        :class="rounded ? 'rounded-full' : 'rounded-lg'"
        class="w-full object-cover aspect-square duration-200 group-hover:brightness-[0.25]"
        :src="API_URL + cover"
        alt="" />
      <button
        @click.stop="onClick && onClick()"
        class="brightness-100 z-10 group p-4 rounded-full bg-amber-500 opacity-0 text-amber-700 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 group-hover:opacity-100 duration-200">
        <Play class="w-10 h-10 group-hover:hover:fill-amber-700" />
      </button>
    </div>
    <div :class="rounded ? 'items-center' : ''" class="w-full flex flex-col justify-center">
      <h1 class="text-md sm:text-xl break-words font-medium">{{ title }}</h1>
      <p v-if="description" class="text-sm font-normal line-clamp-3">{{ description }}</p>
      <p v-if="number" class="text-sm font-normal">
        {{ number }} {{ normalizeCountForm(number, ['трек', 'трека', 'треков']) }}
      </p>
    </div>
  </section>
</template>
