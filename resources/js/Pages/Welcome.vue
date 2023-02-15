<template>
    <div class="container min-h-screen flex flex-col justify-center items-center mx-auto px-4">
        <div class="sm:max-w-md w-full rounded-xl flex flex-col items-center">
            <h1 class="font-bold text-center text-3xl mb-5 text-primary">Ranium's Tech Pictionary</h1>
            <div class="w-full flex flex-col bg-base-200 rounded-lg p-4 items-center gap-4">
                <div class="form-control w-full max-w-sm">
                  <label class="label" for="name">
                    <span class="label-text">Name</span>
                  </label>
                  <input type="text" placeholder="Name" v-model="form.name" autocomplete="name" id="name" class="input input-bordered border-base-content w-full" />
                  <InputError class="mt-1 text-error" :message="form.errors.name" />
                </div>
                <div class="form-control w-full max-w-sm">
                  <label class="label" for="display_name">
                    <span class="label-text">Display Name(Optional)</span>
                  </label>
                  <input type="text" placeholder="Display Name" v-model="form.display_name" id="display_name" class="input input-bordered border-base-content w-full" />
                  <InputError class="mt-1 text-error" :message="form.errors.display_name" />
                </div>
                <div class="form-control w-full max-w-sm">
                  <label class="label" for="email">
                    <span class="label-text">Email</span>
                  </label>
                  <input type="text" placeholder="Email" v-model="form.email" id="email" class="input input-bordered border-base-content w-full" />
                  <InputError class="mt-1 text-error" :message="form.errors.email" />
                </div>
                <div class="form-control w-full max-w-sm">
                  <label class="label" for="phone">
                    <span class="label-text">Phone No.</span>
                  </label>
                  <input type="text" placeholder="Phone No." v-model="form.phone" id="phone" class="input input-bordered border-base-content w-full" />
                  <InputError class="mt-1 text-error" :message="form.errors.phone" />
                </div>
                <div class="alert alert-success shadow-lg" v-if="registered">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>
                            You have successfully registered for the game. You will shortly receive an email with your game link.
                        </span>
                    </div>
                </div>
                <button
                    type="submit"
                    @click="submit"
                    :disabled="form.processing"
                    class="btn btn-outline btn-primary mt-4 mb-2"
                    :class="{
                        'loading': form.processing
                    }"
                >
                    Register
                </button>
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
