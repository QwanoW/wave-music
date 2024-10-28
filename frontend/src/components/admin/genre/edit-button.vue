<script setup lang="ts">
import { ref, inject } from 'vue';
import { useForm } from 'vee-validate';
import { vAutoAnimate } from '@formkit/auto-animate/vue';

import EditDialog from '@/components/admin/edit-dialog.vue';
import FormError from '@/components/form-error.vue';
import FormSuccess from '@/components/form-success.vue';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import Button from '@/components/ui/button/Button.vue';
import { editGenreSchema, editGenreSchemaType } from '@/schemas/index';
import api from '@/lib/axios';
import { API_URL } from '@/constants/index';
import { getURL } from '@/lib/utils';
import { Genre } from '@/constants/types';

const onUpdate = inject<(updatedGenre: editGenreSchemaType) => void>('onUpdate');

const props = defineProps<Genre>();

const form = useForm({
  validationSchema: editGenreSchema,
  initialValues: {
    id: props.id,
    name: props.name,
  },
});

const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const onSubmit = form.handleSubmit((values) => {
  isLoading.value = true;
  api
    .postForm('/genres/edit_genre.php', values)
    .then((resp) => {
      successMessage.value = resp.data.message;
      if (onUpdate) {
        onUpdate(values);
      }
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
  <EditDialog
    triggerTitle="Редактировать жанр"
    dialogTitle="Редактировать жанр"
    dialogDescription="Редактировать жанр">
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
          <div class="flex flex-col gap-y-4" v-if="cover_uri || value.modelValue">
            <p class="">Предпросмотр</p>
            <img
              class="max-w-sm w-full rounded-lg"
              :src="value.modelValue ? getURL(value.modelValue) : API_URL + cover_uri"
              alt="" />
          </div>
        </FormItem>
      </FormField>
      <FormError :message="errorMessage" />
      <FormSuccess :message="successMessage" />
      <Button :loading="isLoading" type="submit"> Сохранить </Button>
    </form>
  </EditDialog>
</template>
