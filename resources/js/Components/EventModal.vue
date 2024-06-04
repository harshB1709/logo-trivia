<template>
    <dialog-modal :show="show" maxWidth="3xl">
        <template #title>
            <div class="flex justify-between text-black">
                <span>Event CRUD</span>
                <button @click="$emit('close')" class="hover:bg-gray-100 p-2 aspect-square">&times;</button>
            </div>
        </template>
        <template #content>
            <div class="flex flex-col md:flex-row w-full gap-3 text-black">
                <form id="event-crud" ref="eventCrudForm"></form>
                <div class="space-y-4 py-2 px-2 w-full">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-3 gap-y-5 w-full">
                        <div>
                            <InputLabel for="event_name" value="Name" />
                            <TextInput
                                id="event_name"
                                name="name"
                                v-model="event.name"
                                type="text"
                                required
                                :readonly="!editable"
                                class="w-full"
                                form="event-crud"
                                @input="handleNameInput"
                            />
                            <InputError class="mt-2" :message="formErrors?.name?.join(' ')" v-if="editable" />
                        </div>
                        <div>
                            <InputLabel for="event_slug" value="Slug" />
                            <TextInput
                                id="event_slug"
                                name="slug"
                                v-model="event.slug"
                                type="text"
                                required
                                :readonly="!editable"
                                class="w-full"
                                form="event-crud"
                            />
                            <InputError class="mt-2" :message="formErrors?.slug?.join(' ')" v-if="editable" />
                        </div>
                        <div>
                            <InputLabel for="start_date" value="Start Date" />
                            <TextInput
                                id="start_date"
                                name="start_date"
                                v-model="event.start_date"
                                type="date"
                                :readonly="!editable"
                                class="w-full"
                                form="event-crud"
                            />
                            <InputError class="mt-2" :message="formErrors?.start_date?.join(' ')" v-if="editable" />
                        </div>
                        <div>
                            <InputLabel for="end_date" value="End Date" />
                            <TextInput
                                id="end_date"
                                name="end_date"
                                v-model="event.end_date"
                                type="date"
                                :readonly="!editable"
                                class="w-full"
                                form="event-crud"
                            />
                            <InputError class="mt-2" :message="formErrors?.end_date?.join(' ')" v-if="editable" />
                        </div>
                        <div>
                            <InputLabel for="wordset_id" value="Wordset" />
                            <select
                                id="wordset_id"
                                name="wordset_id"
                                v-model="event.wordset_id"
                                :disabled="!editable"
                                class="w-full border-gray-500 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                form="event-crud"
                            >
                                <option
                                    v-for="(wordset, key) in $page.props.wordsets"
                                    :value="wordset.id"
                                    :key="key"
                                >
                                    {{wordset.name}}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="formErrors?.wordset_id?.join(' ')" v-if="editable" />
                        </div>
                        <div class="md:pt-6 pl-4">
                            <label class="flex items-center">
                                <Checkbox v-model:checked="event.is_active" value="1" name="is_active" :disabled="!editable" form="event-crud" class="w-6 h-6"/>
                                <span class="ml-2 text-black">Status</span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-4 px-2">
                        <InputLabel for="event-bg" value="Background Image" />
                        <div class="">
                            <img class="h-56" :src="event.background_img_url ?? bgImageContent" alt="Event BG" id="event-bg-img" v-if="event.background_img_url ?? bgImageContent">
                        </div>
                        <input
                            id="event-bg"
                            name="event-bg"
                            ref="eventBg"
                            type="file"
                            @change="handleEventBgUpload"
                            accept="image/*"
                            v-if="editable"
                            form="event-crud"
                        />
                        <InputError :message="formErrors?.['event-bg']?.join(' ')" v-if="editable" />
                    </div>
                    
                    <div>
                        <InputLabel for="home_content" value="Home Content" />
                        <Editor
                            form="event-crud"
                            name="home_content"
                            :readonly="!editable"
                            v-model="event.home_content"
                            api-key="joowo0gyte6a2y91cvcyum6jim7k36ngr2uh4ty3sn9vyupp"
                            :init="{
                                plugins: 'a11ychecker advcode advlist advtable anchor autocorrect autolink autoresize autosave casechange charmap checklist code codesample directionality editimage emoticons export footnotes formatpainter fullscreen help image importcss inlinecss insertdatetime link linkchecker lists media mediaembed mentions mergetags nonbreaking pagebreak pageembed permanentpen powerpaste preview quickbars save searchreplace table tableofcontents template  tinydrive tinymcespellchecker typography visualblocks visualchars wordcount',
                                images_upload_url: route('upload'),
                                automatic_uploads: true,
                                images_reuse_filename: true,
                                images_upload_handler: handleImageUpload,
                            }"
                        />
                        <InputError class="mt-2" :message="formErrors?.home_content?.join(' ')" v-if="editable" />
                        <div class="pt-2 text-base">Use #register_btn as a placeholder for the Registration button</div>
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
import Editor from '@tinymce/tinymce-vue'

export default {
    components: {
        DialogModal,
        Checkbox,
        PrimaryButton,
        TextInput,
        InputLabel,
        InputError,
        Editor
    },

    data() {
        return {
            bgImageContent: null,
            formErrors: null,
            editorConfig: {
                // plugins: [SimpleUploadAdapter],
                // simpleUpload: {
                //     uploadUrl: route('upload')
                // }
            }
        };
    },

    watch: {
        show(newVal, oldVal) {
            if((oldVal === true) && (newVal === false)) {
                this.bgImageContent = null;
                this.formErrors = null;
            }
        }
    },

    props: {
        event: {
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

    emits: ['close', 'eventUpdate'],

    methods: {
        submit() {
            if(this.editable) {
                const apiUrl = this.event?.id ? `/api/event/${this.event.id}/update` : `/api/event/store`;
                const formData = new FormData(this.$refs.eventCrudForm);
                formData.append('home_content', this.event.home_content);
                axios
                    .post(apiUrl, formData)
                    .then((res) => {
                        this.$emit('close');
                        this.$emit('eventUpdate');
                    })
                    .catch((err) => {
                        this.formErrors = err.response?.data?.errors
                    })
            }
        },

        handleNameInput(e) {
            const name = e.target?.value;
            this.event.slug = name.toString()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-');
        },

        handleEventBgUpload(e) {
            if(e.target?.files?.length) {
                const reader = new FileReader();
                reader.onload = () => {
                    this.bgImageContent = reader.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        },

        async handleImageUpload(blobInfo, progress, failure) {
            const formData = new FormData();
            formData.append("file", blobInfo.blob(), blobInfo.filename());

            try {
                const response = await axios.post(
                    route('upload'),
                    formData,
                    {
                        headers: { "Content-Type": "multipart/form-data" },
                        onUploadProgress: (progressEvent) => {
                            const percentCompleted = Math.round(
                                (progressEvent.loaded * 100) / progressEvent.total
                            );
                            if (progress && typeof progress === "function") {
                                progress(percentCompleted);
                            }
                        },
                    }
                );

                if (response.status === 403) {
                    throw new Error("HTTP Error: " + response.status);
                }

                if (response.status < 200 || response.status >= 300) {
                    throw new Error("HTTP Error: " + response.status);
                }

                const json = response.data;

                if (!json || typeof json.location !== "string") {
                    throw new Error("Invalid JSON: " + JSON.stringify(json));
                } else response;
                {
                    return json.location;
                }
            } catch (error) {
                if (failure && typeof failure === "function") {
                    failure(error.message);
                }
            }
        }
    }
}
</script>
