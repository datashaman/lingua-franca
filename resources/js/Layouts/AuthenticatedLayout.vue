<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

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
                <a :href="route('dashboard')" class="btn btn-ghost text-xl">
                    Dashboard
                </a>
            </div>
            <div class="flex-none">
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
        </div>

        <!-- Page Heading -->
        <header class="bg-base-100 shadow" v-if="$slots.header">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="h-full">
            <slot />
        </main>
    </div>
</template>
