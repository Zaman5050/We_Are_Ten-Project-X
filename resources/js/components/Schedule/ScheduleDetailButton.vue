<template>
    <div
        style="gap: 28px"
        class="d-flex flex-column justify-content-between"
        @click="handleDetailClick"
    >
        <a class="filter-btn import-creat-project bg-yellow darker-grotesque-regular">Details</a>
    </div>
</template>

<script setup>
import { useScheduleStore } from "../../stores/scheduleStore";
const ScheduleStore = useScheduleStore();

const handleDetailClick = () => {
    ScheduleStore.setSchdedule(props.schedule);
    ScheduleStore.setMode("edit");
    const offCanvas = new bootstrap.Offcanvas(
        document.getElementById("addSceduleCanvas")
    );
    offCanvas.show();
};

const props = defineProps({
    schedule: {
        type: Object,
        default: {},
    },
});
</script>
