<script setup lang="ts">
import BackButton from '@/components/auth/back-button.vue';

defineProps<{
  headerLabel: string;
  label: string;
  backButtonLabel: string;
  backButtonHref: string;
  reverse?: boolean;
}>();
</script>

<template>
  <div
    class="w-full max-w-6xl mx-auto flex-1 mb-20 bg-white rounded-3xl flex flex-col-reverse"
    :class="reverse ? 'md:flex-row-reverse' : 'md:flex-row'">
    <div
      class="rounded-b-lg md:rounded-b-none py-5 grow md:grow-0 w-full md:w-1/2 lg:w-2/3 bg-orange-300 flex flex-col justify-center items-center"
      :class="reverse ? 'md:rounded-r-3xl' : 'md:rounded-l-3xl'">
      <h3 class="text-3xl font-bold text-white">{{ headerLabel }}</h3>
      <slot />
    </div>

    <div
      class="rounded-t-lg md:rounded-t-none py-5 grow md:grow-0 w-full md:w-1/2 lg:w-1/3 bg-white flex flex-col items-center justify-center space-y-6"
      :class="reverse ? 'md:rounded-l-3xl' : 'md:rounded-r-3xl'">
      <h1 class="text-3xl font-bold text-orange-500">Добро пожаловать!</h1>
      <p class="text-lg text-gray-400 text-center">
        {{ label }}
      </p>
      <BackButton :href="backButtonHref" :label="backButtonLabel" />
    </div>
  </div>
</template>

<style scoped></style>
