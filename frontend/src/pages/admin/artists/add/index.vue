<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from 'vee-validate';
import { vAutoAnimate } from '@formkit/auto-animate/vue';

import FormError from '@/components/form-error.vue';
import FormSuccess from '@/components/form-success.vue';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea'
import Button from '@/components/ui/button/Button.vue';
import Wrapper from '@/components/admin/wrapper.vue';
import { artistSchema } from '@/schemas/index';
import api from '@/lib/axios';

const form = useForm({
  validationSchema: artistSchema,
});

const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

// TODO: CHANGE TO IMPORT
const getURL = (file: File) => {
  if (file) {
    const url = URL.createObjectURL(file);
    return url;
  }
};

const onSubmit = form.handleSubmit((values) => {
  isLoading.value = true;
  api
    .postForm('/artists/add_artist.php', values)
    .then(() => {
      successMessage.value = 'Исполнитель добавлен';
      form.resetForm();
    })
    .catch((error) => {
      errorMessage.value = error.response.data.message;
    })
    .finally(() => {
      isLoading.value = false;
    });
});
</script>

<template>
  <Wrapper title="Добавить исполнителя" width-limit>
    <form class="flex flex-col gap-y-4" @submit="onSubmit">
      <FormField v-slot="{ componentField }" name="name">
        <FormItem v-auto-animate>
          <FormLabel>Имя исполнителя</FormLabel>
          <FormControl>
            <Input type="text" placeholder="Король и шут" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="bio">
        <FormItem v-auto-animate>
          <FormLabel>Об исполнителе</FormLabel>
          <FormControl>
            <Textarea
              type="text"
              placeholder="Советская и российская хоррор-панк-группа из..."
              v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField
        v-slot="{ componentField: value, handleChange, handleBlur, handleInput, handleReset }"
        name="photo">
        <FormItem v-auto-animate>
          <FormLabel>Фото</FormLabel>
          <FormControl>
            <Input
              type="file"
              accept="image/*"
              @change="handleChange"
              @blur="handleBlur"
              @input="handleInput"
              @reset="handleReset" />
          </FormControl>
          <FormMessage />
          <div class="flex flex-col gap-y-4" v-if="value.modelValue">
            <p class="">Предпросмотр</p>
            <img class="max-w-sm w-full rounded-lg" :src="getURL(value.modelValue)" alt="" />
          </div>
        </FormItem>
      </FormField>
      <FormError :message="errorMessage" />
      <FormSuccess :message="successMessage" />
      <Button :loading="isLoading" type="submit"> Сохранить </Button>
    </form>
  </Wrapper>
</template>

<style scoped></style>
