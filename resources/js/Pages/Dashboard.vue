<template>
    <AppLayout title="Words">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Words
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
                    <Table :resource="words" :striped="true">
                        <template #cell(url)="{ item: word }">
                            <div class="h-100 block">
                                <!-- <svg v-html="word.url"></svg> -->
                                <img :src="word.url" height="100" width="100">
                            </div>
                        </template>

                        <template #cell(is_active)="{ item: word }">
                            {{ word.is_active ? 'Active' : 'Inactive' }}
                        </template>

                        <template #cell(actions)="{ item: word }">
                            <a :href="`/words/${word.id}/edit`">
                            Edit
                            </a>
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

    export default {
        components: {
            AppLayout,
            Table
        },

        props: {
            words: {
                type: Object,
                default: {}
            }
        }
    }
</script>
