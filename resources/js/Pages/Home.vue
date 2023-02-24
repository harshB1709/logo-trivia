<template>
    <game-layout>
        <div class="hero min-h-remaining ubuntu-mono">
            <div class="hero-content text-center">
                <div class="max-w-4xl">
                    <h1 class="text-5xl font-bold text-primary mb-5 w-fit mx-auto">
                        <img src="/images/ranium-logo-white.png" class="w-48 mx-auto my-4">
                        <span class="h-100 inline-block">Tech Pictionary</span>
                    </h1>
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <img src="/images/iphone-14_low.png" class="max-w-100 sm:max-w-xs rounded-lg img-shadow">
                        <div class="p-4 rounded-lg bg-base-200 bg-opacity-90">
                            <h1 class="text-3xl font-bold mb-4 underline">Win an iPhone 14* @ LaraconIN 2023</h1>
                            <div class="text-left text-lg">
                                <p class="mb-2">
                                    <span class="font-bold text-primary"> > </span> Test your knowledge of the technologies by recognizing as many logos as possible and earn points. The more points you earn, the higher your chances of winning the latest iPhone 14!
                                </p>
                                <p class="mb-2">
                                    <span class="font-bold text-primary"> > </span> Wait for the announcement at the end of Day 2 to find out the winner.
                                </p>
                                <p class="mb-4 text-info">
                                    <span class="font-bold">Important: </span>Game ends today by the end of the last tea break!
                                </p>
                            </div>
                            <p class="mb-4 text-primary text-3xl font-bold" v-if="registrationSetting?.value">Register now and don't miss this opportunity to win big!</p>
                            <a href="/register" class="btn btn-primary" v-if="registrationSetting?.value">Register</a>
                            <p v-else class="text-2xl text-error font-semibold">{{ registrationSetting.message }}</p>
                        </div>
                    </div>
                    <div class="mt-20 text-lg p-4 bg-base-200 bg-opacity-90 rounded-lg"><span class="font-bold text-primary">*Note:</span> In case of ties or any disputes, the final decision will be taken by the Ranium.</div>
                </div>
            </div>
        </div>
    </game-layout>
</template>

<script>
import GameLayout from "@/Layouts/GameLayout.vue";
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

export default {
    components: {
        GameLayout,
        AuthenticationCardLogo
    },

    props: {
        registrationSetting: {
            type: Object,
            default: {
                value: true,
                message: ''
            }
        }
    }
}
</script>

<style>
    .img-shadow {
        /*-webkit-box-shadow: 0 0 5px 3px #006DF5;
        box-shadow: 0 0 5px 3px #006DF5;*/
    }
</style>
