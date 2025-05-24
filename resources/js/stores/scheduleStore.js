// resources/js/stores/authStore.js
import { defineStore } from "pinia";

export const useScheduleStore = defineStore("schedule-store", {
    state: () => ({
        schedule: {},
        mode: "create",
        module: "schedule",
    }),
    actions: {
        setSchdedule(schedule) {
            this.schedule = Object.assign({}, schedule);
        },
        resetSchdedule() {
            this.schedule = Object.assign({});
        },
        setMode(mode) {
            this.mode = mode;
        },
        setModule(module) {
            this.module = module;
        },
    },
});
