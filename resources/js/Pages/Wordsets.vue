<template>
    <AppLayout title="Wordsets">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Wordsets
            </h2>
        </template>

        <div class="pt-8 pb-10">
            <div class="max-w-7xl mx-auto px-2 px-6 lg:px-8">
                <div class="w-full flex justify-end gap-2 mb-2">
                    <primary-button @click="createWordset">Create Wordset</primary-button>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
                    <Table :meta="wordsets" :striped="true">

                        <template #body>
                            <tr
                                v-for="(wordset, key) in wordsets.data"
                                :key="key"
                            >
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">{{ wordset.name }}</td>
                                <td class="text-base py-4 px-6 text-gray-900">{{ wordset.words.map(w => w.name).join(', ') }}</td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">{{ wordset.words.length }}</td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">
                                    <div class="flex gap-2 w-auto">
                                        <primary-button @click="viewWordset(wordset)" type="button">View</primary-button>
                                        <primary-button @click="editWordset(wordset)" type="button">Edit</primary-button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </Table>
                </div>
            </div>
        </div>

        <wordset-modal
            :show="showWordsetModal"
            :wordset="modalWordset"
            :words="modalWords"
            :editable="modalWordsetEditable"
            @close="showWordsetModal = false"
            @wordset-update="updateWordset"
        />
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import WordsetModal from '@/Components/WordsetModal.vue';

    export default {
        components: {
            AppLayout,
            Table,
            PrimaryButton,
            WordsetModal
        },

        data() {
            return {
                showWordsetModal: false,
                modalWordset: null,
                modalWordsetEditable: false,
                modalWords: []
            }
        },

        props: {
            wordsets: {
                type: Object,
                default: {}
            },
            words: {
                type: Array,
                default: []
            },
        },

        methods: {
            viewWordset(wordset) {
                this.modalWordset = { ...wordset };
                const wordset_words = wordset.words.map((ws) => ws.id);
                this.modalWords = this.words.filter((w) => wordset_words.includes(w.id));
                this.modalWordsetEditable = false;
                this.showWordsetModal = true;
            },
            editWordset(wordset) {
                this.modalWordset = { ...wordset };
                const wordset_words = wordset.words.map((ws) => ws.id);
                this.modalWords = this.words.map((x) => {return{...x}});
                for(const word of this.modalWords) {
                    word.selected = wordset_words.includes(word.id);
                }
                console.log(this.modalWords[0], this.words[0]);
                this.modalWordsetEditable = true;
                this.showWordsetModal = true;
            },
            createWordset(wordset) {
                this.modalWordset = {};
                this.modalWords = this.words.map((x) => {return{...x}});
                this.modalWordsetEditable = true;
                this.showWordsetModal = true;
            },
            updateWordset(wordset) {
                this.$inertia.reload({only: ['wordsets']});
            }
        }
    }
</script>
<style>
    .svg-logo {
        stroke: #000;
        stroke-width: 0.7%;
    }
</style>