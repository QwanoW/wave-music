<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from 'vee-validate';
import { vAutoAnimate } from '@formkit/auto-animate/vue';

import { userStore } from '@/stores/user';
import FormError from '@/components/form-error.vue';
import FormSuccess from '@/components/form-success.vue';
import CardWrapper from '@/components/auth/card-wrapper.vue';
import SocialsButtons from '@/components/auth/socials-buttons.vue';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { registerSchema } from '@/schemas/index';
import Button from '@/components/ui/button/Button.vue';

const { handleSubmit } = useForm({
  validationSchema: registerSchema,
});

const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const onSubmit = handleSubmit((values) => {
  const typedValues = values as {
    username: string;
    email: string;
    password: string;
    confirmPassword: string;
  };

  if (typedValues.password !== typedValues.confirmPassword) {
    errorMessage.value = 'Пароли не совпадают';
    successMessage.value = '';
    return;
  }

  isLoading.value = true;
  userStore
    .register(typedValues.username, typedValues.email, typedValues.password)
    .then((data) => {
      if (data.error) {
        errorMessage.value = data.error;
        successMessage.value = '';
      }

      if (data.success) {
        successMessage.value = data.success;
        errorMessage.value = '';
      }
    })
    .finally(() => (isLoading.value = false));
});
</script>

<template>
  <CardWrapper
    header-label="Регистрация"
    label="Уже есть существующий аккаунт?"
    back-button-href="/auth/login"
    back-button-label="Войти">
    <form
      class="h-fit p-5 rounded-lg w-[80%] sm:w-1/2 md:w-2/3 space-y-6 mt-6 flex flex-col bg-white"
      @submit="onSubmit">
      <FormField v-slot="{ componentField }" name="username">
        <FormItem v-auto-animate>
          <FormLabel>Имя пользователя</FormLabel>
          <FormControl>
            <Input type="text" placeholder="john doe" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="email">
        <FormItem v-auto-animate>
          <FormLabel>Почта</FormLabel>
          <FormControl>
            <Input type="email" placeholder="doe@example.com" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="password">
        <FormItem v-auto-animate>
          <FormLabel>Пароль</FormLabel>
          <FormControl>
            <Input type="password" placeholder="******" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="confirmPassword">
        <FormItem v-auto-animate>
          <FormLabel>Подтвердите пароль</FormLabel>
          <FormControl>
            <Input type="password" placeholder="******" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormError :message="errorMessage" />
      <FormSuccess :message="successMessage" />
      <Button type="submit" :disabled="isLoading"> Создать аккаунт </Button>
      <div class="relative">
        <div class="absolute inset-0 flex items-center">
          <span class="w-full border-t" />
        </div>
        <div class="relative flex justify-center text-xs uppercase">
          <span class="bg-background px-2 text-muted-foreground">
            либо зарегистрируйтесь через
          </span>
        </div>
      </div>
      <SocialsButtons />
    </form>
  </CardWrapper>
</template>

<style scoped></style>
