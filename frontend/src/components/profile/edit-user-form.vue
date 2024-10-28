<script setup lang="ts">
import { ref } from 'vue';

import { useForm } from 'vee-validate';
import { vAutoAnimate } from '@formkit/auto-animate/vue';
import { editUserSchema } from '@/schemas';

import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import FormError from '@/components/form-error.vue';
import FormSuccess from '@/components/form-success.vue';
import { userStore } from '@/stores/user';
import { AuthType } from '@/constants/types';

const form = useForm({
  validationSchema: editUserSchema,
  initialValues: {
    username: userStore.user?.username,
    email: userStore.user?.email,
  },
});

const errorMessage = ref('');
const successMessage = ref('');

const isLoading = ref(false);
const onSubmit = form.handleSubmit((values) => {
  const isAllPasswordsEmpty = !values.oldPassword && !values.password && !values.confirmPassword;
  const isSomePasswordEmpty = !values.oldPassword || !values.password || !values.confirmPassword;

  if (isSomePasswordEmpty && !isAllPasswordsEmpty) {
    return form.setErrors({
      oldPassword: 'Необходимо заполнить',
      password: 'Необходимо заполнить',
      confirmPassword: 'Необходимо заполнить',
    });
  }

  if (values.password !== values.confirmPassword) {
    return form.setErrors({
      password: 'Пароли не совпадают',
      confirmPassword: 'Пароли не совпадают',
    });
  }

  isLoading.value = true;
  userStore
    .editUser(values)
    .then((resp) => {
      if (resp.error) {
        errorMessage.value = resp.error;
      } else if (resp.success) {
        successMessage.value = resp.success;
      }
    })
    .finally(() => {
      isLoading.value = false;
    });
});
</script>

<template>
  <form class="w-full space-y-6" @submit="onSubmit">
    <FormField v-slot="{ componentField }" name="username">
      <FormItem v-auto-animate>
        <FormLabel>Имя пользователя</FormLabel>
        <FormControl>
          <Input type="text" v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField
      v-if="userStore.user?.auth_type === AuthType.CREDENTIALS"
      v-slot="{ componentField }"
      name="email">
      <FormItem v-auto-animate>
        <FormLabel>Почта</FormLabel>
        <FormControl>
          <Input type="email" v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    <div v-if="userStore.user?.auth_type === AuthType.CREDENTIALS" class="space-y-6">
      <Separator class="my-4" />
      <div>
        <h1 class="text-xl font-medium">Смена пароля</h1>
        <p class="text-sm text-muted-foreground">
          Оставьте поля пустыми, если не собираетесь менять пароль
        </p>
      </div>
      <FormField v-slot="{ componentField }" name="oldPassword">
        <FormItem v-auto-animate>
          <FormLabel>Старый пароль</FormLabel>
          <FormControl>
            <Input type="text" placeholder="******" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="password">
        <FormItem v-auto-animate>
          <FormLabel>Новый пароль</FormLabel>
          <FormControl>
            <Input type="text" placeholder="******" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="confirmPassword">
        <FormItem v-auto-animate>
          <FormLabel>Подтвердите новый пароль</FormLabel>
          <FormControl>
            <Input type="text" placeholder="******" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
    </div>
    <FormError :message="errorMessage" />
    <FormSuccess :message="successMessage" />
    <Button type="submit"> Изменить </Button>
  </form>
</template>
