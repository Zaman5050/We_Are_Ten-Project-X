<template>
    <div class="sales-report-area">
        <div class="dashboard-heading-container pb--40">
            <h1 class="dashboard-main-heading">Dashboard</h1>
            <Filters @filterByDateRange="handleDateRangeFilter">
                <slot name="header"></slot>
            </Filters>
        </div>

        <template v-if="data?.projectsCount > 0">
            <div class="pb--20">
                <div class="row">
                    <Card
                        title="Active Projects"
                        :count="props.data?.activeProjectCount"
                    />
                    <Card
                        title="COMPLETED Projects"
                        :count="props.data?.completedProjectCount"
                    />
                    <Card
                        title="DELAYED Projects"
                        :count="props.data?.delayedProjectCount"
                    />
                    <Card
                        title="ARCHIVED Projects"
                        v-if="false"
                        :count="props.data?.archivedProjectCount"
                    />
                    <Card
                        v-if="isAuthUserAdmin"
                        title="Profit"
                        :count="`£ ${(props.data?.totalProfit || 0).toFixed(
                            2
                        )}`"
                    />
                </div>
            </div>
            <div 
                v-if="isAuthUserAdmin"
                class="d-flex justify-content-between align-items-center pb--10"
                id="shadow-container-leaves"
            >
                <h6 class="total-projects">
                    People On Leave
                    <span>({{ leaves.length }})</span>
                </h6>
                <div class="collaps-container">
                    <button
                        class="collaps-expand-btn"
                        data-target="#dashboard-leaves"
                        data-Shadow="#shadow-container-leaves"
                    >
                        Collapse
                    </button>
                    <img 
                        class="collaps-icon"
                        src="/assets/images/arrow-icon.svg"
                    />
                </div>
            </div>
            <div id="dashboard-leaves">
                <LeavesListings  v-if="isAuthUserAdmin && !isLeavesCollapsed" :leaves="leaves" />
            </div>
            <div class="mb-3"></div>
            <div
                class="d-flex justify-content-between align-items-center pb--10"
                id="shadow-container-projects"
            >
                <h6 class="total-projects">
                    Projects <span>({{ filteredProjects?.length ?? 0 }})</span>
                </h6>
                <div class="position-relative">
                    <button
                        class="collaps-expand-btn"
                        data-target="#dashboard-projets"
                        data-Shadow="#shadow-container-projects"
                    >
                        Collapse
                    </button>
                    <img
                        class="collaps-icon"
                        src="/assets/images/arrow-icon.svg"
                    />
                </div>
            </div>
            <div id="dashboard-projets">
                <ProjectListings :projects="filteredProjects" v-if="!isProjectsCollapsed" />
            </div>
        </template>
        <template v-else>
            <slot name="create-btn"></slot>
        </template>
    </div>
</template>

<script setup>
import { defineProps, ref, computed, onMounted } from "vue";
import Card from "./Dashboard/Card.vue";
import Filters from "./Dashboard/Filters.vue";
import ProjectListings from "./Dashboard/ProjectListings.vue";
import { applyDateRangeFilter } from "@/helpers.js";
import { useUserStore } from "../../stores/userStore";
import { storeToRefs } from "pinia";
import LeavesListings from "./Dashboard/LeavesListings.vue";

const props = defineProps({
    data: {
        type: Object,
        default: [],
    },
});

const userStore = useUserStore();
const { authUser } = storeToRefs(userStore);
const isAuthUserAdmin = ref(false);
const isLeavesCollapsed = ref(false);
const isProjectsCollapsed = ref(false);
const dateRangeRef = ref({
    type: "allTime",
    range: { start: "", end: "" },
});

// Handlers to update filter values
const handleDateRangeFilter = (payload) => {
    dateRangeRef.value = payload;
};

const leaves = computed(() => props.data?.leaves || []);
const filteredProjects = computed(() => {
    let filteredData = props.data?.projects ?? [];

    // Apply date range filter
    if (dateRangeRef.value.type) {
        filteredData = applyDateRangeFilter(filteredData, dateRangeRef.value);
    }
    return filteredData;
});
const toggleSection = (section) => {
    if (section === "leaves") {
        isLeavesCollapsed.value = !isLeavesCollapsed.value;
    } else if (section === "projects") {
        isProjectsCollapsed.value = !isProjectsCollapsed.value;
    }
};
onMounted(() => {
    isAuthUserAdmin.value =
        authUser.value.roles.findIndex((role) => role.name === "admin") !== -1;
});



    $(document).ready(function() {

        let collapsButton = document.querySelectorAll('.collaps-expand-btn');
        collapsButton.forEach((item) => {


            item.addEventListener('click', function(e) {
                let img = e.target.nextElementSibling
            let button = this;
            let dataId = item.getAttribute('data-target');
            let element =document.querySelector(dataId);
            let dataShadow = item.getAttribute('data-Shadow');
            let icon = document.querySelector('.collaps-icon');
            let shadowElement =document.querySelector(dataShadow);

            if (button.textContent === 'Expand') {
                        
                        element.classList.remove('custom-collapsed');
                        
                        button.textContent = 'Collapse';
                        img.style.transform='rotate(180deg)'
                       shadowElement.classList.remove('shadow-container')

                    } else {
                      
                        element.classList.add('custom-collapsed');
                        button.textContent = 'Expand';
                        img.style.transform='rotate(0deg)'
                       shadowElement.classList.add('shadow-container')

                    }
                });

        })


    })
</script>
