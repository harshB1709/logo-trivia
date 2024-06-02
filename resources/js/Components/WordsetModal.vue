<template>
    <dialog-modal :show="show" maxWidth="3xl">
        <template #title>
            <div class="flex justify-between text-black">
                <span>Wordset CRUD</span>
                <button @click="$emit('close')" class="hover:bg-gray-100 p-2 aspect-square">&times;</button>
            </div>
        </template>
        <template #content>
            <div class="flex flex-col md:flex-row w-full gap-3 text-black">
                <form id="wordset-crud" ref="wordsetCrudForm" class="hidden"></form>
                <div class="space-y-4 md:space-y-6 p-2 w-full">
                    <div>
                        <InputLabel for="wordset_name" value="Name" />
                        <TextInput
                            id="wordset_name"
                            name="name"
                            v-model="wordset.name"
                            type="text"
                            required
                            :readonly="!editable"
                            class="w-full"
                            form="wordset-crud"
                            placeholder="Wordset name"
                        />
                        <InputError class="mt-2" :message="formErrors?.name?.join(' ')" v-if="editable" />
                    </div>
                    <div>
                        <InputLabel for="wordset_words" value="Words" />
                        <InputError class="mt-2" :message="formErrors?.words?.join(' ')" v-if="editable" />
                        <div class="border border-gray-400 rounded p-4">
                            <TextInput
                                id="words_search"
                                name="words_search"
                                v-model="wordSearch"
                                type="search"
                                class="w-full mt-2 mb-6"
                                placeholder="Search words"
                            />
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 max-h-96 overflow-auto">
                                <template
                                    v-for="(word, key) in showWords"
                                    :key="key"
                                >
                                    <div class="flex flex-row gap-3 h-24 py-2">
                                        <!-- <input type="checkbox" class="h-6 w-6 rounded-sm" :name="`words[${word.id}]`" form="wordset-crud"> -->
                                        <Checkbox v-model:checked="word.selected" value="1" :name="`words[${word.id}]`" form="wordset-crud" class="h-6 w-6" v-if="editable"/>
                                        <svg v-html="word.svg" class="h-20 w-20 svg-logo"></svg>
                                        <span class="text-xl pt-4 font-semibold">{{word.name}}</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <div class="flex justify-end gap-2 text-black">
                <primary-button v-if="editable" @click="submit">Submit</primary-button>
            </div>
        </template>
    </dialog-modal>
</template>

<script>
import DialogModal from "@/Components/DialogModal.vue";
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

export default {
    components: {
        DialogModal,
        Checkbox,
        PrimaryButton,
        TextInput,
        InputLabel,
        InputError
    },

    data() {
        return {
            showWords: this.words,
            formErrors: null,
            wordSearch: ''
        };
    },

    watch: {
        show(newVal, oldVal) {
            if((oldVal === true) && (newVal === false)) {
                this.formErrors = null;
            }
        },
        words(newVal, oldVal) {
            this.showWords = this.words;
        },
        wordSearch(newVal, oldVal) {
            this.showWords = newVal.length ? this.words.filter((w) => w.name?.toLowerCase()?.includes(newVal)) : this.words;
        }
    },

    props: {
        wordset: {
            type: Object,
            default: null
        },
        words: {
            type: Array,
            default: []
        },
        editable: {
            type: Boolean,
            default: false
        },
        show: {
            type: Boolean,
            default: false
        }
    },

    emits: ['close', 'wordsetUpdate'],

    methods: {
        submit() {
            if(this.editable) {
                const apiUrl = this.wordset?.id ? `/api/wordset/${this.wordset.id}/update` : `/api/wordset/store`;
                const formData = new FormData(this.$refs.wordsetCrudForm);
                axios
                    .post(apiUrl, formData)
                    .then((res) => {
                        this.$emit('close');
                        this.$emit('wordsetUpdate');
                    })
                    .catch((err) => {
                        this.formErrors = err.response?.data?.errors
                    })
            }
        }
    }
}
</script>
