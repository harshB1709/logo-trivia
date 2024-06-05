<template>
    <AppLayout title="Events">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Events
            </h2>
        </template>

        <div class="pt-8 pb-10">
            <div class="max-w-7xl mx-auto px-2 px-6 lg:px-8">
                <div class="w-full flex justify-end gap-2 mb-2">
                    <primary-button @click="createEvent">Create Event</primary-button>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
                    <Table :meta="events" :striped="true">

                        <template #body>
                            <tr
                                v-for="(event, key) in events.data"
                                :key="key"
                            >
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">{{ event.name }}</td>
                                <td class="text-base py-4 px-6 text-gray-900">{{ event.slug }}</td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">{{ event.wordset.name }}</td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">{{ event.start_date }}</td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">{{ event.end_date }}</td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">
                                    {{ event.is_active ? 'Active' : 'Inactive' }}
                                </td>
                                <td class="text-base py-4 px-6 text-gray-900">
                                    <div class="flex flex-wrap gap-2 w-auto">
                                        <primary-button @click="viewEvent(event)" type="button">View</primary-button>
                                        <primary-button @click="editEvent(event)" type="button">Edit</primary-button>
                                        <Link class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" :href="route('players', {event: event.slug})">Players</Link>
                                        <Link class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" :href="route('leaderboard', {event: event.slug})">Leaderboard</Link>
                                        <Link class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" :href="route('home', {event: event.slug})">Homepage</Link>
                                        <Link class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" :href="route('player-register', {event: event.slug})">Register</Link>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </Table>
                </div>
            </div>
        </div>

        <event-modal
            :show="showEventModal"
            :event="modalEvent"
            :editable="modalEventEditable"
            @close="showEventModal = false"
            @event-update="updateEvent"
        />
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import EventModal from '@/Components/EventModal.vue';
import { Link } from '@inertiajs/vue3'

    export default {
        components: {
            AppLayout,
            Table,
            PrimaryButton,
            EventModal,
            Link
        },

        data() {
            return {
                showEventModal: false,
                modalEvent: null,
                modalEventEditable: false,
                modalWords: []
            }
        },

        props: {
            events: {
                type: Object,
                default: {}
            },
            wordsets: {
                type: Array,
                default: []
            },
            words: {
                type: Array,
                default: []
            },
        },

        methods: {
            viewEvent(event) {
                this.modalEvent = { ...event };
                this.modalEvent.start_date = this.modalEvent.start_date.split('-').reverse().join('-');
                this.modalEvent.end_date = this.modalEvent.end_date.split('-').reverse().join('-');
                this.modalEvent.is_active = !!this.modalEvent.is_active;
                this.modalEventEditable = false;
                this.showEventModal = true;
            },
            editEvent(event) {
                this.modalEvent = { ...event };
                this.modalEvent.start_date = this.modalEvent.start_date.split('-').reverse().join('-');
                this.modalEvent.end_date = this.modalEvent.end_date.split('-').reverse().join('-');
                this.modalEvent.is_active = !!this.modalEvent.is_active;
                this.modalEventEditable = true;
                this.showEventModal = true;
            },
            createEvent(event) {
                this.modalEvent = {};
                this.modalWords = this.words.map((x) => {return{...x}});
                this.modalEventEditable = true;
                this.showEventModal = true;
            },
            updateEvent(event) {
                this.$inertia.reload({only: ['events']});
            },
            openSettings(event) {
                window.location.href = route('players', {event: event.slug});
            },
            openLeaderboard(event) {
                window.location.href = route('leaderboard', {event: event.slug})
            },
            openHomePage(event) {
                window.location.href = route('home', {event: event.slug})
            },
            openRegister(event) {
                window.location.href = route('player-register', {event: event.slug})
            }
        }
    }
</script>
<style></style>