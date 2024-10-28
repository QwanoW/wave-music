<script setup lang="ts">
import { ref, inject } from 'vue';
import { useForm } from 'vee-validate';
import { vAutoAnimate } from '@formkit/auto-animate/vue';

import { API_URL } from '@/constants/index';
import { editArtistSchema, editArtistSchemaType } from '@/schemas/index';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { getURL } from '@/lib/utils';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import api from '@/lib/axios';
import Button from '@/components/ui/button/Button.vue';
import EditDialog from '@/components/admin/edit-dialog.vue';
import FormError from '@/components/form-error.vue';
import FormSuccess from '@/components/form-success.vue';

const onUpdate = inject<(updatedArtist: editArtistSchemaType) => void>('onUpdate');

const props = defineProps<{
  id: number;
  name: string;
  bio: string;
  photo_uri: string;
}>();

const form = useForm({
  validationSchema: editArtistSchema,
  initialValues: {
    id: props.id,
    name: props.name,
    bio: props.bio,
  },
});

const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const onSubmit = form.handleSubmit((values) => {
  isLoading.value = true;
  api
    .postForm('/artists/edit_artist.php', values)
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
    triggerTitle="Редактировать"
    dialogTitle="Редактировать исполнителя"
    dialogDescription="Редактировать исполнителя">
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
          <div class="flex flex-col gap-y-4" v-if="photo_uri || value.modelValue">
            <p class="">Предпросмотр</p>
            <img
              class="max-w-sm w-full rounded-lg"
              :src="value.modelValue ? getURL(value.modelValue) : API_URL + photo_uri"
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
