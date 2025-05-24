<template>
    <div>
        <button
            class="add-new-task-btn"
            @click="handleCreateTask"
        >
            <img src="/assets/images/add-task.svg" />
        </button>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <input
                    class="nav-input p-task-input"
                    name="search"
                    type="search"
                    placeholder="Search"
                    @keydown.enter.prevent
                    v-model="searchQuery"
                    style="
                        background-image: url('/assets/images/search-icon.svg');
                    "
                />
                <div style="max-width: 300px; width: 100%">
                    <MemberAvatarList
                        :users="filteredMembers"
                        :size="filteredMembers.length"
                        @avatarClick="HandelAssignee"
                        :max-length="5"
                    ></MemberAvatarList>
                </div>
            </div>

            <div class="custom-tabs-container">
                <button
                    @click="toggleViewMode(false)"
                    :class="['tab-img ', { 'active-sub-tab': !isBoardView }]"
                >
                    <img :src="'/assets/images/table-view.svg'" />
                </button>

                <button
                    @click="toggleViewMode(true)"
                    :class="['tab-img ', { 'active-sub-tab': isBoardView }]"
                >
                    <img :src="'/assets/images/board-view.svg'" />
                </button>
            </div>
        </div>
        <div class="dashboard-button-container mb-4">
            <a
                :class="[
                    'dashboard-anchors ',
                    { 'active-background': !tabStatus },
                ]"
                @click="ActiveTabFilterHandle(false)"
                >Tasks</a
            >
            <a
                :class="[
                    'dashboard-anchors ',
                    { 'active-background': tabStatus },
                ]"
                @click="ActiveTabFilterHandle(true)"
                >Archived</a
            >
        </div>
        <ListingView
            @archivedTask="handleArchivedTask"
            :tasks="filteredTasks"
            :task-statuses="taskStatuses"
            :is-board-view="isBoardView"
        />
    </div>
</template>

<script setup>
import { computed, ref, defineProps, watch, onMounted, reactive,onUnmounted } from "vue";
import ListingView from "./ListingView.vue";
import Avatar from "../Avatar.vue";
import MemberAvatarList from "../MemberAvatarList.vue";
import { useTaskStore, useTaskSettingStore } from "../../stores/taskStore";
import { storeToRefs } from "pinia";
import { useToast } from "vue-toastification";

const taskStore = useTaskStore();
const taskSettingStore = useTaskSettingStore();
const { isBoardView } = storeToRefs(taskSettingStore);
const toast = useToast();

const handleCreateTask = () => {
    if (props.stages.length === 0) {
        toast.error("Please add a stage from the project details before adding any tasks.", {
            timeout: 3000,
            position: "top-right",
        });
    } else{
       
        taskStore.resetTask();
        taskStore.setMode("create");
        var taskModel = new bootstrap.Modal(document.getElementById('create-new-task'));
        taskModel && taskModel.show();
    }
};

const props = defineProps({
    tasks: {
        type: Array,
        default: [],
    },
    taskStatuses: {
        type: Array,
        default: [],
    },
    members: {
        type: Array,
        default: [],
    },
    stages: {
        type: Array,
        default: [],
    },
});

const state = reactive({
    tasks: props.tasks,
});
const tabStatus = ref(false);
const currentAssignee = ref([]);
const searchQuery = ref("");

const toggleViewMode = (status) => {
    taskSettingStore.setBoardView(status);
};

const ActiveTabFilterHandle = (status) => {
    tabStatus.value = status;
};

const handleArchivedTask = (taskUuid, status) => {
    const findTaskIndex = state.tasks.findIndex((t) => t.uuid == taskUuid);
    if (findTaskIndex != -1) {
        state.tasks[findTaskIndex].archive = Boolean(!status);
    }
};

const randomColor = () => {
    return `#${getRandomValue().toString(16).padStart(2, "0")}${getRandomValue()
        .toString(16)
        .padStart(2, "0")}${getRandomValue().toString(16).padStart(2, "0")}`;
};

const getRandomValue = () => {
    return Math.floor(Math.random() * 151); // Generates a number between 0 and 150
};

// Function to update the assignees from URL parameters
const updateAssignees = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const assigneeParam = urlParams.get("assignee");
    currentAssignee.value = assigneeParam
        ? [...new Set(assigneeParam.split(",").filter((x) => !!x))]
        : [];
};

const filteredMembers = computed(() => {
    return props.members.map((member) => ({
        ...member,
        active: currentAssignee.value.includes(member.uuid),
    }));
});

// Computed property to filter tasks based on the search query
const filteredTasks = computed(() => {
    let tasks = state.tasks;

    // Filter by assignee if currentAssignee is not empty
    if (currentAssignee.value.length) {
        tasks = tasks.filter((task) =>
            task.members.some((user) =>
                currentAssignee.value.includes(user.uuid)
            )
        );
    }

    if (tabStatus.value) {
        tasks = tasks.filter((task) => !!task.archive);
    } else {
        tasks = tasks.filter((task) => !task.archive);
    }

    // Convert search query to lower case for case-insensitive matching
    const query = searchQuery.value.toLowerCase().replaceAll("-", "–");

    // Filter tasks based on the search query
    tasks = tasks.filter(
        (task) =>
            task.description.toLowerCase().includes(query) ||
            task.title.toLowerCase().includes(query) ||
            task.task_code.toLowerCase().includes(query)
    );

    return tasks;
});

const HandelAssignee = (uuid) => {
    updateQueryParam("assignee", uuid);
};

function updateQueryParam(key, value) {
    // Create a new URLSearchParams object from the current URL
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);

    // Get the existing values for the given key
    const existingValues = params.get(key) ? params.get(key).split(",") : [];

    // Create a new set of values to handle duplicates and ensure uniqueness
    const valuesSet = new Set(existingValues);

    // If the value exists, remove it; otherwise, add it
    if (valuesSet.has(value)) {
        valuesSet.delete(value);
    } else {
        valuesSet.add(value);
    }

    // Update the query parameter or delete it if empty
    if (valuesSet.size > 0) {
        params.set(key, Array.from(valuesSet).join(","));
    } else {
        params.delete(key);
    }

    // Update the URL without reloading the page
    window.history.replaceState({}, "", `${url.pathname}?${params.toString()}`);

    // Manually trigger updateAssignees after updating the URL
    updateAssignees();
}

onMounted(() => {
    taskStore.setTasks(props.tasks);
    const stateTasks = taskStore.getTasks;
    state.tasks = stateTasks;
    
    updateAssignees();
});
</script>
