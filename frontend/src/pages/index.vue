<script setup lang="ts">
import { RouterLink } from 'vue-router/auto';

import Header from '@/components/partials/header/index.vue';
import Footer from '@/components/partials/Footer.vue';
</script>

<template>
  <div class="min-h-screen h-full flex flex-col">
    <Header />
    <div class="w-full h-full bg-white flex-1 pt-8 my-10 lg:my-0">
      <div
        class="max-w-6xl w-full px-6 mx-auto flex md:flex-row flex-col-reverse items-center justify-between">
        <div class="space-y-4">
          <Transition name="slogan" appear>
            <h2
              class="text-5xl font-bold leading-snug relative transition-all duration-1000 delay-100">
              <img
                class="absolute w-36 top-[-30px] left-[-30px]"
                src="../assets/slogan-ring.png"
                alt="" />
              <span class="text-orange-600">Волна</span> за волной, <br />
              <span class="text-orange-600">В ритме</span> вашей души
            </h2>
          </Transition>
          <Transition name="description" appear>
            <p class="text-base text-gray-400 max-w-sm transition-all duration-1000 delay-200">
              Добро пожаловать в <span class="font-semibold text-orange-600">Wave Music</span>, вашу
              платформу для стриминга и открытия потрясающей музыки.
            </p>
          </Transition>
          <Transition name="buttons" appear>
            <RouterLink
              class="w-fit text-white font-semibold px-6 py-4 rounded-lg flex items-center space-x-2 bg-orange-500 hover:bg-orange-600 transition-all duration-1000 delay-300"
              to="/auth/register">
              <img class="w-3" src="../assets/note-icon.png" alt="" />
              <p>Начать сейчас</p>
            </RouterLink>
          </Transition>
        </div>
        <Transition name="hero" appear>
          <img
            class="w-1/2 min-w-[200px] transition-opacity delay-300 duration-1000"
            src="../assets/hero.png"
            alt="" />
        </Transition>
      </div>
    </div>
    <Footer />
  </div>
</template>

<style lang="postcss" scoped>
.slogan-enter-from,
.buttons-enter-from,
.description-enter-from {
  @apply opacity-0 translate-x-[-200px];
}

.hero-enter-from {
  @apply opacity-0;
}
</style>
