<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, defineProps } from 'vue';
import { PlusIcon } from '@heroicons/vue/24/solid';

const page = usePage();

const props = defineProps({
    bot: Object,
    messages: Array,
});

const messages = ref(props.messages);
const newMessage = ref('');

const sendMessage = () => {
    axios.post(`/api/bots/${props.bot.handle}/messages`, {
        content: newMessage.value,
    });
    newMessage.value = '';
}

const fetchMessages = () => {
    axios.get(`/api/bots/${props.bot.handle}/messages`)
        .then(response => {
            messages.value = response.data;
        });
}

Echo.private(`App.Models.Bot.${props.bot.id}`)
    .listen('MessageSent', (event) => {
        messages.value.push(event.message);
    });
</script>

<template>
    <AppLayout title="Bot">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ bot.name }}</h2>
        </template>

        <div class="sm:px-6 flex flex-row gap-4">
            <div class="flex-grow card bg-base-100">
                <div class="card-body">
                    <div class="card-title">
                        <div>Messages</div>
                    </div>
                    <div v-for="message in messages" :key="message.id">
                        <div v-if="message.sender.type === 'user' && message.sender.id === page.props.auth.user.id" class="chat chat-end">
                            <div class="chat-header">
                                {{ message.sender.handle }}
                            </div>
                            <div class="chat-bubble chat-bubble-primary">
                                {{ message.content }}
                            </div>
                        </div>
                        <div v-else class="chat chat-start">
                            <div class="chat-header">
                                {{ message.sender.handle }}
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
        </div>
    </AppLayout>
</template>
