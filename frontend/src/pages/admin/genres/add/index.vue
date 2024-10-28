<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from 'vee-validate';
import { vAutoAnimate } from '@formkit/auto-animate/vue';

import FormError from '@/components/form-error.vue';
import FormSuccess from '@/components/form-success.vue';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import Button from '@/components/ui/button/Button.vue';
import Wrapper from '@/components/admin/wrapper.vue';
import { genreSchema } from '@/schemas/index';
import api from '@/lib/axios';

const form = useForm({
  validationSchema: genreSchema,
});

const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const getURL = (file: File) => {
  if (file) {
    const url = URL.createObjectURL(file);
    return url;
  }
};

const onSubmit = form.handleSubmit((values) => {
  isLoading.value = true;
  api
    .postForm('/genres/add_genre.php', values)
    .then(() => {
      successMessage.value = 'Жанр добавлен';
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
  <Wrapper title="Добавить жанр" width-limit>
    <form class="flex flex-col gap-y-4" @submit="onSubmit">
      <FormField v-slot="{ componentField }" name="name">
        <FormItem v-auto-animate>
          <FormLabel>Название жанра</FormLabel>
          <FormControl>
            <Input type="text" placeholder="Jazz" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField
        v-slot="{ componentField: value, handleChange, handleBlur, handleInput, handleReset }"
        name="cover">
        <FormItem v-auto-animate>
          <FormLabel>Обложка</FormLabel>
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
