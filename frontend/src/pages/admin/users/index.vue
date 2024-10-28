<script setup lang="ts">
import { onMounted, ref } from 'vue';

import api from '@/lib/axios';
import Wrapper from '@/components/admin/wrapper.vue';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import Button from '@/components/ui/button/Button.vue';
import { toast } from '@/components/ui/toast/use-toast';
import { User } from '@/constants/types';
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
import { userStore } from '@/stores/user';

const users = ref<User[]>([]);

const isLoadingDelete = ref(false);
const onDelete = (id: number): void => {
  isLoadingDelete.value = true;
  api
    .delete(`/users/delete_user.php?id=${id}`)
    .then(() => {
      const index = users.value.findIndex((user) => user.id === id);
      if (index !== -1) {
        users.value.splice(index, 1);
      }
      toast({
        title: 'Успешно',
        description: 'Пользователь был удалён',
      });
    })
    .catch((error) => {
      toast({
        title: 'Ошибка',
        description: error.response.data.message,
      });
    })
    .finally(() => {
      isLoadingDelete.value = false;
    });
};

const isLoading = ref(false);
onMounted(async () => {
  users.value = (await api.get<User[]>('/users/')).data.filter(
    (user) => user.id !== userStore.user?.id,
  );
  isLoading.value = false;
});
</script>

<template>
  <Wrapper :is-loading="isLoading" :is-empty="!users.length" title="Пользователи">
    <Table>
      <TableCaption>Список пользователей</TableCaption>
      <TableHeader>
        <TableRow>
          <TableHead class="w-[100px]"> ID </TableHead>
          <TableHead> Имя </TableHead>
          <TableHead> Email </TableHead>
          <TableHead> Дата регистрации </TableHead>
          <TableHead class="text-right"> Роль </TableHead>
          <TableHead class="text-right"> Премиум </TableHead>
          <TableHead class="text-right"> Действия </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-if="users" v-for="user in users" :key="user.id">
          <TableCell class="font-medium">
            {{ user.id }}
          </TableCell>
          <TableCell>{{ user.username }}</TableCell>
          <TableCell>{{ user.email }}</TableCell>
          <TableCell>{{ user.created_at }}</TableCell>
          <TableCell class="text-right">{{ user.role }}</TableCell>
          <TableCell class="text-right">{{ user.is_subscribed ? 'Да' : 'Нет' }}</TableCell>
          <TableCell class="flex flex-col sm:flex-row justify-end items-end sm:gap-x-2 gap-y-2">
            <AlertDialog>
              <AlertDialogTrigger as-child>
                <Button variant="outline">Удалить</Button>
              </AlertDialogTrigger>
              <AlertDialogContent>
                <AlertDialogHeader>
                  <AlertDialogTitle
                    >Вы уверены что хотите удалить этого пользователя?</AlertDialogTitle
                  >
                  <AlertDialogDescription>
                    Пользователь <strong>{{ user.username }}</strong> будет удалён безвозвратно.
                  </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                  <AlertDialogCancel>Отмена</AlertDialogCancel>
                  <AlertDialogAction :disabled="isLoadingDelete" @click="onDelete(user.id)"
                    >Продолжить</AlertDialogAction
                  >
                </AlertDialogFooter>
              </AlertDialogContent>
            </AlertDialog>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </Wrapper>
</template>
