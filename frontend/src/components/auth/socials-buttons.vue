<script setup lang="ts">
import { ref } from 'vue';

import { API_URL } from '@/constants';
import GithubButton from '@/components/auth/github-button.vue';
import GoogleButton from '@/components/auth/google-button.vue';

const isLoadingGithub = ref(false);
const isLoadingGoogle = ref(false);

const onClickProvider = (provider: 'github' | 'google') => {
  if (provider === 'github') {
    isLoadingGithub.value = true;
  } else {
    isLoadingGoogle.value = true;
  }

  window.location.href = `${API_URL}/auth/oauth/${provider}.php`;
};
</script>

<template>
  <div class="w-full justify-between flex space-x-4">
    <GithubButton :isLoading="isLoadingGithub" @click="onClickProvider('github')" class="w-full" />
    <GoogleButton :isLoading="isLoadingGoogle" class="w-full" />
  </div>
</template>
