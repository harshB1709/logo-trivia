<template>
    <div class="min-h-screen bg-gray-100 flex flex-col justify-center sm:py-12">
        <div class="xs:p-0 mx-auto w-11/12 md:w-full md:max-w-md">
            <h1 class="font-bold text-center text-2xl mb-5">Tech Pictionary</h1>
            <div
                class="bg-white shadow w-full rounded-lg divide-y divide-gray-200"
            >
                <div class="p-10 space-y-7">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="displayName" value="Display Name(Optional)" />
                        <TextInput
                            id="displayName"
                            v-model="form.display_name"
                            type="text"
                            class="mt-1 block w-full"
                        />
                        <InputError class="mt-2" :message="form.errors.display_name" />
                    </div>
                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                            autocomplete="email"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div>
                        <InputLabel for="phone" value="Phone No." />
                        <TextInput
                            id="phone"
                            v-model="form.phone"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autocomplete="phone"
                        />
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>
                    <div class="bg-teal-100 border-l-4 border-teal-500 text-teal-700 p-4" v-if="registered" role="alert">
                        <p class="font-bold">Registered Successfully.</p>
                        <p>You have successfully registered for the game. You will shortly receive an email with a game link.</p>
                    </div>
                    <button
                        type="submit"
                        class="transition duration-200 bg-blue-500 hover:bg-blue-600 focus:bg-blue-700 focus:shadow-sm focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50 text-white w-full py-2.5 rounded-lg text-sm shadow-sm hover:shadow-md font-semibold text-center inline-block !mt-8"
                        @click="submit"
                        :disabled="form.processing"
                    >
                        Register
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/inertia-vue3';

export default {
    components: {
        InputLabel,
        TextInput,
        InputError,
        PrimaryButton
    },

    data() {
        return {
            form: useForm({
                name: "",
                display_name: "",
                email: "",
                phone: ""
            })
        }
    },

    props: {
        registered: {
            type: Boolean,
            default: false
        }
    },

    methods: {
        submit() {
            this.form.post('/register', {
                preserveScroll: true,
                onSuccess: () => {
                    this.form.reset();
                },
            })
        }
    }
}
</script>
