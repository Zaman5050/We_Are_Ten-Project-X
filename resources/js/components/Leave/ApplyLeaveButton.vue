<template>
    <a class="filter-btn" @click="handleApplyLeave">Apply For Leaves</a>
</template>

<script setup>
import { useLeavStore } from "../../stores/leaveStore";
import { useToast } from "vue-toastification";

const leaveStore = useLeavStore();
const toast = useToast();

const props = defineProps({
    authuserleaves: {
        type: Array,
        default: [],
    },
});

const handleApplyLeave = () => {
    const totalLeaves = Object.values(props.authuserleaves).reduce(
        (sum, leaveCount) => sum + leaveCount,
        0
    );
    if (totalLeaves === 0) {
        toast.error(
            "Your leave allocation has not been specified by your manager. Please contact your manager.",
            {
                timeout: 5000,
                position: "top-right",
            }
        );
    } else {
        leaveStore.setLeaveStatus("pending");
        leaveStore.setLeave({});
        $("#apply-leave-popup").modal("show");
    }
};
</script>

<style scoped lang="scss">
.approve-status-class {
    color: #4bc057;
}
.deny-status-class {
    color: #f8624e;
}
.negotiation-status-class {
    color: #252c32;
}
</style>
