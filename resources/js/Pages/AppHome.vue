<template>
	<game-layout title="Home" :showEventRoutes="false">
		<div class="max-w-4xl flex flex-col mx-auto">
		    <h1 class="text-5xl font-bold text-primary mb-5 w-fit mx-auto">
		        <img src="/images/ranium-logo-white.png" class="w-48 mx-auto my-4">
		        <span class="h-100 inline-block">Logo Trivia</span>
		    </h1>
		    <div class="w-fit mx-auto flex flex-col gap-4" id="events-container">
		        <div class="card w-80 md:w-96 bg-primary text-primary-content shadow-xl" v-for="event in events" :key="event.id">
		          	<div class="card-body">
		            	<h2 class="card-title">{{event.name}}</h2>
		            	<p v-if="event.start_date === event.end_date">{{formatDate(event.start_date)}}</p>
		            	<p v-else>{{formatDate(event.start_date)}} - {{formatDate(event.end_date)}}</p>
		            	<div class="card-actions justify-end">
		            		<Link class="btn" :href="route('home', {event: event.slug})">
		            			Enter
		            		</Link>
		            	</div>
		          	</div>
		        </div>
		    </div>
		</div>
	</game-layout>
</template>

<script>
import GameLayout from "@/Layouts/GameLayout.vue";
import { Link } from '@inertiajs/vue3'

export default {
	components: {
		GameLayout,
		Link
	},

	props: {
		events: {
			type: Object,
			default: {}
		}
	},

	methods: {
		formatDate(date) {
			return date.split('-').join('/');
		}
	}
}
</script>
<style>
	#events-container {
		max-height: 36em;
		overflow-y: auto;
	}
</style>