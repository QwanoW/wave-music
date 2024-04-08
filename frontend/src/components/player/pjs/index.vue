<script setup lang="ts">
import pjs from './loader';

pjs.then(() => {
  // @ts-ignore
  let player = new Playerjs({
    id: 'player',
    file: 'http://cursach-backend.lndo.site/content/song/660ce3c092ef0/index.m3u8',
  });
});
</script>

<template>
  <div id="player"></div>
</template>

<style>
#player {
  width: 100%;
}
</style>
