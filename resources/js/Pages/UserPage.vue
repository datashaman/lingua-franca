<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { PlusIcon } from '@heroicons/vue/24/solid';

const page = usePage();

const props = defineProps({
    user: Object,
    messages: Array,
});

const messages = ref(props.messages);
const newMessage = ref('');
const translate = ref(page.props.auth.user.translate);

const sendMessage = () => {
    axios.post(`/api/users/${props.user.handle}/messages`, {
        content: newMessage.value,
    });
    newMessage.value = '';
}

const saveTranslate = () => {
    axios
        .put(`/api/users/translate`, {
            translate: translate.value,
        })
        .then(() => {
            getMessages();
        });
}

const getMessages = () => {
    axios
        .get(`/api/users/${props.user.handle}/messages`)
        .then((response) => {
            messages.value = response.data;
        });
}

onMounted(() => {
    Echo.private(`App.Models.User.${page.props.auth.user.id}`)
        .listen('MessageSent', (event) => {
            messages.value.push(event.message);
        });
});
</script>

<template>
    <Head :title="user.handle" />
    <div class="sm:px-6 flex flex-row gap-4">
        <div class="flex-grow card bg-base-100">
            <div class="card-body">
                <div class="card-title">
                    <div>Messages</div>
                    <div class="form-control">
                        <label class="label cursor-pointer">
                            <span class="label-text">Translate</span>
                            <input type="checkbox" class="ms-2 toggle" v-model="translate" @change="saveTranslate" />
                        </label>
                    </div>
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
</template>
