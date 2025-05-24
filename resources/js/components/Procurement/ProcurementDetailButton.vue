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
import { useProcurementStore } from "../../stores/procurmentStore";
const procurmentStore = useProcurementStore();

const handleDetailClick = () => {
    procurmentStore.setProcurement(props.procurement);
    procurmentStore.setMode("edit");
    const offCanvas = new bootstrap.Offcanvas(
        document.getElementById("addProcurementCanvas")
    );
    offCanvas.show();
};

const props = defineProps({
    procurement: {
        type: Object,
        default: {},
    },
});
</script>
