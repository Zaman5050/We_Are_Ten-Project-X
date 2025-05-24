<template>
    <div @click="sendId(props.taskUuid)">
        <slot></slot>
    </div>
</template>

<script setup>

import eventBus from '../eventBus';
import { ref, defineEmits, defineProps } from 'vue'

const props = defineProps({
    taskUuid: ''
})

defineEmits(['idSelected']);

const sendId = (uuid) => {
    eventBus.emit('idSelected', uuid);
}

</script>