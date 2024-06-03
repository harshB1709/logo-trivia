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
                                        <primary-button @click="openSettings(event)" type="button">Players</primary-button>
                                        <primary-button @click="openLeaderboard(event)" type="button">Leaderboard</primary-button>
                                        <primary-button @click="openHomePage(event)" type="button">Homepage</primary-button>
                                        <primary-button @click="openRegister(event)" type="button">Register</primary-button>
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

    export default {
        components: {
            AppLayout,
            Table,
            PrimaryButton,
            EventModal
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