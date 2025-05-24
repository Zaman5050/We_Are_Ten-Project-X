<template>
    <div class="col-md-2 col-sm-6 mb-3">
        <p class="box-text text-center text-uppercase mb-2">{{ title }}</p>
        <div class="dashboard-box">
            <div class="text-center">
                <h1 class="box-heading">{{ count }}</h1>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from "vue";

const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    count: {
        type: Number,
        default: 0,
    },
});
</script>
