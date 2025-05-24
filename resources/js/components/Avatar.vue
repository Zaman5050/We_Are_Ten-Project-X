<script setup>
import { defineProps } from 'vue'


const props = defineProps({
    name: {
        type: String,
        default: ''
    },
    background: {
        type: String,
        default: ''
    },
    active: {
        type: Boolean,
        default: false
    },
    path: {
        type: String,
        default: ''
    },
    size: {
        type: String,
        default: ''
    },
})

</script>

<template>
    <img v-if="path" :class="['member-img-icon avatar ', { 'active': props.active }]"  role="button" :src="path" :width="size" :height="size">
    <svg v-else :class="['member-img-icon avatar ', { 'active': props.active }]"  role="button" xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="32px" viewBox="0 0 24 24" version="1.1">
        <circle :fill="props.background" cx="12" width="24" height="24" cy="12" r="12" /><text x="50%" y="50%"
            class="avatar-text" alignment-baseline="middle" text-anchor="middle" font-size="10" font-weight="600"
            dy=".1em" dominant-baseline="middle" fill="#fff">{{ props.name }}</text>
    </svg>
</template>