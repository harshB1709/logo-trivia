<template>
    <game-layout>
        <div class="hero min-h-remaining bg-base-200">
            <div class="hero-content text-center">
                <div class="max-w-3xl">
                    <h1 class="text-5xl font-bold text-primary mb-3">
                        <svg version="1.0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200.000000 200.000000"
                         preserveAspectRatio="xMidYMid meet" class="w-14 inline-block mr-3 pb-2">

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
                        <span class="h-100 inline-block">Ranium's Tech Pictionary</span>
                    </h1>
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <img src="/images/iphone-14.png" class="max-w-100 sm:max-w-xs rounded-lg shadow-2xl">
                        <div>
                            <p class="py-3 text-xl">Play the game and get a change to win an Iphone 14.</p>
                            <button class="btn btn-primary" @click="$inertia.visit('/register')">Register</button>
                        </div>
                    </div>
                    <div class="mt-20 text-lg"><span class="font-bold">Note:</span> In case of ties or any discrepancies, the final decision will be taken by the Ranium's officials.</div>
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
