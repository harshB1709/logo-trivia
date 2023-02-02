<template>
    <div class="min-h-screen bg-gray-100 flex flex-col gap-6 justify-center items-center p-1">
        <div class="sm:max-h-[26rem] max-w-full sm:max-w-4xl sm:h-screen w-full">
            <div class="flex flex-col-reverse sm:flex-row sm:justify-center h-full gap-10 sm:gap-16">
                <div class="aspect-5/4 w-full sm:w-auto h-auto sm:h-full p-6 sm:p-12 border-2 border-black">
                    <svg id="logo-svg" v-if="logoSvg" v-html="logoSvg" class="h-full w-full" ref="logoSvg"></svg>
                </div>
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-none sm:grid-rows-3 sm:h-full sm:w-36">
                    <div class="flex flex-col gap-1 justify-center items-center border-2 border-black p-1">
                        <span class="text-5xl sm:text-7xl font-bold">{{ timer }}</span>
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
        <div class="flex flex-col items-center w-full border-2 border-black max-w-3xl p-2">
            <div
                v-if="charLength"
                class="px-1 grid gap-1 sm:gap-2 max-w-full sm:max-w-lg"
                :style="`grid-template-columns: repeat(${charLength}, minmax(0, 1fr));`"
            >
                <input
                    v-for="index in charLength" :key="index"
                    class="text-center uppercase text-xl font-bold p-0 h-10 max-w-[32px] form-control border-2 border-black rounded  focus:shadow-outline"
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
            actionsDisabled: false
        }
    },

    methods: {
        startGame() {
            axios
                .post('/start-game')
                .then((res) => {
                    this.hideInstructionsModal();
                    this.setNewWord(res.data.logo, res.data.charLength);
                })
                .catch((err) => {

                })
        },

        hideInstructionsModal() {
            this.showInstructionsModal = false;
        },

        setNewWord(logoSvg, charLength) {
            this.$refs?.logoSvg?.classList?.remove('finished');
            this.logoSvg = logoSvg;
            this.charLength = charLength;
            this.timer = 30;
            this.guesses = 5;
            this.$nextTick(function() {
                this.$refs.guess[0].focus();
                this.animateLogo();
            });
        },

        animateLogo() {
            const vivus = new Vivus('logo-svg', {
                duration: 165,
                type: 'oneByOne'
            }, (obj) => {
                obj.el.classList.add('finished');
                setTimeout(function() {
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

        startTimerInterval() {
            this.timerSetInterval = setInterval(function() {
                if(this.timer > 0) {
                    this.timer--;
                }
                if(this.timer === 0) {
                    this.clearTimerInterval();
                    this.skipWord();
                }
            }.bind(this), 1000)
        },

        clearTimerInterval() {
            if(this.timerSetInterval) {
                clearInterval(this.timerSetInterval)
            }
        },

        handleGameActionResponse(data) {
            this.points = data.points;
            this.guesses = data.guessesRemaining || 0;
            this.$refs?.guess?.map((i) => {i.value = ''});
            if(data.gameOver) {
                window.alert(`Game Over. Points: ${data.points}`);
                this.clearTimerInterval();
                this.logoSvg = null;
                this.charLength = null;
                this.timer = 0;
                this.guesses = 0;
            }
            else if(data.wordChange) {
                this.setNewWord(data.logo, data.charLength);
            }
            else {
                this.startTimerInterval();
            }
            this.$refs?.guess?.[0]?.focus();
        }
    }
}
</script>
<style>
    #logo-svg * {
        fill-opacity: 0;
        transition: fill-opacity 0.5s;
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
