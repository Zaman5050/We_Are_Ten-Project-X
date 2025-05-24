<template>
    <button class="dropdown-item mb-2" @click="handleDetailClick">
        Add Custom Product
    </button>
</template>

<script setup>
import { useToast } from "vue-toastification";
import { useProcurementStore } from "../../stores/procurmentStore";
const procurmentStore = useProcurementStore();
const props = defineProps({
    project: {
        type: Array,
        default: [],
    },
});
const toast = useToast();
const handleDetailClick = () => {
    if(props.project?.start_date === null || props.project?.due_date === null){
        toast.error("The project's start and end dates are missing. Please add them to proceed with procurement.", {
            timeout: 3000,
            position: "top-right",
        });
    }else if (props.project?.procurement_budget.length === 0) {
        toast.error("Please add procurement Budget.", {
            timeout: 3000,
            position: "top-right",
        });
    } else {
        procurmentStore.resetProcurement();
        procurmentStore.setMode("create");
        procurmentStore.setModule("procurement");
        const offCanvas = new bootstrap.Offcanvas(
            document.getElementById("addProcurementCanvas")
        );
        offCanvas.show();
    }
};
</script>
