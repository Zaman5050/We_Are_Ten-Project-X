<template>
    <div class="d-flex align-items-center justify-content-between dashboard-btns-filter-container">
        <div class="dashboard-button-container">
            <span @click="applyDateFilter('allTime')" role="button" :class="[
                'dashboard-anchors px-2 py-1',
                { active: dateFilterActive('allTime') },
            ]">All Times</span>

            <span @click="applyDateFilter('thisYear')" :class="[
                'dashboard-anchors px-2 py-1',
                { active: dateFilterActive('thisYear') },
            ]" role="button">This Year</span>

            <span @click="applyDateFilter('lastMonth')" :class="[
                'dashboard-anchors px-2 py-1',
                { active: dateFilterActive('lastMonth') },
            ]" role="button">Last Month</span>

            <label for="dateRange" @click="applyDateFilter('customRange')" :class="[
                'dashboard-anchors px-2 py-1',
                { active: dateFilterActive('customRange') },
            ]" role="button">Custom</label>

            <flat-pickr v-model="dateRange" :config="ConfigFlatPicker" @on-change="handleDateRangeChange"
                class="invisible" id="dateRange" name="dateRange" />
        </div>

        <div class="d-flex gap-3">
            <div class="dropdown">
                <a class="d-flex align-items-center filter-btn border-0 dropdown-toggle gap-3" href="#" role="button"
                    id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="/assets/images/filter-icon.svg" />
                    <span>Filter by</span>
                </a>
                <ul style="
                        width: 100%;
                        min-width: 138px;
                        padding: 15px 18px;
                        box-shadow: 0px 1px 2px 0px #5b687152,
                            0px 0px 2px 0px #1a202452;
                    " class="dropdown-menu border-0" aria-labelledby="dropdownMenuLink">
                    <li>
                        <a class="dropdown-item mb-2" href="/dashboard?status=active">Active</a>
                    </li>
                    <li>
                        <a class="dropdown-item mb-2" href="/dashboard?status=completed">Completed</a>
                    </li>
                    <li>
                        <a class="dropdown-item mb-2" href="/dashboard?status=delayed">Delayed</a>
                    </li>
                    <li hidden>
                        <a class="dropdown-item" href="/dashboard?status=archieved">Archived</a>
                    </li>
                </ul>
            </div>
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits, ref } from "vue";
import flatPickr from "vue-flatpickr-component";

const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    count: {
        type: Number,
        default: 0,
    },
});

const emits = defineEmits(["filterByDateRange"]);

const ConfigFlatPicker = ref({
    mode: "range",
});

const dateRef = ref("allTime");
const dateRange = ref(new Date()); // for date range

const applyDateFilter = (type) => {
    dateRef.value = type;
    if (type != "customRange") {
        emits("filterByDateRange", {
            type: type,
            range: { start: "", end: "" },
        });
    }
};

const handleDateRangeChange = (range) => {
    if (range.length > 1) {
        emits("filterByDateRange", {
            type: dateRef.value,
            range: { start: range[0], end: range[1] },
        });
    }
};
const dateFilterActive = (type) => {
    return dateRef.value === type;
};
</script>

<style scoped>
.vue-datepicker {
    max-width: 200px;
    /* Adjust width if needed */
}

.dashboard-anchors {
    color: #1A1B1F;
    cursor: pointer;
    font-size: 18px;
    border-radius: 6px;
    display: inline-block;
}

.dashboard-anchors.active {
    background: #FFDC5F;
}

#dateRange {
    height: 0;
    overflow: hidden;
    width: 0;
}
</style>
