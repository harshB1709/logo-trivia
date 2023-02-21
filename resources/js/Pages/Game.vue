<template>
    <div class="container min-h-screen flex flex-col justify-center items-center mx-auto ubuntu-mono">
        <div class="sm:max-w-3xl w-full flex flex-col items-center px-4 sm:px-0 gap-2 sm:gap-3">
            <div class="stats stats-horizontal bg-base-content text-base-content grid-rows-2 sm:grid-rows-none w-full gap-[2px] divide-x-0 divide-y-0 border-2 border-base-content no-scrollbar">

                <div class="stat bg-base-100 place-items-center px-3 py-2 sm:px-6 sm:py-4">
                    <div class="stat-title text-xs sm:text-base">Word</div>
                    <div class="stat-value text-2xl sm:text-4xl">
                        <span class="countdown font-mono">
                          <span :style="`--value:${wordNo};`"></span>/{{totalWords}}
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
                        <progress :value="timer" :max="maxTimer" class="progress w-16"></progress>
                    </div> -->
                </div>
                <div class="stat bg-base-100 place-items-center px-3 py-2 sm:px-6 sm:py-4 font-mono">
                    <div class="stat-title text-xs sm:text-base">Points</div>
                    <div class="stat-value relative text-2xl sm:text-4xl w-full">
                        <div class="w-full text-center">{{ points }}</div>
                        <div
                            class="absolute left-0 top-0 w-full text-center text-success"
                            :class="{
                                'animate__animated animate__fadeOutUp': pointsAdded
                            }"
                        >
                            {{ pointsAdded ? `+${pointsAdded}` : '' }}
                        </div>
                    </div>
                </div>
                <div class="stat bg-base-100 place-items-center px-3 py-2 sm:px-6 sm:py-4 font-mono no-scrollbar">
                    <div class="stat-title text-xs sm:text-base">Guesses</div>
                    <div class="stat-value relative text-2xl sm:text-4xl w-full no-scrollbar">
                        <div class="w-full text-center">{{ guesses || '-' }}</div>
                        <div
                            class="absolute left-0 top-0 w-full text-center text-error"
                            :class="{
                                'animate__animated animate__fadeOutDown': guessesDecreased
                            }"
                        >
                            {{ guessesDecreased ? `-${guessesDecreased}` : '' }}
                        </div>
                    </div>
                </div>

            </div>
            <div class="form-control w-44 mx-auto">
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
                                'finished': showColour,
                                'animate__animated animate__fadeOutLeft': showingNext
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
                <p
                    class="w-full text-center text-lg"
                    :class="{
                        'animate__animated animate__bounceIn': hint
                    }"
                    v-if="hint"
                >
                    <span class="font-semibold">Hint:</span> {{ hint }}
                </p>
            </div>

            <div class="flex flex-col items-center w-full mt-4 border-base-content rounded-xl max-w-3xl p-2">
                <div
                    v-if="charLength"
                    class="px-1 grid gap-1 sm:gap-2 max-w-full sm:max-w-lg"
                    :style="`grid-template-columns: repeat(${charLength}, minmax(0, 1fr));`"
                >
                    <input
                        v-for="index in charLength" :key="index"
                        class="input input-bordered border-2 text-center uppercase font-bold p-0 max-w-[32px] rounded"
                        :class="{
                            'h-10 text-xl': charLength < 10,
                            'h-8 sm:h-10 text-lg sm:text-xl': charLength > 9 && charLength < 12,
                            'h-7 sm:h-10 text-base sm:text-xl': charLength > 11,
                            'border-success text-success': pointsAdded,
                            'border-error text-error animate__animated animate__shakeX': guessesDecreased,
                            'border-base-content': !pointsAdded && !guessesDecreased
                        }"
                        type="text"
                        maxlength="1"
                        ref="guess"
                        @keyup.prevent="handleKeydown($event, index)"
                        @keydown.prevent=""
                    />
                </div>
                <div class="text-primary text-lg my-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="w-4 inline-block mr-1">
                        <path d="M32 0C14.3 0 0 14.3 0 32S14.3 64 32 64V75c0 42.4 16.9 83.1 46.9 113.1L146.7 256 78.9 323.9C48.9 353.9 32 394.6 32 437v11c-17.7 0-32 14.3-32 32s14.3 32 32 32H64 320h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V437c0-42.4-16.9-83.1-46.9-113.1L237.3 256l67.9-67.9c30-30 46.9-70.7 46.9-113.1V64c17.7 0 32-14.3 32-32s-14.3-32-32-32H320 64 32zM96 75V64H288V75c0 19-5.6 37.4-16 53H112c-10.3-15.6-16-34-16-53zm16 309c3.5-5.3 7.6-10.3 12.1-14.9L192 301.3l67.9 67.9c4.6 4.6 8.6 9.6 12.2 14.9H112z" stroke="currentColor" fill="currentColor"/>
                    </svg>
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
                        Submit
                    </button>
                </div>
            </div>
        </div>

        <input type="checkbox" :checked="showInstructionsModal" id="start-game-modal" class="modal-toggle" />
        <div class="modal">
            <label class="modal-box w-11/12 max-w-4xl relative" for="">
                <div class="py-4 pl-3 md:pl-7">
                    <template v-if="gameStartedTimer === null">
                        <p class="w-full text-center text-2xl font-bold mb-4 underline underline-offset-2 text-primary">PLEASE READ THESE INSTRUCTIONS VERY CAREFULLY!!</p>
                        <p class="list-item">You need to play the game in one go. Do NOT reload the page once the game starts.</p>
                        <p class="list-item">You will be presented with {{ totalWords }} logos of various software development technologies.</p>
                        <p class="list-item">Each logo has a point value, with the first {{ wordsPerPoint }} logos being worth 1 point, the next {{ wordsPerPoint }} being worth 2 points, and the last {{ wordsPerPoint }} being worth 3 points.</p>
                        <p class="list-item">Once a logo is displayed, a {{maxTimer}}-second timer will start. You have three attempts to guess the name of the technology and enter it into the input box before the timer runs out.</p>
                        <p class="list-item">The points you score for each technology will be calculated as follows: (remaining_number_of_guesses + remaining_seconds_on_the_timer) x logo_points.</p>
                        <p class="list-item">This means that your score will be directly influenced by the points associated with the logo, how quickly you answer, and the number of guesses you have left.</p>
                        <p class="list-item">Hints are available for some logos at the cost of one guess.</p>
                        <p class="list-item">When the game ends, your final score will be calculated and displayed.</p>
                        <p class="list-item"><span class="font-bold text-lg text-primary">Important:</span> All word scoring and timing calculations are performed on the server. Ensure a stable internet connection before playing. Good luck!</p>
                        <p class="list-item">If there is a tie, Ranium will do a lucky draw to decide the winner.</p>
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
                    <p class="mt-6">Please wait for the announcement to find out the winner!</p>
                </div>
                <div class="modal-action justify-center mt-2 gap-2">
                    <button class="btn btn-outline btn-primary" @click="openAboutModal">About</button>
                </div>
            </label>
        </div>

        <about-modal
            :modelValue="showAboutModal"
            @update:modelValue="handleAboutModalShow"
        />
    </div>
</template>

<script>
import DialogModal from "@/Components/DialogModal.vue";
import AboutModal from "@/Components/AboutModal.vue"
import Vivus from "vivus";
import 'animate.css';

export default {
    components: {
        DialogModal,
        AboutModal
    },

    data() {
        return {
            showInstructionsModal: false,
            showGameOverModal: false,
            showAboutModal: false,
            logoSvg: null,
            charLength: null,
            points: 0,
            pointsAdded: null,
            timer: null,
            timerSetInterval: null,
            guesses: null,
            guessesDecreased: null,
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
            showingNext: false,
            lastAction: null,
        }
    },

    props: {
        totalWords: {
            type: Number,
            required: true
        },
        guessesPerWord: {
            type: Number,
            required: true
        },
        maxTimer: {
            type: Number,
            required: true
        },
    },

    computed: {
        wordsPerPoint() {
            return this.totalWords/3
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
            this.timer = this.maxTimer;
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
                        this.lastAction = 'guessWord';
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
                        this.lastAction = 'skipWord';
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
                        this.lastAction = 'getHint';
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
            }.bind(this), 1000);
            this.points = data.points;
            if(this.lastAction === 'guessWord' && (this.guesses > (data.guessesRemaining || 0) || this.guesses === 1 && data.guessesRemaining === 3 && data.wordChange)) {
                this.guessesDecreased = 1;
                setTimeout(function() {
                    this.guessesDecreased = null;
                }.bind(this), 1000);
            }
            this.guesses = data.guessesRemaining || 0;
            this.hint = data.hint;
            this.hasHint = data.hasHint;
            setTimeout(function() {
                this.$refs?.guess?.map((i) => {i.value = ''});
            }.bind(this), 1000);
            if(data.gameOver) {
                this.showGameOverModal = true;
                this.clearTimerInterval();
                this.logoSvg = null;
                this.charLength = null;
                this.timer = null;
                this.guesses = 0;
            }
            else if(data.wordChange) {
                this.showingNext = true;
                setTimeout(function() {
                    this.showingNext = false;
                    this.setNewWord(data);
                    this.$refs?.guess?.map((i) => {i.value = ''});
                }.bind(this), 1000);
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
        },

        openAboutModal() {
            this.showAboutModal = true;
            this.showGameOverModal = false;
        },

        handleAboutModalShow(val) {
            this.showAboutModal = false;
            if(val === false) {
                this.showGameOverModal = true;
            }
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
