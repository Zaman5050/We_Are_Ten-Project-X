<template>
    <div
        class="draggable px-3 py-2"
        :style="styleObject"
        @mousedown="handleDragStart"
        v-if="isPopupShow"
    >
        <span class="close" @click="handleClosed" v-if="false">&times;</span>
        <span class="task-code">{{ props.task.task_code }}</span>
        <strong class="text-truncate task-title d-block">{{
            props.task.title
        }}</strong>
        <div class="d-flex justify-content-between fs-6 text-nowrap py-2">
            <span
                class="timer-btn d-flex align-items-center"
                @click="logTimeShow"
            >
                <img src="/assets/images/log-timer.svg" alt="log time" />
                &emsp;Log Time
            </span>
            <span
                class="timer-btn d-flex align-items-center"
                @click="toggleTimer"
            >
                <img
                    :src="
                        state.timer_mode === 'active'
                            ? '/assets/images/pause-timer.svg'
                            : '/assets/images/resume-timer.svg'
                    "
                    alt="start timer"
                />
                &emsp;{{ timerToggledStatus }}
            </span>
        </div>
        <span v-if="showTime" class="timer d-block">{{ formattedTime }}</span>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    defineProps,
    onMounted,
    watch,
    reactive,
    onUnmounted,
} from "vue";
import axios from "axios";
import eventBus from "../../eventBus";
import moment from "moment-timezone";
import { useUserStore } from "../../stores/userStore";
import { useTimerStore } from "../../stores/taskStore";
import { useToast } from "vue-toastification";
const toast = useToast();

const userStore = useUserStore();
const timerStore = useTimerStore();
const props = defineProps({
    task: Object,
    blockIndex: Number,
});
const timezone = computed(() => userStore.authUser?.timezone || "UTC");

const isDragging = ref(false);
const showTime = ref(true);
const offsetX = ref(0);
const offsetY = ref(0);
const isPopupShow = ref(true);

const styleObject = ref({
    position: "fixed",
    top: "50px",
    left: "50px",
});

const state = reactive({
    timer_mode: props.task.timer_mode || "idle",
    startTime: null,
    totalTime: props.task.total_time || 0,
    logId: [],
});

const timerInterval = ref(null);
const timerRunning = ref(false);

const timer = ref(state.totalTime);

onMounted(() => {
    isPopupShow.value = timerStore.getVisibility(props.task.uuid);
    const timerState = getTimerStateForTask(props.task.uuid);
    if (
        timerState?.totalTime > (props.task.total_time || 0) &&
        state?.timer_mode === "active"
    ) {
        state.totalTime = timerState.totalTime;
        state.startTime = timerState.startTime;
        timer.value = state.totalTime;
        startTimer();
    } else {
        // Use the database timer state
        state.totalTime = props.task.total_time || 0;
        state.timer_mode = props.task.timer_mode || "idle";

        if (state.timer_mode === "active") {
            startTimer();
        } else if (state.timer_mode === "paused") {
            stopTimer();
        }
    }
});

const handleClosed = () => {
    // if(!props.task.member_eligible){
    //     alert("Action not allowed")
    //     return false;
    // }
    isPopupShow.value = false;
    timerStore.setVisibility(props.task.uuid, false);

    stopTimer("idle")
        .then(() => {
            state.timer_mode = "idle";
            eventBus.emit("timeLogEvent", {
                taskUuid: props.task.uuid,
                timer_mode: state.timer_mode,
                timer: timer.value,
            });
        })
        .catch((err) => {
            console.error(err);
        });
};

onUnmounted(() => {
    if (props.task.timer_mode == "paused") {
        stopTimer("paused")
            .then(() => {
                state.timer_mode = "paused";
                eventBus.emit("captureTimerEvent", {
                    timer_mode: state.timer_mode,
                    timer: timer.value,
                });
                eventBus.emit("timeLogEvent", {
                    taskUuid: props.task.uuid,
                    timer_mode: state.timer_mode,
                    timer: timer.value,
                });
            })
            .catch((err) => {
                console.error(err);
            });
    }
});

const handleDragStart = (event) => {
    isDragging.value = true;
    offsetX.value = event.clientX - event.target.getBoundingClientRect().left;
    offsetY.value = event.clientY - event.target.getBoundingClientRect().top;
    window.addEventListener("mousemove", handleDragging);
    window.addEventListener("mouseup", handleDragEnd);
};

const handleDragging = (event) => {
    if (isDragging.value) {
        styleObject.value.left = `${event.clientX - offsetX.value}px`;
        styleObject.value.top = `${event.clientY - offsetY.value}px`;
    }
};

const handleDragEnd = () => {
    isDragging.value = false;
    window.removeEventListener("mousemove", handleDragging);
    window.removeEventListener("mouseup", handleDragEnd);
};

const timerToggledStatus = computed(() => {
    if (state.timer_mode === "idle") return "Start";
    if (state.timer_mode === "paused") return "Resume";
    if (state.timer_mode === "active") return "Pause";
    return "Start";
});

// const toggleTimer = async () => {

//     // if(!props.task.member_eligible){
//     //     alert("Action not allowed")
//     //     return false;
//     // }

//     if (state.timer_mode === 'idle' || state.timer_mode === 'paused') {
//         state.timer_mode = 'active';
//         await startTimer();
//     } else if (state.timer_mode === 'active') {
//         state.timer_mode = 'paused';
//         await stopTimer();
//     }

//     eventBus.emit('timeLogEvent', {
//         taskUuid: props.task.uuid,
//         timer_mode: state.timer_mode,
//         timer: timer.value
//     });
// };
const toggleTimer = async () => {
    // If another task's timer is active, stop it before starting a new one
    const timerStates = JSON.parse(localStorage.getItem("timer-states")) || {};
    const activeTaskUuid = timerStates.activeTaskUuid;

    if (
        state.timer_mode !== "active" &&
        activeTaskUuid &&
        activeTaskUuid !== props.task.uuid
    ) {
        const activeTaskState = timerStates[activeTaskUuid];
        if (activeTaskState && activeTaskState.mode === "active") {
            // Stop the previously active timer
            await stopTimer();
        }
    }

    if (state.timer_mode === "idle" || state.timer_mode === "paused") {
        state.timer_mode = "active";
        await startTimer();
    } else if (state.timer_mode === "active") {
        state.timer_mode = "paused";
        await stopTimer();
    }

    eventBus.emit("timeLogEvent", {
        taskUuid: props.task.uuid,
        timer_mode: state.timer_mode,
        timer: timer.value,
    });
};

const startTimer = async () => {
    isPopupShow.value = true;
    timerStore.setVisibility(props.task.uuid, true);
    // If the timer is already running, return early to prevent multiple intervals
    if (timerRunning.value) {
        return;
    }

    // Clear any existing interval before starting a new one
    if (timerInterval.value) {
        clearInterval(timerInterval.value);
    }
    timerRunning.value = true;
    state.startTime = moment().tz(timezone.value).format();

    try {
        const response = await logTimeEntry(
            state.startTime,
            null,
            null,
            state.timer_mode
        );
        state.logId = response.data?.timesheet_uuids || []; // Assume the response contains the log ID
    } catch (error) {
        console.error("Error logging start time:", error);
    }
    timerInterval.value = setInterval(() => {
        timer.value++;
        state.totalTime++;
    }, 1000);
};

const stopTimer = async (timerStatus = "") => {
    isPopupShow.value = false;
    timerStore.setVisibility(props.task.uuid, false);
    if (timerRunning.value) {
        clearTimer();

        const endTime = moment().tz(timezone.value);
        const startTime = moment(state.startTime).tz(timezone.value);

        // Calculate  time difference
        const timeDiff = endTime.diff(startTime, "seconds");

        state.totalTime += timeDiff;

        const formattedStartTime = startTime.format();
        const formattedEndTime = endTime.format();

        try {
            await updateLogTimeEntry(
                state.logId,
                formattedStartTime,
                formattedEndTime,
                timeDiff,
                timerStatus ? timerStatus : state.timer_mode
            );
        } catch (error) {
            console.error("Error updating end time:", error);
        }
    } else {
        if (timerStatus) {
            return await updateTimerStatus(timerStatus);
        }
    }
};
const clearTimer = () => {
    if (timerInterval.value) {
        clearInterval(timerInterval.value);
        timerInterval.value = null;
    }
    timerRunning.value = false;
};

const updateTimerStatus = async (timerMode) => {
    try {
        return await axios.post("/timesheet/update-timer-status", {
            taskUuid: props.task.uuid,
            timer_mode: timerMode,
        });
    } catch (error) {
        console.error("Error update status:", error);
    }
};

const logTimeEntry = async (startTime, endTime, totalTime, timerMode) => {
    return await axios.post("/timesheet/log-time", {
        taskUuid: props.task.uuid,
        start_time: startTime,
        end_time: endTime,
        total_time: totalTime,
        timer_mode: timerMode,
    });
};

const updateLogTimeEntry = async (
    logId,
    startTime,
    endTime,
    totalTime,
    timerMode
) => {
    try {
        // Send the request to the backend
        const response = await axios.put(`/timesheet/log-time`, {
            timesheet_uuids: logId,
            taskUuid: props.task.uuid,
            start_time: startTime,
            end_time: endTime,
            total_time: totalTime,
            timer_mode: timerMode,
        });

        // Handle success
        if (response.status === 200) {
            if (response.data.error) {
                // Backend returned an error message 
                toast.error(response.data.message || "An error occurred.", {
                    timeout: 3000,
                    position: "top-right",
                });
                removeTaskFromLocalStorage(props.task.uuid);
                return false;
            }
        }

    } catch (error) {
        // Handle errors (e.g., 403, 500, etc.)
        if (error.response) {
            toast.error("An error occurred while logging time.", {
                timeout: 3000,
                position: "top-right",
            });
        } else {
            // Network or other error
            toast.error("An error occurred while logging time.", {
                timeout: 3000,
                position: "top-right",
            });
        }
        return false;
    }
};
const removeTaskFromLocalStorage = (taskUuid) => {
    const timerStates = JSON.parse(localStorage.getItem("timer-states")) || {};

    if (timerStates[taskUuid]) {
        delete timerStates[taskUuid]; // Remove the specific task
        localStorage.setItem("timer-states", JSON.stringify(timerStates)); // Save updated state
    }
};
watch(
    () => props.task.timer_mode,
    async (newTimeMode) => {
        state.timer_mode = newTimeMode;

        if (newTimeMode === "active") {
            await startTimer();
        } else if (newTimeMode === "paused") {
            await stopTimer();
        } else if (newTimeMode === "idle") {
            clearTimer();
        }
    },
    { immediate: true }
);

const logTimeShow = () => {
    showTime.value = !showTime.value;
};

const formattedTime = computed(() => {
    const hours = Math.floor(timer.value / 3600);
    const minutes = Math.floor((timer.value % 3600) / 60);
    const seconds = timer.value.toFixed(0) % 60;
    return `${String(hours).padStart(2, "0")}:${String(minutes).padStart(
        2,
        "0"
    )}:${String(seconds).padStart(2, "0")}`;
});
watch(
    () => timerStore.timerVisibility,
    (newVisibility) => {
        isPopupShow.value = newVisibility[props.task.uuid] ?? true;
    },
    { deep: true }
);
const updateTimerState = () => {
    const timerStates = JSON.parse(localStorage.getItem("timer-states")) || {};

    // If another task was active previously, clear its state
    const activeTaskUuid = timerStates.activeTaskUuid;
    if (activeTaskUuid && activeTaskUuid !== props.task.uuid) {
        delete timerStates[activeTaskUuid]; // Remove the previous task's timer state
    }

    // Update the state for the current task
    timerStates[props.task.uuid] = {
        mode: state.timer_mode,
        totalTime: state.totalTime,
        startTime: state.startTime,
    };

    // Mark the active task uuid
    timerStates.activeTaskUuid = props.task.uuid;

    // Save the updated state to localStorage
    localStorage.setItem("timer-states", JSON.stringify(timerStates));
};

const getTimerStateForTask = (taskUuid) => {
    const timerStates = JSON.parse(localStorage.getItem("timer-states")) || {};
    return timerStates[taskUuid] || null;
};

watch(
    () => state.totalTime,
    (newVal) => {
        if (state.timer_mode === "active") {
            updateTimerState();
        }
    }
);
</script>

<style scoped>
.draggable {
    border: 1px solid #ccc;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
    z-index: 1056;
    width: 240px;
    height: 100px;
    color: #fff;
    background-color: rgba(0, 0, 0, 0.9);
    cursor: move;
    border-radius: 10px;
    user-select: none;
}

.timer-btn {
    font-size: 13px;
    cursor: pointer;
}

.timer {
    text-indent: 25px;
}

.task-code {
    position: absolute;
    bottom: 1rem;
    right: 1rem;
    font-size: small;
    text-shadow: 0px 0 2px #000, 0px 0px 4px #fff;
}

.task-title {
    width: 150px;
}

.close {
    position: absolute;
    right: -8px;
    top: -8px;
    background: #000;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    text-align: center;
    cursor: pointer;
    font-size: 30px;
    line-height: 13px;
}
</style>
