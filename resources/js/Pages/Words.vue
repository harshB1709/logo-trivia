<template>
    <AppLayout title="Words">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Words
            </h2>
        </template>

        <div class="pt-8 pb-10">
            <div class="max-w-7xl mx-auto px-2 px-6 lg:px-8">
                <div class="w-full flex justify-end gap-2 mb-2">
                    <primary-button @click="createWord">Create Word</primary-button>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
                    <Table :meta="words" :striped="true">

                        <template #body>
                            <tr
                                v-for="(word, key) in words.data"
                                :key="key"
                            >
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">{{ word.name }}</td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">
                                    <svg v-html="word.svg" class="w-32 svg-logo"></svg>
                                </td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap text-center">{{ word.points }}</td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">
                                    {{ word.is_active ? 'Active' : 'Inactive' }}
                                </td>
                                <td class="text-base py-4 px-6 text-gray-900 whitespace-nowrap">
                                    <div class="flex gap-2 w-auto">
                                        <primary-button @click="viewWord(word)" type="button">View</primary-button>
                                        <primary-button @click="editWord(word)" type="button">Edit</primary-button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </Table>
                </div>
            </div>
        </div>

        <word-modal
            :show="showWordModal"
            :word="modalWord"
            :editable="modalWordEditable"
            @close="showWordModal = false"
            @word-update="updateWord"
        />
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import WordModal from '@/Components/WordModal.vue';

    export default {
        components: {
            AppLayout,
            Table,
            PrimaryButton,
            WordModal
        },

        data() {
            return {
                showWordModal: false,
                modalWord: null,
                modalWordEditable: false,
            }
        },

        props: {
            words: {
                type: Object,
                default: {}
            }
        },

        methods: {
            viewWord(word) {
                this.modalWord = { ...word };
                this.modalWord.is_active = !!this.modalWord.is_active;
                this.modalWordEditable = false;
                this.showWordModal = true;
            },
            editWord(word) {
                this.modalWord = { ...word };
                this.modalWord.is_active = !!this.modalWord.is_active;
                this.modalWordEditable = true;
                this.showWordModal = true;
            },
            createWord(word) {
                this.modalWord = {};
                this.modalWordEditable = true;
                this.showWordModal = true;
            },
            updateWord(word) {
                this.$inertia.reload({only: ['words']});
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
