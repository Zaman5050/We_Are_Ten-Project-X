// resources/js/stores/authStore.js
import { defineStore } from "pinia";

export const useUserStore = defineStore("user-store", {
    state: () => ({
        authUser: {},
    }),
    actions: {
        setAuthUser(user) {
            this.authUser = Object.assign({}, user);
        },
    },
    getters: {
        getFullName(state) {
            return state.authUser?.full_name || "";
        },
    },
});
