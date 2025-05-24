<template>
    <div class="dashboard-heading-container pb--40">
        <h1 class="dashboard-main-heading">Time Sheets</h1>
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
                <slot name="export-btn"></slot>
                <a class="filter-btn bg-yellow" data-bs-toggle="modal" data-bs-target="#add-time-entry-popup">Add Time Entry</a>
            </div>
        </div>
    </div>
    <div class="pb--20 d-flex table-filter-dp flex-wrap">
        <div class="dropdown">
            <a class="d-flex align-items-center filter-btn border-0 dropdown-toggle gap-3" href="#" role="button"
                id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="assets/images/filter-icon.svg" />
                <span>Filter by Project</span>
            </a>
            <ul class="ul-div dropdown-menu border-0" aria-labelledby="dropdownMenuLink">
                <li>
                    <a class="dropdown-item mb-2" role="button" @click="$emit('filterByProject', null)">None</a>
                </li>
                <li v-for="project in props.projectsList" :key="project.id">
                    <a class="dropdown-item mb-2 text-truncate" role="button"
                        @click="$emit('filterByProject', project.id)">{{ project.name }}</a>
                </li>
            </ul>
        </div>
        <div class="dropdown">
            <a class="d-flex align-items-center filter-btn border-0 dropdown-toggle gap-3" href="#" role="button"
                id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="assets/images/filter-icon.svg" />
                <span>Filter by Entry Type</span>
            </a>
            <ul class="ul-div dropdown-menu border-0" aria-labelledby="dropdownMenuLink">
                <li>
                    <a role="button" class="dropdown-item mb-2" @click="$emit('filterByType', null)">None</a>
                </li>
                <li>
                    <a role="button" class="dropdown-item mb-2" @click="$emit('filterByType', 1)">Automatic</a>
                </li>
                <li>
                    <a role="button" class="dropdown-item mb-2" @click="$emit('filterByType', 0)">Manual</a>
                </li>
            </ul>
        </div>
        <div class="dropdown">
            <a class="d-flex align-items-center filter-btn border-0 dropdown-toggle gap-3" href="#" role="button"
                id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="assets/images/filter-icon.svg" />
                <span>Filter by Stage</span>
            </a>
            <ul class="ul-div dropdown-menu border-0" aria-labelledby="dropdownMenuLink">
                <li>
                    <a class="dropdown-item mb-2" role="button" @click="$emit('filterByStage', null)">None</a>
                </li>
                <li v-for="stage in props.stageList" :key="stage.id">
                    <a class="dropdown-item mb-2" role="button" @click="$emit('filterByStage', stage.id)">{{
                        stage.stage_name }}</a>
                </li>
            </ul>
        </div>
        <div class="dropdown">
            <a class="d-flex align-items-center filter-btn border-0 dropdown-toggle gap-3" href="#" role="button"
                id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="assets/images/filter-icon.svg" />
                <span>Filter by Team Member</span>
            </a>
            <ul class="ul-div dropdown-menu border-0" aria-labelledby="dropdownMenuLink">
                <li>
                    <a class="dropdown-item mb-2" role="button" @click="$emit('filterByMember', null)">None</a>
                </li>
                <li v-for="member in props.members" :key="member.id">
                    <a class="dropdown-item mb-2" role="button" @click="$emit('filterByMember', member.id)">{{
                        member.full_name }}</a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits, ref } from "vue";
import flatPickr from "vue-flatpickr-component";

const props = defineProps({
    members: {
        type: Object,
        default: () => { },
    },
    stageList: {
        type: Object,
        default: () => { },
    },
    projectsList: {
        type: Object,
        default: () => { },
    },
});
const emits = defineEmits([
    "filterByDateRange",
    "filterByType",
    "filterByStage",
    "filterByMember",
    "filterByProject",
]);

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
            range: { start: '', end: '' }
        });
    }
};

const handleDateRangeChange = (range) => {
    if (range.length > 1) {
        emits("filterByDateRange", {
            type: dateRef.value,
            range: { start: range[0], end: range[1] }
        });
    }
};
const dateFilterActive = (type) => {
    return dateRef.value === type;
};
</script>

<style scoped>
.ul-div {
    width: 100%;
    min-width: 138px;
    padding: 15px 18px;
    box-shadow: 0px 1px 2px 0px #5b687152;
    box-shadow: 0px 0px 2px 0px #1a202452;
}

.vue-datepicker {
    max-width: 200px;
    /* Adjust width if needed */
}

.dashboard-anchors {
    color: #252c32;
    cursor: pointer;
    font-size: 18px;
    border-radius: 6px;
    display: inline-block;
}

.dashboard-anchors.active {
    background: #FFDC5F;
    /* color: #1A1B1F; */
}

#dateRange {
    height: 0;
    overflow: hidden;
    width: 0;
}
</style>
