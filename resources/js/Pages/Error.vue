<script setup>
import { computed } from 'vue'

const props = defineProps({
    status: Number,
    message: String
  });

const title = computed(() => {
  return {
    503: '503: Service Unavailable',
    500: '500: Server Error',
    404: '404: Page Not Found',
    403: '403: Forbidden',
  }[props.status]
})

const description = computed(() => {
  return {
    503: 'Sorry, we are doing some maintenance. Please check back soon.',
    500: 'Whoops, something went wrong on our servers.',
    404: 'Sorry, the page you are looking for could not be found.',
    403: 'Sorry, you are forbidden from accessing this page.',
  }[props.status]
})
</script>

<template>
  <div class="container max-w-7xl px-4 mx-auto h-[100vh] grid items-center ubuntu-mono">
    <div class="flex flex-col justify-center items-center gap-3 text-center text-3xl">
      <img src="/images/error.png" class="max-h-96">
      <template v-if="props.status !== 503 || !props.message">
        <h1>{{ title }}</h1>
        <p>{{ description }}</p>
      </template>
      <template v-else>
        <p class="font-semibold">{{props.message}}</p>
      </template>
    </div>
  </div>
</template>
