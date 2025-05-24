<template>
    <button class="dropdown-item mb-2" @click="handleDetailClick">
        Add Product from Product Library
    </button>
</template>

<script setup>
import { useToast } from "vue-toastification";
import { useProcurementStore } from "../../stores/procurmentStore";
import { nextTick } from 'vue';

const procurmentStore = useProcurementStore();
const props = defineProps({
    project: {
        type: Array,
        default: [],
    },
});
const toast = useToast();

const handleDetailClick = async () => {
    if(props.project?.start_date === null || props.project?.due_date === null){
        toast.error("The project's start and end dates are missing. Please add them to proceed with procurement.", {
            timeout: 3000,
            position: "top-right",
        });
    }else if (props.project?.procurement_budget?.length === 0) {
        toast.error("Please add procurement Budget.", {
            timeout: 3000,
            position: "top-right",
        });
    } else {
        procurmentStore.resetProcurement();
        procurmentStore.setMode("create");
        await nextTick(); // Wait for the DOM to update
        const offCanvasElement = document.getElementById("addProcurementFromProductLibraryCanvas");
        if (offCanvasElement) {
            const offCanvas = new bootstrap.Offcanvas(offCanvasElement);
            offCanvas.show();
        } else {
            console.error("Offcanvas element not found");
        }
    }
};
</script>
