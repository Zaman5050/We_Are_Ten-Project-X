<template>
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center pb--10">
            <div class="position-relative">
                <img class="collaps-icon" src="/assets/images/arrow-icon.svg" />
            </div>
        </div>
        <div class="dashboard-table-container">
            <table class="table mb-0 dashboard-table">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Contact Name</th>
                        <th>Email Address</th>
                        <th width="150px">Address</th>
                        <th>Phone Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tr v-for="(supplier, index) in suppliers" :key="index">
                    <td>
                        <p class="text-truncate">
                            {{ supplier?.company_name }}
                        </p>
                    </td>
                    <td>
                        <p class="text-truncate">{{ supplier?.name }}</p>
                    </td>
                    <td>
                        <p class="text-truncate">{{ supplier?.email }}</p>
                    </td>
                    <td>
                        <p class="text-truncate list-title">
                            {{ supplier?.address }}
                        </p>
                    </td>
                    <td>
                        <p class="text-truncate">
                            {{ supplier?.phone_number }}
                        </p>
                    </td>
                    <td>
                        <div>
                            <button
                                class="edit-dell-btn me-2 editSupplier"
                                type="button"
                                @click="editSupplier(supplier)"
                            >
                                <img src="assets/images/edit-icon.svg" />
                            </button>
                            <button
                                class="edit-dell-btn"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#delete-task"
                                @click="setSupplierForDeletion(supplier.uuid)"
                            >
                                <img src="assets/images/delete-icon.svg" />
                            </button>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="modal fade " id="delete-task" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <!-- <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5> -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="delete-warning">Are You Sure To Want  Delete This Supplier?</p>
                </div>
                <div class="modal-footer border-0 pt-0 flex-nowrap">
                    <button style="width: 50%; background: #fff; color: #000;" type="button"
                        class="apply-leave-btn"  data-bs-dismiss="modal" aria-label="Close">No</button>
                    <button style="width: 50%;" type="submit"  @click="deleteSupplier(selectedSupplierUuid)"  class="apply-leave-btn">Yes</button>
                    <form hidden>
                        <button type="submit" >Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- <AddTimeEntry :memberProjects="memberProjects"></AddTimeEntry> -->
</template>

<script setup>
import { computed, defineProps, ref, onMounted } from "vue";
import axios from "axios";
import { useSupplierStore } from "../../stores/supplierStore";

const supplierStore = useSupplierStore();

const props = defineProps({
    suppliers: {
        type: Object,
        default: [],
    },
});

const timesheetCollapsed = ref({});
const selectedSupplierUuid = ref(null);

onMounted(() => {
    const savedState = localStorage.getItem("timesheetCollapsed");
    if (savedState) {
        timesheetCollapsed.value = JSON.parse(savedState);
    }
});

const handleToggleCollapse = (id) => {
    timesheetCollapsed.value[id] = !timesheetCollapsed.value[id];
    localStorage.setItem(
        "timesheetCollapsed",
        JSON.stringify(timesheetCollapsed.value)
    );
};
const isCollapsed = (id) => {
    return timesheetCollapsed.value[id] || false;
};

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

const handleDeleteEntry = async (uuid) => {
    if (!window.confirm("Are you sure you want to delete this time entry?"))
        return false;

    try {
        const response = await axios.delete(`/timesheet/${uuid}`);
        if (response.data.error) {
            alert("Somthing went wrong");
        }
        window.location.reload();
    } catch (error) {
        alert(error.message);
    }
};
$(document).ready(function () {
    var toggleButton = document.getElementById("toggleButton");
    if (toggleButton) {
        toggleButton.addEventListener("click", function () {
            var icon = document.querySelector(".collaps-icon");
            var button = this;

            if (button.textContent === "Expand") {
                icon.style.transform = "rotate(0deg)";
            } else {
                icon.style.transform = "rotate(180deg)";
            }
        });
    }
});
const editSupplier = (supplier) => {
    supplierStore.setSupplier(supplier);
    supplierStore.setMode("edit");
    supplierStore.setHref("/");
    const model = new bootstrap.Modal(
        document.getElementById("create-new-supplier")
    );
};
const setSupplierForDeletion = (uuid) => {
    selectedSupplierUuid.value = uuid;
};
const deleteSupplier = async (uuid) => {
    try {
        const response = await axios.delete(`/supplier/${uuid}`);
        if (response.data.error) {
            alert("Somthing went wrong");
        }
        window.location.href = '/supplier';

    } catch (error) {
        alert(error.message);
    }
};
</script>

<style scoped>
.list-title {
    max-width: 350px;
}
</style>
