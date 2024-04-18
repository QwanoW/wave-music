import { createApp } from 'vue';
import { createRouter, createWebHistory, DataLoaderPlugin } from 'vue-router/auto';

import '@/style.css';
import App from '@/App.vue';
import { fetchUserData, userStore } from '@/stores/user';
import { UserRole } from '@/constants/types';

const router = createRouter({
  history: createWebHistory(),
});

const onlyForUnauthorized = ['/auth/login', '/auth/register', '/auth/callback'];
const admin = ['/admin'];

router.beforeEach(async (_, __, next) => {
  if (userStore.isAuthenticated && !userStore.user) {
    await fetchUserData();
  }

  next();
});

router.beforeEach((to, _, next) => {
  if (admin.includes(to.path) && userStore.user?.role !== UserRole.ADMIN) {
    next('/permission-denied');
  }

  if (to.matched.some((route) => route.meta.requiresAuth) && !userStore.isAuthenticated) {
    next('/auth/login');
  }

  if (onlyForUnauthorized.includes(to.path) && userStore.isAuthenticated) {
    next('/player');
  }

  next();
});

createApp(App).use(DataLoaderPlugin, { router }).use(router).mount('#app');
