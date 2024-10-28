<script setup lang="ts">
import { ref, provide } from 'vue';

import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion';
import Header from '@/components/partials/header/index.vue';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { AudioLines } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import PaymentMethod from '@/components/payment-method.vue';
import Footer from '@/components/partials/Footer.vue';

const isOpenPlus = ref(false);
const togglePlus = () => (isOpenPlus.value = !isOpenPlus.value);
provide('togglePlus', togglePlus);

const isOpen = ref(false);
const toggle = () => (isOpen.value = !isOpen.value);
provide('toggle', toggle);

const QnA = [
  {
    question: 'Я могу попробовать подписку бесплатно?',
    answer:
      'Если вы подключаете подписку впервые, то вам доступен бесплатный пробный период. После того, как он закончится, будет списываться плата согласно тарифному плану.',
  },
  {
    question: 'Нужно ли привязывать карту, чтобы попробовать подписку бесплатно?',
    answer:
      'Да, к аккаунту нужно привязать карту. Не беспокойтесь: пока не закончится пробный период, никаких списаний не будет.',
  },
  {
    question: 'Как узнать, когда закончится бесплатный пробный период?',
    answer: 'Дату окончания пробного периода вы можете узнать в личном кабинете.',
  },
  {
    question: 'Как часто нужно оплачивать подписку?',
    answer:
      'Если у вас подключена ежемесячная подписка, деньги будут списываться с карты раз в месяц. Если годовая, то раз в год.',
  },
  {
    question: 'Могу ли я отменить подписку?',
    answer: 'Если вам пока не нужна подписка, вы можете отменить ее в личном кабинете.',
  },
];
</script>

<template>
  <div class="min-h-screen h-full flex flex-col bg-orange-100 relative">
    <Header class="sticky top-0 z-10" />
    <div class="max-w-6xl m-auto w-full flex flex-col gap-y-4">
      <div class="flex flex-col gap-y-10 w-full p-5 md:p-10 bg-white rounded-xl">
        <h1 class="text-center text-3xl font-bold">Подписка на Wave Music</h1>
        <div class="w-full flex flex-col lg:flex-row justify-between gap-x-10 gap-y-10">
          <Card
            class="relative group cursor-pointer w-full lg:aspect-square flex flex-col justify-between bg-white ring-1 ring-gray-900/5 rounded-lg leading-none">
            <div
              class="absolute -inset-1 bg-gradient-to-r from-green-600 to-blue-600 rounded-lg blur opacity-25 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
            <div
              class="h-full relative px-7 py-6 bg-white ring-1 ring-gray-900/5 rounded-lg leading-none flex items-top justify-start space-x-6">
              <div class="space-y-2 flex flex-col justify-between w-full">
                <div>
                  <CardHeader>
                    <CardTitle class="text-2xl flex justify-between">
                      Премиум+
                      <AudioLines class="w-8 h-8 text-green-500" />
                    </CardTitle>
                    <h2
                      class="text-3xl font-bold bg-gradient-to-r from-green-500 to-primary bg-clip-text text-transparent">
                      209 ₽ в месяц
                    </h2>
                    <p><span class="line-through">3588</span> 2499 ₽ в год</p>
                  </CardHeader>
                  <CardContent>
                    <ul class="list-disc pl-5 space-y-2">
                      <li>
                        <span class="font-semibold">Все преимущества обычной подписки</span>
                      </li>
                      <li>
                        <span class="font-semibold">Экономия</span> - платите меньше, чем за обычную
                        подписку
                      </li>
                      <li>
                        <span class="font-semibold">Отличное качество звука</span> - наслаждайтесь
                        музыкой в наилучшем качестве
                      </li>
                    </ul>
                  </CardContent>
                </div>
                <CardFooter class="flex flex-col gap-y-2">
                  <Dialog :open="isOpenPlus" @update:open="(state) => (isOpenPlus = state)">
                    <DialogTrigger as-child>
                      <Button class="px-16 py-6 text-lg font-semibold" variant="outline"
                        >30 дней бесплатно</Button
                      >
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[425px] p-0">
                      <PaymentMethod :duration="30" />
                    </DialogContent>
                  </Dialog>
                  <p class="text-gray-400">Далее 2499₽ в год</p>
                </CardFooter>
              </div>
            </div>
          </Card>
          <Card
            class="relative group cursor-pointer w-full lg:aspect-square flex flex-col justify-between bg-white ring-1 ring-gray-900/5 rounded-lg leading-none">
            <div
              class="absolute -inset-1 bg-gradient-to-r from-red-600 to-violet-600 rounded-lg blur opacity-25 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
            <div
              class="h-full relative px-7 py-6 bg-white ring-1 ring-gray-900/5 rounded-lg leading-none flex items-top justify-start space-x-6">
              <div class="space-y-2 flex flex-col justify-between w-full">
                <div>
                  <CardHeader>
                    <CardTitle class="text-2xl flex justify-between">
                      Премиум
                      <AudioLines class="w-8 h-8 text-orange-500" />
                    </CardTitle>
                    <h2 class="text-3xl font-bold text-primary">299₽ в месяц</h2>
                  </CardHeader>
                  <CardContent>
                    <ul class="list-disc pl-5 space-y-2">
                      <li>
                        <span class="font-semibold">Персональные рекомендации</span> - музыкальные
                        предложения, основанные на ваших предпочтениях
                      </li>
                      <li>
                        <span class="font-semibold">Без рекламы</span> - слушайте музыку без
                        перерывов на рекламу
                      </li>
                      <li>
                        <span class="font-semibold">Высокое качество звука</span> - наслаждайтесь
                        музыкой в лучшем качестве
                      </li>
                    </ul>
                  </CardContent>
                </div>
                <CardFooter class="flex flex-col gap-y-2">
                  <Dialog :open="isOpen" @update:open="(state) => (isOpen = state)">
                    <DialogTrigger as-child>
                      <Button class="px-16 py-6 text-lg font-semibold" variant="outline"
                        >90 дней бесплатно</Button
                      >
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[425px] p-0">
                      <PaymentMethod :duration="90" />
                    </DialogContent>
                  </Dialog>
                  <p class="text-gray-400">Далее 299₽ в месяц</p>
                </CardFooter>
              </div>
            </div>
          </Card>
        </div>
      </div>
      <div class="flex flex-col gap-y-10 w-full p-5 md:p-10 bg-white rounded-xl">
        <h1 class="text-center text-3xl font-bold">Есть вопросы?</h1>
        <Accordion type="single" class="w-full md:w-1/2 m-auto" collapsible>
          <AccordionItem v-for="item in QnA" :key="item.question" :value="item.answer">
            <AccordionTrigger class="text-lg text-start">{{ item.question }}</AccordionTrigger>
            <AccordionContent>
              {{ item.answer }}
            </AccordionContent>
          </AccordionItem>
        </Accordion>
      </div>
    </div>
    <Footer class="mt-10" />
  </div>
</template>
