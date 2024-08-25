<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { PlusIcon } from '@heroicons/vue/24/solid';

const page = usePage();

const conversations = ref([]);
const newConversationName = ref('');

const createConversation = () => {
    axios.post('/api/conversations', {
        name: newConversationName.value,
    })
        .then(response => {
            fetchConversations();
        });
};

const fetchConversations = () => {
    axios.get('/api/conversations')
        .then(response => {
            conversations.value = response.data;
        });
};

const systemConversations = computed(() => {
    return conversations.value.filter(conversation => conversation.is_system)
});

const userConversations = computed(() => {
    return conversations.value.filter(conversation => !conversation.is_system)
});

onMounted(() => {
    fetchConversations();
})
</script>

<template>
    <div>
        <ul class="menu w-80 bg-base-200 rounded-box">
            <ul>
                <li v-for="conversation in systemConversations" :key="conversation.id">
                    <Link :href="route('conversations.show', conversation)" :class="{ 'active': $page.url === route('conversations.show', conversation) }">
                        {{ conversation.name }}
                    </Link>
                </li>
            </ul>
        </ul>
        <ul v-if="userConversations.length" class="menu w-80 bg-base-200 rounded-box">
            <li>
                <h2 class="menu-title text-lg">
                    User Conversations
                    <button v-if="page.props.auth.permissions.conversations.create" type="button" class="btn btn-square btn-sm ms-2" onclick="new_conversation.showModal()">
                        <PlusIcon class="size-3" />
                    </button>
                </h2>
            </li>
            <ul>
                <li v-for="conversation in userConversations" :key="conversation.id">
                    <Link :href="route('conversations.show', conversation)" :class="{ 'active': $page.url === route('conversations.show', conversation) }">
                        {{ conversation.name }}
                    </Link>
                </li>
            </ul>
        </ul>
        <dialog id="new_conversation" class="modal modal-bottom sm:modal-middle">
            <div class="modal-box">
                <h3 class="text-lg font-bold">New Conversation</h3>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Name</span>
                    </label>
                    <input type="text" class="input input-bordered" v-model="newConversationName" required />
                </div>

                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Close</button>
                        <button class="btn btn-primary ms-4" @click="createConversation">Create</button>
                    </form>
                </div>
            </div>
        </dialog>
    </div>
</template>
