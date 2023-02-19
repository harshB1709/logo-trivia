<template>
    <div class="min-h-screen ubuntu-mono">
        <div class="navbar bg-base-200 justify-between">
            <div class="navbar-start">
                <a class="btn btn-ghost normal-case text-xl text-primary" href="/">Ranium's Tech Pictionary</a>
            </div>
            <div class="dropdown dropdown-end lg:hidden">
                <label tabindex="0" class="btn btn-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
                </label>
                <ul tabindex="0" class="menu menu-compact dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a href="javascript:void(0);" @click="openAboutModal">About</a></li>
                    <li><a href="/register">Register</a></li>
                    <li><a href="/leaderboard">Leaderboard</a></li>
                </ul>
            </div>
            <div class="hidden lg:flex">
                <ul class="menu menu-horizontal px-1">
                    <li><a href="javascript:void(0);" @click="openAboutModal">About</a></li>
                    <li><a href="/register">Register</a></li>
                    <li><a href="/leaderboard">Leaderboard</a></li>
                </ul>
            </div>
        </div>
        <slot />
    </div>

    <about-modal
        v-model="showAboutModal"
    />
</template>
<script>
import AboutModal from "@/Components/AboutModal.vue"

export default {
    components: {
        AboutModal
    },

    data() {
        return {
            showAboutModal: false
        }
    },

    methods: {
        openAboutModal() {
            this.showAboutModal = true;
        }
    }
}
</script>
