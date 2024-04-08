<script setup lang="ts">
import { LogOut } from 'lucide-vue-next';

import { userStore } from '@/stores/user';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
</script>

<template>
  <AlertDialog>
    <AlertDialogTrigger as-child>
      <div
        class="w-full h-full flex items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground cursor-pointer">
        <LogOut class="mr-2 h-4 w-4" />
        <span>Выйти</span>
      </div>
    </AlertDialogTrigger>
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>Вы уверены?</AlertDialogTitle>
        <AlertDialogDescription>
          Если вы выйдете из своего аккаунта, то не сможете полноценно пользоваться нашим
          музыкальным стриминговым сервисом.
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel>Отмена</AlertDialogCancel>
        <AlertDialogAction @click="userStore.logout()">Выйти</AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>
