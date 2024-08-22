<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    bot: Object,
});
</script>

<template>
    <Head :title="bot.handle" />
</template>
