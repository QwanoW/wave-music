<script setup lang="ts">
import Header from '@/components/partials/header/index.vue';
</script>

<template>
  <div class="min-h-screen h-full flex flex-col">
    <Header />
    <div class="w-full bg-orange-100 flex flex-col px-3 flex-1">
      <RouterView v-slot="{ Component }" >
        <Transition name="fade" mode="out-in">
          <component :is="Component" />
        </Transition>
      </RouterView>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition-property: opacity;
  transition-duration: .25s;
}

.fade-enter-active {
  transition-delay: .25s;
}

.fade-enter, .fade-leave-active {
  opacity: 0
}
</style>
