<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { PlusIcon } from '@heroicons/vue/24/solid';

const page = usePage();

const channels = ref([]);
const newChannelName = ref('');

const createChannel = () => {
    axios.post('/api/channels', {
        name: newChannelName.value,
    })
        .then(response => {
            fetchChannels();
        });
};

const fetchChannels = () => {
    axios.get('/api/channels')
        .then(response => {
            channels.value = response.data;
        });
};

const systemChannels = computed(() => {
    return channels.value.filter(channel => channel.is_system)
});

const userChannels = computed(() => {
    return channels.value.filter(channel => !channel.is_system)
});

onMounted(() => {
    fetchChannels();
})
</script>

<template>
    <div>
        <ul class="menu w-80 bg-base-200 rounded-box">
            <ul>
                <li v-for="channel in systemChannels" :key="channel.id">
                    <Link :href="route('channels.show', channel.slug)" :class="{ 'active': $page.url === `/channels/{channel.slug}` }">
                        {{ channel.name }}
                        <span v-if="channel.unread_message_count" class="badge badge-accent badge-sm">{{ channel.unread_message_count }}</span>
                    </Link>
                </li>
            </ul>
        </ul>
        <ul v-if="userChannels.length" class="menu w-80 bg-base-200 rounded-box">
            <li>
                <h2 class="menu-title text-lg">
                    User Channels
                    <button v-if="page.props.auth.permissions.channels.create" type="button" class="btn btn-square btn-sm ms-2" onclick="new_channel.showModal()">
                        <PlusIcon class="size-3" />
                    </button>
                </h2>
            </li>
            <ul>
                <li v-for="channel in userChannels" :key="channel.id">
                    <Link :href="route('channels.show', channel.slug)" :class="{ 'active': $page.url === `/channels/{channel.slug}` }">
                        {{ channel.name }}
                        <span v-if="channel.unread_message_count" class="badge badge-accent badge-sm">{{ channel.unread_message_count }}</span>
                    </Link>
                </li>
            </ul>
        </ul>
        <dialog id="new_channel" class="modal modal-bottom sm:modal-middle">
            <div class="modal-box">
                <h3 class="text-lg font-bold">New Channel</h3>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Name</span>
                    </label>
                    <input type="text" class="input input-bordered" v-model="newChannelName" required />
                </div>

                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Close</button>
                        <button class="btn btn-primary ms-4" @click="createChannel">Create</button>
                    </form>
                </div>
            </div>
        </dialog>
    </div>
</template>
