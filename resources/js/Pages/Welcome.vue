<template>
    <div class="container min-h-screen flex flex-col justify-center items-center mx-auto px-4 ubuntu-mono">
        <div class="sm:max-w-md w-full rounded-xl flex flex-col items-center">
            <h1 class="whitespace-nowrap font-bold text-center text-3xl mb-2 text-primary heading">
                <svg version="1.0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200.000000 200.000000"
                 preserveAspectRatio="xMidYMid meet" class="w-14 inline-block pb-2">

                <g transform="translate(0.000000,200.000000) scale(0.100000,-0.100000)"
                fill="#000000" stroke="none">
                <path d="M292 1570 c-63 -12 -98 -33 -118 -71 -29 -57 -11 -247 38 -396 123
                -372 469 -606 980 -662 164 -18 175 -15 76 23 -180 68 -326 196 -423 371 -55
                99 -55 101 10 108 142 15 237 137 207 267 -41 176 -167 297 -363 345 -76 19
                -330 28 -407 15z"/>
                <path d="M1167 1468 c60 -65 113 -193 113 -267 -2 -104 -87 -218 -208 -276
                l-33 -16 38 -70 c51 -94 122 -172 210 -230 117 -76 252 -119 374 -119 125 0
                156 40 146 189 -21 286 -154 526 -381 683 -66 46 -234 128 -261 128 -17 0 -17
                -2 2 -22z" fill="#006df5"/>
                </g>
                </svg>
                Ranium's Tech Pictionary</h1>
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
                    <span class="label-text">Phone No.(Optional)</span>
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
                            Thank you! Please check your email inbox for the the link to start the game.
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
