<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed, defineProps } from 'vue';
import { PlusIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    bots: Array,
    users: Array,
});

const page = usePage();
const authUser = computed(() => page.props.auth.user);

const bots = ref(props.bots);
const users = ref(props.users);

const fetchUsers = () => {
    axios.get('/api/users')
        .then(response => {
            users.value = response.data;
        });
}

const fetchBots = () => {
    axios.get('/api/bots')
        .then(response => {
            bots.value = response.data;
        });
}

const localeName = (locale) => {
    return page.props.locales[locale];
}

onMounted(() => {
});
</script>

<template>
    <Head title="Lingua Franca" />

    <AppLayout>
        <div class="flex flex-row gap-4">
            <ul class="menu bg-base-200 rounded-box w-1/3">
                <li>
                    <h2 class="menu-title text-lg">
                        Users
                        <button v-if="page.props.auth.permissions.users.create" type="button" class="btn btn-square btn-sm ms-2" onclick="new_user.showModal()">
                            <PlusIcon class="size-3" />
                        </button>
                    </h2>
                </li>
                <ul>
                    <li v-for="user in users" :key="user.handle">
                        <span v-if="authUser && user.id === authUser.id" class="font-bold">{{ user.handle }} <span class="badge badge-accent badge-sm">{{ localeName(user.locale) }}</span></span>
                        <span v-else>{{ user.handle }} <span class="badge badge-accent badge-sm">{{ localeName(user.locale) }}</span></span>
                    </li>
                </ul>
            </ul>
            <ul class="menu bg-base-200 rounded-box w-1/3">
                <li>
                    <h2 class="menu-title text-lg">
                        Bots
                        <button v-if="page.props.auth.permissions.bots.create" type="button" class="btn btn-square btn-sm ms-2" onclick="new_bot.showModal()">
                            <PlusIcon class="size-3" />
                        </button>
                    </h2>
                </li>
                <ul>
                    <li v-for="bot in bots" :key="bot.handle">
                        <span>{{ bot.handle }} <span class="badge badge-accent badge-sm">{{ localeName(bot.locale) }}</span></span>
                    </li>
                </ul>
            </ul>
        </div>
        <dialog id="new_user" class="modal modal-bottom sm:modal-middle">
            <div class="modal-box">
                <h3 class="text-lg font-bold">New User</h3>
                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Close</button>
                    </form>
                </div>
            </div>
        </dialog>
        <dialog id="new_bot" class="modal modal-bottom sm:modal-middle">
            <div class="modal-box">
                <h3 class="text-lg font-bold">New Bot</h3>
                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Close</button>
                    </form>
                </div>
            </div>
        </dialog>
    </AppLayout>
</template>
