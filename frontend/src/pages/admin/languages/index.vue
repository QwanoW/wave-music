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
import EditDialog from '@/components/admin/edit-dialog.vue';
import Input from '@/components/ui/input/Input.vue';
import { Label } from '@/components/ui/label';
import { useToast } from '@/components/ui/toast/use-toast';

const { toast } = useToast();

const languages = ref<{ id: number; name: string }[]>([]);

const isLoadingEdit = ref(false);
// TODO: fix types
const onSubmitEdit = (e: any): void => {
  isLoadingEdit.value = true;
  const id = Number(e.target[0].value);
  const name = e.target[1].value;

  api
    .postForm('/languages/edit_language.php', { id, name })
    .then(() => {
      const index = languages.value.findIndex((language) => language.id === id);
      if (index !== -1) {
        languages.value.splice(index, 1, {
          ...languages.value[index],
          name,
        });
      }
      toast({
        title: 'Язык успешно был изменен',
      });
    })
    .catch((error) => {
      toast({
        title: 'Ошибка',
        description: error.response.data.message,
        variant: 'destructive',
      });
    })
    .finally(() => {
      isLoadingEdit.value = false;
    });
};

const isLoadingDelete = ref(false);
const onDelete = (id: number): void => {
  isLoadingDelete.value = true;
  api
    .postForm('/languages/delete_language.php', { id })
    .then(() => {
      const index = languages.value.findIndex((language) => language.id === id);
      if (index !== -1) {
        languages.value.splice(index, 1);
        toast({
          title: 'Успешно',
          description: 'Язык был успешно удален',
        });
      }
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

const isLoadingAdd = ref(false);
const onSubmitAdd = (e: any): void => {
  const name = e.target[0].value;

  if (!name) return;

  isLoadingAdd.value = true;
  api
    .postForm('/languages/add_language.php', { name })
    .then((resp) => {
      languages.value.push({ id: resp.data.id, name });
      toast({
        title: 'Успешно',
        description: `Язык ${name} успешно добавлен`,
      });
    })
    .catch((error) => {
      toast({
        title: 'Ошибка',
        description: error.response.data.message,
        variant: 'destructive',
      });
    })
    .finally(() => {
      isLoadingAdd.value = false;
    });
  e.target.reset();
};

const isLoading = ref(true);
onMounted(async () => {
  const response = await api.get('/languages/');
  languages.value = response.data;
  isLoading.value = false;
});
</script>

<template>
  <Wrapper title="Языки">
    <form class="flex w-full space-x-2" @submit.prevent="onSubmitAdd">
      <Input id="languageInput" type="text" placeholder="Название" class="grow" />
      <Button :disabled="isLoadingAdd" type="submit"> Добавить </Button>
    </form>
    <Table>
      <TableCaption>Список языков.</TableCaption>
      <TableHeader>
        <TableRow>
          <TableHead class="w-[100px]"> ID </TableHead>
          <TableHead> Название </TableHead>
          <TableHead class="text-right"> Действия </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-if="languages" v-for="language in languages" :key="language.id">
          <TableCell class="font-medium">
            {{ language.id }}
          </TableCell>
          <TableCell>{{ language.name }}</TableCell>
          <TableCell class="flex flex-col sm:flex-row justify-end items-end sm:gap-x-2 gap-y-2">
            <div>
              <EditDialog
                trigger-title="Редактировать"
                dialog-title="Редактировать язык"
                :dialog-description="'Редактировать язык №' + language.id">
                <form class="flex flex-col gap-y-4" @submit.prevent="onSubmitEdit">
                  <Input
                    class="hidden"
                    id="id"
                    :default-value="language.id"
                    type="number"
                    disabled />
                  <Label for="name">Название</Label>
                  <Input id="name" :default-value="language.name" type="text" />
                  <Button :disabled="isLoadingEdit" variant="outline">Сохранить</Button>
                </form>
              </EditDialog>
            </div>
            <Button :disabled="isLoadingDelete" @click="onDelete(language.id)" variant="outline"
              >Удалить</Button
            >
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </Wrapper>
</template>
