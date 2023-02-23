<template>
    <div
        class="min-w-full min-h-screen ubuntu-mono relative grid gap-px"
        id="wall"
        ref="wall"
        :style="wallCssVars"
        @dblclick="toggleControls"
    >
        <svg class="w-1 h-1 absolute">
            <defs>
                <linearGradient id="gradient" :gradientTransform="`rotate(${strokeColorAngle})`">
                    <stop offset="0%"   :stop-color="strokeColorTab === 1 ? strokeColor : strokeColorGradFrom" />
                    <stop offset="100%" :stop-color="strokeColorTab === 1 ? strokeColor : strokeColorGradTo" />
                </linearGradient>
            </defs>
        </svg>

        <template v-for="col in gridHeight" :key="col">
            <template v-for="row in gridWidth" :key="row">
                <div
                    class="aspect-square svg-container flex justify-center items-center"
                    v-html="svgs[svgArray[row][col]]"
                    :style="{
                        '--rotation': rotationRandom ? `${randomIntFromInterval(rotationRandomFrom, rotationRandomTo)}deg` : 'inherit'
                    }"
                >
                </div>
            </template>
        </template>

        <div
            class="absolute top-1 right-1 w-2/3 md:w-1/3 p-2 md:p-4 rounded-md bg-base-200 space-y-2"
            :class="{
                'hidden': controlsHidden
            }"
            @dblclick.stop=""
        >
            <div class="form-control w-full">
                <label class="label p-px text-xs">
                    <span class="label-text">Logos per row:</span>
                </label>
                <input type="range" min="2" max="30" v-model.number="gridWidth" class="range range-primary range-xs" />
            </div>
            <div class="form-control w-full">
                <label class="label p-px text-xs">
                    <span class="label-text">Logo Size:</span>
                </label>
                <input type="range" min="65" max="98" step="0.5" v-model.number="divSpace" class="range range-primary range-xs" />
            </div>
            <div class="form-control w-full">
                <label class="label p-px text-xs">
                    <span class="label-text">Stroke Width:</span>
                </label>
                <input type="range" min="0.1" max="4" step="0.1" v-model.number="strokeWidth" class="range range-primary range-xs" />
            </div>
            <div class="flex flex-col md:flex-row gap-1">
                <div class="form-control flex-1">
                    <label class="label p-px text-xs">
                        <span class="label-text">Background Color:</span>
                    </label>
                    <div class="tabs tabs-boxed mb-1">
                        <a class="tab tab-sm tab-lifted text-primary" @click="bgColorTab = 1" :class="{'tab-active': bgColorTab === 1}">Solid</a>
                        <a class="tab tab-sm tab-lifted text-primary" @click="bgColorTab = 2" :class="{'tab-active': bgColorTab === 2}">Grad.</a>
                    </div>
                    <div class="w-full border border-primary rounded px-1 py-0.5">
                        <template v-if="bgColorTab === 1">
                            <div class="form-control w-full">
                                <label class="label cursor-pointer">
                                    <span class="label-text">Color:</span>
                                    <input
                                        type="color"
                                        v-model="bgColor"
                                        class="input input-xs w-3/5"
                                    />
                                </label>
                            </div>
                        </template>
                        <template v-else-if="bgColorTab === 2">
                            <label class="label cursor-pointer gap-1">
                                <span class="label-text">From:</span>
                                <input type="color" v-model="bgColorGradFrom" class="input input-xs flex-grow" />
                            </label>
                            <label class="label cursor-pointer gap-1">
                                <span class="label-text">To:</span>
                                <input type="color" v-model="bgColorGradTo" class="input input-xs flex-grow" />
                            </label>
                            <label class="label cursor-pointer gap-1">
                                <span class="label-text">Rotn:</span>
                                <input type="range" min="0" max="360" v-model.number="bgColorAngle" class="range range-primary range-xs flex-grow" />
                            </label>
                        </template>
                    </div>
                </div>

                <div class="form-control flex-1">
                    <label class="label p-px text-xs">
                        <span class="label-text">Stroke Color:</span>
                    </label>
                    <div class="tabs tabs-boxed mb-1">
                        <a class="tab tab-sm tab-lifted text-primary" @click="strokeColorTab = 1" :class="{'tab-active': strokeColorTab === 1}">Solid</a>
                        <a class="tab tab-sm tab-lifted text-primary" @click="strokeColorTab = 2" :class="{'tab-active': strokeColorTab === 2}">Grad.</a>
                    </div>
                    <div class="w-full border border-primary rounded px-1 py-0.5">
                        <template v-if="strokeColorTab === 1">
                            <div class="form-control w-full">
                                <label class="label cursor-pointer">
                                    <span class="label-text">Color:</span>
                                    <input
                                        type="color"
                                        v-model="strokeColor"
                                        class="input input-xs w-3/5"
                                    />
                                </label>
                            </div>
                        </template>
                        <template v-else-if="strokeColorTab === 2">
                            <label class="label cursor-pointer gap-1">
                                <span class="label-text">From:</span>
                                <input type="color" v-model="strokeColorGradFrom" class="input input-xs flex-grow" />
                            </label>
                            <label class="label cursor-pointer gap-1">
                                <span class="label-text">To:</span>
                                <input type="color" v-model="strokeColorGradTo" class="input input-xs flex-grow" />
                            </label>
                            <label class="label cursor-pointer gap-1">
                                <span class="label-text">Rotn:</span>
                                <input type="range" min="0" max="360" v-model.number="strokeColorAngle" class="range range-primary range-xs flex-grow" />
                            </label>
                        </template>
                    </div>
                </div>
            </div>
            <div class="form-control w-full">
                <label class="label cursor-pointer gap-1 pb-1">
                    <span class="label-text whitespace-nowrap">Logo Rotation:</span>
                    <input type="range" min="-90" max="90" v-model.number="rotation" class="range range-primary range-xs flex-grow" />
                </label>
                <p class="w-full text-xl font-semibold text-center text-primary">OR</p>
                <div class="w-full">
                    <div class="form-control w-36">
                        <label class="label cursor-pointer pt-1">
                            <span class="label-text">Random Rotation</span>
                            <input type="checkbox" checked="checked" class="checkbox checkbox-primary checkbox-sm" v-model="rotationRandom"/>
                        </label>
                    </div>
                    <div class="flex flex-col md:flex-row gap-1" v-if="rotationRandom">
                        <div class="form-control flex-1 border border-primary">
                            <label class="label cursor-pointer justify-center gap-1">
                                <span class="label-text">From:</span>
                                <input type="number" class="input input-bordered input-sm w-20" v-model.number="rotationRandomFrom"/>
                            </label>
                        </div>
                        <div class="form-control flex-1 border border-primary">
                            <label class="label cursor-pointer justify-center gap-1">
                                <span class="label-text">To:</span>
                                <input type="number" class="input input-bordered input-sm w-20" v-model.number="rotationRandomTo"/>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-control w-36">
                <label class="label cursor-pointer">
                    <span class="label-text">Alternate Blank</span>
                    <input type="checkbox" checked="checked" class="checkbox checkbox-primary checkbox-sm" v-model="alternateBlank"/>
                </label>
            </div>
            <div class="form-control w-40">
                <label class="label cursor-pointer">
                    <span class="label-text">Diagonally Same</span>
                    <input type="checkbox" checked="checked" class="checkbox checkbox-primary checkbox-sm" v-model="diagonallySame"/>
                </label>
            </div>
            <div class="w-full flex justify-center gap-2">
                <button class="btn btn-primary btn" @click="updateKey">Shuffle</button>
                <button class="btn btn-primary btn" @click="captureImage">Capture</button>
            </div>
            <p class="w-full text-[10px] text-primary text-center">Double Click on the wall to hide the controls.</p>
        </div>
    </div>
</template>

<script>
import * as htmlToImage from 'html-to-image';
import { toBlob } from 'html-to-image';
import { saveAs } from 'file-saver';

export default {
    components: {
    },

    created() {
      window.addEventListener("resize", this.updateKey);
    },
    destroyed() {
      window.removeEventListener("resize", this.updateKey);
    },

    mounted() {
        this.updateKey();
    },

    computed: {
        gridHeight() {
            this.refreshKey;
            const h = Math.ceil((screen.height * this.gridWidth)/screen.width) + 4;
            // console.log(h, screen.width, screen.height);
            return h;
        },

        wallCssVars() {
            return {
                '--grid-width': this.gridWidth,
                '--wall-color-from': this.bgColorTab === 1 ? this.bgColor : this.bgColorGradFrom,
                '--wall-color-to': this.bgColorTab === 1 ? this.bgColor : this.bgColorGradTo,
                '--wall-color-angle': `${this.bgColorAngle}deg`,
                '--div-padding': `${100 - this.divSpace}%`,
                '--stroke-width': `${this.strokeWidth}%`,
                '--stroke-color': this.strokeColor,
                '--rotation': `${this.rotation}deg`
            }
        },

        svgArray() {
            this.refreshKey;
            const arr = this.shuffle(Array.from({length: this.svgs.length}, (_, index) => index));
            const width = this.gridWidth + 2;
            const height = this.gridHeight + 2;
            let rowAlt = false;
            let colAlt = false;
            let count = 0;

            let result = [...Array(width)].map(e => Array(height));

            for(let i = 1; i < width - 1; i++) {
                rowAlt = this.alternateBlank ? !rowAlt : true;
                colAlt = !rowAlt;

                count = arr.length - (parseInt(this.alternateBlank ? ((i-1)/2) : i) % (arr.length));

                for(let j = 1; j < height - 1; j++) {
                    colAlt = this.alternateBlank ? !colAlt : true;

                    if(colAlt) {
                        if(this.diagonallySame) {
                            const prev = result[i-1][j-1];
                            result[i][j] = prev ?? arr[(count++) % arr.length];
                        }
                        else {
                            const filtered = arr.filter(x => ![result[i][j-1], result[i-1][j], result[i-1][j-1], result[i-1][j+1]].includes(x));
                            result[i][j] = filtered[this.randomFromArray(filtered.length)]
                        }
                    }
                }
            }
            // console.table(result);
            return result;
        }
    },

    data() {
        return {
            gridWidth: 13,
            divSpace: 75,
            refreshKey: 0,
            strokeWidth: 2,
            strokeColorTab: 1,
            strokeColor: "#d3d3d3",
            strokeColorGradFrom: "#d3d3d3",
            strokeColorGradTo: "#d3d3d3",
            strokeColorAngle: 90,
            bgColorTab: 1,
            bgColor: "#212121",
            bgColorGradFrom: "#212121",
            bgColorGradTo: "#212121",
            bgColorAngle: 90,
            rotation: 0,
            rotationRandom: false,
            rotationRandomFrom: -10,
            rotationRandomTo: 10,
            alternateBlank: false,
            diagonallySame: true,
            controlsHidden: false
        }
    },

    props: {
        svgs: {
            type: Object,
            required: true
        }
    },

    methods: {
        updateKey() {
            this.refreshKey = this.refreshKey + [-1,1][Math.floor(Math.random() * 2)];
        },

        randomFromArray(len) {
            return Math.floor(Math.random()*len);
        },

        randomIntFromInterval(min, max) {
            const newMin = Math.min(min, max)
            const newMax = Math.max(min, max)
            return Math.floor(Math.random() * (newMax - newMin + 1) + newMin)
        },

        shuffle(array) {
            let currentIndex = array.length,  randomIndex;

            while (currentIndex != 0) {

                randomIndex = Math.floor(Math.random() * currentIndex);
                currentIndex--;

                [array[currentIndex], array[randomIndex]] = [array[randomIndex], array[currentIndex]];
            }

            return array;
        },

        toggleControls() {
            this.controlsHidden = !this.controlsHidden;
        },

        captureImage() {
            const currControls = this.controlsHidden;
            this.controlsHidden = true;

            setTimeout(function() {
                htmlToImage.toBlob(this.$refs.wall)
                    .then(function (blob) {
                        saveAs(blob, 'tech-wall.png');
                    })
                    .catch(function (error) {
                        console.error('oops, something went wrong!', error);
                    });

                setTimeout(function() {
                    this.controlsHidden = currControls;
                }.bind(this), 300)
            }.bind(this), 300)
        }
    }
}
</script>
<style>
    #wall {
        grid-template-columns: repeat(var(--grid-width), minmax(0, 1fr));
        background-image: linear-gradient(var(--wall-color-angle), var(--wall-color-from), var(--wall-color-to));
    }

    #wall div.svg-container {
        padding: var(--div-padding);
    }

    #wall .svg-container>svg {
        max-height: 100%;
        max-width: 100%;
        transform: rotate(var(--rotation));
    }

    #wall .svg-container>svg *{
        fill-opacity: 0;
        stroke-width: var(--stroke-width);
        stroke: url(#gradient);
    }
</style>
