// resources/js/stores/authStore.js
import { defineStore } from "pinia";

export const useProcurementStore = defineStore("procurement-store", {
    state: () => ({
        procurement: {},
        mode: "create",
        module: "procurement",
        categories: [],
    }),
    actions: {
        setProcurement(procurement) {
            this.procurement = Object.assign({}, procurement);
        },
        resetProcurement() {
            this.procurement = Object.assign({});
        },
        setMode(mode) {
            this.mode = mode;
        },
        setModule(module) {
            this.module = module;
        },
        setCategories(categories) {
            this.categories = categories;
        },
    },
});
