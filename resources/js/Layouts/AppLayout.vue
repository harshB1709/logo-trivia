<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Head, Link, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const currentEvent = computed(() => {
    return usePage().props.currentEvent;
})

const appStatus = computed(() => {
    return currentEvent.value ? usePage().props.appSettings['app_status']['value'] : null;
})

const playerRegistration = computed(() => {
    return currentEvent.value ? usePage().props.appSettings['player_registration']['value'] : null;
})

const showLeaderboard = computed(() => {
    return currentEvent.value ? usePage().props.appSettings['show_leaderboard']['value'] : null;
})

const appStatusMessage = computed(() => {
    return currentEvent.value ? usePage().props.appSettings['app_status']['message'] : null;
})

const playerRegistrationMessage = computed(() => {
    return currentEvent.value ? usePage().props.appSettings['player_registration']['message'] : null;
})

const showLeaderboardMessage = computed(() => {
    return currentEvent.value ? usePage().props.appSettings['show_leaderboard']['message'] : null;
})

const toggleSetting = (setting) => {
    const val = usePage().props.appSettings[setting];
    const message = val['value'] ? prompt('Enter the error message to be shown', val['message'] ?? "The game is not available as of now. Please try again later") : true;
    const conf = !val['value'] ? confirm(`Are you sure about toggling the ${setting.replace('_', ' ')}`) : true;
    if(message && conf) {
        const payload = {
            setting
        };

        if(val['value']) {
            payload.message = message;
        }

        const event = usePage().props.currentEvent;
        axios
            .post(route('toggle-setting', {event: event.slug}), payload)
            .then((res) => {
                usePage().props.appSettings[setting]['value'] = res.data.value;
                usePage().props.appSettings[setting]['message'] = res.data.settingMessage;
            });
    }
}

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('words')">
                                    <ApplicationMark class="block h-12 w-auto" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('words')" :active="route().current('words')">
                                    Words
                                </NavLink>

                                <NavLink :href="route('wordsets')" :active="route().current('wordsets')">
                                    Wordsets
                                </NavLink>

                                <NavLink :href="route('events')" :active="route().current('events')">
                                    Events
                                </NavLink>

                                <NavLink :href="route('run-command')" :active="route().current('run-command')">
                                    Run Command
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative">
                                <!-- Teams Dropdown -->
                                <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                                {{ $page.props?.auth?.user?.current_team.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Manage Team
                                                </div>

                                                <!-- Team Settings -->
                                                <DropdownLink :href="route('teams.show', $page.props?.auth?.user?.current_team)">
                                                    Team Settings
                                                </DropdownLink>

                                                <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
                                                    Create New Team
                                                </DropdownLink>

                                                <div class="border-t border-gray-100" />

                                                <!-- Team Switcher -->
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Switch Teams
                                                </div>

                                                <template v-for="team in $page.props?.auth?.user?.all_teams" :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <DropdownLink as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="team.id == $page.props?.auth?.user?.current_team_id" class="mr-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>

                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props?.auth?.user?.profile_photo_url" :alt="$page.props?.auth?.user?.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                                {{ $page.props?.auth?.user?.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>

                                        <DropdownLink :href="route('profile.show')">
                                            Profile
                                        </DropdownLink>

                                        <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                            API Tokens
                                        </DropdownLink>

                                        <div class="border-t border-gray-100" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Log Out
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('words')" :active="route().current('words')">
                            Words
                        </ResponsiveNavLink>

                        <ResponsiveNavLink :href="route('wordsets')" :active="route().current('wordsets')">
                            Wordsets
                        </ResponsiveNavLink>

                        <ResponsiveNavLink :href="route('events')" :active="route().current('events')">
                            Events
                        </ResponsiveNavLink>

                        <ResponsiveNavLink :href="route('run-command')" :active="route().current('run-command')">
                            Run Command
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props?.auth?.user?.profile_photo_url" :alt="$page.props?.auth?.user?.name">
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800">
                                    {{ $page.props?.auth?.user?.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props?.auth?.user?.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                Profile
                            </ResponsiveNavLink>

                            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                API Tokens
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    Log Out
                                </ResponsiveNavLink>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-gray-200" />

                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Team
                                </div>

                                <!-- Team Settings -->
                                <ResponsiveNavLink :href="route('teams.show', $page.props?.auth?.user?.current_team)" :active="route().current('teams.show')">
                                    Team Settings
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')" :active="route().current('teams.create')">
                                    Create New Team
                                </ResponsiveNavLink>

                                <div class="border-t border-gray-200" />

                                <!-- Team Switcher -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Switch Teams
                                </div>

                                <template v-for="team in $page.props?.auth?.user?.all_teams" :key="team.id">
                                    <form @submit.prevent="switchToTeam(team)">
                                        <ResponsiveNavLink as="button">
                                            <div class="flex items-center">
                                                <svg v-if="team.id == $page.props?.auth?.user?.current_team_id" class="mr-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <div>{{ team.name }}</div>
                                            </div>
                                        </ResponsiveNavLink>
                                    </form>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <div class="w-full flex flex-col md:flex-row justify-center gap-3 md:gap-20 mt-4 px-6 lg:px-8 text-black" v-if="currentEvent">
                <div class="border border-gray-700 ring-offset-2 ring-gray-900 ring-2 w-full md:w-1/3 rounded-lg px-3 py-4">
                    <div class="flex flex-col lg:flex-row gap-3 justify-between items-center mb-2">
                        <p class="text-lg font-bold">
                            App Status:&nbsp;
                            <span
                                class="font-semibold text-xl px-1.5 py-0.5 rounded border-2"
                                :class="{
                                    'bg-green-100 border-green-500 text-green-500': appStatus,
                                    'bg-red-100 border-red-500 text-red-500': !appStatus
                                }"
                            >
                                {{ (appStatus) ? `ON` : `OFF` }}
                            </span>
                        </p>
                        <primary-button @click="toggleSetting('app_status')" type="button">Toggle</primary-button>
                    </div>
                    <p v-if="!appStatus"><span class="font-bold text-lg">Message:</span> {{appStatusMessage}}</p>
                </div>
                <div class="border border-gray-700 ring-offset-2 ring-gray-900 ring-2 w-full md:w-1/3 rounded-lg px-3 py-4">
                    <div class="flex flex-col lg:flex-row gap-3 justify-between items-center mb-2">
                        <p class="text-lg font-bold">
                            Self Registration:&nbsp;
                            <span
                                class="font-semibold text-xl px-1.5 py-0.5 rounded border-2"
                                :class="{
                                    'bg-green-100 border-green-500 text-green-500': playerRegistration,
                                    'bg-red-100 border-red-500 text-red-500': !playerRegistration
                                }"
                            >
                                {{ (playerRegistration) ? `ON` : `OFF` }}
                            </span>
                        </p>
                        <primary-button @click="toggleSetting('player_registration')" type="button">Toggle</primary-button>
                    </div>
                    <p v-if="!playerRegistration"><span class="font-bold text-lg">Message:</span> {{playerRegistrationMessage}}</p>
                </div>
                <div class="border border-gray-700 ring-offset-2 ring-gray-900 ring-2 w-full md:w-1/3 rounded-lg px-3 py-4">
                    <div class="flex flex-col lg:flex-row gap-3 justify-between items-center mb-2">
                        <p class="text-lg font-bold">
                            Show Leaderboard:&nbsp;
                            <span
                                class="font-semibold text-xl px-1.5 py-0.5 rounded border-2"
                                :class="{
                                    'bg-green-100 border-green-500 text-green-500': showLeaderboard,
                                    'bg-red-100 border-red-500 text-red-500': !showLeaderboard
                                }"
                            >
                                {{ (showLeaderboard) ? `ON` : `OFF` }}
                            </span>
                        </p>
                        <primary-button @click="toggleSetting('show_leaderboard')" type="button">Toggle</primary-button>
                    </div>
                    <p v-if="!showLeaderboard"><span class="font-bold text-lg">Message:</span> {{showLeaderboardMessage}}</p>
                </div>
            </div>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
<style type="text/css">
    input {
        color: black;
    }
</style>