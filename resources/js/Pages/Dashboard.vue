<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const messages = ref([]);
const speakers = ref({});
const me = 'datashaman';
const newMessage = ref('');

const addMessage = (content, speaker) => {
    speaker ||= me;
    messages.value.push({ id: messages.value.length + 1, content, speaker });
}

const sendMessage = () => {
    addMessage(newMessage.value);
    newMessage.value = '';
}

const addSpeaker = (handle, name, language, bot=false) => {
    speakers.value[handle] = { handle, name, language, bot };
}

onMounted(() => {
    addSpeaker(me, 'Marlin', 'en');
    addSpeaker('alice', 'Alice', 'af');
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 m-8 flex flex-row gap-4">
            <div class="flex-grow card bg-base-100">
                <div class="card-body">
                    <div class="card-title">Messages</div>

                    <div v-for="message in messages" :key="message.id">
                        <div v-if="message.speaker === me" class="chat chat-end">
                            <div class="chat-header">
                                {{ me }}
                            </div>
                            <div class="chat-bubble chat-bubble-primary">
                                {{ message.content }}
                            </div>
                        </div>
                        <div v-else class="chat chat-start">
                            <div class="chat-header">
                                {{ message.speaker }}
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
                    <div class="card-title">Speakers</div>

                    <ul>
                        <li v-for="speaker in speakers" :key="speaker.handle">
                            <div v-if="speaker.handle === me" class="badge badge-primary text-lg">{{ speaker.handle }}</div>
                            <div v-else class="badge badge-accent text-lg">{{ speaker.handle }}</div>
                            ({{ speaker.language }})
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
