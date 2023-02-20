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
    401: '401: Unauthorized',
    400: '400: Bad Request'
  }[props.status]
})

const description = computed(() => {
  return {
    503: 'Sorry, we are doing some maintenance. Please check back soon.',
    500: 'Whoops, something went wrong on our servers.',
    404: 'Sorry, the page you are looking for could not be found.',
    403: 'Sorry, you are forbidden from accessing this page.',
    401: 'Sorry, you are not authorized to access this page.',
    400: 'Sorry, looks like you cannot use this url'
  }[props.status]
})
</script>

<template>
  <div class="container max-w-7xl px-4 mx-auto h-[100vh] grid items-center ubuntu-mono">
    <div class="flex flex-col justify-center items-center gap-3 text-center text-3xl">
      <img src="/images/error.png" class="max-h-96">
      <template v-if="![400, 503].includes(props.status) || !props.message">
        <h1>{{ title }}</h1>
        <p>{{ description }}</p>
      </template>
      <template v-else>
        <p class="font-semibold">{{props.message}}</p>
      </template>
      <button class="btn btn-outline btn-primary" @click="$inertia.visit('/')">Back to Homepage</button>
    </div>
  </div>
</template>
