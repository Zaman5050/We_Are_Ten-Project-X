<template>
    <vue-loader v-if="loading" />
    <div>
        

        <timesheet-filters
            :projectsList="projectsList"
            :stageList="stageList"
            :members="props.members"
            @filterByDateRange="handleDateRangeFilter"
            @filterByProject="handleProjectFilter"
            @filterByType="handleTypeFilter"
            @filterByStage="handleStageFilter"
            @filterByMember="handleMemberFilter"
        >
            <template v-slot:export-btn>
                <download-excel
                    :data="exportedData"
                    :fields="xls_json_fields"
                    :json_meta="xls_json_meta"
                    class="filter-btn bg-yellow"
                    worksheet="Timesheets"
                    name="Timesheets.xls"
                    type="xlsx"
                >
                    Export to Excel
                </download-excel>
            </template>
        </timesheet-filters>
        <timesheet-listing
            :timesheets="filteredTimesheets"
            :pagination="pagination"
            :member-projects="memberProjects"
        ></timesheet-listing>
        <div class="text-center">
            <button @click="loadMoreTimesheets" class="btn btn-primary">
            Load More
        </button>

        </div>
    </div>
</template>

<script setup>
import { computed, ref, defineProps, onMounted, watch } from "vue";
import TimesheetListing from "./TimesheetListing.vue";
import TimesheetFilters from "./TimesheetFilters.vue";
import VueLoader from "../Common/Loader.vue";
import axios from 'axios'

const props = defineProps({
    timesheets: {
        type: Object,
        default: () => {},
    },
    members: {
        type: Object,
        default: () => {},
    },
    memberProjects: {
        type: Object,
        default: () => {},
    },
    pagination: {
        type: Object, // Expecting the pagination object
        default: () => ({
            currentPage: 1,
            lastPage: 1,
            total: 0,
            perPage: 20,
        }),
    },
});
const timesheets = ref({ ...props.timesheets });
const loading = ref(false);
const xls_json_meta = () => {
    return [
        {
            key: "charset",
            value: "utf-8",
        },
    ];
};
const loadMoreTimesheets = async () => {
    if (props.pagination.currentPage < props.pagination.lastPage) {
        const nextPage = props.pagination.currentPage + 1;
        if (loading.value) return;
        try {
            loading.value = true;
            const response = await fetch(`/timesheet?page=${nextPage}`, {
                method: 'GET', 
                headers: {
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json', 
                },
            });
            
            loading.value = false;
            const data = await response.json();

            const currentMaxIndex = Object.keys(timesheets.value).length;
                timesheets.value = {
                ...timesheets.value, // Spread the old timesheets
                ...data.timesheets.reduce((acc, timesheet, index) => {
                    // Use the next available numeric index
                    acc[currentMaxIndex + index] = timesheet;
                    return acc;
                }, {}),
            };
            // Update the pagination information
            props.pagination.currentPage = nextPage;

        } catch (error) {
            loading.value = false;
            console.error("Error loading more timesheets:", error);
        }
    }
};






const xls_json_fields = computed(() => {
    return {
        ID: "task.task_code",
        "Project Name": "task.project.name",
        "Task Title": "task.title",
        Status: "task.status.title",
        "Starting Time": "start_time_formated",
        "Ending Time": "end_time_formated",
        "Total Time": {
            field: "total_time",
            callback: (value) => {
                return convertMinutesToReadableTime(value);
            },
        },
        Stage: "task.stage.stage_name",
    };
});

// Reactive state for filters and lists
const stageList = ref([]);
const projectsList = ref([]);
const typeRef = ref(null);
const stageRef = ref(null);
const memberRef = ref(null);
const projectRef = ref(null);
const dateRangeRef = ref({
    type: "allTime",
    range: { start: "", end: "" },
});

// Handlers to update filter values
const handleDateRangeFilter = (payload) => {
    debugger;
    dateRangeRef.value = payload;
    resetPagination();
    loadMoreTimesheets();
};

const handleTypeFilter = (type) => {
    typeRef.value = type;
    resetPagination();
    loadMoreTimesheets();
};


const handleStageFilter = (stage) => {
    stageRef.value = stage;
    resetPagination();
    loadMoreTimesheets();
};

const handleMemberFilter = (member) => {
    memberRef.value = member;
    resetPagination();
    loadMoreTimesheets();
};

const handleProjectFilter = (project) => {
    projectRef.value = project;
    resetPagination();
    loadMoreTimesheets();
};
const resetPagination = () => {
    props.pagination.currentPage = 1;
};

onMounted(() => {
    const data = Object.values(props.timesheets) || [];

    // Extract unique stages and projects using Set for better performance
    const stages = new Set();
    const projects = new Set();

    data.forEach((timesheet) => {
        const stage = timesheet?.task?.stage;
        const project = timesheet?.task?.project;

        if (stage && !stages.has(stage.id)) {
            stages.add(stage.id);
            stageList.value.push(stage);
        }

        if (project && !projects.has(project.id)) {
            projects.add(project.id);
            projectsList.value.push(project);
        }
    });
});
const filteredTimesheets = computed(() => {
    // Unwrap the Proxy by using `Object.values` or directly accessing the `value`
    let filteredData = Object.values(timesheets.value ?? {}); // Extract values directly from Proxy

    // Apply date range filter if necessary
    if (dateRangeRef.value.type) {
        filteredData = applyDateRangeFilter(filteredData, dateRangeRef.value);
    }

    // Convert the array of timesheets back into a grouped object by date
    const filteredResult = filteredData.reduce((acc, timesheet) => {
        // Accessing date from timesheet directly
        const date = timesheet.created_date; 

        // Apply the filter conditions for the current timesheet
        const matchesType =
            typeRef.value === null || timesheet.is_automatic === typeRef.value;
        const matchesProject =
            projectRef.value === null || timesheet?.task?.project_id === projectRef.value;
        const matchesStage =
            stageRef.value === null || timesheet?.task?.stage_id === stageRef.value;
        const matchesMember =
            memberRef.value === null ||
            timesheet?.task?.users.some((user) => user.id === memberRef.value);

        if (matchesType && matchesProject && matchesStage && matchesMember) {
            // If all filters match, add timesheet to the appropriate date group
            if (!acc[date]) {
                acc[date] = [];
            }
            acc[date].push(timesheet);
        }

        return acc;
    }, {});

    return filteredResult; // Return the filtered result as an object grouped by date
});


const exportedData = computed(() => {
    return Object.values(filteredTimesheets.value).flat();
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

const applyDateRangeFilter =  (data, payload) => {
    const today = new Date();
    let startDate = null;
    let endDate = null;

    switch (payload.type) {
        case "thisYear":
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = new Date(today.getFullYear(), 11, 31);
            break;
        case "lastMonth":
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        case "customRange":
            startDate = new Date(payload.range.start);
            endDate = new Date(payload.range.end);
            break;
        default:
            return data;
    }

    // let dateFilterData;
    return data.filter((timesheet) => {
        const createdDate = new Date(timesheet.start_time);
        return createdDate >= startDate && createdDate <= endDate;
    });
    // if (dateFilterData.length === 0) {
    //     // Load next page if no data on current page
    //     const nextPage = props.pagination.currentPage + 1; // Increment page number
    //     const response = await fetch(`/timesheet?page=${nextPage}&start_date=${startDate}&end_date=${endDate}`, {
    //         method: 'GET',
    //         headers: {
    //             'Content-Type': 'application/json',
    //             'Accept': 'application/json',
    //         },
    //     });
    //     const data = await response.json();

    //     // Update the timesheets
    //     const currentMaxIndex = Object.keys(timesheets.value).length;
    //     timesheets.value = {
    //         ...timesheets.value, 
    //         ...data.timesheets.reduce((acc, timesheet, index) => {
    //             acc[currentMaxIndex + index] = timesheet;
    //             return acc;
    //         }, {}),
    //     };

    //     // Update pagination info
    //     props.pagination.currentPage = nextPage;
    // }
    // return dateFilterData;
};
</script>

<style scoped>
/* Optionally, if you want to apply additional styling for the button */
.text-center {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px; /* Adds space above the button */
}
</style>