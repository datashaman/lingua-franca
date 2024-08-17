<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, defineProps } from 'vue';
import { PlusIcon } from '@heroicons/vue/24/solid';
import { marked } from 'marked';
import DOMPurify from 'dompurify';

const page = usePage();

const props = defineProps({
    channel: Object,
    members: Array,
    messages: Array,
});

const members = ref(props.members);
const messages = ref(props.messages);
const newMessage = ref('');
const translate = ref(page.props.auth.user.translate);

const sendMessage = () => {
    axios.post(`/api/channels/${props.channel.slug}/messages`, {
        content: newMessage.value,
    });
    newMessage.value = '';
}

const getMembers = () => {
    axios.get(`/api/channels/${props.channel.slug}/members`)
        .then(response => {
            members.value = response.data;
        });
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
        .get(`/api/channels/${props.channel.slug}/messages`)
        .then((response) => {
            messages.value = response.data;
        });
}

const newMember = () => {
}

onMounted(() => {
    Echo.private(`App.Models.Channel.${props.channel.id}`)
        .listen('MessageSent', (event) => {
            messages.value.push(event.message);
        });
});
</script>

<template>
    <Head :title="channel.name" />

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
                            <div v-html="DOMPurify.sanitize(marked(message.content))"></div>
                        </div>
                    </div>
                </div>

                <div class="card-actions">
                    <input type="text" v-model="newMessage" @keyup.enter="sendMessage" placeholder="Type a message" class="input input-bordered w-full" />
                </div>
            </div>
        </div>
        <ul class="menu bg-base-200 rounded-box">
            <ul>
                <li v-for="member in members" :key="member.handle" class="flex flex-row">
                    <div class="flex-grow">{{ member.handle }}</div>
                    <div><div class="badge badge-accent">{{ member.locale }}</div></div>
                    <div class="text-green-500">&#9679;</div>
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
</template>
