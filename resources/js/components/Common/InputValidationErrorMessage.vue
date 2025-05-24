<template>
    <p class="text-danger my-1"
        v-if="v.$error"
        >{{
            v?.$errors[0]?.$message
        }}
        </p>
</template>
<script setup>
const props = defineProps({
    v: {
        type: Object,
        default: {},
    },
})
</script>