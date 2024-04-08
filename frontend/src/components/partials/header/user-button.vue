<script setup lang="ts">
import { Settings, User, ChevronDown } from 'lucide-vue-next';

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
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import LogoutDialog from '@/components/partials/header/logout-dialog.vue';
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger class="flex items-center space-x-2">
      <Avatar>
        <AvatarImage src="https://i.pravatar.cc/300" />
        <AvatarFallback class="bg-emerald-500">
          <User class="text-primary" />
        </AvatarFallback>
      </Avatar>
      <ChevronDown class="w-5 h-5 text-black dark:text-white font-bold" />
    </DropdownMenuTrigger>
    <DropdownMenuContent>
      <DropdownMenuLabel>
        <div className="flex justify-center items-center space-x-2">
          <Avatar>
            <AvatarImage src="https://i.pravatar.cc/300" />
            <AvatarFallback className="bg-emerald-500">
              <User className="text-white" />
            </AvatarFallback>
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
          <User class="mr-2 h-4 w-4" />
          <span>Профиль</span>
        </DropdownMenuItem>
        <DropdownMenuItem>
          <Settings class="mr-2 h-4 w-4" />
          <span>Настройки</span>
        </DropdownMenuItem>
      </DropdownMenuGroup>
      <DropdownMenuSeparator />
      <DropdownMenuItem as-child>
        <LogoutDialog />
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
