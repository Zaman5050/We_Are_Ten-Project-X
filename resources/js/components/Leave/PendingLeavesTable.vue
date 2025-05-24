<template>
    <table class="table" id="pending-leave-request">
        <thead>
            <tr>
                <th>Leave Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody >
            <tr v-for="leave in leaves" :key="leave.uuid">
                <td>{{ leave.leave_type }}</td>
                <td>{{ leave.start_date }}</td>
                <td>{{ leave.end_date }}</td>
                <td>{{ leave.notes }}</td>
                <td>
                    <div class="approve-deny-div">
                        <!-- Common Actions: Approve, Deny, and Download -->
                        <a @click="handleStatusClick(leave,'approved')" class="approve-status-class">Approve </a>
                        <a @click="handleStatusClick(leave,'declined')" class="deny-status-class">Deny </a>
                        <a
                            v-if="leave.status === 'pending'"
                            class="negotiation-status-class"
                            @click="handleStatusClick(leave,'negotiated')"
                            >
                            Negotiate
                        </a>
                        <a
                            v-else-if="leave.status === 're-negotiated'"
                            class="negotiation-status-class"
                            @click="handleStatusClick(leave,'re-negotiated')"
                            >
                            Renegotiate
                        </a>
                        <a v-if="false">
                            <img src="/assets/images/icon/download-icons.svg"
                        /></a>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { useLeavStore } from '../../stores/leaveStore';

const leaveStore = useLeavStore();
const props = defineProps({
    leaves: {
        type: Array,
        default: [],
    },
    member: {
        type: Object,
        default: [],
    },
});

const handleStatusClick = (leave,status) => {
    leaveStore.setLeave(leave);
    leaveStore.setLeaveStatus(status)
    leaveStore.setMember(props.member)
    $('#apply-leave-popup').modal('show');
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
