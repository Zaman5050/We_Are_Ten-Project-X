// resources/js/stores/authStore.js
import { defineStore } from "pinia";

export const useSupplierStore = defineStore("supplier-store", {
    state: () => ({
        supplier: {},
        mode: "create",
        href: "/",
    }),
    actions: {
        setSupplier(supplier) {
            this.supplier = Object.assign({}, supplier);
        },
        resetSupplier() {
            this.supplier = Object.assign({});
        },
        setMode(mode) {
            this.mode = mode;
        },
        setHref(href) {
            this.href = href;
        },
    },
});
