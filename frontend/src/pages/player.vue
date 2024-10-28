<script setup lang="ts">
import { RouterView, definePage } from 'vue-router/auto';

import { ScrollArea } from '@/components/ui/scroll-area';
import { ResizableHandle, ResizablePanel, ResizablePanelGroup } from '@/components/ui/resizable';
import SideBar from '@/components/player/side-bar.vue';
import Player from '@/components/player/pjs/index.vue';
import { ref } from 'vue';

const panelRef = ref<InstanceType<typeof ResizablePanel>>();

definePage({
  meta: {
    requiresAuth: true,
  },
});

const windowSize = ref(window.innerWidth);

window.addEventListener('resize', () => {
  windowSize.value = window.innerWidth;
});
</script>

<template>
  <div class="max-h-screen h-screen flex flex-col space-y-1">
    <ResizablePanelGroup
      :direction="windowSize >= 640 ? 'horizontal' : 'vertical'"
      class="grow"
      auto-save-id="player">
      <ResizablePanel
        :order="1"
        class="min-w-[100px] bg-slate-100"
        collapsible
        :collapsed-size="5"
        :min-size="20"
        :max-size="50"
        :default-size="25"
        ref="panelRef"
        v-if="windowSize >= 640"
        ><SideBar :isCollapsed="panelRef?.isCollapsed || false"
      /></ResizablePanel>
      <ResizableHandle v-if="windowSize >= 640" />
      <ResizablePanel :order="2">
        <ScrollArea class="h-full outline-none ring-0 relative">
          <RouterView v-slot="{ Component }">
            <Transition name="fade" mode="out-in">
              <component :is="Component" />
            </Transition>
          </RouterView>
        </ScrollArea>
      </ResizablePanel>
    </ResizablePanelGroup>
    <div class="w-full bg-slate-100">
      <Player />
      <div>
        <SideBar
          v-if="windowSize < 640"
          class="h-full"
          :isCollapsed="panelRef?.isCollapsed || false" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition-property: opacity;
  transition-duration: 0.25s;
}

.fade-enter-active {
  transition-delay: 0.25s;
}

.fade-enter,
.fade-leave-active {
  opacity: 0;
}
</style>
