<script setup lang="ts">
import { RouterView, definePage } from 'vue-router/auto';

import SideBar from '@/components/admin/side-bar.vue';
import Header from '@/components/admin/header.vue';

definePage({
  meta: {
    requiresAuth: true,
    role: 'ADMIN',
  },
});
</script>

<template>
  <div class="grid min-h-screen w-full md:grid-cols-[220px_1fr] lg:grid-cols-[280px_1fr]">
    <SideBar />
    <div class="flex flex-col">
      <Header />
      <main class="flex flex-1 flex-col gap-4 p-4 lg:gap-6 lg:p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>
