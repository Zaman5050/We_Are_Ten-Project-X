<template>
    <div v-dragscroll:nochilddrag class="board-view-container">
        <div class="board-view-vertical-row" data-dragscroll v-for="(column, columnIndex) in state.boards"
            :key="columnIndex" @dragover.prevent @drop="onDrop($event, columnIndex)" :data-column-index="columnIndex"
            :data-status-uuid="column.status_uuid">

            <h6 class="task-stage-heading ms-2 mb-3">{{ column.name }} {{ countTasks(column.tasks) }} </h6>
            <div @dragenter="onDragEnterColumn(columnIndex)" class="bord-view-column-height overflow-auto  border-red" >
                <TransitionGroup tag="div" name="tasks" data-dragscroll>
    
                    <div class="board-view-box cursor-pointer group" v-for="(task, taskIndex) in column?.tasks" :key="task.uuid"
                            @dragstart="onDragTask($event, task, columnIndex, taskIndex)"
                            @dragenter="onDragEnterTask($event, task, columnIndex, taskIndex)" draggable="true"
                            @dragend="onDragEnd($event)" @dragleave.prevent="onDragLeaveTask($event)"
                            @click.stop="$emit('edit-task', task)"
                            :class="[(tempTask?.taskIndex === taskIndex) && (tempTask?.columnIndex === columnIndex) ? tempTaskStyle : '', (draggedTask?.task?.uuid === task.uuid) ? 'opacity-50' : '']">
    
                        <div class="text-end mb-3 float-end" >
                            <div class="dropdown three-dots-dropdown">
                                <a @click.stop style="width: fit-content; margin-left:  auto;"
                                    class="d-flex justify-content-end filter-btn border-0 dropdown-toggle gap-3" href="#"
                                    role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="/assets/images/three-dots.svg">
                                </a>
                                <ul style="width: 100%; min-width: 138px; padding: 15px 18px; box-shadow: rgba(26, 32, 36, 0.32) 0px 0px 2px 0px;"
                                class="dropdown-menu border-0" aria-labelledby="dropdownMenuLink">
                               
                                <li>
                                    <span role="button" class="dropdown-item mb-2">Edit</span>
                                </li>
                                    <li><span role="button" @click.stop="$emit('flag-task', task.uuid); " class="dropdown-item mb-2"> {{ task.flaged_by ? 'Unflag' :'Flag' }}</span></li>
                                    <li><span role="button" @click.stop="$emit('archive-task', task.uuid, task.archive)" class="dropdown-item mb-2">{{ task.archive ? 'Un-Archive' : 'Archive' }}</span></li>
                                    <li>
                                        <span role="button" class="dropdown-item mb-2" style="color: rgb(248, 98, 78)">Delete</span>
                                    </li>
                                </ul>
                            </div>
    
                        </div>
                        <p class="task-description text-truncate mb-2">
                            {{ task.title }}
                        </p>
                        <span v-if="task?.stage" class="tags mb-2 d-inline-block text-nowrap text-black">
                            {{ task?.stage ? task?.stage?.stage_name : 'Stage' }}
                        </span>
    
                        <div class="small d-flex justify-content-between align-items-center gap-1">
                            <p class="gray-color text-nowrap">Due Date: {{ task.due_date }}</p>
                            <span @click.stop><img src="/assets/images/calender-icon.svg"></span>
                            <img v-if="task.flaged_by" class="red-flag" src="/assets/images/red-flag.svg">
                            <span class=" gray-color text-nowrap">{{ task.task_code }}</span>
                            <span v-if="task?.members.length" @click.stop>
                                <MemberAvatarList :users="task.members" :size="task.members?.length" :uindex="taskIndex" ></MemberAvatarList>
                            </span>
                        </div>
                    </div>
    
                </TransitionGroup>

            </div>


        </div>
    </div>
</template>

<script setup>
import { reactive, ref, defineProps, onMounted, watch, defineEmits } from 'vue';
import OpenModel from "../OpenModel.vue";
import MemberAvatarList from "../MemberAvatarList.vue";
import eventBus from '../../eventBus';

const props = defineProps({
    tasks: {
        type: Array,
        default: [],
    },
    taskStatuses: {
        type: Array,
        default: [],
    }
});

const emits = defineEmits(['update-status']);

const state = reactive({
    boards: [],
    selectedBoard: 0,
    selectedColumn: 0,
    selectedTask: 0,
});

const handleTaskEdit = (taskUuid) => {
    eventBus.emit('idSelected', taskUuid);
}

onMounted(() => {
    transformedTasks();
})

watch(() => props.tasks, (newTasks) => {
    transformedTasks();
}, { deep: true });


const randomColor = () => {
    return `#${getRandomValue().toString(16).padStart(2, '0')}${getRandomValue().toString(16).padStart(2, '0')}${getRandomValue().toString(16).padStart(2, '0')}`;
}

const getRandomValue = () => {
    return Math.floor(Math.random() * 151); // Generates a number between 0 and 150
}


const statusColors = {
    "Todo": "#49C4E5",
    "In Progress": "#8471F2",
    "In Review": "#FFD700",
    "Completed": "#67E2AE"
};

const transformedTasks = () => {
    // Group tasks by status
    const groupedTasks = props.tasks
        .sort((a, b) => a.status_id - b.status_id)
        .reduce((acc, task) => {
            const statusTitle = task.status.title;

            if (!acc[statusTitle]) {
                acc[statusTitle] = {
                    name: statusTitle,
                    status_id: task?.status?.index ?? task?.status_id,
                    status_uuid: task?.status?.uuid,
                    color: statusColors[statusTitle] || '#000000', // Default color if not found in the mapping
                    tasks: []
                };
            }

            acc[statusTitle].tasks.push(task);

            return acc;
        }, {});

    // Ensure all statuses are present
    props.taskStatuses.forEach((status, index) => {

        // console.log(props.taskStatuses.findIndex(x => x.title == status));
        if (!groupedTasks[status.title]) {
            groupedTasks[status.title] = {
                name: status.title,
                status_id: status?.index,
                status_uuid: status?.uuid,
                color: statusColors[status] || '#000000', // Default color if not found in the mapping
                tasks: []
            };
        }
    });

    // Convert the object to an array
    const result = Object.values(groupedTasks).sort((a, b) => a.status_id - b.status_id);
    state.boards = result;
}

const countTasks = (tasks) => {
    return tasks.length ? `(${tasks.length})` : '';
}
const draggedTask = ref(null)
let tempTaskStyle = ['drag-active']
const tempTask = ref(null) //For visual feedback
const onDragTask = (evt, task, columnIndex, taskIndex) => {
    // managerStore.dragging = true
    draggedTask.value = {
        el: evt.target,
        task,
        columnIndex,
        taskIndex
    }
    state.boards[columnIndex].tasks.splice(taskIndex, 1)
    // state.boards[state.selectedBoard].columns[columnIndex].tasks.splice(taskIndex, 1)
    // boardsStore.getCurrentBoard.columns[columnIndex].tasks.splice(taskIndex, 1)
    evt.dataTransfer.dropEffect = 'move'
    evt.dataTransfer.effectAllowed = 'move'
}
const onDragEnterColumn = (columnIndex) => {
    removeTempTask()
    state.boards[columnIndex].tasks.push(draggedTask.value.task)
    tempTask.value = {
        columnIndex,
        taskIndex: state.boards[columnIndex].tasks.length - 1
    }
}

const onDragEnterTask = (evt, task, columnIndex, taskIndex) => {
    if (draggedTask.value.task.uuid !== task.uuid) {
        removeTempTask()
        state.boards[columnIndex].tasks.splice(taskIndex, 0, draggedTask.value.task)
        tempTask.value = {
            columnIndex,
            taskIndex
        }
    }
}
const onDragLeaveTask = (evt) => {

}
const onDragEnd = (evt) => {
    if (tempTask.value) {
        const sameColumn = draggedTask.value?.columnIndex === tempTask.value?.columnIndex
        const isAbove = draggedTask.value?.taskIndex > tempTask.value?.taskIndex
    } else {
        state.boards[draggedTask.value.columnIndex].tasks.splice(draggedTask.value.taskIndex, 0, draggedTask.value.task)
    }
    // managerStore.dragging = false
    draggedTask.value = null
    tempTask.value = null
    state.boards = state.boards
}
const removeTempTask = () => {
    if (tempTask.value) {
        state.boards[tempTask.value.columnIndex].tasks.splice(tempTask.value.taskIndex, 1)
        tempTask.value = null
    }
}

const onDrop = (evt, columnIndex) => {
    evt.preventDefault();
    evt.stopPropagation();

    const statusUuid = evt.currentTarget.dataset.statusUuid;


    if (draggedTask) {
        const { task } = draggedTask.value;

        // Emit event to update task status
        emits('update-status', { taskUuid: task.uuid, newStatusUuid: statusUuid });
    }
};
</script>

<style scoped>
.opacity-50 {
    opacity: 0.5;
}
.tags{
    background: #F8DD4E;
}
.gray-color{
    color: rgb(110, 124, 135);
}
.drag-active {
    border: 2px solid #6E7C87;
}
</style>