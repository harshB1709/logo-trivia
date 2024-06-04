<template>
    <AppLayout title="Players">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Players
            </h2>
        </template>

        <div class="pt-8 pb-10">
            <div class="max-w-7xl mx-auto px-2 px-6 lg:px-8">
                <div class="w-full flex justify-end gap-2 mb-2">
                    <a :href="route('player-register', {event: $page.props.currentEvent.slug})" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Register User</a>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
                    <Table :meta="players" :striped="true">

                        <template #body>
                            <tr
                                v-for="(player, key) in players.data"
                                :key="key"
                            >
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">{{ player.name }}</td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">{{ player.email }}</td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">{{ player.display_name }}</td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap text-center">{{ player?.game?.score }}</td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">
                                    <div class="flex gap-2 w-auto">
                                        <primary-button @click="sendInvite(player.id)" type="button">Send Invite</primary-button>
                                        <primary-button @click="resetGame(player.id)" type="button">Reset Game</primary-button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePage } from '@inertiajs/vue3'

    export default {
        components: {
            AppLayout,
            Table,
            PrimaryButton,
        },

        data() {
            return {
            }
        },

        props: {
            players: {
                type: Object,
                default: {}
            }
        },

        methods: {
            sendInvite(playerId) {
                if(confirm('Are you sure about sending the game invite to this player?')) {
                    axios.post(route('send-invite', {event: usePage().props.currentEvent.slug, player: playerId}))
                        .then((res) => {
                            alert(res.data.message)
                        });
                }
            },

            resetGame(playerId) {
                if(confirm('Are you sure about resetting this player\'s game?')) {
                    axios.post(route('reset-game', {event: usePage().props.currentEvent.slug, player: playerId}))
                        .then((res) => {
                            this.$inertia.reload({only: ['players']});
                            alert(res.data.message);
                        });
                }
            }
        }
    }
</script>
