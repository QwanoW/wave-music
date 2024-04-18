<script setup lang="ts">
import {
  Dialog,
  DialogScrollContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import Button from '@/components/ui/button/Button.vue';

defineProps<{
  triggerTitle: string;
  dialogTitle: string;
  dialogDescription: string;
}>();
</script>

<template>
  <Dialog>
    <DialogTrigger class="w-full">
      <Button class="w-full" variant="outline">{{ triggerTitle }}</Button>
    </DialogTrigger>
    <DialogScrollContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>{{ dialogTitle }}</DialogTitle>
        <DialogDescription>{{ dialogDescription }} </DialogDescription>
      </DialogHeader>
      <slot />
    </DialogScrollContent>
  </Dialog>
</template>
