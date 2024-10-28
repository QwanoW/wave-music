<script setup lang="ts">
import Draggable from 'vuedraggable';
import { ref, onMounted } from 'vue';
import { useForm } from 'vee-validate';
import { vAutoAnimate } from '@formkit/auto-animate/vue';

import FormError from '@/components/form-error.vue';
import FormSuccess from '@/components/form-success.vue';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import Button from '@/components/ui/button/Button.vue';
import Wrapper from '@/components/admin/wrapper.vue';
import { albumSchema } from '@/schemas/index';
import api from '@/lib/axios';
import { Track } from '@/constants/types.ts';
import { getURL } from '@/lib/utils';

const tracks = ref<Track[]>();
const newTracks = ref<Track[]>([]);

const form = useForm({
  validationSchema: albumSchema,
});

const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

// TODO: переписать везде вывод ошибки
const onSubmit = form.handleSubmit((values) => {
  isLoading.value = true;
  api
    .postForm('/albums/add_album.php', values)
    .then(() => {
      successMessage.value = 'Альбом добавлен';
      form.resetForm();
    })
    .catch((error) => {
      errorMessage.value = error.response.data.message;
    })
    .finally(() => {
      isLoading.value = false;
    });
});

onMounted(async () => {
  tracks.value = (await api.get('/tracks/')).data;
});
</script>

<!-- TODO: IF NO DATA SHOW LINK TO ADD DATA -->
<template>
  <Wrapper title="Добавить альбом" width-limit>
    <form class="flex flex-col gap-y-4" @submit="onSubmit">
      <FormField v-slot="{ componentField }" name="title">
        <FormItem v-auto-animate>
          <FormLabel>Название альбома</FormLabel>
          <FormControl>
            <Input type="text" placeholder="Thriller" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField name="tracks">
        <FormItem v-auto-animate>
          <FormControl>
            <div class="flex w-full h-fit gap-2">
              <div class="w-1/2 space-y-2">
                <FormLabel>Альбом</FormLabel>
                <Draggable
                  class="border border-gray-300 rounded-md h-72 overflow-y-auto"
                  group="allTracks"
                  :animation="200"
                  tag="ul"
                  @change="
                    () => {
                      form.setFieldValue(
                        'tracks',
                        newTracks.map((track, index) => {
                          return { id: track.id, order: index + 1 };
                        }),
                      );
                    }
                  "
                  :list="newTracks">
                  <template #item="{ element, index }">
                    <li class="cursor-grab">
                      <div class="flex items-center gap-x-2 p-2">
                        <span class="text-gray-500">#{{ index + 1 }}</span>
                        {{ element.title }}
                      </div>
                    </li>
                  </template>
                </Draggable>
              </div>
              <div class="w-1/2">
                <span class="block mb-2 font-medium">Все треки</span>
                <Draggable
                  class="border border-gray-300 rounded-md h-72 overflow-y-auto"
                  group="allTracks"
                  :animation="200"
                  tag="ul"
                  :list="tracks">
                  <template #item="{ element, index }">
                    <li class="cursor-grab">
                      <div class="flex items-center gap-x-2 p-2">
                        <span class="text-gray-500">#{{ index + 1 }}</span>
                        {{ element.title }}
                      </div>
                    </li>
                  </template>
                </Draggable>
              </div>
            </div>
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>

      <p class="text-gray-500">Перенесите нужные треки в левый столбец и сформируйте альбом</p>
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

<style scoped>
.draggable-list {
  min-height: 100px;
}

.moving-card {
  opacity: 0.5;
  background: #f7fafc;
  border: 1px solid #4299e1;
}
</style>
