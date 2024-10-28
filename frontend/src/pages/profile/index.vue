<script setup lang="ts">
import { definePage } from 'vue-router/auto';
import { UserCircle2 } from 'lucide-vue-next';
import { ref } from 'vue';

import { userStore } from '@/stores/user';
import Header from '@/components/partials/header/index.vue';
import Button from '@/components/ui/button/Button.vue';
import { toast } from '@/components/ui/toast/use-toast';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import EditUserForm from '@/components/profile/edit-user-form.vue';

definePage({ meta: { requiresAuth: true } });

const isLoading = ref(false);
const unsubscribe = async () => {
  isLoading.value = true;
  try {
    await userStore.unsubscribe();

    toast({
      title: 'Успешно',
      description: 'Подписка отменена',
    });
  } catch (error) {
    toast({
      title: 'Не удалось отменить подписку',
      description: 'Что-то пошло не так',
    });
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen h-full flex flex-col">
    <Header />
    <div class="w-full bg-orange-100 flex flex-col p-3 flex-1">
      <div class="w-full max-w-6xl h-full rounded-xl p-6 mx-auto grow bg-white">
        <Tabs default-value="info" class="w-full md:w-2/3 m-auto">
          <TabsList class="grid w-full grid-cols-2 mb-10">
            <TabsTrigger value="info"> Общая информация </TabsTrigger>
            <TabsTrigger value="change"> Изменение данных </TabsTrigger>
          </TabsList>
          <TabsContent value="info"
            ><h1 class="text-3xl text-center font-bold text-amber-700">Профиль</h1>
            <div v-if="userStore.user" class="mt-6 flex flex-col gap-y-4">
              <div class="flex items-center space-x-3">
                <UserCircle2 stroke-width="1" class="w-16 h-16 rounded-full text-amber-700" />
                <div class="flex flex-col">
                  <p class="font-bold text-amber-700">{{ userStore.user?.username }}</p>
                  <p class="text-gray-400">{{ userStore.user?.email }}</p>
                </div>
              </div>
              <div class="flex space-x-3">
                <p class="font-bold text-amber-700">Роль:</p>
                <p class="text-gray-400">{{ userStore.user?.role }}</p>
              </div>
              <div class="flex space-x-3">
                <p class="font-bold text-amber-700">Дата регистрации:</p>
                <p class="text-gray-400">{{ userStore.user?.created_at }}</p>
              </div>
              <div v-if="userStore.user.is_subscribed" class="flex space-x-3">
                <p class="font-bold text-amber-700">Подписка оформлена:</p>
                <p class="text-gray-400">
                  {{ userStore.user.is_subscribed ? 'Да' : 'Нет' }}
                </p>
              </div>
              <div v-if="userStore.user.trial_expires_at" class="flex space-x-3">
                <p class="font-bold text-amber-700">Дата окончания пробного периода:</p>
                <p class="text-gray-400">
                  {{ userStore.user.trial_expires_at }}
                </p>
              </div>
              <Button
                class="py-6"
                variant="destructive"
                v-if="userStore.user.is_subscribed"
                :disabled="isLoading"
                @click="unsubscribe()"
                >Отменить подписку</Button
              >
              <Button class="py-6" @click="userStore.logout()">Выйти</Button>
            </div></TabsContent
          >
          <TabsContent value="change">
            <EditUserForm />
          </TabsContent>
        </Tabs>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
