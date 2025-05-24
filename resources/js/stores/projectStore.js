// resources/js/stores/authStore.js
import { defineStore } from "pinia";

export const useProjectStore = defineStore("project-store", {
    state: () => ({
        project: {},
        mode: "create",
    }),
    actions: {
        setProject(project) {
            this.project = Object.assign({}, project);
        },
        resetProject() {
            this.project = Object.assign({});
        },
        setMode(mode) {
            this.mode = mode;
        },
    },
});
