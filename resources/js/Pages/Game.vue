<template>
    <div class="container min-h-screen flex flex-col justify-center items-center mx-auto">
        <div class="sm:max-w-3xl w-full flex flex-col items-center px-4 sm:px-0 gap-2 sm:gap-3">
            <div class="stats stats-horizontal bg-base-content text-base-content grid-rows-2 sm:grid-rows-none w-full gap-[2px] divide-x-0 divide-y-0 border-2 border-base-content">

                <div class="stat bg-base-100 place-items-center px-3 py-2 sm:px-6 sm:py-4">
                    <div class="stat-title text-xs sm:text-base">Word</div>
                    <div class="stat-value text-2xl sm:text-4xl">
                        <span class="countdown font-mono">
                          <span :style="`--value:${wordNo};`"></span>/21
                        </span>
                    </div>
                </div>
                <div class="stat bg-base-100 place-items-center px-3 py-2 sm:px-6 sm:py-4">
                    <div class="stat-title text-xs sm:text-base">Timer</div>
                    <div class="stat-value text-2xl sm:text-4xl">
                        <span class="countdown font-mono">
                          <span :style="`--value:${timer || 0};`"></span>
                        </span>
                    </div>
                    <!-- <div class="stat-desc text-base-content">
                        <progress :value="timer" max="30" class="progress w-16"></progress>
                    </div> -->
                </div>
                <div class="stat bg-base-100 place-items-center px-3 py-2 sm:px-6 sm:py-4 font-mono">
                    <div class="stat-title text-xs sm:text-base">Points</div>
                    <div class="stat-value relative text-2xl sm:text-4xl w-full">
                        <div class="w-full text-center">{{ points }}</div>
                        <div
                            class="absolute left-0 top-0 w-full text-center"
                            :class="{
                                'animate__animated animate__fadeOutUp': pointsAdded
                            }"
                        >
                            {{ pointsAdded ? `+${pointsAdded}` : '' }}
                        </div>
                    </div>
                </div>
                <div class="stat bg-base-100 place-items-center px-3 py-2 sm:px-6 sm:py-4 font-mono">
                    <div class="stat-title text-xs sm:text-base">Guesses</div>
                    <div class="stat-value text-2xl sm:text-4xl">{{ guesses || '-' }}</div>
                </div>

            </div>
            <div class="form-control w-40 mx-auto">
                    <label class="label cursor-pointer">
                        <span class="label-text text-lg">Show Outlines</span>
                        <input
                            type="checkbox"
                            v-model="showStroke"
                            :disabled="actionsDisabled"
                            class="checkbox checkbox-primary"
                        />
                </label>
            </div>
            <div class="sm:max-h-[23rem] max-w-full sm:max-w-3xl sm:h-screen w-full">
                <div class="flex justify-center h-full gap-10 sm:gap-16">
                    <div class="aspect-5/4 w-full sm:w-auto h-auto sm:h-full p-6 sm:p-7 border-2 border-black bg-white rounded-xl">
                        <div
                            id="logo-svg"
                            v-if="logoSvg"
                            v-html="logoSvg"
                            class="flex h-full w-full"
                            :class="{
                                'stroked': showStroke || showStrokeInternal,
                                'finished': showColour
                            }"
                            ref="logoSvg"
                        />
                    </div>
                </div>
            </div>

            <div class="w-full max-w-md flex justify-end" v-if="hasHint || hint">
                <button
                    class="btn gap-1 btn-outline btn-primary btn-sm md:btn-md"
                    v-if="hasHint"
                    @click="getHint"
                    :disabled="actionsDisabled"
                    ref="hintButton"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="1.5em" height="1.5em">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                        </path>
                    </svg>
                    <span class="mt-0.5">Hint</span>
                </button>
                <p class="w-full text-center text-lg" v-if="hint"><span class="font-semibold">Hint:</span> {{ hint }}</p>
            </div>

            <div class="flex flex-col items-center w-full mt-4 border-base-content rounded-xl max-w-3xl p-2">
                <div
                    v-if="charLength"
                    class="px-1 grid gap-1 sm:gap-2 max-w-full sm:max-w-lg"
                    :style="`grid-template-columns: repeat(${charLength}, minmax(0, 1fr));`"
                >
                    <input
                        v-for="index in charLength" :key="index"
                        class="input input-bordered border-base-content border-2 text-center uppercase font-bold p-0 max-w-[32px] rounded"
                        :class="{
                            'h-10 text-xl': charLength < 10,
                            'h-8 sm:h-10 text-lg sm:text-xl': charLength > 9 && charLength < 12,
                            'h-7 sm:h-10 text-base sm:text-xl': charLength > 11
                        }"
                        type="text"
                        maxlength="1"
                        ref="guess"
                        @keyup.prevent="handleKeydown($event, index)"
                    />
                </div>
                <div class="text-primary text-lg my-3">Time Remaining:
                    <span class="countdown font-mono">
                        <span :style="`--value:${timer || 0};`"></span>
                    </span>
                </div>
                <div class="btn-group" v-if="charLength">
                    <button
                        class="btn btn-md btn-outline w-28 md:btn-wide"
                        type="button"
                        :disabled="actionsDisabled"
                        @click="skipWord"
                        ref="skipButton"
                    >
                        Skip
                    </button>
                    <button
                        class="btn btn-md btn-outline btn-primary w-28 md:btn-wide"
                        type="button"
                        :disabled="actionsDisabled"
                        @click="guessWord"
                        ref="guessButton"
                    >
                        Guess
                    </button>
                </div>
            </div>
        </div>

        <input type="checkbox" :checked="showInstructionsModal" id="start-game-modal" class="modal-toggle" />
        <div class="modal">
            <label class="modal-box w-11/12 max-w-4xl relative" for="">
                <h3 class="text-2xl underline text-primary font-bold md:pl-4" v-if="gameStartedTimer === null">Instructions:</h3>
                <div class="py-4 pl-3 md:pl-7">
                    <template v-if="gameStartedTimer === null">
                        <p class="w-full text-center text-2xl font-bold mb-4 underline underline-offset-2">PLEASE READ THESE INSTRUCTIONS VERY CAREFULLY!!</p>
                        <p class="list-item">In this game, you will be presented with 21 logos of various software development technologies.</p>
                        <p class="list-item">Each logo has a point value, with the first 7 logos being worth 1 point, the next 7 being worth 2 points, and the last 7 being worth 3 points.</p>
                        <p class="list-item">Once a logo is displayed, a 30-second timer will start. You have three attempts to guess the name of the technology and enter it into the input box before the timer runs out.</p>
                        <p class="list-item">The points you score for each technology will be calculated as follows: (logo_points + remaining_seconds_on_the_timer) x remaining_number_of_guesses.</p>
                        <p class="list-item">This means that your score will be directly influenced by the points associated with the logo, how quickly you answer, and the number of guesses you have left.</p>
                        <p class="list-item">Hints are available for some logos at the cost of one guess.</p>
                        <p class="list-item">When the game ends, your final score will be calculated and displayed.</p>
                        <p class="list-item"><span class="font-bold text-lg underline">Important</span>: All word scoring and timing calculations are performed on the server. Ensure a stable internet connection before playing. Good luck!</p>
                    </template>
                    <template v-else>
                        <div class="w-full flex flex-col items-center gap-6">
                            <h3 class="text-2xl font-bold text-primary">STARTING IN</h3>
                            <div class="radial-progress bg-primary text-primary-content border-4 border-primary" :style="`--value:${(gameStartedTimer*100)/300}; --size:12rem; --thickness: 2rem;`">
                                <span class="countdown font-mono font-bold text-6xl">
                                    <span :style="`--value:${Math.round(gameStartedTimer/86)};`"></span>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="modal-action justify-center" v-if="gameStartedTimer === null">
                    <button class="btn btn-outline btn-primary btn-lg" @click="startGame" ref="startGame">Start Game</button>
                </div>
            </label>
        </div>

        <input type="checkbox" :checked="showGameOverModal" id="start-game-modal" class="modal-toggle" />
        <div class="modal">
            <label class="modal-box relative" for="">
                <h3 class="text-2xl font-bold text-center text-primary">Game Over!</h3>
                <div class="py-4 w-full flex flex-col justify-center text-center">
                    <p class="text-2xl">Final Score</p>
                    <h3 class="font-bold text-primary text-6xl mt-4">{{ points }}</h3>
                    <p class="mt-6">Check the leaderboard to check where you stand!</p>
                </div>
                <div class="modal-action justify-center mt-2">
                    <button class="btn btn-outline btn-primary" @click="$inertia.visit('/leaderboard')">Leaderboard</button>
                </div>
            </label>
        </div>
    </div>
</template>

<script>
import DialogModal from "@/Components/DialogModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Vivus from "vivus";
import 'animate.css';

export default {
    components: {
        DialogModal,
        PrimaryButton
    },

    data() {
        return {
            showInstructionsModal: false,
            showGameOverModal: false,
            logoSvg: null,
            charLength: null,
            points: 0,
            pointsAdded: null,
            timer: null,
            timerSetInterval: null,
            guesses: null,
            actionsDisabled: false,
            hint: null,
            hasHint: false,
            drawing: false,
            refreshRate: null,
            wordNo: 0,
            gameStartedTimer: null,
            gameStartedTimerSetInterval: null,
            showStroke: true,
            showStrokeInternal: false,
            showColour: false,
        }
    },

    mounted() {
        setTimeout(function() {
            this.showInstructionsModal = true;
        }.bind(this), 500)

        this.getFPS()
            .then((fps) => {
                this.refreshRate = fps ?? 60;
            })
            .catch((err) => {
                this.refreshRate = 60;
            });
    },

    methods: {
        startGame() {
            this.$refs.startGame?.classList?.add('loading');
            axios
                .post('/start-game')
                .then((res) => {
                    this.gameStartedTimer = 300;
                    this.gameStartedTimerSetInterval = setInterval(function() {
                        if(this.gameStartedTimer > 0) {
                            this.gameStartedTimer--;
                        }
                        if(this.gameStartedTimer === 0) {
                            this.hideInstructionsModal();
                            this.setNewWord(res.data);
                            clearInterval(this.gameStartedTimerSetInterval);
                            this.gameStartedTimerSetInterval = null;
                        }
                    }.bind(this), 10)
                })
                .catch((err) => {

                })
                .finally(() => {
                    this.$refs.startGame?.classList?.remove('loading');
                })
        },

        hideInstructionsModal() {
            this.showInstructionsModal = false;
        },

        setNewWord(data) {
            this.showColour = false;
            this.logoSvg = data.logo;
            this.charLength = data.charLength;
            this.timer = 30;
            this.guesses = data.guessesRemaining;
            this.hasHint = data.hasHint;
            this.$nextTick(function() {
                this.focusOnInput();
                this.animateLogo();
            });
            this.wordNo++;
        },

        focusOnInput() {
            if(this.$refs?.guess?.[0])
                this.$refs.guess[0].readOnly = true;
            this.$nextTick(function() {
                this.$refs.guess[0].focus();
                if(this.$refs?.guess?.[0])
                    this.$refs.guess[0].readOnly = false;
            });
        },

        animateLogo() {
            this.drawing = true;
            this.actionsDisabled = true;
            this.showStrokeInternal = true;
            const vivus = new Vivus(this.$refs?.logoSvg?.querySelector('svg'), {
                duration: 165,
                type: 'oneByOne'
            }, (obj) => {
                this.showColour = true;
                setTimeout(function() {
                    this.drawing = false;
                    this.actionsDisabled = false;
                    this.showStrokeInternal = false;
                    this.startTimerInterval();
                }.bind(this), 500)
            });
        },

        handleKeydown(event, index) {
            const keyCode = event.keyCode
            if(keyCode == 229) {
                const val = event.target.value;
                const found = val.match(/[A-Za-z0-9]/g);
                if(found?.length) {
                    event.target.value = found[found.length - 1];
                    this.$refs.guess[Math.min(index, this.charLength - 1)].focus();
                }
                else {
                    this.$refs.guess[Math.max(index - 2, 0)].focus();
                }
            }
            else if(keyCode == 8) {
                this.$refs.guess[index - 1].value = "";
                this.$nextTick(function() {
                    this.$refs.guess[Math.max(index - 2, 0)].focus();
                });
            }
            else if((keyCode > 47 && keyCode < 58) || (keyCode > 64 && keyCode < 91)) {
                this.$refs.guess[index - 1].value = String.fromCharCode(keyCode);
                this.$nextTick(function() {
                    this.$refs.guess[Math.min(index, this.charLength - 1)].focus();
                });
            }
            else if((index === this.charLength) && (keyCode === 13)) {
                this.guessWord();
            }
        },

        guessWord() {
            if(!this.actionsDisabled) {
                this.$refs.guessButton?.classList?.add('loading');
                this.clearTimerInterval();
                this.actionsDisabled = true;
                axios
                    .post('/game-action', {
                        action: 'guessWord',
                        guess: this.$refs.guess.map(i => i.value).join('')
                    })
                    .then((res) => {
                        this.handleGameActionResponse(res.data)
                    })
                    .finally(()=> {
                        this.actionsDisabled = false;
                        this.$refs.guessButton?.classList?.remove('loading');
                    })
            }
        },

        skipWord() {
            if(!this.actionsDisabled) {
                this.$refs.skipButton?.classList?.add('loading');
                this.clearTimerInterval();
                this.actionsDisabled = true;
                axios
                    .post('/game-action', {
                        action: 'skipWord'
                    })
                    .then((res) => {
                        this.handleGameActionResponse(res.data)
                    })
                    .finally(()=> {
                        this.$refs.skipButton?.classList?.remove('loading');
                        this.actionsDisabled = false;
                    })
            }
        },

        getHint() {
            if(!this.actionsDisabled) {
                this.$refs.hintButton?.classList?.add('loading');
                this.clearTimerInterval();
                this.actionsDisabled = true;
                axios
                    .post('/game-action', {
                        action: 'getHint'
                    })
                    .then((res) => {
                        this.handleGameActionResponse(res.data)
                    })
                    .finally(()=> {
                        this.$refs.hintButton?.classList?.remove('loading');
                        this.actionsDisabled = false;
                    })
            }
        },

        startTimerInterval() {
            if(!this.timerSetInterval && !this.drawing) {
                this.timerSetInterval = setInterval(function() {
                    if(this.timer > 0) {
                        this.timer--;
                    }
                    if(this.timer === 0) {
                        this.clearTimerInterval();
                        this.skipWord();
                    }
                }.bind(this), 1000)
            }
        },

        clearTimerInterval() {
            if(this.timerSetInterval) {
                clearInterval(this.timerSetInterval);
                this.timerSetInterval = null;
            }
        },

        handleGameActionResponse(data) {
            if(data?.status === 'redirect') {
                window.location.href = data?.redirect || '';
            }
            this.pointsAdded = data.points - this.points;
            setTimeout(function() {
                this.pointsAdded = null;
            }.bind(this), 600);
            this.points = data.points;
            this.guesses = data.guessesRemaining || 0;
            this.hint = data.hint;
            this.hasHint = data.hasHint;
            this.$refs?.guess?.map((i) => {i.value = ''});
            if(data.gameOver) {
                this.showGameOverModal = true;
                this.clearTimerInterval();
                this.logoSvg = null;
                this.charLength = null;
                this.timer = null;
                this.guesses = 0;
            }
            else if(data.wordChange) {
                this.setNewWord(data);
            }
            else {
                this.startTimerInterval();
            }
            this.focusOnInput();
        },

        getFPS() {
            return new Promise(resolve =>
              requestAnimationFrame(t1 =>
                requestAnimationFrame(t2 => resolve(1000 / (t2 - t1)))
              )
            )
        }
    }
}
</script>
<style>
    #logo-svg, #logo-svg svg{
        margin-left: auto;
        margin-right: auto;
    }

    #logo-svg * {
        fill-opacity: 0;
        transition: fill-opacity 0.5s;
    }

    #logo-svg.stroked *{
        stroke: black;
        stroke-width: 0.5%;
    }

    #logo-svg.finished * {
        fill-opacity: 1;
    }
</style>
