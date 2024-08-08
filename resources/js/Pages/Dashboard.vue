<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const currentChannel = ref(null);

const channels = ref([]);
const messages = ref([]);
const members = ref({});

const me = 'datashaman';
const newMessage = ref('');

const newChannel = (name) => {
}

const sendMessage = () => {
    newMessage.value = '';
}

const fetchChannels = () => {
    axios.get('/api/channels')
        .then(response => {
            console.log(response.data);
            channels.value = response.data;
        });
}

onMounted(() => {
    fetchChannels();
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 m-8 flex flex-row gap-4">
            <div class="card bg-base-100">
                <div class="card-body">
                    <div class="card-title">Channels</div>
                    <ul class="menu bg-base-200 rounded-box">
                        <li v-for="channel in channels" :key="channel.id">
                            <a href="#">
                                {{ channel.name }}
                            </a>
                        </li>
                    </ul>
                    <div class="card-actions">
                        <button type="button" class="btn btn-primary" @click="newChannel">New Channel</button>
                    </div>
                </div>
            </div>
            <div class="flex-grow card bg-base-100">
                <div class="card-body">
                    <div class="card-title">Messages</div>

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
                        <input type="text" v-model="newMessage" @keyup.enter="sendMessage" placeholder="Type a message" class="input input-bordered w-full" />
                    </div>
                </div>
            </div>
            <div class="card bg-base-100">
                <div class="card-body">
                    <div class="card-title">Members</div>

                    <ul>
                        <li v-for="member in members" :key="member.handle">
                            <div v-if="member.handle === me" class="badge badge-primary text-lg">{{ member.handle }}</div>
                            <div v-else class="badge badge-accent text-lg">{{ member.handle }}</div>
                            ({{ member.language }})
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
