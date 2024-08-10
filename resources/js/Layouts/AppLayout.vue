<script setup>
import { ref, onMounted, defineProps } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ChannelMenu from '@/Components/ChannelMenu.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    constrain: Boolean,
    title: String,
});

const page = usePage();

const showingNavigationDropdown = ref(false);

const logout = () => {
    axios.post(route('logout')).then(() => {
        window.location = '/';
    });
};
</script>

<template>
    <div class="min-h-screen bg-base-200">
        <div class="navbar bg-base-100">
            <div class="flex-1">
                <a :href="route('home')" class="btn btn-ghost text-xl">
                    Home
                </a>
            </div>
            <div v-if="page.props.auth.user" class="flex-none">
                <ul class="menu menu-horizontal px-1">
                    <li>
                        <details>
                            <summary>{{ $page.props.auth.user.name }}</summary>
                            <ul class="bg-base-100 rounded-t-none p-2">
                                <li>
                                    <a :href="route('profile.edit')">Profile</a>
                                </li>
                                <li>
                                    <a @click="logout">Logout</a>
                                </li>
                            </ul>
                        </details>
                    </li>
                </ul>
            </div>
            <div v-else class="flex-none">
                <Link :href="route('login')" class="btn btn-ghost">
                    Login
                </Link>
            </div>
        </div>

        <!-- Page Heading -->
        <header class="bg-base-100 shadow" v-if="$slots.header">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="h-full flex flex-row gap-4">
            <div class="py-8">
                <ChannelMenu />
            </div>
            <div class="flex-grow">
                <div v-if="props.constrain" class="w-1/3 mx-auto py-8">
                    <h1 class="text-3xl mb-8 font-bold">{{ props.title }}</h1>
                    <slot />
                </div>
                <div v-else class="py-8">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>
