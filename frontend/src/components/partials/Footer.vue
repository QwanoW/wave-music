<script setup lang="ts"></script>

<template>
  <div class="w-full bg-orange-100">
    <ul
      class="max-w-6xl w-fit md:w-full px-6 mx-auto grid grid-cols-1 gap-x-3 md:grid-cols-4 md:gap-x-6 py-8">
      <li
        class="text-center font-medium text-amber-600 hover:text-amber-900 cursor-pointer transition-all">
        <RouterLink to="/agreement">Пользовательское соглашение</RouterLink>
      </li>
      <li
        class="text-center font-medium text-amber-600 hover:text-amber-900 cursor-pointer transition-all">
        <RouterLink to="/offer">Публичная оферта</RouterLink>
      </li>
      <li
        class="text-center font-medium text-amber-600 hover:text-amber-900 cursor-pointer transition-all">
        <RouterLink to="/policy">Политика конфиденциальности</RouterLink>
      </li>
      <li
        class="text-center font-medium text-amber-600 hover:text-amber-900 cursor-pointer transition-all">
        <RouterLink to="/terms">Условия использования</RouterLink>
      </li>
    </ul>
  </div>
</template>

<style scoped></style>
