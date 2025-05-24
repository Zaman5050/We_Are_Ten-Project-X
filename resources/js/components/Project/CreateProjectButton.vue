<template>
    <a
        href="javascript:void(0)"
        class="filter-btn import-creat-project"
        @click="handleDetailClick"
        >Create New Project</a
    >
</template>

<script setup>
import { defineProps } from "vue";

import { useProjectStore } from "../../stores/projectStore";
const projectStore = useProjectStore();

const handleDetailClick = () => {
    projectStore.resetProject();
    projectStore.setMode("create");
    const modalElement = document.getElementById("create-new-project");
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
};
</script>
