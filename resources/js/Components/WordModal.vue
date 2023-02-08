<template>
    <dialog-modal :show="show">
        <template #title>
            <div class="flex justify-between text-black">
                <span>Word CRUD</span>
                <button @click="$emit('close')" class="hover:bg-gray-100 p-2 aspect-square">&times;</button>
            </div>
        </template>
        <template #content>
            <div class="flex flex-col md:flex-row w-full gap-3 text-black">
                <form id="svg-crud" ref="svgCrudForm"></form>
                <div class="space-y-2 md:space-y-4 md:w-1/2 h-full px-2">
                    <div class="h-44 md:h-56 md:pt-4" v-if="svgContent || word.svg">
                        <div v-html="svgContent || word.svg" class="w-full h-full finished flex justify-center" id="word-logo" ref="wordLogo"></div>
                    </div>
                    <input
                        id="word-svg"
                        name="svg-file"
                        ref="svgInput"
                        type="file"
                        @change="handleSvgUpload"
                        accept="image/svg+xml"
                        v-if="editable"
                        form="svg-crud"
                    />
                    <InputError class="mt-2" :message="formErrors?.['svg-file']?.join(' ')" v-if="editable" />
                </div>
                <div class="space-y-4 md:space-y-6 md:w-1/2 py-2 px-2">
                    <div>
                        <InputLabel for="word_name" value="Name" />
                        <TextInput
                            id="word_name"
                            name="name"
                            v-model="word.name"
                            type="text"
                            required
                            :readonly="!editable"
                            class="w-full"
                            form="svg-crud"
                        />
                        <InputError class="mt-2" :message="formErrors?.name?.join(' ')" v-if="editable" />
                    </div>
                    <div>
                        <InputLabel for="word_points" value="Points" />
                        <TextInput
                            id="word_points"
                            name="points"
                            v-model="word.points"
                            type="number"
                            required
                            :readonly="!editable"
                            min="1"
                            max="3"
                            form="svg-crud"
                        />
                        <InputError class="mt-2" :message="formErrors?.points?.join(' ')" v-if="editable" />
                    </div>
                    <div>
                        <label class="flex items-center">
                            <Checkbox v-model:checked="word.is_active" value="1" name="is_active" :disabled="!editable" form="svg-crud"/>
                            <span class="ml-2 text-sm text-gray-600">Status</span>
                        </label>
                    </div>
                    <div>
                        <InputLabel for="word_hint" value="Hint" />
                        <textarea
                            class="border-gray-500 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                            id="word_hint"
                            v-model="word.hint"
                            :readonly="!editable"
                            form="svg-crud"
                            name="hint"
                        >
                        </textarea>
                        <InputError class="mt-2" :message="formErrors?.hint?.join(' ')" v-if="editable" />
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <div class="flex justify-end gap-2 text-black">
                <primary-button v-if="svgContent || word.svg" @click="animateLogo">Animate</primary-button>
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
import * as Vivus from "vivus";

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
            svgContent: null,
            formErrors: null
        };
    },

    watch: {
        show(newVal, oldVal) {
            if((oldVal === true) && (newVal === false)) {
                this.svgContent = null;
                this.formErrors = null;
            }
        }
    },

    props: {
        word: {
            type: Object,
            default: null
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

    emits: ['close', 'wordUpdate'],

    methods: {
        animateLogo() {
            this.$refs?.wordLogo?.classList?.remove('finished');
            const vivus = new Vivus(this.$refs?.wordLogo?.querySelector('svg'), {
                duration: 165,
                type: 'oneByOne'
            }, (obj) => {
                obj.el.parentNode.classList.add('finished');
            });
        },

        submit() {
            if(this.editable) {
                const apiUrl = this.word?.id ? `/api/word/${this.word.id}/update` : `/api/word/store`;
                const formData = new FormData(this.$refs.svgCrudForm);
                axios
                    .post(apiUrl, formData)
                    .then((res) => {
                        this.$emit('close');
                        this.$emit('wordUpdate');
                    })
                    .catch((err) => {
                        this.formErrors = err.response?.data?.errors
                    })
            }
        },

        handleSvgUpload(e) {
            if(e.target.files.length) {
                const reader = new FileReader();
                reader.onload = () => {
                    this.svgContent = reader.result;
                }
                reader.readAsBinaryString(e.target.files[0]);
            }
        }
    }
}
</script>
<style>
    #word-logo > svg {
        fill-opacity: 0;
        transition: fill-opacity 0.5s;
        stroke: #000;
        stroke-width: 0.4%;
    }

    #word-logo.finished > svg {
        fill-opacity: 1;
    }
</style>
