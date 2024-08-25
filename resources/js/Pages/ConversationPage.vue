<script setup>
import AppLayout from "../Layouts/AppLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import { PlusIcon } from "@heroicons/vue/24/solid";
import { marked } from "marked";
import DOMPurify from "dompurify";

const page = usePage();

const props = defineProps({
    conversation: Object,
    messages: Array,
    isMember: Boolean,
});

const bots = ref(props.conversation.bots);
const users = ref(props.conversation.users);
const messages = ref(props.messages);
const newMessage = ref("");
const translate = ref(page.props.auth.user?.translate ?? false);
const isMember = ref(props.isMember);
const permissions = ref({});

const getPermissions = async () => {
    const response = await axios.get(route('conversations.permissions', props.conversation));
    permissions.value = response.data;
}

const sendMessage = async () => {
    axios.post(route("conversations.messages.send", props.conversation), {
        content: newMessage.value,
    });
    newMessage.value = "";
};

const getBots = async () => {
    const response = await axios.get(route("conversations.bots", props.conversation));
    bots.value = response.data;
};

const getUsers = async () => {
    const response = await axios.get(route("conversations.users", props.conversation));
    users.value = response.data;
};

const saveTranslate = async () => {
    const response = await axios.put(route("users.translate"), {
        translate: translate.value,
    });
    await getMessages();
};

const getMessages = async () => {
    const response = await axios.get(route("conversations.messages.index", props.conversation));
    messages.value = response.data;
};

const join = async () => {
    const response = await axios.post(route("conversations.join", props.conversation));
    isMember.value = true;
    getUsers();
    getPermissions();
};

const leave = async () => {
    const response = await axios.post(route("conversations.leave", props.conversation));
    isMember.value = false;
    getUsers();
    getPermissions();
};

const newMember = () => {};

const getSenderHandle = (message) => {
    return message.metadata.sender
};

const renderMessageContent = (message) => {
    return DOMPurify.sanitize(marked(message.content[0].text.value));
};

const conversationTitle = computed(() => {
    return props.conversation.name;
});

onMounted(async () => {
    const eventName = page.props.auth.user.translate
        ? `TranslationSent.${page.props.auth.user.locale}`
        : "MessageSent";

    Echo.private(`App.Models.Conversation.${props.conversation.id}`).listen(
        eventName,
        (event) => {
            console.log(event.message);
            messages.value.push(event.message);
        },
    );

    await getPermissions();
});
</script>

<template>
    <AppLayout>
        <Head :title="'HI'" />

        <div class="sm:px-6 flex flex-row gap-4">
            <div class="flex-grow card bg-base-100">
                <div class="card-body">
                    <div class="card-title">
                        <div>Messages</div>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">Translate</span>
                                <input
                                    type="checkbox"
                                    class="ms-2 toggle"
                                    v-model="translate"
                                    @change="saveTranslate"
                                />
                            </label>
                        </div>
                        <button
                            v-if="permissions.leave"
                            @click="leave"
                            class="btn btn-error"
                        >
                            Leave Conversation
                        </button>
                    </div>
                    <div v-for="message in messages" :key="message.id">
                        <div
                            v-if="
                                message.metadata.sender ===
                                `user:${page.props.auth.user.handle}`
                            "
                            class="chat chat-end"
                        >
                            <div class="chat-header">
                                {{ getSenderHandle(message) }}
                            </div>
                            <div class="chat-bubble chat-bubble-primary">
                                <div
                                    v-html="renderMessageContent(message)"
                                ></div>
                            </div>
                        </div>
                        <div v-else class="chat chat-start">
                            <div class="chat-header">
                                {{ getSenderHandle(message) }}
                            </div>
                            <div class="chat-bubble chat-bubble-accent">
                                <div
                                    v-html="renderMessageContent(message)"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-actions">
                        <input
                            v-if="isMember"
                            type="text"
                            v-model="newMessage"
                            @keyup.enter="sendMessage"
                            placeholder="Type a message"
                            class="input input-bordered w-full"
                        />
                        <button v-if="permissions.join" @click="join" class="btn btn-primary">
                            <PlusIcon class="h-5 w-5" />
                            Join Conversation
                        </button>
                    </div>
                </div>
            </div>
            <div class="w-1/5">
                <h2 class="menu-title text-lg">Users</h2>
                <ul v-if="users.length" class="menu bg-base-200 rounded-box">
                    <li
                        v-for="user in users"
                        :key="user.handle"
                        class="flex flex-row"
                    >
                        <div class="flex-grow px-2">{{ user.handle }}</div>
                        <div>
                            <div class="badge badge-accent">
                                {{ user.locale }}
                            </div>
                        </div>
                        <div class="text-green-500">&#9679;</div>
                    </li>
                </ul>
                <div v-else class="px-4">
                    <p>No users</p>
                </div>
                <h2 class="menu-title text-lg">Bots</h2>
                <ul v-if="bots.length" class="menu bg-base-200 rounded-box">
                    <li
                        v-for="bot in bots"
                        :key="bot.handle"
                        class="flex flex-row"
                    >
                        <div class="flex-grow px-2">{{ bot.handle }}</div>
                        <div>
                            <div class="badge badge-accent">
                                {{ bot.locale }}
                            </div>
                        </div>
                        <div class="text-green-500">&#9679;</div>
                    </li>
                </ul>
                <div v-else class="px-4">
                    <p>No bots</p>
                </div>
            </div>
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
    </AppLayout>
</template>
