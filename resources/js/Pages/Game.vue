<template>
    <div class="min-h-screen bg-gray-100 flex flex-col gap-6 justify-center items-center p-1">
        <div class="sm:max-h-[26rem] max-w-full sm:max-w-4xl sm:h-screen w-full">
            <div class="flex flex-col-reverse sm:flex-row sm:justify-center h-full gap-10 sm:gap-16">
                <div class="aspect-5/4 w-full sm:w-auto h-auto sm:h-full p-6 sm:p-12 border-2 border-black">
                    <svg id="logo-svg" v-if="logoSvg" v-html="logoSvg" class="h-full w-full stroked" ref="logoSvg"></svg>
                </div>
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-none sm:grid-rows-3 sm:h-full sm:w-36">
                    <div class="flex flex-col gap-1 justify-center items-center border-2 border-black p-1">
                        <span class="text-5xl sm:text-7xl font-bold">{{ timer || 0 }}</span>
                        <span class="text-sm sm:text-lg">TIMER</span>
                    </div>
                    <div class="flex flex-col gap-1 justify-center items-center border-2 border-black p-1">
                        <span class="text-5xl sm:text-7xl font-bold">{{ guesses }}</span>
                        <span class="text-sm sm:text-lg">GUESSES</span>
                    </div>
                    <div class="flex flex-col gap-1 justify-center items-center border-2 border-black p-1">
                        <span class="text-5xl sm:text-7xl font-bold">{{ points }}</span>
                        <span class="text-sm sm:text-lg">POINTS</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full max-w-2xl px-2 flex justify-center" v-if="hasHint || hint">
            <button
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 border-2 border-gray-800 font-bold py-2 px-3 rounded flex"
                v-if="hasHint"
                @click="getHint"
                :disabled="actionsDisabled"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="1.5em" height="1.5em">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                    </path>
                </svg>&nbsp;<span class="mt-0.5">Hint</span>
            </button>
            <p v-if="hint" class="w-full text-center text-lg"><span class="font-semibold">Hint:</span> {{ hint }}</p>
        </div>
        <div class="flex flex-col items-center w-full border-2 border-black max-w-3xl p-2">
            <div
                v-if="charLength"
                class="px-1 grid gap-1 sm:gap-2 max-w-full sm:max-w-lg"
                :style="`grid-template-columns: repeat(${charLength}, minmax(0, 1fr));`"
            >
                <input
                    v-for="index in charLength" :key="index"
                    class="text-center uppercase text-xl font-bold p-0 max-w-[32px] form-control border-2 border-black rounded focus:shadow-outline"
                    :class="{
                        'h-10': charLength < 12,
                        'h-7 md:h-10': charLength > 11
                    }"
                    type="text"
                    maxlength="1"
                    ref="guess"
                    @keydown.prevent="handleKeydown($event, index)"
                />
            </div>
            <div class="mt-3 flex gap-3" v-if="charLength">
                <primary-button
                    type="button"
                    :disabled="actionsDisabled"
                    @click="guessWord"
                >
                    Submit
                </primary-button>
                <primary-button
                    type="button"
                    :disabled="actionsDisabled"
                    @click="skipWord"
                >
                    Skip
                </primary-button>
            </div>
        </div>

        <dialog-modal
            :show="showInstructionsModal"
            :closeable="false"
            @close="showInstructionsModal = false"
            max-width="2xl"
            class-name="mt-40 sm:mt-32"
        >
            <template #title>
                <span class="font-bold">Instructions:</span>
            </template>

            <template #content>
                <div class="pl-4">
                    <p class="list-item">Fusce laoreet ipsum sed porta egestas.</p>
                    <p class="list-item">Morbi rhoncus lectus ut velit faucibus laoreet.</p>
                    <p class="list-item">Vivamus non ligula in est fringilla efficitur pretium id justo.</p>
                    <p class="list-item">Quisque et sapien tincidunt, pellentesque nibh in, rutrum ex.</p>
                    <p class="list-item">Integer feugiat nisl sed suscipit fringilla.</p>
                </div>
            </template>

            <template #footer>
                <div class="w-full text-center">
                    <primary-button @click="startGame">Start Game</primary-button>
                </div>
            </template>
        </dialog-modal>
    </div>
</template>

<script>
import DialogModal from "@/Components/DialogModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import * as Vivus from "vivus";

export default {
    components: {
        DialogModal,
        PrimaryButton
    },

    data() {
        return {
            showInstructionsModal: true,
            logoSvg: null,
            charLength: null,
            points: 0,
            timer: null,
            timerSetInterval: null,
            guesses: null,
            actionsDisabled: false,
            hint: null,
            hasHint: null,
            drawing: false,
            refreshRate: null
        }
    },

    mounted() {
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
            axios
                .post('/start-game')
                .then((res) => {
                    this.hideInstructionsModal();
                    this.setNewWord(res.data);
                })
                .catch((err) => {

                })
        },

        hideInstructionsModal() {
            this.showInstructionsModal = false;
        },

        setNewWord(data) {
            this.$refs?.logoSvg?.classList?.remove('finished');
            this.logoSvg = data.logo;
            this.charLength = data.charLength;
            this.timer = 30;
            this.guesses = data.guessesRemaining;
            this.hasHint = data.hasHint;
            this.$nextTick(function() {
                this.$refs.guess[0].focus();
                this.animateLogo();
            });
        },

        animateLogo() {
            this.drawing = true;
            const vivus = new Vivus('logo-svg', {
                duration: this.refreshRate * 2,
                type: 'oneByOne'
            }, (obj) => {
                obj.el.classList.add('finished');
                setTimeout(function() {
                    this.drawing = false;
                    this.startTimerInterval();
                }.bind(this), 500)
            });
        },

        handleKeydown(event, index) {
            const keyCode = event.keyCode
            if(keyCode == 8) {
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
                })
        },

        skipWord() {
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
                    this.actionsDisabled = false;
                })
        },

        getHint() {
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
                    this.actionsDisabled = false;
                })
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
            this.points = data.points;
            this.guesses = data.guessesRemaining || 0;
            this.hint = data.hint;
            this.hasHint = data.hasHint;
            this.$refs?.guess?.map((i) => {i.value = ''});
            if(data.gameOver) {
                window.alert(`Game Over. Points: ${data.points}`);
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
            this.$refs?.guess?.[0]?.focus();
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
    #logo-svg * {
        fill-opacity: 0;
        transition: fill-opacity 0.5s;
    }

    #logo-svg.stroked, #logo-svg.stroked *{
        stroke: black;
        stroke-width: 0.4%;
    }

    #logo-svg.finished * {
        fill-opacity: 1;
    }

    .form-control {
      background-color: #F3F6F9;
      transition: all 0.15s ease;
    }

    .form-control:active,
    .form-control:focus {
      background-color: #EBEDF3;
    }
</style>
