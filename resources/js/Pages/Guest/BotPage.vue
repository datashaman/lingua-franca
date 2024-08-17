<script setup>
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    bot: Object,
});
</script>

<template>
    <Head :title="bot.handle" />
</template>
