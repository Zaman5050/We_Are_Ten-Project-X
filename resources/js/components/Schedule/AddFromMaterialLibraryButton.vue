<template>
    <a
        style="cursor: pointer"
        class="dropdown-item mb-2"
        @click="handleAddCustomMaterialClick"
    >
        Add From Material Library
    </a>
</template>

<script setup>
import { useToast } from "vue-toastification";
import { nextTick } from 'vue';

const toast = useToast();
const props = defineProps({
    project_areas: {
        type: Array,
        default: [],
    },
});

const handleAddCustomMaterialClick = async () => {
    if (props.project_areas.length === 0) {
        toast.error("Please add Project Area.", {
            timeout: 3000,
            position: "top-right",
        });
    } else {
        await nextTick(); // Wait for the DOM to update
        const offCanvas = new bootstrap.Offcanvas(
            document.getElementById("addSceduleFromMaterialLibrary")
        );
        offCanvas.show();
    }
};
</script>
