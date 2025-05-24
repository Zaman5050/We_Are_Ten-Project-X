<script setup>
import axios from "axios"
import { defineProps, reactive, onMounted, ref,  watch, computed, onUnmounted } from 'vue';
import MemberAvatarList from "../MemberAvatarList.vue";
import BoardView from "./BoardView.vue";
import OpenModel from "../OpenModel.vue";
import LogTimerBlock from "./LogTimerBlock.vue";
import eventBus from "../../eventBus";

import { useTaskStore } from "../../stores/taskStore";
const taskStore = useTaskStore();

const handleEditTaskClick = (task) => {
    taskStore.setTask(task);
    taskStore.setMode("edit");

    var taskModel = new bootstrap.Modal(document.getElementById('create-new-task'));
    taskModel && taskModel.show();
};

const props = defineProps({
    isBoardView: Boolean,
    taskStatuses: {
        type: Array,
        default: [],
    },
    tasks: {
        type: Array,
        default: [],
    },
});

const emit = defineEmits(['archivedTask'])

const collapsedSections = ref({});
const groupedTasks = ref([])
const tasksList = ref([])

onMounted(() => {

    const savedState = localStorage.getItem('collapsedSections');
    if (savedState) {
      collapsedSections.value = JSON.parse(savedState);
    }

    tasksList.value = props.tasks;
    eventBus.on('timeLogEvent', handleTimeLogEvent);
    updateTaskByView(tasksList.value);
})

onUnmounted(() => {
    eventBus.off('timeLogEvent', handleTimeLogEvent);
}); 


const isCollapsed = (id) => {
    return collapsedSections.value[id] || false;
}

const handleTimeLogEvent = (data) => {
    tasksList.value.forEach(task => {
        if (task.uuid === data.taskUuid) {
            if(+data.timer > 0){
                task.total_time = data.timer
            }
            task.timer_mode = data.timer_mode;
            console.log(task)
        }else{
            if(task.timer_mode == 'active' && data.timer_mode != 'idle'){
                task.timer_mode = 'paused';
            }
        }
    });
};

const updateTaskByView = (tasksList) => {
    groupedTasks.value = tasksList
        .sort((a, b) => a.stage_id - b.stage_id)
        .reduce((acc, task) => {
            const viewType = props.isBoardView ? task.status.title : task.stage ? task.stage?.stage_name : 'Stage ';
            if (!acc[viewType]) acc[viewType] = [];
            acc[viewType].push(task);
            return acc;
        }, {});
};

watch(() => props.tasks, (newTasks) => {
    updateTaskByView(props.tasks);
}, { deep: true });
const isAuthUserTeamMember = (task) => {
    // Find the current task in props.tasks by UUID
    const currentTask = props.tasks.find((t) => t.uuid === task.uuid);

    // Ensure the task exists and has members before accessing members
    if (currentTask && Array.isArray(currentTask.members)) {
        return currentTask.members.some((member) => member.uuid === currentAuth.authUuid);
    }

    // Return false if task or members are not available
    return false;
};

watch(() => props.isBoardView, (newView, oldView) => {
    updateTaskByView(props.tasks);
});

const randomColor = () => {
    return `#${getRandomValue().toString(16).padStart(2, '0')}${getRandomValue().toString(16).padStart(2, '0')}${getRandomValue().toString(16).padStart(2, '0')}`;
}

const getRandomValue = () => {
    return Math.floor(Math.random() * 151); // Generates a number between 0 and 150
}

const flagTaskHandle = (taskUuid) => {

    axios.get('/task/mark-flag/' + taskUuid)
        .then((response) => {
            console.log(response)
            const taskIndex = tasksList.value.findIndex(task => task.uuid === taskUuid);
            if (taskIndex !== -1) {
                tasksList.value[taskIndex].flaged_by = response.data?.status ? 1 : "";
            }
        })
        .catch((error) => {
            console.log(error);
        })
};

const archiveTask = (taskUuid, status) => {

    axios.get('/task/mark-archived/' + taskUuid+'/' + Number(status))
        .then((response) => {
            console.log(response)
            if(response){
                emit('archivedTask', taskUuid , status);
            }
        })
        .catch((error) => {
            console.log(error);
        })
};

const updateStatusHandle = ({ taskUuid, newStatusUuid }) => {

    console.log(taskUuid, newStatusUuid)
    axios.get(`/task/update-status/${taskUuid}/${newStatusUuid}`)
        .then(response => {
            const taskIndex = tasksList.value.findIndex(task => task.uuid === taskUuid);
            if (taskIndex !== -1) {
                const updatedStatus = props.taskStatuses.find(status => status.uuid === newStatusUuid);
                if (updatedStatus) {
                    tasksList.value[taskIndex].status = updatedStatus;
                    tasksList.value[taskIndex].status_id = updatedStatus.index;
                }
            }
            updateTaskByView(tasksList.value);  // Update the view with the new task status
        })
        .catch(error => {
            console.log(error);
        });
};

const toggleButton = (index) => {
    let collapsButton = document.querySelectorAll('.collaps-expand-btn');
    let button = collapsButton[index];
    let img = button.nextElementSibling;
    let dataId = button.getAttribute('data-target');
    let element = document.querySelector(dataId);
    let dataShadow = button.getAttribute('data-Shadow');
    let shadowElement = document.querySelector(dataShadow);

    if (button.textContent === 'Expand') {
        element.classList.remove('custom-collapsed');
        button.textContent = 'Collapse';
        img.style.transform = 'rotate(180deg)';
        shadowElement.classList.remove('shadow-container');
    } else {
        element.classList.add('custom-collapsed');
        button.textContent = 'Expand';
        img.style.transform = 'rotate(0deg)';
        shadowElement.classList.add('shadow-container');
    }
};


</script>

<template>
    <div>
        <template v-if="props.isBoardView">


            <BoardView :tasks="tasks" :task-statuses="taskStatuses" @flag-task="flagTaskHandle"
                @archive-task="archiveTask" @update-status="updateStatusHandle" @edit-task="handleEditTaskClick" />

        </template>

        

        <template v-else v-for="(tasks, stage, index) in groupedTasks" :key="index">
            <div class="" :id="'shadow-container-stageIndex-' + index">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="task-stage-heading">{{ stage }} ( {{ tasks.length }} )</h6>
                    <div class="position-relative">
                        <button class="collaps-expand-btn" :data-target="'#stageIndex-' + index" :data-shadow="'#shadow-container-stageIndex-' + index" @click="toggleButton(index)">Collapse</button>
                        <img class="collaps-icon" src="/assets/images/arrow-icon.svg">
                    </div>
                </div>
                <div class="task-table-container">
    
                    <table class="table" :id="'stageIndex-' + index">
                       <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>TEAM MEMBER</th>
                            <th>START DATE</th>
                            <th>END DATE</th>
                            <th>STATUS</th>
                            <th></th>
                        </tr>
                       </thead>
    
                        <template v-if="!isCollapsed(stage)">
                        <tr class="table-body" v-for="(task, key) in tasks" :key="key" :data-task-uuid="task.uuid"
                            :id="stage + '-tr-' + index">
    
                            <td v-show="false">
                                <label class="check-container">
                                    <input type="checkbox" checked="checked">
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td class="task-ID"> {{ task.task_code }}</td>
                            <td>
                                <p class="text-truncate task-title w-10 text-decoration-underline" @click="handleEditTaskClick(task)"  >
                                    {{ task.title }}
                                </p>
                            </td>
                            <td>
                                <div class="w-147">

                                    <MemberAvatarList :users="task.members" :size="task.members?.length" :uindex="key"></MemberAvatarList>
                                </div>
                            </td>
                            <td> {{ task.start_date }}</td>
                            <td> {{ task.due_date }}</td>
                            <td :id="index + '-td-' + key">
                                <div class="dropdown three-dots-dropdown w-153">
                                    <a
                                        class="d-flex filter-btn border-0 dropdown-toggle gap-3 px-0 darker-grotesque-regular"
                                        href="#" role="button" :id="'dropdownMenuLink-' + index" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <span class="status-title">{{ task.status.title }}</span>
                                        <img src="/assets/images/arrow-icon.svg">
                                    </a>
                                    <ul style="min-width: min-content!important; padding: 15px 18px; box-shadow: rgba(26, 32, 36, 0.32) 0px 0px 2px 0px;"
                                        class="status-dropdown dropdown-menu border-0"
                                        :aria-labelledby="'dropdownMenuLink-' + index">
    
                                        <template v-if="taskStatuses" v-for="(status, sKey) in taskStatuses" :key="sKey">
                                            <li>
                                                <span role="button"
                                                    @click="updateStatusHandle({ taskUuid: task.uuid, newStatusUuid: status.uuid })"
                                                    class="dropdown-item mb-2">{{ status.title }}</span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-3 action-wrap">
                                    <img v-if="task.flaged_by" class="red-flag" src="/assets/images/red-flag.svg">
    
                                    <a style="width: fit-content; margin-left: auto;" class="filter-btn border-0 " href="#"
                                        role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="/assets/images/three-dots.svg">
                                    </a>
                                    <ul style="min-width: 104px; padding: 15px 7px; box-shadow: rgba(26, 32, 36, 0.32) 0px 0px 2px 0px;"
                                        class="dropdown-menu border-0 project-task-edit-dell" aria-labelledby="dropdownMenuLink">
                                        <li> 
                                            <span role="button" class="dropdown-item mb-2"  @click="handleEditTaskClick(task)">Edit</span>
                                        </li>
                                        <li><span role="button" @click="flagTaskHandle(task.uuid)"
                                                class="dropdown-item mb-2"> {{ task.flaged_by ?
                                                'Unflag' :'Flag' }}</span></li>
                                        <li><span role="button" @click="archiveTask(task.uuid, task.archive)"
                                                class="dropdown-item mb-2">{{ task.archive ? 'Un-Archive' : 'Archive' }}</span></li>
                                        <li><a style="color: #F8624E;" role="button" class="dropdown-item mb-2 taskDeleted"
                                                :data-uuid="task.uuid"
                                                :data-tr="stage + '-tr-' + index" data-bs-toggle="modal"
                                                data-bs-target="#delete-task">Delete</a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        </template>
                    </table>
                </div>
            </div>
        </template>


        <template v-for="(task, index) in tasksList" :key="index">
            <LogTimerBlock v-if="task.timer_mode === 'active' && isAuthUserTeamMember(task)" :task="task" :block-index="index" />
        </template>

    </div>
</template>

<style scoped>
.task-description {
    font-size: 14px;
    margin-bottom: 10px;
    line-height: 20px;
}

.board-view-box {
    width: 340px;
}
</style>