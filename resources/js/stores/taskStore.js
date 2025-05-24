import { defineStore } from "pinia";

export const useTaskStore = defineStore("task-store", {
    state: () => ({
        task: {},
        mode: "create",
        tasks: [],
    }),
    actions: {
        setTask(task) {
            this.task = Object.assign({}, task);
        },
        resetTask() {
            this.task = Object.assign({});
        },
        setMode(mode) {
            this.mode = mode;
        },
        updateTaskInList(updatedTask) {
            const taskIndex = this.tasks.findIndex(
                (t) => t.task_uuid === updatedTask.task_uuid
            );
            if (taskIndex !== -1) {
                // Only update the estimate_time while preserving other properties
                this.tasks[taskIndex] = {
                    ...this.tasks[taskIndex], // Retain existing task data
                    estimate_time: updatedTask.estimate_time, // Update only estimate_time
                    total_time: updatedTask.total_time,
                    total_time_formated: updatedTask.total_time_formated,
                    timer_mode: updatedTask.timer_mode,
                    members: Array.isArray(updatedTask.member)
                        ? [...updatedTask.member] // Ensure member is an array
                        : [updatedTask.member], // Wrap in an array if it's a single member object
                };
            }
        },
        setTasks(tasks) {
            this.tasks = tasks;
        },
    },
    getters: {
        getTask(state) {
            return state.task; // Return the current task
        },
        getTasks(state) {
            return state.tasks; // Return the current task
        },
    },
});

export const useTaskSettingStore = defineStore("task-setting-store", {
    state: () => ({
        isBoardView: false,
    }),
    actions: {
        setBoardView(status) {
            this.isBoardView = status;
        },
    },
    persist: true, // Keep this option here
});

export const useTimerStore = defineStore("timerStore", {
    state: () => ({
        timerVisibility: {},
    }),
    actions: {
        setVisibility(taskUuid, isVisible) {
            this.timerVisibility[taskUuid] = isVisible;
        },
        syncVisibility(syncData) {
            this.timerVisibility = {
                ...this.timerVisibility,
                ...syncData.visibility,
            };
        },
        getVisibility(taskUuid) {
            return this.timerVisibility[taskUuid] ?? true;
        },
    },
    persist: true, // Keep this option here
});
