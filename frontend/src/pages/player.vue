<script setup lang="ts">
import { RouterView, definePage } from 'vue-router/auto';
// import { useRouter } from 'vue-router/auto';

// import Button from '@/components/ui/button/Button.vue';
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

// const router = useRouter();
</script>

<template>
  <div class="max-h-screen h-screen flex flex-col space-y-1">
    <ResizablePanelGroup auto-save-id="player" class="grow" direction="horizontal">
      <ResizablePanel
        class="min-w-[100px] bg-slate-100"
        collapsible
        :collapsed-size="5"
        :min-size="20"
        :max-size="50"
        :default-size="25"
        ref="panelRef"
        ><SideBar :isCollapsed="panelRef?.isCollapsed || false"
      /></ResizablePanel>
      <ResizableHandle />
      <ResizablePanel>
        <ScrollArea class="h-full outline-none ring-0 relative">
          <!-- <div class="flex gap-x-2 absolute top-2 left-2 z-10">
            <Button @click="router.back()" variant="outline">Назад</Button>
            <Button @click="router.forward()" variant="outline">Вперед</Button>
          </div> -->
          <RouterView v-slot="{ Component }">
            <Transition name="fade" mode="out-in">
              <component :is="Component" />
            </Transition>
          </RouterView>
        </ScrollArea>
      </ResizablePanel>
    </ResizablePanelGroup>
    <div class="w-full bg-slate-100 p-2">
      <Player />
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
