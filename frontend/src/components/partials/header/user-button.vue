<script setup lang="ts">
import { User, ChevronDown, UserCircle2 } from 'lucide-vue-next';

import { userStore } from '@/stores/user';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Avatar } from '@/components/ui/avatar';
import LogoutDialog from '@/components/partials/header/logout-dialog.vue';
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger class="flex items-center space-x-2">
      <Avatar class="bg-transparent">
        <UserCircle2
          stroke-width="1"
          class="w-16 h-16 rounded-full text-orange-500 fill-transparent" />
      </Avatar>
      <ChevronDown class="w-5 h-5 text-black dark:text-white font-bold" />
    </DropdownMenuTrigger>
    <DropdownMenuContent>
      <DropdownMenuLabel>
        <div className="flex justify-center items-center space-x-2">
          <Avatar>
            <UserCircle2
              stroke-width="1"
              class="w-16 h-16 rounded-full text-orange-500 fill-transparent" />
          </Avatar>
          <div>
            <p>{{ userStore.user?.username }}</p>
            <p className="text-xs text-slate-400">{{ userStore.user?.email }}</p>
          </div>
        </div>
      </DropdownMenuLabel>
      <DropdownMenuSeparator />
      <DropdownMenuGroup>
        <DropdownMenuItem>
          <RouterLink class="flex items-center" to="/profile">
            <User class="mr-2 h-4 w-4" />
            <span>Профиль</span>
          </RouterLink>
        </DropdownMenuItem>
      </DropdownMenuGroup>
      <DropdownMenuSeparator />
      <DropdownMenuItem as-child>
        <LogoutDialog />
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
