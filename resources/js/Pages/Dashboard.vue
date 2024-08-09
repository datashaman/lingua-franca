<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { PlusIcon } from '@heroicons/vue/24/solid';

const currentChannel = ref(null);

const channels = ref([]);
const messages = ref([]);
const members = ref({});

const me = 'datashaman';
const newMessage = ref('');

const newChannelName = ref('');

const createChannel = () => {
    axios.post('/api/channels', {
        name: newChannelName.value,
    })
        .then(response => {
            fetchChannels();
        });
}

const createMessage = () => {
    axios.post(`/api/channels/${currentChannel.value.id}/messages`, {
        content: newMessage.value,
    })
        .then(response => {
            fetchMessages();
        });
    newMessage.value = '';
}

const fetchChannels = () => {
    axios.get('/api/channels')
        .then(response => {
            channels.value = response.data;
        });
}

const fetchMessages = () => {
    axios.get(`/api/channels/${currentChannel.value.id}/messages`)
        .then(response => {
            messages.value = response.data;
        });
}

const fetchMembers = () => {
    axios.get(`/api/channels/${currentChannel.value.id}/members`)
        .then(response => {
            members.value = response.data;
        });
}

const newMember = () => {
}

onMounted(() => {
    fetchChannels();
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 m-8 flex flex-row gap-4">
            <ul class="menu bg-base-200 rounded-box">
                <li>
                    <h2 class="menu-title">
                        Channels
                        <button type="button" class="btn btn-square btn-sm ms-2" onclick="new_channel.showModal()">
                            <PlusIcon class="size-3" />
                        </button>
                    </h2>
                </li>
                <ul>
                    <li v-for="channel in channels" :key="channel.id">
                        <a v-if="channel.is_member" ref="#">
                            <div class="">{{ channel.name }}</div>
                        </a>
                        <a href="#">
                            {{ channel.name }}
                        </a>
                    </li>
                </ul>
            </ul>
            <div class="flex-grow card bg-base-100">
                <div class="card-body">
                    <div class="card-title">
                        <div>Messages</div>
                    </div>
                    <div v-for="message in messages" :key="message.id">
                        <div v-if="message.member === me" class="chat chat-end">
                            <div class="chat-header">
                                {{ me }}
                            </div>
                            <div class="chat-bubble chat-bubble-primary">
                                {{ message.content }}
                            </div>
                        </div>
                        <div v-else class="chat chat-start">
                            <div class="chat-header">
                                {{ message.member }}
                            </div>
                            <div class="chat-bubble chat-bubble-accent">
                                {{ message.content }}
                            </div>
                        </div>
                    </div>

                    <div class="card-actions">
                        <input type="text" v-model="newMessage" @keyup.enter="createMessage" placeholder="Type a message" class="input input-bordered w-full" />
                    </div>
                </div>
            </div>
            <ul class="menu bg-base-200 rounded-box">
                <li>
                    <h2 class="menu-title">
                        Members
                        <button type="button" class="btn btn-square btn-sm ms-2" onclick="new_member.showModal()">
                            <PlusIcon class="size-3" />
                        </button>
                    </h2>
                </li>
                <ul>
                    <li v-for="member in members" :key="member.handle">
                        <div v-if="member.handle === me" class="badge badge-primary text-lg">{{ member.handle }}</div>
                        <div v-else class="badge badge-accent text-lg">{{ member.handle }}</div>
                        ({{ member.language }})
                    </li>
                </ul>
            </ul>
        </div>
        <dialog id="new_member" class="modal modal-bottom sm:modal-middle">
            <div class="modal-box">
                <h3 class="text-lg font-bold">New Member</h3>
                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Close</button>
                    </form>
                </div>
            </div>
        </dialog>
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
    </AuthenticatedLayout>
</template>
