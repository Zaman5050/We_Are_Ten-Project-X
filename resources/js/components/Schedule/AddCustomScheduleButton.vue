<template>
    <a
        style="cursor: pointer"
        class="dropdown-item mb-2"
        @click="handleAddCustomMaterialClick"
    >
        Add Custom Material
    </a>
</template>

<script setup>
import { useScheduleStore } from "../../stores/scheduleStore";
import { useToast } from "vue-toastification";
const scheduleStore = useScheduleStore();
const toast = useToast();
const props = defineProps({
    project_areas: {
        type: Array,
        default: [],
    },
    project: {
        type: Array,
        default: [],
    },
});

const handleAddCustomMaterialClick = () => {
    if (props.project_areas.length === 0) {
        toast.error("Please add Project Area.", {
            timeout: 3000,
            position: "top-right",
        });
    } else {
        scheduleStore.resetSchdedule();
        scheduleStore.setMode("create");
        const offCanvas = new bootstrap.Offcanvas(
            document.getElementById("addSceduleCanvas")
        );
        offCanvas.show();
    }
};
</script>
