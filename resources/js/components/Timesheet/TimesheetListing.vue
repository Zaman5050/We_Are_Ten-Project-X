<template>
    <div
        v-for="(timesheets, dateIndex, index) in props.timesheets"
        :key="dateIndex"
        class="mb-3"
    >
        <div :id="'shadow-container-time-sheet-' + index">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="table-date-heading">{{ dateIndex }}</h6>
                <div class="position-relative">
                    <button
                        class="collaps-expand-btn"
                        :data-target="'#time-sheet-' + index"
                        :data-shadow="'#shadow-container-time-sheet-' + index"
                    >
                        Collapse
                    </button>
                    <img
                        class="collaps-icon"
                        src="/assets/images/arrow-icon.svg"
                    />
                </div>
            </div>
            <div class="dashboard-table-container">
                <table
                    class="table mb-0 dashboard-table"
                    :id="'time-sheet-' + index"
                >
                    <thead>
                        <tr>
                            <th width="50px">ID</th>
                            <th width="150px">Project Name</th>
                            <th width="150px">Task Title</th>
                            <th>Status</th>
                            <th>Starting Time</th>
                            <th>Ending Time</th>
                            <th>Total Time</th>
                            <th>Stage</th>
                            <th>Team Members</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(timesheet, index) in timesheets"
                            :key="index"
                        >
                            <td>
                                <div>{{ timesheet?.task?.task_code }}</div>
                            </td>
                            <td>
                                <p class="text-truncate list-title">
                                    {{ timesheet?.task?.project?.name }}
                                </p>
                            </td>
                            <td>
                                <p class="text-truncate list-title">
                                    {{ timesheet?.task?.title }}
                                </p>
                            </td>
                            <td>
                                <div>{{ timesheet?.task?.status?.title }}</div>
                            </td>
                            <td>
                                <div>{{ timesheet?.start_time_formated }}</div>
                            </td>
                            <td>
                                <div>{{ timesheet?.end_time_formated }}</div>
                            </td>
                            <td>
                                <div>
                                    {{
                                        convertMinutesToReadableTime(
                                            timesheet?.total_time
                                        )
                                    }}
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="stage">{{
                                        timesheet?.task?.stage?.stage_name ??
                                        "---"
                                    }}</span>
                                </div>
                            </td>
                            <td>
                                <!-- <MemberAvatarList :users="timesheet?.task?.users" :size="timesheet?.task?.users?.length"
                                    :uindex="index"></MemberAvatarList> -->
                                <MemberAvatarList
                                    :users="
                                        timesheet?.task?.users.filter(
                                            (user) =>
                                                user.id === timesheet.user_id
                                        )
                                    "
                                    :size="1"
                                    :uindex="index"
                                >
                                </MemberAvatarList>
                            </td>
                            <th v-if="false">
                                <div>
                                    <!-- <button class="edit-dell-btn me-2"><img src="assets/images/edit-icon.svg"></button> -->
                                    <button
                                        class="edit-dell-btn"
                                        type="button"
                                        @click="
                                            handleDeleteClick(timesheet.uuid)
                                        "
                                        data-bs-toggle="modal"
                                        data-bs-target="#delete-area"
                                    >
                                        <img
                                            src="assets/images/delete-icon.svg"
                                        />
                                    </button>
                                </div>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div
        class="modal fade"
        id="delete-area"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        tabindex="-1"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="delete-warning">
                        ARE YOU SURE YOU WANT TO DELETE THIS AREA ?
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0 flex-nowrap">
                    <button
                        style="width: 50%; background: #fff; color: #000"
                        type="button"
                        class="apply-leave-btn"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    >
                        No
                    </button>
                    <button
                        style="width: 50%"
                        type="button"
                        @click="handleDeleteEntry"
                        class="apply-leave-btn"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    >
                        Yes
                    </button>
                </div>
            </div>
        </div>
    </div>
    <AddTimeEntry :memberProjects="memberProjects"></AddTimeEntry>
</template>

<script setup>
import { computed, defineProps, ref, onMounted } from "vue";
import axios from "axios";
import MemberAvatarList from "../MemberAvatarList.vue";
import AddTimeEntry from "./AddTimeEntry.vue";

const props = defineProps({
    timesheets: {
        type: Object,
        default: [],
    },
    memberProjects: {
        type: Object,
        default: [],
    },
});

const deleteEntryUuid = ref(null);

const handleDeleteClick = (uuid) => {
    deleteEntryUuid.value = uuid;
};

const timesheetCollapsed = ref({});

onMounted(() => {
    const savedState = localStorage.getItem("timesheetCollapsed");
    if (savedState) {
        timesheetCollapsed.value = JSON.parse(savedState);
    }
});

function convertMinutesToReadableTime(minutes) {
    // Convert the decimal minutes to total seconds
    const totalSeconds = Math.floor(minutes * 60);

    // Calculate hours, minutes, and remaining seconds
    const hours = Math.floor(totalSeconds / 3600);
    const remainingMinutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    // Construct the human-readable format
    let readableTime = "";

    if (hours > 0) {
        readableTime += `${hours}h `;
    }
    if (remainingMinutes > 0) {
        readableTime += `${remainingMinutes}m `;
    }
    if (seconds > 0 || readableTime === "") {
        readableTime += `${seconds}s`;
    }

    return readableTime.trim(); // Remove any trailing spaces
}

const handleDeleteEntry = async () => {
    if (!deleteEntryUuid.value) return false;

    try {
        const response = await axios.delete(
            `/timesheet/${deleteEntryUuid.value}`
        );
        if (response.data.error) {
            alert("Somthing went wrong");
        }
        window.location.reload();
    } catch (error) {
        alert(error.message);
    }
    deleteEntryUuid.value = null;
};

$(document).ready(function () {
    let collapsButton = document.querySelectorAll(".collaps-expand-btn");
    collapsButton.forEach((item) => {
        item.addEventListener("click", function (e) {
            let img = e.target.nextElementSibling;
            var button = this;
            let dataId = item.getAttribute("data-target");
            let dataShadow = item.getAttribute("data-shadow");
            let shadowElement = document.querySelector(dataShadow);

            let element = document.querySelector(dataId);

            if (button.textContent === "Expand") {
                element.classList.remove("custom-collapsed");
                button.textContent = "Collapse";
                img.style.transform = "rotate(180deg)";
                shadowElement.classList.remove("shadow-container");
            } else {
                element.classList.add("custom-collapsed");
                button.textContent = "Expand";
                img.style.transform = "rotate(0deg)";
                shadowElement.classList.add("shadow-container");
            }
        });
    });
});
</script>

<style scoped>
.list-title {
    max-width: 150px;
}
</style>
