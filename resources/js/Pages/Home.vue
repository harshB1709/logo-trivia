<template>
    <game-layout>
        <div class="hero min-h-remaining bg-base-200 ubuntu-mono">
            <div class="hero-content text-center">
                <div class="max-w-4xl">
                    <h1 class="text-5xl font-bold text-primary mb-5 w-fit mx-auto">
                        <img src="/images/ranium-logo-white.png" class="w-32 mx-auto">
                        <span class="h-100 inline-block">Tech Pictionary</span>
                    </h1>
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <img src="/images/iphone-14_low.png" class="max-w-100 sm:max-w-xs rounded-lg img-shadow">
                        <div>
                            <h1 class="text-3xl font-bold mb-4 underline">Play the Game to Win an iPhone 14*</h1>
                            <div class="text-left text-lg">
                                <p class="mb-2">
                                    <span class="font-bold text-primary">Step 1:</span> Register for the game.
                                </p>
                                <p class="mb-2">
                                    <span class="font-bold text-primary">Step 2:</span> Play the game and test your knowledge of the technologies you use regularly by recognizing as many logos as possible to earn points which will increase your chances of winning the latest iPhone 14!
                                </p>
                                <p class="mb-4">
                                    <span class="font-bold text-primary">Step 3:</span> Check the leaderboard to see where you stand against other players.
                                </p>
                            </div>
                            <p class="mb-4 text-primary text-3xl font-bold">Register now and don't miss this opportunity to win big!</p>
                            <a href="/register" class="btn btn-primary">Register</a>
                        </div>
                    </div>
                    <div class="mt-20 text-lg"><span class="font-bold text-primary">*Note:</span> In case of ties or any discrepancies, the final decision will be taken by the Ranium's officials.</div>
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
}
</script>

<style>
    .img-shadow {
        /*-webkit-box-shadow: 0 0 5px 3px #006DF5;
        box-shadow: 0 0 5px 3px #006DF5;*/
    }
</style>
